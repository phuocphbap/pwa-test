<?php

namespace App\Entities;

use Illuminate\Database\Eloquent\Model;
use Prettus\Repository\Contracts\Transformable;
use Prettus\Repository\Traits\TransformableTrait;

/**
 * Class ArticleImages.
 *
 * @package namespace App\Entities;
 */
class ArticleImages extends Model implements Transformable
{
    use TransformableTrait;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'article_id',
        'img_path',
        'sort',
    ];

    protected $hidden = ['created_at', 'updated_at'];
}
