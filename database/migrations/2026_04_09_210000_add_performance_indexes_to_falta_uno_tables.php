<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('falta_uno_games', function (Blueprint $table) {
            $table->index(['status', 'start_at'], 'falta_uno_games_status_start_at_index');
            $table->index('field_id', 'falta_uno_games_field_id_index');
        });

        Schema::table('falta_uno_participants', function (Blueprint $table) {
            $table->index(['game_id', 'user_id', 'status'], 'falta_uno_participants_game_user_status_index');
        });

        Schema::table('falta_uno_penalties', function (Blueprint $table) {
            $table->index(['user_id', 'expires_at'], 'falta_uno_penalties_user_expires_index');
        });

        Schema::table('falta_uno_ratings', function (Blueprint $table) {
            $table->index('rated_user_id', 'falta_uno_ratings_rated_user_id_index');
        });
    }

    public function down(): void
    {
        Schema::table('falta_uno_games', function (Blueprint $table) {
            $table->dropIndex('falta_uno_games_status_start_at_index');
            $table->dropIndex('falta_uno_games_field_id_index');
        });

        Schema::table('falta_uno_participants', function (Blueprint $table) {
            $table->dropIndex('falta_uno_participants_game_user_status_index');
        });

        Schema::table('falta_uno_penalties', function (Blueprint $table) {
            $table->dropIndex('falta_uno_penalties_user_expires_index');
        });

        Schema::table('falta_uno_ratings', function (Blueprint $table) {
            $table->dropIndex('falta_uno_ratings_rated_user_id_index');
        });
    }
};
