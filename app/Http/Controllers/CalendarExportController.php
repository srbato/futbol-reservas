<?php

namespace App\Http\Controllers;

use App\Models\FaltaUnoGame;
use App\Models\Reservation;
use App\Models\Tournament;
use Illuminate\Http\Response;

class CalendarExportController extends Controller
{
    public function reservation(Reservation $reservation): Response
    {
        $reservation->load('field.venue');

        $venue = $reservation->field->venue;
        $field = $reservation->field;

        $summary = "Reserva en {$venue->name} - {$field->name}";
        $description = "Cancha: {$field->name}\\nComplejo: {$venue->name}\\nReserva #{$reservation->id}";
        $location = $venue->address ?? '';
        $uid = "reservation-{$reservation->id}@tucancha.com.ar";

        $dtStart = $this->formatDate($reservation->start_at);
        $dtEnd = $this->formatDate($reservation->end_at);

        $ics = $this->buildIcs($summary, $description, $location, $dtStart, $dtEnd, $uid);

        return $this->icsResponse($ics, "reserva-{$reservation->id}.ics");
    }

    public function faltaUno(FaltaUnoGame $game): Response
    {
        $game->load('field.venue', 'reservation');

        $venue = $game->field->venue;
        $field = $game->field;

        $summary = "Falta Uno en {$venue->name} - {$field->name}";
        $description = "Partido Falta Uno\\nCancha: {$field->name}\\nComplejo: {$venue->name}\\nJugadores: {$game->total_players}";
        $location = $venue->address ?? '';
        $uid = "faltauno-{$game->id}@tucancha.com.ar";

        $dtStart = $this->formatDate($game->start_at);
        // FaltaUnoGame no tiene end_at propio; usar el de la reserva o asumir 1 hora
        $endAt = $game->reservation?->end_at ?? $game->start_at->copy()->addHour();
        $dtEnd = $this->formatDate($endAt);

        $ics = $this->buildIcs($summary, $description, $location, $dtStart, $dtEnd, $uid);

        return $this->icsResponse($ics, "falta-uno-{$game->id}.ics");
    }

    public function tournament(Tournament $tournament): Response
    {
        $summary = "Torneo: {$tournament->name}";
        $description = "Torneo en TuCancha\\nDeporte: {$tournament->sport}\\nFormato: {$tournament->format}\\nEquipos: {$tournament->max_teams}";
        $location = $tournament->external_venue_address ?? $tournament->external_venue_name ?? '';
        $uid = "tournament-{$tournament->id}@tucancha.com.ar";

        // Torneos usan date (no datetime), evento de dia completo
        $startDate = $tournament->estimated_start_date ?? $tournament->actual_start_date;

        if (!$startDate) {
            abort(404, 'El torneo no tiene fecha de inicio definida.');
        }

        $dtStart = $startDate->format('Ymd');
        $dtEnd = $startDate->copy()->addDay()->format('Ymd');

        $ics = $this->buildAllDayIcs($summary, $description, $location, $dtStart, $dtEnd, $uid);

        return $this->icsResponse($ics, "torneo-{$tournament->slug}.ics");
    }

    private function formatDate(\Carbon\Carbon $date): string
    {
        return $date->setTimezone('America/Argentina/Buenos_Aires')->format('Ymd\THis');
    }

    private function buildIcs(string $summary, string $description, string $location, string $dtStart, string $dtEnd, string $uid): string
    {
        $now = now()->setTimezone('America/Argentina/Buenos_Aires')->format('Ymd\THis');

        return implode("\r\n", [
            'BEGIN:VCALENDAR',
            'VERSION:2.0',
            'PRODID:-//TuCancha//ES',
            'CALSCALE:GREGORIAN',
            'METHOD:PUBLISH',
            'BEGIN:VEVENT',
            "DTSTART;TZID=America/Argentina/Buenos_Aires:{$dtStart}",
            "DTEND;TZID=America/Argentina/Buenos_Aires:{$dtEnd}",
            "SUMMARY:{$this->escapeIcs($summary)}",
            "DESCRIPTION:{$description}",
            "LOCATION:{$this->escapeIcs($location)}",
            "UID:{$uid}",
            "DTSTAMP;TZID=America/Argentina/Buenos_Aires:{$now}",
            'STATUS:CONFIRMED',
            'END:VEVENT',
            'END:VCALENDAR',
        ]);
    }

    private function buildAllDayIcs(string $summary, string $description, string $location, string $dtStart, string $dtEnd, string $uid): string
    {
        $now = now()->setTimezone('America/Argentina/Buenos_Aires')->format('Ymd\THis');

        return implode("\r\n", [
            'BEGIN:VCALENDAR',
            'VERSION:2.0',
            'PRODID:-//TuCancha//ES',
            'CALSCALE:GREGORIAN',
            'METHOD:PUBLISH',
            'BEGIN:VEVENT',
            "DTSTART;VALUE=DATE:{$dtStart}",
            "DTEND;VALUE=DATE:{$dtEnd}",
            "SUMMARY:{$this->escapeIcs($summary)}",
            "DESCRIPTION:{$description}",
            "LOCATION:{$this->escapeIcs($location)}",
            "UID:{$uid}",
            "DTSTAMP;TZID=America/Argentina/Buenos_Aires:{$now}",
            'STATUS:CONFIRMED',
            'END:VEVENT',
            'END:VCALENDAR',
        ]);
    }

    private function escapeIcs(string $text): string
    {
        return str_replace([',', ';'], ['\\,', '\\;'], $text);
    }

    private function icsResponse(string $content, string $filename): Response
    {
        return response($content, 200, [
            'Content-Type' => 'text/calendar; charset=utf-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ]);
    }
}
