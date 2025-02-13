<?php

namespace App\Repositories;

use App\Interfaces\Repositories\ITipoHabitacionRepository;
use App\Models\TipoHabitacion;

class TipoHabitacionRepository extends BaseRepository implements ITipoHabitacionRepository
{
    public function __construct(TipoHabitacion $model)
    {
        parent::__construct($model);
    }

    public function obtenerPorCodigo(string $codigo): ?TipoHabitacion
    {
        return $this->model->where('codigo', $codigo)->first();
    }
}
