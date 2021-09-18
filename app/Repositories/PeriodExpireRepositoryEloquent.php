<?php

namespace App\Repositories;

use App\Entities\PeriodExpire;
use App\Repositories\PeriodExpireRepository;
use Prettus\Repository\Eloquent\BaseRepository;
use Prettus\Repository\Criteria\RequestCriteria;

/**
 * Class ReferralBonusRepositoryEloquent.
 *
 * @package namespace App\Repositories;
 */
class PeriodExpireRepositoryEloquent extends BaseRepository implements PeriodExpireRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return PeriodExpire::class;
    }



    /**
     * Boot up the repository, pushing criteria
     */
    public function boot()
    {
        $this->pushCriteria(app(RequestCriteria::class));
    }

}
