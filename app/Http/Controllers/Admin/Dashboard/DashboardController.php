<?php

namespace App\Http\Controllers\Admin\Dashboard;

use App\Http\Controllers\Controller;
use App\Repositories\RequestConsultingRepository;
use App\Repositories\ServiceRepository;
use App\Repositories\UserRepository;
use App\Repositories\WalletExpireRepository;
use App\Repositories\WalletRepository;

class DashboardController extends Controller
{
    protected $userRepository;
    protected $serviceRepository;
    protected $requestConsultingRepository;
    protected $walletRepository;
    protected $walletExpireRepository;

    public function __construct(
        UserRepository $userRepository,
        ServiceRepository $serviceRepository,
        RequestConsultingRepository $requestConsultingRepository,
        WalletRepository $walletRepository,
        WalletExpireRepository $walletExpireRepository
    ) {
        $this->userRepository = $userRepository;
        $this->serviceRepository = $serviceRepository;
        $this->requestConsultingRepository = $requestConsultingRepository;
        $this->walletRepository = $walletRepository;
        $this->walletExpireRepository = $walletExpireRepository;
    }

    public function index()
    {
        $userCount = $this->userRepository->count();
        $serviceCount = $this->serviceRepository->count();
        $todayAgreement = $this->requestConsultingRepository->listAgreementToday()->count();
        $yesterdayAgreement = $this->requestConsultingRepository->listAgreementYesterday()->count();
        $pointWallet = $this->walletRepository->sum('amount');
        $pointWalletExpire = $this->walletExpireRepository->where('expire_date', '>=', \Carbon\Carbon::now())->sum('amount');

        return response()->json([
            'success' => true,
            'user_count' => $userCount,
            'service_count' => $serviceCount,
            'today_agreements' => $todayAgreement,
            'yesterday_agreements' => $yesterdayAgreement,
            'point_wallet' => $pointWallet,
            'point_wallet_expire' => $pointWalletExpire,
        ]);
    }
}
