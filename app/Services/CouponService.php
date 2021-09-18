<?php

namespace App\Services;

use App\Repositories\CouponRepository;
use App\Repositories\ServiceRepository;
use App\Repositories\UserRepository;

class CouponService
{
    protected $couponRepo;
    protected $userRepo;
    protected $serviceRepo;

    /**
     * CouponRepository constructor.
     */
    public function __construct(
        CouponRepository $couponRepo,
        UserRepository $userRepo,
        ServiceRepository $serviceRepo
    ) {
        $this->couponRepo = $couponRepo;
        $this->userRepo = $userRepo;
        $this->serviceRepo = $serviceRepo;
    }

    public function generateCode()
    {
        $code = $this->userRepo->generateReferralCode();
        $check = $this->couponRepo->checkExistsCouponCode($code);
        if (!$check) {
            return $code;
        }

        return $this->generateCode();
    }
}
