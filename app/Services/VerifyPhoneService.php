<?php

namespace App\Services;

use Carbon\Carbon;
use App\Services\FirebaseService;
use App\Repositories\UserRepository;

class VerifyPhoneService
{
    protected $userRepository;
    protected $smsService;
    protected $firebaseService;

    public function __construct(UserRepository $userRepository, SMSService $smsService, FirebaseService $firebaseService)
    {
        $this->userRepository = $userRepository;
        $this->smsService = $smsService;
        $this->firebaseService = $firebaseService;
    }

    public function sendVefiryPhoneCode($phone)
    {
        $userId = auth()->user()->id;
        $code = $this->generateVerificationCode();
        $token = app('crypt')->encode($code.';'.Carbon::now()->addMinutes(3));
        $success = $this->updateTokenPhoneCode($token, $userId);
        if ($success) {
            //send sms code
            $message = "{$code}";
            $this->smsService->sendSMS($phone, $message);

            return true;
        }

        return false;
    }

    public function confirmVerifyPhoneCode($code, $phone)
    {
        $user = auth()->user();
        $userId = $user->id;
        $phoneToken = $user->phone_verify_token;
        [$tokenCode, $expireTime] = explode(';', app('crypt')->decode($phoneToken));
        if ($tokenCode == $code) {
            if (Carbon::parse($expireTime)->isPast()) {
                return 'EXPIRED';
            }
            // you may specify the country codes you need to remove
            $phone_number = preg_replace("/^\+(?:84|81)/", "0", $phone);
            $success = $this->updateSuccessVerifyPhone($phone_number, $phone, $userId);
            if ($success) {
                // send notification
                $this->firebaseService->pushNoticeVerifySMS($user);

                return 'SUCCESS';
            }

            return 'FALSE';
        }

        return 'INVALID';
    }

    public function updateTokenPhoneCode($token, $userId)
    {
        $user = $this->userRepository->find($userId);
        $status = $user->update(['phone_verify_token' => $token]);
        if ($status) {
            return true;
        }

        return false;
    }

    public function updatePhoneNumber($phone, $phone_verify, $userId)
    {
        $user = $this->userRepository->find($userId);
        $status = $user->update(['phone' => $phone, 'phone_verify'=> $phone_verify, 'is_two_fa'=>1]);
        if ($status) {
            return true;
        }

        return false;
    }

    public function updateSuccessVerifyPhone($phone, $phone_verify, $userId)
    {
        $user = $this->userRepository->find($userId);
        $status = $user->update([
                'phone' => $phone,
                'phone_verify'=> $phone_verify,
                'is_phone_verified' => 1,
                'is_two_fa'=>1
            ]);
        if ($status) {
            return true;
        }

        return false;
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
}
