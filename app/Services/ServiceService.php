<?php

namespace App\Services;

use Carbon\Carbon;
use App\Constant\StatusConstant;
use App\Repositories\ServiceRepository;
use App\Repositories\RequestConsultingRepository;
use Illuminate\Http\Exceptions\HttpResponseException;
use App\Repositories\ServiceSuggestRepository;

class ServiceService
{
    protected $serviceRepo;
    protected $requestConsultRepo;
    protected $serviceSuggestRepo;

    /**
     * constructor.
     */
    public function __construct(
        ServiceRepository $serviceRepo,
        RequestConsultingRepository $requestConsultRepo,
        ServiceSuggestRepository $serviceSuggestRepo
    ) {
        $this->serviceRepo = $serviceRepo;
        $this->requestConsultRepo = $requestConsultRepo;
        $this->serviceSuggestRepo = $serviceSuggestRepo;
    }
    
    /**
     * servicesIsProgress
     *
     * @param int $serviceId
     *
     * @return bool
     */
    public function servicesIsProgress(int $serviceId): bool
    {
        return $this->requestConsultRepo->checkServiceIsProgess($serviceId);
    }
    
    /**
     * removeServiceSuggest
     *
     * @param int $serviceId
     *
     * @return void
     */
    public function removeServiceSuggest(int $serviceId)
    {
        return $this->serviceSuggestRepo->deleteByServiceId($serviceId);
    }
}
