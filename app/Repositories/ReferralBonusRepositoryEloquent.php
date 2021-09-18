<?php

namespace App\Repositories;

use Prettus\Repository\Eloquent\BaseRepository;
use Prettus\Repository\Criteria\RequestCriteria;
use App\Repositories\ReferralBonusRepository;
use App\Entities\ReferralBonus;

/**
 * Class ReferralBonusRepositoryEloquent.
 *
 * @package namespace App\Repositories;
 */
class ReferralBonusRepositoryEloquent extends BaseRepository implements ReferralBonusRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return ReferralBonus::class;
    }

    /**
     * Boot up the repository, pushing criteria
     */
    public function boot()
    {
        $this->pushCriteria(app(RequestCriteria::class));
    }

    public function getReferralBonus()
    {
        $data = $this->model->orderBy('id', 'desc')->first();
        return $data;
    }
}
