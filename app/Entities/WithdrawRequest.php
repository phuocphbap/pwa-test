<?php

namespace App\Entities;

use DateTimeInterface;
use Illuminate\Database\Eloquent\Model;
use Prettus\Repository\Contracts\Transformable;
use Prettus\Repository\Traits\TransformableTrait;

/**
 * Class WithdrawRequest.
 */
class WithdrawRequest extends Model implements Transformable
{
    use TransformableTrait;
    protected $table = 'withdraw_requests';

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
        'state',
        'date_accepted',
        'date_rejected',
        'reason_rejected',
    ];
    const PENDING_STATE = 'PENDING';
    const ACCEPTED_STATE = 'ACCEPTED';
    const REJECTED_STATE = 'REJECTED';
    const DONE_STATE = 'DONE';

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
}
