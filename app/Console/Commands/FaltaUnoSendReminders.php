<?php

namespace App\Console\Commands;

use App\Models\FaltaUnoGame;
use App\Models\FaltaUnoSportProfile;
use App\Notifications\FaltaUnoReminderNotification;
use Illuminate\Console\Command;

class FaltaUnoSendReminders extends Command
{
    protected $signature   = 'falta-uno:send-reminders {--dry-run : Solo muestra cuántos recordatorios se enviarían}';
    protected $description = 'Envía recordatorios (notificación interna) a jugadores elegibles cuando un partido está por cerrar el cupo';

    /**
     * Ventana de tiempo (en minutos) antes del fill_deadline en la que se envía el recordatorio.
     * El aviso se manda cuando faltan entre WINDOW_START y WINDOW_END minutos para el cierre de cupo.
     */
    private const WINDOW_START = 90;
    private const WINDOW_END   = 30;

    public function handle(): int
    {
        $games = FaltaUnoGame::where('status', 'open')
            ->whereHas('reservation', fn($q) => $q->where('status', 'PAID'))
            ->whereNull('reminder_sent_at')
            ->where('start_at', '>', now())
            ->with(['field.faltaUnoSetting', 'field.venue', 'activeParticipants'])
            ->get()
            ->filter(fn($game) => $this->isInReminderWindow($game));

        $count = $games->count();

        if ($this->option('dry-run')) {
            $this->info("DRY RUN: {$count} partido(s) en ventana de recordatorio.");
            return self::SUCCESS;
        }

        foreach ($games as $game) {
            $sent = $this->sendRemindersForGame($game);
            $game->update(['reminder_sent_at' => now()]);
            $this->info("Juego #{$game->id}: {$sent} recordatorio(s) enviado(s).");
        }

        $this->info("OK: {$count} partido(s) procesado(s).");
        return self::SUCCESS;
    }

    private function isInReminderWindow(FaltaUnoGame $game): bool
    {
        $setting = $game->field->faltaUnoSetting;
        if (!$setting) {
            return false;
        }

        $fillDeadline  = $game->start_at->copy()->subMinutes($setting->fill_deadline_minutes);
        $windowStart   = $fillDeadline->copy()->subMinutes(self::WINDOW_START);
        $windowEnd     = $fillDeadline->copy()->subMinutes(self::WINDOW_END);

        return now()->between($windowStart, $windowEnd);
    }

    private function sendRemindersForGame(FaltaUnoGame $game): int
    {
        $sport = $game->field->sport;
        if (!$sport) {
            return 0;
        }

        // Usuarios que ya están en el partido (no notificar)
        $excludedIds = $game->activeParticipants
            ->pluck('user_id')
            ->push($game->initiator_user_id)
            ->unique();

        // Determinar categorías válidas para el rango del partido
        $categories = FaltaUnoSportProfile::getCategoriesForSport($sport);
        $minSearch  = $game->category_min ? array_search($game->category_min, $categories) : false;
        $maxSearch  = $game->category_max ? array_search($game->category_max, $categories) : false;
        $minIdx     = $minSearch !== false ? $minSearch : 0;
        $maxIdx     = $maxSearch !== false ? $maxSearch : count($categories) - 1;
        $validCats  = array_slice($categories, $minIdx, $maxIdx - $minIdx + 1);

        $query = FaltaUnoSportProfile::with('user')
            ->where('sport', $sport)
            ->whereNotIn('user_id', $excludedIds);

        if ($game->gender_filter !== 'mixed') {
            $query->where('gender', $game->gender_filter);
        }

        if ($game->category_min || $game->category_max) {
            $query->whereIn('category', $validCats);
        }

        $sent = 0;

        $query->limit(100)->chunk(20, function ($profiles) use ($game, &$sent) {
            foreach ($profiles as $profile) {
                if ($profile->user) {
                    try {
                        $profile->user->notify(new FaltaUnoReminderNotification($game, $profile->user));
                        $sent++;
                    } catch (\Throwable $e) {
                        $this->warn("Error notificando user #{$profile->user_id}: {$e->getMessage()}");
                    }
                }
            }
        });

        return $sent;
    }
}
