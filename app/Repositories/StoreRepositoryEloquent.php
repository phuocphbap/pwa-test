<?php

namespace App\Repositories;

use App\Entities\Store;
use App\Entities\StoreLike;
use App\Entities\VService;
use App\Entities\VStore;
use App\Validators\StoreValidator;
use Prettus\Repository\Criteria\RequestCriteria;
use Prettus\Repository\Eloquent\BaseRepository;

/**
 * Class StoreRepositoryEloquent.
 */
class StoreRepositoryEloquent extends BaseRepository implements StoreRepository
{
    /**
     * Specify Model class name.
     *
     * @return string
     */
    public function model()
    {
        return Store::class;
    }

    /**
     * Specify Validator class name.
     *
     * @return mixed
     */
    public function validator()
    {
        return StoreValidator::class;
    }

    /**
     * Boot up the repository, pushing criteria.
     */
    public function boot()
    {
        $this->pushCriteria(app(RequestCriteria::class));
    }

    public function getListStoreEveryOne($filter)
    {
        return VStore::when($filter->has('region_id') && $filter->region_id != null, function ($q) use ($filter) {
            $q->where('address_id', $filter->region_id);
        })->where(VStore::TABLE_NAME.'.state', 1);
    }

    /**
     * Get get detail of store.
     */
    public function getDetail($storeId)
    {
        return VStore::where(VStore::TABLE_NAME.'.id', $storeId)
        ->where(VStore::TABLE_NAME.'.state', VStore::STORE_ACTIVE);
    }

    /**
     * Get list services of store.
     */
    public function listServiceOfStore($storeId)
    {
        return VService::where('store_id', $storeId);
    }

    public function likeStore($storeId)
    {
        try {
            $storeLike = StoreLike::where('user_id', auth()->user()->id)->where('store_id', $storeId)->first();
            if (!$storeLike) {
                StoreLike::create(['user_id' => auth()->user()->id, 'store_id' => $storeId]);

                return 1;
            } else {
                $storeLike->delete();

                return 0;
            }
        } catch (\Throwable $th) {
            return false;
        }
    }

    /**
     * get spot store radius <= 5 km.
     */
    public function getSpotStore($latitude, $longitude, $user, $search, $regionId)
    {
        // no login
        if (!$user) {
            $store = $this->subGetAllSpotStore($regionId, $search);
        } else {
            // for login
            $userId = $user->id;
            $store = $user->store;
            if (!$regionId) {
                if (!$latitude || !$longitude) {
                    $store = $this->subGetAllSpotStore($regionId, $search);
                } else {
                    $spot = VStore::select('*')
                        ->selectRaw(
                            '( 6371 * acos(
                                cos( radians(?) )
                                * cos(radians(latitude))
                                * cos(radians( longitude)
                                    - radians(?))
                                + sin( radians(?))
                                * sin( radians(latitude))
                                )
                            ) AS distance ')
                        ->having('distance', '<=', Store::RADIUS_SPOT)
                        ->orderBy('distance')
                        ->setBindings([$latitude, $longitude, $latitude]);

                    $store = VStore::joinSub($spot, 'spot', function ($join) {
                        $join->on('view_020_stores.id', '=', 'spot.id');
                    })
                        ->where('view_020_stores.state', VStore::STORE_ACTIVE)
                        ->when($search, function ($query, $search) {
                            $query->where('view_020_stores.store_address', 'LIKE', "%{$search}%");
                        })
                        ->withCount('likes', 'unsatisfyEmotion', 'mediumEmotion', 'satisfyEmotion')
                        ->with('categories')
                        ->leftJoin('store_likes', function ($q) use ($userId) {
                            $q->on('store_likes.store_id', '=', 'view_020_stores.id')
                            ->where('store_likes.user_id', '=', $userId);
                        })
                        ->selectRaw('view_020_stores.id as id, CASE WHEN store_likes.id IS NOT NULL THEN 1 ELSE 0 END AS liked')
                        ->selectRaw('view_020_stores.*')
                        ->selectRaw('spot.distance')
                        ->orderBy('spot.distance');
                }
            } else {
                $store = VStore::where('address_id', $regionId)
                    ->where('view_020_stores.state', VStore::STORE_ACTIVE)
                    ->when($search, function ($query, $search) {
                        $query->where('view_020_stores.store_address', 'LIKE', "%{$search}%");
                    })
                    ->withCount('likes', 'unsatisfyEmotion', 'mediumEmotion', 'satisfyEmotion')
                    ->with('categories')
                    ->leftJoin('store_likes', function ($q) use ($userId) {
                        $q->on('store_likes.store_id', '=', 'view_020_stores.id')
                        ->where('store_likes.user_id', '=', $userId);
                    })
                    ->selectRaw('view_020_stores.id as id, CASE WHEN store_likes.id IS NOT NULL THEN 1 ELSE 0 END AS liked')
                    ->selectRaw('view_020_stores.*');
            }
        }

        return $store;
    }

    /**
     * get full detail store.
     */
    public function getAllDetailStoreById($storeId)
    {
        return VStore::withCount('likes', 'unsatisfyEmotion', 'mediumEmotion', 'satisfyEmotion')
            ->with('categories')
            ->with('region:id,state_name')
            ->leftJoin('store_likes', function ($q) {
                $q->on('store_likes.store_id', '=', 'view_020_stores.id')
                ->where('store_likes.user_id', '=', auth('api')->user() != null ? auth('api')->user()->id : null);
            })
            ->selectRaw('view_020_stores.id as id, CASE WHEN store_likes.id IS NOT NULL THEN 1 ELSE 0 END AS liked')
            ->selectRaw('view_020_stores.*')
            ->where('view_020_stores.id', $storeId)
            ->where('view_020_stores.state', VStore::STORE_ACTIVE)
            ->first();
    }

    /**
     * get store already likeed.
     */
    public function listStoreAlreadyLiked()
    {
        return VStore::withCount('likes', 'unsatisfyEmotion', 'mediumEmotion', 'satisfyEmotion')
        ->with('categories')
        ->join('store_likes', VStore::TABLE_NAME.'.id', '=', 'store_likes.store_id')
        ->selectRaw(VStore::TABLE_NAME.'.id as id, CASE WHEN store_likes.id IS NOT NULL THEN 1 ELSE 0 END AS liked')
        ->where('store_likes.user_id', '=', auth()->user()->id);
    }

    /**
     * sub function get all spot store.
     */
    public function subGetAllSpotStore($regionId, $search)
    {
        
        return VStore::where('view_020_stores.state', VStore::STORE_ACTIVE)
                ->when($regionId, function ($query, $regionId) {
                    $query->where('view_020_stores.address_id', $regionId);
                })
                ->when($search, function ($query, $search) {
                    $query->where('view_020_stores.store_address', 'LIKE', "%{$search}%");
                })
                ->withCount('likes', 'unsatisfyEmotion', 'mediumEmotion', 'satisfyEmotion')
                ->with('categories')
                ->leftJoin('store_likes', function ($q) {
                    $q->on('store_likes.store_id', '=', 'view_020_stores.id')
                    ->whereNull('store_likes.user_id');
                })
                ->selectRaw('view_020_stores.id as id, CASE WHEN store_likes.id IS NOT NULL THEN 1 ELSE 0 END AS liked')
                ->selectRaw('view_020_stores.*')
                ->inRandomOrder();
    }
}
