<?php

namespace App\Repositories;

use App\Entities\AdvertisingBlock;
use App\Entities\AdvertisingCategory;
use Prettus\Repository\Eloquent\BaseRepository;
use App\Repositories\AdvertisingBlockRepository;
use Prettus\Repository\Criteria\RequestCriteria;

/**
 * Class AdvertisingBlockRepositoryEloquent.
 *
 * @package namespace App\Repositories;
 */
class AdvertisingBlockRepositoryEloquent extends BaseRepository implements AdvertisingBlockRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return AdvertisingBlock::class;
    }

    /**
     * Boot up the repository, pushing criteria
     */
    public function boot()
    {
        $this->pushCriteria(app(RequestCriteria::class));
    }

    /**
     * get list block ads by category_id
     */
    public function getListBlockAdsByCategory($categoryId)
    {
        return $this->model->where('category_id', $categoryId);
    }

    /**
     * update content
     */
    public function updateContentBlock($blockId, $contents)
    {
        return $this->model->where('id', $blockId)
                        ->update(['contents' => $contents]);
    }

    /**
     *
     */
    public function getListAdvertising($name)
    {
        $categoryId = AdvertisingCategory::where('name', $name)->first()->id;
        return $this->model->with('media')
                        ->where('category_id', $categoryId)
                        ->get();
    }
}
