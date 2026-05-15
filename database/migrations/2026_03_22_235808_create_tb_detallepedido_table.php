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
        Schema::create('tb_detallepedido', function (Blueprint $table) {
            $table->increments('Id_DetallePedido');
            $table->unsignedInteger('Id_Pedido');
            $table->unsignedInteger('Id_Producto');
            $table->integer('DetaPed_Cantidad');
            $table->decimal('DetaPed_Precio', 10, 2);
            $table->decimal('DetaPed_SubTotal', 10, 2);
            $table->timestamps();
            // $table->timestamp('Created_At')->useCurrent();
            // $table->timestamp('Updated_At')->nullable(true)->useCurrent()->useCurrentOnUpdate();

            $table->foreign('Id_Pedido')->references('Id_Pedido')->on('tb_pedido');
            $table->foreign('Id_Producto')->references('Id_Producto')->on('tb_producto');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tb_detallepedido');
    }
};