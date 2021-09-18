<?php

namespace App\Http\Controllers\Admin\Services;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use App\Services\Admin\ServicesService;
use App\Repositories\CategoryRepository;
use App\Helpers\General\CollectionHelper;
use App\Http\Requests\Admin\CreateCategoryRequest;
use App\Http\Requests\Admin\UpdateCategoryRequest;

class CategoryService extends Controller
{
    /**
     * @var CategoryRepository
     */
    protected $repository;
    /**
     * @var ServiceRepository
     */
    protected $serService;

    /**
     * BonusesController constructor.
     */
    public function __construct(CategoryRepository $repository, ServicesService $serService)
    {
        $this->repository = $repository;
        $this->serService = $serService;
    }

    /**
     * create category
     */
    public function createCategory(CreateCategoryRequest $request)
    {
        try {
            DB::beginTransaction();
            $data = $this->repository->create($request->all());
            DB::commit();

            return response()->json([
                'success' => true,
                'data' => $data
            ]);
        } catch (\Throwable $th) {
            Log::error('Controllers\Admin\Services\CategoryService - createCategory : '.$th->getMessage());
            DB::rollback();
            return response()->json([
                'error' => true,
                'message' => __('api.exception'),
            ]);
        }
    }

    /**
     * get list category
     */
    public function getListCategory(Request $request)
    {
        try {
            $pagination = $request->has('per_page') && is_numeric($request->per_page) ? $request->per_page : 10;
            $data = getAllCategoriesByLevels();
            $data = CollectionHelper::paginate($data, $pagination);

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
     * update category
     */
    public function updateCategory(UpdateCategoryRequest $request)
    {
        try {
            DB::beginTransaction();
            $data = $this->repository->update($request->all(), $request->id);
            DB::commit();

            return response()->json([
                'success' => true,
                'data' => $data
            ]);
        } catch (\Throwable $th) {
            Log::error('Controllers\Admin\Services\CategoryService - updateCategory : '.$th->getMessage());
            DB::rollback();
            return response()->json([
                'error' => true,
                'message' => __('api.exception'),
            ]);
        }
    }

    /**
     * update category
     */
    public function deleteCategory($id)
    {
        try {
            DB::beginTransaction();
            $data = $this->repository->checkHasChildCategory($id);
            if ($data->children->isNotEmpty()) {
                return response()->json(['error' => true, 'message' => __('api.category.has_child_data')]);
            };
            $checkHasService = $this->serService->checkExistsCategory($id);
            if ($checkHasService) {
                return response()->json(['error' => true, 'message' => __('api.category.has_services')]);
            };
            $this->repository->delete($id);
            DB::commit();

            return response()->json([
                'success' => true,
                'data' => null
            ]);
        } catch (\Throwable $th) {
            Log::error('Controllers\Admin\Services\CategoryService - updateCategory : '.$th->getMessage());
            DB::rollback();
            return response()->json([
                'error' => true,
                'message' => __('api.exception'),
            ]);
        }
    }
}
