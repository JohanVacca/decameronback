<?php

namespace App\Interfaces\Services;

interface IHotelService
{
    public function crearHotel(array $body);
    public function obtenerHotel($id);
    public function obtenerHoteles();
    public function actualizarHotel(array $body, $id);
    public function eliminarHotel($id);
}
