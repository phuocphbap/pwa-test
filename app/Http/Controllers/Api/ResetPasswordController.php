<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\ResetPasswordRequest;
use App\Http\Requests\SendMailRequest;
use App\Mail\ResetAppUserPassword;
use App\Repositories\UserRepository;
use Carbon\Carbon;
use Mail;
use Prettus\Validator\Exceptions\ValidatorException;

class ResetPasswordController extends Controller
{
    /**
     * @var UserRepository
     */
    protected $repository;

    /**
     * UsersController constructor.
     */
    public function __construct(UserRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * Create token password reset.
     *
     * @return JsonResponse
     */
    public function sendMail(SendMailRequest $request)
    {
        try {
            if (auth('api')->user()) {
                $user = auth('api')->user();
                if ($request->email == $user->email) {
                    // Create token
                    $token = app('crypt')->encode($request->email.';'.Carbon::now()->addHours(3));
                    $url = env('APP_URL').'/change-password?token='.$token;
                    $timeNow['hour'] = Carbon::now()->format('h');
                    $timeNow['minute'] = Carbon::now()->format('i');
                    $timeNow['second'] = Carbon::now()->format('s');
                    Mail::to($request->email)->send(new ResetAppUserPassword(compact('user', 'url', 'timeNow')));
                } else {
                    return response()->json([
                        'status' => false,
                        'message' => __('api.reset_password.invalid_email'),
                    ]);
                }
            } else {
                $user = $this->repository->where('email', $request->email)->first();
                if ($user) {
                    //check email still not verify
                    if ($user->is_email_verified != 1) {
                        return response()->json(['status' => false, 'message' => __('api.reset_password.email_not_confirm')]);
                    }
                    // Create token
                    $token = app('crypt')->encode($request->email.';'.Carbon::now()->addHours(3));
                    $url = env('APP_URL').'/change-password?token='.$token;
                    $timeNow['hour'] = Carbon::now()->format('h');
                    $timeNow['minute'] = Carbon::now()->format('i');
                    $timeNow['second'] = Carbon::now()->format('s');
                    Mail::to($request->email)->send(new ResetAppUserPassword(compact('user', 'url', 'timeNow')));
                } else {
                    return response()->json([
                        'status' => false,
                        'message' => __('api.reset_password.not_found'),
                    ]);
                }
            }

            return response()->json([
                'status' => true,
                'message' => __('api.reset_password.sent_success'),
            ]);
        } catch (ValidatorException $e) {
            return response()->json([
                'status' => false,
                'message' => __('api.exception'),
            ]);
        }
    }

    public function resetPassword(ResetPasswordRequest $request)
    {
        try {
            if ($request->token) {
                $data = app('crypt')->decode($request->token);
                if ($data) {
                    [$email, $exprire_time] = explode(';', $data);
                    if (Carbon::parse($exprire_time)->isPast()) {
                        return redirect(env('APP_URL').'/auth/register/failure');
                    }
                    $user = $this->repository->where('email', $email)->first();
                    if ($user) {
                        $updatePasswordUser = $user->update($request->only('password'));
                        if ($updatePasswordUser) {
                            return response()->json([
                                'status' => true,
                                'message' => __('api.reset_password.reset_password_success'),
                            ]);
                        } else {
                            return response()->json([
                                'status' => false,
                                'message' => __('api.common.failed'),
                            ]);
                        }
                    } else {
                        return response()->json([
                            'status' => false,
                            'message' => __('api.reset_password.not_found'),
                        ]);
                    }
                } else {
                    return response()->json([
                        'message' => __('api.reset_password.token_invalid'),
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
}
