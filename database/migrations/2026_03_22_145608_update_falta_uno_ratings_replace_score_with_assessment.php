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
        Schema::table('falta_uno_ratings', function (Blueprint $table) {
            $table->dropColumn('score');
            $table->enum('assessment', ['below', 'match', 'above'])->after('rated_user_id');
        });
    }

    public function down(): void
    {
        Schema::table('falta_uno_ratings', function (Blueprint $table) {
            $table->dropColumn('assessment');
            $table->unsignedTinyInteger('score')->after('rated_user_id');
        });
    }
};
