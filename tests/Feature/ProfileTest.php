<?php

use App\Models\User;

test('profile page is displayed', function () {
    $user = User::factory()->create(['is_active' => true]);

    $this->actingAs($user)
        ->get('/profile')
        ->assertOk();
});

test('profile information can be updated', function () {
    $user = User::factory()->create(['is_active' => true]);

    // El controller usa POST /profile, no PATCH (boilerplate viejo decía PATCH)
    $response = $this->actingAs($user)
        ->post('/profile', [
            'name'  => 'Test User',
            'email' => 'test@example.com',
        ]);

    $response
        ->assertSessionHasNoErrors()
        ->assertRedirect(route('profile.edit'));

    $user->refresh();
    $this->assertSame('Test User', $user->name);
    $this->assertSame('test@example.com', $user->email);
    // Cambio de email invalida la verificación previa
    $this->assertNull($user->email_verified_at);
});

test('email verification status is unchanged when the email address is unchanged', function () {
    $user = User::factory()->create(['is_active' => true]);

    $this->actingAs($user)
        ->post('/profile', [
            'name'  => 'Test User',
            'email' => $user->email, // mismo email
        ])
        ->assertSessionHasNoErrors()
        ->assertRedirect(route('profile.edit'));

    $this->assertNotNull($user->refresh()->email_verified_at);
});

test('user can delete their account', function () {
    $user = User::factory()->create(['is_active' => true]);

    $response = $this->actingAs($user)
        ->delete('/profile', [
            'password' => 'password',
        ]);

    $response
        ->assertSessionHasNoErrors()
        ->assertRedirect('/');

    $this->assertGuest();
    $this->assertNull($user->fresh());
});

test('correct password must be provided to delete account', function () {
    $user = User::factory()->create(['is_active' => true]);

    $this->actingAs($user)
        ->from('/profile')
        ->delete('/profile', [
            'password' => 'wrong-password',
        ])
        ->assertSessionHasErrorsIn('userDeletion', 'password')
        ->assertRedirect('/profile');

    $this->assertNotNull($user->fresh());
});
