<?php

namespace App\Repositories;

use App\Entities\Bill;
use Prettus\Repository\Eloquent\BaseRepository;
use Prettus\Repository\Criteria\RequestCriteria;

/**
 * Class StoreArticleRepositoryEloquent.
 *
 * @package namespace App\Repositories;
 */
class BillRepositoryEloquent extends BaseRepository implements BillRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return Bill::class;
    }

    /**
     * Boot up the repository, pushing criteria
     */
    public function boot()
    {
        $this->pushCriteria(app(RequestCriteria::class));
    }
}
