<?php

namespace App\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Prettus\Repository\Contracts\Transformable;
use Prettus\Repository\Traits\TransformableTrait;

/**
 * Class Service.
 */
class Service extends Model implements Transformable
{
    use TransformableTrait, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'category_id',
        'store_id',
        'region_id',
        'service_image',
        'service_title',
        'service_detail',
        'price',
        'time_required',
        'is_blocked',
        'sort',
        'reason_blocked',
        'time_sort',
        'deleted_at',
        'deleted_by',
    ];

    protected $hidden = ['created_at', 'updated_at', 'time_sort'];

    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id');
    }

    public function likes()
    {
        return $this->hasMany(ServiceLike::class, 'service_id');
    }

    public function agreements()
    {
        return $this->hasMany(RequestConsulting::class, 'service_id')->where('progress', RequestConsulting::PROGRESS_DONE);
    }

    public function requestConsultings()
    {
        return $this->hasMany(RequestConsulting::class);
    }

    public function store()
    {
        return $this->belongsTo(Store::class);
    }

    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

    public function regions()
    {
        return $this->belongsToMany(Region::class, 'service_regions', 'service_id', 'region_id');
    }
}
