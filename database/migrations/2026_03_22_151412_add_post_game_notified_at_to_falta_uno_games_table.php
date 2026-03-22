<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('falta_uno_games', function (Blueprint $table) {
            $table->timestamp('post_game_notified_at')->nullable()->after('reminder_sent_at');
        });
    }

    public function down(): void
    {
        Schema::table('falta_uno_games', function (Blueprint $table) {
            $table->dropColumn('post_game_notified_at');
        });
    }
};
