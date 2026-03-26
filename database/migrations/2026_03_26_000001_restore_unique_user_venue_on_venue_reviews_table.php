<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Eliminar reseñas duplicadas dejando solo la más antigua por usuario+complejo
        DB::statement('
            DELETE FROM venue_reviews
            WHERE id NOT IN (
                SELECT MIN(id)
                FROM venue_reviews
                GROUP BY venue_id, user_id
            )
        ');

        Schema::table('venue_reviews', function (Blueprint $table) {
            $table->unique(['venue_id', 'user_id']);
        });
    }

    public function down(): void
    {
        Schema::table('venue_reviews', function (Blueprint $table) {
            $table->dropUnique(['venue_id', 'user_id']);
        });
    }
};
