<?php

namespace App\Http\Controllers\Admin\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\ResetPasswordRequest;
use App\Http\Requests\SendMailRequest;
use App\Mail\ResetAppUserPassword;
use App\Repositories\AdminUserRepository;
use Carbon\Carbon;
use Mail;
use Prettus\Validator\Exceptions\ValidatorException;

class ResetPasswordController extends Controller
{
    /**
     * @var AdminUserRepository
     */
    protected $repository;

    /**
     * UsersController constructor.
     */
    public function __construct(AdminUserRepository $repository)
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
            if (auth('admin-api')->user()) {
                $user = auth('admin-api')->user();
                if ($request->email == $user->email) {
                    // Create token
                    $token = app('crypt')->encode($request->email.';'.Carbon::now()->addMinutes(15));
                    $url = env('APP_URL_ADMIN').'/change-password?token='.$token;
                    Mail::to($request->email)->send(new ResetAppUserPassword(compact('user', 'url')));
                } else {
                    return response()->json([
                        'status' => false,
                        'message' => __('api.reset_password.invalid_email'),
                    ]);
                }
            } else {
                $user = $this->repository->where('email', $request->email)->first();
                if ($user) {
                    // Create token
                    $token = app('crypt')->encode($request->email.';'.Carbon::now()->addMinutes(15));
                    $url = env('APP_URL_ADMIN').'/change-password?token='.$token;
                    Mail::to($request->email)->send(new ResetAppUserPassword(compact('user', 'url')));
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
                'message' => __('api.reset_password.sent_failed'),
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
                        return response()->json([
                            'message' => __('api.reset_password.token_invalid'),
                        ], 422);
                    }
                    $adminUser = $this->repository->where('email', $email)->first();
                    if ($adminUser) {
                        $updatePasswordUser = $adminUser->update($request->only('password'));
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
                'message' => __('api.common.failed'),
            ], 400);
        }
    }
}
