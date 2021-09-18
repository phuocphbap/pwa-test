<?php

namespace App\Services;

use App\Helpers\Facades\UploadS3Helper;
use App\Repositories\StoreArticleRepository;

class StoreArticleService
{
    protected $articleRepo;

    public function __construct(StoreArticleRepository $articleRepo)
    {
        $this->articleRepo = $articleRepo;
    }

    /**
     * upload multiple images articles
     */
    public function uploadMultipleImages($request, $id)
    {
        foreach ($request->file('file_name') as $image) {
            $imageLink = UploadS3Helper::uploadImage($image, 'store_articles');
            if ($imageLink) {
                $this->articleRepo->storeImages($imageLink, $id);
            } else {
                throw new \Exception("Upload file to S3 has error");
            }
        }

        return true;
    }

    /**
     * remove image articles by id
     */
    public function removeImageArticles($imageId)
    {
        $image = $this->articleRepo->getImageArticles($imageId);
        UploadS3Helper::deleteImage($image->img_path);
        $this->articleRepo->removeImageById($image->id);

        return true;
    }
}
