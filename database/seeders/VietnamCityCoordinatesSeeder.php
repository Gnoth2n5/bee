<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\VietnamCity;

class VietnamCityCoordinatesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $coordinates = [
            // Miền Bắc
            ['name' => 'Hà Nội', 'latitude' => 21.0285, 'longitude' => 105.8542],
            ['name' => 'Hải Phòng', 'latitude' => 20.8449, 'longitude' => 106.6881],
            ['name' => 'Quảng Ninh', 'latitude' => 21.0064, 'longitude' => 107.2925],
            ['name' => 'Bắc Giang', 'latitude' => 21.2731, 'longitude' => 106.1946],
            ['name' => 'Bắc Ninh', 'latitude' => 21.1861, 'longitude' => 106.0763],
            ['name' => 'Hải Dương', 'latitude' => 20.9373, 'longitude' => 106.3341],
            ['name' => 'Hưng Yên', 'latitude' => 20.8525, 'longitude' => 106.0169],
            ['name' => 'Hòa Bình', 'latitude' => 20.8133, 'longitude' => 105.3383],
            ['name' => 'Phú Thọ', 'latitude' => 21.2682, 'longitude' => 105.2346],
            ['name' => 'Thái Nguyên', 'latitude' => 21.5942, 'longitude' => 105.8482],
            ['name' => 'Tuyên Quang', 'latitude' => 21.8233, 'longitude' => 105.2142],
            ['name' => 'Lào Cai', 'latitude' => 22.4809, 'longitude' => 103.9755],
            ['name' => 'Yên Bái', 'latitude' => 21.7167, 'longitude' => 104.9000],
            ['name' => 'Lạng Sơn', 'latitude' => 21.8533, 'longitude' => 106.7610],
            ['name' => 'Cao Bằng', 'latitude' => 22.6667, 'longitude' => 106.2500],
            ['name' => 'Bắc Kạn', 'latitude' => 22.1473, 'longitude' => 105.8348],
            ['name' => 'Thái Bình', 'latitude' => 20.4461, 'longitude' => 106.3369],
            ['name' => 'Nam Định', 'latitude' => 20.4339, 'longitude' => 106.1621],
            ['name' => 'Ninh Bình', 'latitude' => 20.2506, 'longitude' => 105.9744],
            ['name' => 'Thanh Hóa', 'latitude' => 19.8066, 'longitude' => 105.7852],
            ['name' => 'Nghệ An', 'latitude' => 19.2342, 'longitude' => 104.9200],
            ['name' => 'Hà Tĩnh', 'latitude' => 18.3333, 'longitude' => 105.9000],
            ['name' => 'Quảng Bình', 'latitude' => 17.4684, 'longitude' => 106.6222],
            ['name' => 'Quảng Trị', 'latitude' => 16.7942, 'longitude' => 107.0022],
            ['name' => 'Thừa Thiên Huế', 'latitude' => 16.0544, 'longitude' => 108.2022],
            ['name' => 'Điện Biên', 'latitude' => 21.3867, 'longitude' => 103.0167],
            ['name' => 'Lai Châu', 'latitude' => 22.3964, 'longitude' => 103.4708],
            ['name' => 'Sơn La', 'latitude' => 21.1023, 'longitude' => 103.7289],
            ['name' => 'Hà Giang', 'latitude' => 22.8233, 'longitude' => 104.9789],

            // Miền Trung
            ['name' => 'Đà Nẵng', 'latitude' => 16.0544, 'longitude' => 108.2022],
            ['name' => 'Quảng Nam', 'latitude' => 15.5394, 'longitude' => 108.0191],
            ['name' => 'Quảng Ngãi', 'latitude' => 15.1213, 'longitude' => 108.8044],
            ['name' => 'Bình Định', 'latitude' => 14.1667, 'longitude' => 108.9000],
            ['name' => 'Phú Yên', 'latitude' => 13.1667, 'longitude' => 109.1667],
            ['name' => 'Khánh Hòa', 'latitude' => 12.2500, 'longitude' => 109.0000],
            ['name' => 'Ninh Thuận', 'latitude' => 11.7500, 'longitude' => 108.8333],
            ['name' => 'Bình Thuận', 'latitude' => 10.9333, 'longitude' => 108.1000],
            ['name' => 'Kon Tum', 'latitude' => 14.3500, 'longitude' => 108.0000],
            ['name' => 'Gia Lai', 'latitude' => 13.9833, 'longitude' => 108.0000],
            ['name' => 'Đắk Lắk', 'latitude' => 12.6667, 'longitude' => 108.2333],
            ['name' => 'Đắk Nông', 'latitude' => 12.0000, 'longitude' => 107.7000],
            ['name' => 'Lâm Đồng', 'latitude' => 11.9500, 'longitude' => 108.4422],
            ['name' => 'Bình Phước', 'latitude' => 11.7500, 'longitude' => 106.7500],
            ['name' => 'Tây Ninh', 'latitude' => 11.3333, 'longitude' => 106.1000],
            ['name' => 'Bình Dương', 'latitude' => 11.1667, 'longitude' => 106.6667],

            // Miền Nam
            ['name' => 'TP. Hồ Chí Minh', 'latitude' => 10.8231, 'longitude' => 106.6297],
            ['name' => 'Bà Rịa - Vũng Tàu', 'latitude' => 10.3454, 'longitude' => 107.0843],
            ['name' => 'Đồng Nai', 'latitude' => 10.9574, 'longitude' => 106.8426],
            ['name' => 'Bình Thạnh', 'latitude' => 10.8231, 'longitude' => 106.6297],
            ['name' => 'Bình Tân', 'latitude' => 10.8231, 'longitude' => 106.6297],
            ['name' => 'Tân Bình', 'latitude' => 10.8231, 'longitude' => 106.6297],
            ['name' => 'Tân Phú', 'latitude' => 10.8231, 'longitude' => 106.6297],
            ['name' => 'Phú Nhuận', 'latitude' => 10.8231, 'longitude' => 106.6297],
            ['name' => 'Gò Vấp', 'latitude' => 10.8231, 'longitude' => 106.6297],
            ['name' => 'Quận 1', 'latitude' => 10.8231, 'longitude' => 106.6297],
            ['name' => 'Quận 2', 'latitude' => 10.8231, 'longitude' => 106.6297],
            ['name' => 'Quận 3', 'latitude' => 10.8231, 'longitude' => 106.6297],
            ['name' => 'Quận 4', 'latitude' => 10.8231, 'longitude' => 106.6297],
            ['name' => 'Quận 5', 'latitude' => 10.8231, 'longitude' => 106.6297],
            ['name' => 'Quận 6', 'latitude' => 10.8231, 'longitude' => 106.6297],
            ['name' => 'Quận 7', 'latitude' => 10.8231, 'longitude' => 106.6297],
            ['name' => 'Quận 8', 'latitude' => 10.8231, 'longitude' => 106.6297],
            ['name' => 'Quận 9', 'latitude' => 10.8231, 'longitude' => 106.6297],
            ['name' => 'Quận 10', 'latitude' => 10.8231, 'longitude' => 106.6297],
            ['name' => 'Quận 11', 'latitude' => 10.8231, 'longitude' => 106.6297],
            ['name' => 'Quận 12', 'latitude' => 10.8231, 'longitude' => 106.6297],
            ['name' => 'Thủ Đức', 'latitude' => 10.8231, 'longitude' => 106.6297],
            ['name' => 'Cần Giờ', 'latitude' => 10.8231, 'longitude' => 106.6297],
            ['name' => 'Củ Chi', 'latitude' => 10.8231, 'longitude' => 106.6297],
            ['name' => 'Hóc Môn', 'latitude' => 10.8231, 'longitude' => 106.6297],
            ['name' => 'Bình Chánh', 'latitude' => 10.8231, 'longitude' => 106.6297],
            ['name' => 'Nhà Bè', 'latitude' => 10.8231, 'longitude' => 106.6297],
            ['name' => 'Long An', 'latitude' => 10.5333, 'longitude' => 106.4000],
            ['name' => 'Tiền Giang', 'latitude' => 10.3500, 'longitude' => 106.3500],
            ['name' => 'Bến Tre', 'latitude' => 10.2333, 'longitude' => 106.3833],
            ['name' => 'Trà Vinh', 'latitude' => 9.9333, 'longitude' => 106.3333],
            ['name' => 'Vĩnh Long', 'latitude' => 10.2500, 'longitude' => 105.9667],
            ['name' => 'Đồng Tháp', 'latitude' => 10.5000, 'longitude' => 105.6667],
            ['name' => 'An Giang', 'latitude' => 10.5000, 'longitude' => 105.1667],
            ['name' => 'Kiên Giang', 'latitude' => 10.0000, 'longitude' => 105.1667],
            ['name' => 'Cần Thơ', 'latitude' => 10.0333, 'longitude' => 105.7833],
            ['name' => 'Hậu Giang', 'latitude' => 9.7833, 'longitude' => 105.6333],
            ['name' => 'Sóc Trăng', 'latitude' => 9.6000, 'longitude' => 105.9667],
            ['name' => 'Bạc Liêu', 'latitude' => 9.2833, 'longitude' => 105.7167],
            ['name' => 'Cà Mau', 'latitude' => 9.1667, 'longitude' => 105.1667],
        ];

        $updatedCount = 0;
        foreach ($coordinates as $coord) {
            $city = VietnamCity::where('name', 'like', '%' . $coord['name'] . '%')->first();
            if ($city) {
                $city->update([
                    'latitude' => $coord['latitude'],
                    'longitude' => $coord['longitude']
                ]);
                $updatedCount++;
                
                // Chỉ log khi chạy từ command line
                if ($this->command) {
                    $this->command->info("Cập nhật tọa độ cho: {$city->name}");
                }
            }
        }

        // Chỉ log khi chạy từ command line
        if ($this->command) {
            $this->command->info("Hoàn thành cập nhật tọa độ cho {$updatedCount} tỉnh thành!");
        }
    }
}
