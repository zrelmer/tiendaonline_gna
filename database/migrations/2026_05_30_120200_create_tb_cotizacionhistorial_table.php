<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tb_cotizacionhistorial', function (Blueprint $table) {
            $table->increments('Id_CotizacionHistorial');
            $table->unsignedInteger('Id_Cotizacion');
            $table->unsignedInteger('Id_Estatus');
            $table->text('Comentario')->nullable();
            $table->timestamp('Fecha_Cambio')->useCurrent();

            $table->foreign('Id_Cotizacion')->references('Id_Cotizacion')->on('tb_cotizacion')->cascadeOnDelete();
            $table->foreign('Id_Estatus')->references('Id_Estatus')->on('tb_estatus');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tb_cotizacionhistorial');
    }
};
