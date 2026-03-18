<?php

namespace App\Mail;

use App\Models\VenueAdminSubscription;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class TrialEndingMail extends Mailable
{
    use Queueable, SerializesModels;

    public VenueAdminSubscription $subscription;

    public function __construct(VenueAdminSubscription $subscription)
    {
        $this->subscription = $subscription;
    }

    public function build(): static
    {
        return $this
            ->subject('Tu período de prueba en TuCancha vence hoy')
            ->view('emails.membership.trial-ending');
    }
}
