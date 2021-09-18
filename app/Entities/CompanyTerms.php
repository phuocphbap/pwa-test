<?php

namespace App\Entities;

use Illuminate\Database\Eloquent\Model;
use Prettus\Repository\Contracts\Transformable;
use Prettus\Repository\Traits\TransformableTrait;

/**
 * Class CompanyTerms.
 *
 * @package namespace App\Entities;
 */
class CompanyTerms extends Model implements Transformable
{
    use TransformableTrait;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'file_name',
        'file_path',
        'file_size',
        'type',
    ];

    public function getFileSizeAttribute($value)
    {
        return $value . 'KB';
    }
}
