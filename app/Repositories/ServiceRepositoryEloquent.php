<?php

namespace App\Repositories;

use Carbon\Carbon;
use App\Entities\Service;
use App\Entities\Category;
use App\Entities\VService;
use App\Entities\ServiceLike;
use App\Constant\StatusConstant;
use App\Entities\ServiceSuggest;
use Illuminate\Support\Facades\DB;
use Prettus\Repository\Eloquent\BaseRepository;
use Prettus\Repository\Criteria\RequestCriteria;

/**
 * Class ServiceRepositoryEloquent.
 */
class ServiceRepositoryEloquent extends BaseRepository implements ServiceRepository
{
    /**
     * Specify Model class name.
     *
     * @return string
     */
    public function model()
    {
        return Service::class;
    }

    /**
     * Boot up the repository, pushing criteria.
     */
    public function boot()
    {
        $this->pushCriteria(app(RequestCriteria::class));
    }

    public function getListService($filter)
    {
        return VService::when($filter->has('category_id') && $filter->category_id != null, function ($q) use ($filter) {
            //get children ids.
            $catIds = Category::where('parent_id', $filter->category_id)->pluck('id')->toArray();
            array_push($catIds, (int) $filter->category_id);
            $q->whereIn('category_id', $catIds);
        })
        ->when($filter->has('region_id') && $filter->region_id != null, function ($q) use ($filter) {
            $q->whereHas('regions', function ($q) use ($filter) {
                $q->where('region_id', $filter->region_id);
            });
        })
        ->when($filter->has('order_type') && $filter->order_type != null, function ($q) use ($filter) {
            switch ($filter->order_type) {
                case 'default': $q->orderBy('id', 'desc');
                    break;
                case 'newest': $q->orderBy('id', 'desc');
                    break;
                case 'highest_price': $q->orderBy('price', 'desc');
                    break;
                case 'lowest_price': $q->orderBy('price', 'asc');
                    break;
                case 'highest_likes': $q->withCount('likes')->orderBy('likes_count', 'desc');
                    break;
                case 'highest_register': $q->withCount('agreements')->orderBy('agreements_count', 'desc');
                    break;
                default:
                    $q->orderBy('id', 'desc');
                    break;
            }
        })
        ->when($filter->has('price_from') && $filter->price_from != null, function ($q) use ($filter) {
            $q->where('price', '>=', $filter->price_from);
        })
        ->when($filter->has('price_to') && $filter->price_to != null, function ($q) use ($filter) {
            $q->where('price', '<=', $filter->price_to);
        })
        ->where('view_010_services.state', 1);
    }

    /**
     * Get list services of user.
     */
    public function listServiceBelongToUser($request, $userId)
    {
        $query = VService::when($request->has('user_id') && $request->user_id != null, function ($q) use ($request) {
            $q->where('view_010_services.user_id', $request->user_id);
        });
        return $query;
    }

    /**
     * get owner of Servcies.
     */
    public function getOwnerServices($servicesId)
    {
        return $this->model->find($servicesId);
    }

    /**
     * Get service detail.
     */
    public function getServiceDetail($serviceId)
    {
        return VService::withCount(['agreements', 'likes'])->with('category:id,name,description')->with('regions')
        ->addSelect(\DB::raw('CASE WHEN service_likes.id IS NOT NULL THEN 1 ELSE 0 END AS liked'))
        ->leftJoin('service_likes', function ($q) {
            $q->on('service_likes.service_id', '=', 'view_010_services.id')
            ->where('service_likes.user_id', auth('api')->user() != null ? auth('api')->user()->id : null);
        })
        ->find($serviceId);
    }

    public function createService($data)
    {
        try {
            $service = new $this->model();

            $service->category_id = $data['category_id'];
            $service->service_title = $data['service_title'];
            $service->service_detail = $data['service_detail'];
            $service->price = $data['price'];
            $service->time_required = $data['time_required'];
            $service->store_id = $data['store_id'];
            $service->service_image = $data['service_image'];
            if ($service->save()) {
                $service->regions()->sync(json_decode($data['region_id']));
            }

            return $service;
        } catch (\Throwable $th) {
            \Log::error('Controllers\ServicesController - store : '.$th->getMessage());

            return false;
        }
    }

    public function likeService($serviceId)
    {
        try {
            $serviceLike = ServiceLike::where('user_id', auth()->user()->id)->where('service_id', $serviceId)->first();
            if (!$serviceLike) {
                ServiceLike::create(['user_id' => auth()->user()->id, 'service_id' => $serviceId]);

                return 1;
            } else {
                $serviceLike->delete();

                return 0;
            }
        } catch (\Throwable $th) {
            return false;
        }
    }

