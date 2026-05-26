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
        Schema::create('tb_boletapago', function (Blueprint $table) {
            $table->increments('Id_Boletapago');
            $table->string('BoletaImagen', 255);
            $table->unsignedInteger('Id_Pedido');
            $table->timestamps();

            $table->foreign('Id_Pedido')->references('Id_Pedido')->on('tb_pedido');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tb_boletapago');
    }
};
