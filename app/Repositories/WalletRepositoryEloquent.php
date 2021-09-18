<?php

namespace App\Repositories;

use App\Entities\Wallet;
use Prettus\Repository\Eloquent\BaseRepository;
use Prettus\Repository\Criteria\RequestCriteria;

/**
 * Class StoreArticleRepositoryEloquent.
 *
 * @package namespace App\Repositories;
 */
class WalletRepositoryEloquent extends BaseRepository implements WalletRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return Wallet::class;
    }

    /**
     * Boot up the repository, pushing criteria
     */
    public function boot()
    {
        $this->pushCriteria(app(RequestCriteria::class));
    }
}
