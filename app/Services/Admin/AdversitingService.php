<?php

namespace App\Services\Admin;

use App\Constant\StatusConstant;
use App\Repositories\AdvertisingBlockRepository;
use App\Repositories\AdvertisingMediaRepository;
use App\Repositories\AdvertisingCategoryRepository;

class AdversitingService
{
    protected $adsMediaRepo;
    protected $adsBlockRepo;
    protected $adsCategoryRepo;

    /**
     * constructor.
     */
    public function __construct(
        AdvertisingMediaRepository $adsMediaRepo,
        AdvertisingBlockRepository $adsBlockRepo,
        AdvertisingCategoryRepository $adsCategoryRepo
    )
    {
        $this->adsMediaRepo = $adsMediaRepo;
        $this->adsBlockRepo = $adsBlockRepo;
        $this->adsCategoryRepo = $adsCategoryRepo;
    }

    /**
     * get category advertising
     */
    public function getListCategoryAds()
    {
        return $this->adsCategoryRepo->getListCategoryAds();
    }

    /**
     * get list block
     */
    public function getListBlockAds($categoryId)
    {
        return $this->adsBlockRepo->getListBlockAdsByCategory($categoryId);
    }

    /**
     * udpate content block
     */
    public function updateContentBlock($blockId, $contents)
    {
        $this->adsBlockRepo->updateContentBlock($blockId, $contents);
    }

    /**
     * get advertising
     */
    public function getAdvertising($type)
    {
        switch ($type) {
            case StatusConstant::ADVERTISING_TYPE_SERVICE:
                return $this->getAdvertisingByCategoryName(StatusConstant::ADVERTISING_NAME_SERVICE);
                break;
            case StatusConstant::ADVERTISING_TYPE_SUGGEST:
                return $this->getAdvertisingByCategoryName(StatusConstant::ADVERTISING_NAME_SUGGEST);
                break;
            case StatusConstant::ADVERTISING_TYPE_STORE:
                return $this->getAdvertisingByCategoryName(StatusConstant::ADVERTISING_NAME_STORE);
                break;
            case StatusConstant::ADVERTISING_TYPE_SPOT:
                return $this->getAdvertisingByCategoryName(StatusConstant::ADVERTISING_NAME_SPOT);
                break;
            default:
                break;
        }
    }

    /**
     *
     */
    public function getAdvertisingByCategoryName($name)
    {
        return $this->adsBlockRepo->getListAdvertising($name);
    }
}
