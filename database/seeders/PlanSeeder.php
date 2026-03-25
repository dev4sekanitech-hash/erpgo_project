<?php

namespace Database\Seeders;

use App\Models\Plan;
use Illuminate\Database\Seeder;

class PlanSeeder extends Seeder
{
    public function run(): void
    {
        $allModules = [
            "AIAssistant","Account","Accounting","BudgetPlanner","Calendar","Contract",
            "DoubleEntry","FormBuilder","Goal","GoogleCaptcha","Hrm","LandingPage","Lead",
            "Paypal","Performance","Pos","ProductService","Quotation","Recruitment","Slack",
            "Stripe","SupportTicket","Taskly","Telegram","Timesheet","Training","Twilio",
            "Webhook","ZoomMeeting",
        ];

        $plans = [
            [
                'name' => 'Enterprise',
                'description' => 'All modules, unlimited users — full access',
                'number_of_users' => -1,
                'status' => true,
                'free_plan' => true,
                'modules' => $allModules,
                'package_price_yearly' => 0,
                'package_price_monthly' => 0,
                'storage_limit' => 0,
                'trial' => false,
                'trial_days' => 0,
                'created_by' => 1,
            ],
            [
                'name' => 'Free Plan',
                'description' => 'Perfect for getting started with basic features',
                'number_of_users' => 10,
                'status' => true,
                'free_plan' => true,
                'modules' => $allModules,
                'package_price_yearly' => 0,
                'package_price_monthly' => 0,
                'trial' => false,
                'trial_days' => 0,
                'created_by' => 1,
            ],
            [
                'name' => 'Starter Plan',
                'description' => 'Great for small teams and growing businesses',
                'number_of_users' => 50,
                'status' => true,
                'free_plan' => false,
                'modules' => $allModules,
                'package_price_yearly' => 240,
                'package_price_monthly' => 25,
                'trial' => true,
                'trial_days' => 14,
                'created_by' => 1,
            ],
            [
                'name' => 'Professional Plan',
                'description' => 'Advanced features for established businesses',
                'number_of_users' => 100,
                'status' => true,
                'free_plan' => false,
                'modules' => $allModules,
                'package_price_yearly' => 960,
                'package_price_monthly' => 99,
                'trial' => true,
                'trial_days' => 30,
                'created_by' => 1,
            ],
        ];

        $plan = Plan::first();
        if (!$plan) {
            foreach ($plans as $plan) {
                Plan::firstOrCreate(
                    ['name' => $plan['name']],
                    $plan
                );
            }
        }
    }
}
