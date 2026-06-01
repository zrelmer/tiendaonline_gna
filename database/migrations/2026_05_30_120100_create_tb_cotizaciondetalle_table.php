<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tb_cotizaciondetalle', function (Blueprint $table) {
            $table->increments('Id_CotizacionDetalle');
            $table->unsignedInteger('Id_Cotizacion');
            $table->unsignedInteger('Id_Producto')->nullable();
            $table->unsignedInteger('Cantidad');
            $table->text('Descripcion');
            $table->decimal('Costo_Unit', 12, 2)->default(0);
            $table->decimal('Subtotal', 12, 2)->default(0);

            $table->foreign('Id_Cotizacion')->references('Id_Cotizacion')->on('tb_cotizacion')->cascadeOnDelete();
            $table->foreign('Id_Producto')->references('Id_Producto')->on('tb_producto')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tb_cotizaciondetalle');
    }
};
