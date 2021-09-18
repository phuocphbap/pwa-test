<?php

namespace App\Services\Admin;

use Carbon\Carbon;
use App\Entities\VService;
use App\Constant\StatusConstant;
use App\Repositories\ServiceRepository;
use App\Repositories\ServiceRegionRepository;
use App\Repositories\ServiceSuggestRepository;
use App\Repositories\RequestConsultingRepository;
use Illuminate\Http\Exceptions\HttpResponseException;

class ServicesService
{
    protected $serviceRepo;
    protected $requestConsultRepo;
    protected $serviceSuggestRepo;
    protected $serviceRegionRepo;

    /**
     * constructor.
     */
    public function __construct(
        ServiceRepository $serviceRepo,
        RequestConsultingRepository $requestConsultRepo,
        ServiceSuggestRepository $serviceSuggestRepo,
        ServiceRegionRepository $serviceRegionRepo
    ) {
        $this->serviceRepo = $serviceRepo;
        $this->requestConsultRepo = $requestConsultRepo;
        $this->serviceSuggestRepo = $serviceSuggestRepo;
        $this->serviceRegionRepo = $serviceRegionRepo;
    }

    /**
     * storeServiceSuggest
     *
     * @param array $serviceId
     *
     * @return bool
     */
    public function storeServiceSuggest(array $serviceId)
    {
        $this->serviceRepo->removeServiceSuggest();
        if (!empty($serviceId)) {
            foreach ($serviceId as $key => $value) {
                $this->serviceRepo->storeServiceSuggest($value);
            }
        }

        return true;
    }

    /**
     * check exists category id on service
     */
    public function checkExistsCategory($categoryId)
    {
        return $this->serviceRepo->where('category_id', $categoryId)->exists();
    }

    /**
     * update suggest related
     */
    public function updateSuggestRelated(array $serviceIds, $type)
    {
        foreach ($serviceIds as $serviceId) {
            $this->serviceRepo->update([
                'sort' => $type,
                'time_sort' => Carbon::parse(Carbon::now())->format('Y-m-d H:i:s.u'),
            ], $serviceId);
        }

        return true;
    }

    /**
     * handle block service
     */
    public function handleBlockService($serviceId, $type, $reason)
    {
        $checkServiceIsProgress = $this->requestConsultRepo->checkServiceIsProgess($serviceId);
        if ($checkServiceIsProgress) {
            $result = response()->json(['error' => true, 'messages' => __('api.request-consulting.service_exists_in_progress')]);
            throw new HttpResponseException($result);
        }

        switch ($type) {
            case StatusConstant::SERVICE_IS_BLOCKED:
                $this->serviceRepo->updateBlockService($serviceId, $reason, $type);
                break;
            case StatusConstant::SERVICE_NOT_IS_BLOCKED:
                $this->serviceRepo->updateBlockService($serviceId, $reason, $type);
                break;
            default:
                break;
        }
    }
    
    /**
     * getServiceById
     *
     * @param int $serviceId
     *
     * @return object
     */
    public function getServiceById(int $serviceId): object
    {
        return VService::find($serviceId);
    }
    
    /**
     * checkServiceInProgress
     *
     * @param int $serviceId
     *
     * @return bool
     */
    public function checkServiceInProgress(int $serviceId): bool
    {
        return $this->requestConsultRepo->checkServiceIsProgess($serviceId);
    }
    
    /**
     * removeServiceSuggestByAdmin
     *
     * @param int $serviceId
     *
     * @return void
     */
    public function removeServiceSuggestByAdmin(int $serviceId)
    {
        return $this->serviceSuggestRepo
            ->where('service_id', $serviceId)
            ->delete();
    }
    
    /**
     * removeServiceRegionByAdmin
     *
     * @param int $serviceId
     *
     * @return void
     */
    public function removeServiceRegionByAdmin(int $serviceId)
    {
        return $this->serviceRegionRepo->where('service_id', $serviceId)
                            ->delete();
    }
    
    /**
     * restoreServiceRegionByAdmin
     *
     * @param int $serviceId
     *
     * @return void
     */
    public function restoreServiceRegionByAdmin(int $serviceId)
    {
        return $this->serviceRegionRepo->onlyTrashed()
                ->where('service_id', $serviceId)
                ->update([
                    'deleted_at' => null
                ]);
    }
}
