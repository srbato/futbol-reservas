<?php

namespace App\Services;

use App\Models\FaltaUnoGame;
use App\Models\FaltaUnoParticipant;
use App\Models\FaltaUnoPenalty;
use App\Models\User;
use Carbon\Carbon;

class FaltaUnoPenaltyService
{
    /**
     * Registra una bajada tardia: marca el participant, recalcula attendance, aplica cooldown/ban.
     */
    public function registerLateLeave(User $user, FaltaUnoGame $game): void
    {
        // Marcar el participant como bajada tardia
        $participant = FaltaUnoParticipant::where('game_id', $game->id)
            ->where('user_id', $user->id)
            ->where('status', 'cancelled')
            ->latest()
            ->first();

        if ($participant) {
            $participant->update([
                'is_late_leave' => true,
                'left_at' => now(),
            ]);
        }

        $sport = $game->field->sport;
        $profile = $user->sportProfileFor($sport);

        if ($profile) {
            $profile->increment('late_leaves_count');
            $this->recalculateAttendanceRate($user, $sport);
        }

        // Contar bajadas tardias en los ultimos 60 dias
        $recentLateCount = $this->countRecentLateLeaves($user, 60);

        // Bloqueo total: 5 bajadas tardias en 60 dias = ban de 7 dias
        if ($recentLateCount >= 5) {
            FaltaUnoPenalty::create([
                'user_id'    => $user->id,
                'sport'      => $sport,
                'type'       => 'ban',
                'reason'     => "Bloqueado por {$recentLateCount} bajadas tardias en los ultimos 60 dias.",
                'expires_at' => now()->addDays(7),
            ]);
        } else {
            // Cooldown escalonado: 24h * cantidad de bajadas recientes
            $cooldownHours = $this->calculateCooldownHours($recentLateCount);
            FaltaUnoPenalty::create([
                'user_id'    => $user->id,
                'sport'      => $sport,
                'type'       => 'cooldown',
                'reason'     => "Cooldown de {$cooldownHours}h por bajada tardia (#{$recentLateCount} en 60 dias).",
                'expires_at' => now()->addHours($cooldownHours),
            ]);
        }
    }

    /**
     * Verifica si el usuario puede unirse a un partido.
     * Retorna ['allowed' => bool, 'reason' => string|null, 'warnings' => []]
     */
    public function canJoin(User $user): array
    {
        $warnings = [];

        // Verificar ban activo
        $activeBan = FaltaUnoPenalty::where('user_id', $user->id)
            ->active()
            ->bans()
            ->first();

        if ($activeBan) {
            return [
                'allowed'  => false,
                'reason'   => 'Estas bloqueado de Falta Uno hasta el ' . $activeBan->expires_at->format('d/m/Y H:i') . '. Motivo: demasiadas bajadas tardias.',
                'warnings' => [],
            ];
        }

        // Verificar cooldown activo
        $activeCooldown = FaltaUnoPenalty::where('user_id', $user->id)
            ->active()
            ->cooldowns()
            ->first();

        if ($activeCooldown) {
            return [
                'allowed'  => false,
                'reason'   => 'Tenes un cooldown activo hasta el ' . $activeCooldown->expires_at->format('d/m/Y H:i') . ' por bajada tardia.',
                'warnings' => [],
            ];
        }

        // Warnings informativos
        $recentLateCount = $this->countRecentLateLeaves($user, 60);
        if ($recentLateCount >= 3) {
            $warnings[] = "Tenes {$recentLateCount} bajadas tardias en los ultimos 60 dias. A las 5 seras bloqueado por 7 dias.";
        }

        return [
            'allowed'  => true,
            'reason'   => null,
            'warnings' => $warnings,
        ];
    }

    /**
     * Obtiene datos de reputacion del usuario.
     */
    public function getReputationData(User $user, ?string $sport = null): array
    {
        $profile = $sport ? $user->sportProfileFor($sport) : null;

        $lateLeaves30 = $this->countRecentLateLeaves($user, 30);

        return [
            'attendance_rate'    => $profile ? (float) $profile->attendance_rate : 100.0,
            'late_leaves_count'  => $profile ? (int) $profile->late_leaves_count : 0,
            'has_badge'          => $lateLeaves30 >= 2,
            'late_leaves_30_days' => $lateLeaves30,
        ];
    }

    /**
     * Calcula las horas de cooldown segun la cantidad de bajadas recientes.
     * 24h la 1ra, 48h la 2da, 72h la 3ra, etc.
     */
    public function calculateCooldownHours(int $recentCount): int
    {
        return max(1, $recentCount) * 24;
    }

    /**
     * Recalcula el attendance_rate del perfil deportivo.
     * attendance_rate = completados / (completados + tardias) * 100
     */
    private function recalculateAttendanceRate(User $user, string $sport): void
    {
        $profile = $user->sportProfileFor($sport);
        if (!$profile) {
            return;
        }

        // Partidos completados: participaciones confirmadas en partidos que ya terminaron
        $completed = FaltaUnoParticipant::where('user_id', $user->id)
            ->where('status', 'confirmed')
            ->whereHas('game', function ($q) use ($sport) {
                $q->where('start_at', '<', now())
                  ->whereHas('field', fn($q2) => $q2->where('sport', $sport));
            })
            ->count();

        // Bajadas tardias totales en este deporte
        $lateLeaves = FaltaUnoParticipant::where('user_id', $user->id)
            ->where('is_late_leave', true)
            ->whereHas('game', function ($q) use ($sport) {
                $q->whereHas('field', fn($q2) => $q2->where('sport', $sport));
            })
            ->count();

        $total = $completed + $lateLeaves;
        $rate = $total > 0 ? round(($completed / $total) * 100, 2) : 100.00;

        $profile->update(['attendance_rate' => $rate]);
    }

    /**
     * Cuenta bajadas tardias en los ultimos N dias.
     */
    private function countRecentLateLeaves(User $user, int $days): int
    {
        return FaltaUnoParticipant::where('user_id', $user->id)
            ->where('is_late_leave', true)
            ->where('updated_at', '>=', now()->subDays($days))
            ->count();
    }
}
