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
        Schema::create('tb_inventariohistorial', function (Blueprint $table) {
            $table->increments('Id_InventarioHistorial');
            $table->unsignedInteger('Id_Inventario');
            $table->unsignedInteger('Id_Movimiento');
            $table->integer('Cantidad');
            $table->integer('Stock_Antes');
            $table->integer('Stock_Despues');
            $table->string('Referencia',200)->nullable(true);
            $table->timestamp('Fecha_Movimiento')->useCurrent();

            $table->foreign('Id_Inventario')->references('Id_Inventario')->on('tb_inventario');
            $table->foreign('Id_Movimiento')->references('Id_Movimiento')->on('tb_movimiento');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tb_inventariohistorial');
    }
};