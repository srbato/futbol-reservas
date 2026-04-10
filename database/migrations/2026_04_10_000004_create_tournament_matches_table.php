<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tournament_matches', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tournament_id')->constrained()->cascadeOnDelete();
            $table->unsignedInteger('round');
            $table->string('round_name')->nullable();
            $table->unsignedInteger('match_number');
            $table->foreignId('home_team_id')->nullable()->constrained('tournament_teams')->nullOnDelete();
            $table->foreignId('away_team_id')->nullable()->constrained('tournament_teams')->nullOnDelete();
            $table->string('venue_name_override')->nullable();
            $table->timestamp('scheduled_at')->nullable();
            $table->string('status')->default('scheduled');
            $table->unsignedInteger('home_score')->nullable();
            $table->unsignedInteger('away_score')->nullable();
            $table->unsignedInteger('home_penalties')->nullable();
            $table->unsignedInteger('away_penalties')->nullable();
            $table->foreignId('winner_team_id')->nullable()->constrained('tournament_teams')->nullOnDelete();
            $table->text('notes')->nullable();
            $table->timestamp('played_at')->nullable();
            $table->timestamps();
            $table->index(['tournament_id', 'round', 'match_number']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tournament_matches');
    }
};
