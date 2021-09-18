<?php

namespace App\Http\Controllers\Admin\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\AdminLoginRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class AuthController extends Controller
{
    public function adminLoginProcess(AdminLoginRequest $request)
    {
        try {
            $credentials = $request->only('email', 'password');
            if (Auth::guard('admin')->attempt($credentials)) {
                return response()->json([
                    'status' => true,
                    'message' => __('api.login.login_success'),
                    'token' => auth('admin')->user()->createToken(Auth::guard('admin')->user()->id)->accessToken,
                    'user' => auth('admin')->user(),
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

    public function getAdminAuth(Request $request)
    {
        $user = auth('admin-api')->user();

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

    public function adminLogout()
    {
        auth('admin-api')->user()->token()->revoke();

        return response()->json([
            'status' => true,
            'message' => __('api.login.logout_success'),
        ]);
    }
}
