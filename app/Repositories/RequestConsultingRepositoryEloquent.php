<?php

namespace App\Repositories;

use App\Constant\StatusConstant;
use App\Entities\RequestConsulting;
use App\Validators\RequestConsultingValidator;
use Carbon\Carbon;
use Prettus\Repository\Criteria\RequestCriteria;
use Prettus\Repository\Eloquent\BaseRepository;

/**
 * Class RequestConsultingRepositoryEloquent.
 */
class RequestConsultingRepositoryEloquent extends BaseRepository implements RequestConsultingRepository
{
    /**
     * Specify Model class name.
     *
     * @return string
     */
    public function model()
    {
        return RequestConsulting::class;
    }

    /**
     * Specify Validator class name.
     *
     * @return mixed
     */
    public function validator()
    {
        return RequestConsultingValidator::class;
    }

    /**
     * Boot up the repository, pushing criteria.
     */
    public function boot()
    {
        $this->pushCriteria(app(RequestCriteria::class));
    }

    public function listRequestConsulting($progressType, $orderBy = null)
    {
        $userId = auth()->user()->id;
        return  $this->model->with(['service' => function ($query) {
            $query->withTrashed();
        }])
        ->with('owner:id,user_name,avatar')
        ->whereRaw('(customer_id='. $userId.' OR owner_id='.$userId.')')
        
        ->when($orderBy, function ($q, $orderBy) {
            switch ($orderBy) {
                case 'new':
                    $q->orderBy('id', 'desc');
                    break;
                case 'process':
                    $q->orderBy('progress', 'ASC')->orderby('id','DESC');
                    break;
                default:
                    break;
            }
            
        })
        ->when(!$orderBy, function ($q) {
            $q->orderBy('id', 'desc');
        })
        ->when($progressType, function ($q, $progressType) {
            switch ($progressType) {
                case 'IN_PROGRESS':
                    $q->where(function($query) {
                        $query->whereIn('progress', [RequestConsulting::PROGRESS_BEFORE_AGREEMENT, RequestConsulting::PROGRESS_CONFIRMED_REQUEST, RequestConsulting::PROGRESS_UNDER_AGREEMENT, RequestConsulting::PROGRESS_WAITING_EVALUATION])
                            ->where('state', RequestConsulting::STATE_ACTICE);
                    });
                    break;
                case 'SALE':
                    $q->where(function($query) {
                        $query->where('progress', RequestConsulting::PROGRESS_BEFORE_AGREEMENT)
                            ->where('state', RequestConsulting::STATE_ACTICE);
                    });
                    break;
                case 'BEFORE_AGREEMENT':
                    $q->where(function($query) {
                        $query->where('progress', RequestConsulting::PROGRESS_CONFIRMED_REQUEST)
                            ->where('state', RequestConsulting::STATE_ACTICE);
                    });
                    break;
                case 'UNDER_AGREEMENT':
                    $q->where(function($query) {
                        $query->where('progress', RequestConsulting::PROGRESS_UNDER_AGREEMENT)
                            ->where('state', RequestConsulting::STATE_ACTICE);
                    });
                    break;
                case 'WAITING_EVALUATION':
                    $q->where(function($query) {
                        $query->where('progress', RequestConsulting::PROGRESS_WAITING_EVALUATION)
                            ->where('state', RequestConsulting::STATE_ACTICE);
                    });
                    break;
                case 'PROGRESS_DONE':
                    $q->where(function($query) {
                        $query->where('progress', RequestConsulting::PROGRESS_DONE)
                        ->orwhere('state', RequestConsulting::STATE_CANCEL);
                    });
                    break;
                default:
                    break;
                }
        });
    }

    /**
     * get request consulting.
     */
    public function getRequestConsulting($ownerId, $customerId, $servicesId)
    {
        return $this->model->where('owner_id', $ownerId)
                        ->where('customer_id', $customerId)
                        ->where('service_id', $servicesId)
                        ->where('progress', 1)
                        ->where('state', 1)
                        ->first();
    }

    public function updateProgressRequestConsulting($id, $progress)
    {
        $this->model->where('id', $id)
                ->update(['progress' => $progress]);

        return true;
    }

    /**
     * check exists progress of user when leave group.
     */
    public function checkExistsProgress($userId)
    {
        return $this->model->where(function ($query) use ($userId) {
            $query->where('customer_id', $userId)
                    ->orWhere('owner_id', $userId);
        })
                ->where('progress', '!=', RequestConsulting::PROGRESS_DONE)
                ->where('state', '!=', RequestConsulting::STATE_CANCEL)
                ->exists();
    }

    public function listAgreementToday()
    {
        return $this->model
                ->where('progress', RequestConsulting::PROGRESS_DONE)
                ->where('state', RequestConsulting::STATE_ACTICE)
                ->whereDate('updated_at', Carbon::today())
                ->get();
    }

    public function listAgreementYesterday()
    {
        return $this->model
                ->where('progress', RequestConsulting::PROGRESS_DONE)
                ->where('state', RequestConsulting::STATE_ACTICE)
                ->where('updated_at', '>', Carbon::today()->subDays(1))
                ->where('updated_at', '<', Carbon::today())
                ->get();
    }

