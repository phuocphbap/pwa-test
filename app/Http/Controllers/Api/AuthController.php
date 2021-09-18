<?php

namespace App\Http\Controllers\Api;

use App\Constant\StatusConstant;
use App\Helpers\Facades\UploadS3Helper;
use App\Http\Controllers\Controller;
use App\Http\Requests\UserCreateRequest;
use App\Mail\VerifyRegisterUser;
use App\Repositories\UserRepository;
use App\Services\FirebaseService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Mail;
use App\Services\SMSService;
use Cache;

class AuthController extends Controller
{
    /**
     * @var UserRepository
     */
    protected $repository;

    /**
     * @var FirebaseService
     */
    protected $firebaseService;

    /**
     * @var SMSService
     */
    protected $smsService;

    /**
     * UsersController constructor.
     */
    public function __construct(UserRepository $repository, FirebaseService $firebaseService, SMSService $smsService)
    {
        $this->repository = $repository;
        $this->firebaseService = $firebaseService;
        $this->smsService = $smsService;
    }

    public function loginProcess(Request $request)
    {
        try {
            if (!$request->email) {
                return response()->json([
                    'status' => false,
                    'message' => __('api.login.login_email'),
                ], 422);
            }
            if (!$request->password) {
                return response()->json([
                    'status' => false,
                    'message' => __('api.login.login_password'),
                ], 422);
            }
            $credentials = $request->only('email', 'password');
            $user = $this->repository->getUserByEmail($request->email);
            if ($user) {
                if ($user['is_email_verified'] == 0) {
                    return response()->json([
                        'status' => false,
                        'message' => __('api.login.email_not_verified'),
                    ], 401);
                }
                if ($user['state'] != StatusConstant::USER_ACTIVE) {
                    return response()->json([
                        'status' => false,
                        'message' => __('api.login.user_invalid'),
                    ], 401);
                }
            } else {
                return response()->json([
                    'status' => false,
                    'message' => __('api.login.account_not_exists'),
                ], 401);
            }
            if (Auth::attempt($credentials)) {
                if(auth()->user()->is_two_fa) {
                    $code = $this->generateVerificationCode();
                    $message = "{$code}";
                    $phone = auth()->user()->phone_verify;
                    $token = auth()->user()->createToken(Auth::user()->id)->accessToken;
                    $userId = auth()->user()->id;
                    $data = collect(array('otp'=>$code,'token'=>$token, 'phone_verify' => $phone, 'user'=>auth()->user()));// save otp and token in an array 
                    Cache::put($userId, $data, Carbon::now()->addMinutes(60)); //cache data add 60 minutes
                    //check key exist
                    if (Cache::has($userId)) {
                        $this->smsService->sendSMS($phone, $message); //send sms otp
                        return response()->json([
                            'status' => true,
                            'message' => __('api.login.login_success'),
                            'otp_required' => true,
                            'user' => auth()->user(),
                        ]);
                    }
                }

                if ($user['state'] == StatusConstant::USER_LEAVE_GROUP) {
                    // unlock chat on firebase
                    $this->firebaseService->handleLeaveChat($user['id'], false, StatusConstant::USER_LEAVE_GROUP);
                    $this->repository->unlockLeaveGroup($user['id']);
                }

                return response()->json([
                    'status' => true,
                    'message' => __('api.login.login_success'),
                    'otp_required' => false,
                    'token' => auth()->user()->createToken(Auth::user()->id)->accessToken,
                    'user' => auth()->user(),
                ]);
            }

            return response()->json([
                'status' => false,
                'message' => __('api.login.login_fail'),
            ], 401);
        } catch (\Throwable $th) {
            Log::error('Controllers\Api\AuthController - loginProcess : '.$th->getMessage());

            return response()->json([
                'error' => true,
                'message' => __('api.exception'),
            ]);
        }
    }

    public function getUserAuth(Request $request)
    {
        $sumPoint = $this->repository->getPointOfUser(auth()->user()->id);
        $user = $this->repository->where('id', auth()->user()->id)->with('roles', 'skills', 'wallet', 'identityCard', 'bankAccount')->first();
        $user['point'] = $sumPoint;
        if ($user) {
            return response()->json([
                'status' => true,
                'data' => $user,
            ]);
        } else {
            return response()->json([
                'status' => false,
                'message' => __('api.common.not_found'),
            ]);
        }
    }

    public function logout(Request $request)
    {
        auth()->user()->token()->revoke();

        return response()->json([
            'status' => true,
            'message' => __('api.login.logout_success'),
        ]);
    }

