<?php

namespace App\Repositories;

use App\Interfaces\Repositories\IHabitacionRepository;
use App\Models\Habitacion;

class HabitacionRepository extends BaseRepository implements IHabitacionRepository
{
    public function __construct(Habitacion $model)
    {
        parent::__construct($model);
    }

    public function borrarTodasPorHotel($hotelId)
    {
        return $this->model::where('hotelId', $hotelId)->delete();
    }

    public function insertarVarias(array $habitaciones): void
    {
        $this->model::insert($habitaciones);
    }
}
