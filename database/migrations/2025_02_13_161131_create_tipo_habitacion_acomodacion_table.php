<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('tipo_habitacion_acomodacion', function (Blueprint $table) {
            $table->id();
            $table->string('tipoHabitacionCodigo');
            $table->string('tipoAcomodacionCodigo');
            $table->timestamps();

            $table->foreign('tipoHabitacionCodigo')->references('codigo')->on('tipos_habitacion')->onDelete('cascade');
            $table->foreign('tipoAcomodacionCodigo')->references('codigo')->on('acomodaciones')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('tipo_habitacion_acomodacion');
    }
};
