<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('habitaciones', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('hotelId');
            $table->string('tipoHabitacionCodigo');
            $table->string('tipoAcomodacionCodigo');
            $table->string('infoAdicional')->default('Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nullam nec purus nec sapien.');

            $table->foreign('hotelId')->references('id')->on('hoteles')->onDelete('cascade');
            $table->foreign('tipoHabitacionCodigo')->references('codigo')->on('tipos_habitacion');
            $table->foreign('tipoAcomodacionCodigo')->references('codigo')->on('acomodaciones');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('habitaciones');
    }
};
