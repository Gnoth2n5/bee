<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\VietnamCity;

class FixCoordinates extends Command
{
    protected $signature = 'coordinates:fix {--force : Force update without confirmation}';
    protected $description = 'Sửa tọa độ chính xác cho các tỉnh thành Việt Nam';

    protected $correctCoordinates = [
        // Miền Bắc - Tọa độ chính xác theo tên trong database
        'Thành phố Hà Nội' => [21.0278, 105.8342],
        'Hà Giang' => [22.8233, 104.9784],
        'Cao Bằng' => [22.6667, 106.2500],
        'Bắc Kạn' => [22.1473, 105.8348],
        'Tuyên Quang' => [21.8233, 105.2142],
        'Lào Cai' => [22.4833, 103.9500],
        'Điện Biên' => [21.3833, 103.0167],
        'Lai Châu' => [22.4000, 103.4700],
        'Sơn La' => [21.1023, 103.7289],
        'Yên Bái' => [21.7167, 104.9000],
        'Hoà Bình' => [20.8133, 105.3383],
        'Thái Nguyên' => [21.6000, 105.8500],
        'Lạng Sơn' => [21.8500, 106.7500],
        'Quảng Ninh' => [21.0063, 107.2925],
        'Bắc Giang' => [21.2731, 106.1946],
        'Phú Thọ' => [21.2684, 105.2346],
        'Vĩnh Phúc' => [21.3609, 105.5474],
        'Bắc Ninh' => [21.1861, 106.0763],
        'Hải Dương' => [20.9373, 106.3344],
        'Hưng Yên' => [20.8525, 106.0169],
        'Hà Nam' => [20.5835, 105.9229],
        'Nam Định' => [20.4338, 106.1621],
        'Thái Bình' => [20.4461, 106.3369],
        'Ninh Bình' => [20.2506, 105.9744],
        'Thanh Hóa' => [19.8066, 105.7852],
        'Nghệ An' => [19.2342, 104.9200],
        'Hà Tĩnh' => [18.3333, 105.9000],
        'Quảng Bình' => [17.4684, 106.6222],
        'Quảng Trị' => [16.7942, 107.1818],
        'Thành phố Huế' => [16.4637, 107.5909],
        'Thành phố Hải Phòng' => [20.8449, 106.6881],

        // Miền Trung
        'Thành phố Đà Nẵng' => [16.0544, 108.2022],
        'Quảng Nam' => [15.5394, 108.0191],
        'Quảng Ngãi' => [15.1213, 108.8044],
        'Bình Định' => [14.1667, 108.9000],
        'Phú Yên' => [13.1667, 109.1667],
        'Khánh Hòa' => [12.2500, 109.0000],
        'Ninh Thuận' => [11.7500, 108.8333],
        'Bình Thuận' => [10.9333, 108.1000],
        'Kon Tum' => [14.3500, 108.0000],
        'Gia Lai' => [13.9833, 108.0000],
        'Đắk Lắk' => [12.6667, 108.2333],
        'Đắk Nông' => [12.0000, 107.7000],
        'Lâm Đồng' => [11.9500, 108.4422],
        'Bình Phước' => [11.7500, 106.7500],
        'Tây Ninh' => [11.3333, 106.1000],
        'Bình Dương' => [11.1667, 106.6667],
        'Đồng Nai' => [10.9500, 106.8167],
        'Bà Rịa - Vũng Tàu' => [10.3333, 107.0833],

        // Miền Nam
        'Thành phố Hồ Chí Minh' => [10.8231, 106.6297],
        'Long An' => [10.5333, 106.4000],
        'Tiền Giang' => [10.3500, 106.3500],
        'Bến Tre' => [10.2333, 106.3833],
        'Trà Vinh' => [9.9333, 106.3333],
        'Vĩnh Long' => [10.2500, 105.9667],
        'Đồng Tháp' => [10.5167, 105.6889],
        'An Giang' => [10.5216, 105.1258],
        'Kiên Giang' => [10.0000, 105.1667],
        'Thành phố Cần Thơ' => [10.0333, 105.7833],
        'Hậu Giang' => [9.7833, 105.6333],
        'Sóc Trăng' => [9.6000, 105.9667],
        'Bạc Liêu' => [9.2833, 105.7167],
        'Cà Mau' => [9.1769, 105.1522],
    ];

    public function handle()
    {
        $this->info('🔧 Bắt đầu sửa tọa độ chính xác cho các tỉnh thành...');

        // Xác nhận cập nhật
        if (!$this->option('force')) {
            if (!$this->confirm('Bạn có chắc chắn muốn cập nhật tọa độ cho tất cả tỉnh thành?')) {
                $this->info('🛑 Hủy cập nhật');
                return 0;
            }
        }

        $updated = 0;
        $notFound = 0;
        $cities = VietnamCity::all();

        foreach ($cities as $city) {
            $found = false;

            foreach ($this->correctCoordinates as $name => $coords) {
                if ($city->name === $name) {
                    $city->update([
                        'latitude' => $coords[0],
                        'longitude' => $coords[1]
                    ]);

                    $this->info("✅ Cập nhật: {$city->name} -> {$coords[0]}, {$coords[1]}");
                    $updated++;
                    $found = true;
                    break;
                }
            }

            if (!$found) {
                $notFound++;
                $this->warn("⚠️  Không tìm thấy tọa độ cho: {$city->name}");
            }
        }

        $this->newLine();
        $this->info("📈 Kết quả cập nhật:");
        $this->info("   ✅ Cập nhật: {$updated}");
        $this->info("   ⚠️  Không tìm thấy: {$notFound}");

        // Kiểm tra lại vị trí người dùng
        $this->newLine();
        $this->info('📍 Kiểm tra lại vị trí người dùng (20.786086, 105.974104):');

        $userLat = 20.786086;
        $userLng = 105.974104;

        $nearestCity = $this->findNearestCity($userLat, $userLng);

        if ($nearestCity) {
            $distance = $this->calculateDistance($userLat, $userLng, $nearestCity->latitude, $nearestCity->longitude);
            $this->info("🏙️  Tỉnh gần nhất: {$nearestCity->name} (cách {$distance} km)");
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
