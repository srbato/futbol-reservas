<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;

class InstagramPostMail extends Mailable
{
    use Queueable, SerializesModels;

    public string $caption;
    public string $imagePath;
    public string $topic;
    public string $imageUrl;

    public function __construct(string $caption, string $imagePath, string $topic)
    {
        $this->caption = $caption;
        $this->imagePath = $imagePath;
        $this->topic = $topic;
        $this->imageUrl = url('/storage/' . $imagePath);
    }

    public function build(): static
    {
        $mail = $this
            ->subject('Instagram post listo — ' . ucfirst($this->topic))
            ->view('emails.instagram-post');

        // Adjuntar la imagen para descargar directo
        $fullPath = Storage::disk('public')->path($this->imagePath);
        if (file_exists($fullPath)) {
            $mail->attach($fullPath, [
                'as' => 'instagram-post.png',
                'mime' => 'image/png',
            ]);
        }

        return $mail;
    }
}
