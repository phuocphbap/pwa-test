<?php

namespace App\Entities;

use Illuminate\Database\Eloquent\Model;
use Prettus\Repository\Contracts\Transformable;
use Prettus\Repository\Traits\TransformableTrait;

/**
 * Class Store.
 */
class SocialAccount extends Model implements Transformable
{
    use TransformableTrait;
    protected $table = 'social_accounts';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id',
        'social_id',
        'provider',
        'state',
    ];

    protected $hidden = ['created_at', 'updated_at'];

    public function user()
  {
      return $this->belongsTo(User::class);
  }
}
