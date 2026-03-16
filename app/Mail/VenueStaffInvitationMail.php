<?php

namespace App\Mail;

use App\Models\VenueStaffInvitation;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class VenueStaffInvitationMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public VenueStaffInvitation $invitation) {}

    public function build(): static
    {
        return $this
            ->subject('Te invitaron a trabajar en ' . $this->invitation->venue->name)
            ->view('emails.staff.invitation');
    }
}
