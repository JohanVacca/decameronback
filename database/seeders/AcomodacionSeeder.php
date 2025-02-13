<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AcomodacionSeeder extends Seeder
{
    public function run()
    {
        DB::table('acomodaciones')->insert([
            ['codigo' => '1', 'descripcion' => 'SENCILLA'],
            ['codigo' => '2', 'descripcion' => 'DOBLE'],
            ['codigo' => '3', 'descripcion' => 'TRIPLE'],
            ['codigo' => '4', 'descripcion' => 'CUADRUPLE']
        ]);
    }
}
