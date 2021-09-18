<?php

namespace App\Entities;

use Illuminate\Database\Eloquent\Model;
use Prettus\Repository\Contracts\Transformable;
use Prettus\Repository\Traits\TransformableTrait;

/**
 * Class FeePayment.
 */
class PeriodExpire extends Model implements Transformable
{
    use TransformableTrait;
    protected $table = 'period_expire';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'sum_month',
    ];
}
