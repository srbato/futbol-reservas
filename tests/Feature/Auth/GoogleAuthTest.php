<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Socialite\Contracts\User as SocialiteUserContract;
use Laravel\Socialite\Facades\Socialite;
use Mockery;
use Tests\TestCase;

/**
 * Cobertura crítica de seguridad de Google OAuth.
 *  - Login con Google existente
 *  - Auto-link con email verificado (seguro)
 *  - HIJACK PREVENTION: NO auto-linkea si email no verificado (atacante podría tomar la cuenta)
 *  - Cuenta inactiva no puede entrar
 *  - Sin cuenta → redirige a registro con datos pre-cargados
 */
class GoogleAuthTest extends TestCase
{
    use RefreshDatabase;

    private function fakeGoogleUser(string $email, string $googleId, string $name = 'Test User'): void
    {
        $mock = Mockery::mock(SocialiteUserContract::class);
        $mock->shouldReceive('getId')->andReturn($googleId);
        $mock->shouldReceive('getEmail')->andReturn($email);
        $mock->shouldReceive('getName')->andReturn($name);

        $driverMock = Mockery::mock();
        $driverMock->shouldReceive('stateless')->andReturnSelf();
        $driverMock->shouldReceive('setHttpClient')->andReturnSelf();
        $driverMock->shouldReceive('user')->andReturn($mock);

        Socialite::shouldReceive('driver')->with('google')->andReturn($driverMock);
    }

    public function test_callback_logs_in_existing_google_user(): void
    {
        $user = User::factory()->create([
            'google_id'         => 'google-123',
            'email'             => 'google@test.com',
            'is_active'         => true,
            'email_verified_at' => now(),
        ]);

        $this->fakeGoogleUser('google@test.com', 'google-123');

        $this->get('/auth/google/callback')
            ->assertRedirect(route('venues.index'));

        $this->assertAuthenticatedAs($user);
    }

    public function test_callback_links_google_to_existing_verified_email_account(): void
    {
        $user = User::factory()->create([
            'google_id'         => null,
            'email'             => 'verified@test.com',
            'is_active'         => true,
            'email_verified_at' => now(), // EMAIL VERIFICADO → seguro auto-linkear
        ]);

        $this->fakeGoogleUser('verified@test.com', 'google-456');

        $this->get('/auth/google/callback')
            ->assertRedirect(route('venues.index'));

        $user->refresh();
        $this->assertSame('google-456', $user->google_id);
        $this->assertAuthenticatedAs($user);
    }

    public function test_callback_REFUSES_to_link_unverified_email_account(): void
    {
        // CRÍTICO DE SEGURIDAD: si el email no está verificado, no auto-linkear.
        // Caso de hijack: un atacante registra <email-de-víctima>@..., crea cuenta sin
        // verificar, y luego espera que la víctima entre con Google → tomaría la cuenta.
        $user = User::factory()->create([
            'google_id'         => null,
            'email'             => 'unverified@test.com',
            'is_active'         => true,
            'email_verified_at' => null,
        ]);

        $this->fakeGoogleUser('unverified@test.com', 'attacker-google-789');

        $this->get('/auth/google/callback')
            ->assertRedirect(route('login'))
            ->assertSessionHasErrors(['email']);

        $this->assertGuest();
        $this->assertNull($user->fresh()->google_id, 'Google ID NO debe haber sido linkeado');
    }

    public function test_callback_blocks_inactive_user(): void
    {
        User::factory()->create([
            'google_id'         => 'google-inactive',
            'email'             => 'inactive@test.com',
            'is_active'         => false,
            'email_verified_at' => now(),
        ]);

        $this->fakeGoogleUser('inactive@test.com', 'google-inactive');

        $this->get('/auth/google/callback')
            ->assertRedirect(route('login'))
            ->assertSessionHasErrors(['email']);

        $this->assertGuest();
    }

    public function test_callback_redirects_to_register_with_prefill_when_no_account_exists(): void
    {
        $this->fakeGoogleUser('newuser@test.com', 'google-newbie', 'Nuevo User');

        $this->get('/auth/google/callback')
            ->assertRedirect(route('register'))
            ->assertSessionHas('google_name', 'Nuevo User')
            ->assertSessionHas('google_email', 'newuser@test.com')
            ->assertSessionHas('google_id', 'google-newbie');

        $this->assertGuest();
    }

    public function test_redirect_endpoint_returns_redirect_to_google(): void
    {
        $driverMock = Mockery::mock();
        $driverMock->shouldReceive('stateless')->andReturnSelf();
        $driverMock->shouldReceive('redirect')->andReturn(redirect('https://accounts.google.com/oauth/test'));
        Socialite::shouldReceive('driver')->with('google')->andReturn($driverMock);

        $this->get('/auth/google')
            ->assertRedirect('https://accounts.google.com/oauth/test');
    }
}
