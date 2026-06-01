<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tb_cotizacion', function (Blueprint $table) {
            $table->increments('Id_Cotizacion');
            $table->unsignedInteger('Id_Usuario');
            $table->string('Cot_Numero', 50)->unique();
            $table->string('Cot_NombreCliente', 200);
            $table->string('Cot_Nit', 50)->nullable();
            $table->string('Cot_Direccion', 300)->nullable();
            $table->string('Cot_Email', 150)->nullable();
            $table->text('Cot_NotasSolicitud')->nullable();
            $table->decimal('Cot_Subtotal', 12, 2)->default(0);
            $table->decimal('Cot_Total', 12, 2)->default(0);
            $table->unsignedSmallInteger('Cot_VigenciaDias')->default(10);
            $table->text('Cot_Terminos')->nullable();
            $table->timestamp('Cot_FechaEmision')->nullable();
            $table->string('Cot_Archivo', 500)->nullable();
            $table->unsignedInteger('Id_Estatus');
            $table->timestamps();

            $table->foreign('Id_Usuario')->references('Id_Usuario')->on('tb_usuario');
            $table->foreign('Id_Estatus')->references('Id_Estatus')->on('tb_estatus');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tb_cotizacion');
    }
};
