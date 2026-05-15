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
        Schema::create('tb_pedido', function (Blueprint $table) {
            $table->increments('Id_Pedido');
            $table->unsignedInteger('Id_Usuario');
            $table->unsignedInteger('Id_Direccion');
            $table->string('Ped_Numero',200)->unique();
            $table->decimal('Ped_TotalPrecio', 10, 2);
            $table->unsignedInteger('Id_Estatus');
            // $table->timestamp('Created_At')->useCurrent();
            // $table->timestamp('Updated_At')->nullable(true)->useCurrent()->useCurrentOnUpdate();
            $table->timestamps();

            $table->foreign('Id_Usuario')->references('Id_Usuario')->on('tb_usuario');
            $table->foreign('Id_Direccion')->references('Id_Direccion')->on('tb_direccion');
            $table->foreign('Id_Estatus')->references('Id_Estatus')->on('tb_estatus');
            // $table->timestamps(); crea los campos created_at y updated_at, pero no los necesitamos en esta tabla
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tb_pedido');
    }
};
