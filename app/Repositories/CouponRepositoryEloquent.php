<?php

namespace App\Repositories;

use App\Entities\Coupon;
use App\Entities\UserCoupon;
use App\Validators\CouponValidator;
use Carbon\Carbon;
use Prettus\Repository\Criteria\RequestCriteria;
use Prettus\Repository\Eloquent\BaseRepository;

/**
 * Class CouponRepositoryEloquent.
 */
class CouponRepositoryEloquent extends BaseRepository implements CouponRepository
{
    /**
     * Specify Model class name.
     *
     * @return string
     */
    public function model()
    {
        return Coupon::class;
    }

    /**
     * Specify Validator class name.
     *
     * @return mixed
     */
    public function validator()
    {
        return CouponValidator::class;
    }

    /**
     * Boot up the repository, pushing criteria.
     */
    public function boot()
    {
        $this->pushCriteria(app(RequestCriteria::class));
    }

    /**
     * check exists coupon.
     */
    public function checkExistsCouponCode($code)
    {
        return $this->model->where('coupon_code', $code)->exists();
    }

    public function checkCodeCouponValid($code, $serviceId)
    {
        $now = Carbon::now();

        return $this->model->where('coupon_code', $code)
                        ->where('couponable_type', Coupon::DEFAULT_COUPONABLE_TYPE)
                        ->where('couponable_id', $serviceId)
                        ->where('start_date', '<=', $now)
                        ->where('expire_date', '>=', $now)
                        ->where('state', 1)
                        ->first();
    }

    public function createUserCoupon($userId, $couponId)
    {
        $data = UserCoupon::create(['user_id' => $userId, 'coupon_id' => $couponId]);

        return $data;
    }
}
