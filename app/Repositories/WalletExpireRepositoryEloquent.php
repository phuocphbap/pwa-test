<?php

namespace App\Repositories;

use App\Entities\WalletExpire;
use App\Repositories\WalletExpireRepository;
use Prettus\Repository\Eloquent\BaseRepository;
use Prettus\Repository\Criteria\RequestCriteria;

/**
 * Class StoreArticleRepositoryEloquent.
 *
 * @package namespace App\Repositories;
 */
class WalletExpireRepositoryEloquent extends BaseRepository implements WalletExpireRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return WalletExpire::class;
    }

    /**
     * Boot up the repository, pushing criteria
     */
    public function boot()
    {
        $this->pushCriteria(app(RequestCriteria::class));
    }

    /**
     * create wallet expire
     */
    public function createWalletExpire($walletId, $amount, $date, $periodExpireId)
    {
        $id = $this->model->create([
            'wallet_id' => $walletId,
            'amount' => $amount,
            'expire_date' => $date,
            'period_id' => $periodExpireId
        ])->id;

        return $id;
    }

    /**
     * update wallet expire
     */
    public function updateDateExpire($walletExpireId, $amount, $date, $periodExpireId)
    {
        $this->model->where('id', $walletExpireId)
                ->update([
                    'amount' => $amount,
                    'expire_date' => $date,
                    'period_id' => $periodExpireId
                ]);

        return true;
    }
}
