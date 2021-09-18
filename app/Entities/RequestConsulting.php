<?php

namespace App\Entities;

use DateTimeInterface;
use Illuminate\Database\Eloquent\Model;
use Prettus\Repository\Contracts\Transformable;
use Prettus\Repository\Traits\TransformableTrait;

/**
 * Class RequestConsulting.
 */
class RequestConsulting extends Model implements Transformable
{
    use TransformableTrait;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'customer_id',
        'owner_id',
        'service_id',
        'message',
        'progress',
        'price_requested',
        'title_service_request',
        'category_name_request',
        'state',
        'reason',
    ];

    const PROGRESS_BEFORE_AGREEMENT = 0;
    const PROGRESS_CONFIRMED_REQUEST = 1;
    const PROGRESS_UNDER_AGREEMENT = 2;
    const PROGRESS_WAITING_EVALUATION = 3;
    const PROGRESS_DONE = 4;

    const STATE_ACTICE = 1;
    const STATE_CANCEL = 0;

    /**
     * Prepare a date for array / JSON serialization.
     *
     * @return string
     */
    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }

    public function service()
    {
        return $this->belongsTo(Service::class)->withCount(['likes', 'agreements']);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function owner()
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    public function customer()
    {
        return $this->belongsTo(User::class, 'customer_id');
    }
}
