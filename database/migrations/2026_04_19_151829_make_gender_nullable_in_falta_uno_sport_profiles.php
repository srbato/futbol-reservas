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
        // Postgres: ALTER COLUMN raw. SQLite (testing): no-op porque el create_table inicial
        // ya define gender nullable o no existe la tabla todavía.
        if (\Illuminate\Support\Facades\DB::getDriverName() === 'pgsql') {
            \Illuminate\Support\Facades\DB::statement('ALTER TABLE falta_uno_sport_profiles ALTER COLUMN gender DROP NOT NULL');
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (\Illuminate\Support\Facades\DB::getDriverName() === 'pgsql') {
            \Illuminate\Support\Facades\DB::statement("UPDATE falta_uno_sport_profiles SET gender = 'male' WHERE gender IS NULL");
            \Illuminate\Support\Facades\DB::statement('ALTER TABLE falta_uno_sport_profiles ALTER COLUMN gender SET NOT NULL');
        }
    }
};
