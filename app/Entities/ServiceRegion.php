<?php

namespace App\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Prettus\Repository\Contracts\Transformable;
use Prettus\Repository\Traits\TransformableTrait;

/**
 * Class Store.
 */
class ServiceRegion extends Model implements Transformable
{
    use TransformableTrait, SoftDeletes;
    
    protected $table = 'service_regions';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id',
        'service_id',
        'region_id',
    ];

    protected $hidden = ['created_at', 'updated_at'];
}
