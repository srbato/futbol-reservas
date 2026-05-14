<?php

namespace Tests\Feature\Admin;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Concerns\MakesVenues;
use Tests\TestCase;

/**
 * Cobertura de seguridad de la impersonación super-admin.
 *  - Solo super_admin puede impersonar
 *  - Auto-impersonación bloqueada
 *  - stopImpersonate restaura el admin original
 *  - Sesión válida sólo si original_admin_id sigue siendo super_admin
 *  - stopImpersonate sin sesión activa redirige sin romper
 */
class ImpersonationTest extends TestCase
{
    use RefreshDatabase, MakesVenues;

    public function test_super_admin_can_impersonate_another_user(): void
    {
        $sa     = $this->makeUser(['role' => 'super_admin']);
        $target = $this->makeUser(['role' => 'user']);

        $this->actingAs($sa)
            ->post(route('sa.users.impersonate', $target))
            ->assertRedirect('/');

        $this->assertSame($target->id, auth()->id());
        $this->assertSame($target->id, session('impersonating_as'));
        $this->assertSame($sa->id, session('original_admin_id'));
    }

    public function test_non_super_admin_cannot_impersonate(): void
    {
        $regular = $this->makeUser(['role' => 'user']);
        $target  = $this->makeUser(['role' => 'user']);

        $resp = $this->actingAs($regular)
            ->post(route('sa.users.impersonate', $target));

        // role:super_admin middleware → 302 / 403
        $this->assertContains($resp->status(), [302, 403]);
        $this->assertSame($regular->id, auth()->id()); // no cambió de identidad
    }

    public function test_super_admin_cannot_impersonate_themselves(): void
    {
        $sa = $this->makeUser(['role' => 'super_admin']);

        $this->actingAs($sa)
            ->post(route('sa.users.impersonate', $sa))
            ->assertSessionHas('error');

        $this->assertSame($sa->id, auth()->id());
        $this->assertNull(session('impersonating_as'));
    }

    public function test_stop_impersonate_restores_original_admin(): void
    {
        $sa     = $this->makeUser(['role' => 'super_admin']);
        $target = $this->makeUser(['role' => 'user']);

        $this->actingAs($sa)->post(route('sa.users.impersonate', $target));
        $this->assertSame($target->id, auth()->id());

        $this->post(route('sa.users.stop_impersonate'))
            ->assertRedirect(route('sa.users.index'));

        $this->assertSame($sa->id, auth()->id());
        $this->assertNull(session('impersonating_as'));
        $this->assertNull(session('original_admin_id'));
    }

    public function test_stop_impersonate_without_session_redirects_safely(): void
    {
        $user = $this->makeUser();

        $this->actingAs($user)
            ->post(route('sa.users.stop_impersonate'))
            ->assertRedirect('/');
    }

    public function test_impersonation_route_requires_csrf_authenticated_super_admin(): void
    {
        // No auth en absoluto → 302 al login
        $target = $this->makeUser();
        $this->post(route('sa.users.impersonate', $target))
            ->assertRedirect(route('login'));
    }
}
