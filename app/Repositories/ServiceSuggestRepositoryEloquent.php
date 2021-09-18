<?php

namespace App\Repositories;

use App\Entities\ServiceSuggest;
use App\Repositories\ServiceSuggestRepository;
use Prettus\Repository\Eloquent\BaseRepository;
use Prettus\Repository\Criteria\RequestCriteria;

/**
 * Class ServiceSuggestRepositoryEloquent.
 */
class ServiceSuggestRepositoryEloquent extends BaseRepository implements ServiceSuggestRepository
{
    /**
     * Specify Model class name.
     *
     * @return string
     */
    public function model()
    {
        return ServiceSuggest::class;
    }

    /**
     * Boot up the repository, pushing criteria.
     */
    public function boot()
    {
        $this->pushCriteria(app(RequestCriteria::class));
    }
    
    /**
     * deleteByServiceId
     *
     * @param int $serviceId
     *
     * @return void
     */
    public function deleteByServiceId(int $serviceId)
    {
        return $this->model->where('service_id', $serviceId)
                    ->delete();
    }
}
