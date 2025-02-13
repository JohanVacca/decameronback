<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TipoHabitacionSeeder extends Seeder
{
    public function run()
    {
        DB::table('tipos_habitacion')->insert([
            ['codigo' => '1', 'descripcion' => 'ESTANDAR'],
            ['codigo' => '2', 'descripcion' => 'JUNIOR'],
            ['codigo' => '3', 'descripcion' => 'SUITE']
        ]);
    }
}
