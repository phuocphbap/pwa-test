<?php

namespace App\Http\Controllers\Api\StoreImage;

use App\Helpers\General\CollectionHelper;
use App\Http\Controllers\Api\Controller;
use App\Http\Requests\StoreImageCreateRequest;
use App\Http\Requests\StoreImageUpdateRequest;
use App\Repositories\StoreImageRepository;
use App\Services\StoreIntroductionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * Class StoreImagesController.
 */
class StoreImagesController extends Controller
{
    /**
     * @var StoreImageRepository
     */
    protected $repository;

    /**
     * @var StoreIntroductionService
     */
    protected $storeIntroService;

    /**
     * StoreImagesController constructor.
     */
    public function __construct(StoreImageRepository $repository, StoreIntroductionService $storeIntroService)
    {
        $this->repository = $repository;
        $this->storeIntroService = $storeIntroService;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request, $storeId)
    {
        try {
            $pagination = $request->has('per_page') && is_numeric($request->per_page) ? $request->per_page : 10;
            $data = $this->repository->findWhere(['store_id' => $storeId])->shuffle();
            $data = CollectionHelper::paginate($data, $pagination);

            return response()->json([
                'success' => true,
                'data' => $data,
            ]);
        } catch (\Throwable $th) {
            Log::error('Controllers\Api\StoreImage\StoreImagesController - index : '.$th->getMessage());

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
     */
    public function store(StoreImageCreateRequest $request)
    {
        try {
            DB::beginTransaction();
            $user = auth()->user();
            if ($request->hasfile('file_name')) {
                $imagePath = $this->storeIntroService->uploadFile($request->file_name, config('setting.folder.store_image'));
                $request->merge([
                    'image_path' => $imagePath,
                    'store_id' => $user->store->id,
                ]);
            }
            $id = $this->repository->create($request->all())->id;
            DB::commit();

            return response()->json([
                'message' => __('api.store_image.create_success'),
                'id' => $id,
            ]);
        } catch (\Throwable $th) {
            Log::error('Controllers\Api\StoreImage\StoreImagesController - store : '.$th->getMessage());
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
     * @param int $id
     *
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        try {
            $data = $this->repository->find($id);

            return response()->json([
                'success' => true,
                'data' => $data,
            ]);
        } catch (\Throwable $th) {
            Log::error('Controllers\Api\StoreImage\StoreImagesController - show : '.$th->getMessage());

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
    public function update(StoreImageUpdateRequest $request)
    {
        try {
            DB::beginTransaction();
            if ($request->hasfile('file_name')) {
                // remove old image
                $image = $this->repository->find($request->id);
                $this->storeIntroService->removeFile($image->image_path);
                // upload file new
                $imagePath = $this->storeIntroService->uploadFile($request->file_name, config('setting.folder.store_image'));
                $request->merge([
                    'image_path' => $imagePath,
                ]);
            }
            $id = $this->repository->update($request->all(), $request->id)->id;
            DB::commit();

            return response()->json([
                'message' => __('api.store_image.update_success'),
                'id' => $id,
            ]);
        } catch (\Throwable $th) {
            Log::error('Controllers\Api\StoreImage\StoreImagesController - update : '.$th->getMessage());
            DB::rollback();

            return response()->json([
                'error' => true,
                'message' => __('api.exception'),
            ]);
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
            // remove image
            $image = $this->repository->find($id);
            $this->storeIntroService->removeFile($image->image_path);
            $this->repository->delete($id);

            return response()->json([
                'success' => true,
                'message' => __('api.store_image.delete_success'),
            ]);
        } catch (\Throwable $th) {
            Log::error('Controllers\Api\StoreImage\StoreImagesController - destroy : '.$th->getMessage());

            return response()->json([
                'error' => true,
                'message' => __('api.exception'),
            ]);
        }
    }
}
