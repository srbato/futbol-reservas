<?php

namespace Tests\Feature;

use App\Models\MembershipPlan;
use App\Models\User;
use App\Models\VenueAdminSubscription;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Cobertura del flujo de membresía de venue_admin:
 *  - startTrial: regular_user → trial activa, role pasa a venue_admin
 *  - No se puede startTrial 2 veces (1 trial por usuario en su vida)
 *  - No se puede startTrial si ya hay sub activa
 *  - cancelSubscription cancela trial inmediatamente
 *  - Pantallas success/pending/failure responden 200
 */
class MembershipTest extends TestCase
{
    use RefreshDatabase;

    private function makePlan(array $overrides = []): MembershipPlan
    {
        return MembershipPlan::create(array_merge([
            'slug'                       => 'basic',
            'name'                       => 'Básico',
            'max_fields'                 => 3,
            'monthly_price'              => 5000,
            'annual_discount_percentage' => 0,
            'long_term_months'           => 1,
            'trial_days'                 => 7,
            'is_active'                  => true,
            'sort_order'                 => 1,
        ], $overrides));
    }

    public function test_user_can_start_trial_for_a_plan_with_trial_days(): void
    {
        $plan = $this->makePlan(['trial_days' => 14]);
        $user = User::factory()->create(['role' => 'user', 'is_active' => true]);

        $this->actingAs($user)
            ->post(route('membership.start_trial'), ['plan_slug' => $plan->slug])
            ->assertRedirect(route('va.dashboard'))
            ->assertSessionHas('success');

        $sub = VenueAdminSubscription::where('user_id', $user->id)->first();
        $this->assertNotNull($sub);
        $this->assertSame('TRIAL', $sub->status);
        $this->assertSame($plan->slug, $sub->plan_slug);
        $this->assertNotNull($sub->trial_ends_at);

        // Auto-promueve el rol a venue_admin
        $this->assertSame('venue_admin', $user->fresh()->role);
    }

    public function test_cannot_start_trial_twice(): void
    {
        $plan = $this->makePlan();
        $user = User::factory()->create(['role' => 'user', 'is_active' => true]);

        // Trial ya usado
        VenueAdminSubscription::create([
            'user_id'          => $user->id,
            'plan_slug'        => $plan->slug,
            'billing_cycle'    => 'monthly',
            'long_term_months' => 1,
            'status'           => 'EXPIRED',
            'monthly_price'    => $plan->monthly_price,
            'currency'         => 'ARS',
            'trial_ends_at'    => now()->subDays(30),
            'starts_at'        => now()->subDays(40),
            'expires_at'       => now()->subDays(30),
        ]);

        $this->actingAs($user)
            ->post(route('membership.start_trial'), ['plan_slug' => $plan->slug])
            ->assertSessionHas('error');

        // Solo debería haber 1 sub (la vieja)
        $this->assertSame(1, VenueAdminSubscription::where('user_id', $user->id)->count());
    }

    public function test_cannot_start_trial_when_already_has_active_subscription(): void
    {
        $plan = $this->makePlan();
        $user = User::factory()->create(['role' => 'venue_admin', 'is_active' => true]);

        VenueAdminSubscription::create([
            'user_id'          => $user->id,
            'plan_slug'        => $plan->slug,
            'billing_cycle'    => 'monthly',
            'long_term_months' => 1,
            'status'           => 'ACTIVE',
            'monthly_price'    => $plan->monthly_price,
            'currency'         => 'ARS',
            'starts_at'        => now()->subDays(5),
            'expires_at'       => now()->addDays(25),
        ]);

        $this->actingAs($user)
            ->post(route('membership.start_trial'), ['plan_slug' => $plan->slug])
            ->assertSessionHas('error');
    }

    public function test_cannot_start_trial_for_plan_without_trial_days(): void
    {
        $plan = $this->makePlan(['trial_days' => 0]);
        $user = User::factory()->create(['role' => 'user', 'is_active' => true]);

        $this->actingAs($user)
            ->post(route('membership.start_trial'), ['plan_slug' => $plan->slug])
            ->assertSessionHas('error');

        $this->assertSame(0, VenueAdminSubscription::where('user_id', $user->id)->count());
    }

    public function test_super_admin_cannot_start_trial(): void
    {
        $plan = $this->makePlan();
        $sa = User::factory()->create(['role' => 'super_admin', 'is_active' => true]);

        $this->actingAs($sa)
            ->post(route('membership.start_trial'), ['plan_slug' => $plan->slug])
            ->assertSessionHas('error');
    }

    public function test_cancel_subscription_terminates_trial_immediately(): void
    {
        $plan = $this->makePlan();
        $user = User::factory()->create(['role' => 'venue_admin', 'is_active' => true]);

        $sub = VenueAdminSubscription::create([
            'user_id'          => $user->id,
            'plan_slug'        => $plan->slug,
            'billing_cycle'    => 'monthly',
            'long_term_months' => 1,
            'status'           => 'TRIAL',
            'monthly_price'    => $plan->monthly_price,
            'currency'         => 'ARS',
            'starts_at'        => now(),
            'expires_at'       => now()->addDays(7),
            'trial_ends_at'    => now()->addDays(7),
        ]);

        $this->actingAs($user)
            ->post(route('membership.cancel_subscription'))
            ->assertRedirect();

        $sub->refresh();
        $this->assertSame('CANCELLED', $sub->status);
    }

    public function test_cancel_returns_error_when_no_active_subscription(): void
    {
        $user = User::factory()->create(['role' => 'venue_admin', 'is_active' => true]);

        $this->actingAs($user)
            ->post(route('membership.cancel_subscription'))
            ->assertSessionHas('error');
    }

    public function test_membership_screens_respond_for_authed_users(): void
    {
        $this->makePlan();
        $user = User::factory()->create(['is_active' => true]);

        // Algunas pantallas pueden redirigir según estado de la suscripción.
        // Lo importante es que NO devuelvan 401/500.
        foreach (['membership.success', 'membership.pending', 'membership.failure', 'membership.become'] as $route) {
            $resp = $this->actingAs($user)->get(route($route));
            $this->assertContains($resp->status(), [200, 302], "Route $route returned {$resp->status()}");
        }
    }
}
