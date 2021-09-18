<?php

namespace App\Services\Admin;

use App\Entities\Payment;
use App\Entities\WalletTransaction;
use App\Repositories\PeriodExpireRepository;
use App\Repositories\UserRepository;
use App\Repositories\WalletExpireRepository;
use App\Repositories\WalletRepository;
use App\Repositories\WalletTransactionRepository;
use App\Repositories\WithdrawRequestRepository;
use Carbon\Carbon;

class WithdrawService
{
    protected $userRepository;
    protected $withdrawRepository;
    protected $walletRepository;
    protected $walletTransactionRepository;
    protected $periodExpireRePo;
    protected $walletExpireRepository;

    public function __construct(
        UserRepository $userRepository,
        WithdrawRequestRepository $withdrawRepository,
        WalletRepository $walletRepository,
        WalletTransactionRepository $walletTransactionRepository,
        PeriodExpireRepository $periodExpireRePo,
        WalletExpireRepository $walletExpireRepository
    ) {
        $this->userRepository = $userRepository;
        $this->withdrawRepository = $withdrawRepository;
        $this->walletRepository = $walletRepository;
        $this->walletTransactionRepository = $walletTransactionRepository;
        $this->periodExpireRePo = $periodExpireRePo;
        $this->walletExpireRepository = $walletExpireRepository;
    }

    public function rejectWithDrawPoint($id)
    {
        $withdrawData = $this->withdrawRepository->find($id);
        $userId = $withdrawData->user_id;
        $wallet = $this->userRepository->getWallet($userId);
        $periodExpire = $this->periodExpireRePo->first();
        $dateExpire = Carbon::now()->addMonths($periodExpire->sum_month);
        $performTypeWallet = 'App\Entities\Wallet';
        $performTypeWalletExpire = 'App\Entities\WalletExpire';
        if ($withdrawData->trans_wallet_expire_id) {
            $walletExpire = $this->userRepository->getWalletExpire($userId);
            $walletExpireId = $walletExpire ? $walletExpire->id : null;
            $oldTransExpire = $this->walletTransactionRepository->find($withdrawData->trans_wallet_expire_id);

            if (!$walletExpireId) {
                $walletExpireId = $this->walletExpireRepository->createWalletExpire($wallet->id, 0, $dateExpire, $periodExpire->id);
            }
            $this->performTransactionWallet(abs($oldTransExpire->amount), $wallet, $performTypeWalletExpire, $walletExpireId);
            $this->updateAmountWalletExpire($userId, abs($oldTransExpire->amount), $dateExpire);
        }
        if ($withdrawData->trans_wallet_id) {
            $oldTransWallet = $this->walletTransactionRepository->find($withdrawData->trans_wallet_id);

            $this->performTransactionWallet(abs($oldTransWallet->amount), $wallet, $performTypeWallet, $wallet->id);
            $this->updateAmountWallet($userId, abs($oldTransWallet->amount));
        }

        return true;
    }

    public function performTransactionWallet($amount, $wallet, $performType, $performById)
    {
        $walletTransaction = $wallet->walletTransactions()->create([
            'currency_type' => Payment::DEFAULT_CURRENCY,
            'amount' => $amount,
            'performed_type' => $performType,
            'performed_by_id' => $performById,
            'type' => WalletTransaction::TRANSACTION_REFUND,
        ]);

        return $walletTransaction->id;
    }

    public function updateAmountWalletExpire($userId, $plusPoint, $dateExpire)
    {
        $walletExpire = $this->userRepository->getWalletExpire($userId);
        if ($walletExpire) {
            return $walletExpire->fill([
                'amount' => $walletExpire->amount + $plusPoint,
                'expire_date' => $dateExpire,
            ])->save();
        }
    }

    public function updateAmountWallet($userId, $plusPoint)
    {
        $wallet = $this->userRepository->getWallet($userId);
        if ($wallet) {
            return $wallet->fill([
                'amount' => $wallet->amount + $plusPoint,
            ])->save();
        }
    }
}
