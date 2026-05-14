<?php

namespace Tests\Feature\Admin;

use App\Models\User;
use App\Models\VenueAdminSubscription;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Concerns\MakesVenues;
use Tests\TestCase;

/**
 * Cobertura del UserManagementController (super-admin only).
 *  - updateRole con validaciones
 *  - Promover a venue_admin crea suscripción "special"
 *  - Quitar rol venue_admin cancela suscripciones
 *  - Activate/Deactivate
 *  - No suicidio (no podés eliminarte/desactivarte)
 */
class UserManagementTest extends TestCase
{
    use RefreshDatabase, MakesVenues;

    public function test_super_admin_can_promote_user_to_venue_admin(): void
    {
        $sa     = $this->makeUser(['role' => 'super_admin']);
        $target = $this->makeUser(['role' => 'user']);

        $this->actingAs($sa)
            ->post(route('sa.users.role', $target), ['role' => 'venue_admin'])
            ->assertSessionHas('success');

        $this->assertSame('venue_admin', $target->fresh()->role);
        // Debe haber una subscription activa "special"
        $this->assertDatabaseHas('venue_admin_subscriptions', [
            'user_id'    => $target->id,
            'plan_slug'  => 'special',
            'status'     => 'ACTIVE',
        ]);
    }

    public function test_demote_venue_admin_cancels_active_subscriptions(): void
    {
        $sa     = $this->makeUser(['role' => 'super_admin']);
        $target = $this->makeUser(['role' => 'venue_admin']);

        VenueAdminSubscription::create([
            'user_id'          => $target->id,
            'plan_slug'        => 'basic',
            'billing_cycle'    => 'monthly',
            'long_term_months' => 1,
            'status'           => 'ACTIVE',
            'monthly_price'    => 5000,
            'currency'         => 'ARS',
            'starts_at'        => now()->subDays(10),
            'expires_at'       => now()->addDays(20),
        ]);

        $this->actingAs($sa)
            ->post(route('sa.users.role', $target), ['role' => 'user'])
            ->assertSessionHas('success');

        $this->assertSame('user', $target->fresh()->role);
        $this->assertDatabaseHas('venue_admin_subscriptions', [
            'user_id' => $target->id,
            'status'  => 'CANCELLED',
        ]);
    }

    public function test_role_must_be_one_of_allowed_values(): void
    {
        $sa     = $this->makeUser(['role' => 'super_admin']);
        $target = $this->makeUser(['role' => 'user']);

        $this->actingAs($sa)
            ->post(route('sa.users.role', $target), ['role' => 'developer'])
            ->assertSessionHasErrors(['role']);
    }

    public function test_non_super_admin_cannot_change_roles(): void
    {
        $regular = $this->makeUser(['role' => 'user']);
        $target  = $this->makeUser(['role' => 'user']);

        $resp = $this->actingAs($regular)
            ->post(route('sa.users.role', $target), ['role' => 'venue_admin']);

        $this->assertContains($resp->status(), [302, 403]);
        $this->assertSame('user', $target->fresh()->role);
    }

    public function test_super_admin_can_deactivate_user(): void
    {
        $sa     = $this->makeUser(['role' => 'super_admin']);
        $target = $this->makeUser(['is_active' => true]);

        $this->actingAs($sa)
            ->post(route('sa.users.deactivate', $target))
            ->assertSessionHas('success');

        $this->assertFalse((bool) $target->fresh()->is_active);
    }

    public function test_super_admin_can_reactivate_user(): void
    {
        $sa     = $this->makeUser(['role' => 'super_admin']);
        $target = $this->makeUser(['is_active' => false]);

        $this->actingAs($sa)
            ->post(route('sa.users.activate', $target))
            ->assertSessionHas('success');

        $this->assertTrue((bool) $target->fresh()->is_active);
    }

    public function test_super_admin_cannot_deactivate_themselves(): void
    {
        $sa = $this->makeUser(['role' => 'super_admin', 'is_active' => true]);

        $this->actingAs($sa)
            ->post(route('sa.users.deactivate', $sa))
            ->assertSessionHas('error');

        $this->assertTrue((bool) $sa->fresh()->is_active);
    }

    public function test_super_admin_cannot_delete_themselves(): void
    {
        $sa = $this->makeUser(['role' => 'super_admin']);

        $this->actingAs($sa)
            ->post(route('sa.users.destroy', $sa))
            ->assertSessionHas('error');

        $this->assertNotNull(User::find($sa->id));
    }

    public function test_super_admin_can_delete_other_user(): void
    {
        $sa     = $this->makeUser(['role' => 'super_admin']);
        $target = $this->makeUser();

        $this->actingAs($sa)
            ->post(route('sa.users.destroy', $target))
            ->assertSessionHas('success');

        $this->assertNull(User::find($target->id));
    }
}
