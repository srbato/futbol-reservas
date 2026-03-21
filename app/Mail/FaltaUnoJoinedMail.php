<?php

namespace App\Mail;

use App\Models\FaltaUnoGame;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class FaltaUnoJoinedMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public FaltaUnoGame $game,
        public User $joiner,
    ) {}

    public function build(): static
    {
        return $this
            ->subject('Alguien se unió a tu partido')
            ->view('emails.falta-uno.joined');
    }
}
