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
        Schema::create('tb_marca', function (Blueprint $table) {
            $table->increments('Id_Marca');
            $table->string('Nom_Marca',200);
            $table->string('slug_Marca',200)->unique();
            $table->text('Descrip_Marca');
            $table->timestamps();

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tb_marca');
    }
};
