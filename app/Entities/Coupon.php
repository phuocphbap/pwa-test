<?php

namespace App\Entities;

use Illuminate\Database\Eloquent\Model;
use Prettus\Repository\Contracts\Transformable;
use Prettus\Repository\Traits\TransformableTrait;

/**
 * Class Coupon.
 */
class Coupon extends Model implements Transformable
{
    use TransformableTrait;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'couponable_type',
        'couponable_id',
        'coupon_code',
        'coupon_discount',
        'start_date',
        'expire_date',
        'state',
    ];

    protected $hidden = ['created_at', 'updated_at'];

    const DEFAULT_COUPONABLE_TYPE = 'App\Entities\Service';
}
