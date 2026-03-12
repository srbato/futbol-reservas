<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('venue_admin_subscriptions', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();

            $table->string('status', 30)->default('PENDING_PAYMENT');
            $table->decimal('monthly_price', 12, 2)->default(0);
            $table->string('currency', 3)->default('ARS');

            $table->string('payment_provider', 40)->nullable();
            $table->string('payment_external_id')->nullable();
            $table->string('payment_status', 40)->nullable();
            $table->string('mp_preference_id')->nullable();

            $table->dateTime('starts_at')->nullable();
            $table->dateTime('expires_at')->nullable();

            $table->timestamps();

            $table->index(['user_id', 'status']);
            $table->index(['expires_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('venue_admin_subscriptions');
    }
};
