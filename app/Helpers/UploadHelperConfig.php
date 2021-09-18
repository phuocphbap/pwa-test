<?php

namespace App\Helpers;

use Illuminate\Http\UploadedFile;
use Storage;

class UploadHelperConfig
{
    private $storageImage;

    /**
     * UploadHelper constructor.
     */
    public function __construct()
    {
        $this->storageImage = Storage::disk('public');
    }

    /**
     * Get Config.
     *
     * @param string $imgType Type
     *
     * @return array
     */
    public function getConfig($imgType)
    {
        $configs = config("image.$imgType.type");

        return $configs;
    }

    /**
     * @param UploadedFile $image   Image
     * @param string       $imgType Image type
     * @param string       $oldName Old name
     *
     * @return array
     */
    public function updateImage($image, $imgType, $oldName = '', $prefix = 'storage/')
    {
        try {
            $name = microtime(true).'.png';
            $configs = $this->getConfig($imgType);
            $imageFile = \Image::make($image)->encode('png', 65)->orientate();
            foreach ($configs as $key => $config) {
                if ($key == 'original') {
                    $this->storageImage->put($configs[$key]['path'].$name, $imageFile->stream()->__toString());
                } else {
                    $imageFile = \Image::make($image)->encode('png', 65)->orientate();
                    $this->formatImage($imageFile, $configs[$key]['width'], null);
                    $this->storageImage->put($configs[$key]['path'].$name, $imageFile->stream()->__toString());
                }
            }
            if (!empty($oldName) && $oldName !== 'default.png') { //default.jpg can't delete
                $paths = array_keys($configs);
                array_walk($paths, function (&$value, $key) use ($imgType, $oldName) {
                    $value = $imgType.'/'.$value.'/'.$oldName;
                });
                $this->deleteImage($paths);
            }

            return $name;
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }

    public function uploadFile($file, $folder)
    {
        $name = $file->getClientOriginalName();
        // $onlyName = explode('.', $name)[0];
        $onlyName = microtime(true);

        $extension = $file->getClientOriginalExtension();
        if ($this->storageImage->exists("$folder/$name")) {
            $i = 1;
            while ($this->storageImage->exists("$folder/$name")) {
                $name = $onlyName.'('.$i.').'.$extension;
                ++$i;
            }
            $this->storageImage->putFileAs("$folder", $file, $name);
        } else {
            $this->storageImage->putFileAs("$folder", $file, $name);
        }

        return $name;
    }

    public function deleteFile($file, $folder)
    {
        $path = "$folder/$file";

        $exists = $this->storageImage->exists($path);
        if ($exists) {
            Storage::delete("public/$path");
            // $this->storageImage->delete($path);
        }

        return true;
    }

    public function existsFile($file, $folder)
    {
        $path = "$folder/$file";

        return  $this->storageImage->exists($path);
    }

    public function getFile($folder, $name)
    {
        $path = null;
        if ($name) {
            $value = $folder.'/'.$name;
            $path = $this->storageImage->url($value);
        }

        return $path;
    }
}
