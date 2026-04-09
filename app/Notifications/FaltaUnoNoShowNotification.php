<?php

namespace App\Notifications;

use App\Models\FaltaUnoGame;
use App\Models\FaltaUnoParticipant;
use App\Models\FaltaUnoPenalty;
use App\Notifications\Concerns\HasWebPush;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class FaltaUnoNoShowNotification extends Notification implements ShouldQueue
{
    use HasWebPush, Queueable;

    public function __construct(
        public readonly FaltaUnoGame $game,
    ) {}

    public function toArray($notifiable): array
    {
        $this->game->loadMissing('field.venue', 'initiator');

        $venue     = $this->game->field->venue->name ?? 'el complejo';
        $fieldName = $this->game->field->name ?? 'la cancha';
        $date      = $this->game->start_at->translatedFormat('l j \d\e F');
        $time      = $this->game->start_at->format('H:i');
        $organizer = $this->game->initiator->name ?? 'El organizador';

        // Contar no-shows recientes del usuario (últimos 60 días)
        $recentNoShows = FaltaUnoParticipant::where('user_id', $notifiable->id)
            ->where('status', 'no_show')
            ->where('no_show_at', '>=', now()->subDays(60))
            ->count();

        // Buscar penalización activa más reciente
        $activePenalty = FaltaUnoPenalty::where('user_id', $notifiable->id)
            ->where('expires_at', '>', now())
            ->orderByDesc('created_at')
            ->first();

        // Armar el mensaje con contexto
        $penaltyInfo = '';
        if ($activePenalty) {
            $expiresFormatted = $activePenalty->expires_at->translatedFormat('l j \d\e F \a \l\a\s H:i');
            if ($activePenalty->type === 'ban') {
                $penaltyInfo = " Estás bloqueado para unirte a partidos hasta el {$expiresFormatted}.";
            } else {
                $penaltyInfo = " Tenés un cooldown activo hasta el {$expiresFormatted}.";
            }
        }

        $warningLevel = '';
        if ($recentNoShows >= 2) {
            $remaining = 3 - $recentNoShows;
            if ($remaining > 0) {
                $warningLevel = " Llevás {$recentNoShows} ausencias en 60 días. Con {$remaining} más serás bloqueado por 14 días.";
            } else {
                $warningLevel = " Acumulaste {$recentNoShows} ausencias en 60 días.";
            }
        }

        $body = "{$organizer} te marcó como ausente en el partido de {$fieldName} ({$venue}) del {$date} a las {$time} hs.{$penaltyInfo}{$warningLevel}";

        return [
            'title'        => 'Ausencia registrada',
            'body'         => $body,
            'icon'         => '⚠️',
            'action_url'   => route('falta-uno.show', $this->game),
            'action_label' => 'Ver partido',
            'metadata'     => [
                'game_id'           => $this->game->id,
                'venue'             => $venue,
                'field'             => $fieldName,
                'date'              => $this->game->start_at->toDateTimeString(),
                'recent_no_shows'   => $recentNoShows,
                'penalty_type'      => $activePenalty?->type,
                'penalty_expires'   => $activePenalty?->expires_at?->toDateTimeString(),
            ],
        ];
    }
}
