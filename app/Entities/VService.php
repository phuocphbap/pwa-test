<?php

namespace App\Entities;

use Illuminate\Database\Eloquent\Model;
use Prettus\Repository\Contracts\Transformable;
use Prettus\Repository\Traits\TransformableTrait;

/**
 * Class Service.
 */
class VService extends Model implements Transformable
{
    use TransformableTrait;

    public $table = 'view_010_services';
    const TABLE_NAME = 'view_010_services';

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

    public function regions()
    {
        return $this->belongstoMany(Region::class, 'service_regions', 'service_id', 'region_id');
    }
}
