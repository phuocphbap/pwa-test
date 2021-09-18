<?php

namespace App\Entities;

use Illuminate\Database\Eloquent\Model;
use Prettus\Repository\Contracts\Transformable;
use Prettus\Repository\Traits\TransformableTrait;

/**
 * Class ServiceReview.
 */
class ServiceReview extends Model implements Transformable
{
    use TransformableTrait;
    protected $table = 'service_reviews';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'consulting_id',
        'service_id',
        'store_id',
        'reviewer_id',
        'is_owner',
        'value',
        'message',
        'state',
    ];
    //define value
    const UNSATISFIED_EMOTION = 0;
    const MEDIUM_EMOTION = 1;
    const SATISFIED_EMOTiON = 2;
    //define owner service
    const NOT_OWNER_SERVICE = 0;
    const IS_OWNER_SERVICE = 1;

    public function user()
    {
        return $this->belongsTo(User::class, 'reviewer_id');
    }
}
