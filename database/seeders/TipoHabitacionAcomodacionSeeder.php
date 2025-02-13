<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TipoHabitacionAcomodacionSeeder extends Seeder
{
    public function run()
    {
        DB::table('tipo_habitacion_acomodacion')->insert([
            // ESTANDAR (1) solo permite SENCILLA (1) o DOBLE (2)
            ['tipoHabitacionCodigo' => '1', 'tipoAcomodacionCodigo' => '1'],
            ['tipoHabitacionCodigo' => '1', 'tipoAcomodacionCodigo' => '2'],

            // JUNIOR (2) solo permite TRIPLE (3) o CUADRUPLE (4)
            ['tipoHabitacionCodigo' => '2', 'tipoAcomodacionCodigo' => '3'],
            ['tipoHabitacionCodigo' => '2', 'tipoAcomodacionCodigo' => '4'],

            // SUITE (3) permite SENCILLA (1), DOBLE (2) o TRIPLE (3)
            ['tipoHabitacionCodigo' => '3', 'tipoAcomodacionCodigo' => '1'],
            ['tipoHabitacionCodigo' => '3', 'tipoAcomodacionCodigo' => '2'],
            ['tipoHabitacionCodigo' => '3', 'tipoAcomodacionCodigo' => '3']
        ]);
    }
}
