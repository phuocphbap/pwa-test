<?php

namespace App\Http\Controllers\Api\StorePost;

use App\Entities\StorePost;
use App\Http\Controllers\Api\ApiController;
use App\Repositories\StorePostRepository;
use Illuminate\Http\Request;

/**
 * Class StorePostsController.
 */
class StorePostsController extends ApiController
{
    /**
     * @var StorePostRepository
     */
    protected $repository;

    /**
     * StorePostsController constructor.
     */
    public function __construct(StorePostRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $this->repository->pushCriteria(app('Prettus\Repository\Criteria\RequestCriteria'));
        if (!$request->store_id) {
            return response()->json(['error' => true, 'message' => __('api.store.not_found')]);
        }
        $pagination = $request->has('per_page') && is_numeric($request->per_page) ? $request->per_page : 10;
        $posts = [];
        switch ($request->type) {
            case 'news':
                $posts = $this->repository->where('store_id', $request->store_id)->where('type', StorePost::NEW_POST)->orderBy('order', 'desc')->paginate($pagination);
                break;
            case 'images':
                $posts = $this->repository->where('store_id', $request->store_id)->where('type', StorePost::IMAGE_POST)->orderBy('order', 'desc')->paginate($pagination);
                break;
            default:
                $posts = $this->repository->where('store_id', $request->store_id)->where('type', StorePost::NEW_POST)->orderBy('order', 'desc')->paginate($pagination);
                break;
        }

        return response()->json([
            'success' => true,
            'data' => $posts,
        ]);
    }
}
