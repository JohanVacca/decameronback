<?php

namespace App\Interfaces\Repositories;

interface IHotelRepository extends IBaseRepository
{
    public function guardarConHabitaciones(array $data, bool $actualizar = false);
}
