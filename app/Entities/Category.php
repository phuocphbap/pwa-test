<?php

namespace App\Entities;

use Illuminate\Database\Eloquent\Model;
use Prettus\Repository\Contracts\Transformable;
use Prettus\Repository\Traits\TransformableTrait;

/**
 * Class Store.
 */
class Category extends Model implements Transformable
{
    use TransformableTrait;
    protected $table = 'categories';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'parent_id',
        'name',
        'description',
        'cat_sort',
        'state',
    ];

    protected $hidden = ['created_at', 'updated_at'];

    public function parent()
    {
        return $this->belongsTo(self::class, 'parent_id', 'id')->where('state', 1);
    }

    public function children()
    {
        return $this->hasMany(self::class, 'parent_id')->where('state', 1)->orderBy('id');
    }
}
