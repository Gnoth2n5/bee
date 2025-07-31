<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\VietnamCity;

class UpdateVietnamCoordinates extends Command
{
    protected $signature = 'vietnam:update-coordinates {--force : Force update without confirmation}';
    protected $description = 'Cập nhật tọa độ chính xác cho các tỉnh thành Việt Nam';

    protected $coordinates = [
        // Miền Bắc
        'Thành phố Hà Nội' => [21.0278, 105.8342],
        'Tỉnh Hà Giang' => [22.8233, 104.9784],
        'Tỉnh Cao Bằng' => [22.6667, 106.2500],
        'Tỉnh Bắc Kạn' => [22.1473, 105.8348],
        'Tỉnh Tuyên Quang' => [21.8233, 105.2142],
        'Tỉnh Lào Cai' => [22.4833, 103.9500],
        'Tỉnh Điện Biên' => [21.3833, 103.0167],
        'Tỉnh Lai Châu' => [22.4000, 103.4700],
        'Tỉnh Sơn La' => [21.1023, 103.7289],
        'Tỉnh Yên Bái' => [21.7167, 104.9000],
        'Tỉnh Hoà Bình' => [20.8133, 105.3383],
        'Tỉnh Thái Nguyên' => [21.6000, 105.8500],
        'Tỉnh Lạng Sơn' => [21.8500, 106.7500],
        'Tỉnh Quảng Ninh' => [21.0063, 107.2925],
        'Tỉnh Bắc Giang' => [21.2731, 106.1946],
        'Tỉnh Phú Thọ' => [21.2684, 105.2346],
        'Tỉnh Vĩnh Phúc' => [21.3609, 105.5474],
        'Tỉnh Quảng Ninh' => [21.0063, 107.2925],
        'Tỉnh Bắc Ninh' => [21.1861, 106.0763],
        'Tỉnh Hải Dương' => [20.9373, 106.3344],
        'Tỉnh Hưng Yên' => [20.8525, 106.0169],
        'Tỉnh Hòa Bình' => [20.8133, 105.3383],
        'Tỉnh Hà Nam' => [20.5835, 105.9229],
        'Tỉnh Nam Định' => [20.4338, 106.1621],
        'Tỉnh Thái Bình' => [20.4461, 106.3369],
        'Tỉnh Ninh Bình' => [20.2506, 105.9744],
        'Tỉnh Thanh Hóa' => [19.8066, 105.7852],
        'Tỉnh Nghệ An' => [19.2342, 104.9200],
        'Tỉnh Hà Tĩnh' => [18.3333, 105.9000],
        'Tỉnh Quảng Bình' => [17.4684, 106.6222],
        'Tỉnh Quảng Trị' => [16.7942, 107.1818],
        'Thành phố Huế' => [16.4637, 107.5909],
        
        // Miền Trung
        'Thành phố Đà Nẵng' => [16.0544, 108.2022],
        'Tỉnh Quảng Nam' => [15.5394, 108.0191],
        'Tỉnh Quảng Ngãi' => [15.1213, 108.8044],
        'Tỉnh Bình Định' => [14.1667, 108.9000],
        'Tỉnh Phú Yên' => [13.1667, 109.1667],
        'Tỉnh Khánh Hòa' => [12.2500, 109.0000],
        'Tỉnh Ninh Thuận' => [11.7500, 108.8333],
        'Tỉnh Bình Thuận' => [10.9333, 108.1000],
        'Tỉnh Kon Tum' => [14.3500, 108.0000],
        'Tỉnh Gia Lai' => [13.9833, 108.0000],
        'Tỉnh Đắk Lắk' => [12.6667, 108.2333],
        'Tỉnh Đắk Nông' => [12.0000, 107.7000],
        'Tỉnh Lâm Đồng' => [11.9500, 108.4422],
        'Tỉnh Bình Phước' => [11.7500, 106.7500],
        'Tỉnh Tây Ninh' => [11.3333, 106.1000],
        'Tỉnh Bình Dương' => [11.1667, 106.6667],
        'Tỉnh Đồng Nai' => [10.9500, 106.8167],
        'Tỉnh Bà Rịa - Vũng Tàu' => [10.3333, 107.0833],
        
        // Miền Nam
        'Thành phố Hồ Chí Minh' => [10.8231, 106.6297],
        'Tỉnh Long An' => [10.5333, 106.4000],
        'Tỉnh Tiền Giang' => [10.3500, 106.3500],
        'Tỉnh Bến Tre' => [10.2333, 106.3833],
        'Tỉnh Trà Vinh' => [9.9333, 106.3333],
        'Tỉnh Vĩnh Long' => [10.2500, 105.9667],
        'Tỉnh Đồng Tháp' => [10.5167, 105.6889],
        'Tỉnh An Giang' => [10.5216, 105.1258],
        'Tỉnh Kiên Giang' => [10.0000, 105.1667],
        'Thành phố Cần Thơ' => [10.0333, 105.7833],
        'Tỉnh Hậu Giang' => [9.7833, 105.6333],
        'Tỉnh Sóc Trăng' => [9.6000, 105.9667],
        'Tỉnh Bạc Liêu' => [9.2833, 105.7167],
        'Tỉnh Cà Mau' => [9.1769, 105.1522],
    ];

    public function handle()
    {
        $this->info('🔄 Bắt đầu cập nhật tọa độ cho các tỉnh thành Việt Nam...');

        // Xác nhận cập nhật
        if (!$this->option('force')) {
            if (!$this->confirm('Bạn có chắc chắn muốn cập nhật tọa độ cho tất cả tỉnh thành?')) {
                $this->info('🛑 Hủy cập nhật');
                return 0;
            }
        }

        $updated = 0;
        $skipped = 0;
        $notFound = 0;

        $cities = VietnamCity::all();

        foreach ($cities as $city) {
            if (isset($this->coordinates[$city->name])) {
                $coords = $this->coordinates[$city->name];
                
                $city->update([
                    'latitude' => $coords[0],
                    'longitude' => $coords[1]
                ]);
                
                $updated++;
                $this->info("✅ Cập nhật: {$city->name} -> {$coords[0]}, {$coords[1]}");
            } else {
                $notFound++;
                $this->warn("⚠️  Không tìm thấy tọa độ cho: {$city->name}");
            }
        }

        $this->newLine();
        $this->info("📈 Kết quả cập nhật:");
        $this->info("   ✅ Cập nhật: {$updated}");
        $this->info("   ⚠️  Không tìm thấy: {$notFound}");

        // Kiểm tra lại
        $this->checkResults();

        return 0;
    }

    protected function checkResults()
    {
        $this->newLine();
        $this->info('🔍 Kiểm tra kết quả sau cập nhật:');

        $totalCities = VietnamCity::count();
        $withCoords = VietnamCity::whereNotNull('latitude')
            ->whereNotNull('longitude')
            ->where('latitude', '!=', 0.0)
            ->where('longitude', '!=', 0.0)
            ->count();

        $this->info("📊 Tổng thể: {$withCoords}/{$totalCities} tỉnh có tọa độ");

        if ($withCoords > 0) {
            $this->info('✅ Cập nhật tọa độ thành công!');
            $this->info('💡 Bây giờ bạn có thể test API thời tiết với: php artisan weather:check-coordinates --test');
        } else {
            $this->error('❌ Không có tỉnh nào được cập nhật tọa độ');
        }
    }
} 