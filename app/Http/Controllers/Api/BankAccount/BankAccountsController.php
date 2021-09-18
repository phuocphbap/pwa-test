<?php

namespace App\Http\Controllers\Api\BankAccount;

use App\Http\Controllers\Controller;
use App\Http\Requests\CreateAccountBankRequest;
use App\Http\Requests\UpdateBankAccountRequest;
use App\Repositories\BankAccountRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

/**
 * Class BankAccountsController.
 */
class BankAccountsController extends Controller
{
    /**
     * @var BankAccountRepository
     */
    protected $repository;

    /**
     * BankAccountsController constructor.
     */
    public function __construct(BankAccountRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * create account bank.
     */
    public function createAccountBank(CreateAccountBankRequest $request)
    {
        try {
            $userId = auth()->user()->id;
            $bank = $this->repository->findWhere(['user_id' => $userId])->first();
            if ($bank) {
                return response()->json([
                    'error' => true,
                    'message' => __('api.bank_account_exists'),
                ]);
            }

            $request->merge(['user_id' => $userId]);
            $id = $this->repository->create($request->all())->id;

            return response()->json([
                'message' => __('api.bank_account.create_success'),
                'data' => ['id' => $id],
            ]);
        } catch (\Throwable $th) {
            Log::error('Controllers\Api\BankAccount\BankAccountsController - createAccountBank : '.$th->getMessage());

            return response()->json([
                'error' => true,
                'message' => __('api.exception'),
            ]);
        }
    }

    /**
     * get list category bank account.
     */
    public function getListCategoryBankAccount(Request $request)
    {
        try {
            $data = $this->repository->getAllCategoryBankAccount();

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

    /**
     * show detail.
     */
    public function show(Request $request)
    {
        try {
            $user = auth()->user();
            $data = $this->repository->findWhere(['user_id' => $user->id]);

            return response()->json([
                'success' => true,
                'data' => $data,
            ]);
        } catch (\Throwable $th) {
            Log::error('Controllers\Api\BankAccount\BankAccountsController - show : '.$th->getMessage());

            return response()->json([
                'error' => true,
                'message' => __('api.exception'),
            ]);
        }
    }

    /**
     * update bank account.
     */
    public function update(UpdateBankAccountRequest $request)
    {
        try {
            $userId = auth()->user()->id;
            $bank = $this->repository->findWhere(['user_id' => $userId])->first();
            $id = $this->repository->update($request->all(), $bank->id)->id;

            return response()->json([
                'message' => __('api.bank_account.update_success'),
                'data' => ['id' => $id],
            ]);
        } catch (\Throwable $th) {
            Log::error('Controllers\Api\BankAccount\BankAccountsController - update : '.$th->getMessage());

            return response()->json([
                'error' => true,
                'message' => __('api.exception'),
            ]);
        }
    }
}
