<?php

namespace App\Interfaces\Repositories;

use App\Interfaces\Repositories\IBaseRepository;

interface IHabitacionRepository extends IBaseRepository
{
    public function borrarTodasPorHotel($hotelId);
    public function insertarVarias(array $habitaciones): void;
}
