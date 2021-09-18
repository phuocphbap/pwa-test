<?php

namespace App\Entities;

use Illuminate\Database\Eloquent\Model;
use Prettus\Repository\Contracts\Transformable;
use Prettus\Repository\Traits\TransformableTrait;

/**
 * Class Store.
 */
class UserCoupon extends Model implements Transformable
{
    use TransformableTrait;
    protected $table = 'user_coupons';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id',
        'coupon_id',
    ];

    protected $hidden = ['created_at', 'updated_at'];
}
