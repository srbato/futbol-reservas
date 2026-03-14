<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('membership_plans', function (Blueprint $table) {
            $table->id();
            $table->string('slug', 50)->unique();
            $table->string('name', 100);
            $table->unsignedSmallInteger('max_fields')->nullable();
            $table->decimal('monthly_price', 10, 2);
            $table->decimal('annual_discount_percentage', 5, 2)->default(20);
            $table->boolean('is_active')->default(true);
            $table->boolean('is_featured')->default(false);
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->timestamps();
        });

        // Seed default plans
        DB::table('membership_plans')->insert([
            [
                'slug'                       => 'starter',
                'name'                       => 'Starter',
                'max_fields'                 => 2,
                'monthly_price'              => 9990,
                'annual_discount_percentage' => 20,
                'is_active'                  => true,
                'is_featured'                => false,
                'sort_order'                 => 1,
                'created_at'                 => now(),
                'updated_at'                 => now(),
            ],
            [
                'slug'                       => 'pro',
                'name'                       => 'Pro',
                'max_fields'                 => 5,
                'monthly_price'              => 19990,
                'annual_discount_percentage' => 20,
                'is_active'                  => true,
                'is_featured'                => true,
                'sort_order'                 => 2,
                'created_at'                 => now(),
                'updated_at'                 => now(),
            ],
            [
                'slug'                       => 'full',
                'name'                       => 'Full',
                'max_fields'                 => null,
                'monthly_price'              => 34990,
                'annual_discount_percentage' => 20,
                'is_active'                  => true,
                'is_featured'                => false,
                'sort_order'                 => 3,
                'created_at'                 => now(),
                'updated_at'                 => now(),
            ],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('membership_plans');
    }
};
