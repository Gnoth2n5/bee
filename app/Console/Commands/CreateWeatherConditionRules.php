<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\WeatherConditionRuleService;

class CreateWeatherConditionRules extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'weather:create-rules {--force : Force recreate all rules}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create predefined weather condition rules for recipe suggestions';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Creating weather condition rules...');

        $weatherConditionRuleService = app(WeatherConditionRuleService::class);
        
        if ($this->option('force')) {
            $this->warn('Force option detected. All existing rules will be recreated.');
            // Có thể thêm logic xóa rules cũ nếu cần
        }

        try {
            $weatherConditionRuleService->createPredefinedRules();
            
            $this->info('Weather condition rules created successfully!');
            
            // Hiển thị thống kê
            $stats = $weatherConditionRuleService->getStats();
            $this->table(
                ['Metric', 'Count'],
                [
                    ['Total Rules', $stats['total_rules']],
                    ['Active Rules', $stats['active_rules']],
                    ['Temperature Rules', $stats['temperature_rules']],
                    ['Humidity Rules', $stats['humidity_rules']],
                ]
            );

        } catch (\Exception $e) {
            $this->error('Error creating weather condition rules: ' . $e->getMessage());
            return 1;
        }

        return 0;
    }
} 