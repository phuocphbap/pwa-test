<?php

namespace App\Entities;

use Illuminate\Database\Eloquent\Model;
use Prettus\Repository\Contracts\Transformable;
use Prettus\Repository\Traits\TransformableTrait;

class ServiceSuggest extends Model implements Transformable
{
    use TransformableTrait;
    protected $table = 'service_suggests';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id',
        'service_id',
        'time_sort'
    ];

    protected $hidden = ['created_at', 'updated_at'];
}
