<?php

namespace App\Services\Admin;

use App\Constant\StatusConstant;
use App\Repositories\BonusRepository;
use App\Repositories\IdentityCardRepository;
use App\Repositories\PeriodExpireRepository;
use App\Repositories\ReferralBonusRepository;
use App\Repositories\UserRepository;
use App\Repositories\WalletExpireRepository;
use App\Repositories\WalletRepository;
use App\Repositories\WalletTransactionRepository;
use Carbon\Carbon;

class BonusService
{
    protected $referBonusRepo;
    protected $bonusRepo;
    protected $walletTrans;
    protected $walletExpire;
    protected $wallet;
    protected $periodExpire;
    protected $userRepo;
    protected $IDRepo;
    protected $noticesService;

    /**
     * constructor.
     */
    public function __construct(
        ReferralBonusRepository $referBonusRepo,
        BonusRepository $bonusRepo,
        WalletTransactionRepository $walletTrans,
        WalletExpireRepository $walletExpire,
        WalletRepository $wallet,
        PeriodExpireRepository $periodExpire,
        UserRepository $userRepo,
        IdentityCardRepository $IDRepo,
        NotificationsService $noticesService
    ) {
        $this->referBonusRepo = $referBonusRepo;
        $this->bonusRepo = $bonusRepo;
        $this->walletTrans = $walletTrans;
        $this->walletExpire = $walletExpire;
        $this->wallet = $wallet;
        $this->periodExpire = $periodExpire;
        $this->userRepo = $userRepo;
        $this->IDRepo = $IDRepo;
        $this->noticesService = $noticesService;
    }

    /**
     * handle bonus indecated.
     */
    public function handleBonusIndecated(array $userIds, $amount)
    {
        $userIds = $this->userRepo->getUserActive($userIds);
        foreach ($userIds as $userId) {
            $this->subHandleBonusIndecated($userId, $amount);
        }

        return true;
    }

    /**
     * sub function handle bonus indecated;.
     */
    public function subHandleBonusIndecated($userId, $amount)
    {
        $wallet = $this->wallet->findWhere(['user_id' => $userId])->first();
        $walletExpire = $this->walletExpire->findWhere(['wallet_id' => $wallet->id])->sortByDesc('id')->first();
        $periodExpire = $this->periodExpire->first();
        $dateExpire = Carbon::now()->addMonths($periodExpire->sum_month);
        if (!$walletExpire) {
            $this->subBonusTransaction($userId, $amount, $dateExpire, $wallet, $periodExpire, StatusConstant::BONUS_TYPE_ADMIN);
        } else {
            // check expire wallet
            if (Carbon::now()->lte($walletExpire->expire_date)) {
                $total = (float) $amount + (float) $walletExpire->amount;
                $this->walletExpire->updateDateExpire($walletExpire->id, $total, $dateExpire, $periodExpire->id);
                $idWalletTrans = $this->walletTrans->createWalletTrans($wallet->id, $amount, StatusConstant::TRANSACTION_TYPE_WALLET_EXPIRE, $walletExpire->id, null, StatusConstant::TRANSACTION_BONUS);
                $this->bonusRepo->createBonus($userId, null, $amount, $idWalletTrans, $walletExpire->id, StatusConstant::BONUS_TYPE_ADMIN);
            } else {
                $this->subBonusTransaction($userId, $amount, $dateExpire, $wallet, $periodExpire, StatusConstant::BONUS_TYPE_ADMIN);
            }
        }

        // send notifications when bonus
        $this->noticesService->noticeBonusUser($userId, $amount, $dateExpire);
    }

    /**
     * sub function bonus transaction.
     */
    public function subBonusTransaction($userId, $amount, $dateExpire, $wallet, $periodExpire, $typeBonus)
    {
        $idWalletExpire = $this->walletExpire->createWalletExpire($wallet->id, $amount, $dateExpire, $periodExpire->id);
        $idWalletTrans = $this->walletTrans->createWalletTrans($wallet->id, $amount, StatusConstant::TRANSACTION_TYPE_WALLET_EXPIRE, $idWalletExpire, null, StatusConstant::TRANSACTION_BONUS);
        $this->bonusRepo->createBonus($userId, null, $amount, $idWalletTrans, $idWalletExpire, $typeBonus);

        return true;
    }

    /**
     * handle bonus to all user.
     */
    public function handleBonusToAllUser($amount)
    {
        $userIds = $this->userRepo->getListUserActive('id');
        $userIds->chunk(200, function ($users) use ($amount) {
            foreach ($users as $userId) {
                $this->subHandleBonusIndecated($userId, $amount);
            }
        });

        return true;
    }

