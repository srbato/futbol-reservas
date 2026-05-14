<?php

namespace Tests\Feature\Admin;

use App\Mail\VenueStaffInvitationMail;
use App\Models\User;
use App\Models\VenueStaff;
use App\Models\VenueStaffInvitation;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\Concerns\MakesVenues;
use Tests\TestCase;

/**
 * Cobertura del flujo de invitaciones a empleados (staff) de un complejo.
 *  - Owner invita por email → genera invitation token
 *  - Validaciones: usuario debe existir, no auto-invitarse, no invitar venue_admin
 *  - No re-invitar si ya es staff
 *  - Updates de permisos
 *  - Remove staff
 *  - Mail de invitación enviado
 */
class StaffInvitationTest extends TestCase
{
    use RefreshDatabase, MakesVenues;

    protected function setUp(): void
    {
        parent::setUp();
        Mail::fake();
    }

    public function test_owner_can_invite_existing_user_to_be_staff(): void
    {
        $venue   = $this->makeVenue();
        $owner   = $venue->owner;
        $invitee = $this->makeUser(['role' => 'user']);

        $this->actingAs($owner)
            ->post(route('va.staff.invite'), [
                'venue_id'    => $venue->id,
                'email'       => $invitee->email,
                'permissions' => ['view_reservations', 'manage_blocks'],
            ])
            ->assertSessionHas('success');

        $this->assertDatabaseHas('venue_staff_invitations', [
            'venue_id'   => $venue->id,
            'user_id'    => $invitee->id,
            'invited_by' => $owner->id,
            'status'     => 'pending',
        ]);

        $invitation = VenueStaffInvitation::first();
        $this->assertSame(['view_reservations', 'manage_blocks'], $invitation->permissions);
        $this->assertNotEmpty($invitation->token);

        Mail::assertSent(VenueStaffInvitationMail::class);
    }

    public function test_invitation_fails_when_email_does_not_match_existing_user(): void
    {
        $venue = $this->makeVenue();
        $owner = $venue->owner;

        $this->actingAs($owner)
            ->post(route('va.staff.invite'), [
                'venue_id' => $venue->id,
                'email'    => 'noexiste@test.com',
            ])
            ->assertSessionHasErrors(['email']);

        $this->assertDatabaseCount('venue_staff_invitations', 0);
        Mail::assertNothingSent();
    }

    public function test_owner_cannot_invite_themselves(): void
    {
        $venue = $this->makeVenue();
        $owner = $venue->owner;

        $this->actingAs($owner)
            ->post(route('va.staff.invite'), [
                'venue_id' => $venue->id,
                'email'    => $owner->email,
            ])
            ->assertSessionHasErrors(['email']);
    }

    public function test_cannot_invite_a_venue_admin_or_super_admin(): void
    {
        $venue = $this->makeVenue();
        $owner = $venue->owner;
        $other_admin = $this->makeUser(['role' => 'venue_admin']);

        $this->actingAs($owner)
            ->post(route('va.staff.invite'), [
                'venue_id' => $venue->id,
                'email'    => $other_admin->email,
            ])
            ->assertSessionHasErrors(['email']);
    }

    public function test_cannot_invite_existing_staff(): void
    {
        $venue = $this->makeVenue();
        $owner = $venue->owner;
        $existingStaff = $this->makeUser(['role' => 'user']);

        VenueStaff::create([
            'venue_id'    => $venue->id,
            'user_id'     => $existingStaff->id,
            'permissions' => ['view_reservations'],
        ]);

        $this->actingAs($owner)
            ->post(route('va.staff.invite'), [
                'venue_id' => $venue->id,
                'email'    => $existingStaff->email,
            ])
            ->assertSessionHasErrors(['email']);
    }

    public function test_inviting_again_replaces_previous_pending_invitation(): void
    {
        $venue   = $this->makeVenue();
        $owner   = $venue->owner;
        $invitee = $this->makeUser(['role' => 'user']);

        // Primera invitación
        $this->actingAs($owner)->post(route('va.staff.invite'), [
            'venue_id' => $venue->id,
            'email'    => $invitee->email,
        ]);

        // Segunda → debe reemplazar (no acumular)
        $this->actingAs($owner)->post(route('va.staff.invite'), [
            'venue_id' => $venue->id,
            'email'    => $invitee->email,
        ]);

        $this->assertSame(1, VenueStaffInvitation::where('user_id', $invitee->id)->count());
    }

    public function test_owner_can_remove_staff(): void
    {
        $venue   = $this->makeVenue();
        $owner   = $venue->owner;
        $staffUser = $this->makeUser(['role' => 'user']);
        VenueStaff::create([
            'venue_id'    => $venue->id,
            'user_id'     => $staffUser->id,
            'permissions' => ['view_reservations'],
        ]);

        $this->actingAs($owner)
            ->post(route('va.staff.remove'), [
                'venue_id' => $venue->id,
                'user_id'  => $staffUser->id,
            ])
            ->assertSessionHas('success');

        $this->assertDatabaseMissing('venue_staff', [
            'venue_id' => $venue->id,
            'user_id'  => $staffUser->id,
        ]);
    }

    public function test_owner_can_update_staff_permissions(): void
    {
        $venue = $this->makeVenue();
        $owner = $venue->owner;
        $staff = VenueStaff::create([
            'venue_id'    => $venue->id,
            'user_id'     => $this->makeUser()->id,
            'permissions' => ['view_reservations'],
        ]);

        $this->actingAs($owner)
            ->post(route('va.staff.update_permissions', $staff), [
                'permissions' => ['view_reservations', 'manage_blocks', 'create_manual_reservations'],
            ])
            ->assertSessionHas('success');

        $staff->refresh();
        $this->assertCount(3, $staff->permissions);
        $this->assertContains('manage_blocks', $staff->permissions);
    }

    public function test_other_users_cannot_invite_staff_to_other_venues(): void
    {
        $venue   = $this->makeVenue();
        $invitee = $this->makeUser(['role' => 'user']);
        $stranger = $this->makeUser(['role' => 'venue_admin']);

        $resp = $this->actingAs($stranger)
            ->post(route('va.staff.invite'), [
                'venue_id' => $venue->id,
                'email'    => $invitee->email,
            ]);

        // 302 redirect (firstOrFail → 404 wrapped) o 404
        $this->assertContains($resp->status(), [302, 403, 404]);
        $this->assertDatabaseCount('venue_staff_invitations', 0);
    }
}
