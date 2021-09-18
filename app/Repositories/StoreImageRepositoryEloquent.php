<?php

namespace App\Repositories;

use Prettus\Repository\Eloquent\BaseRepository;
use Prettus\Repository\Criteria\RequestCriteria;
use App\Repositories\StoreImageRepository;
use App\Entities\StoreImage;

/**
 * Class StoreImageRepositoryEloquent.
 *
 * @package namespace App\Repositories;
 */
class StoreImageRepositoryEloquent extends BaseRepository implements StoreImageRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return StoreImage::class;
    }



    /**
     * Boot up the repository, pushing criteria
     */
    public function boot()
    {
        $this->pushCriteria(app(RequestCriteria::class));
    }

}
