<?php

namespace App\Mail;

use App\Models\ReservationBatch;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class VenueAdminBatchReservationMail extends Mailable
{
    use Queueable, SerializesModels;

    public ReservationBatch $batch;

    public function __construct(ReservationBatch $batch)
    {
        $this->batch = $batch;
    }

    public function build(): static
    {
        return $this
            ->subject('Nuevas reservas recurrentes en tu cancha')
            ->view('emails.batches.venue_admin');
    }
}
