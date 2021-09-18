<?php

namespace App\Helpers\Facades;

use Illuminate\Support\Facades\Facade;

class UploadHelper extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'UploadHelperConfig';
    }
}
