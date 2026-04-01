<?php

namespace App\Mail;

use App\Models\FaltaUnoGame;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class FaltaUnoExpiredParticipantMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public FaltaUnoGame $game,
        public User $recipient,
    ) {}

    public function build(): static
    {
        return $this
            ->subject('El partido en el que estabas anotado venció')
            ->view('emails.falta-uno.expired-participant');
    }
}
