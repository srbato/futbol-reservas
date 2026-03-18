<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('membership_plans', function (Blueprint $table) {
            $table->unsignedTinyInteger('long_term_months')->default(12)->after('annual_discount_percentage');
        });

        Schema::table('venue_admin_subscriptions', function (Blueprint $table) {
            $table->unsignedTinyInteger('long_term_months')->default(12)->after('billing_cycle');
        });
    }

    public function down(): void
    {
        Schema::table('membership_plans', function (Blueprint $table) {
            $table->dropColumn('long_term_months');
        });

        Schema::table('venue_admin_subscriptions', function (Blueprint $table) {
            $table->dropColumn('long_term_months');
        });
    }
};
