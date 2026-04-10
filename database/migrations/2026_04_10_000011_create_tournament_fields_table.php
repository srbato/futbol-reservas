<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tournament_fields', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tournament_id')->constrained()->cascadeOnDelete();
            $table->foreignId('field_id')->constrained()->cascadeOnDelete();
            $table->timestamps();

            $table->unique(['tournament_id', 'field_id']);
        });

        // Migrate existing data: if a tournament has field_id, create a pivot row
        $tournaments = DB::table('tournaments')->whereNotNull('field_id')->get();
        foreach ($tournaments as $t) {
            DB::table('tournament_fields')->insert([
                'tournament_id' => $t->id,
                'field_id' => $t->field_id,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('tournament_fields');
    }
};
