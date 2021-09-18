<?php

namespace App\Entities;

use Illuminate\Database\Eloquent\Model;
use Prettus\Repository\Contracts\Transformable;
use Prettus\Repository\Traits\TransformableTrait;

/**
 * Class StorePost.
 */
class StorePost extends Model implements Transformable
{
    use TransformableTrait;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id',
        'store_id',
        'link',
        'order',
        'type',
    ];

    const NEW_POST = 'news';
    const IMAGE_POST = 'images';
}
