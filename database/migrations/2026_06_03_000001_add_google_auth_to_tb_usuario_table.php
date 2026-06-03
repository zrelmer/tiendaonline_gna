<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tb_usuario', function (Blueprint $table) {
            $table->string('google_id', 255)->nullable()->unique()->after('Usu_Correo');
            $table->string('Usu_Pass', 255)->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('tb_usuario', function (Blueprint $table) {
            $table->dropColumn('google_id');
            $table->string('Usu_Pass', 255)->nullable(false)->change();
        });
    }
};
