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
        Schema::create('tb_prodimagen', function (Blueprint $table) {
            $table->increments('Id_ProdImagen');
            $table->unsignedInteger('Id_Producto');
            $table->string('url', 255);
            $table->integer('orden')->default(0);
            $table->foreign('Id_Producto')->references('Id_Producto')->on('tb_producto');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tb_prodimagen');
    }
};
