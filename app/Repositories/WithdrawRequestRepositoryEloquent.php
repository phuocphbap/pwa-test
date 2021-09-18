<?php

namespace App\Repositories;

use Carbon\Carbon;
use App\Entities\WithdrawRequest;
use Illuminate\Support\Facades\DB;
use Prettus\Repository\Eloquent\BaseRepository;
use Prettus\Repository\Criteria\RequestCriteria;

/**
 * Class WithdrawRequestRepositoryEloquent.
 */
class WithdrawRequestRepositoryEloquent extends BaseRepository implements WithdrawRequestRepository
{
    /**
     * Specify Model class name.
     *
     * @return string
     */
    public function model()
    {
        return WithdrawRequest::class;
    }

    /**
     * Boot up the repository, pushing criteria.
     */
    public function boot()
    {
        $this->pushCriteria(app(RequestCriteria::class));
    }

    /**
     * check exists status withdraw by user id.
     */
    public function checkExistsStatusWithDraw($userId)
    {
        return $this->model->where('user_id', $userId)
                        ->where('state', WithdrawRequest::PENDING_STATE)
                        ->exists();
    }

    public function historyWithDraw($userId, $startDate, $endDate)
    {
        if ($endDate) {
            $endDate = Carbon::parse($endDate)->endOfDay()->toDateTimeString();
        }

        return $this->model->where('user_id', $userId)
                    ->when($startDate, function ($query, $startDate) {
                        $query->where('created_at', '>=', $startDate);
                    })
                    ->when($endDate, function ($query, $endDate) {
                        $query->where('created_at', '<=', $endDate);
                    })
                    ->latest();
    }

    public function listWithdrawInAdmin($filter)
    {
        $latestExprixe = DB::table('wallet_expires')->select(DB::raw('MAX(id) as exprises_id_max'), 'wallet_id')->groupBy('wallet_id');
        $wallet = DB::table('wallets')->leftJoinSub($latestExprixe, 'latest_wallet_expires',  function ($join) {
                    $join->on('wallets.id', '=', 'latest_wallet_expires.wallet_id');
                })
                ->leftJoin('wallet_expires', function ($join) {
                    $join->on('wallet_expires.id', '=', 'latest_wallet_expires.exprises_id_max')
                    ->where('wallet_expires.expire_date', '>=', Carbon::now());
                })
                ->select('wallets.user_id',
                    'wallets.amount AS amount_wallet'
                )
                ->selectRaw('CASE
                                WHEN wallet_expires.amount IS NULL THEN 0
                                ELSE wallet_expires.amount END
                                AS amount_wallet_expries'
                            );

        return $this->model->leftJoinSub($wallet, 'wallet_latest', 'wallet_latest.user_id', '=', 'withdraw_requests.user_id')
                        ->when($filter->has('start_date') && $filter->start_date != null, function ($q) use ($filter) {
                            $q->where('withdraw_requests.created_at', '>=', Carbon::parse($filter->start_date)->startOfDay());
                        })
                        ->when($filter->has('end_date') && $filter->end_date != null, function ($q) use ($filter) {
                            $q->where('withdraw_requests.created_at', '<=', Carbon::parse($filter->end_date)->endOfDay());
                        })
                        ->when($filter->has('state') && $filter->state != null, function ($q) use ($filter) {
                            $q->where('withdraw_requests.state', $filter->state);
                        })
                        ->select('withdraw_requests.*',
                                'wallet_latest.*'
                        )
                        ->with(['user' => function ($query) {
                            $query->with('region', 'bankAccount.category');
                        }])
                        ->orderBy('withdraw_requests.created_at', 'desc');
    }
}
