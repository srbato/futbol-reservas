<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// onOneServer() previene ejecución múltiple en setups multi-server.
// Hoy TuCancha corre en un solo VPS pero si se escala, esto evita duplicados.

Schedule::command('reservations:expire')
    ->everyMinute()
    ->withoutOverlapping()
    ->onOneServer();

Schedule::command('membership:send-expiration-reminders')
    ->dailyAt('09:00')
    ->withoutOverlapping()
    ->onOneServer();

Schedule::command('membership:process-trials')
    ->dailyAt('08:00')
    ->withoutOverlapping()
    ->onOneServer();

Schedule::command('reservations:send-reminders')
    ->everyMinute()
    ->withoutOverlapping()
    ->onOneServer();

Schedule::command('reservations:purge-dead')
    ->monthlyOn(1, '03:00')
    ->withoutOverlapping()
    ->onOneServer();

Schedule::command('falta-uno:process-expired')
    ->everyFiveMinutes()
    ->withoutOverlapping()
    ->onOneServer();

Schedule::command('falta-uno:send-reminders')
    ->everyFiveMinutes()
    ->withoutOverlapping()
    ->onOneServer();

Schedule::command('falta-uno:purge-chats')
    ->hourly()
    ->withoutOverlapping()
    ->onOneServer();

Schedule::command('falta-uno:notify-post-game')
    ->everyFiveMinutes()
    ->withoutOverlapping()
    ->onOneServer();

Schedule::command('subscriptions:reconcile')
    ->daily()
    ->withoutOverlapping()
    ->onOneServer();

Schedule::command('reviews:send-reminders')
    ->hourly()
    ->withoutOverlapping()
    ->onOneServer();

Schedule::command('reservations:notify-post-game')
    ->everyFiveMinutes()
    ->withoutOverlapping()
    ->onOneServer();

Schedule::command('app:send-reengagement-emails')
    ->dailyAt('10:00')
    ->withoutOverlapping()
    ->onOneServer();

Schedule::command('app:alert-inactive-venues')
    ->weeklyOn(1, '09:00') // Lunes 9am
    ->withoutOverlapping()
    ->onOneServer();

Schedule::command('app:generate-instagram-post')
    ->days([1, 3, 5]) // Lunes, Miercoles, Viernes
    ->at('10:00')
    ->withoutOverlapping()
    ->onOneServer();
