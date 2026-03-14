<?php

namespace App\Mail;

use App\Models\ReservationBatch;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class BatchReservationPaidMail extends Mailable
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
            ->subject('Reservas recurrentes confirmadas')
            ->view('emails.batches.paid');
    }
}
