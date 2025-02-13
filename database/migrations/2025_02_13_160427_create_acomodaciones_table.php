<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('acomodaciones', function (Blueprint $table) {
            $table->string('codigo')->primary();
            $table->string('descripcion');
        });
    }

    public function down()
    {
        Schema::dropIfExists('acomodaciones');
    }
};
