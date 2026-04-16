<?php

namespace App\Services;

use App\Models\FaltaUnoSportProfile;
use Illuminate\Support\Collection;

class BadgeService
{
    /**
     * Calculate reputation badges for a sport profile based on real-time stats.
     *
     * @return array<int, array{name: string, icon: string, color: string, bg: string, description: string}>
     */
    public function getBadges(FaltaUnoSportProfile $profile): array
    {
        $badges = [];

        $gamesPlayed = (int) $profile->games_played;
        $wins = (int) $profile->wins;
        $attendanceRate = (float) $profile->attendance_rate;
        $averageRating = (float) $profile->average_rating;
        $lateLeaves = (int) $profile->late_leaves_count;

        // "Jugador confiable" — attendance_rate >= 90%
        if ($attendanceRate >= 90 && $gamesPlayed >= 5) {
            $badges[] = [
                'name'        => 'Jugador confiable',
                'icon'        => 'shield-check',
                'color'       => '#16a34a',
                'bg'          => '#f0fdf4',
                'border'      => '#bbf7d0',
                'description' => 'Asistencia mayor al 90% en sus partidos',
            ];
        }

        // "Bien calificado" — average_rating >= 4.0
        if ($averageRating >= 4.0 && $gamesPlayed >= 3) {
            $badges[] = [
                'name'        => 'Bien calificado',
                'icon'        => 'star',
                'color'       => '#ca8a04',
                'bg'          => '#fefce8',
                'border'      => '#fef08a',
                'description' => 'Calificacion promedio de ' . number_format($averageRating, 1) . '/5',
            ];
        }

        // "Veterano" — games_played >= 50
        if ($gamesPlayed >= 50) {
            $badges[] = [
                'name'        => 'Veterano',
                'icon'        => 'award',
                'color'       => '#7c3aed',
                'bg'          => '#f5f3ff',
                'border'      => '#ddd6fe',
                'description' => 'Mas de 50 partidos jugados',
            ];
        }

        // "En racha" — wins >= 10 o win_rate >= 60% con minimo 10 partidos
        if ($gamesPlayed >= 10) {
            $winRate = $gamesPlayed > 0 ? ($wins / $gamesPlayed) * 100 : 0;
            if ($wins >= 10 || $winRate >= 60) {
                $badges[] = [
                    'name'        => 'En racha',
                    'icon'        => 'flame',
                    'color'       => '#ea580c',
                    'bg'          => '#fff7ed',
                    'border'      => '#fed7aa',
                    'description' => 'Tasa de victorias del ' . round($winRate) . '%',
                ];
            }
        }

        // "Puntual" — late_leaves_count == 0 y games_played >= 5
        if ($lateLeaves === 0 && $gamesPlayed >= 5) {
            $badges[] = [
                'name'        => 'Puntual',
                'icon'        => 'clock',
                'color'       => '#0284c7',
                'bg'          => '#f0f9ff',
                'border'      => '#bae6fd',
                'description' => 'Nunca abandono un partido antes de tiempo',
            ];
        }

        // "Debutante" — games_played >= 1 y < 10
        if ($gamesPlayed >= 1 && $gamesPlayed < 10) {
            $badges[] = [
                'name'        => 'Debutante',
                'icon'        => 'sparkles',
                'color'       => '#db2777',
                'bg'          => '#fdf2f8',
                'border'      => '#fbcfe8',
                'description' => 'Recien arrancando con ' . $gamesPlayed . ' partido' . ($gamesPlayed !== 1 ? 's' : ''),
            ];
        }

        return $badges;
    }

    /**
     * Get all badges across all sport profiles for a user.
     *
     * @param  Collection<int, FaltaUnoSportProfile>  $profiles
     * @return array<string, array<int, array{name: string, icon: string, color: string, bg: string, description: string}>>
     */
    public function getBadgesForProfiles(Collection $profiles): array
    {
        $badgesBySport = [];

        foreach ($profiles as $profile) {
            $badges = $this->getBadges($profile);
            if (!empty($badges)) {
                $badgesBySport[$profile->sport] = $badges;
            }
        }

        return $badgesBySport;
    }

    /**
     * Get unique badges across all profiles (for a summary view).
     *
     * @param  Collection<int, FaltaUnoSportProfile>  $profiles
     * @return array<int, array{name: string, icon: string, color: string, bg: string, description: string}>
     */
    public function getUniqueBadges(Collection $profiles): array
    {
        $allBadges = [];
        $seen = [];

        foreach ($profiles as $profile) {
            foreach ($this->getBadges($profile) as $badge) {
                if (!in_array($badge['name'], $seen)) {
                    $seen[] = $badge['name'];
                    $allBadges[] = $badge;
                }
            }
        }

        return $allBadges;
    }
}