    public function register(UserCreateRequest $request)
    {
        try {
            $data = $request->only(['email', 'password', 'user_name', 'phone', 'gender', 'birth_date', 'first_name', 'last_name', 'referral_code', 'address', 'input_refferal_code']);
            
            if ($request->avatar) {
                $imageLink = UploadS3Helper::uploadImage($request->avatar, 'avatar');

                $data['avatar'] = $imageLink;
            } else {
                $data['avatar'] = '';
            }
            $user = $this->repository->registerUser($data);

            if ($user) {
                $token = app('crypt')->encode($user->email.';'.Carbon::now()->addMinutes(30));
                $url = url('api/auth/verify-register/'.$token);
                $userName = $user->first_name.' '.$user->last_name;
                $exprireTime = Carbon::now()->addMinutes(30)->format('Y/m/d H:i:s');
                Mail::to($request->email)->send(new VerifyRegisterUser(compact('userName', 'url', 'exprireTime')));

                return response()->json([
                    'status' => true,
                    'message' => __('api.common.create_success'),
                    'data' => $user->toArray(),
                ]);
            } else {
                return response()->json([
                    'status' => false,
                    'message' => __('api.common.failed'),
                ]);
            }
        } catch (ValidatorException $e) {
            return response()->json([
                'status' => false,
                'message' => __('api.exception'),
            ]);
        }
    }

    public function verifyRegister(Request $request, $token)
    {
        try {
            if ($token) {
                $data = app('crypt')->decode($token);
                if ($data) {
                    [$email, $exprire_time] = explode(';', $data);
                    if (Carbon::parse($exprire_time)->isPast()) {
                        return redirect(env('APP_URL').'/auth/register/failure');
                    }
                    $user = $this->repository->where('email', $email)->first();
                    if ($user) {
                        $verifyUser = $user->update(['is_email_verified' => 1, 'state' => 1]);
                        if ($verifyUser) {
                            return redirect(env('APP_URL').'/auth/register/active');
                        }
                    } else {
                        return response()->json([
                            'status' => false,
                            'message' => __('api.common.not_found'),
                        ]);
                    }
                } else {
                    return response()->json([
                        'message' => __('api.verify_email.token_invalid'),
                    ], 422);
                }
            } else {
                return response()->json([
                    'status' => false,
                    'message' => __('api.common.failed'),
                ]);
            }
        } catch (ValidatorException $e) {
            return response()->json([
                'status' => false,
                'message' => __('api.exception'),
            ]);
        }
    }

    public function generateVerificationCode()
    {
        $count = 0;
        $code = '';
        while ($count < 6) {
            $code .= mt_rand(0, 9);
            ++$count;
        }

        return chunk_split($code, 1, '');
    }
    public function smsVerification(Request $request)
    {
        try {
            $otp = $request->otp;
            $userId = $request->user_id;

            if(Cache::has($userId) && Cache::get($userId)->get('otp') == $otp) {
                $data_cache = Cache::get($userId);
                return response()->json([
                    'status' => true,
                    'message' => __('api.login.login_success'),
                    'token' => $data_cache->get('token'),
                    'user' => $data_cache->get('user'),
                ]);
            }

            return response()->json([
                'status' => false,
                'message' => __('api.login.otp_invalid'),
            ]);


        } catch (ValidatorException $e) {
            return response()->json([
                'status' => false,
                'message' => __('api.exception'),
            ]);
        }

    }

    public function smsResendVerification(Request $request)
    {
        try {
            $userId = $request->user_id;
            if(Cache::has($userId)) {
                $data_cache = Cache::get($userId);
                $phone = $data_cache->get('phone_verify');
                $token = $data_cache->get('token');
                $user = $data_cache->get('user');
                //create new code
                $code = $this->generateVerificationCode();
                $message = "{$code}";
                $data = collect(array('otp'=>$code,'token'=>$token, 'phone_verify'=> $phone, 'user'=>$user));// save otp and token in an array 
                Cache::put($userId, $data, Carbon::now()->addMinutes(60)); //cache data 1 day
                if (Cache::has($userId)) {
                    //resend sms
                    $this->smsService->sendSMS($phone, $message);
                    return response()->json([
                        'status' => true,
                        'message' => __('api.login.resend_otp_success'),
                    ]);
                }
            }
            
            return response()->json([
                'status' => false,
                'message' => __('api.login.otp_expire'),
            ]);

        } catch (ValidatorException $e) {
            return response()->json([
                'status' => false,
                'message' => __('api.exception'),
            ]);
        }
    }

    public function updateTokenOTPPhoneCode($token, $userId)
    {
        $user = $this->repository->find($userId);
        $status = $user->update(['phone_otp_token' => $token]);
        if ($status) {
            return true;
        }

        return false;
    }

    
    
}
