<?php

namespace App\Entities;

use Illuminate\Database\Eloquent\Model;
use Prettus\Repository\Contracts\Transformable;
use Prettus\Repository\Traits\TransformableTrait;

/**
 * Class Store.
 */
class ServiceLike extends Model implements Transformable
{
    use TransformableTrait;
    protected $table = 'service_likes';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id',
        'user_id',
        'service_id',
        'state',
    ];

    protected $hidden = ['created_at', 'updated_at'];

    public function services()
    {
        return $this->belongsTo(Service::class, 'service_id');
    }
}
