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
        Schema::create('tb_inventario', function (Blueprint $table) {
            $table->increments('Id_Inventario');
            $table->unsignedInteger('Id_Producto')->unique();
            $table->integer('Stock');
            $table->integer('Stock_Reservado');
            $table->integer('Stock_Disponible')->nullable(true)->storedAs('Stock - Stock_Reservado');
            $table->timestamp('Ultima_Actualizacion')->nullable(true)->useCurrent()->useCurrentOnUpdate();

            $table->foreign('Id_Producto')->references('Id_Producto')->on('tb_producto');

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tb_inventario');

    }
};
