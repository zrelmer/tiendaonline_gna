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
        Schema::create('tb_municipio', function (Blueprint $table) {
            $table->increments('Id_Municipio');
            $table->string('Nom_Municipio',200);
            $table->unsignedInteger('Id_Departamento');

            $table->foreign('Id_Departamento')->references('Id_departamento')->on('tb_Departamento');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tb_municipio');
    }
};