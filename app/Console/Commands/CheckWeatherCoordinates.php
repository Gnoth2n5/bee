<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\VietnamCity;
use App\Services\WeatherService;

class CheckWeatherCoordinates extends Command
{
    protected $signature = 'weather:check-coordinates {--test : Test weather API for first 3 cities}';
    protected $description = 'Kiểm tra tọa độ và khả năng liên kết với API thời tiết';

    protected $weatherService;

    public function __construct(WeatherService $weatherService)
    {
        parent::__construct();
        $this->weatherService = $weatherService;
    }

    public function handle()
    {
        $this->info('🔍 Kiểm tra tọa độ và khả năng liên kết với API thời tiết...');

        // Kiểm tra tọa độ
        $this->checkCoordinates();

        // Test API thời tiết nếu được yêu cầu
        if ($this->option('test')) {
            $this->testWeatherAPI();
        }

        return 0;
    }

    protected function checkCoordinates()
    {
        $this->info('📊 Kiểm tra tọa độ các tỉnh thành:');

        $cities = VietnamCity::take(10)->get();
        
        $hasCoordinates = 0;
        $noCoordinates = 0;

        foreach ($cities as $city) {
            $hasLatLon = !empty($city->latitude) && !empty($city->longitude) && 
                        $city->latitude != 0.0 && $city->longitude != 0.0;
            
            $status = $hasLatLon ? '✅' : '❌';
            
            if ($hasLatLon) {
                $hasCoordinates++;
                $this->info("   {$status} {$city->name}: {$city->latitude}, {$city->longitude}");
            } else {
                $noCoordinates++;
                $this->info("   {$status} {$city->name}: Không có tọa độ");
            }
        }

        $this->newLine();
        $this->info("📈 Thống kê tọa độ:");
        $this->info("   ✅ Có tọa độ: {$hasCoordinates}");
        $this->info("   ❌ Không có tọa độ: {$noCoordinates}");

        // Thống kê tổng thể
        $totalCities = VietnamCity::count();
        $totalWithCoords = VietnamCity::whereNotNull('latitude')
            ->whereNotNull('longitude')
            ->where('latitude', '!=', 0.0)
            ->where('longitude', '!=', 0.0)
            ->count();

        $this->info("   📊 Tổng thể: {$totalWithCoords}/{$totalCities} tỉnh có tọa độ");
    }

    protected function testWeatherAPI()
    {
        $this->newLine();
        $this->info('🌤️  Test API thời tiết cho 3 tỉnh đầu tiên:');

        $cities = VietnamCity::whereNotNull('latitude')
            ->whereNotNull('longitude')
            ->where('latitude', '!=', 0.0)
            ->where('longitude', '!=', 0.0)
            ->take(3)
            ->get();

        if ($cities->isEmpty()) {
            $this->error('❌ Không có tỉnh nào có tọa độ hợp lệ để test');
            return;
        }

        foreach ($cities as $city) {
            $this->info("🔍 Test thời tiết cho: {$city->name} ({$city->latitude}, {$city->longitude})");
            
            try {
                $weather = $this->weatherService->getCurrentWeather($city);
                
                if ($weather) {
                    $description = $weather['description'] ?? 'Không có mô tả';
                    $this->info("   ✅ Thành công: {$weather['temperature']}°C, {$description}");
                } else {
                    $this->error("   ❌ Thất bại: Không lấy được dữ liệu thời tiết");
                }
            } catch (\Exception $e) {
                $this->error("   ❌ Lỗi: {$e->getMessage()}");
            }
        }
    }
} 