<?php

namespace App\Console\Commands;

use App\Models\RecurringSubscription;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class ReconcileRecurringSubscriptions extends Command
{
    protected $signature   = 'subscriptions:reconcile';
    protected $description = 'Detecta suscripciones ACTIVE cuyo next_billing_date paso hace mas de 3 dias sin cobro registrado';

    public function handle(): void
    {
        $threshold = now()->subDays(3);

        $stale = RecurringSubscription::where('status', 'ACTIVE')
            ->where('next_billing_date', '<', $threshold)
            ->get();

        foreach ($stale as $sub) {
            Log::warning('RecurringSubscription: cobro esperado no recibido', [
                'subscription_id'   => $sub->id,
                'user_id'           => $sub->user_id,
                'field_id'          => $sub->field_id,
                'next_billing_date' => $sub->next_billing_date,
                'last_payment_at'   => $sub->last_payment_at,
            ]);
        }

        $this->info("Reconciliacion completa. {$stale->count()} suscripcion(es) con cobro pendiente.");
    }
}
