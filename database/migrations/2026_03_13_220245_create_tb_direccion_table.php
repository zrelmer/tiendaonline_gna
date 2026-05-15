<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('tb_direccion', function (Blueprint $table) {
            $table->increments('Id_Direccion');
            $table->unsignedInteger('Id_Usuario');
            $table->string('Direccion',200);
            $table->unsignedInteger('Id_Municipio');

            $table->foreign('Id_Usuario')->references('Id_Usuario')->on('tb_usuario');
            $table->foreign('Id_Municipio')->references('Id_Municipio')->on('tb_municipio');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tb_direccion');
    }
};
