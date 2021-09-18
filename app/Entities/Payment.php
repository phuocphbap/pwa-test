<?php

namespace App\Entities;

use Illuminate\Database\Eloquent\Model;
use Prettus\Repository\Contracts\Transformable;
use Prettus\Repository\Traits\TransformableTrait;

/**
 * Class Store.
 */
class Payment extends Model implements Transformable
{
    use TransformableTrait;
    protected $table = 'payments';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id',
        'customer',
        'payment_intent_id',
        'object',
        'currency',
        'receipt_email',
        'status',
    ];

    protected $hidden = ['created_at', 'updated_at'];

    const DEFAULT_CURRENCY = 'jpy';
    const SYSTEM = 'system';

    public function bill()
    {
        return $this->hasOne(Bill::class);
    }
}
