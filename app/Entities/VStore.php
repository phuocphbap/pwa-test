<?php

namespace App\Entities;

use Illuminate\Database\Eloquent\Model;
use Prettus\Repository\Contracts\Transformable;
use Prettus\Repository\Traits\TransformableTrait;

/**
 * Class Service.
 */
class VStore extends Model implements Transformable
{
    use TransformableTrait;

    public $table = 'view_020_stores';
    const TABLE_NAME = 'view_020_stores';
    const STORE_ACTIVE = 1;

    public function services()
    {
        return $this->hasMany(Service::class, 'id');
    }

    public function likes()
    {
        return $this->hasMany(StoreLike::class, 'store_id');
    }

    public function unsatisfyEmotion()
    {
        return $this->hasMany(ServiceReview::class, 'store_id')->where('value', ServiceReview::UNSATISFIED_EMOTION)->where('is_owner', ServiceReview::NOT_OWNER_SERVICE);
    }

    public function mediumEmotion()
    {
        return $this->hasMany(ServiceReview::class, 'store_id')->where('value', ServiceReview::MEDIUM_EMOTION)->where('is_owner', ServiceReview::NOT_OWNER_SERVICE);
    }

    public function satisfyEmotion()
    {
        return $this->hasMany(ServiceReview::class, 'store_id')->where('value', ServiceReview::SATISFIED_EMOTiON)->where('is_owner', ServiceReview::NOT_OWNER_SERVICE);
    }

    public function skills()
    {
        return $this->belongsToMany(Skill::class, 'user_skills', 'user_id', 'skill_id');
    }

    public function categories()
    {
        return $this->belongsToMany(Category::class, 'services', 'store_id', 'category_id')->distinct();
    }

    public function region()
    {
        return $this->belongsTo(Region::class, 'address_id');
    }
}
