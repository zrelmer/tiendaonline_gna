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
        Schema::create('tb_carritodetalle', function (Blueprint $table) {
            $table->increments('Id_CarritoDetalle');
            $table->unsignedInteger('Id_Carrito');
            $table->unsignedInteger('Id_Producto');
            $table->integer('Cantidad');
            $table->decimal('Precio', 10, 2);
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->nullable(true)->useCurrent()->useCurrentOnUpdate();

            $table->unique(['Id_Carrito', 'Id_Producto'], 'carrito_producto_unique');

            $table->foreign('Id_Carrito')->references('Id_Carrito')->on('tb_carrito');
            $table->foreign('Id_Producto')->references('Id_Producto')->on('tb_producto');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tb_carritodetalle');
    }
};
