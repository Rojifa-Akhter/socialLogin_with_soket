<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class PlanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $plans = [
            [
                'name' => 'Personal',
                'price' => 47,
                'interval' => 'month',
                'trial_period_days' => 7,
                'lookup_key' => 'personal',
                'stripe_plan_id' => 'price_1QTgb0H4T1x8hb5hJGNUKUqL'  // Make sure this is a valid price ID
            ],
            [
                'name' => 'Business',
                'price' => 99,
                'interval' => 'month',
                'trial_period_days' => 15,
                'lookup_key' => 'business',
                'stripe_plan_id' => 'price_1QTgPOH4T1x8hb5htmLsFZwq'  // Correct price ID for 'business'
            ],
            [
                'name' => 'Expert',
                'price' => 197,
                'interval' => 'month',
                'trial_period_days' => 30,
                'lookup_key' => 'expert',
                'stripe_plan_id' => 'price_1QTdpBH4T1x8hb5hiE16YHyb'  // Correct price ID for 'expert'
            ]
        ];

        foreach ($plans as $plan) {
            \App\Models\Plan::create($plan);
        }
    }
}
