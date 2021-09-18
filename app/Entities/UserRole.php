<?php

namespace App\Entities;

use Illuminate\Database\Eloquent\Model;
use Prettus\Repository\Contracts\Transformable;
use Prettus\Repository\Traits\TransformableTrait;

/**
 * Class UserRole.
 */
class UserRole extends Model implements Transformable
{
    use TransformableTrait;
    protected $table = 'user_roles';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id',
        'role_id',
    ];

    protected $hidden = ['created_at', 'updated_at'];
}
