<?php

namespace App\Http\Controllers\Api\CompanyTerms;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use App\Repositories\CompanyTermsRepository;
use App\Constant\StatusConstant;

class CompanyTermsController extends Controller
{
     /**
     * @var CompanyTermsRepository
     */
    protected $repository;

    /**
     * CompanyTermsController constructor.
     *
     * @param CompanyTermsRepository $repository
     */
    public function __construct(CompanyTermsRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * get index
     */
    public function getCompanyTerms(Request $request)
    {
        try {
            $type = $request->type ?? null;
            $data = $this->repository->findWhere(['type' => $type])->sortByDesc('id')->first();

            return response()->json([
                'success' => true,
                'data' => $data
            ]);
        } catch (\Throwable $th) {
            Log::error('Controllers\Admin\CompanyTerms\CompanyTermsController - index : '.$th->getMessage());
            DB::rollback();
            return response()->json([
                'error' => true,
                'message' => __('api.exception'),
            ]);
        }
    }

    public function getAllCompanyTerms() {
        try {
            $data = [
                'terms' => $this->repository->findWhere(['type' => StatusConstant::TYPE_TERMS_OF_USE])->sortByDesc('id')->first(),
                'symbol' => $this->repository->findWhere(['type' => StatusConstant::TYPE_SYMBOL])->sortByDesc('id')->first(),
                'privacy' => $this->repository->findWhere(['type' => StatusConstant::TYPE_PRIVACY_POLICY])->sortByDesc('id')->first(),
            ];
            return response()->json([
                'success' => true,
                'data' => $data
            ]);
        } catch (\Throwable $th) {
            Log::error('Controllers\Admin\CompanyTerms\CompanyTermsController - getAll : '.$th->getMessage());
            return response()->json([
                'error' => true,
                'message' => __('api.exception'),
            ]);
        }
    }
}
