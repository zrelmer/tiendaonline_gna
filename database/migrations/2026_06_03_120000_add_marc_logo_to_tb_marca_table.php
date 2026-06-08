<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tb_marca', function (Blueprint $table) {
            $table->string('Marc_Logo', 500)->nullable()->after('Descrip_Marca');
        });
    }

    public function down(): void
    {
        Schema::table('tb_marca', function (Blueprint $table) {
            $table->dropColumn('Marc_Logo');
        });
    }
};
