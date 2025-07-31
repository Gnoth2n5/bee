<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\WeatherConditionRule;

class CheckWeatherRules extends Command
{
    protected $signature = 'weather:check-rules';
    protected $description = 'Kiểm tra quy tắc thời tiết trong database';

    public function handle()
    {
        $this->info('🔍 Kiểm tra quy tắc thời tiết...');

        $totalRules = WeatherConditionRule::count();
        $this->info("📊 Tổng số quy tắc: {$totalRules}");

        if ($totalRules > 0) {
            $this->info('📋 Danh sách quy tắc:');
            $rules = WeatherConditionRule::take(10)->get();

            foreach ($rules as $rule) {
                $tempRange = "{$rule->temperature_min}°C - {$rule->temperature_max}°C";
                $humidityRange = "{$rule->humidity_min}% - {$rule->humidity_max}%";
                $status = $rule->is_active ? '✅' : '❌';

                $this->info("   {$status} {$rule->name}");
                $this->info("      🌡️  Nhiệt độ: {$tempRange}");
                $this->info("      💧 Độ ẩm: {$humidityRange}");
                $this->info("      📝 Mô tả: {$rule->description}");
                $this->newLine();
            }
        } else {
            $this->warn('⚠️  Chưa có quy tắc nào trong database');
            $this->info('💡 Chạy seeder để tạo dữ liệu mẫu: php artisan db:seed --class=WeatherConditionRuleSeeder');
        }

        return 0;
    }
}