<?php

namespace App\Http\Controllers\Api;

use Throwable;
use App\Entities\User;
use Illuminate\Http\Request;
use App\Constant\StatusConstant;
use App\Services\FirebaseService;
use Illuminate\Support\Facades\DB;
use App\Entities\RequestConsulting;
use Illuminate\Support\Facades\Log;
use App\Repositories\ServiceRepository;
use App\Helpers\General\CollectionHelper;
use App\Repositories\RequestConsultingRepository;
use Prettus\Validator\Exceptions\ValidatorException;
use App\Http\Requests\RequestConsultingCreateRequest;
use App\Http\Requests\OwnerCancelRequestConsultingRequest;

/**
 * Class RequestConsultingsController.
 */
class RequestConsultingsController extends Controller
{
    /**
     * @var RequestConsultingRepository
     */
    protected $repository;

    /**
     * @var ServiceRepository
     */
    protected $serviceRepository;

    /**
     * @var FirebaseService
     */
    protected $firebaseService;

    /**
     * RequestConsultingsController constructor.
     */
    public function __construct(
        RequestConsultingRepository $repository,
        ServiceRepository $serviceRepository,
        FirebaseService $firebaseService
    ) {
        $this->repository = $repository;
        $this->serviceRepository = $serviceRepository;
        $this->firebaseService = $firebaseService;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        try {
            $user = auth()->user();
            $progressType = $request->progress_type ?? null;
            $orderBy = $request->orderby ?? null;
            $unreadMess = $request->unread_message ?? false;
            $pagination = $request->has('per_page') && is_numeric($request->per_page) ? $request->per_page : 10;

            if ($unreadMess) {
                $progress = $this->repository->listRequestConsulting($progressType, $orderBy);
                // get unread message from firebase
                $data = $this->firebaseService->getLatestProgressUnreadMessage($user, $progress);
                if (!$data) {
                    return response()->json(['status' => true, 'data' => $data]);
                }
                $data = CollectionHelper::paginate($data, $pagination);
            } else {
                $data = $this->repository->listRequestConsulting($progressType, $orderBy)->paginate($pagination);
            }

            return response()->json([
                'status' => true,
                'data' => $data,
            ]);
        } catch (Throwable $th) {
            Log::error('Controllers\Api\RequestConsultingsController - index : '.$th->getMessage());

            return response()->json([
                'error' => true,
                'message' => __('api.exception'),
            ]);
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return \Illuminate\Http\Response
     *
     * @throws \Prettus\Validator\Exceptions\ValidatorException
     */
    public function store(RequestConsultingCreateRequest $request)
    {
        try {
            if (auth()->user()->id) {
                $checkService = $this->serviceRepository->checkServiceIsAvailable($request->service_id);
                if (!$checkService) {
                    return response()->json(['status' => false, 'message' => __('api.service.blocked_or_not_exists')]);
                }

                $service = $this->serviceRepository->find($request->service_id);
                $dataRequest = $request->only('service_id', 'message');
                $dataRequest['customer_id'] = auth()->user()->id;
                $dataRequest['price_requested'] = $service->price;
                $dataRequest['title_service_request'] = $service->service_title;
                $dataRequest['category_name_request'] = $service->category->name ?? null;
                $dataRequest['owner_id'] = $service->store->user_id;

                $data = $this->repository->create($dataRequest);
                return response()->json([
                    'status' => true,
                    'data' => $data,
                ]);
            } else {
                return response()->json([
                    'status' => false,
                    'message' => __('api.common.not_found'),
                ]);
            }
        } catch (ValidatorException $e) {
            if ($request->wantsJson()) {
                return response()->json([
                    'status' => false,
                    'message' => __('api.common.failed'),
                ]);
            }
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
        if (auth()->user()->id) {
            $requestConsulting = $this->repository->where('state', RequestConsulting::STATE_ACTICE)->where('id', $id)->first();
            if ($requestConsulting) {
                $requestConsulting['service'] = $this->serviceRepository->getServiceDetail($requestConsulting->service_id);
                $requestConsulting['is_owner'] = $requestConsulting->owner_id == auth()->user()->id ? true : false;
                $requestConsulting['owner_name'] = User::find($requestConsulting->owner_id)->user_name;
                $requestConsulting['customer_name'] = User::find($requestConsulting->customer_id)->user_name;

                return response()->json([
                    'status' => true,
                    'data' => $requestConsulting,
                ]);
            }

            return response()->json([
                'status' => true,
                'message' => __('api.request-consulting.canceled_request'),
            ]);
        } else {
            return response()->json([
                'status' => false,
                'message' => __('api.common.not_found'),
            ]);
        }
    }

    /**
     * Update progress when confirm request consulting.
     *
     * @param int $id
     *
     * @return \Illuminate\Http\Response
     */
    public function confirmRequestConsulting(Request $request, $id)
    {
        try {
            $requestConsulting = $this->repository->findOrFail($id);
            if ($requestConsulting->progress == RequestConsulting::PROGRESS_BEFORE_AGREEMENT) {
                $requestConsulting->update(['progress' => RequestConsulting::PROGRESS_CONFIRMED_REQUEST]);
            } else {
                return response()->json([
                    'status' => false,
                    'message' => __('api.common.failed'),
                ]);
            }
            $requestConsulting['service'] = $this->serviceRepository->getServiceDetail($requestConsulting->service_id);
            $requestConsulting['is_owner'] = $requestConsulting->owner_id == auth()->user()->id ? true : false;
            $requestConsulting['owner_name'] = User::find($requestConsulting->owner_id)->user_name;
            $requestConsulting['customer_name'] = User::find($requestConsulting->customer_id)->user_name;

            return response()->json([
                'status' => true,
                'message' => __('api.common.update_success'),
                'data' => $requestConsulting,
            ]);
        } catch (ValidatorException $e) {
            if ($request->wantsJson()) {
                return response()->json([
                    'error' => true,
                    'message' => __('api.common.failed'),
                ]);
            }
        }
    }

    /**
     * Update progress when confirm payment.
     *
     * @param int $id
     *
     * @return \Illuminate\Http\Response
     */
    public function confirmPayment(Request $request, $id)
    {
        try {
            $requestConsulting = $this->repository->findOrFail($id);
            if ($requestConsulting->progress == RequestConsulting::PROGRESS_CONFIRMED_REQUEST) {
                $requestConsulting->update(['progress' => RequestConsulting::PROGRESS_UNDER_AGREEMENT]);
            } else {
                return response()->json([
                    'status' => false,
                    'message' => __('api.common.failed'),
                ]);
            }
            $requestConsulting['service'] = $this->serviceRepository->getServiceDetail($requestConsulting->service_id);
            $requestConsulting['is_owner'] = $requestConsulting->owner_id == auth()->user()->id ? true : false;
            $requestConsulting['owner_name'] = User::find($requestConsulting->owner_id)->user_name;
            $requestConsulting['customer_name'] = User::find($requestConsulting->customer_id)->user_name;

            return response()->json([
                'status' => true,
                'message' => __('api.common.update_success'),
                'data' => $requestConsulting,
            ]);
        } catch (ValidatorException $e) {
            if ($request->wantsJson()) {
                return response()->json([
                    'error' => true,
                    'message' => __('api.common.failed'),
                ]);
            }
        }
    }

    /**
     * Update progress when finishing reviews.
     *
     * @param int $id
     *
     * @return \Illuminate\Http\Response
     */
    public function confirmReview(Request $request, $id)
    {
        try {
            $requestConsulting = $this->repository->findOrFail($id);
            if ($requestConsulting->progress == RequestConsulting::PROGRESS_WAITING_EVALUATION) {
                $requestConsulting->update(['progress' => RequestConsulting::PROGRESS_DONE]);
            } else {
                return response()->json([
                    'status' => false,
                    'message' => __('api.common.failed'),
                ]);
            }
            $requestConsulting['service'] = $this->serviceRepository->getServiceDetail($requestConsulting->service_id);
            $requestConsulting['is_owner'] = $requestConsulting->owner_id == auth()->user()->id ? true : false;
            $requestConsulting['owner_name'] = User::find($requestConsulting->owner_id)->user_name;
            $requestConsulting['customer_name'] = User::find($requestConsulting->customer_id)->user_name;

            return response()->json([
                'status' => true,
                'message' => __('api.common.update_success'),
                'data' => $requestConsulting,
            ]);
        } catch (ValidatorException $e) {
            if ($request->wantsJson()) {
                return response()->json([
                    'error' => true,
                    'message' => __('api.common.failed'),
                ]);
            }
        }
    }

    /**
     * Cancel request consulting.
     *
     * @param int $id
     *
     * @return \Illuminate\Http\Response
     */
    public function cancelRequestConsulting(Request $request, $id)
    {
        try {
            $requestConsulting = $this->repository->findOrFail($id);
            if (auth()->user()->id == $requestConsulting->customer_id) {
                $requestConsulting->update(['state' => RequestConsulting::STATE_CANCEL]);
                $requestConsulting['service'] = $this->serviceRepository->getServiceDetail($requestConsulting->service_id);
                $requestConsulting['is_owner'] = $requestConsulting->owner_id == auth()->user()->id ? true : false;
                $requestConsulting['owner_name'] = User::find($requestConsulting->owner_id)->user_name;
                $requestConsulting['customer_name'] = User::find($requestConsulting->customer_id)->user_name;

                // push notification
                $this->firebaseService->noticeCancelConsultingService($id);
            } else {
                return response()->json([
                    'error' => true,
                    'message' => __('api.common.failed'),
                ]);
            }

            return response()->json([
                'status' => true,
                'message' => __('api.common.update_success'),
                'data' => $requestConsulting,
            ]);
        } catch (ValidatorException $e) {
            return response()->json([
                'error' => true,
                'message' => __('api.common.failed'),
            ]);
        }
    }
    
    /**
     * ownerCancelRequestConsulting
     *
     * @param OwnerCancelRequestConsultingRequest $request
     *
     * @return void
     */
    public function ownerCancelRequestConsulting(OwnerCancelRequestConsultingRequest $request)
    {
        try {
            DB::beginTransaction();
            $user = auth()->user();
            $progress = $this->repository->find($request->id);
            if ($user->id != $progress->owner_id) {
                return response()->json([
                    'error' => true,
                    'message' => __('api.common.you_not_permission'),
                ]);
            }

            if ($progress->state == StatusConstant::CONSULTING_STATE_INACTIVE) {
                return response()->json([
                    'error' => true,
                    'message' => __('api.request-consulting.canceled_request'),
                ]);
            }

            if ($progress->progress > StatusConstant::PROGRESS_CONFIRMED_REQUEST) {
                return response()->json([
                    'error' => true,
                    'message' => __('api.request-consulting.cannot_cancel_request'),
                ]);
            }

            $this->repository->update([
                'state' => StatusConstant::CONSULTING_STATE_INACTIVE,
                'reason' => $request->reason,
            ], $request->id);

            // push notification
            $this->firebaseService->noticeCancelProgress($progress, $user);

            // send message
            $this->firebaseService->handleSendMessageProgress($progress, $request->reason);

            DB::commit();
            return response()->json(['status' => true, 'data' => null]);
        } catch (\Throwable $th) {
            Log::error('Controllers\Api\RequestConsultingsController - ownerCancelRequestConsulting : '.$th->getMessage());
            DB::rollback();
            return response()->json([
                'error' => true,
                'message' => __('api.exception'),
            ]);
        }
    }
}
