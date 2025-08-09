<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\VietnamProvinceService;
use App\Models\VietnamCity;
use Illuminate\Support\Facades\Log;

class SyncVietnamProvinces extends Command
{
    protected $signature = 'vietnam:sync-provinces 
                            {--force : Force sync even if data exists}
                            {--test : Test API connection only}';

    protected $description = 'Đồng bộ dữ liệu tỉnh thành từ OpenAPI Vietnam';

    protected $provinceService;

    public function __construct(VietnamProvinceService $provinceService)
    {
        parent::__construct();
        $this->provinceService = $provinceService;
    }

    public function handle()
    {
        $this->info('🔄 Bắt đầu đồng bộ dữ liệu tỉnh thành từ OpenAPI Vietnam...');

        // Test kết nối API
        if ($this->option('test')) {
            $this->testApiConnection();
            return;
        }

        // Kiểm tra kết nối API
        if (!$this->provinceService->testConnection()) {
            $this->error('❌ Không thể kết nối đến API OpenAPI Vietnam');
            return 1;
        }

        $this->info('✅ Kết nối API thành công');

        // Lấy dữ liệu từ API
        $provinces = $this->provinceService->getAllProvinces();

        if (empty($provinces)) {
            $this->error('❌ Không lấy được dữ liệu tỉnh thành từ API');
            return 1;
        }

        $this->info("📊 Tìm thấy " . count($provinces) . " tỉnh thành");

        // Kiểm tra xem có cần force sync không
        if (!$this->option('force') && VietnamCity::count() > 0) {
            if (!$this->confirm('Dữ liệu tỉnh thành đã tồn tại. Bạn có muốn đồng bộ lại không?')) {
                $this->info('🛑 Hủy đồng bộ');
                return 0;
            }
        }

        // Bắt đầu đồng bộ
        $this->syncProvinces($provinces);

        $this->info('✅ Hoàn thành đồng bộ dữ liệu tỉnh thành');

        // Hiển thị thống kê
        $this->showStats();

        return 0;
    }

    protected function testApiConnection()
    {
        $this->info('🔍 Kiểm tra kết nối API...');

        if ($this->provinceService->testConnection()) {
            $this->info('✅ API hoạt động bình thường');

            $stats = $this->provinceService->getStats();
            $this->table(
                ['Thông số', 'Giá trị'],
                [
                    ['Tổng số tỉnh', $stats['total_provinces']],
                    ['Trạng thái API', $stats['api_status']],
                    ['Trạng thái Cache', $stats['cache_status']],
                    ['Cập nhật lần cuối', $stats['last_updated']],
                ]
            );
        } else {
            $this->error('❌ Không thể kết nối đến API');
        }
    }

    protected function syncProvinces($provinces)
    {
        $bar = $this->output->createProgressBar(count($provinces));
        $bar->start();

        $created = 0;
        $updated = 0;
        $skipped = 0;

        foreach ($provinces as $province) {
            try {
                // Tìm kiếm theo tên (loại bỏ "Thành phố", "Tỉnh" để so sánh)
                $cleanName = $this->cleanProvinceName($province['name']);
                $city = VietnamCity::whereRaw('REPLACE(REPLACE(name, "Thành phố ", ""), "Tỉnh ", "") = ?', [$cleanName])
                    ->orWhere('name', $province['name'])
                    ->first();

                $data = [
                    'name' => $province['name'],
                    'code' => (string) $province['code'], // Chuyển thành string
                    'codename' => $province['codename'] ?? null,
                    'region' => $this->determineRegion($province['name']),
                    'latitude' => $province['latitude'] ?? null,
                    'longitude' => $province['longitude'] ?? null,
                    'is_active' => true,
                    'api_data' => json_encode($province),
                ];

                if ($city) {
                    $city->update($data);
                    $updated++;
                } else {
                    VietnamCity::create($data);
                    $created++;
                }

                $bar->advance();
            } catch (\Exception $e) {
                Log::error("Lỗi khi đồng bộ tỉnh {$province['name']}: " . $e->getMessage());
                $skipped++;
                $bar->advance();
            }
        }

        $bar->finish();
        $this->newLine();

        $this->info("📈 Kết quả đồng bộ:");
        $this->info("   ✅ Tạo mới: {$created}");
        $this->info("   🔄 Cập nhật: {$updated}");
        $this->info("   ⚠️  Bỏ qua: {$skipped}");
    }

