<?php

namespace App\Providers;

use App\Events\NotificationCreated;
use App\Models\Venue;
use App\Observers\VenueObserver;
use GuzzleHttp\Client as GuzzleClient;
use Illuminate\Routing\UrlGenerator;
use Illuminate\Support\ServiceProvider;
use Minishlink\WebPush\WebPush;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        if (config('app.env') !== 'production') {
            $this->app->extend(WebPush::class, function (WebPush $webPush, $app) {
                $client = new GuzzleClient(['verify' => false]);
                $reflection = new \ReflectionProperty(WebPush::class, 'client');
                $reflection->setAccessible(true);
                $reflection->setValue($webPush, $client);

                return $webPush;
            });
        }
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(UrlGenerator $url): void
    {
        if (str_starts_with(config('app.url'), 'https://')) {
            $url->forceScheme('https');
        }

        Venue::observe(VenueObserver::class);

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
