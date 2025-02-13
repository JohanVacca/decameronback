<?php

namespace App\Providers;

use App\Interfaces\Services\IHotelService;
use Illuminate\Support\ServiceProvider;
use App\Services\hotelService;
use App\Services\PasarelaService;

class ServiceServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(IHotelService::class, hotelService::class);
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
