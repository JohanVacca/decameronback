<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('hoteles', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');
            $table->string('direccion');
            $table->string('ciudad');
            $table->string('nit')->unique();
            $table->integer('numeroHabitaciones');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('hoteles');
    }
};
