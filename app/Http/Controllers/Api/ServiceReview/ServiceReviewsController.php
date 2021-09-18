<?php

namespace App\Http\Controllers\Api\ServiceReview;

use App\Entities\ServiceReview;
use App\Entities\User;
use App\Http\Controllers\Api\ApiController;
use App\Http\Requests\ServiceReviewCancelRequest;
use App\Http\Requests\ServiceReviewCreateRequest;
use App\Repositories\RequestConsultingRepository;
use App\Repositories\ServiceReviewRepository;
use Illuminate\Http\Request;

/**
 * Class ServiceReviewsController.
 */
class ServiceReviewsController extends ApiController
{
    /**
     * @var ServiceReviewRepository
     */
    protected $repository;
    protected $requestConsultingRepository;

    /**
     * ServiceReviewsController constructor.
     */
    public function __construct(ServiceReviewRepository $repository, RequestConsultingRepository $requestConsultingRepository)
    {
        $this->repository = $repository;
        $this->requestConsultingRepository = $requestConsultingRepository;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $this->repository->pushCriteria(app('Prettus\Repository\Criteria\RequestCriteria'));
        if (!$request->service_id) {
            return response()->json(['error' => true, 'message' => __('api.service.not_found')]);
        }
        $pagination = $request->has('per_page') && is_numeric($request->per_page) ? $request->per_page : 10;

        $reviews = $this->repository->with('user')->where('service_id', $request->service_id)->where('is_owner', ServiceReview::NOT_OWNER_SERVICE)->orderBy('created_at')->paginate($pagination);

        return response()->json([
            'success' => true,
            'data' => $reviews,
        ]);
    }

    /**
     * create review service for both customer and owner.
     */
    public function createServiceReview(ServiceReviewCreateRequest $request)
    {
        $dataRes = $this->repository->createServiceReview($request->consulting_id, $request->value, $request->message);
        if (!$dataRes) {
            return response()->json([
                'status' => false,
                'message' => __('api.review.review_failed'),
            ]);
        }

        return response()->json([
            'status' => true,
            'data' => $dataRes,
            'message' => __('api.review.review_success'),
        ]);
    }

    /**
     * cancel review service for both customer and owner.
     */
    public function cancelServiceReview(ServiceReviewCancelRequest $request)
    {
        $requestConsulting = $this->requestConsultingRepository->findOrFail($request->consulting_id);

        $dataRes = $this->repository->cancelServiceReview($request->consulting_id);
        if (!$dataRes) {
            return response()->json([
                'status' => false,
                'message' => __('api.review.cancel_failed'),
            ]);
        }
        $dataRes['is_owner'] = $requestConsulting->owner_id == auth()->user()->id ? true : false;
        $dataRes['owner_name'] = User::find($requestConsulting->owner_id)->user_name;
        $dataRes['customer_name'] = User::find($requestConsulting->customer_id)->user_name;

        return response()->json([
            'status' => true,
            'data' => $dataRes,
            'message' => __('api.review.cancel_success'),
        ]);
    }
}
