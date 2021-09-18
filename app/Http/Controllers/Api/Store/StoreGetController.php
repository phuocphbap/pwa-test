<?php

namespace App\Http\Controllers\Api\Store;

use Illuminate\Http\Request;
use App\Services\StoreService;
use App\Http\Controllers\Controller;
use App\Repositories\StoreRepository;
use App\Http\Requests\GetPlaceSearchRequest;

class StoreGetController extends Controller
{
    /**
     * @var StoreRepository
     */
    protected $repository;

    /**
     * @var StoreService
     */
    protected $storeService;

    /**
     * StoreGetController constructor.
     */
    public function __construct(StoreRepository $repository, StoreService $storeService)
    {
        $this->repository = $repository;
        $this->storeService = $storeService;
    }

    /**
     * get place search by google cloud
     */
    public function getPlaceSearch(GetPlaceSearchRequest $request)
    {
        try {
            $data = $this->storeService->getPlaceSearch($request->text);
            return response()->json([
                'success' => true,
                'data' => $data,
            ]);
        } catch (\Throwable $th) {
            \Log::ERROR('Controllers\Api\Store\StoreGetController - getPlaceSearch : '.$th->getMessage());
            return response()->json([
                'error' => true,
                'message' => __('api.exception'),
            ]);
        }
    }
}
