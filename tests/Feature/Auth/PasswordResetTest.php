<?php

use App\Models\User;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Support\Facades\Notification;

test('reset password link screen can be rendered', function () {
    $response = $this->get('/forgot-password');

    $response->assertStatus(200);
});

test('reset password link can be requested', function () {
    Notification::fake();

    $user = User::factory()->create();

    $this->post('/forgot-password', ['email' => $user->email]);

    Notification::assertSentTo($user, ResetPassword::class);
});

test('reset password screen can be rendered', function () {
    Notification::fake();

    $user = User::factory()->create();

    $this->post('/forgot-password', ['email' => $user->email]);

    Notification::assertSentTo($user, ResetPassword::class, function ($notification) {
        $response = $this->get('/reset-password/'.$notification->token);

        $response->assertStatus(200);

        return true;
    });
});

test('password can be reset with valid token', function () {
    Notification::fake();

    $user = User::factory()->create();

    $this->post('/forgot-password', ['email' => $user->email]);

    Notification::assertSentTo($user, ResetPassword::class, function ($notification) use ($user) {
        $response = $this->post('/reset-password', [
            'token' => $notification->token,
            'email' => $user->email,
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $response
            ->assertSessionHasNoErrors()
            ->assertRedirect(route('login'));

        return true;
    });
});

test('forgot password fails for unknown email', function () {
    Notification::fake();
    $this->post('/forgot-password', ['email' => 'noexiste@test.com'])
        ->assertSessionHasErrors(['email']);
    Notification::assertNothingSent();
});

test('reset password requires matching confirmation', function () {
    $this->post('/reset-password', [
        'token'                 => 'fake',
        'email'                 => 'x@test.com',
        'password'              => 'one',
        'password_confirmation' => 'other',
    ])->assertSessionHasErrors(['password']);
});

test('reset password rejects invalid token', function () {
    User::factory()->create(['email' => 'real@test.com']);

    $this->post('/reset-password', [
        'token'                 => 'totally-invalid-token',
        'email'                 => 'real@test.com',
        'password'              => 'newpassword123',
        'password_confirmation' => 'newpassword123',
    ])->assertSessionHasErrors(['email']);
});
