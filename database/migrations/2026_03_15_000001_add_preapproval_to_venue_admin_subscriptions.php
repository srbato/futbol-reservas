<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('venue_admin_subscriptions', function (Blueprint $table) {
            $table->string('mp_preapproval_id')->nullable()->after('mp_preference_id');
            $table->string('mp_subscription_status')->nullable()->after('mp_preapproval_id');
        });
    }

    public function down(): void
    {
        Schema::table('venue_admin_subscriptions', function (Blueprint $table) {
            $table->dropColumn(['mp_preapproval_id', 'mp_subscription_status']);
        });
    }
};