    /**
     * get request consulting by user_id.
     */
    public function getConsultingByCustomerId($userId, $progressType)
    {
        return $this->model
                    ->when($progressType, function ($q, $progressType) {
                        switch ($progressType) {
                            case 'IN_PROGRESS':
                                $q->whereIn('progress', [RequestConsulting::PROGRESS_BEFORE_AGREEMENT, RequestConsulting::PROGRESS_CONFIRMED_REQUEST, RequestConsulting::PROGRESS_UNDER_AGREEMENT, RequestConsulting::PROGRESS_WAITING_EVALUATION]);
                                break;
                            case 'SALE':
                                $q->where('progress', RequestConsulting::PROGRESS_BEFORE_AGREEMENT);
                                break;
                            case 'BEFORE_AGREEMENT':
                                $q->where('progress', RequestConsulting::PROGRESS_CONFIRMED_REQUEST);
                                break;
                            case 'UNDER_AGREEMENT':
                                $q->where('progress', RequestConsulting::PROGRESS_UNDER_AGREEMENT);
                                break;
                            case 'WAITING_EVALUATION':
                                $q->where('progress', RequestConsulting::PROGRESS_WAITING_EVALUATION);
                                break;
                            case 'PROGRESS_DONE':
                                $q->where('progress', RequestConsulting::PROGRESS_DONE);
                                break;
                            default:
                                break;
                            }
                    })
                    ->with('service:id,service_title,service_image')
                    ->with('owner:id,user_name,avatar')
                    ->with('customer:id,user_name,avatar')
                    ->where('customer_id', $userId)
                    ->where('state', StatusConstant::CONSULTING_STATE_ACTIVE)
                    ->get();
    }

    /**
     * get request consulting by user_id.
     */
    public function getConsultingByService($userId, $progressType, $serviceId)
    {
        return $this->model
                    ->when($progressType, function ($q, $progressType) {
                        switch ($progressType) {
                            case 'IN_PROGRESS':
                                $q->whereIn('progress', [RequestConsulting::PROGRESS_BEFORE_AGREEMENT, RequestConsulting::PROGRESS_CONFIRMED_REQUEST, RequestConsulting::PROGRESS_UNDER_AGREEMENT, RequestConsulting::PROGRESS_WAITING_EVALUATION]);
                                break;
                            case 'SALE':
                                $q->where('progress', RequestConsulting::PROGRESS_BEFORE_AGREEMENT);
                                break;
                            case 'BEFORE_AGREEMENT':
                                $q->where('progress', RequestConsulting::PROGRESS_CONFIRMED_REQUEST);
                                break;
                            case 'UNDER_AGREEMENT':
                                $q->where('progress', RequestConsulting::PROGRESS_UNDER_AGREEMENT);
                                break;
                            case 'WAITING_EVALUATION':
                                $q->where('progress', RequestConsulting::PROGRESS_WAITING_EVALUATION);
                                break;
                            case 'PROGRESS_DONE':
                                $q->where('progress', RequestConsulting::PROGRESS_DONE);
                                break;
                            default:
                                break;
                            }
                    })
                    ->with('service:id,service_title,service_image')
                    ->with('owner:id,user_name,avatar')
                    ->with('customer:id,user_name,avatar')
                    ->where('owner_id', $userId)
                    ->where('service_id', $serviceId)
                    ->where('state', StatusConstant::CONSULTING_STATE_ACTIVE)
                    ->get();
    }

    /**
     * get request consulting by user_id.
     */
    public function getConsultingByOwnerId($userId, $progressType)
    {
        return $this->model
                    ->when($progressType, function ($q, $progressType) {
                        switch ($progressType) {
                            case 'IN_PROGRESS':
                                $q->whereIn('progress', [RequestConsulting::PROGRESS_BEFORE_AGREEMENT, RequestConsulting::PROGRESS_CONFIRMED_REQUEST, RequestConsulting::PROGRESS_UNDER_AGREEMENT, RequestConsulting::PROGRESS_WAITING_EVALUATION]);
                                break;
                            case 'SALE':
                                $q->where('progress', RequestConsulting::PROGRESS_BEFORE_AGREEMENT);
                                break;
                            case 'BEFORE_AGREEMENT':
                                $q->where('progress', RequestConsulting::PROGRESS_CONFIRMED_REQUEST);
                                break;
                            case 'UNDER_AGREEMENT':
                                $q->where('progress', RequestConsulting::PROGRESS_UNDER_AGREEMENT);
                                break;
                            case 'WAITING_EVALUATION':
                                $q->where('progress', RequestConsulting::PROGRESS_WAITING_EVALUATION);
                                break;
                            case 'PROGRESS_DONE':
                                $q->where('progress', RequestConsulting::PROGRESS_DONE);
                                break;
                            default:
                                break;
                            }
                    })
                    ->with('service:id,service_title,service_image')
                    ->with('owner:id,user_name,avatar')
                    ->with('customer:id,user_name,avatar')
                    ->where('owner_id', $userId)
                    ->where('state', StatusConstant::CONSULTING_STATE_ACTIVE)
                    ->get();
    }

    /**
     * get detail progress.
     */
    public function getDetailProgress($consultingId)
    {
        return $this->model->where('id', $consultingId)
                    ->where('state', StatusConstant::CONSULTING_STATE_ACTIVE)
                    ->with('service.category:id,name,description', 'service.regions:id,state_name')
                    ->with('owner:id,user_name,avatar')
                    ->with('customer:id,user_name,avatar')
                    ->first();
    }
    
    /**
     * checkServiceIsProgess
     *
     * @param int $servicesId
     *
     * @return bool
     */
    public function checkServiceIsProgess(int $servicesId): bool
    {
        $progress = $this->model->where('service_id', $servicesId)
                                ->wherein('progress', [StatusConstant::PROGRESS_BEFORE_AGREEMENT, StatusConstant::PROGRESS_CONFIRMED_REQUEST,StatusConstant::PROGRESS_UNDER_AGREEMENT,StatusConstant::PROGRESS_WAITING_EVALUATION])
                                ->where('state', StatusConstant::CONSULTING_STATE_ACTIVE)
                                ->exists();
        if ($progress) {
            return true;
        }
        return false;
    }
}
