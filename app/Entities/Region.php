<?php

namespace App\Entities;

use Illuminate\Database\Eloquent\Model;
use Prettus\Repository\Contracts\Transformable;
use Prettus\Repository\Traits\TransformableTrait;

/**
 * Class Store.
 */
class Region extends Model implements Transformable
{
    use TransformableTrait;
    protected $table = 'regions';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'country_code',
        'state_code',
        'state_name',
        'type',
    ];

    protected $hidden = ['created_at', 'updated_at'];

    public function service()
    {
        return $this->hasOne(Service::class);
    }
}
