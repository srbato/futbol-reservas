<?php

namespace App\Mail;

use App\Models\ReferralReward;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ReferralRewardMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public User $referrer,
        public ReferralReward $reward,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(subject: '¡Ganaste una recompensa por tu referido en TuCancha!');
    }

    public function content(): Content
    {
        return new Content(view: 'emails.referral.reward');
    }
}
