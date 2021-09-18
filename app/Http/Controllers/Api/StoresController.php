<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Services\StoreService;
use App\Entities\ServiceReview;
use App\Constant\StatusConstant;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Repositories\StoreRepository;
use App\Http\Requests\LikeStoreRequest;
use App\Http\Requests\GetSpotStoreRequest;
use App\Http\Requests\UpdateInfoStoreRequest;
use App\Repositories\ServiceReviewRepository;
use App\Http\Requests\GetMapDetailStoreRequest;

/**
 * Class StoresController.
 */
class StoresController extends Controller
{
    /**
     * @var StoreRepository
     */
    protected $repository;

    /**
     * @var ServiceReviewRepository
     */
    protected $reviewRepository;

    /**
     * @var StoreService
     */
    protected $storeService;

    /**
     * StoresController constructor.
     */
    public function __construct(StoreRepository $repository, ServiceReviewRepository $reviewRepository, StoreService $storeService)
    {
        $this->repository = $repository;
        $this->reviewRepository = $reviewRepository;
        $this->storeService = $storeService;
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
            'region_id' => $request->region_id,
        ]);
        $search = $request->search ?? null;
        $sort = $request->sort ?? null;

        $pagination = $request->has('per_page') && is_numeric($request->per_page) ? $request->per_page : 10;
        $stores = $this->repository->getListStoreEveryOne($filter)
                ->addSelect(\DB::raw('view_020_stores.id as id, CASE WHEN store_likes.id IS NOT NULL THEN 1 ELSE 0 END AS liked'))
                ->withCount('likes', 'unsatisfyEmotion', 'mediumEmotion', 'satisfyEmotion')
                ->with('categories')
                ->leftJoin('store_likes', function ($q) {
                    $q->on('store_likes.store_id', '=', 'view_020_stores.id')
                    ->where('store_likes.user_id', '=', auth('api')->user() != null ? auth('api')->user()->id : null);
                })
                ->when($search, function ($query, $search) {
                    $query->where('view_020_stores.user_name', 'LIKE', "%{$search}%");
                })
                ->when($sort, function ($q, $sort) {
                    switch ($sort) {
                        case 'LIKES':
                            $q->orderBy('likes_count', 'desc');
                            break;
                        case 'NEW':
                            $q->orderBy('view_020_stores.id', 'desc');
                            break;
                        default:
                            break;
                        }
                })
                ->when(!$sort, function ($q) {
                    $q->inRandomOrder();
                })
                ->selectRaw('view_020_stores.*')
                ->paginate($pagination)
                ->appends(request()->query());

        return response()->json([
            'success' => true,
            'data' => $stores,
        ]);
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
        $store = $this->repository->getDetail($id)
        ->select()
        ->addSelect(\DB::raw('view_020_stores.id as id, CASE WHEN store_likes.id IS NOT NULL THEN 1 ELSE 0 END AS liked'))
        ->with('categories')
        ->withCount('likes', 'unsatisfyEmotion', 'mediumEmotion', 'satisfyEmotion')
        ->leftJoin('store_likes', function ($q) {
            $q->on('store_likes.store_id', '=', 'view_020_stores.id')
            ->where('store_likes.user_id', '=', auth('api')->user() != null ? auth('api')->user()->id : null);
        })
        ->first();

        return response()->json([
            'success' => true,
            'data' => $store,
        ]);
    }

    public function listServiceOfStore(Request $request)
    {
        if (!$request->store_id) {
            return response()->json(['error' => true, 'message' => __('api.store.not_found')]);
        }
        $pagination = $request->has('per_page') && is_numeric($request->per_page) ? $request->per_page : 10;
        $checkIsOwnerService = true;
        $userLogin = auth('api')->user() != null ? auth('api')->user()->id : null;
        
        $userStore = $this->repository->getDetail($request->store_id)->first();
        if ($userLogin == $userStore->user_id) {
            $checkIsOwnerService = false;
        }
        $services = $this->repository->listServiceOfStore($request->store_id)
                ->when($checkIsOwnerService, function ($query) {
                    $query->where('view_010_services.is_blocked', StatusConstant::SERVICE_NOT_IS_BLOCKED);
                })
                ->whereNull('view_010_services.deleted_at')
                ->addSelect(\DB::raw('view_010_services.*, view_010_services.id as id, CASE WHEN service_likes.id IS NOT NULL THEN 1 ELSE 0 END AS liked'))
                ->withCount('likes', 'agreements')
                ->leftJoin('service_likes', function ($q) {
                    $q->on('service_likes.service_id', '=', 'view_010_services.id')
                    ->where('service_likes.user_id', '=', auth('api')->user() != null ? auth('api')->user()->id : null);
                })
                ->orderBy('view_010_services.id')
                ->paginate($pagination)
                ->appends(request()->query());

        return response()->json([
            'success' => true,
            'data' => $services,
        ]);
    }

    /**
     * get detail map store.
     */
    public function getMapDetailStore(GetMapDetailStoreRequest $request)
    {
        try {
            $store = $this->repository->getDetail($request->store_id)->first();
            if (!$store) {
                return response()->json([
                    'error' => true,
                    'data' => __('api.store.store_blocked'),
                ]);
            }

            $data = $this->storeService->handleGetImagesMap($store);

            return response()->json([
                'success' => true,
                'data' => $data,
            ]);
        } catch (\Throwable $th) {
            \Log::ERROR('Controllers\Api\StoresController - getMapDetailStore : '.$th->getMessage());

            return response()->json([
                'error' => true,
                'message' => __('api.exception'),
            ]);
        }
    }

    /*
     * like service
     */
    public function likeStore(LikeStoreRequest $request)
    {
        $ownerId = $this->repository->findOrFail($request->store_id)->user_id;
        if ($ownerId == auth()->user()->id) {
            return response()->json(['error' => true, 'message' => __('api.store.unauthorized_like')]);
        }
        $status = $this->repository->likeStore($request->store_id);
        switch ($status) {
            case 1:
                return response()->json(['status' => $status]);
                break;
            case 0:
                return response()->json(['status' => $status]);
                break;
            default:
                return response()->json(['error' => true, 'message' => __('api.store.error_like')]);
                break;
        }
    }

    /*
     * review of customer
     */
    public function customerReviews(Request $request)
    {
        if (!$request->store_id) {
            return response()->json(['error' => true, 'message' => __('api.store.not_found')]);
        }
        $pagination = $request->has('per_page') && is_numeric($request->per_page) ? $request->per_page : 10;

        $reviews = $this->reviewRepository->with('user')->where('store_id', $request->store_id)->where('is_owner', ServiceReview::NOT_OWNER_SERVICE)->orderBy('created_at')->paginate($pagination);

        return response()->json([
            'success' => true,
            'data' => $reviews,
        ]);
    }

    /*
     * review of owner
     */
    public function ownerReviews(Request $request)
    {
        if (!$request->store_id) {
            return response()->json(['error' => true, 'message' => __('api.store.not_found')]);
        }
        $pagination = $request->has('per_page') && is_numeric($request->per_page) ? $request->per_page : 10;

        $reviews = $this->reviewRepository->with('user')->where('store_id', $request->store_id)->where('is_owner', ServiceReview::IS_OWNER_SERVICE)->orderBy('created_at')->paginate($pagination);

        return response()->json([
            'success' => true,
            'data' => $reviews,
        ]);
    }

    /**
     * get spot of store.
     */
    public function getSpotStore(GetSpotStoreRequest $request)
    {
        try {
            $user = auth('api')->user() ?? null;
            $search = $request->search ?? null;
            $regionId = $request->regionId ?? null;
            $pagination = $request->has('per_page') && is_numeric($request->per_page) ? $request->per_page : 10;
            $spot = $this->storeService->getSpotStore($request->latitude, $request->longitude, $user, $search, $regionId)->paginate($pagination);

            return response()->json([
                'success' => true,
                'data' => $spot,
            ]);
        } catch (\Throwable $th) {
            \Log::ERROR('Controllers\Api\StoresController - getSpotStore : '.$th->getMessage());

            return response()->json([
                'error' => true,
                'message' => __('api.exception'),
            ]);
        }
    }

    /**
     * get stores already liked.
     */
    public function listStoreLiked(Request $request)
    {
        $pagination = $request->has('per_page') && is_numeric($request->per_page) ? $request->per_page : 10;
        $stores = $this->repository->listStoreAlreadyLiked()->paginate($pagination);

        return response()->json([
            'success' => true,
            'data' => $stores,
        ]);
    }
    
    /**
     * updateInfoStore
     *
     * @param UpdateInfoStoreRequest $request
     *
     * @return void
     */
    public function updateInfoStore(UpdateInfoStoreRequest $request)
    {
        try {
            DB::beginTransaction();
            $user = auth('api')->user();
            $data = $this->storeService->handleUpdateInfoStore($user, $request);
            DB::commit();

            return response()->json([
                'status' => true,
                'data' => $data,
            ]);
        } catch (\Throwable $th) {
            Log::ERROR('Controllers\Api\StoresController - updateInfoStore : '.$th->getMessage());
            DB::rollback();
            return response()->json([
                'error' => true,
                'message' => __('api.exception'),
            ]);
        }
    }
}
