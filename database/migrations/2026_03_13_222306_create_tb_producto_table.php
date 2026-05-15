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
        Schema::create('tb_producto', function (Blueprint $table) {
            $table->increments('Id_Producto');
            $table->unsignedInteger('Id_Categoria');
            $table->unsignedInteger('Id_Marca');
            $table->string('Prod_Nombre',200);
            $table->string('Prod_Slug',200)->unique();
            $table->text('Prod_Descripcion');
            $table->decimal('Prod_Precio',10,2);
            $table->decimal('Prod_PrecioOferta',10,2)->nullable();
            $table->unsignedInteger('Id_Estatus');
            $table->boolean('Prod_Activo')->default(true);
            $table->timestamps();

            $table->foreign('Id_Categoria')->references('Id_Categoria')->on('tb_categoria');
            $table->foreign('Id_Marca')->references('Id_Marca')->on('tb_marca');
            $table->foreign('Id_Estatus')->references('Id_Estatus')->on('tb_estatus');


        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tb_producto');
    }
};
