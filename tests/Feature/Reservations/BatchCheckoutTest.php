<?php

namespace Tests\Feature\Reservations;

use App\Models\Reservation;
use App\Models\ReservationBatch;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Concerns\MakesVenues;
use Tests\TestCase;

/**
 * Vistas y endpoints de batches de reservas:
 *  - GET /batches/{batch}/checkout — pantalla de pago del batch (solo dueño/SA)
 *  - GET /batches/{batch}/status — JSON con status (para polling)
 *  - GET /batch-pending/{batch} y /batch-failure/{batch} — pantallas de retorno MP
 */
class BatchCheckoutTest extends TestCase
{
    use RefreshDatabase, MakesVenues;

    private function makeBatch(int $userId, array $overrides = []): ReservationBatch
    {
        $field = $this->makeField();
        $batch = ReservationBatch::create(array_merge([
            'user_id'      => $userId,
            'field_id'     => $field->id,
            'subtotal'     => 10000,
            'total_amount' => 10000,
            'currency'     => 'ARS',
            'status'       => 'PENDING_PAYMENT',
            'expires_at'   => now()->addMinutes(15),
        ], $overrides));

        Reservation::factory()->pendingPayment()->create([
            'field_id' => $field->id,
            'user_id'  => $userId,
            'batch_id' => $batch->id,
        ]);

        return $batch;
    }

    public function test_owner_can_view_batch_checkout(): void
    {
        $user = $this->makeUser();
        $batch = $this->makeBatch($user->id);

        $this->actingAs($user)
            ->get(route('batches.checkout', $batch))
            ->assertOk();
    }

    public function test_other_user_cannot_view_someone_elses_batch_checkout(): void
    {
        $user  = $this->makeUser();
        $batch = $this->makeBatch($user->id);
        $other = $this->makeUser();

        $this->actingAs($other)
            ->get(route('batches.checkout', $batch))
            ->assertForbidden();
    }

    public function test_super_admin_can_view_any_batch_checkout(): void
    {
        $user  = $this->makeUser();
        $batch = $this->makeBatch($user->id);
        $sa    = $this->makeUser(['role' => 'super_admin']);

        $this->actingAs($sa)
            ->get(route('batches.checkout', $batch))
            ->assertOk();
    }

    public function test_batch_status_endpoint_returns_current_status(): void
    {
        $user  = $this->makeUser();
        $batch = $this->makeBatch($user->id);

        $this->actingAs($user)
            ->getJson(route('batches.status', $batch))
            ->assertOk()
            ->assertExactJson(['status' => 'PENDING_PAYMENT']);

        $batch->update(['status' => 'PAID']);

        $this->actingAs($user)
            ->getJson(route('batches.status', $batch))
            ->assertOk()
            ->assertExactJson(['status' => 'PAID']);
    }

    public function test_batch_pending_screen_works(): void
    {
        $user  = $this->makeUser();
        $batch = $this->makeBatch($user->id);

        $this->actingAs($user)
            ->get(route('batches.pending', $batch))
            ->assertOk();
    }

    public function test_batch_failure_screen_works(): void
    {
        $user  = $this->makeUser();
        $batch = $this->makeBatch($user->id);

        $this->actingAs($user)
            ->get(route('batches.failure', $batch))
            ->assertOk();
    }

    public function test_unauthenticated_users_cannot_access_batch_endpoints(): void
    {
        $user  = $this->makeUser();
        $batch = $this->makeBatch($user->id);

        $this->get(route('batches.checkout', $batch))->assertRedirect(route('login'));
        $this->get(route('batches.pending', $batch))->assertRedirect(route('login'));
        $this->get(route('batches.failure', $batch))->assertRedirect(route('login'));
    }

    public function test_other_user_cannot_check_someone_elses_batch_status(): void
    {
        $user  = $this->makeUser();
        $batch = $this->makeBatch($user->id);
        $other = $this->makeUser();

        $this->actingAs($other)
            ->getJson(route('batches.status', $batch))
            ->assertForbidden();
    }

    // ─── Reservation view ────────────────────────────────────────────────

    public function test_owner_can_view_their_reservation_detail(): void
    {
        $field = $this->makeField();
        $user  = $this->makeUser();
        $reservation = Reservation::factory()->create([
            'field_id' => $field->id,
            'user_id'  => $user->id,
        ]);

        $this->actingAs($user)
            ->get(route('reservations.show', $reservation))
            ->assertOk();
    }

    public function test_stranger_cannot_view_others_reservation_detail(): void
    {
        $field = $this->makeField();
        $reservation = Reservation::factory()->create([
            'field_id' => $field->id,
            'user_id'  => $this->makeUser()->id,
        ]);
        $other = $this->makeUser();

        $this->actingAs($other)
            ->get(route('reservations.show', $reservation))
            ->assertForbidden();
    }

    public function test_venue_owner_can_view_reservation_in_their_venue(): void
    {
        $field = $this->makeField(); // owner es super_admin → veamos el venue_admin path
        $venueOwner = $this->makeUser(['role' => 'venue_admin']);
        $field->venue->update(['owner_user_id' => $venueOwner->id]);

        $reservation = Reservation::factory()->create([
            'field_id' => $field->id,
            'user_id'  => $this->makeUser()->id,
        ]);

        $this->actingAs($venueOwner)
            ->get(route('reservations.show', $reservation))
            ->assertOk();
    }
}
