<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\VietnamCity;

class CheckLocation extends Command
{
    protected $signature = 'location:check {latitude} {longitude}';
    protected $description = 'Kiểm tra vị trí và tìm tỉnh gần nhất';

    public function handle()
    {
        $latitude = (float) $this->argument('latitude');
        $longitude = (float) $this->argument('longitude');

        $this->info("📍 Kiểm tra vị trí: {$latitude}, {$longitude}");
        $this->newLine();

        // Tìm tỉnh gần nhất
        $nearestCity = $this->findNearestCity($latitude, $longitude);

        if ($nearestCity) {
            $this->info("🏙️  Tỉnh gần nhất: {$nearestCity->name}");
            $this->info("📍 Tọa độ tỉnh: {$nearestCity->latitude}, {$nearestCity->longitude}");
            $this->info("🌍 Vùng miền: {$nearestCity->region}");

            $distance = $this->calculateDistance($latitude, $longitude, $nearestCity->latitude, $nearestCity->longitude);
            $this->info("📏 Khoảng cách: " . number_format($distance, 2) . " km");
        }

        // Hiển thị tất cả tỉnh trong bán kính 50km
        $this->newLine();
        $this->info("🗺️  Các tỉnh trong bán kính 50km:");

        $nearbyCities = $this->findNearbyCities($latitude, $longitude, 50);

        foreach ($nearbyCities as $city) {
            $distance = $this->calculateDistance($latitude, $longitude, $city->latitude, $city->longitude);
            $this->line("   {$city->name} ({$city->region}): " . number_format($distance, 2) . " km");
        }

        return 0;
    }

    protected function findNearestCity($lat, $lng)
    {
        $cities = VietnamCity::whereNotNull('latitude')
            ->whereNotNull('longitude')
            ->get();

        $nearest = null;
        $minDistance = PHP_FLOAT_MAX;

        foreach ($cities as $city) {
            $distance = $this->calculateDistance($lat, $lng, $city->latitude, $city->longitude);

            if ($distance < $minDistance) {
                $minDistance = $distance;
                $nearest = $city;
            }
        }

        return $nearest;
    }

    protected function findNearbyCities($lat, $lng, $radiusKm)
    {
        $cities = VietnamCity::whereNotNull('latitude')
            ->whereNotNull('longitude')
            ->get();

        $nearby = [];

        foreach ($cities as $city) {
            $distance = $this->calculateDistance($lat, $lng, $city->latitude, $city->longitude);

            if ($distance <= $radiusKm) {
                $nearby[] = (object) [
                    'name' => $city->name,
                    'region' => $city->region,
                    'latitude' => $city->latitude,
                    'longitude' => $city->longitude,
                    'distance' => $distance
                ];
            }
        }

        // Sắp xếp theo khoảng cách
        usort($nearby, function ($a, $b) {
            return $a->distance <=> $b->distance;
        });

        return $nearby;
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
