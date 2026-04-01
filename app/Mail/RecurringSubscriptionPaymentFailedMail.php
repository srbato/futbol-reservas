<?php

namespace App\Mail;

use App\Models\RecurringSubscription;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class RecurringSubscriptionPaymentFailedMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public RecurringSubscription $subscription,
    ) {}

    public function build(): static
    {
        return $this
            ->subject('Tu pago mensual no pudo procesarse')
            ->view('emails.recurring-subscription.payment-failed');
    }
}