    /**
     * check identity card is process.
     */
    public function checkIdentiryCardIsProcess($userId)
    {
        return $this->userRepo->checkIdentiryCardIsProcess($userId);
    }

    /**
     * handle verify identity card.
     */
    public function handleVerifyIdentityCard($userId, $type)
    {
        switch ($type) {
            case StatusConstant::IDENTITY_ACCEPT_STATUS:
                $user = $this->userRepo->find($userId);
                if ($user->input_refferal_code) {
                    $userRefferal = $this->userRepo->findWhere(['referral_code' => $user->input_refferal_code])->first();
                    if ($userRefferal) {
                        $this->handleBonusReferral($userId, StatusConstant::BONUS_TYPE_INPUT_REFFERAL, $userRefferal->id);
                        $this->handleBonusReferral($userRefferal->id, StatusConstant::BONUS_TYPE_REFFERAL, $userId);
                    }
                }
                $this->userRepo->updateIdentityStatus($userId, StatusConstant::IDENTITY_ACCEPT_STATUS);
                $this->noticesService->noticeVerifyIdentityCard(StatusConstant::IDENTITY_ACCEPT_STATUS, $userId);
                break;
            case StatusConstant::IDENTITY_REJECT_STATUS:
                $this->IDRepo->removeIdentityCard($userId);
                $this->userRepo->updateIdentityStatus($userId, StatusConstant::IDENTITY_REJECT_STATUS);
                $this->noticesService->noticeVerifyIdentityCard(StatusConstant::IDENTITY_REJECT_STATUS, $userId);
                break;
            default:
                break;
        }
    }

    /**
     * handle bonus referral.
     */
    public function handleBonusReferral($userId, $typeBonus, $userInputReffer)
    {
        $wallet = $this->wallet->findWhere(['user_id' => $userId])->first();
        $walletExpire = $this->walletExpire->findWhere(['wallet_id' => $wallet->id])->sortByDesc('id')->first();
        $periodExpire = $this->periodExpire->first();
        $dateExpire = Carbon::now()->addMonths($periodExpire->sum_month);
        $refferal = $this->referBonusRepo->first();
        if (!$refferal) {
            throw new \Exception(__('api.refferal_bonus.data_not_exists'));
        }
        $amount = $refferal->amount ?? 0;
        if (!$walletExpire) {
            $this->subBonusReferral($userId, $amount, $dateExpire, $wallet, $periodExpire, $typeBonus, $refferal->id, $userInputReffer);
        } else {
            // check expire wallet
            if (Carbon::now()->lte($walletExpire->expire_date)) {
                $total = (float) $amount + (float) $walletExpire->amount;
                $this->walletExpire->updateDateExpire($walletExpire->id, $total, $dateExpire, $periodExpire->id);
                $idWalletTrans = $this->walletTrans->createWalletTrans($wallet->id, $amount, StatusConstant::TRANSACTION_TYPE_WALLET_EXPIRE, $walletExpire->id, null, StatusConstant::TRANSACTION_BONUS);
                if ($userInputReffer) {
                    $this->bonusRepo->createBonus($userId, $refferal->id, $amount, $idWalletTrans, $walletExpire->id, $typeBonus, $userInputReffer);
                } else {
                    $this->bonusRepo->createBonus($userId, $refferal->id, $amount, $idWalletTrans, $walletExpire->id, $typeBonus);
                }
            } else {
                $this->subBonusReferral($userId, $amount, $dateExpire, $wallet, $periodExpire, $typeBonus, $refferal->id, $userInputReffer);
            }
        }

        $this->noticesService->noticeBonusInputReferralCode($userId, $amount);

        return true;
    }

    /**
     * sub function bonus referral.
     */
    public function subBonusReferral($userId, $amount, $dateExpire, $wallet, $periodExpire, $typeBonus, $refferalId, $userInputReffer)
    {
        $idWalletExpire = $this->walletExpire->createWalletExpire($wallet->id, $amount, $dateExpire, $periodExpire->id);
        $idWalletTrans = $this->walletTrans->createWalletTrans($wallet->id, $amount, StatusConstant::TRANSACTION_TYPE_WALLET_EXPIRE, $idWalletExpire, null, StatusConstant::TRANSACTION_BONUS);
        if ($userInputReffer) {
            $this->bonusRepo->createBonus($userId, $refferalId, $amount, $idWalletTrans, $idWalletExpire, $typeBonus, $userInputReffer);
        } else {
            $this->bonusRepo->createBonus($userId, $refferalId, $amount, $idWalletTrans, $idWalletExpire, $typeBonus);
        }

        return true;
    }
}
