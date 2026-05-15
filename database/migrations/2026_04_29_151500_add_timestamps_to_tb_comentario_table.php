<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('tb_comentario', function (Blueprint $table) {
            // Cambio: agrega fechas para mostrar cuándo se creó/actualizó la reseña.
            if (!Schema::hasColumn('tb_comentario', 'created_at')) {
                $table->timestamp('created_at')->nullable()->after('Comentario');
            }

            if (!Schema::hasColumn('tb_comentario', 'updated_at')) {
                $table->timestamp('updated_at')->nullable()->after('created_at');
            }
        });

        // Cambio: rellena fecha en comentarios existentes para evitar valores nulos visibles.
        DB::table('tb_comentario')
            ->whereNull('created_at')
            ->update([
                'created_at' => now(),
                'updated_at' => now(),
            ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tb_comentario', function (Blueprint $table) {
            if (Schema::hasColumn('tb_comentario', 'updated_at')) {
                $table->dropColumn('updated_at');
            }

            if (Schema::hasColumn('tb_comentario', 'created_at')) {
                $table->dropColumn('created_at');
            }
        });
    }
};
