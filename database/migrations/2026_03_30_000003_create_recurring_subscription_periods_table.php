<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('recurring_subscription_periods', function (Blueprint $table) {
            $table->id();
            $table->foreignId('recurring_subscription_id')
                ->constrained('recurring_subscriptions')
                ->cascadeOnDelete();
            $table->string('mp_authorized_payment_id', 100)->unique();
            $table->date('period_start');
            $table->date('period_end');
            $table->decimal('amount_charged', 12, 2);
            $table->string('status', 10)->default('PAID');
            $table->unsignedSmallInteger('reservations_created')->default(0);
            $table->timestamps();

            $table->index(['recurring_subscription_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('recurring_subscription_periods');
    }
};
