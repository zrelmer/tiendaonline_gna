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
        Schema::table('tb_pedido', function (Blueprint $table) {
            $table->boolean('Ped_OcultoAdmin')->default(false)->after('Id_Estatus');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tb_pedido', function (Blueprint $table) {
            $table->dropColumn('Ped_OcultoAdmin');
        });
    }
};
