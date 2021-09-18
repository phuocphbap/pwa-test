<?php

namespace App\Repositories;

use App\Entities\ServiceRegion;
use App\Repositories\ServiceRegionRepository;
use Prettus\Repository\Eloquent\BaseRepository;
use Prettus\Repository\Criteria\RequestCriteria;

class ServiceRegionRepositoryEloquent extends BaseRepository implements ServiceRegionRepository
{
    /**
     * Specify Model class name.
     *
     * @return string
     */
    public function model()
    {
        return ServiceRegion::class;
    }

    /**
     * Boot up the repository, pushing criteria.
     */
    public function boot()
    {
        $this->pushCriteria(app(RequestCriteria::class));
    }
}
