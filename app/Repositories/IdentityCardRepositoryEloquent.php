<?php

namespace App\Repositories;

use App\Entities\IdentityCard;
use App\Helpers\Facades\UploadS3Helper;
use App\Repositories\IdentityCardRepository;
use Prettus\Repository\Eloquent\BaseRepository;
use Prettus\Repository\Criteria\RequestCriteria;

/**
 * Class FeePaymentRepositoryEloquent.
 */
class IdentityCardRepositoryEloquent extends BaseRepository implements IdentityCardRepository
{
    /**
     * Specify Model class name.
     *
     * @return string
     */
    public function model()
    {
        return IdentityCard::class;
    }

    /**
     * Boot up the repository, pushing criteria.
     */
    public function boot()
    {
        $this->pushCriteria(app(RequestCriteria::class));
    }

    /**
     * get detail
     */
    public function getDetailIDByUserId($userId)
    {
        return $this->model->where('user_id', $userId)
                        ->with('user:id,identity_status')
                        ->first();
    }

    /**
     * check has data
     */
    public function checkIdentifyCard($userId)
    {
        return $this->model->where('user_id', $userId)->exists();
    }

    public function removeIdentityCard($userId)
    {
        $data = $this->model->where('user_id', $userId)->first();
        foreach ($data->images as $value) {
            UploadS3Helper::deleteImage($value);
        }
        $this->model->where('id', $data->id)->delete();

        return true;
    }
}
