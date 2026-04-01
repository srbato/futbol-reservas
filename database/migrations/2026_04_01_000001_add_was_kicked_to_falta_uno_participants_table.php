<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('falta_uno_participants', function (Blueprint $table) {
            $table->boolean('was_kicked')->default(false)->after('is_late_leave');
        });
    }

    public function down(): void
    {
        Schema::table('falta_uno_participants', function (Blueprint $table) {
            $table->dropColumn('was_kicked');
        });
    }
};
