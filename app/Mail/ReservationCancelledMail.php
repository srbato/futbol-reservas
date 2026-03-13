<?php

namespace App\Mail;

use App\Models\Reservation;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ReservationCancelledMail extends Mailable
{
    use Queueable, SerializesModels;

    public Reservation $reservation;
    public string $recipient; // 'user' o 'admin'

    public function __construct(Reservation $reservation, string $recipient = 'user')
    {
        $this->reservation = $reservation;
        $this->recipient   = $recipient;
    }

    public function build(): static
    {
        $subject = $this->recipient === 'admin'
            ? 'Reserva cancelada en ' . $this->reservation->field->venue->name
            : 'Tu reserva fue cancelada';

        return $this
            ->subject($subject)
            ->view('emails.reservations.cancelled');
    }
}