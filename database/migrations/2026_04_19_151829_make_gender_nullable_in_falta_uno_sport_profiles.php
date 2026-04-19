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
        // Usamos raw SQL para Postgres (la enum column no se altera con Schema::change fácilmente)
        \Illuminate\Support\Facades\DB::statement('ALTER TABLE falta_uno_sport_profiles ALTER COLUMN gender DROP NOT NULL');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revertir: primero setear un default en las filas null, luego aplicar NOT NULL
        \Illuminate\Support\Facades\DB::statement("UPDATE falta_uno_sport_profiles SET gender = 'male' WHERE gender IS NULL");
        \Illuminate\Support\Facades\DB::statement('ALTER TABLE falta_uno_sport_profiles ALTER COLUMN gender SET NOT NULL');
    }
};
