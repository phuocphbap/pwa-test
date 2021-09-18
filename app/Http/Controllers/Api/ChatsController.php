<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Repositories\ChatRepository;

/**
 * Class ChatsController.
 *
 * @package namespace App\Http\Controllers;
 */
class ChatsController extends Controller
{
    /**
     * @var ChatRepository
     */
    protected $repository;

    /**
     * ChatsController constructor.
     *
     * @param ChatRepository $repository
     */
    public function __construct(ChatRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * get list chat of user
     */
    public function getListChat(Request $request)
    {
        $user = auth()->user();
        $chats = $this->repository->getListChat($user)->get();

        return response()->json([
            'data' => $chats,
        ]);

    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $this->repository->pushCriteria(app('Prettus\Repository\Criteria\RequestCriteria'));
        $chats = $this->repository->all();

        return response()->json([
            'data' => $chats,
        ]);
    }
}
