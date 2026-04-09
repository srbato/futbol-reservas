<?php

namespace App\Listeners;

use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SendNewUserWebhook
{
    public function handle(Registered $event): void
    {
        $webhookUrl = config('services.n8n.new_user_webhook');

        if (!$webhookUrl) {
            return;
        }

        try {
            Http::timeout(5)->post($webhookUrl, [
                'event'      => 'user.registered',
                'user_id'    => $event->user->id,
                'name'       => $event->user->name,
                'email'      => $event->user->email,
                'registered_at' => $event->user->created_at->toIso8601String(),
                'source'     => $event->user->google_id ? 'google' : 'email',
            ]);
        } catch (\Throwable $e) {
            Log::warning('n8n webhook failed: ' . $e->getMessage());
        }
    }
}
