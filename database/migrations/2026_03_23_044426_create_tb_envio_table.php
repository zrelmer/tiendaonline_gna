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
        Schema::create('tb_envio', function (Blueprint $table) {
            $table->increments('Id_Envio');
            $table->unsignedInteger('Id_Pedido')->unique('uq_tb_envio_Id_Pedido');
            $table->string('Direccion_Envio',200);
            $table->string('Empresa_Envio',200)->nullable(true);
            $table->string('Numero_Guia',200)->nullable(true);
            $table->timestamp('Fecha_Envio')->useCurrent();
            $table->timestamp('Fecha_Entrega')->nullable(true);
            $table->unsignedInteger('Id_Estatus');

            $table->foreign('Id_Pedido')->references('Id_Pedido')->on('tb_pedido');
            $table->foreign('Id_Estatus')->references('Id_Estatus')->on('tb_estatus');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tb_envio');
    }
};