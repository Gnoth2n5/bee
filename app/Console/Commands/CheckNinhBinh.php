<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\VietnamCity;

class CheckNinhBinh extends Command
{
    protected $signature = 'location:check-ninhbinh';
    protected $description = 'Kiểm tra tọa độ Ninh Bình và so sánh với vị trí người dùng';

    public function handle()
    {
        $userLat = 20.786086;
        $userLng = 105.974104;

        $this->info("📍 Vị trí người dùng: {$userLat}, {$userLng}");
        $this->newLine();

        // Tìm Ninh Bình
        $ninhBinh = VietnamCity::where('name', 'LIKE', '%Ninh Bình%')->first();

        if ($ninhBinh) {
            $this->info("🏙️  Ninh Bình:");
            $this->info("   Tọa độ: {$ninhBinh->latitude}, {$ninhBinh->longitude}");
            $this->info("   Vùng miền: {$ninhBinh->region}");

            $distance = $this->calculateDistance($userLat, $userLng, $ninhBinh->latitude, $ninhBinh->longitude);
            $this->info("   Khoảng cách: " . number_format($distance, 2) . " km");
        }

        // Tìm Hưng Yên
        $hungYen = VietnamCity::where('name', 'LIKE', '%Hưng Yên%')->first();

        if ($hungYen) {
            $this->newLine();
            $this->info("🏙️  Hưng Yên:");
            $this->info("   Tọa độ: {$hungYen->latitude}, {$hungYen->longitude}");
            $this->info("   Vùng miền: {$hungYen->region}");

            $distance = $this->calculateDistance($userLat, $userLng, $hungYen->latitude, $hungYen->longitude);
            $this->info("   Khoảng cách: " . number_format($distance, 2) . " km");
        }

        // Tìm tất cả tỉnh trong bán kính 20km
        $this->newLine();
        $this->info("🗺️  Các tỉnh trong bán kính 20km:");

        $cities = VietnamCity::whereNotNull('latitude')
            ->whereNotNull('longitude')
            ->get();

        $nearby = [];

        foreach ($cities as $city) {
            $distance = $this->calculateDistance($userLat, $userLng, $city->latitude, $city->longitude);

            if ($distance <= 20) {
                $nearby[] = [
                    'name' => $city->name,
                    'distance' => $distance,
                    'coords' => "{$city->latitude}, {$city->longitude}"
                ];
            }
        }

        // Sắp xếp theo khoảng cách
        usort($nearby, function ($a, $b) {
            return $a['distance'] <=> $b['distance'];
        });

        foreach ($nearby as $city) {
            $this->line("   {$city['name']}: " . number_format($city['distance'], 2) . " km ({$city['coords']})");
        }

        return 0;
    }

    protected function calculateDistance($lat1, $lng1, $lat2, $lng2)
    {
        $earthRadius = 6371; // Bán kính trái đất (km)

        $latDelta = deg2rad($lat2 - $lat1);
        $lngDelta = deg2rad($lng2 - $lng1);

        $a = sin($latDelta / 2) * sin($latDelta / 2) +
            cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
            sin($lngDelta / 2) * sin($lngDelta / 2);

        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        return $earthRadius * $c;
    }
}
