<?php

namespace App\Console\Commands;

use App\Models\FaltaUnoSportProfile;
use Illuminate\Console\Command;

class FaltaUnoResyncStats extends Command
{
    protected $signature = 'falta-uno:resync-stats {--user=* : IDs de usuarios a resincronizar (opcional, default: todos)}';

    protected $description = 'Recalcula stats (games_played, wins, draws, losses, average_rating, attendance_rate) de todos los FaltaUnoSportProfile desde las tablas fuente.';

    public function handle(): int
    {
        $userIds = $this->option('user');

        $query = FaltaUnoSportProfile::query();
        if (!empty($userIds)) {
            $query->whereIn('user_id', $userIds);
        }

        $total = $query->count();
        if ($total === 0) {
            $this->info('No hay perfiles para procesar.');
            return self::SUCCESS;
        }

        $this->info("Resincronizando {$total} perfil(es)...");
        $bar = $this->output->createProgressBar($total);
        $bar->start();

        $changed = 0;
        $errors  = 0;

        // Desactivar eventos durante el resync para evitar loops del observer
        // (el observer dispara recalculateStats sobre cada rating, pero acá ya lo hacemos manualmente)
        FaltaUnoSportProfile::withoutEvents(function () use ($query, &$changed, &$errors, $bar) {
        $query->chunk(50, function ($profiles) use (&$changed, &$errors, $bar) {
            foreach ($profiles as $profile) {
                try {
                    $before = [
                        'games_played'    => (int) $profile->games_played,
                        'wins'            => (int) $profile->wins,
                        'draws'           => (int) $profile->draws,
                        'losses'          => (int) $profile->losses,
                        'average_rating'  => (float) $profile->average_rating,
                        'attendance_rate' => (float) $profile->attendance_rate,
                    ];

                    $profile->recalculateStats();

                    $after = [
                        'games_played'    => (int) $profile->games_played,
                        'wins'            => (int) $profile->wins,
                        'draws'           => (int) $profile->draws,
                        'losses'          => (int) $profile->losses,
                        'average_rating'  => (float) $profile->average_rating,
                        'attendance_rate' => (float) $profile->attendance_rate,
                    ];

                    if ($before !== $after) {
                        $changed++;
                    }
                } catch (\Throwable $e) {
                    $errors++;
                    $this->newLine();
                    $this->error("Error en perfil #{$profile->id}: " . $e->getMessage());
                }

                $bar->advance();
            }
        });
        }); // withoutEvents

        $bar->finish();
        $this->newLine(2);
        $this->info("✓ Listo: {$total} perfiles procesados, {$changed} actualizados, {$errors} errores.");

        return self::SUCCESS;
    }
}
