<?php

namespace App\Http\Controllers\Api\Bonus;

use App\Http\Controllers\Api\ApiController;
use App\Repositories\BonusRepository;
use Illuminate\Http\Request;

/**
 * Class BonusesController.
 */
class BonusesController extends ApiController
{
    /**
     * @var BonusRepository
     */
    protected $repository;

    /**
     * BonusesController constructor.
     */
    public function __construct(BonusRepository $repository)
    {
        $this->repository = $repository;
    }

    public function getListHistory(Request $request)
    {
        $filter = newCond([
            'start_date' => $request->start_date ?: null,
            'end_date' => $request->end_date ?: null,
        ]);
        $pagination = $request->has('per_page') && is_numeric($request->per_page) ? $request->per_page : 10;
        $bonuses = $this->repository->listHistoryBonuses($filter)
                    ->paginate($pagination);

        $totalAmount = $this->repository->listHistoryBonuses($filter)
                        ->sum('amount');

        return response()->json([
            'success' => true,
            'data' => $bonuses,
            'total_amount' => $totalAmount,
        ]);
    }
}
