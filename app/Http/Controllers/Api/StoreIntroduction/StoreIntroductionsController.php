<?php

namespace App\Http\Controllers\Api\StoreIntroduction;

use App\Helpers\General\CollectionHelper;
use App\Http\Controllers\Api\Controller;
use App\Http\Requests\CreateStoreIntroductionRequest;
use App\Http\Requests\GetDetailStoreIntroduction;
use App\Http\Requests\ShowStoreIntroductionRequest;
use App\Http\Requests\UpdateStoreIntroductionRequest;
use App\Repositories\StoreIntroductionRepository;
use App\Services\StoreIntroductionService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * Class StoreIntroductionsController.
 */
class StoreIntroductionsController extends Controller
{
    /**
     * @var StoreIntroductionRepository
     */
    protected $repository;

    /**
     * @var StoreIntroductionService
     */
    protected $storeIntroService;

    /**
     * StoreIntroductionsController constructor.
     */
    public function __construct(StoreIntroductionRepository $repository, StoreIntroductionService $storeIntroService)
    {
        $this->repository = $repository;
        $this->storeIntroService = $storeIntroService;
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param StoreIntroductionCreateRequest $request
     *
     * @return \Illuminate\Http\Response
     *
     * @throws \Prettus\Validator\Exceptions\ValidatorException
     */
    public function store(CreateStoreIntroductionRequest $request)
    {
        try {
            DB::beginTransaction();
            $user = auth()->user();
            if ($request->hasfile('file_name')) {
                $imagePath = $this->storeIntroService->uploadFile($request->file_name, config('setting.folder.store_introduction'));
            }
            $request->merge([
                'store_id' => $user->store->id,
                'image_path' => $imagePath,
            ]);
            $id = $this->repository->create($request->all())->id;
            DB::commit();

            return response()->json([
                'message' => __('api.store_intro.create_success'),
                'id' => $id,
            ]);
        } catch (\Throwable $th) {
            Log::error('Controllers\Api\StoreIntroduction\StoreIntroductionsController - store : '.$th->getMessage());
            DB::rollback();

            return response()->json([
                'error' => true,
                'message' => __('api.exception'),
            ]);
        }
    }

    /**
     * Display the specified resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function show(ShowStoreIntroductionRequest $request)
    {
        try {
            $pagination = $request->has('per_page') && is_numeric($request->per_page) ? $request->per_page : 10;
            $data = $this->repository->findWhere(['store_id' => $request->storeId])->shuffle();
            $data = CollectionHelper::paginate($data, $pagination);

            return response()->json([
                'success' => true,
                'data' => $data,
            ]);
        } catch (\Throwable $th) {
            Log::error('Controllers\Api\StoreIntroduction\StoreIntroductionsController - show : '.$th->getMessage());

            return response()->json([
                'error' => true,
                'message' => __('api.exception'),
            ]);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @return Response
     */
    public function update(UpdateStoreIntroductionRequest $request)
    {
        try {
            DB::beginTransaction();
            if ($request->hasfile('file_name')) {
                // remove old image
                $storeIntro = $this->repository->find($request->id);
                $this->storeIntroService->removeFile($storeIntro->image_path);
                // upload file new
                $imagePath = $this->storeIntroService->uploadFile($request->file_name, config('setting.folder.store_introduction'));
                $request->merge([
                    'image_path' => $imagePath,
                ]);
            }
            $id = $this->repository->update($request->all(), $request->id)->id;
            DB::commit();

            return response()->json([
                'message' => __('api.store_intro.update_success'),
                'id' => $id,
            ]);
        } catch (\Throwable $th) {
            Log::error('Controllers\Api\StoreIntroduction\StoreIntroductionsController - update : '.$th->getMessage());
            DB::rollback();

            return response()->json([
                'error' => true,
                'message' => __('api.exception'),
            ]);
        }
    }

    /**
     * get detail of store introduction.
     */
    public function getDetail(GetDetailStoreIntroduction $request)
    {
        try {
            $data = $this->repository->find($request->id);

            return response()->json([
                'success' => true,
                'data' => $data,
            ]);
        } catch (\Throwable $th) {
            Log::error('Controllers\Api\StoreIntroduction\StoreIntroductionsController - getDetail : '.$th->getMessage());

            return response()->json([
                'error' => true,
                'message' => __('api.exception'),
            ]);
        }
    }
    public function delete($id)
    {
        try {
            $storeIntro = $this->repository->find($id);
            if (!$storeIntro) {
                return response()->json(['error' => true, 'message' => __('api.common.id_not_exists')]);
            }
            $this->repository->delete($id);

            return response()->json([
                'success' => true,
                'data' => null,
            ]);
        } catch (\Throwable $th) {
            Log::error('Controllers\Admin\StoreIntroductions\StoreIntroductionsController - answerStoreIntroductions : '.$th->getMessage());
            return response()->json([
                'error' => true,
                'message' => __('api.exception'),
            ]);
        }
    }
}
