<?php

namespace App\Entities;

use Illuminate\Database\Eloquent\Model;
use Prettus\Repository\Contracts\Transformable;
use Prettus\Repository\Traits\TransformableTrait;

/**
 * Class Notice.
 *
 * @package namespace App\Entities;
 */
class NoticeCategories extends Model implements Transformable
{
    use TransformableTrait;

    protected $table = 'notice_categories';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
    ];
}