    protected function determineRegion($provinceName)
    {
        // Chuẩn hóa tên để so sánh
        $cleanName = str_replace(['Thành phố ', 'Tỉnh '], '', trim($provinceName));

        // Ngoại lệ theo yêu cầu của bạn
        $overrides = [
            'Đà Nẵng' => 'Trung',
            'Huế' => 'Trung',
            'Thừa Thiên Huế' => 'Trung',
            'Cần Thơ' => 'Trung',
            'Nghệ An' => 'Trung',
            'Hà Tĩnh' => 'Trung',
            'Thanh Hóa' => 'Trung',
            'Quảng Trị' => 'Trung',
            'Hà Nội' => 'Bắc',
            'Hải Phòng' => 'Bắc',
        ];
        if (isset($overrides[$cleanName])) {
            return $overrides[$cleanName];
        }

        // Mapping gốc
        $northProvinces = [
            'Hà Nội',
            'Hải Phòng',
            'Quảng Ninh',
            'Bắc Giang',
            'Bắc Ninh',
            'Hải Dương',
            'Hưng Yên',
            'Hòa Bình',
            'Phú Thọ',
            'Thái Nguyên',
            'Tuyên Quang',
            'Lào Cai',
            'Yên Bái',
            'Lạng Sơn',
            'Cao Bằng',
            'Bắc Kạn',
            'Thái Bình',
            'Nam Định',
            'Ninh Bình',
            'Vĩnh Phúc',
            'Hà Nam',
            'Điện Biên',
            'Lai Châu',
            'Sơn La',
            'Hà Giang'
        ];

        $centralProvinces = [
            'Đà Nẵng',
            'Quảng Nam',
            'Quảng Ngãi',
            'Bình Định',
            'Phú Yên',
            'Khánh Hòa',
            'Ninh Thuận',
            'Bình Thuận',
            'Kon Tum',
            'Gia Lai',
            'Đắk Lắk',
            'Đắk Nông',
            'Lâm Đồng',
            'Thừa Thiên Huế'
        ];

        $southProvinces = [
            'TP. Hồ Chí Minh',
            'Hồ Chí Minh',
            'Long An',
            'Tiền Giang',
            'Bến Tre',
            'Trà Vinh',
            'Vĩnh Long',
            'Đồng Tháp',
            'An Giang',
            'Kiên Giang',
            'Cần Thơ',
            'Hậu Giang',
            'Sóc Trăng',
            'Bạc Liêu',
            'Cà Mau',
            'Bình Phước',
            'Tây Ninh',
            'Bình Dương',
            'Đồng Nai',
            'Bà Rịa - Vũng Tàu'
        ];

        if (in_array($cleanName, $northProvinces, true)) {
            return 'Bắc';
        }
        if (in_array($cleanName, $centralProvinces, true)) {
            return 'Trung';
        }
        if (in_array($cleanName, $southProvinces, true)) {
            return 'Nam';
        }

        // Không xác định được thì giữ 'Nam' theo yêu cầu
        return 'Nam';
    }

    protected function cleanProvinceName($name)
    {
        return str_replace(['Thành phố ', 'Tỉnh '], '', $name);
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
        $this->info('📊 Thống kê sau đồng bộ:');
        $this->info("   🏙️  Tổng số tỉnh thành: {$totalCities}");
        $this->info("   ✅ Đang hoạt động: {$activeCities}");

        foreach ($regions as $region => $count) {
            $this->info("   📍 {$region} Miền: {$count}");
        }
    }
}