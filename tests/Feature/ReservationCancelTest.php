<?php

namespace Tests\Feature;

use App\Models\Reservation;
use App\Services\MercadoPagoRefundService;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Mockery;
use Tests\Concerns\MakesVenues;
use Tests\TestCase;

/**
 * Cobertura de cancelación de reservas:
 *  - Permisos (sólo dueño de la reserva o super_admin)
 *  - Política de cancellation_hours del complejo
 *  - Refund automático vía MP (mockeado)
 *  - Estados terminales (CANCELLED/EXPIRED no se pueden recancelar)
 *  - Reservas vinculadas a suscripción recurrente activa NO se cancelan individualmente
 */
class ReservationCancelTest extends TestCase
{
    use RefreshDatabase, MakesVenues;

    protected function setUp(): void
    {
        parent::setUp();
        Mail::fake(); // evitar enviar emails reales en tests

        // Mockear el refund service para no llamar a MP
        $this->mock(MercadoPagoRefundService::class, function ($m) {
            $m->shouldReceive('refund')->andReturn(null); // null = no refund needed
        });
    }

    public function test_user_can_cancel_their_own_reservation(): void
    {
        $field = $this->makeField();
        $user  = $this->makeUser();
        $reservation = Reservation::factory()->create([
            'field_id' => $field->id,
            'user_id'  => $user->id,
        ]);

        $this->actingAs($user)
            ->post(route('reservations.cancel', $reservation))
            ->assertSessionHas('success');

        $this->assertSame('CANCELLED', $reservation->fresh()->status);
        $this->assertNull($reservation->fresh()->expires_at);
    }

    public function test_user_cannot_cancel_someone_elses_reservation(): void
    {
        $field = $this->makeField();
        $userA = $this->makeUser();
        $userB = $this->makeUser();
        $reservation = Reservation::factory()->create([
            'field_id' => $field->id,
            'user_id'  => $userA->id,
        ]);

        $this->actingAs($userB)
            ->post(route('reservations.cancel', $reservation))
            ->assertForbidden();

        $this->assertSame('PAID', $reservation->fresh()->status);
    }

    public function test_super_admin_can_cancel_any_reservation(): void
    {
        $field = $this->makeField();
        $reservation = Reservation::factory()->create([
            'field_id' => $field->id,
            'user_id'  => $this->makeUser()->id,
        ]);
        $sa = $this->makeUser(['role' => 'super_admin']);

        $this->actingAs($sa)
            ->post(route('reservations.cancel', $reservation))
            ->assertSessionHas('success');

        $this->assertSame('CANCELLED', $reservation->fresh()->status);
    }

    public function test_cannot_cancel_already_cancelled_reservation(): void
    {
        $field = $this->makeField();
        $user  = $this->makeUser();
        $reservation = Reservation::factory()->cancelled()->create([
            'field_id' => $field->id,
            'user_id'  => $user->id,
        ]);

        $this->actingAs($user)
            ->post(route('reservations.cancel', $reservation))
            ->assertSessionHas('error');
    }

    public function test_cannot_cancel_expired_reservation(): void
    {
        $field = $this->makeField();
        $user  = $this->makeUser();
        $reservation = Reservation::factory()->expired()->create([
            'field_id' => $field->id,
            'user_id'  => $user->id,
        ]);

        $this->actingAs($user)
            ->post(route('reservations.cancel', $reservation))
            ->assertSessionHas('error');
    }

    public function test_cannot_cancel_paid_reservation_after_deadline_when_policy_set(): void
    {
        // Venue exige cancelar al menos 24h antes
        $venue = $this->makeVenue(['cancellation_hours' => 24]);
        $field = $this->makeField($venue);
        $user  = $this->makeUser();

        // Reserva PAID que arranca en 12 horas → ya pasó el deadline (24h antes)
        $reservation = Reservation::factory()->create([
            'field_id' => $field->id,
            'user_id'  => $user->id,
            'start_at' => now()->addHours(12),
            'end_at'   => now()->addHours(13),
            'status'   => 'PAID',
        ]);

        $this->actingAs($user)
            ->post(route('reservations.cancel', $reservation))
            ->assertSessionHas('error');

        $this->assertSame('PAID', $reservation->fresh()->status);
    }

    public function test_can_cancel_paid_reservation_within_deadline(): void
    {
        $venue = $this->makeVenue(['cancellation_hours' => 12]);
        $field = $this->makeField($venue);
        $user  = $this->makeUser();

        $reservation = Reservation::factory()->create([
            'field_id' => $field->id,
            'user_id'  => $user->id,
            'start_at' => now()->addHours(48), // mucho margen
            'end_at'   => now()->addHours(49),
            'status'   => 'PAID',
        ]);

        $this->actingAs($user)
            ->post(route('reservations.cancel', $reservation))
            ->assertSessionHas('success');

        $this->assertSame('CANCELLED', $reservation->fresh()->status);
    }

    public function test_cannot_cancel_reservation_linked_to_active_subscription(): void
    {
        $field = $this->makeField();
        $user  = $this->makeUser();

        $sub = \App\Models\RecurringSubscription::create([
            'user_id'        => $user->id,
            'field_id'       => $field->id,
            'status'         => 'ACTIVE',
            'frequency'      => 'weekly',
            'occurrences'    => 4,
            'day_of_week'    => 1,
            'start_time'     => '10:00:00',
            'slot_minutes'   => 60,
            'monthly_amount' => 5000,
            'currency'       => 'ARS',
        ]);

        $reservation = Reservation::factory()->create([
            'field_id'                  => $field->id,
            'user_id'                   => $user->id,
            'recurring_subscription_id' => $sub->id,
        ]);

        $this->actingAs($user)
            ->post(route('reservations.cancel', $reservation))
            ->assertSessionHas('error');

        $this->assertSame('PAID', $reservation->fresh()->status);
    }

    public function test_cancellation_attempts_refund_via_mp_service(): void
    {
        $field = $this->makeField();
        $user  = $this->makeUser();
        $reservation = Reservation::factory()->create([
            'field_id'         => $field->id,
            'user_id'          => $user->id,
            'payment_provider' => 'mercadopago',
        ]);

        // Override the mock to track call
        $mock = Mockery::mock(MercadoPagoRefundService::class);
        $mock->shouldReceive('refund')->once()->with(Mockery::on(fn ($r) => $r->id === $reservation->id))->andReturn(true);
        $this->app->instance(MercadoPagoRefundService::class, $mock);

        $this->actingAs($user)
            ->post(route('reservations.cancel', $reservation))
            ->assertSessionHas('success');
    }

    public function test_cancellation_succeeds_even_when_refund_fails(): void
    {
        $field = $this->makeField();
        $user  = $this->makeUser();
        $reservation = Reservation::factory()->create([
            'field_id' => $field->id,
            'user_id'  => $user->id,
        ]);

        $mock = Mockery::mock(MercadoPagoRefundService::class);
        $mock->shouldReceive('refund')->andReturn(false); // refund fail
        $this->app->instance(MercadoPagoRefundService::class, $mock);

        $this->actingAs($user)
            ->post(route('reservations.cancel', $reservation))
            ->assertSessionHas('success');

        // La reserva igual queda CANCELLED
        $this->assertSame('CANCELLED', $reservation->fresh()->status);
    }
}
