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
        Schema::create('tb_usuario', function (Blueprint $table) {
            $table->increments('Id_Usuario');
            $table->string('Usu_Nombre',200);
            $table->string('Usu_Correo',150)->unique();
            $table->string('Usu_Pass',255);
            $table->string('Usu_Telefono',20);
            $table->unsignedInteger('Id_Rol');
            $table->timestamps();

            $table->foreign('Id_Rol')->references('Id_Rol')->on('tb_rol');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tb_usuario');
    }
};