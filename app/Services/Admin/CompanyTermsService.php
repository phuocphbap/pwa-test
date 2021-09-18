<?php

namespace App\Services\Admin;

use App\Constant\StatusConstant;
use App\Repositories\CompanyTermsRepository;

class ServicesService
{
    protected $termsRepo;

    /**
     * constructor.
     */
    public function __construct(CompanyTermsRepository $termsRepo)
    {
        $this->termsRepo = $termsRepo;
    }
}
