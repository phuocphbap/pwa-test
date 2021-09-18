<?php

namespace App\Entities;

use DateTimeInterface;
use Illuminate\Database\Eloquent\Model;
use Prettus\Repository\Contracts\Transformable;
use Prettus\Repository\Traits\TransformableTrait;

/**
 * Class WalletTransaction.
 */
class WalletTransaction extends Model implements Transformable
{
    use TransformableTrait;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'wallet_id',
        'amount',
        'performed_type',
        'performed_by_id',
        'description',
        'type',
    ];

    const TRANSACTION_PAYMENT = 'PAYMENT';
    const TRANSACTION_RECEIVE_PAYMENT = 'RECEIVE_PAYMENT';
    const TRANSACTION_WITHDRAW = 'WITHDRAW';
    const TRANSACTION_BONUS = 'BONUS';
    const TRANSACTION_REFUND = 'REFUND';

    public $timestamps = true;

    /**
     * Prepare a date for array / JSON serialization.
     *
     * @return string
     */
    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }

    public function wallet()
    {
        return $this->belongsTo(Wallet::class);
    }
}
