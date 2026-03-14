<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reservation_batches', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('field_id')->constrained()->cascadeOnDelete();
            $table->decimal('subtotal', 12, 2);
            $table->decimal('discount_percentage', 5, 2)->default(0);
            $table->decimal('discount_amount', 12, 2)->default(0);
            $table->decimal('total_amount', 12, 2);
            $table->string('currency', 10)->default('ARS');
            $table->string('status', 30)->default('PENDING_PAYMENT');
            $table->string('payment_provider', 50)->nullable();
            $table->string('mp_preference_id')->nullable();
            $table->string('payment_external_id')->nullable();
            $table->string('payment_status', 50)->nullable();
            $table->string('payment_mp_token_owner', 20)->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reservation_batches');
    }
};
