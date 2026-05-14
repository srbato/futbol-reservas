<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * EnsureUserIsActive middleware:
 *  - Si el usuario está autenticado pero is_active=false → logout + redirect al login con error
 *  - Si is_active=true → pasa
 *  - Sin auth → pasa (la auth la maneja otro middleware)
 */
class ActiveUserMiddlewareTest extends TestCase
{
    use RefreshDatabase;

    public function test_active_user_can_access_protected_route(): void
    {
        $user = User::factory()->create(['is_active' => true]);

        $this->actingAs($user)
            ->get('/profile')
            ->assertOk();

        $this->assertAuthenticated();
    }

    public function test_inactive_user_is_logged_out_on_protected_route(): void
    {
        $user = User::factory()->create(['is_active' => false]);

        $this->actingAs($user)
            ->get('/profile')
            ->assertRedirect(route('login'));

        $this->assertGuest();
    }

    public function test_inactive_user_logout_clears_session(): void
    {
        $user = User::factory()->create(['is_active' => false]);

        $this->actingAs($user);
        // Antes: hay sesión auth
        $this->assertAuthenticated();

        $this->get('/profile');
        // Después del middleware: sesión limpia
        $this->assertGuest();
    }

    public function test_my_reservations_blocked_for_inactive_user(): void
    {
        $user = User::factory()->create(['is_active' => false]);

        $this->actingAs($user)
            ->get(route('my_reservations'))
            ->assertRedirect(route('login'));
    }
}
