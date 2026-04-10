<?php

namespace Database\Seeders;

use App\Models\OrganizerPlan;
use Illuminate\Database\Seeder;

class OrganizerPlanSeeder extends Seeder
{
    public function run(): void
    {
        OrganizerPlan::updateOrCreate(
            ['slug' => 'free'],
            [
                'name' => 'Gratis',
                'monthly_price' => 0,
                'max_tournaments' => 1,
                'max_teams_per_tournament' => 8,
                'available_formats' => ['single_elimination'],
                'has_stats' => false,
                'has_notifications' => false,
                'has_custom_branding' => false,
                'has_mp_payments' => false,
                'is_active' => true,
                'sort_order' => 0,
            ]
        );

        OrganizerPlan::updateOrCreate(
            ['slug' => 'pro'],
            [
                'name' => 'Pro',
                'monthly_price' => 9999,
                'max_tournaments' => -1,
                'max_teams_per_tournament' => 128,
                'available_formats' => ['single_elimination', 'round_robin', 'groups_elimination'],
                'has_stats' => true,
                'has_notifications' => true,
                'has_custom_branding' => true,
                'has_mp_payments' => true,
                'is_active' => true,
                'sort_order' => 1,
            ]
        );
    }
}
