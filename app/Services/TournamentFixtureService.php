<?php

namespace App\Services;

use App\Models\Tournament;
use App\Models\TournamentMatch;
use App\Models\TournamentTeam;

class TournamentFixtureService
{
    /**
     * Generar bracket de eliminacion directa.
     *
     * Algoritmo:
     * 1. Obtener equipos confirmados
     * 2. Calcular bracket_size (siguiente potencia de 2)
     * 3. Calcular rondas = log2(bracket_size)
     * 4. Crear matches de primera ronda, asignando byes si hay menos equipos que bracket_size
     * 5. Crear matches vacios para rondas siguientes (TBD)
     * 6. Resolver byes automaticamente (avanzar equipos sin rival)
     */
    public function generateSingleElimination(Tournament $tournament): void
    {
        // Borrar matches existentes (por si se regenera)
        $tournament->matches()->delete();

        $teams = $tournament->confirmedTeams()->inRandomOrder()->get();
        $numTeams = $teams->count();

        abort_if($numTeams < 2, 422, 'Se necesitan al menos 2 equipos.');

        // Siguiente potencia de 2
        $bracketSize = 1;
        while ($bracketSize < $numTeams) {
            $bracketSize *= 2;
        }

        $totalRounds = intval(log($bracketSize, 2));
        $firstRoundMatches = $bracketSize / 2;
        $byes = $bracketSize - $numTeams;

        // Crear matches de primera ronda
        $teamIndex = 0;
        for ($i = 1; $i <= $firstRoundMatches; $i++) {
            $homeTeam = $teams[$teamIndex] ?? null;
            $teamIndex++;

            $awayTeam = null;
            if ($i > $byes) {
                // Este match tiene dos equipos (no es bye)
                $awayTeam = $teams[$teamIndex] ?? null;
                $teamIndex++;
            }
            // Si $i <= $byes, awayTeam queda null (bye)

            TournamentMatch::create([
                'tournament_id' => $tournament->id,
                'round' => 1,
                'round_name' => $this->getRoundName(1, $totalRounds),
                'match_number' => $i,
                'home_team_id' => $homeTeam?->id,
                'away_team_id' => $awayTeam?->id,
                'venue_name_override' => $tournament->external_venue_name,
                'status' => TournamentMatch::STATUS_SCHEDULED,
            ]);
        }

        // Crear matches vacios para rondas siguientes
        for ($round = 2; $round <= $totalRounds; $round++) {
            $matchesInRound = $bracketSize / pow(2, $round);

            for ($i = 1; $i <= $matchesInRound; $i++) {
                TournamentMatch::create([
                    'tournament_id' => $tournament->id,
                    'round' => $round,
                    'round_name' => $this->getRoundName($round, $totalRounds),
                    'match_number' => $i,
                    'home_team_id' => null,
                    'away_team_id' => null,
                    'venue_name_override' => $tournament->external_venue_name,
                    'status' => TournamentMatch::STATUS_SCHEDULED,
                ]);
            }
        }

        // Resolver byes: avanzar automaticamente equipos sin rival
        $this->resolveByes($tournament);
    }

    /**
     * Avanzar ganador al siguiente match del bracket.
     *
     * El ganador de match N en ronda R va al match ceil(N/2) en ronda R+1.
     * Si N es impar -> slot home. Si N es par -> slot away.
     */
    public function advanceWinner(TournamentMatch $match): void
    {
        if (!$match->winner_team_id) return;

        $tournament = $match->tournament;
        $nextRound = $match->round + 1;
        $nextMatchNumber = intval(ceil($match->match_number / 2));

        $nextMatch = TournamentMatch::where('tournament_id', $tournament->id)
            ->where('round', $nextRound)
            ->where('match_number', $nextMatchNumber)
            ->first();

        if (!$nextMatch) {
            // Es la final, no hay siguiente match -> torneo terminado
            return;
        }

        // Si el match_number original es impar -> va al home, si es par -> away
        if ($match->match_number % 2 === 1) {
            $nextMatch->update(['home_team_id' => $match->winner_team_id]);
        } else {
            $nextMatch->update(['away_team_id' => $match->winner_team_id]);
        }
    }

    /**
     * Resolver byes de primera ronda.
     * Si un match tiene home pero no away (bye), el home avanza automaticamente.
     */
    private function resolveByes(Tournament $tournament): void
    {
        $byeMatches = $tournament->matches()
            ->where('round', 1)
            ->whereNotNull('home_team_id')
            ->whereNull('away_team_id')
            ->get();

        foreach ($byeMatches as $match) {
            $match->update([
                'status' => TournamentMatch::STATUS_FINISHED,
                'winner_team_id' => $match->home_team_id,
                'home_score' => 0,
                'away_score' => 0,
                'notes' => 'Bye — pase directo',
                'played_at' => now(),
            ]);

            $this->advanceWinner($match);
        }
    }

    /**
     * Obtener nombre legible de la ronda.
     */
    public function getRoundName(int $round, int $totalRounds): string
    {
        $fromEnd = $totalRounds - $round;

        return match ($fromEnd) {
            0 => 'Final',
            1 => 'Semifinal',
            2 => 'Cuartos de final',
            3 => 'Octavos de final',
            4 => 'Dieciseisavos de final',
            default => 'Ronda ' . $round,
        };
    }
}
