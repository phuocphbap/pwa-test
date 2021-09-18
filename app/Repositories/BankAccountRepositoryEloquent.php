<?php

namespace App\Repositories;

use Prettus\Repository\Eloquent\BaseRepository;
use Prettus\Repository\Criteria\RequestCriteria;
use App\Repositories\BankAccountRepository;
use App\Entities\BankAccount;
use App\Entities\CategoryBankAccount;
use App\Validators\BankAccountValidator;

/**
 * Class BankAccountRepositoryEloquent.
 *
 * @package namespace App\Repositories;
 */
class BankAccountRepositoryEloquent extends BaseRepository implements BankAccountRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return BankAccount::class;
    }



    /**
     * Boot up the repository, pushing criteria
     */
    public function boot()
    {
        $this->pushCriteria(app(RequestCriteria::class));
    }

    /**
     * get all category bank account
     */
    public function getAllCategoryBankAccount()
    {
        return CategoryBankAccount::all();
    }

    public function getBankAccountByUserId($userId)
    {
        return $this->model->where('user_id', $userId)
                        ->with('category')->first();
    }
}
