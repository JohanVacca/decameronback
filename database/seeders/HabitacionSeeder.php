<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Habitacion;
use App\Models\Hotel;
use App\Models\TipoHabitacion;
use App\Models\Acomodacion;

class HabitacionSeeder extends Seeder
{
    public function run()
    {
        $hotel = Hotel::first();

        if (!$hotel) {
            return;
        }

        $habitaciones = [
            ['tipoHabitacionCodigo' => 'ESTANDAR', 'tipoAcomodacionCodigo' => 'SENCILLA'],
            ['tipoHabitacionCodigo' => 'ESTANDAR', 'tipoAcomodacionCodigo' => 'DOBLE'],
            ['tipoHabitacionCodigo' => 'JUNIOR', 'tipoAcomodacionCodigo' => 'TRIPLE'],
            ['tipoHabitacionCodigo' => 'JUNIOR', 'tipoAcomodacionCodigo' => 'CUADRUPLE'],
            ['tipoHabitacionCodigo' => 'SUITE', 'tipoAcomodacionCodigo' => 'SENCILLA'],
        ];

        foreach ($habitaciones as $habitacion) {
            Habitacion::create([
                'hotelId' => $hotel->id,
                'tipoHabitacionCodigo' => $habitacion['tipoHabitacionCodigo'],
                'tipoAcomodacionCodigo' => $habitacion['tipoAcomodacionCodigo'],
            ]);
        }
    }
}
