<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Configuración por cancha
        Schema::create('falta_uno_settings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('field_id')->constrained()->cascadeOnDelete();
            $table->boolean('enabled')->default(false);
            $table->unsignedInteger('refund_deadline_minutes')->default(60);  // minutos antes del partido para reembolso
            $table->unsignedInteger('fill_deadline_minutes')->default(120);   // minutos antes del partido para llenarse
            $table->timestamps();
        });

        // Partidos falta uno
        Schema::create('falta_uno_games', function (Blueprint $table) {
            $table->id();
            $table->foreignId('field_id')->constrained()->cascadeOnDelete();
            $table->foreignId('reservation_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('initiator_user_id')->constrained('users')->cascadeOnDelete();
            $table->unsignedInteger('total_players');      // total de jugadores para el partido
            $table->unsignedInteger('initiator_players');  // jugadores que trae el iniciador
            $table->unsignedInteger('players_needed');     // faltan = total - initiator_players
            $table->string('status', 20)->default('open'); // open|full|cancelled|expired|finished — sin enum DB para evitar problemas de migración futura
            $table->dateTime('start_at');
            $table->decimal('amount_paid', 12, 2)->default(0);
            $table->timestamp('cancelled_at')->nullable();
            $table->timestamps();
        });

        // Participantes que se unen
        Schema::create('falta_uno_participants', function (Blueprint $table) {
            $table->id();
            $table->foreignId('game_id')->constrained('falta_uno_games')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('status', 20)->default('confirmed'); // confirmed|cancelled|no_show — sin enum para que migrations posteriores no necesiten alter constraint
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('falta_uno_participants');
        Schema::dropIfExists('falta_uno_games');
        Schema::dropIfExists('falta_uno_settings');
    }
};
