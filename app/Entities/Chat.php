<?php

namespace App\Entities;

use App\Entities\User;
use DateTimeInterface;
use Illuminate\Database\Eloquent\Model;
use Prettus\Repository\Contracts\Transformable;
use Prettus\Repository\Traits\TransformableTrait;

/**
 * Class Chat.
 *
 * @package namespace App\Entities;
 */
class Chat extends Model implements Transformable
{
    use TransformableTrait;

    protected $table = 'chats';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id',
        'key_firebase',
        'consulting_id',
        'room_name',
        'owner_id',
        'customer_id',
        'service_id',
        'type',
        'state',
        'is_completed',
        'is_leave',
        'is_black_list',
    ];

    public $timestamps = true;

    protected $hidden = ['updated_at'];

    /**
     * Prepare a date for array / JSON serialization.
     *
     * @param  \DateTimeInterface  $date
     * @return string
     */
    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }

    public function userCustomer()
    {
        return $this->belongsTo(User::class, 'customer_id');
    }

    public function userOnwer()
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    public function service()
    {
        return $this->hasOne(Service::class, 'id', 'service_id');
    }
}
