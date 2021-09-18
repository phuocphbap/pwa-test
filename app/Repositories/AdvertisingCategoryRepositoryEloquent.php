<?php

namespace App\Repositories;

use Prettus\Repository\Eloquent\BaseRepository;
use Prettus\Repository\Criteria\RequestCriteria;
use App\Repositories\AdvertisingCategoryRepository;
use App\Entities\AdvertisingCategory;
use App\Validators\AdvertisingCategoryValidator;

/**
 * Class AdvertisingCategoryRepositoryEloquent.
 *
 * @package namespace App\Repositories;
 */
class AdvertisingCategoryRepositoryEloquent extends BaseRepository implements AdvertisingCategoryRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return AdvertisingCategory::class;
    }

    /**
     * Boot up the repository, pushing criteria
     */
    public function boot()
    {
        $this->pushCriteria(app(RequestCriteria::class));
    }

    /**
     * get list category ads
     */
    public function getListCategoryAds()
    {
        return $this->model->all();
    }
}
