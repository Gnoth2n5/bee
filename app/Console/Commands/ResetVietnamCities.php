<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\VietnamCity;
use App\Services\VietnamProvinceService;

class ResetVietnamCities extends Command
{
    protected $signature = 'vietnam:reset-cities {--force : Force reset without confirmation}';
    protected $description = 'Reset và tạo lại dữ liệu thành phố Việt Nam từ API';

    protected $provinceService;

    public function __construct(VietnamProvinceService $provinceService)
    {
        parent::__construct();
        $this->provinceService = $provinceService;
    }

    public function handle()
    {
        $this->info('🔄 Bắt đầu reset dữ liệu thành phố Việt Nam...');

        // Kiểm tra kết nối API
        if (!$this->provinceService->testConnection()) {
            $this->error('❌ Không thể kết nối đến API OpenAPI Vietnam');
            return 1;
        }

        $this->info('✅ Kết nối API thành công');

        // Xác nhận reset
        if (!$this->option('force')) {
            if (!$this->confirm('Bạn có chắc chắn muốn xóa tất cả dữ liệu thành phố hiện tại và tạo lại từ API?')) {
                $this->info('🛑 Hủy reset');
                return 0;
            }
        }

        // Xóa tất cả dữ liệu cũ
        $oldCount = VietnamCity::count();
        VietnamCity::truncate();
        $this->info("🗑️  Đã xóa {$oldCount} thành phố cũ");

        // Lấy dữ liệu từ API
        $provinces = $this->provinceService->getAllProvinces();

        if (empty($provinces)) {
            $this->error('❌ Không lấy được dữ liệu tỉnh thành từ API');
            return 1;
        }

        $this->info("📊 Tìm thấy " . count($provinces) . " tỉnh thành từ API");

        // Tạo dữ liệu mới
        $bar = $this->output->createProgressBar(count($provinces));
        $bar->start();

        $created = 0;
        $skipped = 0;

        foreach ($provinces as $province) {
            try {
                $data = [
                    'name' => $province['name'],
                    'code' => (string) $province['code'],
                    'codename' => $province['codename'] ?? null,
                    'region' => $this->determineRegion($province['name']),
                    'latitude' => $province['latitude'] ?? 0.0,
                    'longitude' => $province['longitude'] ?? 0.0,
                    'is_active' => true,
                    'api_data' => json_encode($province),
                ];

                VietnamCity::create($data);
                $created++;

                $bar->advance();
            } catch (\Exception $e) {
                $this->error("Lỗi khi tạo tỉnh {$province['name']}: " . $e->getMessage());
                $skipped++;
                $bar->advance();
            }
        }

        $bar->finish();
        $this->newLine();

        $this->info("📈 Kết quả reset:");
        $this->info("   ✅ Tạo mới: {$created}");
        $this->info("   ⚠️  Bỏ qua: {$skipped}");

        // Hiển thị thống kê
        $this->showStats();

        return 0;
    }

    protected function determineRegion($provinceName)
    {
        $northProvinces = [
            'Hà Nội', 'Hải Phòng', 'Quảng Ninh', 'Bắc Giang', 'Bắc Ninh', 'Hải Dương', 
            'Hưng Yên', 'Hòa Bình', 'Phú Thọ', 'Thái Nguyên', 'Tuyên Quang', 'Lào Cai',
            'Yên Bái', 'Lạng Sơn', 'Cao Bằng', 'Bắc Kạn', 'Thái Bình', 'Nam Định',
            'Ninh Bình', 'Thanh Hóa', 'Nghệ An', 'Hà Tĩnh', 'Quảng Bình', 'Quảng Trị',
            'Thừa Thiên Huế', 'Điện Biên', 'Lai Châu', 'Sơn La', 'Hà Giang'
        ];

        $centralProvinces = [
            'Đà Nẵng', 'Quảng Nam', 'Quảng Ngãi', 'Bình Định', 'Phú Yên', 'Khánh Hòa',
            'Ninh Thuận', 'Bình Thuận', 'Kon Tum', 'Gia Lai', 'Đắk Lắk', 'Đắk Nông',
            'Lâm Đồng', 'Bình Phước', 'Tây Ninh', 'Bình Dương', 'Đồng Nai', 'Bà Rịa - Vũng Tàu'
        ];

        $southProvinces = [
            'TP. Hồ Chí Minh', 'Long An', 'Tiền Giang', 'Bến Tre', 'Trà Vinh', 'Vĩnh Long',
            'Đồng Tháp', 'An Giang', 'Kiên Giang', 'Cần Thơ', 'Hậu Giang', 'Sóc Trăng',
            'Bạc Liêu', 'Cà Mau'
        ];

        $cleanName = str_replace(['Thành phố ', 'Tỉnh '], '', $provinceName);

        if (in_array($cleanName, $northProvinces)) {
            return 'Bắc';
        } elseif (in_array($cleanName, $centralProvinces)) {
            return 'Trung';
        } elseif (in_array($cleanName, $southProvinces)) {
            return 'Nam';
        }

        return 'Khác';
    }

    protected function showStats()
    {
        $totalCities = VietnamCity::count();
        $activeCities = VietnamCity::where('is_active', true)->count();
        $regions = VietnamCity::selectRaw('region, COUNT(*) as count')
            ->groupBy('region')
            ->pluck('count', 'region')
            ->toArray();

        $this->newLine();
        $this->info('📊 Thống kê sau reset:');
        $this->info("   🏙️  Tổng số tỉnh thành: {$totalCities}");
        $this->info("   ✅ Đang hoạt động: {$activeCities}");

        foreach ($regions as $region => $count) {
            $this->info("   📍 {$region} Miền: {$count}");
        }
    }
} 