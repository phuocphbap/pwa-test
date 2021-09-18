<?php

namespace App\Repositories;

use App\Entities\RequestConsulting;
use App\Entities\ServiceReview;
use DB;
use Prettus\Repository\Criteria\RequestCriteria;
use Prettus\Repository\Eloquent\BaseRepository;

/**
 * Class ServiceReviewRepositoryEloquent.
 */
class ServiceReviewRepositoryEloquent extends BaseRepository implements ServiceReviewRepository
{
    /**
     * Specify Model class name.
     *
     * @return string
     */
    public function model()
    {
        return ServiceReview::class;
    }

    /**
     * Boot up the repository, pushing criteria.
     */
    public function boot()
    {
        $this->pushCriteria(app(RequestCriteria::class));
    }

    /**
     * Create service review.
     */
    public function createServiceReview($consultingId, $value, $message)
    {
        try {
            DB::beginTransaction();
            $requestConsulting = RequestConsulting::findOrFail($consultingId);
            // isOwnerService = 1 or not = 0
            $isOwnerService = auth()->user()->id == $requestConsulting->service->store->user->id ? ServiceReview::IS_OWNER_SERVICE : ServiceReview::NOT_OWNER_SERVICE;
            $dataCreate = [
                'consulting_id' => $consultingId,
                'service_id' => $requestConsulting->service->id,
                'store_id' => $requestConsulting->service->store->id,
                'reviewer_id' => auth()->user()->id,
                'is_owner' => $isOwnerService,
                'value' => $value,
                'message' => $message,
            ];
            $dataCreated = $this->model->create($dataCreate);
            if ($dataCreated->id) {
                if ($isOwnerService) {
                    if ($requestConsulting->progress == RequestConsulting::PROGRESS_WAITING_EVALUATION) {
                        $requestConsulting->update(['progress' => RequestConsulting::PROGRESS_DONE]);
                    } else {
                        return false;
                    }
                } else {
                    if ($requestConsulting->progress == RequestConsulting::PROGRESS_UNDER_AGREEMENT) {
                        $requestConsulting->update(['progress' => RequestConsulting::PROGRESS_WAITING_EVALUATION]);
                    } else {
                        return false;
                    }
                }
            }

            DB::commit();

            return $requestConsulting->refresh();
        } catch (\Exception $e) {
            DB::rollBack();

            return false;
        }
    }

    public function cancelServiceReview($consultingId)
    {
        try {
            DB::beginTransaction();
            $requestConsulting = RequestConsulting::findOrFail($consultingId);
            // isOwnerService = 1 or not = 0
            $isOwnerService = auth()->user()->id == $requestConsulting->service->store->user->id ? ServiceReview::IS_OWNER_SERVICE : ServiceReview::NOT_OWNER_SERVICE;

            if ($isOwnerService) {
                if ($requestConsulting->progress == RequestConsulting::PROGRESS_WAITING_EVALUATION) {
                    $requestConsulting->update(['progress' => RequestConsulting::PROGRESS_DONE]);
                } else {
                    return false;
                }
            } else {
                if ($requestConsulting->progress == RequestConsulting::PROGRESS_UNDER_AGREEMENT) {
                    $requestConsulting->update(['progress' => RequestConsulting::PROGRESS_WAITING_EVALUATION]);
                } else {
                    return false;
                }
            }

            DB::commit();

            return $requestConsulting->refresh();
        } catch (\Exception $e) {
            DB::rollBack();

            return false;
        }
    }
}
