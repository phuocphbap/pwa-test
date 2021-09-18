<?php

namespace App\Http\Controllers\Api\StoreArticle;

use App\Http\Controllers\Controller;
use App\Http\Requests\GetArticlesRequest;
use App\Http\Requests\ShowArticlesRequest;
use App\Http\Requests\StoreArticleCreateRequest;
use App\Http\Requests\UpdateArticlesRequest;
use App\Repositories\StoreArticleRepository;
use App\Services\StoreArticleService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

/**
 * Class StoreArticlesController.
 */
class StoreArticlesController extends Controller
{
    /**
     * @var StoreArticleRepository
     */
    protected $repository;

    /**
     * @var StoreArticleService
     */
    protected $articleService;

    /**
     * StoreArticlesController constructor.
     */
    public function __construct(StoreArticleRepository $repository, StoreArticleService $articleService)
    {
        $this->repository = $repository;
        $this->articleService = $articleService;
    }

    /**
     * store article store.
     */
    public function store(StoreArticleCreateRequest $request)
    {
        try {
            \DB::beginTransaction();
            $user = auth()->user();
            $data = [
                'store_id' => $user->store->id,
                'title' => $request->title,
                'contents' => $request->contents,
            ];
            $id = $this->repository->create($data)->id;
            if ($request->hasfile('file_name')) {
                $this->articleService->uploadMultipleImages($request, $id);
            }
            \DB::commit();

            return response()->json([
                'message' => __('api.store_article.create_success'),
                'data' => ['id' => $id],
            ]);
        } catch (\Throwable $th) {
            \Log::error('Controllers\Api\StoreArticle - store : '.$th->getMessage());
            \DB::rollback();

            return response()->json([
                'error' => true,
                'message' => __('api.exception'),
            ]);
        }
    }

    /**
     * get articles of store by store_id.
     */
    public function getArticles(GetArticlesRequest $request)
    {
        try {
            $pagination = $request->has('per_page') && is_numeric($request->per_page) ? $request->per_page : 10;
            $data = $this->repository->getArticleByStoreId($request->storeId)->paginate($pagination);

            return response()->json([
                'success' => true,
                'data' => $data,
            ]);
        } catch (\Throwable $th) {
            Log::error('Controllers\Api\StoreArticle - show : '.$th->getMessage());

            return response()->json([
                'error' => true,
                'message' => __('api.exception'),
            ]);
        }
    }

    /**
     * show articles by id.
     */
    public function show(ShowArticlesRequest $request)
    {
        try {
            $data = $this->repository->getArticlesById($request->id);

            return response()->json([
                'success' => true,
                'data' => $data,
            ]);
        } catch (\Throwable $th) {
            \Log::error('Controllers\Api\StoreArticle - show : '.$th->getMessage());

            return response()->json([
                'error' => true,
                'message' => __('api.exception'),
            ]);
        }
    }

    /**
     * function update articles.
     */
    public function update(UpdateArticlesRequest $request)
    {
        try {
            \DB::beginTransaction();
            $this->repository->update($request->all(), $request->id);
            // remove images
            if ($request->image_id) {
                $imageIds = explode(',', $request->image_id);
                foreach ($imageIds as $key => $value) {
                    $this->articleService->removeImageArticles($value);
                }
            }
            // store new images
            if ($request->hasfile('file_name')) {
                $this->articleService->uploadMultipleImages($request, $request->id);
            }
            \DB::commit();

            return response()->json([
                'message' => __('api.store_article.update_success'),
                'data' => ['id' => $request->id],
            ]);
        } catch (\Throwable $th) {
            \Log::error('Controllers\Api\StoreArticle - update : '.$th->getMessage());
            \DB::rollback();

            return response()->json([
                'error' => true,
                'message' => __('api.exception'),
            ]);
        }
    }

    public function getImagesArticles(Request $request, $storeId)
    {
        try {
            $pagination = $request->has('per_page') && is_numeric($request->per_page) ? $request->per_page : 10;
            $data = $this->repository->getAllImagesArticles($storeId)->paginate($pagination);

            return response()->json([
                'success' => true,
                'data' => $data,
            ]);
        } catch (\Throwable $th) {
            Log::error('Controllers\Api\StoreArticle - getImagesArticles : '.$th->getMessage());

            return response()->json([
                'error' => true,
                'message' => __('api.exception'),
            ]);
        }
    }
    public function delete($id)
    {
        try {
            $storeArticle = $this->repository->find($id);
            if (!$storeArticle) {
                return response()->json(['error' => true, 'message' => __('api.common.id_not_exists')]);
            }
            $this->repository->delete($id);

            return response()->json([
                'success' => true,
                'data' => null,
            ]);
        } catch (\Throwable $th) {
            Log::error('Controllers\Admin\StoreArticles\StoreArticlesController - answerStoreArticles : '.$th->getMessage());
            return response()->json([
                'error' => true,
                'message' => __('api.exception'),
            ]);
        }
    }
}
