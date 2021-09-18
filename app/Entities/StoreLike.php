<?php

namespace App\Entities;

use Illuminate\Database\Eloquent\Model;
use Prettus\Repository\Contracts\Transformable;
use Prettus\Repository\Traits\TransformableTrait;

/**
 * Class Store.
 */
class StoreLike extends Model implements Transformable
{
    use TransformableTrait;
    protected $table = 'store_likes';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id',
        'user_id',
        'store_id',
        'state',
    ];

    protected $hidden = ['created_at', 'updated_at'];

    public function store()
    {
        return $this->belongsTo(Store::class, 'store_id');
    }
}
