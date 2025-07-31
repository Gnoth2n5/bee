<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\VietnamCity;
use App\Services\WeatherService;
use App\Services\WeatherConditionRuleService;

class TestWeatherComponent extends Command
{
    protected $signature = 'weather:test-component {city_code? : Mã thành phố để test}';
    protected $description = 'Test component thời tiết và đề xuất món ăn';

    protected $weatherService;
    protected $weatherConditionRuleService;

    public function __construct(WeatherService $weatherService, WeatherConditionRuleService $weatherConditionRuleService)
    {
        parent::__construct();
        $this->weatherService = $weatherService;
        $this->weatherConditionRuleService = $weatherConditionRuleService;
    }

    public function handle()
    {
        $cityCode = $this->argument('city_code') ?? 'HCM';
        
        $this->info("🌤️  Test component thời tiết cho thành phố: {$cityCode}");

        // Tìm thành phố
        $city = VietnamCity::where('code', $cityCode)->first();
        if (!$city) {
            $this->error("❌ Không tìm thấy thành phố với mã: {$cityCode}");
            return 1;
        }

        $this->info("📍 Thành phố: {$city->name} ({$city->latitude}, {$city->longitude})");

        // Test lấy thời tiết
        $this->info("🔍 Đang lấy thời tiết...");
        $weatherData = $this->weatherService->getCurrentWeather($city);

        if (!$weatherData) {
            $this->error("❌ Không lấy được dữ liệu thời tiết");
            return 1;
        }

        $this->info("✅ Thời tiết:");
        $this->info("   🌡️  Nhiệt độ: {$weatherData['temperature']}°C");
        $this->info("   💧 Độ ẩm: {$weatherData['humidity']}%");
        $this->info("   🌤️  Mô tả: " . ($weatherData['description'] ?? 'Không có mô tả'));

        // Test đề xuất món ăn
        $this->info("🍽️  Đang lấy đề xuất món ăn...");
        $suggestions = $this->weatherConditionRuleService->getSuggestionsByConditions(
            $weatherData['temperature'],
            $weatherData['humidity'],
            5
        );

        if ($suggestions->isEmpty()) {
            $this->warn("⚠️  Không có đề xuất món ăn nào");
        } else {
            $this->info("✅ Đề xuất món ăn ({$suggestions->count()} món):");
            foreach ($suggestions as $recipe) {
                $this->info("   🍳 {$recipe->title}");
            }
        }

        // Test lý do đề xuất
        $reason = $this->weatherConditionRuleService->getSuggestionReason(
            $weatherData['temperature'],
            $weatherData['humidity']
        );

        $this->info("📝 Lý do đề xuất:");
        $this->info("   {$reason}");

        $this->info("✅ Test component thành công!");

        return 0;
    }
} 