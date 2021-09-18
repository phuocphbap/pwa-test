<?php

namespace App\Repositories;

use App\Entities\FeePayment;
use Prettus\Repository\Criteria\RequestCriteria;
use Prettus\Repository\Eloquent\BaseRepository;

/**
 * Class FeePaymentRepositoryEloquent.
 */
class FeePaymentRepositoryEloquent extends BaseRepository implements FeePaymentRepository
{
    /**
     * Specify Model class name.
     *
     * @return string
     */
    public function model()
    {
        return FeePayment::class;
    }

    /**
     * Boot up the repository, pushing criteria.
     */
    public function boot()
    {
        $this->pushCriteria(app(RequestCriteria::class));
    }

    public function getFeePayment()
    {
        return $this->model->first();
    }
}
