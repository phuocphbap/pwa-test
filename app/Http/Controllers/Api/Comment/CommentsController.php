<?php

namespace App\Http\Controllers\Api\Comment;

use Illuminate\Http\Request;
use App\Services\CommentService;
use App\Repositories\CommentRepository;
use App\Http\Controllers\Api\ApiController;
use App\Http\Requests\CommentCreateRequest;
use App\Services\FirebaseService;
use Prettus\Validator\Exceptions\ValidatorException;

/**
 * Class CommentsController.
 */
class CommentsController extends ApiController
{
    /**
     * @var CommentRepository
     */
    protected $repository;

    /**
     * @var CommentService
     */
    protected $commentService;
    /**
     * @var FirebaseService
     */
    protected $firebaseService;

    /**
     * CommentsController constructor.
     */
    public function __construct(
        CommentRepository $repository,
        CommentService $commentService,
        FirebaseService $firebaseService
    )
    {
        $this->repository = $repository;
        $this->commentService = $commentService;
        $this->firebaseService = $firebaseService;
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

        $comments = $this->repository->where('service_id', $request->service_id)->with('user')->orderBy('created_at', 'desc')->paginate($pagination);

        return response()->json([
            'success' => true,
            'data' => $comments,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return \Illuminate\Http\Response
     *
     * @throws \Prettus\Validator\Exceptions\ValidatorException
     */
    public function store(CommentCreateRequest $request)
    {
        try {
            $data = $request->only('service_id', 'message');
            $data['user_id'] = auth()->user()->id;
            $comment = $this->repository->create($data);
            $comment['user'] = auth()->user();

            // push notification
            $owner = $this->commentService->getOwnerServiceByComment($request->service_id);
            if (auth()->user()->id != $owner['userId']) {
                $this->firebaseService->noticeCommentInService($owner['userId'], $request->service_id, $owner['nameService']);
            }

            return response()->json([
                'success' => true,
                'data' => $comment,
            ]);
        } catch (ValidatorException $e) {
            return response()->json([
                'error' => true,
                'message' => __('api.service.error_comment'),
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
        $deleted = $this->repository->delete($id);

        return response()->json([
            'success' => true,
            'deleted' => $deleted,
        ]);
    }
}
