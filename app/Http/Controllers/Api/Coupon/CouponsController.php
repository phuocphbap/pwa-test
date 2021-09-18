<?php

namespace App\Http\Controllers\Api\Coupon;

use App\Entities\Coupon;
use App\Http\Controllers\Api\ApiController;
use App\Http\Requests\CheckCodeCouponRequest;
use App\Http\Requests\GenerateCodeCouponRequest;
use App\Repositories\CouponRepository;
use App\Repositories\UserRepository;
use App\Services\CouponService;
use Carbon\Carbon;

/**
 * Class CouponsController.
 */
class CouponsController extends ApiController
{
    /**
     * @var CouponRepository
     */
    protected $repository;

    /**
     * @var CouponService
     */
    protected $services;
    protected $userRepository;

    /**
     * CouponsController constructor.
     */
    public function __construct(
        CouponRepository $repository,
        CouponService $services,
        UserRepository $userRepository
    ) {
        $this->repository = $repository;
        $this->services = $services;
        $this->userRepository = $userRepository;
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return \Illuminate\Http\Response
     *
     * @throws \Prettus\Validator\Exceptions\ValidatorException
     */
    public function createCoupon(GenerateCodeCouponRequest $request)
    {
        try {
            \DB::beginTransaction();
            $coupon_code = $this->services->generateCode();
            $data = [
                'couponable_type' => Coupon::DEFAULT_COUPONABLE_TYPE,
                'couponable_id' => $request->service_id,
                'coupon_code' => $coupon_code,
                'coupon_discount' => $request->coupon_discount,
                'start_date' => $request->start_date,
                'expire_date' => Carbon::parse($request->expire_date)->endOfDay(),
            ];
            $coupon = $this->repository->create($data);
            \DB::commit();

            $response = [
                'message' => 'Coupon created.',
                'data' => $coupon,
            ];

            return $this->respondSuccess($response);
        } catch (\ValidatorException $th) {
            return response()->json([
                'status' => false,
                'message' => __('api.exception'),
            ]);
        }
    }

    /**
     * check input coupon when payment.
     */
    public function checkCodeCoupon(CheckCodeCouponRequest $request)
    {
        try {
            $coupon = $this->repository->checkCodeCouponValid($request->coupon_code, $request->service_id);
            if (!$coupon) {
                return response()->json([
                    'status' => false,
                    'message' => __('api.coupon.invalid_code'),
                ]);
            }
            $isUsedCoupon = $this->userRepository->checkAlreadyUseCoupon(auth()->user()->id, $coupon->id);
            if ($isUsedCoupon) {
                return response()->json([
                    'status' => false,
                    'message' => __('api.payment.used_coupon'),
                ]);
            }

            return response()->json([
                'status' => true,
                'message' => __('api.coupon.valid_code'),
                'data' => $coupon,
            ]);
        } catch (\ValidatorException $e) {
            return response()->json([
                'status' => false,
                'message' => __('api.exception'),
            ]);
        }
    }
}
