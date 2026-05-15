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
        Schema::table('password_reset_tokens', function (Blueprint $table) {
            // Cambio: agrega columnas requeridas por Laravel si faltan en una DB ya migrada.
            if (!Schema::hasColumn('password_reset_tokens', 'email')) {
                $table->string('email')->nullable();
            }

            if (!Schema::hasColumn('password_reset_tokens', 'token')) {
                $table->string('token')->nullable();
            }

            if (!Schema::hasColumn('password_reset_tokens', 'created_at')) {
                $table->timestamp('created_at')->nullable();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('password_reset_tokens', function (Blueprint $table) {
            // Cambio: revierte columnas agregadas por esta migracion.
            if (Schema::hasColumn('password_reset_tokens', 'created_at')) {
                $table->dropColumn('created_at');
            }

            if (Schema::hasColumn('password_reset_tokens', 'token')) {
                $table->dropColumn('token');
            }

            if (Schema::hasColumn('password_reset_tokens', 'email')) {
                $table->dropColumn('email');
            }
        });
    }
};
