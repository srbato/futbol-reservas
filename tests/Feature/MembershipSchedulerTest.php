<?php

namespace Tests\Feature;

use App\Mail\TrialEndingMail;
use App\Models\User;
use App\Models\VenueAdminSubscription;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

/**
 * Cobertura del scheduler `membership:process-trials`.
 *  - Envía aviso al usuario cuyo trial vence HOY
 *  - Cuando el trial venció (expires_at < now), marca EXPIRED y revoca venue_admin role
 *  - No revoca role si el user tiene otra sub activa
 */
class MembershipSchedulerTest extends TestCase
{
    use RefreshDatabase;

    private function makeTrial(User $user, \DateTimeInterface $expiresAt, string $status = 'TRIAL'): VenueAdminSubscription
    {
        return VenueAdminSubscription::create([
            'user_id'          => $user->id,
            'plan_slug'        => 'basic',
            'billing_cycle'    => 'monthly',
            'long_term_months' => 1,
            'status'           => $status,
            'monthly_price'    => 5000,
            'currency'         => 'ARS',
            'starts_at'        => now()->subDays(7),
            'expires_at'       => $expiresAt,
            'trial_ends_at'    => $expiresAt,
        ]);
    }

    public function test_sends_warning_email_for_trial_ending_today(): void
    {
        Mail::fake();
        $user = User::factory()->create(['role' => 'venue_admin', 'is_active' => true]);
        $sub  = $this->makeTrial($user, today()->endOfDay());

        $this->artisan('membership:process-trials')->assertSuccessful();

        Mail::assertSent(TrialEndingMail::class, fn ($mail) => $mail->hasTo($user->email));

        // Sigue siendo TRIAL (todavía no expiró)
        $this->assertSame('TRIAL', $sub->fresh()->status);
        $this->assertSame('venue_admin', $user->fresh()->role);
    }

    public function test_revokes_trial_when_expires_at_is_past(): void
    {
        $user = User::factory()->create(['role' => 'venue_admin', 'is_active' => true]);
        $sub  = $this->makeTrial($user, now()->subHour());

        $this->artisan('membership:process-trials')->assertSuccessful();

        $this->assertSame('EXPIRED', $sub->fresh()->status);
        // Revoca el rol porque no tiene otra sub activa
        $this->assertSame('user', $user->fresh()->role);
    }

    public function test_does_not_revoke_role_when_user_has_other_active_subscription(): void
    {
        $user = User::factory()->create(['role' => 'venue_admin', 'is_active' => true]);

        // Trial expirado
        $expired = $this->makeTrial($user, now()->subHour());
        // PERO tiene otra sub activa
        $active = $this->makeTrial($user, now()->addDays(30), 'ACTIVE');

        $this->artisan('membership:process-trials')->assertSuccessful();

        $this->assertSame('EXPIRED', $expired->fresh()->status);
        $this->assertSame('ACTIVE', $active->fresh()->status);
        // Mantiene el rol gracias a la otra sub
        $this->assertSame('venue_admin', $user->fresh()->role);
    }

    public function test_does_not_send_email_for_trial_not_ending_today(): void
    {
        Mail::fake();
        $user = User::factory()->create(['role' => 'venue_admin', 'is_active' => true]);
        $this->makeTrial($user, now()->addDays(10)); // No vence hoy

        $this->artisan('membership:process-trials')->assertSuccessful();

        Mail::assertNothingSent();
    }
}
