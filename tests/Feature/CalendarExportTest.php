<?php

namespace Tests\Feature;

use App\Models\FaltaUnoGame;
use App\Models\Reservation;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Concerns\MakesVenues;
use Tests\TestCase;

/**
 * Calendar export endpoints (.ics):
 *  - reservation → genera .ics descargable con horario y datos del complejo
 *  - faltaUno → genera .ics del partido
 *  - tournament → genera .ics all-day del torneo (skip — requiere fixture extra)
 */
class CalendarExportTest extends TestCase
{
    use RefreshDatabase, MakesVenues;

    public function test_reservation_export_returns_valid_ics_with_correct_headers(): void
    {
        $field = $this->makeField();
        $user  = $this->makeUser();
        $reservation = Reservation::factory()->create([
            'field_id' => $field->id,
            'user_id'  => $user->id,
        ]);

        $resp = $this->actingAs($user)
            ->get(route('calendar.reservation', $reservation));

        $resp->assertOk()
            ->assertHeader('Content-Type', 'text/calendar; charset=utf-8')
            ->assertHeader('Content-Disposition', 'attachment; filename="reserva-' . $reservation->id . '.ics"');

        $body = $resp->getContent();
        $this->assertStringContainsString('BEGIN:VCALENDAR', $body);
        $this->assertStringContainsString('END:VCALENDAR', $body);
        $this->assertStringContainsString('BEGIN:VEVENT', $body);
        $this->assertStringContainsString('SUMMARY:Reserva en', $body);
        $this->assertStringContainsString('UID:reservation-' . $reservation->id . '@tucancha.com.ar', $body);
        $this->assertStringContainsString('STATUS:CONFIRMED', $body);
    }

    public function test_reservation_export_includes_venue_and_field_names(): void
    {
        $venue = $this->makeVenue(['name' => 'Complejo Test']);
        $field = $this->makeField($venue, ['name' => 'Cancha Premium']);
        $user  = $this->makeUser();
        $reservation = Reservation::factory()->create([
            'field_id' => $field->id,
            'user_id'  => $user->id,
        ]);

        $resp = $this->actingAs($user)
            ->get(route('calendar.reservation', $reservation));

        $body = $resp->getContent();
        $this->assertStringContainsString('Complejo Test', $body);
        $this->assertStringContainsString('Cancha Premium', $body);
    }

    public function test_falta_uno_export_returns_valid_ics(): void
    {
        $field = $this->makeField();
        $user  = $this->makeUser();
        $reservation = Reservation::factory()->create([
            'field_id' => $field->id, 'user_id' => $user->id,
        ]);
        $game = FaltaUnoGame::factory()->create([
            'field_id'       => $field->id,
            'reservation_id' => $reservation->id,
            'start_at'       => $reservation->start_at,
            'total_players'  => 10,
        ]);

        $resp = $this->actingAs($user)
            ->get(route('calendar.falta-uno', $game));

        $resp->assertOk()
            ->assertHeader('Content-Type', 'text/calendar; charset=utf-8');

        $body = $resp->getContent();
        $this->assertStringContainsString('SUMMARY:Falta Uno en', $body);
        $this->assertStringContainsString('Jugadores: 10', $body);
        $this->assertStringContainsString('UID:faltauno-' . $game->id . '@tucancha.com.ar', $body);
    }

    public function test_unauthenticated_users_cannot_export(): void
    {
        $field = $this->makeField();
        $user  = $this->makeUser();
        $reservation = Reservation::factory()->create([
            'field_id' => $field->id, 'user_id' => $user->id,
        ]);

        $this->get(route('calendar.reservation', $reservation))
            ->assertRedirect(route('login'));
    }
}
