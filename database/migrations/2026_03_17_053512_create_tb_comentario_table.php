<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('tb_comentario', function (Blueprint $table) {
            $table->increments('Id_Comentario');
            $table->unsignedInteger('Id_Usuario');
            $table->unsignedInteger('Id_Producto');
            $table->tinyInteger('Rating');
            $table->text('Comentario');


            $table->foreign('Id_Usuario')->references('Id_Usuario')->on('tb_usuario');
            $table->foreign('Id_Producto')->references('Id_Producto')->on('tb_producto');
        });

        DB::statement('ALTER TABLE tb_comentario ADD CONSTRAINT chk_rating CHECK (Rating BETWEEN 1 AND 5)');
    }

    /**DB::statement('ALTER TABLE tb_comentario ADD CONSTRAINT chk_rating CHECK (Rating BETWEEN 1 AND 5)');
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tb_comentario');
    }
};
