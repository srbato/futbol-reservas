<?php

namespace App\Notifications;

use App\Models\Tournament;
use App\Models\TournamentTeam;
use App\Notifications\Concerns\HasWebPush;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class TournamentTeamRegisteredNotification extends Notification implements ShouldQueue
{
    use HasWebPush, Queueable;

    public function __construct(
        public readonly Tournament $tournament,
        public readonly TournamentTeam $team,
    ) {}

    public function toArray($notifiable): array
    {
        $confirmed = $this->tournament->confirmedTeams()->count();

        return [
            'title' => 'Nuevo equipo inscripto',
            'body' => "{$this->team->name} se inscribio a {$this->tournament->name}. ({$confirmed}/{$this->tournament->max_teams})",
            'icon' => 'users',
            'action_url' => route('torneos.manage', $this->tournament),
            'action_label' => 'Ver torneo',
        ];
    }
}
