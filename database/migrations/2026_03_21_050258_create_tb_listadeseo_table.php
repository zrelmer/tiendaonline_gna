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
        Schema::create('tb_listadeseo', function (Blueprint $table) {
            $table->increments('Id_ListaDeseo');
            $table->unsignedInteger('Id_Usuario');
            $table->unsignedInteger('Id_Producto');
            // se usa unique para asegurar que un usuario no pueda agregar el mismo producto a su lista de deseos más de una vez
            $table->unique(['Id_Usuario', 'Id_Producto'], 'unique_usuario_producto');

            $table->foreign('Id_Usuario')->references('Id_Usuario')->on('tb_usuario');
            $table->foreign('Id_Producto')->references('Id_Producto')->on('tb_producto');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tb_listadeseo');
    }
};
