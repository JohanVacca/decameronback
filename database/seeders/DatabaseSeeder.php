<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            TipoHabitacionSeeder::class,
            AcomodacionSeeder::class,
            TipoHabitacionAcomodacionSeeder::class
            // HotelSeeder::class,
            // HabitacionSeeder::class,
        ]);
    }
}
