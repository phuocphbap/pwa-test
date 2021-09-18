<?php

namespace App\Repositories;

use Prettus\Repository\Eloquent\BaseRepository;
use Prettus\Repository\Criteria\RequestCriteria;
use App\Repositories\StoreIntroductionRepository;
use App\Entities\StoreIntroduction;

/**
 * Class StoreIntroductionRepositoryEloquent.
 *
 * @package namespace App\Repositories;
 */
class StoreIntroductionRepositoryEloquent extends BaseRepository implements StoreIntroductionRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return StoreIntroduction::class;
    }



    /**
     * Boot up the repository, pushing criteria
     */
    public function boot()
    {
        $this->pushCriteria(app(RequestCriteria::class));
    }
}
