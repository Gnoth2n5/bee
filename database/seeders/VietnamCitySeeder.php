<?php

namespace Database\Seeders;

use App\Models\VietnamCity;
use Illuminate\Database\Seeder;

class VietnamCitySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $cities = [
            // Miền Bắc
            ['name' => 'Hà Nội', 'code' => 'HANOI', 'region' => 'Bắc', 'latitude' => 21.0285, 'longitude' => 105.8542, 'sort_order' => 1],
            ['name' => 'Hải Phòng', 'code' => 'HAIPHONG', 'region' => 'Bắc', 'latitude' => 20.8449, 'longitude' => 106.6881, 'sort_order' => 2],
            ['name' => 'Quảng Ninh', 'code' => 'QUANGNINH', 'region' => 'Bắc', 'latitude' => 21.0064, 'longitude' => 107.2925, 'sort_order' => 3],
            ['name' => 'Lào Cai', 'code' => 'LAOCAI', 'region' => 'Bắc', 'latitude' => 22.3564, 'longitude' => 103.9750, 'sort_order' => 4],
            ['name' => 'Sơn La', 'code' => 'SONLA', 'region' => 'Bắc', 'latitude' => 21.1022, 'longitude' => 103.7289, 'sort_order' => 5],
            ['name' => 'Điện Biên', 'code' => 'DIENBIEN', 'region' => 'Bắc', 'latitude' => 21.8042, 'longitude' => 103.1072, 'sort_order' => 6],
            ['name' => 'Lai Châu', 'code' => 'LAICHAU', 'region' => 'Bắc', 'latitude' => 22.3964, 'longitude' => 103.4708, 'sort_order' => 7],
            ['name' => 'Yên Bái', 'code' => 'YENBAI', 'region' => 'Bắc', 'latitude' => 21.7167, 'longitude' => 104.9000, 'sort_order' => 8],
            ['name' => 'Tuyên Quang', 'code' => 'TUYENQUANG', 'region' => 'Bắc', 'latitude' => 21.8233, 'longitude' => 105.2142, 'sort_order' => 9],
            ['name' => 'Thái Nguyên', 'code' => 'THAINGUYEN', 'region' => 'Bắc', 'latitude' => 21.5944, 'longitude' => 105.8483, 'sort_order' => 10],
            ['name' => 'Bắc Kạn', 'code' => 'BACKAN', 'region' => 'Bắc', 'latitude' => 22.1473, 'longitude' => 105.8349, 'sort_order' => 11],
            ['name' => 'Cao Bằng', 'code' => 'CAOBANG', 'region' => 'Bắc', 'latitude' => 22.6667, 'longitude' => 106.2500, 'sort_order' => 12],
            ['name' => 'Lạng Sơn', 'code' => 'LANGSON', 'region' => 'Bắc', 'latitude' => 21.8527, 'longitude' => 106.7610, 'sort_order' => 13],
            ['name' => 'Bắc Giang', 'code' => 'BACGIANG', 'region' => 'Bắc', 'latitude' => 21.2731, 'longitude' => 106.1946, 'sort_order' => 14],
            ['name' => 'Phú Thọ', 'code' => 'PHUTHO', 'region' => 'Bắc', 'latitude' => 21.2682, 'longitude' => 105.2381, 'sort_order' => 15],
            ['name' => 'Vĩnh Phúc', 'code' => 'VINHPHUC', 'region' => 'Bắc', 'latitude' => 21.3609, 'longitude' => 105.5474, 'sort_order' => 16],
            ['name' => 'Hà Giang', 'code' => 'HAGIANG', 'region' => 'Bắc', 'latitude' => 22.8233, 'longitude' => 104.9784, 'sort_order' => 17],
            ['name' => 'Hòa Bình', 'code' => 'HOABINH', 'region' => 'Bắc', 'latitude' => 20.8133, 'longitude' => 105.3383, 'sort_order' => 18],
            ['name' => 'Ninh Bình', 'code' => 'NINHBINH', 'region' => 'Bắc', 'latitude' => 20.2506, 'longitude' => 105.9744, 'sort_order' => 19],
            ['name' => 'Thanh Hóa', 'code' => 'THANHHOA', 'region' => 'Bắc', 'latitude' => 19.8066, 'longitude' => 105.7852, 'sort_order' => 20],
            ['name' => 'Nghệ An', 'code' => 'NGHEAN', 'region' => 'Bắc', 'latitude' => 19.2342, 'longitude' => 104.9200, 'sort_order' => 21],
            ['name' => 'Hà Tĩnh', 'code' => 'HATINH', 'region' => 'Bắc', 'latitude' => 18.3333, 'longitude' => 105.9000, 'sort_order' => 22],

            // Miền Trung
            ['name' => 'Quảng Bình', 'code' => 'QUANGBINH', 'region' => 'Trung', 'latitude' => 17.4684, 'longitude' => 106.6222, 'sort_order' => 23],
            ['name' => 'Quảng Trị', 'code' => 'QUANGTRI', 'region' => 'Trung', 'latitude' => 16.7943, 'longitude' => 107.1817, 'sort_order' => 24],
            ['name' => 'Thừa Thiên Huế', 'code' => 'THUATHIENHUE', 'region' => 'Trung', 'latitude' => 16.0544, 'longitude' => 108.2022, 'sort_order' => 25],
            ['name' => 'Đà Nẵng', 'code' => 'DANANG', 'region' => 'Trung', 'latitude' => 16.0544, 'longitude' => 108.2022, 'sort_order' => 26],
            ['name' => 'Quảng Nam', 'code' => 'QUANGNAM', 'region' => 'Trung', 'latitude' => 15.5394, 'longitude' => 108.0191, 'sort_order' => 27],
            ['name' => 'Quảng Ngãi', 'code' => 'QUANGNGAI', 'region' => 'Trung', 'latitude' => 15.1213, 'longitude' => 108.8044, 'sort_order' => 28],
            ['name' => 'Bình Định', 'code' => 'BINHDINH', 'region' => 'Trung', 'latitude' => 14.1667, 'longitude' => 108.9000, 'sort_order' => 29],
            ['name' => 'Phú Yên', 'code' => 'PHUYEN', 'region' => 'Trung', 'latitude' => 13.1667, 'longitude' => 109.1667, 'sort_order' => 30],
            ['name' => 'Khánh Hòa', 'code' => 'KHANHHOA', 'region' => 'Trung', 'latitude' => 12.2500, 'longitude' => 109.0000, 'sort_order' => 31],
            ['name' => 'Ninh Thuận', 'code' => 'NINHTHUAN', 'region' => 'Trung', 'latitude' => 11.7500, 'longitude' => 108.8333, 'sort_order' => 32],
            ['name' => 'Bình Thuận', 'code' => 'BINHTHUAN', 'region' => 'Trung', 'latitude' => 10.9333, 'longitude' => 108.1000, 'sort_order' => 33],

            // Miền Nam
            ['name' => 'TP. Hồ Chí Minh', 'code' => 'HCM', 'region' => 'Nam', 'latitude' => 10.8231, 'longitude' => 106.6297, 'sort_order' => 34],
            ['name' => 'Bình Phước', 'code' => 'BINHPHUOC', 'region' => 'Nam', 'latitude' => 11.7500, 'longitude' => 106.7500, 'sort_order' => 35],
            ['name' => 'Tây Ninh', 'code' => 'TAYNINH', 'region' => 'Nam', 'latitude' => 11.3333, 'longitude' => 106.1000, 'sort_order' => 36],
            ['name' => 'Bình Dương', 'code' => 'BINHDUONG', 'region' => 'Nam', 'latitude' => 11.1667, 'longitude' => 106.6667, 'sort_order' => 37],
            ['name' => 'Đồng Nai', 'code' => 'DONGNAI', 'region' => 'Nam', 'latitude' => 10.9500, 'longitude' => 106.8167, 'sort_order' => 38],
            ['name' => 'Bà Rịa - Vũng Tàu', 'code' => 'BARIAVUNGTAU', 'region' => 'Nam', 'latitude' => 10.4000, 'longitude' => 107.1667, 'sort_order' => 39],
            ['name' => 'Long An', 'code' => 'LONGAN', 'region' => 'Nam', 'latitude' => 10.5333, 'longitude' => 106.4000, 'sort_order' => 40],
            ['name' => 'Tiền Giang', 'code' => 'TIENGIANG', 'region' => 'Nam', 'latitude' => 10.3500, 'longitude' => 106.3500, 'sort_order' => 41],
            ['name' => 'Bến Tre', 'code' => 'BENTRE', 'region' => 'Nam', 'latitude' => 10.2333, 'longitude' => 106.3833, 'sort_order' => 42],
            ['name' => 'Trà Vinh', 'code' => 'TRAVINH', 'region' => 'Nam', 'latitude' => 9.9333, 'longitude' => 106.3333, 'sort_order' => 43],
            ['name' => 'Vĩnh Long', 'code' => 'VINHLONG', 'region' => 'Nam', 'latitude' => 10.2500, 'longitude' => 105.9667, 'sort_order' => 44],
            ['name' => 'Đồng Tháp', 'code' => 'DONGTHAP', 'region' => 'Nam', 'latitude' => 10.5000, 'longitude' => 105.6667, 'sort_order' => 45],
            ['name' => 'An Giang', 'code' => 'ANGIANG', 'region' => 'Nam', 'latitude' => 10.5000, 'longitude' => 105.1667, 'sort_order' => 46],
            ['name' => 'Kiên Giang', 'code' => 'KIENGIANG', 'region' => 'Nam', 'latitude' => 10.0000, 'longitude' => 105.1667, 'sort_order' => 47],
            ['name' => 'Cần Thơ', 'code' => 'CANTHO', 'region' => 'Nam', 'latitude' => 10.0333, 'longitude' => 105.7833, 'sort_order' => 48],
            ['name' => 'Hậu Giang', 'code' => 'HAUGIANG', 'region' => 'Nam', 'latitude' => 9.7833, 'longitude' => 105.6333, 'sort_order' => 49],
            ['name' => 'Sóc Trăng', 'code' => 'SOCTRANG', 'region' => 'Nam', 'latitude' => 9.6000, 'longitude' => 105.9667, 'sort_order' => 50],
            ['name' => 'Bạc Liêu', 'code' => 'BACLIEU', 'region' => 'Nam', 'latitude' => 9.2833, 'longitude' => 105.7167, 'sort_order' => 51],
            ['name' => 'Cà Mau', 'code' => 'CAMAU', 'region' => 'Nam', 'latitude' => 9.1769, 'longitude' => 105.1522, 'sort_order' => 52],
        ];

        foreach ($cities as $city) {
            VietnamCity::updateOrCreate(
                ['code' => $city['code']],
                $city
            );
        }

        $this->command->info('✅ Đã thêm ' . count($cities) . ' tỉnh thành Việt Nam');
    }
}