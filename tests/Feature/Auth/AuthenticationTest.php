<?php

use App\Models\User;

test('login screen can be rendered', function () {
    $response = $this->get('/login');
    $response->assertStatus(200);
});

test('users can authenticate using the login screen', function () {
    $user = User::factory()->create(['is_active' => true]);

    $response = $this->post('/login', [
        'email'    => $user->email,
        'password' => 'password',
    ]);

    $this->assertAuthenticated();
    // El controller redirige a venues.index post-login (UX intencional, no a dashboard)
    $response->assertRedirect(route('venues.index'));
});

test('users can not authenticate with invalid password', function () {
    $user = User::factory()->create(['is_active' => true]);

    $this->post('/login', [
        'email'    => $user->email,
        'password' => 'wrong-password',
    ]);

    $this->assertGuest();
});

test('inactive users are logged out and shown an error on login', function () {
    $user = User::factory()->create(['is_active' => false]);

    $response = $this->post('/login', [
        'email'    => $user->email,
        'password' => 'password',
    ]);

    $this->assertGuest();
    $response->assertSessionHasErrors(['email']);
});

test('users can logout', function () {
    $user = User::factory()->create(['is_active' => true]);

    $response = $this->actingAs($user)->post('/logout');

    $this->assertGuest();
    // Redirige a /venues, no a / (UX intencional)
    $response->assertRedirect('/venues');
});
