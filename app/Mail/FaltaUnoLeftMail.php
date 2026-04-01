<?php

namespace App\Mail;

use App\Models\FaltaUnoGame;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class FaltaUnoLeftMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public FaltaUnoGame $game,
        public User $leaver,
    ) {}

    public function build(): static
    {
        return $this
            ->subject('Un jugador se fue de tu partido')
            ->view('emails.falta-uno.left');
    }
}
