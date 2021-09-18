<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\ConfirmSMSVerifyPhoneRequest;
use App\Http\Requests\SendSMSVerifyPhoneRequest;
use App\Repositories\UserRepository;
use App\Services\VerifyPhoneService;

class VerifySMSController extends Controller
{
    protected $service;
    protected $repository;

    public function __construct(VerifyPhoneService $service, UserRepository $repository)
    {
        $this->service = $service;
        $this->repository = $repository;
    }

    public function sendVerifyCode(SendSMSVerifyPhoneRequest $request)
    {
        $existUser = $this->repository->getUserByPhoneVerified($request->phone);

        if ($existUser) {
            return response()->json(['error' => true, 'message' => __('api.verify_phone.exist_phone_verified')]);
        }

        $status = $this->service->sendVefiryPhoneCode($request->phone);
        if ($status) {
            return response()->json(['success' => true, 'message' => __('api.verify_phone.sent_success')]);
        }

        return response()->json(['error' => true, 'message' => __('api.verify_phone.verify_failed')]);
    }

    public function confirmVerifyCode(ConfirmSMSVerifyPhoneRequest $request)
    {
        $status = $this->service->confirmVerifyPhoneCode($request->code, $request->phone);
        switch ($status) {
            case 'INVALID':
                return response()->json(['error' => true, 'message' => __('api.verify_phone.code_invalid')]);
                break;
            case 'EXPIRED':
                return response()->json(['error' => true, 'message' => __('api.verify_phone.code_expired')]);
                break;
            case 'FALSE':
                return response()->json(['error' => true, 'message' => __('api.verify_phone.verify_failed')]);
                break;
            case 'SUCCESS':
                return response()->json(['success' => true, 'message' => __('api.verify_phone.verify_success')]);
                break;
            default:
                return response()->json(['error' => true, 'message' => __('api.verify_phone.verify_failed')]);
                break;
        }
    }
}
