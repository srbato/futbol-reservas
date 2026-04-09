<?php

namespace App\Console\Commands;

use App\Mail\ReengagementMail;
use App\Models\Reservation;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class SendReengagementEmails extends Command
{
    protected $signature = 'app:send-reengagement-emails';
    protected $description = 'Manda mail a usuarios que reservaron antes pero no reservan hace 14 dias';

    public function handle(): void
    {
        // Usuarios que tienen al menos 1 reserva PAID pero su última reserva fue hace 14+ días
        $inactiveUsers = User::where('role', 'user')
            ->where('is_active', true)
            ->whereHas('reservations', function ($q) {
                $q->where('status', 'PAID');
            })
            ->whereDoesntHave('reservations', function ($q) {
                $q->where('status', 'PAID')
                  ->where('created_at', '>=', now()->subDays(14));
            })
            // No mandar si ya le mandamos uno en los últimos 30 días
            ->where(function ($q) {
                $q->whereNull('reengagement_sent_at')
                  ->orWhere('reengagement_sent_at', '<', now()->subDays(30));
            })
            ->limit(50) // No mandar más de 50 por día
            ->get();

        $count = 0;

        foreach ($inactiveUsers as $user) {
            try {
                Mail::to($user->email)->queue(new ReengagementMail($user));
                $user->update(['reengagement_sent_at' => now()]);
                $count++;
            } catch (\Throwable $e) {
                $this->error("Error enviando a {$user->email}: {$e->getMessage()}");
            }
        }

        $this->info("Re-engagement emails enviados: {$count}");
    }
}
