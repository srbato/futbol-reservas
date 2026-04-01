<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Schedule::command('reservations:expire')
    ->everyMinute()
    ->withoutOverlapping();

Schedule::command('membership:send-expiration-reminders')
    ->dailyAt('09:00')
    ->withoutOverlapping();

Schedule::command('membership:process-trials')
    ->dailyAt('08:00')
    ->withoutOverlapping();

Schedule::command('reservations:send-reminders')
    ->everyMinute()
    ->withoutOverlapping();

Schedule::command('reservations:purge-dead')
    ->monthlyOn(1, '03:00')
    ->withoutOverlapping();

Schedule::command('falta-uno:process-expired')
    ->everyFiveMinutes()
    ->withoutOverlapping();

Schedule::command('falta-uno:send-reminders')
    ->everyFiveMinutes()
    ->withoutOverlapping();

Schedule::command('falta-uno:purge-chats')
    ->hourly()
    ->withoutOverlapping();

Schedule::command('falta-uno:notify-post-game')
    ->everyFiveMinutes()
    ->withoutOverlapping();

Schedule::command('subscriptions:reconcile')
    ->daily()
    ->withoutOverlapping();

Schedule::command('reviews:send-reminders')
    ->hourly()
    ->withoutOverlapping();