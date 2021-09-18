<?php

namespace App\Services;

use App\Helpers\Facades\UploadS3Helper;
use App\Repositories\StoreIntroductionRepository;

class StoreIntroductionService
{
    protected $storeIntroRepo;

    public function __construct(StoreIntroductionRepository $storeIntroRepo)
    {
        $this->storeIntroRepo = $storeIntroRepo;
    }

    /**
     * save image store introduction
     */
    public function uploadFile($fileName, $namefolder)
    {
        $imageLink = UploadS3Helper::uploadImage($fileName, $namefolder);
        if ($imageLink) {
            return $imageLink;
        }

        throw new \Exception("Upload file to S3 has error");
    }

    /**
     *
     */
    public function removeFile($imagePath)
    {
        UploadS3Helper::deleteImage($imagePath);

        return true;
    }
}
