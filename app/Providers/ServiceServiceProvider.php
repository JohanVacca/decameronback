<?php

namespace App\Providers;

use App\Interfaces\Services\IHotelService;
use Illuminate\Support\ServiceProvider;
use App\Services\HotelService;

class ServiceServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(IHotelService::class, HotelService::class);
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
