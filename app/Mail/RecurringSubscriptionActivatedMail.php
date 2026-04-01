<?php

namespace App\Mail;

use App\Models\RecurringSubscription;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class RecurringSubscriptionActivatedMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public RecurringSubscription $subscription,
    ) {}

    public function build(): static
    {
        return $this
            ->subject('Tu suscripción mensual fue activada')
            ->view('emails.recurring-subscription.activated');
    }
}