    /**
     * Get list related services.
     */
    public function getRelatedtServices($id, $categoryId)
    {
        return VService::withCount(['agreements', 'likes'])
        ->where('category_id', $categoryId)
        ->where('view_010_services.id', '!=', $id)
        ->where('view_010_services.state', 1)
        ->where('view_010_services.is_blocked', StatusConstant::SERVICE_NOT_IS_BLOCKED)
        ->where('view_010_services.sort', StatusConstant::SERVICE_SORT_TRUE)
        ->orderBy('time_sort', 'ASC');
    }

    /**
     * get services already likeed.
     */
    public function listServiceAlreadyLiked()
    {
        return VService::withCount(['agreements', 'likes'])
        ->join('service_likes', VService::TABLE_NAME.'.id', '=', 'service_likes.service_id')
        ->selectRaw(VService::TABLE_NAME.'.id as id, CASE WHEN service_likes.id IS NOT NULL THEN 1 ELSE 0 END AS liked')
        ->whereNull('view_010_services.deleted_at')
        ->where('service_likes.user_id', '=', auth()->user()->id);
    }

    /**
     * Get list services by userId.
     */
    public function getServiceByUserId($userId)
    {
        return VService::withCount(['agreements', 'likes'])
            ->where('view_010_services.user_id', $userId)
            ->where('view_010_services.state', 1);
    }

    /**
     * get list services with relationship.
     */
    public function getListServicesAdmin($filter, $pagination)
    {
        $search = $filter->search;

        return $this->getListService($filter)
                ->when($search, function ($query, $search) {
                    $query->where('service_title', 'LIKE', "%{$search}%");
                })
                ->with('category:id,name')
                ->withCount('likes', 'agreements')
                ->orderBy('view_010_services.id')
                ->paginate($pagination)
                ->appends(request()->query());
    }
    
    /**
     * removeServiceSuggest
     *
     * @return bool
     */
    public function removeServiceSuggest()
    {
        ServiceSuggest::where('service_id', '!=', 0)->delete();
        DB::statement("ALTER TABLE service_suggests AUTO_INCREMENT = 1");

        return true;
    }

    /**
     * store service suggest.
     */
    public function storeServiceSuggest($serviceId)
    {
        ServiceSuggest::create(
            [
                'service_id' => $serviceId,
                'time_sort' => Carbon::parse(Carbon::now())->format('Y-m-d H:i:s.u'),
            ]
        );

        return true;
    }

    /**
     * delete service suggest.
     */
    public function deleteServiceSuggest($serviceId)
    {
        ServiceSuggest::where('id', $serviceId)->delete();

        return true;
    }

    /**
     * get list service suggest side admin.
     */
    public function listServiceSuggestsAdmin()
    {
        return VService::where('view_010_services.state', 1)
                ->with('category:id,name')
                ->where('view_010_services.is_blocked', StatusConstant::SERVICE_NOT_IS_BLOCKED)
                ->withCount('likes', 'agreements')
                ->rightJoin('service_suggests', 'view_010_services.id', '=', 'service_suggests.service_id')
                ->orderBy('service_suggests.time_sort')
                ->get();
    }

    /**
     * update block service.
     */
    public function updateBlockService($serviceId, $reason, $type)
    {
        $this->model->where('id', $serviceId)
            ->update([
                'reason_blocked' => $reason,
                'is_blocked' => $type,
            ]);

        return true;
    }
    
    /**
     * checkServiceIsAvailable
     *
     * @param int $serviceId
     *
     * @return bool
     */
    public function checkServiceIsAvailable(int $serviceId): bool
    {
        $service = VService::where('id', $serviceId)
                    ->whereNull('deleted_at')
                    ->first();

        if ($service && $service->is_blocked == StatusConstant::SERVICE_NOT_IS_BLOCKED
            && $service->state == StatusConstant::USER_ACTIVE) {
            return true;
        }

        return false;
    }
    
    /**
     * getServiceById
     *
     * @param int $serviceId
     * @param array $column = ['*']
     *
     * @return object|null
     */
    public function getServiceById(int $serviceId, array $column = ['*']): object
    {
        return $this->model->where('id', $serviceId)
                    ->select($column)
                    ->first();
    }
    
    /**
     * checkExistsServiceById
     *
     * @param int $serviceId
     *
     * @return bool
     */
    public function checkExistsServiceById(int $serviceId): bool
    {
        return $this->model->where('id', $serviceId)
                    ->exists();
    }
    
    /**
     * removeServiceByAdmin
     *
     * @param int $serviceId
     *
     * @return void
     */
    public function removeServiceByAdmin(int $serviceId)
    {
        return $this->model->where('id', $serviceId)
                    ->update([
                        'deleted_at' => Carbon::now(),
                        'deleted_by' => StatusConstant::TYPE_ADMIN,
                    ]);
    }
    
    /**
     * restoreServiceByAdmin
     *
     * @param int $serviceId
     *
     * @return void
     */
    public function restoreServiceByAdmin(int $serviceId)
    {
        return $this->model->onlyTrashed()
                    ->where('id', $serviceId)
                    ->update([
                        'deleted_at' => null,
                        'deleted_by' => null,
                    ]);
    }
}
