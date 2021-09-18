<?php

namespace App\Helpers;

use Storage;

class UploadS3Config
{
    private $storageImage;
    public $bucketUrl = 'https://pwa-dev-storage.s3.ap-northeast-1.amazonaws.com/';

    /**
     * UploadHelper constructor.
     */
    public function __construct()
    {
        $this->storageImage = Storage::disk('s3');
    }

    public function getConfig($imgType)
    {
        $configs = config("image.$imgType.type");

        return $configs;
    }

    public function uploadImage($image, $folder)
    {
        $status = false;
        $name = microtime(true).'.png';
        $configs = $this->getConfig($folder);
        // $imageFile = \Image::make($image)->encode('png', 65)->orientate();
        foreach ($configs as $key => $config) {
            if ($key == 'original') {
                // $this->storageImage->put($configs[$key]['path'].$name, $imageFile->stream()->__toString(), 'public');
                $this->storageImage->put($configs[$key]['path'].$name, file_get_contents($image), 'public');

                return $this->bucketUrl.$folder.'/'.$key.'/'.$name;
            } else {
                //resize iamge
                // $this->storageImage->put($configs[$key]['path'].$name, $imageFile->stream()->__toString(), 'public');
                $this->storageImage->put($configs[$key]['path'].$name, file_get_contents($image), 'public');

                return $this->bucketUrl.$folder.'/'.$key.'/'.$name;
            }
        }
    }

    public function deleteImage($imageLink)
    {
        if ($imageLink) {
            $folderPath = $this->getFolderLink($imageLink);
            if ($this->storageImage->exists($folderPath)) {
                return $this->storageImage->delete($folderPath);
            }
        }

        return true;
    }

    public function getFolderLink($imageLink)
    {
        return str_replace($this->bucketUrl, '', $imageLink);
    }

    /**
     * upload file pdf to s3
     */
    public function uploadPDF($file, $folder)
    {
        $name = microtime(true).'.pdf';
        $configs = $this->getConfig($folder);

        foreach ($configs as $key => $config) {
            if ($key == 'original') {
                $this->storageImage->put($configs[$key]['path'].$name, file_get_contents($file), 'public');

                return $this->bucketUrl.$folder.'/'.$key.'/'.$name;
            } else {
                //resize iamge
                $this->storageImage->put($configs[$key]['path'].$name, file_get_contents($file), 'public');

                return $this->bucketUrl.$folder.'/'.$key.'/'.$name;
            }
        }
    }
}
