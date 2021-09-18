<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Constant\StatusConstant;
use App\Services\ServiceService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Helpers\Facades\UploadS3Helper;
use App\Repositories\ServiceRepository;
use App\Http\Requests\LikeServiceRequest;
use App\Http\Requests\BlockServiceRequest;
use App\Http\Requests\ServiceCreateRequest;
use App\Http\Requests\ServiceUpdateRequest;
use Prettus\Validator\Exceptions\ValidatorException;

/**
 * Class ServicesController.
 */
class ServicesController extends Controller
{
    /**
     * @var ServiceRepository
     */
    protected $repository;

    /**
     * @var ServiceService
     */
    protected $serService;

    /**
     * ServicesController constructor.
     */
    public function __construct(ServiceRepository $repository, ServiceService $serService)
    {
        $this->repository = $repository;
        $this->serService = $serService;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $this->repository->pushCriteria(app('Prettus\Repository\Criteria\RequestCriteria'));
        $filter = newCond([
            'category_id' => $request->category_id ?: '',
            'region_id' => $request->region_id ?: '',
            'order_type' => $request->order_type ?: '',
            'price_from' => $request->price_from ?: '',
            'price_to' => $request->price_to ?: '',
        ]);
        $pagination = $request->has('per_page') && is_numeric($request->per_page) ? $request->per_page : 10;

        $services = $this->repository->getListService($filter)
                ->where('view_010_services.is_blocked', StatusConstant::SERVICE_NOT_IS_BLOCKED)
                ->with('category')
                ->withCount('likes', 'agreements')
                ->addSelect(\DB::raw('view_010_services.id as id, CASE WHEN service_likes.id IS NOT NULL THEN 1 ELSE 0 END AS liked'))
                ->leftJoin('service_likes', function ($q) {
                    $q->on('service_likes.service_id', '=', 'view_010_services.id')
                    ->where('service_likes.user_id', '=', auth('api')->user() != null ? auth('api')->user()->id : null);
                })
                ->whereNull('view_010_services.deleted_at')
                ->orderBy('view_010_services.id')
                ->paginate($pagination)
                ->appends(request()->query());

        return response()->json([
            'status' => true,
            'data' => $services,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return \Illuminate\Http\Response
     *
     * @throws \Prettus\Validator\Exceptions\ValidatorException
     */
    public function store(ServiceCreateRequest $request)
    {
        try {
            DB::beginTransaction();
            $data = $request->only('category_id', 'region_id', 'service_title', 'service_detail', 'price', 'time_required');
            $data['store_id'] = auth()->user()->store->id;
            $imageLink = UploadS3Helper::uploadImage($request->service_image, 'service');
            $data['service_image'] = $imageLink;
            $service = $this->repository->createService($data);

            if ($service) {
                DB::commit();

                return response()->json(['success' => true, 'data' => $service, 'message' => __('api.service.create_success')]);
            }
            DB::rollBack();

            return response()->json(['error' => true, 'message' => __('api.service.error_create')]);
        } catch (ValidatorException $e) {
            DB::rollBack();
            Log::error('Controllers\ServicesController - store : '.$e->getMessage());

            return response()->json(['error' => true, 'message' => __('api.service.error_create')]);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     *
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $service = $this->repository->getServiceDetail($id);
        if ($service) {
            $categoryList = getCategoryByChild($service->category_id);
            $service['categories'] = $categoryList;

            return response()->json([
                'status' => true,
                'data' => $service,
            ]);
        }

        return response()->json(['error' => true, 'message' => __('api.service.service_not_exists')]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @return Response
     *
     * @throws \Prettus\Validator\Exceptions\ValidatorException
     */
    public function update(ServiceUpdateRequest $request)
    {
        try {
            DB::beginTransaction();
            $checkExists = $this->repository->checkExistsServiceById($request->id);
            if (!$checkExists) {
                return response()->json(['error' => true, 'message' => __('api.service.service_not_exists')]);
            }

            $service = $this->repository->findOrFail($request->id);
            if (auth()->user()->id != $service->store->user->id) {
                return response()->json(['error' => true, 'message' => __('api.common.failed')]);
            }

            // check service in progress consulting do not allow update
            $serInProgress = $this->serService->servicesIsProgress($request->id);
            if ($serInProgress) {
                return response()->json(['error' => true, 'message' => __('api.request-consulting.service_exists_in_progress')]);
            }

            $dataUpdate = $request->only('category_id', 'service_title', 'service_detail', 'price', 'time_required');
            if ($request->service_image) {
                //delete old service_image
                if ($service->service_image) {
                    UploadS3Helper::deleteImage($service->service_image);
                }
                $imageLink = UploadS3Helper::uploadImage($request->service_image, 'service');

                $dataUpdate['service_image'] = $imageLink;
            }
            $data = $this->repository->update($dataUpdate, $request->id);
            if ($data) {
                $data->regions()->sync(json_decode($request->region_id));
            }
            DB::commit();

            return response()->json(['success' => true, 'data' => $data, 'message' => __('api.common.update_success')]);
        } catch (ValidatorException $e) {
            DB::rollBack();

            return response()->json(['error' => true, 'message' => __('api.common.failed')]);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try {
            DB::beginTransaction();
            $user = auth()->user();
            $checkExists = $this->repository->checkExistsServiceById($id);
            if (!$checkExists) {
                return response()->json(['error' => true, 'message' => __('api.service.service_not_exists')]);
            }

            $column = ['id', 'store_id'];
            $service = $this->repository->getServiceById($id, $column);
            if ($service->store_id != $user->store->id) {
                return response()->json(['error' => true, 'message' => __('api.common.you_not_permission')]);
            }

            $check = $this->serService->servicesIsProgress($id);
            if ($check) {
                return response()->json(['error' => true, 'message' => __('api.request-consulting.service_exists_in_progress')]);
            }

            $this->repository->find($id)->regions()->detach();
            $this->serService->removeServiceSuggest($id);
            $this->repository->delete($id);

            DB::commit();
            return response()->json([
                'status' => true,
                'data' => null
            ]);
        } catch (\Throwable $th) {
            DB::rollBack();
            Log::error('Controllers\Api\ServicesController - destroy : '.$th->getMessage());
            return response()->json(['error' => true, 'message' => __('api.common.failed')]);
        }
    }

    /*
     * Display a listing of list services suggested.
     *
     * @return \Illuminate\Http\Response
     */
    public function listServiceSuggests(Request $request)
    {
        $filter = newCond([
            'category_id' => $request->category_id ?: '',
            'region_id' => $request->region_id ?: '',
            'order_type' => $request->order_type ?: '',
            'price_from' => $request->price_from ?: '',
            'price_to' => $request->price_to ?: '',
        ]);
        $pagination = $request->has('per_page') && is_numeric($request->per_page) ? $request->per_page : 10;

        $services = $this->repository->getListService($filter)
                ->where('view_010_services.is_blocked', StatusConstant::SERVICE_NOT_IS_BLOCKED)
                ->whereNull('view_010_services.deleted_at')
                ->with('category')
                ->withCount('likes', 'agreements')
                ->rightJoin('service_suggests', 'view_010_services.id', '=', 'service_suggests.service_id')
                ->addSelect(\DB::raw('view_010_services.id as id, CASE WHEN service_likes.id IS NOT NULL THEN 1 ELSE 0 END AS liked'))
                ->leftJoin('service_likes', function ($q) {
                    $q->on('service_likes.service_id', '=', 'view_010_services.id')
                    ->where('service_likes.user_id', '=', auth('api')->user() != null ? auth('api')->user()->id : null);
                })
                ->orderBy('service_suggests.time_sort')
                ->paginate($pagination);

        return response()->json([
            'status' => true,
            'data' => $services,
        ]);
    }

    /*
     * Display a listing of list services belong to user.
     *
     * @return \Illuminate\Http\Response
     */
    public function listServicesOfUser(Request $request)
    {
        $pagination = $request->has('per_page') && is_numeric($request->per_page) ? $request->per_page : 10;
        $userId = auth('api')->user()->id ?? null;
        //check user login
        $userLogin = auth('api')->user() != null ? auth('api')->user()->id : null;
        $checkIsOwnerService = true;
        if ($userLogin == $request->user_id) {
            $checkIsOwnerService = false;
        }

        $services = $this->repository->listServiceBelongToUser($request, $userId)
                ->when($checkIsOwnerService, function ($q) {
                    $q->where('view_010_services.is_blocked', 0)
                        ->whereNull('deleted_at');
                })
                ->when(!$checkIsOwnerService, function ($q) {
                    $q->whereRaw('((deleted_by is not null and deleted_at is not null) OR (deleted_by is null and deleted_at is null))');
                })
                ->with('category')
                ->withCount('likes', 'agreements')
                ->addSelect(\DB::raw('CASE WHEN service_likes.id IS NOT NULL THEN 1 ELSE 0 END AS liked'))
                ->leftJoin('service_likes', function ($q) use ($userId) {
                    $q->on('service_likes.service_id', '=', 'view_010_services.id')
                    ->where('service_likes.user_id', '=', $userId);
                })
                ->orderBy('view_010_services.id')
                ->paginate($pagination);

        return response()->json([
            'status' => true,
            'data' => $services,
        ]);
    }

    /*
     * like service
     */
    public function likeService(LikeServiceRequest $request)
    {
        $status = $this->repository->likeService($request->service_id);
        switch ($status) {
            case 1:
                return response()->json(['status' => $status]);
                break;
            case 0:
                return response()->json(['status' => $status]);
                break;
            default:
                return response()->json(['error' => true, 'message' => __('api.service.error_like')]);
                break;
        }
    }

    /*
     * related services
     */
    public function relatedServices(Request $request)
    {
        try {
            if (!$request->service_id) {
                return response()->json(['error' => true, 'message' => __('api.service.not_found')]);
            }

            $pagination = $request->has('per_page') && is_numeric($request->per_page) ? $request->per_page : 10;
            $service = $this->repository->findOrFail($request->service_id);
            $services = $this->repository->getRelatedtServices($request->service_id, $service->category_id)
                        ->with('category')
                        ->addSelect(\DB::raw('view_010_services.id as id, CASE WHEN service_likes.id IS NOT NULL THEN 1 ELSE 0 END AS liked'))
                        ->leftJoin('service_likes', function ($q) {
                            $q->on('service_likes.service_id', '=', 'view_010_services.id')
                            ->where('service_likes.user_id', '=', auth('api')->user() != null ? auth('api')->user()->id : null);
                        })
                        ->paginate($pagination);

            return response()->json([
                'success' => true,
                'data' => $services,
            ]);
        } catch (\Throwable $th) {
            Log::error('Controllers\Api\ServicesController - relatedServices : '.$th->getMessage());
            return response()->json(['error' => true, 'message' => __('api.common.failed')]);
        }
    }

    /**
     * get services already liked.
     */
    public function listServiceLiked(Request $request)
    {
        $pagination = $request->has('per_page') && is_numeric($request->per_page) ? $request->per_page : 10;
        $services = $this->repository->listServiceAlreadyLiked()->with('category')->paginate($pagination);

        return response()->json([
            'success' => true,
            'data' => $services,
        ]);
    }
    
    /**
     * block service
     *
     * @param BlockServiceRequest $request
     *
     * @return void
     */
    public function block(BlockServiceRequest $request)
    {
        try {
            $checkService = $this->repository->checkServiceIsAvailable($request->serviceId);
            if (!$checkService) {
                return response()->json(['error' => true, 'message' => __('api.service.blocked_or_not_exists')]);
            }

            $checkServiceIsProgress = $this->serService->servicesIsProgress($request->serviceId);
            if ($checkServiceIsProgress) {
                return response()->json(['error' => true, 'message' => __('api.request-consulting.service_exists_in_progress')]);
            }
            
            $user = auth()->user();
            $service = $this->repository->find($request->serviceId);
            if ($service->store_id != $user->store->id) {
                return response()->json(['error' => true, 'message' => __('api.common.you_not_permission')]);
            }
            
            $data = $this->repository->update(
                ['is_blocked' => StatusConstant::SERVICE_IS_BLOCKED],
                $request->serviceId
            );

            return response()->json(['status' => true, 'data' => $data]);
        } catch (\Throwable $th) {
            Log::error('Controllers\ServicesController - block : '.$th->getMessage());
            return response()->json(['error' => true, 'message' => __('api.exception')]);
        }
    }
        
    /**
     * unlock
     *
     * @param mixed $serviceId
     *
     * @return void
     */
    public function unlock(BlockServiceRequest $request)
    {
        try {
            $checkExists = $this->repository->checkExistsServiceById($request->serviceId);
            if (!$checkExists) {
                return response()->json(['error' => true, 'message' => __('api.service.service_not_exists')]);
            }
            $service = $this->repository->find($request->serviceId);
            $user = auth()->user();
            if ($service->store_id != $user->store->id) {
                return response()->json(['error' => true, 'message' => __('api.common.you_not_permission')]);
            }
            if ($service->is_blocked == StatusConstant::SERVICE_NOT_IS_BLOCKED) {
                return response()->json(['error' => true, 'message' => __('api.service.service_is_active')]);
            }

            $data = $this->repository->update(
                ['is_blocked' => StatusConstant::SERVICE_NOT_IS_BLOCKED],
                $request->serviceId
            );

            return response()->json(['status' => true, 'data' => $data]);
        } catch (\Throwable $th) {
            Log::error('Controllers\ServicesController - unlock : '.$th->getMessage());
            return response()->json(['error' => true, 'message' => __('api.exception')]);
        }
    }
}
