<?php

namespace App\Repositories;

use Prettus\Repository\Eloquent\BaseRepository;
use Prettus\Repository\Criteria\RequestCriteria;
use App\Repositories\CompanyTermsRepository;
use App\Entities\CompanyTerms;
use App\Validators\CompanyTermsValidator;

/**
 * Class CompanyTermsRepositoryEloquent.
 *
 * @package namespace App\Repositories;
 */
class CompanyTermsRepositoryEloquent extends BaseRepository implements CompanyTermsRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return CompanyTerms::class;
    }

    /**
     * Boot up the repository, pushing criteria
     */
    public function boot()
    {
        $this->pushCriteria(app(RequestCriteria::class));
    }

}
