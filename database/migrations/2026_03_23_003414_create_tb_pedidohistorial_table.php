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
        Schema::create('tb_pedidohistorial', function (Blueprint $table) {
            $table->increments('Id_PedidoHistorial');
            $table->unsignedInteger('Id_Pedido');
            $table->unsignedInteger('Id_Estatus');
            $table->text('Comentario')->nullable(true);
            $table->timestamp('Fecha_Cambio')->useCurrent();

            $table->foreign('Id_Pedido')->references('Id_Pedido')->on('tb_pedido');
            $table->foreign('Id_Estatus')->references('Id_Estatus')->on('tb_estatus');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tb_pedidohistorial');
    }
};