<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class FeedbackMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public readonly string $feedbackMessage,
        public readonly ?string $senderEmail = null,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Nuevo feedback — TuCancha',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.feedback',
        );
    }
}
