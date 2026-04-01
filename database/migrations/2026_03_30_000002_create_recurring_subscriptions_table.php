<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('recurring_subscriptions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('field_id')->constrained()->cascadeOnDelete();
            $table->string('status', 20)->default('PENDING_PAYMENT');
            $table->string('frequency', 10);
            $table->tinyInteger('day_of_week');
            $table->time('start_time');
            $table->smallInteger('slot_minutes')->unsigned();
            $table->decimal('monthly_amount', 12, 2);
            $table->string('currency', 3)->default('ARS');
            $table->string('mp_preapproval_id', 100)->nullable()->unique();
            $table->string('mp_subscription_status', 30)->nullable();
            $table->string('payment_mp_token_owner', 10)->nullable();
            $table->date('next_billing_date')->nullable();
            $table->date('subscription_starts_at')->nullable();
            $table->timestamp('cancelled_at')->nullable();
            $table->string('cancel_reason', 100)->nullable();
            $table->string('last_payment_id', 100)->nullable();
            $table->timestamp('last_payment_at')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'status']);
            $table->index(['field_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('recurring_subscriptions');
    }
};
