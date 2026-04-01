<?php

namespace App\Mail;

use App\Models\FaltaUnoGame;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class FaltaUnoKickedMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public FaltaUnoGame $game,
        public User $kickedUser,
    ) {}

    public function build(): static
    {
        return $this
            ->subject('Fuiste removido de un partido de Falta Uno')
            ->view('emails.falta-uno.kicked');
    }
}
