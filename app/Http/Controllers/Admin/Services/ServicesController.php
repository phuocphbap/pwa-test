<?php

namespace App\Http\Controllers\Admin\Services;

use Illuminate\Http\Request;
use App\Constant\StatusConstant;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use App\Repositories\ServiceRepository;
use App\Services\Admin\ServicesService;
use App\Services\Admin\NotificationsService;
use App\Http\Requests\Admin\RemoveServicesRequest;
use App\Http\Requests\Admin\HandleBlockServiceRequest;
use App\Http\Requests\Admin\StoreServiceSuggestRequest;
use App\Http\Requests\Admin\StoreSuggestRelatedRequest;

class ServicesController extends Controller
{
    /**
     * @var ServiceRepository
     */
    protected $repository;

    /**
     * @var ServicesService
     */
    protected $serService;

    /**
     * @var NotificationsService
     */
    protected $noticeService;

    /**
     * BonusesController constructor.
     */
    public function __construct(
        ServiceRepository $repository,
        ServicesService $serService,
        NotificationsService $noticeService
    ) {
        $this->repository = $repository;
        $this->serService = $serService;
        $this->noticeService = $noticeService;
    }

    /**
     * get list servcies
     */
    public function getListServices(Request $request)
    {
        try {
            $pagination = $request->has('per_page') && is_numeric($request->per_page) ? $request->per_page : 10;
            $filter = newCond([
                'category_id' => $request->category_id ?? null,
                'region_id' => $request->region_id ?? null,
                'order_type' => $request->order_type ?? null,
                'filter_price' => $request->filter_price ?? null,
                'search' => $request->search ?? null,
            ]);
            $data = $this->repository->getListServicesAdmin($filter, $pagination);

            return response()->json([
                'success' => true,
                'data' => $data,
            ]);
        } catch (\Throwable $th) {
            Log::error('Controllers\Admin\Services\ServicesController - getListServices : '.$th->getMessage());
            return response()->json([
                'error' => true,
                'message' => __('api.exception'),
            ]);
        }
    }

    /**
     * store services suggest
     */
    public function storeServiceSuggest(StoreServiceSuggestRequest $request)
    {
        try {
            DB::beginTransaction();
            $this->serService->storeServiceSuggest($request->service_id);
            DB::commit();

            return response()->json([
                'success' => true,
                'data' => null,
            ]);
        } catch (\Throwable $th) {
            Log::error('Controllers\Admin\Services\ServicesController - storeServiceSuggest : '.$th->getMessage());
            DB::rollback();
            return response()->json([
                'error' => true,
                'message' => __('api.exception'),
            ]);
        }
    }

    /**
     * get services suggest
     */
    public function getServiceSuggest(Request $request)
    {
        try {
            $data = $this->repository->listServiceSuggestsAdmin();

            return response()->json([
                'success' => true,
                'data' => $data,
            ]);
        } catch (\Throwable $th) {
            Log::error('Controllers\Admin\Services\ServicesController - getServiceSuggest : '.$th->getMessage());
            return response()->json([
                'error' => true,
                'message' => __('api.exception'),
            ]);
        }
    }

    /**
     * store suggest related service
     */
    public function storeSuggestRelated(StoreSuggestRelatedRequest $request)
    {
        try {
            DB::beginTransaction();
            $this->serService->updateSuggestRelated($request->service_id, $request->type);
            DB::commit();

            return response()->json([
                'success' => true,
                'data' => null,
            ]);
        } catch (\Throwable $th) {
            Log::error('Controllers\Admin\Services\ServicesController - storeSuggestRelated : '.$th->getMessage());
            DB::rollback();
            return response()->json([
                'error' => true,
                'message' => __('api.exception'),
            ]);
        }
    }

    /**
     * handle block service
     */
    public function handleBlockService(HandleBlockServiceRequest $request)
    {
        try {
            DB::beginTransaction();
            $reason = $request->reason ?? null;
            $this->serService->handleBlockService($request->serviceId, $request->type, $reason);
            if ($request->type = StatusConstant::SERVICE_IS_BLOCKED) {
                $service = $this->serService->getServiceById($request->serviceId);
                $this->noticeService->noticeBlockService($service, $reason);
            }
            DB::commit();

            return response()->json([
                'status' => true,
                'data' => null,
            ]);
        } catch (\Throwable $th) {
            Log::error('Controllers\Admin\Services\ServicesController - handleBlockService : '.$th->getMessage());
            DB::rollback();
            return response()->json([
                'error' => true,
                'message' => __('api.exception'),
            ]);
        }
    }
    
    /**
     * removeServices
     *
     * @param mixed $serviceId
     *
     * @return void
     */
    public function removeServices(RemoveServicesRequest $request)
    {
        try {
            DB::beginTransaction();
            $checkExists = $this->repository->withTrashed()->find($request->serviceId);
            if (!$checkExists) {
                return response()->json(['error' => true, 'message' => __('api.service.service_not_exists')]);
            }

            if ($checkExists->deleted_at) {
                return response()->json(['error' => true, 'message' => __('api.service.service_removed')]);
            }

            $serInProgress = $this->serService->checkServiceInProgress($request->serviceId);
            if ($serInProgress) {
                return response()->json(['error' => true, 'message' => __('api.request-consulting.service_exists_in_progress')]);
            }

            $this->serService->removeServiceSuggestByAdmin($request->serviceId);
            $this->serService->removeServiceRegionByAdmin($request->serviceId);
            $this->repository->update(['reason_blocked' => $request->reason], $request->serviceId);
            $this->repository->removeServiceByAdmin($request->serviceId);

            $service = $this->serService->getServiceById($request->serviceId);
            $this->noticeService->noticeBlockService($service, $request->reason);

            DB::commit();
            return response()->json([
                'status' => true,
                'data' => null
            ]);
        } catch (\Throwable $th) {
            Log::error('Controllers\Admin\Services\ServicesController - removeServices : '.$th->getMessage());
            DB::rollback();
            return response()->json([
                'error' => true,
                'message' => __('api.exception'),
            ]);
        }
    }
    
    /**
     * recoverServices
     *
     * @param mixed $serviceId
     *
     * @return void
     */
    public function recoverServices($serviceId)
    {
        try {
            DB::beginTransaction();
            if (!$serviceId || $serviceId  && !is_numeric($serviceId)) {
                return response()->json(['error' => true, 'message' => __('api.common.not_found')]);
            }

            $checkExists = $this->repository->onlyTrashed()->find($serviceId);
            if (!$checkExists) {
                return response()->json(['error' => true, 'message' => __('api.common.not_found')]);
            }

            if ($checkExists->deleted_by != StatusConstant::TYPE_ADMIN) {
                return response()->json(['error' => true, 'message' => __('api.service.service_removed_by_user_owner')]);
            }

            $this->serService->restoreServiceRegionByAdmin($serviceId);
            $this->repository->restoreServiceByAdmin($serviceId);
            DB::commit();

            return response()->json([
                'status' => true,
                'data' => null
            ]);
        } catch (\Throwable $th) {
            Log::error('Controllers\Admin\Services\ServicesController - recoverServices : '.$th->getMessage());
            DB::rollback();
            return response()->json([
                'error' => true,
                'message' => __('api.exception'),
            ]);
        }
    }
}
