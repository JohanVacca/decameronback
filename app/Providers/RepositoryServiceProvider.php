<?php

namespace App\Providers;

use App\Interfaces\Repositories\IAcomodacionRepository;
use App\Interfaces\Repositories\IHabitacionRepository;
use App\Interfaces\Repositories\IHotelRepository;
use App\Interfaces\Repositories\ITipoHabitacionRepository;
use App\Repositories\AcomodacionRepository;
use App\Repositories\HabitacionRepository;
use Illuminate\Support\ServiceProvider;
use App\Repositories\HotelRepository;
use App\Repositories\TipoHabitacionRepository;

class RepositoryServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(IHotelRepository::class, HotelRepository::class);
        $this->app->bind(IHabitacionRepository::class, HabitacionRepository::class);
        $this->app->bind(IAcomodacionRepository::class, AcomodacionRepository::class);
        $this->app->bind(ITipoHabitacionRepository::class, TipoHabitacionRepository::class);
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
