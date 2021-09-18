<?php

namespace App\Services;

use App\Entities\Payment;
use App\Entities\RequestConsulting;
use App\Entities\WalletTransaction;
use App\Repositories\BillRepository;
use App\Repositories\CouponRepository;
use App\Repositories\FeePaymentRepository;
use App\Repositories\PaymentRepository;
use App\Repositories\RequestConsultingRepository;
use App\Repositories\ServiceRepository;
use App\Repositories\UserRepository;
use App\Repositories\WalletRepository;
use App\Repositories\WalletTransactionRepository;
use Illuminate\Support\Facades\DB;

class PaymentService
{
    protected $repository;
    protected $walletRepo;
    protected $walletTransRepo;
    protected $billRepo;
    protected $feeRepository;
    protected $serviceRepository;
    protected $couponRepository;
    protected $requestConsultingRepository;
    protected $userRepository;

    public function __construct(
        PaymentRepository $repository,
        WalletRepository $walletRepo,
        WalletTransactionRepository $walletTransRepo,
        BillRepository $billRepo,
        FeePaymentRepository $feeRepository,
        ServiceRepository $serviceRepository,
        CouponRepository $couponRepository,
        UserRepository $userRepository,
        RequestConsultingRepository $requestConsultingRepository
    ) {
        $this->repository = $repository;
        $this->walletRepo = $walletRepo;
        $this->walletTransRepo = $walletTransRepo;
        $this->billRepo = $billRepo;
        $this->feeRepository = $feeRepository;
        $this->serviceRepository = $serviceRepository;
        $this->couponRepository = $couponRepository;
        $this->requestConsultingRepository = $requestConsultingRepository;
        $this->userRepository = $userRepository;
    }

    public function performTransactionWallet($amount, $wallet, $performType, $performById, $type)
    {
        $walletTransaction = $wallet->walletTransactions()->create([
            'currency_type' => Payment::DEFAULT_CURRENCY,
            'amount' => $amount,
            'performed_type' => $performType,
            'performed_by_id' => $performById,
            'type' => $type,
        ]);

        return $walletTransaction->id;
    }

    /**
     * handle payment.
     */
    public function handlePayment($serviceId, $price, $consultingId, $amount, $point, $couponId = null, $paymentData = null)
    {
        try {
            DB::beginTransaction();
            $user = auth()->user();
            $userId = $user->id;
            $wallet = $user->wallet;
            $cusTransId = null;
            $cusTransExpireId = null;
            $paymentId = null;
            $performTypeWallet = 'App\Entities\Wallet';
            $performTypeWalletExpire = 'App\Entities\WalletExpire';
            $pointExpireValid = $this->userRepository->getPointValidWalletExpire($userId);
            //relate owner
            $ownerTransId = null;
            $ownerUser = $this->serviceRepository->find($serviceId)->store->user;
            $ownerUserId = $ownerUser->id;
            $feeData = $this->feeRepository->getFeePayment();
            $fee = ($feeData->fee_percent ?? 15) / 100;

            // deduct point on wallet of user buy service
            if ($pointExpireValid > 0) {
                $walletExpireId = $this->userRepository->getWalletExpire($userId)->id;

                $pointUpdateOnExpire = $point <= $pointExpireValid ? $point : $pointExpireValid;
                //create transactions and deduct point in wallet_expires
                $cusTransExpireId = $this->performTransactionWallet($pointUpdateOnExpire * -1, $wallet, $performTypeWalletExpire, $walletExpireId, WalletTransaction::TRANSACTION_PAYMENT);
                $this->updateAmountWalletExpire($userId, $pointUpdateOnExpire * -1);
                $redundPoint = $point - $pointExpireValid;
                if ($redundPoint > 0) {
                    //create transactions wallet then deduct point in wallet.
                    $cusTransId = $this->performTransactionWallet($redundPoint * -1, $wallet, $performTypeWallet, $wallet->id, WalletTransaction::TRANSACTION_PAYMENT);
                    $this->updateAmountWallet($userId, $redundPoint * -1);
                }
            } else {
                //create transactions wallet then deduct point in wallet.
                $cusTransId = $this->performTransactionWallet($point * -1, $wallet, $performTypeWallet, $wallet->id, WalletTransaction::TRANSACTION_PAYMENT);
                $this->updateAmountWallet($userId, $point * -1);
            }

            // plus point on wallet of user sell service
            $ownerTransId = $this->performTransactionWallet($price * (1 - $fee), $ownerUser->wallet, $performTypeWallet, $wallet->id, WalletTransaction::TRANSACTION_RECEIVE_PAYMENT);
            $this->updateAmountWallet($ownerUserId, $price * (1 - $fee));

            //create user use coupon
            if ($couponId) {
                $this->couponRepository->createUserCoupon($user->id, $couponId);
            }

            //create payment
            if ($paymentData && $paymentData['payment_intent_id']) {
                $paymentId = $this->repository->create($paymentData)->id;
            }
            //create bill  of payment
            $this->repository->createBill([
                'service_id' => $serviceId,
                'payment_id' => $paymentId,
                'customer_id' => $user->id,
                'owner_id' => $ownerUser->id,
                'customer_trans_id' => $cusTransId,
                'customer_trans_expire_id' => $cusTransExpireId,
                'owner_trans_id' => $ownerTransId,
                'coupon_id' => $couponId,
                'point' => $point,
                'price' => $price,
                'amount' => $amount,
                'point_owner_received' => (1 - $fee) * $price,
                'fee_payment_id' => $feeData->id,
                'consulting_id' => $consultingId,
            ]);

            // update status request consulting = 2
            $this->requestConsultingRepository->updateProgressRequestConsulting($consultingId, RequestConsulting::PROGRESS_UNDER_AGREEMENT);

            DB::commit();

            return true;
        } catch (\Exception $e) {
            DB::rollBack();

            return false;
        }
    }

