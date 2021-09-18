<?php

namespace App\Repositories;

use App\Constant\StatusConstant;
use App\Entities\User;
use App\Entities\WalletTransaction;
use Prettus\Repository\Eloquent\BaseRepository;
use Prettus\Repository\Criteria\RequestCriteria;
use App\Repositories\WalletTransactionRepository;
use Carbon\Carbon;

/**
 * Class StoreArticleRepositoryEloquent.
 *
 * @package namespace App\Repositories;
 */
class WalletTransactionRepositoryEloquent extends BaseRepository implements WalletTransactionRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return WalletTransaction::class;
    }

    /**
     * Boot up the repository, pushing criteria
     */
    public function boot()
    {
        $this->pushCriteria(app(RequestCriteria::class));
    }

    /**
     * get history payment
     */
    public function historyPayment($wallet, $startDate, $endDate, $type)
    {
        if ($endDate) {
            $endDate = Carbon::parse($endDate)->endOfDay()->toDateTimeString();
        }
        $walletTrans = $this->model->where('wallet_id', $wallet->id)
                        ->leftJoin('wallets', 'wallets.id', '=', 'wallet_transactions.wallet_id')
                        ->when(isset($type), function ($q) use ($type) {
                            switch ($type) {
                                case StatusConstant::TRANSACTION_PAYMENT: $q->where('wallet_transactions.type', StatusConstant::TRANSACTION_PAYMENT);
                                    break;
                                case StatusConstant::TRANSACTION_RECEIVE_PAYMENT: $q->where('wallet_transactions.type', StatusConstant::TRANSACTION_RECEIVE_PAYMENT);
                                    break;
                                default:
                                    break;
                            }
                        })
                        ->when(!$type, function ($q) {
                            $q->where(function($query){
                                $query->where('wallet_transactions.type', StatusConstant::TRANSACTION_PAYMENT)
                                ->orWhere('wallet_transactions.type', StatusConstant::TRANSACTION_RECEIVE_PAYMENT);
                            });
                        })
                        ->select(
                            'wallet_transactions.*',
                            'wallets.user_id',
                            'wallets.amount AS ammount_wallet'
                        );

        $history = User::joinSub($walletTrans, 'wallet_trans_temp', function ($join) {
                $join->on('wallet_trans_temp.user_id', '=', 'users.id');
            })
            ->leftJoin('bills', function($join) {
                $join->on('wallet_trans_temp.id', '=', 'bills.customer_trans_id')
                    ->orOn('wallet_trans_temp.id', '=', 'bills.owner_trans_id')
                    ->orOn('wallet_trans_temp.id', '=', 'bills.customer_trans_expire_id');
            })
            ->leftJoin('users as user_customer', 'user_customer.id', '=', 'bills.customer_id')
            ->leftJoin('users as user_owner', 'user_owner.id', '=', 'bills.owner_id')
            ->leftJoin('services', 'services.id', '=', 'bills.service_id')
            ->when($startDate, function ($query, $startDate) {
                $query->where('wallet_trans_temp.created_at', '>=', $startDate);
            })
            ->when($endDate, function ($query, $endDate) {
                $query->where('wallet_trans_temp.created_at', '<=', $endDate);
            })
            ->select(
                'wallet_trans_temp.*',
                'users.email',
                'users.user_name',
                'users.first_name',
                'users.last_name',
                'services.id as service_id',
                'services.service_title',
                'bills.consulting_id',
                'bills.price',
                'bills.amount AS amount_payment',
                'bills.point AS point_payment',
                'bills.point_owner_received',
                'bills.customer_id',
                'bills.owner_id',
                'user_customer.user_name AS customer_user_name',
                'user_customer.first_name AS customer_first_name',
                'user_customer.last_name AS customer_last_name',
                'user_owner.user_name AS owner_user_name',
                'user_owner.first_name AS owner_first_name',
                'user_owner.last_name AS owner_last_name',
            )
            ->orderByDesc('wallet_trans_temp.created_at');

        return $history;
    }

    /**
     * create wallet transaction
     */
    public function createWalletTrans($walletId, $amount, $performedType, $performedById, $description = null, $type)
    {
        $id = $this->model->create([
            'wallet_id' => $walletId,
            'amount' => $amount,
            'performed_type' => $performedType,
            'performed_by_id' => $performedById,
            'description' => $description,
            'type' => $type,
        ])->id;

        return $id;
    }
}
