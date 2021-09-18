<?php

namespace App\Entities;

use Illuminate\Database\Eloquent\Model;
use Prettus\Repository\Contracts\Transformable;
use Prettus\Repository\Traits\TransformableTrait;

/**
 * Class Store.
 */
class WalletExpire extends Model implements Transformable
{
    use TransformableTrait;
    protected $table = 'wallet_expires';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'wallet_id',
        'amount',
        'expire_date',
        'period_id',
        'state',
    ];

    protected $hidden = ['created_at', 'updated_at'];

    public function wallet()
    {
        return $this->belongsTo(Wallet::class);
    }
}
