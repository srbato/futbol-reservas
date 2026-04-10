<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tournaments', function (Blueprint $table) {
            $table->foreignId('venue_id')->nullable()->after('organizer_user_id')->constrained()->onDelete('set null');
            $table->foreignId('field_id')->nullable()->after('venue_id')->constrained()->onDelete('set null');
            $table->string('venue_approval_status')->nullable()->after('status'); // pending, approved, rejected
            $table->datetime('venue_approved_at')->nullable()->after('venue_approval_status');
            $table->text('venue_rejection_reason')->nullable()->after('venue_approved_at');
        });
    }

    public function down(): void
    {
        Schema::table('tournaments', function (Blueprint $table) {
            $table->dropForeign(['venue_id']);
            $table->dropForeign(['field_id']);
            $table->dropColumn([
                'venue_id',
                'field_id',
                'venue_approval_status',
                'venue_approved_at',
                'venue_rejection_reason',
            ]);
        });
    }
};
