<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\VietnamCity;
use App\Services\WeatherService;

class TestAllWeather extends Command
{
    protected $signature = 'weather:test-all {--limit=10 : Số lượng tỉnh test tối đa} {--region= : Miền cụ thể (north/central/south)}';
    protected $description = 'Test thời tiết cho tất cả các tỉnh có tọa độ';

    protected $weatherService;

    public function __construct(WeatherService $weatherService)
    {
        parent::__construct();
        $this->weatherService = $weatherService;
    }

    public function handle()
    {
        $limit = $this->option('limit');
        $region = $this->option('region');

        $this->info("🌤️  Test thời tiết cho các tỉnh thành (giới hạn: {$limit})...");

        // Lấy danh sách tỉnh có tọa độ
        $query = VietnamCity::whereNotNull('latitude')
            ->whereNotNull('longitude')
            ->where('latitude', '!=', 0.0)
            ->where('longitude', '!=', 0.0);

        // Lọc theo miền nếu được chỉ định
        if ($region) {
            $query = $this->filterByRegion($query, $region);
        }

        $cities = $query->take($limit)->get();

        if ($cities->isEmpty()) {
            $this->error('❌ Không có tỉnh nào có tọa độ hợp lệ để test');
            return 1;
        }

        $this->info("📊 Test thời tiết cho {$cities->count()} tỉnh:");
        $this->newLine();

        $successCount = 0;
        $failCount = 0;

        foreach ($cities as $index => $city) {
            $number = $index + 1;
            $this->info("{$number}. 🔍 Test thời tiết cho: {$city->name} ({$city->latitude}, {$city->longitude})");
            
            try {
                $weather = $this->weatherService->getCurrentWeather($city);
                
                if ($weather) {
                    $description = $weather['description'] ?? 'Không có mô tả';
                    $humidity = $weather['humidity'] ?? 'N/A';
                    $this->info("   ✅ Thành công: {$weather['temperature']}°C, {$description}, Độ ẩm: {$humidity}%");
                    $successCount++;
                } else {
                    $this->error("   ❌ Thất bại: Không lấy được dữ liệu thời tiết");
                    $failCount++;
                }
            } catch (\Exception $e) {
                $this->error("   ❌ Lỗi: {$e->getMessage()}");
                $failCount++;
            }

            // Tạm dừng 1 giây để tránh rate limit
            if ($index < $cities->count() - 1) {
                sleep(1);
            }
        }

        $this->newLine();
        $this->info("📈 Kết quả test:");
        $this->info("   ✅ Thành công: {$successCount}");
        $this->info("   ❌ Thất bại: {$failCount}");
        $this->info("   📊 Tỷ lệ thành công: " . round(($successCount / $cities->count()) * 100, 1) . "%");

        return 0;
    }

    protected function filterByRegion($query, $region)
    {
        switch (strtolower($region)) {
            case 'north':
                return $query->where('latitude', '>=', 20.0);
            case 'central':
                return $query->whereBetween('latitude', [15.0, 20.0]);
            case 'south':
                return $query->where('latitude', '<', 15.0);
            default:
                return $query;
        }
    }
} 