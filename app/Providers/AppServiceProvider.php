<?php

namespace App\Providers;

use App\Events\NotificationCreated;
use Illuminate\Routing\UrlGenerator;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(UrlGenerator $url): void
    {
        if (config('app.env') !== 'production') {
            $url->forceScheme('https');
        }

        \Illuminate\Notifications\DatabaseNotification::created(function ($notification) {
            try {
                $user = $notification->notifiable;
                if ($user) {
                    broadcast(new NotificationCreated(
                        $user->id,
                        $user->unreadNotifications()->count(),
                    ));
                }
            } catch (\Throwable $e) {
                \Illuminate\Support\Facades\Log::warning('NotificationCreated broadcast failed', [
                    'error' => $e->getMessage(),
                ]);
            }
        });
    }
}
