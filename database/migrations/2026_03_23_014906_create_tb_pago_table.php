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
        Schema::create('tb_pago', function (Blueprint $table) {
            $table->increments('Id_Pago');
            $table->unsignedInteger('Id_Pedido')->unique('uq_tb_pago_Id_Pedido');
            $table->unsignedInteger('Id_MetodoPago');
            $table->string('Transaccion_Id',200)->nullable(true);
            $table->json('Transaccion_Json')->nullable(true);
            $table->unsignedInteger('Id_Estatus');
            // $table->timestamp('Created_At')->useCurrent();
            // $table->timestamp('Updated_At')->nullable(true)->useCurrent()->useCurrentOnUpdate();
            $table->timestamps();

            $table->foreign('Id_Pedido')->references('Id_Pedido')->on('tb_pedido');
            $table->foreign('Id_MetodoPago')->references('Id_MetodoPago')->on('tb_metodopago');
            $table->foreign('Id_Estatus')->references('Id_Estatus')->on('tb_estatus');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tb_pago');
    }
};
