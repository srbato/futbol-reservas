<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Notifications\DatabaseNotification;
use Illuminate\Support\Str;
use Tests\TestCase;

/**
 * Notificaciones in-app + push subscriptions.
 *  - Index lista las propias
 *  - markRead / markAllRead funcionan
 *  - Auth check (no leer ajenas)
 *  - markRead sanitiza redirect URL (anti open-redirect)
 *  - Push subscribe/unsubscribe
 */
class NotificationsTest extends TestCase
{
    use RefreshDatabase;

    private function makeNotification(User $user, ?string $actionUrl = '/profile', bool $read = false): DatabaseNotification
    {
        return DatabaseNotification::create([
            'id'              => (string) Str::uuid(),
            'type'            => 'App\\Notifications\\Test',
            'notifiable_type' => User::class,
            'notifiable_id'   => $user->id,
            'data'            => ['message' => 'Hola', 'action_url' => $actionUrl],
            'read_at'         => $read ? now() : null,
        ]);
    }

    public function test_user_can_view_their_notifications_index(): void
    {
        $user = User::factory()->create(['is_active' => true]);
        $this->makeNotification($user);

        $this->actingAs($user)
            ->get(route('notifications.index'))
            ->assertOk();
    }

    public function test_user_can_mark_notification_as_read(): void
    {
        $user = User::factory()->create(['is_active' => true]);
        $notif = $this->makeNotification($user, '/profile');

        $this->actingAs($user)
            ->post(route('notifications.read', $notif->id))
            ->assertRedirect('/profile');

        $this->assertNotNull($notif->fresh()->read_at);
    }

    public function test_user_cannot_mark_others_notification_as_read(): void
    {
        $userA = User::factory()->create(['is_active' => true]);
        $userB = User::factory()->create(['is_active' => true]);
        $notif = $this->makeNotification($userA);

        $this->actingAs($userB)
            ->post(route('notifications.read', $notif->id))
            ->assertForbidden();

        $this->assertNull($notif->fresh()->read_at);
    }

    public function test_mark_all_read_marks_unread_notifications(): void
    {
        $user = User::factory()->create(['is_active' => true]);
        $n1 = $this->makeNotification($user);
        $n2 = $this->makeNotification($user);
        $n3 = $this->makeNotification($user, read: true);

        $this->actingAs($user)
            ->post(route('notifications.mark_all_read'))
            ->assertRedirect();

        $this->assertNotNull($n1->fresh()->read_at);
        $this->assertNotNull($n2->fresh()->read_at);
        $this->assertNotNull($n3->fresh()->read_at);
    }

    // ─── Anti open-redirect ──────────────────────────────────────────────

    public function test_mark_read_rejects_protocol_relative_url(): void
    {
        $user = User::factory()->create(['is_active' => true]);
        // action_url malicioso protocol-relative
        $notif = $this->makeNotification($user, '//evil.com/steal');

        $this->actingAs($user)
            ->post(route('notifications.read', $notif->id))
            ->assertRedirect('/'); // sanitizado a /
    }

    public function test_mark_read_rejects_external_absolute_url(): void
    {
        $user = User::factory()->create(['is_active' => true]);
        $notif = $this->makeNotification($user, 'https://evil.com/phish');

        $this->actingAs($user)
            ->post(route('notifications.read', $notif->id))
            ->assertRedirect('/');
    }

    public function test_mark_read_accepts_relative_path(): void
    {
        $user = User::factory()->create(['is_active' => true]);
        $notif = $this->makeNotification($user, '/my-reservations');

        $this->actingAs($user)
            ->post(route('notifications.read', $notif->id))
            ->assertRedirect('/my-reservations');
    }

    public function test_unauthenticated_users_cannot_use_notifications(): void
    {
        $this->get(route('notifications.index'))->assertRedirect(route('login'));
    }

    // ─── Push subscriptions ─────────────────────────────────────────────

    public function test_user_can_subscribe_to_push_notifications(): void
    {
        $user = User::factory()->create(['is_active' => true]);

        $this->actingAs($user)
            ->postJson(route('push.subscribe'), [
                'endpoint' => 'https://fcm.googleapis.com/fcm/send/test-endpoint',
                'keys'     => [
                    'auth'   => 'test-auth-key',
                    'p256dh' => 'test-p256dh-key',
                ],
            ])
            ->assertOk()
            ->assertJson(['ok' => true]);
    }

    public function test_push_subscription_validates_required_fields(): void
    {
        $user = User::factory()->create(['is_active' => true]);

        $this->actingAs($user)
            ->postJson(route('push.subscribe'), [])
            ->assertStatus(422)
            ->assertJsonValidationErrors(['endpoint', 'keys.auth', 'keys.p256dh']);
    }

    public function test_user_can_unsubscribe_from_push(): void
    {
        $user = User::factory()->create(['is_active' => true]);
        $endpoint = 'https://fcm.googleapis.com/fcm/send/test-endpoint';

        // Suscribir primero
        $this->actingAs($user)->postJson(route('push.subscribe'), [
            'endpoint' => $endpoint,
            'keys'     => ['auth' => 'a', 'p256dh' => 'p'],
        ]);

        $this->actingAs($user)
            ->deleteJson(route('push.unsubscribe'), ['endpoint' => $endpoint])
            ->assertOk()
            ->assertJson(['ok' => true]);
    }
}
