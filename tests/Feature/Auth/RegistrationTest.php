<?php

use App\Models\User;
use App\Mail\WelcomeMail;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Mail;

test('registration screen can be rendered', function () {
    $response = $this->get('/register');
    $response->assertStatus(200);
});

test('new users can register', function () {
    Mail::fake();
    Event::fake([Registered::class]);

    $response = $this->post('/register', [
        'name'                  => 'Test User',
        'email'                 => 'test@example.com',
        'password'              => 'password',
        'password_confirmation' => 'password',
    ]);

    $this->assertAuthenticated();
    // Después de registrarse va a venues.index (intencional, no a dashboard)
    $response->assertRedirect(route('venues.index'));

    $this->assertDatabaseHas('users', ['email' => 'test@example.com']);
    Mail::assertSent(WelcomeMail::class);
    Event::assertDispatched(Registered::class);
});

test('registration validates required fields', function () {
    $this->post('/register', [])
        ->assertSessionHasErrors(['name', 'email', 'password']);
});

test('registration rejects duplicate email', function () {
    User::factory()->create(['email' => 'taken@example.com']);

    $this->post('/register', [
        'name'                  => 'Other',
        'email'                 => 'taken@example.com',
        'password'              => 'password',
        'password_confirmation' => 'password',
    ])->assertSessionHasErrors(['email']);
});

test('password confirmation must match', function () {
    $this->post('/register', [
        'name'                  => 'X',
        'email'                 => 'x@example.com',
        'password'              => 'one-password',
        'password_confirmation' => 'other-password',
    ])->assertSessionHasErrors(['password']);
});

test('registration rejects name with a URL (spam bot)', function () {
    Mail::fake();

    $this->post('/register', [
        'name'                  => '$3,222 deposit available! http://naveenplast.com/?mn1m29',
        'email'                 => 'spam@mailbox.in.ua',
        'password'              => 'password',
        'password_confirmation' => 'password',
    ])->assertSessionHasErrors(['name']);

    $this->assertDatabaseMissing('users', ['email' => 'spam@mailbox.in.ua']);
    Mail::assertNothingSent();
});

test('registration rejects when honeypot is filled', function () {
    Mail::fake();

    $this->post('/register', [
        'name'                  => 'Real Person',
        'email'                 => 'bot@example.com',
        'password'              => 'password',
        'password_confirmation' => 'password',
        'website_url'           => 'http://bot.example',
    ])->assertStatus(422);

    $this->assertDatabaseMissing('users', ['email' => 'bot@example.com']);
});

test('registration rejects submission faster than 3 seconds', function () {
    Mail::fake();

    $this->post('/register', [
        'name'                  => 'Real Person',
        'email'                 => 'fast@example.com',
        'password'              => 'password',
        'password_confirmation' => 'password',
        'form_loaded_at'        => now()->valueOf(), // recién cargado → < 3s
    ])->assertStatus(422);

    $this->assertDatabaseMissing('users', ['email' => 'fast@example.com']);
});

test('registration accepts a human with valid timing', function () {
    Mail::fake();
    Event::fake([Registered::class]);

    $this->post('/register', [
        'name'                  => 'Juan Pérez',
        'email'                 => 'juan@example.com',
        'password'              => 'password',
        'password_confirmation' => 'password',
        'form_loaded_at'        => now()->subSeconds(10)->valueOf(),
    ]);

    $this->assertAuthenticated();
    $this->assertDatabaseHas('users', ['email' => 'juan@example.com']);
});
