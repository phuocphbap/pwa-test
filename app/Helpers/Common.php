<?php

use Carbon\Carbon;
use App\Entities\Region;
use App\Entities\Category;
use App\Entities\ReferralBonus;
use App\Helpers\Facades\UploadS3Helper;

function newCond($array)
{
    $item = new \App\Helpers\Condition($array);

    return $item;
}

function formatDate($date)
{
    return Carbon::parse($date)->format('d-m-Y');
}

/**
 * get all categories by level.
 */
function getAllCategoriesByLevels($parent_id = 0, $state = 1)
{
    return Category::with('children')
    ->when(is_numeric($state), function ($q) use ($state) {
        $q->where('state', $state);
    })
    ->when(is_numeric($parent_id), function ($q) use ($parent_id) {
        $q->where('parent_id', $parent_id);
    })
    ->orderBy('cat_sort')
    ->get();
}

/**
 * get all categories by level.
 */
function getCategoryByChild($category_id = 0, $state = 1)
{
    return Category::with('parent')
    ->when(is_numeric($state), function ($q) use ($state) {
        $q->where('state', $state);
    })
    ->when(is_numeric($category_id), function ($q) use ($category_id) {
        $q->where('id', $category_id);
    })
    ->orderBy('cat_sort')
    ->first();
}
/*
* get all regions
*/
function getRegions()
{
    $regions = Region::get();

    return $regions;
}

/**
 * function upload image to S3
 *
 * @param file $fileName
 * @param string $nameFolder
 * @return string|null
 */
if (!function_exists('uploadFileToS3')) {
    function uploadFileToS3($fileName, $nameFolder) {
        $imageLink = UploadS3Helper::uploadImage($fileName, $nameFolder);
        if ($imageLink) {
            return $imageLink;
        }

        throw new \Exception("Upload file to S3 has error");
    }
}

/**
 * function upload file PDF to S3
 *
 * @param file $fileName
 * @param string $nameFolder
 * @return string|null
 */
if (!function_exists('uploadPDFToS3')) {
    function uploadPDFToS3($fileName, $nameFolder) {
        $imageLink = UploadS3Helper::uploadPDF($fileName, $nameFolder);
        if ($imageLink) {
            return $imageLink;
        }

        throw new \Exception("Upload file PDF to S3 has error");
    }
}

/**
 * function remove image S3
 *
 * @param string $imagePath
 * @return bool
 */
if (!function_exists('removeFileS3')) {
    function removeFileS3($imagePath) {
        UploadS3Helper::deleteImage($imagePath);

        return true;
    }
}

/**
 * function get Referral Bonus
 * @return string|null
 */
if (!function_exists('getReferralBonus')) {
    function getReferralBonus() {
        return ReferralBonus::whereNull('deleted_at')->value('amount');
    }
}


