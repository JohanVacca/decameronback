<?php

namespace App\Interfaces\Repositories;

use App\Models\TipoHabitacion;

interface ITipoHabitacionRepository extends IBaseRepository
{
    public function obtenerPorCodigo(string $codigo): ?TipoHabitacion;
    public function obtenerDescripcionesPorCodigos(array $codigos);
}
