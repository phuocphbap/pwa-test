<?php

namespace App\Helpers\Facades;

use Illuminate\Support\Facades\Facade;

class UploadS3Helper extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'UploadS3Config';
    }
}
