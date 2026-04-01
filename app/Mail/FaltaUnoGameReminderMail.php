<?php

namespace App\Mail;

use App\Models\FaltaUnoGame;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class FaltaUnoGameReminderMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public FaltaUnoGame $game,
        public User $recipient,
    ) {}

    public function build(): static
    {
        return $this
            ->subject('Tu partido es en 2 horas')
            ->view('emails.falta-uno.game-reminder');
    }
}
