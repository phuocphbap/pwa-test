<?php

namespace App\Services;

use App\Entities\Payment;
use App\Entities\WalletTransaction;
use App\Entities\WithdrawRequest;
use App\Repositories\UserRepository;
use App\Repositories\WithdrawRequestRepository;

class WithdrawService
{
    protected $userRepository;
    protected $withdrawRepository;

    public function __construct(UserRepository $userRepository, WithdrawRequestRepository $withdrawRepository)
    {
        $this->userRepository = $userRepository;
        $this->withdrawRepository = $withdrawRepository;
    }

    public function requestWithdrawPoint($withdrawPoint)
    {
        $user = auth()->user();
        $userId = $user->id;
        $wallet = $user->wallet;
        $performTypeWallet = 'App\Entities\Wallet';
        $performTypeWalletExpire = 'App\Entities\WalletExpire';
        $pointExpireValid = $this->userRepository->getPointValidWalletExpire($userId);
        $redundPoint = 0;
        $transExpireId = null;
        $transWalletId = null;
        if ($pointExpireValid > 0) {
            $walletExpireId = $this->userRepository->getWalletExpire($userId)->id;
            $pointUpdate = $withdrawPoint <= $pointExpireValid ? $withdrawPoint : $pointExpireValid;
            //create transactions and deduct point in wallet_expires
            $transExpireId = $this->performTransactionWallet($pointUpdate * -1, $wallet, $performTypeWalletExpire, $walletExpireId);
            $this->updateAmountWalletExpire($userId, $pointUpdate);
            $redundPoint = $withdrawPoint - $pointExpireValid;
            if ($redundPoint > 0) {
                //create transactions wallet then deduct point in wallet.
                $transWalletId = $this->performTransactionWallet($redundPoint * -1, $wallet, $performTypeWallet, $wallet->id);
                $this->updateAmountWallet($userId, $redundPoint);
            }
        } else {
            //create transactions wallet then deduct point in wallet.
            $transWalletId = $this->performTransactionWallet($withdrawPoint * -1, $wallet, $performTypeWallet, $wallet->id);
            $this->updateAmountWallet($userId, $withdrawPoint);
        }
        $resData = $this->createWithdrawRequest($userId, $withdrawPoint, $transWalletId, $transExpireId);

        return $resData;
    }

    public function createWithdrawRequest($userId, $withdrawPoint, $transWalletId, $transExpireId)
    {
        $data = $this->withdrawRepository->create([
            'user_id' => $userId,
            'amount' => $withdrawPoint,
            'trans_wallet_id' => $transWalletId,
            'trans_wallet_expire_id' => $transExpireId,
            'state' => WithdrawRequest::PENDING_STATE,
        ]);

        return $data;
    }

    public function performTransactionWallet($amount, $wallet, $performType, $performById)
    {
        $walletTransaction = $wallet->walletTransactions()->create([
            'currency_type' => Payment::DEFAULT_CURRENCY,
            'amount' => $amount,
            'performed_type' => $performType,
            'performed_by_id' => $performById,
            'type' => WalletTransaction::TRANSACTION_WITHDRAW,
        ]);

        return $walletTransaction->id;
    }

    public function updateAmountWalletExpire($userId, $deductPoint)
    {
        $walletExpire = $this->userRepository->getWalletExpire($userId);
        if ($walletExpire) {
            return $walletExpire->fill([
                'amount' => $walletExpire->amount - $deductPoint,
            ])->save();
        }
    }

    public function updateAmountWallet($userId, $deductPoint)
    {
        $wallet = $this->userRepository->getWallet($userId);
        if ($wallet) {
            return $wallet->fill([
                'amount' => $wallet->amount - $deductPoint,
            ])->save();
        }
    }

    /**
     * check user exists withdraw.
     */
    public function checkExistsStatusWithDraw($userId)
    {
        return $this->withdrawRepository->checkExistsStatusWithDraw($userId);
    }
}
