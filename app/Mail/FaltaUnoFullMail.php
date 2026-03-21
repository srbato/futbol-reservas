<?php

namespace App\Mail;

use App\Models\FaltaUnoGame;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class FaltaUnoFullMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public FaltaUnoGame $game) {}

    public function build(): static
    {
        return $this
            ->subject('¡Tu partido está completo!')
            ->view('emails.falta-uno.full');
    }
}
