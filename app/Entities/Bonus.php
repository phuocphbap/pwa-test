<?php

namespace App\Entities;

use DateTimeInterface;
use Illuminate\Database\Eloquent\Model;
use Prettus\Repository\Contracts\Transformable;
use Prettus\Repository\Traits\TransformableTrait;

/**
 * Class Bonus.
 */
class Bonus extends Model implements Transformable
{
    use TransformableTrait;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id',
        'amount',
        'trans_wallet_id',
        'trans_wallet_expire_id',
        'type',
        'user_input_refferal',
        'reason_bonus',
    ];

    /**
     * Prepare a date for array / JSON serialization.
     *
     * @return string
     */
    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function userIndicateCode()
    {
        return $this->belongsTo(User::class, 'user_input_refferal');
    }
}
