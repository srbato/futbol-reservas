<?php

namespace App\Mail;

use App\Models\Reservation;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ReservationModifiedMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public readonly Reservation $reservation,
        public readonly float $refundAmount,
        public readonly ?bool $refundResult,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "Tu reserva #{$this->reservation->id} fue modificada — TuCancha",
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.reservation-modified',
        );
    }
}
