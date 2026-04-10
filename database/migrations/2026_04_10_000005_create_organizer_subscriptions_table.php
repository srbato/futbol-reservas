<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('organizer_subscriptions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('plan')->default('free');
            $table->string('status')->default('ACTIVE');
            $table->decimal('monthly_price', 12, 2)->nullable();
            $table->string('currency', 10)->default('ARS');
            $table->string('mp_preapproval_id')->nullable();
            $table->string('mp_subscription_status')->nullable();
            $table->datetime('trial_ends_at')->nullable();
            $table->datetime('starts_at')->nullable();
            $table->datetime('expires_at')->nullable();
            $table->datetime('cancelled_at')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('organizer_subscriptions');
    }
};
