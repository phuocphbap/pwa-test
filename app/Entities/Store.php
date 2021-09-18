<?php

namespace App\Entities;

use Illuminate\Database\Eloquent\Model;
use Prettus\Repository\Contracts\Transformable;
use Prettus\Repository\Traits\TransformableTrait;

/**
 * Class Store.
 */
class Store extends Model implements Transformable
{
    use TransformableTrait;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id',
        'store_address',
        'latitude',
        'longitude',
        'state',
        'image_map',
        'phone',
    ];

    const RADIUS_SPOT = 5;

    public function services()
    {
        return $this->hasMany(Service::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function likes()
    {
        return $this->hasMany(StoreLike::class);
    }

    public function categories()
    {
        return $this->belongsToMany(Category::class, 'services', 'store_id', 'category_id')->distinct();
    }
}
