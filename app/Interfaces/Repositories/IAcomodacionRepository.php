<?php

namespace App\Interfaces\Repositories;

use App\Models\Acomodacion;

interface IAcomodacionRepository extends IBaseRepository
{
    public function obtenerPorCodigo(string $codigo): ?Acomodacion;
    public function obtenerDescripcionesPorCodigos(array $codigos);
}
