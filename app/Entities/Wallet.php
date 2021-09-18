<?php

namespace App\Entities;

use Illuminate\Database\Eloquent\Model;
use Prettus\Repository\Contracts\Transformable;
use Prettus\Repository\Traits\TransformableTrait;

/**
 * Class Store.
 */
class Wallet extends Model implements Transformable
{
    use TransformableTrait;
    protected $table = 'wallets';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id',
        'amount',
        'state',
    ];

    protected $hidden = ['created_at', 'updated_at'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function walletExpires()
    {
        return $this->hasMany(WalletExpire::class);
    }

    public function walletTransactions()
    {
        return $this->hasMany(WalletTransaction::class);
    }

    public function walletExpire()
    {
        return $this->hasMany(WalletExpire::class);
    }

    public function updateAmountWallet($amount)
    {
        $this->fill([
            'amount' => $this->amount + $amount,
        ])->save();
    }
}
