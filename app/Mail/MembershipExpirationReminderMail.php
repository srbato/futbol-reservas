<?php

namespace App\Mail;

use App\Models\VenueAdminSubscription;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class MembershipExpirationReminderMail extends Mailable
{
    use Queueable, SerializesModels;

    public VenueAdminSubscription $subscription;
    public int $daysRemaining;

    public function __construct(VenueAdminSubscription $subscription, int $daysRemaining)
    {
        $this->subscription = $subscription;
        $this->daysRemaining = $daysRemaining;
    }

    public function build(): static
    {
        $subject = match ($this->daysRemaining) {
            7 => 'Tu membresía vence en 7 días',
            2 => 'Tu membresía vence en 2 días',
            0 => 'Tu membresía vence hoy',
            default => 'Recordatorio de vencimiento de membresía',
        };

        return $this
            ->subject($subject)
            ->view('emails.membership.expiration-reminder');
    }
}