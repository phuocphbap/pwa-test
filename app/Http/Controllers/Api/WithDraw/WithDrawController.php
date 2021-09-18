<?php

namespace App\Http\Controllers\Api\WithDraw;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use App\Repositories\WithdrawRequestRepository;
use App\Http\Requests\GetHistoryWithDrawRequest;

class WithDrawController extends Controller
{
    /**
     * @var WithdrawRequestRepository
     */
    protected $repository;

    /**
     * WithDrawController constructor.
     */
    public function __construct(WithdrawRequestRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * get history withdraw
     */
    public function getHistoryWithDraw(GetHistoryWithDrawRequest $request)
    {
        try {
            $user = auth()->user();
            $startDate = $request->start_date ?? null;
            $endDate = $request->end_date ?? null;
            $pagination = $request->has('per_page') && is_numeric($request->per_page) ? $request->per_page : 10;
            $data = $this->repository->historyWithDraw($user->id, $startDate, $endDate)->paginate($pagination);

            return response()->json([
                'success' => true,
                'data' => $data,
            ]);
        } catch (\Throwable $th) {
            Log::error('Controllers\Api\WithDraw\WithDrawController - getHistoryWithDraw : '. $th->getMessage());
            return response()->json([
                'error' => true,
                'message' => __('api.exception'),
            ]);
        }
    }
}
