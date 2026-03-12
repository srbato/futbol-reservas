<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('field_discounts', function (Blueprint $table) {
            $table->id();

            $table->foreignId('field_id')
                ->constrained('fields')
                ->cascadeOnDelete();

            // Opcional: si querés que aplique a un día fijo de la semana
            $table->unsignedTinyInteger('day_of_week')->nullable(); // 0=dom ... 6=sab

            // Opcional: si querés que aplique a una fecha puntual
            $table->date('date')->nullable();

            // Si querés horario específico
            $table->time('start_time')->nullable();
            $table->time('end_time')->nullable();

            // Precio promocional final
            $table->decimal('discount_price', 10, 2);

            $table->string('label')->nullable(); // Ej: Promo tarde / Happy hour

            $table->boolean('is_active')->default(true);

            $table->timestamps();

            $table->index(['field_id', 'day_of_week']);
            $table->index(['field_id', 'date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('field_discounts');
    }
};
