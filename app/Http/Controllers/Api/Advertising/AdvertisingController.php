<?php

namespace App\Http\Controllers\Api\Advertising;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use App\Services\Admin\AdversitingService;
use App\Repositories\AdvertisingBlockRepository;

class AdvertisingController extends Controller
{
    /**
     * @var ReferralBonusRepository
     */
    protected $repository;

    /**
     * @var AdversitingService
     */
    protected $adsService;

    /**
     * BonusesController constructor.
     */
    public function __construct(AdvertisingBlockRepository $repository, AdversitingService $adsService)
    {
        $this->repository = $repository;
        $this->adsService = $adsService;
    }

    /**
     * get advertising
     */
    public function getAdvertising(Request $request)
    {
        try {
            $type = $request->type ?? 1;
            $data = $this->adsService->getAdvertising($type);

            return response()->json([
                'success' => true,
                'data' => $data,
            ]);
        } catch (\Throwable $th) {
            Log::error('Controllers\Api\BankAccount\BankAccountsController - getListCategoryBankAccount : '.$th->getMessage());

            return response()->json([
                'error' => true,
                'message' => __('api.exception'),
            ]);
        }
    }
}
