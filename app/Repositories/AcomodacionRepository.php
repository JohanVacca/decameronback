<?php

namespace App\Repositories;

use App\Interfaces\Repositories\IAcomodacionRepository;
use App\Models\Acomodacion;

class AcomodacionRepository extends BaseRepository implements IAcomodacionRepository
{
    public function __construct(Acomodacion $model)
    {
        parent::__construct($model);
    }

    public function obtenerPorCodigo(string $codigo): ?Acomodacion
    {
        return $this->model->where('codigo', $codigo)->first();
    }

    public function obtenerDescripcionesPorCodigos(array $codigos)
    {
        return $this->model->whereIn('codigo', $codigos)
            ->pluck('descripcion', 'codigo');
    }
}
