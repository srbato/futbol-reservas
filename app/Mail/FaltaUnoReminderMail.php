<?php

namespace App\Mail;

use App\Models\FaltaUnoGame;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class FaltaUnoReminderMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public readonly FaltaUnoGame $game,
        public readonly User $recipient,
    ) {}

    public function envelope(): Envelope
    {
        $sport     = ucfirst($this->game->field->sport ?? 'deporte');
        $remaining = $this->game->remainingSlots();
        $plural    = $remaining === 1 ? 'jugador' : 'jugadores';

        return new Envelope(
            subject: "⏰ ¡Último momento! Falta {$remaining} {$plural} para un partido de {$sport}",
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.falta-uno.reminder',
        );
    }
}
