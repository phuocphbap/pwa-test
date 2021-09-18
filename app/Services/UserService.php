<?php

namespace App\Services;

use App\Helpers\Facades\UploadS3Helper;
use App\Repositories\IdentityCardRepository;
use App\Repositories\RequestConsultingRepository;
use App\Repositories\StoreRepository;

class UserService
{
    protected $storeRepo;
    protected $consultingRepo;
    protected $firebaseService;
    protected $IDRepo;

    public function __construct(
        StoreRepository $storeRepo,
        RequestConsultingRepository $consultingRepo,
        FirebaseService $firebaseService,
        IdentityCardRepository $IDRepo
    ) {
        $this->storeRepo = $storeRepo;
        $this->consultingRepo = $consultingRepo;
        $this->firebaseService = $firebaseService;
        $this->IDRepo = $IDRepo;
    }

    /**
     * update store when update profile user.
     */
    public function updateStoreByUser($data, $userId)
    {
        // remove image map old
        $store = $this->storeRepo->where('user_id', $userId)->first();
        UploadS3Helper::deleteImage($store->image_map);
        $this->storeRepo->update([
            'store_address' => $data['store_address'],
            'image_map' => null,
        ], $store->id);

        // store new image map with long & lat
        if (isset($data['latitude']) && isset($data['longitude'])) {
            $src = 'http://maps.google.com/maps/api/staticmap?center='.$data['latitude'].','.$data['longitude'].'&markers=color:red%7Clabel:C%7C'.$data['latitude'].','.$data['longitude'].'&zoom=16&size=600x400&key='.env('GOOGLE_CLOUD_KEY');
            $imageLink = UploadS3Helper::uploadImage($src, 'store');
            if ($imageLink) {
                $this->storeRepo->update([
                    'image_map' => $imageLink,
                    'latitude' => $data['latitude'],
                    'longitude' => $data['longitude'],
                ], $store->id);
            } else {
                throw new \Exception('UserService - updateStoreByUser: Upload file to S3 has error');
            }
        }

        return true;
    }

    /**
     * check Exists Progress.
     */
    public function checkExistsProgress($userId)
    {
        return $this->consultingRepo->checkExistsProgress($userId);
    }

    /**
     * handle block chat.
     */
    public function handleLeaveChat($userId, $status, $type)
    {
        return $this->firebaseService->handleLeaveChat($userId, $status, $type);
    }

    /**
     * check Identify Card.
     */
    public function checkIdentifyCard($userId)
    {
        return $this->IDRepo->checkIdentifyCard($userId);
    }

    /**
     * check Identify Card.
     */
    public function removeIdentityCard($userId)
    {
        return $this->IDRepo->removeIdentityCard($userId);
    }
}
