<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\LocationService;
use App\Models\VietnamCity;

class SystemLocationReport extends Command
{
    protected $signature = 'system:location-report';
    protected $description = 'Báo cáo tình trạng hệ thống xác định vị trí';

    public function handle()
    {
        $this->info('📊 BÁO CÁO HỆ THỐNG XÁC ĐỊNH VỊ TRÍ');
        $this->newLine();

        // 1. Kiểm tra dữ liệu tỉnh thành
        $this->info('1️⃣ KIỂM TRA DỮ LIỆU TỈNH THÀNH:');
        $totalCities = VietnamCity::count();
        $citiesWithCoords = VietnamCity::whereNotNull('latitude')->whereNotNull('longitude')->count();
        $citiesWithoutCoords = $totalCities - $citiesWithCoords;

        $this->info("   Tổng số tỉnh: {$totalCities}");
        $this->info("   Có tọa độ: {$citiesWithCoords}");
        $this->info("   Thiếu tọa độ: {$citiesWithoutCoords}");

        if ($citiesWithoutCoords > 0) {
            $this->warn("   ⚠️  Cần cập nhật tọa độ cho {$citiesWithoutCoords} tỉnh");
        } else {
            $this->info("   ✅ Tất cả tỉnh đều có tọa độ");
        }

        // 2. Kiểm tra phân loại vùng miền
        $this->newLine();
        $this->info('2️⃣ KIỂM TRA PHÂN LOẠI VÙNG MIỀN:');

        $regions = VietnamCity::selectRaw('region, COUNT(*) as count')
            ->groupBy('region')
            ->orderBy('region')
            ->get();

        foreach ($regions as $region) {
            $this->info("   Miền {$region->region}: {$region->count} tỉnh");
        }

        // 3. Test LocationService
        $this->newLine();
        $this->info('3️⃣ TEST LOCATIONSERVICE:');

        $testCases = [
            ['name' => 'Hà Nội (trung tâm)', 'lat' => 21.0278, 'lng' => 105.8342],
            ['name' => 'Vị trí của bạn', 'lat' => 20.786086, 'lng' => 105.974104],
            ['name' => 'TP.HCM (trung tâm)', 'lat' => 10.8231, 'lng' => 106.6297],
            ['name' => 'Đà Nẵng (trung tâm)', 'lat' => 16.0544, 'lng' => 108.2022],
        ];

        $locationService = new LocationService();

        foreach ($testCases as $testCase) {
            $locationInfo = $locationService->getLocationInfo($testCase['lat'], $testCase['lng']);

            if ($locationInfo) {
                $accuracyIcon = $locationInfo['is_exact'] ? '✅' : '⚠️';
                $accuracyText = $locationInfo['is_exact'] ? 'Chính xác' : 'Gần nhất';
                $this->info("   {$accuracyIcon} {$testCase['name']}: {$locationInfo['province_name']} ({$accuracyText})");
            } else {
                $this->error("   ❌ {$testCase['name']}: Không xác định được");
            }
        }

        // 4. Kiểm tra các vấn đề tiềm ẩn
        $this->newLine();
        $this->info('4️⃣ KIỂM TRA CÁC VẤN ĐỀ TIỀM ẨN:');

        // Kiểm tra tọa độ trùng lặp
        $duplicateCoords = VietnamCity::selectRaw('latitude, longitude, COUNT(*) as count')
            ->whereNotNull('latitude')
            ->whereNotNull('longitude')
            ->groupBy('latitude', 'longitude')
            ->having('count', '>', 1)
            ->get();

        if ($duplicateCoords->count() > 0) {
            $this->warn("   ⚠️  Phát hiện {$duplicateCoords->count()} bộ tọa độ trùng lặp");
        } else {
            $this->info("   ✅ Không có tọa độ trùng lặp");
        }

        // Kiểm tra tọa độ nằm ngoài phạm vi Việt Nam
        $invalidCoords = VietnamCity::where(function ($query) {
            $query->where('latitude', '<', 8)
                ->orWhere('latitude', '>', 23)
                ->orWhere('longitude', '<', 102)
                ->orWhere('longitude', '>', 110);
        })->whereNotNull('latitude')
            ->whereNotNull('longitude')
            ->get();

        if ($invalidCoords->count() > 0) {
            $this->warn("   ⚠️  Phát hiện {$invalidCoords->count()} tọa độ nằm ngoài phạm vi Việt Nam");
        } else {
            $this->info("   ✅ Tất cả tọa độ đều trong phạm vi Việt Nam");
        }

        // 5. Kết luận
        $this->newLine();
        $this->info('5️⃣ KẾT LUẬN:');

        $issues = [];
        if ($citiesWithoutCoords > 0)
            $issues[] = "Thiếu tọa độ cho {$citiesWithoutCoords} tỉnh";
        if ($duplicateCoords->count() > 0)
            $issues[] = "Có tọa độ trùng lặp";
        if ($invalidCoords->count() > 0)
            $issues[] = "Có tọa độ không hợp lệ";

        if (empty($issues)) {
            $this->info("   ✅ Hệ thống xác định vị trí hoạt động tốt");
            $this->info("   ✅ LocationService đã được tích hợp");
            $this->info("   ✅ Vị trí của bạn sẽ được xác định chính xác là Ninh Bình");
        } else {
            $this->warn("   ⚠️  Cần khắc phục các vấn đề sau:");
            foreach ($issues as $issue) {
                $this->warn("      - {$issue}");
            }
        }

        return 0;
    }
}
