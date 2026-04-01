<?php

namespace App\Mail;

use App\Models\RecurringSubscription;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class VenueAdminRecurringSubscriptionActivatedMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public RecurringSubscription $subscription,
    ) {}

    public function build(): static
    {
        return $this
            ->subject('Nueva suscripción mensual en tu complejo')
            ->view('emails.recurring-subscription.venue-admin-activated');
    }
}
