<?php

namespace App\Repositories;

use App\Constant\StatusConstant;
use App\Entities\Bonus;
use Carbon\Carbon;
use Prettus\Repository\Criteria\RequestCriteria;
use Prettus\Repository\Eloquent\BaseRepository;

/**
 * Class BonusRepositoryEloquent.
 */
class BonusRepositoryEloquent extends BaseRepository implements BonusRepository
{
    /**
     * Specify Model class name.
     *
     * @return string
     */
    public function model()
    {
        return Bonus::class;
    }

    /**
     * Boot up the repository, pushing criteria.
     */
    public function boot()
    {
        $this->pushCriteria(app(RequestCriteria::class));
    }

    public function listHistoryBonuses($filter)
    {
        return $this->model->when($filter->has('start_date') && $filter->start_date != null, function ($q) use ($filter) {
            $q->where('created_at', '>=', Carbon::parse($filter->start_date)->startOfDay());
        })
        ->when($filter->has('end_date') && $filter->end_date != null, function ($q) use ($filter) {
            $q->where('created_at', '<=', Carbon::parse($filter->end_date)->endOfDay());
        })
        ->where('user_id', auth()->user()->id)
        ->orderBy('created_at', 'desc');
    }

    /**
     * function create bonus.
     */
    public function createBonus($userId, $referralBonusId = null, $amount, $idWalletTrans, $idWalletExpire, $type, $userInputRefferal = null, $reason = null)
    {
        $id = $this->model->create([
            'user_id' => $userId,
            'referral_bonus_id' => $referralBonusId,
            'amount' => $amount,
            'trans_wallet_id' => $idWalletTrans,
            'trans_wallet_expire_id' => $idWalletExpire,
            'type' => $type,
            'user_input_refferal' => $userInputRefferal,
            'reason_bonus' => $reason,
        ])->id;

        return $id;
    }

    public function listBonusesInAdmin($type)
    {
        switch ($type) {
            case StatusConstant::BONUS_TYPE_ADMIN:
                return $this->model->where('type', StatusConstant::BONUS_TYPE_ADMIN)->with('user')->get();
                break;
            case StatusConstant::BONUS_TYPE_REFFERAL:
                return $this->model->where('type', StatusConstant::BONUS_TYPE_REFFERAL)
                                    ->orWhere('type', StatusConstant::BONUS_TYPE_INPUT_REFFERAL)->with(['user', 'userIndicateCode'])->get();
                break;
            default:
            return null;
                break;
        }
    }
}
