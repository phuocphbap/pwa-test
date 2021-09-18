<?php

namespace App\Entities;

use Illuminate\Database\Eloquent\Model;
use Prettus\Repository\Contracts\Transformable;
use Prettus\Repository\Traits\TransformableTrait;

/**
 * Class AdvertisingBlock.
 *
 * @package namespace App\Entities;
 */
class AdvertisingBlock extends Model implements Transformable
{
    use TransformableTrait;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'category_id',
        'contents',
        'name',
    ];

    protected $hidden = ['created_at', 'updated_at'];

    public function media()
    {
        return $this->hasMany(AdvertisingMedia::class, 'block_id');
    }
}
