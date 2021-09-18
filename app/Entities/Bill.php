<?php

namespace App\Entities;

use Illuminate\Database\Eloquent\Model;
use Prettus\Repository\Contracts\Transformable;
use Prettus\Repository\Traits\TransformableTrait;

/**
 * Class Store.
 */
class Bill extends Model implements Transformable
{
    use TransformableTrait;
    protected $table = 'bills';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'payment_id',
        'checkout_session_id',
        'title',
        'service_id',
        'customer_id',
        'owner_id',
        'customer_trans_id',
        'customer_trans_expire_id',
        'owner_trans_id',
        'coupon_id',
        'point',
        'price',
        'amount',
        'point_owner_received',
        'fee_payment_id',
        'note',
        'state',
        'consulting_id',
    ];

    protected $hidden = ['created_at', 'updated_at'];

    public function payment()
    {
        return $this->belongsTo(Payment::class);
    }
}
