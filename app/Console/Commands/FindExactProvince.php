<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\VietnamCity;

class FindExactProvince extends Command
{
    protected $signature = 'location:find-exact {latitude} {longitude}';
    protected $description = 'Xác định chính xác tỉnh dựa trên ranh giới địa lý';

    // Ranh giới địa lý của các tỉnh (vĩ độ min, vĩ độ max, kinh độ min, kinh độ max)
    protected $provinceBoundaries = [
        'Hà Nội' => [20.8, 21.5, 105.0, 106.0],
        'Hưng Yên' => [20.8, 21.0, 105.9, 106.1],
        'Hải Dương' => [20.7, 21.2, 106.0, 106.8],
        'Hải Phòng' => [20.5, 21.0, 106.4, 107.0],
        'Bắc Ninh' => [21.0, 21.4, 105.8, 106.4],
        'Vĩnh Phúc' => [21.0, 21.6, 105.2, 105.8],
        'Phú Thọ' => [21.0, 21.5, 105.0, 105.5],
        'Thái Nguyên' => [21.4, 22.0, 105.5, 106.2],
        'Lạng Sơn' => [21.5, 22.5, 106.4, 107.2],
        'Quảng Ninh' => [20.8, 21.5, 106.8, 108.0],
        'Bắc Giang' => [21.0, 21.6, 106.0, 106.8],
        'Hà Nam' => [20.3, 20.8, 105.7, 106.2],
        'Nam Định' => [20.0, 20.6, 105.8, 106.5],
        'Thái Bình' => [20.2, 20.7, 106.0, 106.8],
        'Ninh Bình' => [20.6, 20.9, 105.9, 106.0],
        'Thanh Hóa' => [19.2, 20.5, 105.0, 106.2],
        'Nghệ An' => [18.5, 19.8, 104.0, 105.5],
        'Hà Tĩnh' => [17.8, 18.8, 105.5, 106.5],
        'Quảng Bình' => [17.0, 18.0, 106.0, 107.0],
        'Quảng Trị' => [16.2, 17.2, 106.5, 107.5],
        'Thừa Thiên Huế' => [15.8, 16.8, 107.0, 108.0],
        'Đà Nẵng' => [15.8, 16.3, 107.8, 108.5],
        'Quảng Nam' => [15.0, 16.0, 107.5, 108.5],
        'Quảng Ngãi' => [14.5, 15.5, 108.0, 109.0],
        'Bình Định' => [13.5, 14.5, 108.5, 109.5],
        'Phú Yên' => [12.5, 13.5, 108.5, 109.5],
        'Khánh Hòa' => [11.5, 12.8, 108.5, 109.5],
        'Ninh Thuận' => [11.0, 12.0, 108.5, 109.0],
        'Bình Thuận' => [10.5, 11.5, 107.5, 108.5],
        'Kon Tum' => [14.0, 15.0, 107.5, 108.5],
        'Gia Lai' => [13.0, 14.5, 107.5, 108.5],
        'Đắk Lắk' => [12.0, 13.5, 107.5, 108.5],
        'Đắk Nông' => [11.5, 12.5, 107.0, 108.0],
        'Lâm Đồng' => [11.0, 12.5, 107.5, 108.5],
        'Bình Phước' => [11.0, 12.0, 106.0, 107.0],
        'Tây Ninh' => [10.8, 11.8, 105.5, 106.5],
        'Bình Dương' => [10.8, 11.5, 106.0, 106.8],
        'Đồng Nai' => [10.5, 11.2, 106.5, 107.2],
        'Bà Rịa - Vũng Tàu' => [10.0, 10.8, 106.8, 107.5],
        'TP. Hồ Chí Minh' => [10.3, 11.0, 106.2, 106.8],
        'Long An' => [10.2, 10.8, 105.8, 106.5],
        'Tiền Giang' => [10.0, 10.6, 105.8, 106.5],
        'Bến Tre' => [9.8, 10.5, 105.8, 106.5],
        'Trà Vinh' => [9.5, 10.2, 105.8, 106.5],
        'Vĩnh Long' => [9.8, 10.5, 105.5, 106.2],
        'Đồng Tháp' => [10.0, 10.8, 105.2, 106.0],
        'An Giang' => [10.0, 10.8, 104.5, 105.5],
        'Kiên Giang' => [9.5, 10.5, 104.5, 105.5],
        'Cần Thơ' => [9.8, 10.3, 105.2, 105.8],
        'Hậu Giang' => [9.5, 10.0, 105.2, 105.8],
        'Sóc Trăng' => [9.2, 9.8, 105.5, 106.2],
        'Bạc Liêu' => [8.8, 9.5, 105.2, 105.8],
        'Cà Mau' => [8.5, 9.2, 104.5, 105.2],
    ];

    public function handle()
    {
        $latitude = (float) $this->argument('latitude');
        $longitude = (float) $this->argument('longitude');

        $this->info("📍 Kiểm tra vị trí: {$latitude}, {$longitude}");
        $this->newLine();

        $exactProvince = $this->findExactProvince($latitude, $longitude);

        if ($exactProvince) {
            $this->info("🏙️  Tỉnh chính xác: {$exactProvince}");
        } else {
            $this->info("⚠️  Không xác định được tỉnh chính xác");
        }

        // Hiển thị tất cả tỉnh có thể chứa vị trí này
        $this->newLine();
        $this->info("🗺️  Các tỉnh có thể chứa vị trí này:");

        $possibleProvinces = $this->findPossibleProvinces($latitude, $longitude);

        foreach ($possibleProvinces as $province => $distance) {
            $this->line("   {$province}: cách " . number_format($distance, 2) . " km");
        }

        return 0;
    }

    protected function findExactProvince($lat, $lng)
    {
        foreach ($this->provinceBoundaries as $province => $boundary) {
            [$minLat, $maxLat, $minLng, $maxLng] = $boundary;

            if (
                $lat >= $minLat && $lat <= $maxLat &&
                $lng >= $minLng && $lng <= $maxLng
            ) {
                return $province;
            }
        }

        return null;
    }

    protected function findPossibleProvinces($lat, $lng)
    {
        $provinces = [];

        foreach ($this->provinceBoundaries as $province => $boundary) {
            [$minLat, $maxLat, $minLng, $maxLng] = $boundary;

            // Tính khoảng cách đến trung tâm tỉnh
            $centerLat = ($minLat + $maxLat) / 2;
            $centerLng = ($minLng + $maxLng) / 2;

            $distance = $this->calculateDistance($lat, $lng, $centerLat, $centerLng);

            // Nếu trong phạm vi 50km
            if ($distance <= 50) {
                $provinces[$province] = $distance;
            }
        }

        // Sắp xếp theo khoảng cách
        asort($provinces);

        return $provinces;
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
