<?php

namespace App\Notifications;

use App\Models\Tournament;
use App\Models\TournamentMatch;
use App\Notifications\Concerns\HasWebPush;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class TournamentMatchResultNotification extends Notification implements ShouldQueue
{
    use HasWebPush, Queueable;

    public function __construct(
        public readonly Tournament $tournament,
        public readonly TournamentMatch $match,
    ) {}

    public function toArray($notifiable): array
    {
        $home = $this->match->homeTeam->name ?? 'Local';
        $away = $this->match->awayTeam->name ?? 'Visitante';
        $score = $this->match->scoreDisplay();

        return [
            'title' => "Resultado: {$home} vs {$away}",
            'body' => "{$home} {$score} {$away} — {$this->tournament->name}",
            'icon' => 'trophy',
            'action_url' => route('torneos.show', $this->tournament),
            'action_label' => 'Ver torneo',
        ];
    }
}
