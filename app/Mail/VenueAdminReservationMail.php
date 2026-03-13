<?php

namespace App\Mail;

use App\Models\Reservation;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class VenueAdminReservationMail extends Mailable
{
    use Queueable, SerializesModels;

    public Reservation $reservation;

    public function __construct(Reservation $reservation)
    {
        $this->reservation = $reservation;
    }

    public function build(): static
    {
        return $this
            ->subject('Nueva reserva en ' . $this->reservation->field->venue->name)
            ->view('emails.reservations.venue_admin_notification');
    }
}