<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Hotel;

class HotelSeeder extends Seeder
{
    public function run()
    {
        Hotel::insert([
            [
                'nombre' => 'Decameron Cartagena',
                'direccion' => 'Calle 23 58-25',
                'ciudad' => 'Cartagena',
                'nit' => '12345678-9',
                'numeroHabitaciones' => 42
            ],
            [
                'nombre' => 'Decameron San Andrés',
                'direccion' => 'Av. Las Américas 12-34',
                'ciudad' => 'San Andrés',
                'nit' => '87654321-0',
                'numeroHabitaciones' => 55
            ]
        ]);
    }
}