    /**
     * get history payment.
     */
    public function historyPayment($userId, $startDate, $endDate, $type)
    {
        $wallet = $this->walletRepo->findWhere(['user_id' => $userId])->first();
        $walletTrans = $this->walletTransRepo->historyPayment($wallet, $startDate, $endDate, $type);

        return $walletTrans;
    }

    public function updateAmountWalletExpire($userId, $deductPoint)
    {
        $walletExpire = $this->userRepository->getWalletExpire($userId);
        if ($walletExpire) {
            return $walletExpire->fill([
                'amount' => $walletExpire->amount + $deductPoint,
            ])->save();
        }
    }

    public function updateAmountWallet($userId, $deductPoint)
    {
        $wallet = $this->userRepository->getWallet($userId);
        if ($wallet) {
            return $wallet->fill([
                'amount' => $wallet->amount + $deductPoint,
            ])->save();
        }
    }

    /**
     * handle payment user checkout.
     */
    public function handleCheckoutPayment($userId, $serviceId, $price, $consultingId, $amount, $point, $couponId = null, $sessionId=null)
    {
        try {
            DB::beginTransaction();
            $user = $this->userRepository->find($userId);
            $wallet = $user->wallet;
            $cusTransId = null;
            $cusTransExpireId = null;
            $performTypeWallet = 'App\Entities\Wallet';
            $performTypeWalletExpire = 'App\Entities\WalletExpire';
            $pointExpireValid = $this->userRepository->getPointValidWalletExpire($userId);
            //relate owner
            $ownerTransId = null;
            $ownerUser = $this->serviceRepository->find($serviceId)->store->user;
            $ownerUserId = $ownerUser->id;
            $feeData = $this->feeRepository->getFeePayment();
            $fee = ($feeData->fee_percent ?? 15) / 100;

            // deduct point on wallet of user buy service
            if ($pointExpireValid > 0) {
                $walletExpireId = $this->userRepository->getWalletExpire($userId)->id;

                $pointUpdateOnExpire = $point <= $pointExpireValid ? $point : $pointExpireValid;
                //create transactions and deduct point in wallet_expires
                $cusTransExpireId = $this->performTransactionWallet($pointUpdateOnExpire * -1, $wallet, $performTypeWalletExpire, $walletExpireId, WalletTransaction::TRANSACTION_PAYMENT);
                $this->updateAmountWalletExpire($userId, $pointUpdateOnExpire * -1);
                $redundPoint = $point - $pointExpireValid;
                if ($redundPoint > 0) {
                    //create transactions wallet then deduct point in wallet.
                    $cusTransId = $this->performTransactionWallet($redundPoint * -1, $wallet, $performTypeWallet, $wallet->id, WalletTransaction::TRANSACTION_PAYMENT);
                    $this->updateAmountWallet($userId, $redundPoint * -1);
                }
            } else {
                //create transactions wallet then deduct point in wallet.
                $cusTransId = $this->performTransactionWallet($point * -1, $wallet, $performTypeWallet, $wallet->id, WalletTransaction::TRANSACTION_PAYMENT);
                $this->updateAmountWallet($userId, $point * -1);
            }

            // plus point on wallet of user sell service
            $ownerTransId = $this->performTransactionWallet($price * (1 - $fee), $ownerUser->wallet, $performTypeWallet, $wallet->id, WalletTransaction::TRANSACTION_RECEIVE_PAYMENT);
            $this->updateAmountWallet($ownerUserId, $price * (1 - $fee));
            
            //create user use coupon
            if ($couponId) {
                $this->couponRepository->createUserCoupon($user->id, $couponId);
            }
            
            //create bill  of payment
            $this->repository->createBill([
                'service_id' => $serviceId,
                'customer_id' => $user->id,
                'owner_id' => $ownerUser->id,
                'customer_trans_id' => $cusTransId,
                'customer_trans_expire_id' => $cusTransExpireId,
                'owner_trans_id' => $ownerTransId,
                'coupon_id' => $couponId,
                'point' => $point,
                'price' => $price,
                'amount' => $amount,
                'point_owner_received' => (1 - $fee) * $price,
                'fee_payment_id' => $feeData->id,
                'consulting_id' => $consultingId,
                'checkout_session_id' => $sessionId,
            ]);

            // update status request consulting = 2
            $this->requestConsultingRepository->updateProgressRequestConsulting($consultingId, RequestConsulting::PROGRESS_UNDER_AGREEMENT);

            DB::commit();
            
            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            return false;
        }
    }
}
