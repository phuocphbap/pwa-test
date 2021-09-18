<?php

namespace App\Providers;

use App\Helpers\UploadHelperConfig;
use App\Helpers\UploadS3Config;
use App\Support\TokenCrypt;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton('crypt', TokenCrypt::class);
        $this->app->singleton('UploadHelperConfig', function ($app) {
            return new UploadHelperConfig();
        });
        $this->app->singleton('UploadS3Config', function ($app) {
            return new UploadS3Config();
        });
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
    }
}
