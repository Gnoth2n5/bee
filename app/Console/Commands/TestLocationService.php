<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\LocationService;

class TestLocationService extends Command
{
    protected $signature = 'location:test {latitude} {longitude}';
    protected $description = 'Test LocationService với tọa độ cụ thể';

    public function handle()
    {
        $latitude = (float) $this->argument('latitude');
        $longitude = (float) $this->argument('longitude');

        $this->info("📍 Test LocationService với tọa độ: {$latitude}, {$longitude}");
        $this->newLine();

        $locationService = new LocationService();
        $locationInfo = $locationService->getLocationInfo($latitude, $longitude);

        if ($locationInfo) {
            $this->info("🏙️  Kết quả:");
            $this->info("   Tỉnh: {$locationInfo['province_name']}");
            $this->info("   Mã tỉnh: {$locationInfo['province_code']}");
            $this->info("   Vùng miền: {$locationInfo['region']}");
            $this->info("   Tọa độ tỉnh: {$locationInfo['latitude']}, {$locationInfo['longitude']}");
            $this->info("   Chính xác: " . ($locationInfo['is_exact'] ? '✅ Có' : '⚠️ Không (tìm tỉnh gần nhất)'));

            if (isset($locationInfo['distance'])) {
                $this->info("   Khoảng cách: " . number_format($locationInfo['distance'], 2) . " km");
            }
        } else {
            $this->error("❌ Không tìm thấy thông tin tỉnh");
        }

        return 0;
    }
}
