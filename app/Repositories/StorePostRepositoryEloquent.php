<?php

namespace App\Repositories;

use Prettus\Repository\Eloquent\BaseRepository;
use Prettus\Repository\Criteria\RequestCriteria;
use App\Repositories\StorePostRepository;
use App\Entities\StorePost;
use App\Validators\StorePostValidator;

/**
 * Class StorePostRepositoryEloquent.
 *
 * @package namespace App\Repositories;
 */
class StorePostRepositoryEloquent extends BaseRepository implements StorePostRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return StorePost::class;
    }

    

    /**
     * Boot up the repository, pushing criteria
     */
    public function boot()
    {
        $this->pushCriteria(app(RequestCriteria::class));
    }
    
}
