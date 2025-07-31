<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Services\WeatherConditionRuleService;

class WeatherConditionRuleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $weatherConditionRuleService = app(WeatherConditionRuleService::class);
        
        // Tạo các quy tắc mặc định
        $weatherConditionRuleService->createPredefinedRules();
        
        $this->command->info('Weather condition rules seeded successfully!');
    }
} 