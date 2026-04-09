<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Collection;

class InactiveVenueAlertMail extends Mailable
{
    use Queueable, SerializesModels;

    public Collection $venues;

    public function __construct(Collection $venues)
    {
        $this->venues = $venues;
    }

    public function build(): static
    {
        return $this
            ->subject('Complejos inactivos — ' . $this->venues->count() . ' sin reservas')
            ->view('emails.inactive-venues');
    }
}
