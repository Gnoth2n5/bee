<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\VietnamProvinceService;
use Illuminate\Support\Facades\Http;

class TestVietnamProvinceAPI extends Command
{
    protected $signature = 'vietnam:test-api 
                            {--endpoint= : Test specific endpoint}
                            {--code= : Province code for testing}';

    protected $description = 'Test Vietnam Province API endpoints';

    protected $provinceService;

    public function __construct(VietnamProvinceService $provinceService)
    {
        parent::__construct();
        $this->provinceService = $provinceService;
    }

    public function handle()
    {
        $this->info('🧪 Bắt đầu test API tỉnh thành...');

        $endpoint = $this->option('endpoint');
        $code = $this->option('code');

        if ($endpoint) {
            $this->testSpecificEndpoint($endpoint, $code);
        } else {
            $this->runAllTests();
        }

        return 0;
    }

    protected function runAllTests()
    {
        $this->info('📋 Chạy tất cả test cases...');

        // Test 1: Kết nối API
        $this->testConnection();

        // Test 2: Lấy danh sách tỉnh
        $this->testGetAllProvinces();

        // Test 3: Lấy thông tin tỉnh cụ thể
        $this->testGetProvinceByCode();

        // Test 4: Lấy quận/huyện
        $this->testGetDistricts();

        // Test 5: Lấy xã/phường
        $this->testGetWards();

        // Test 6: Tìm kiếm tỉnh
        $this->testSearchProvinces();

        // Test 7: Thống kê
        $this->testStats();

        $this->info('✅ Hoàn thành tất cả test cases');
    }

    protected function testSpecificEndpoint($endpoint, $code = null)
    {
        $this->info("🎯 Test endpoint: {$endpoint}");

        switch ($endpoint) {
            case 'connection':
                $this->testConnection();
                break;
            case 'provinces':
                $this->testGetAllProvinces();
                break;
            case 'province':
                $this->testGetProvinceByCode($code);
                break;
            case 'districts':
                $this->testGetDistricts($code);
                break;
            case 'wards':
                $this->testGetWards($code);
                break;
            case 'search':
                $this->testSearchProvinces();
                break;
            case 'stats':
                $this->testStats();
                break;
            default:
                $this->error("❌ Endpoint không hợp lệ: {$endpoint}");
                $this->info('Các endpoint có sẵn: connection, provinces, province, districts, wards, search, stats');
        }
    }

    protected function testConnection()
    {
        $this->info('🔍 Test kết nối API...');

        try {
            $response = Http::timeout(10)->get('https://provinces.open-api.vn/api/p/');
            
            if ($response->successful()) {
                $this->info('✅ Kết nối API thành công');
                $this->info("   Status: {$response->status()}");
                $this->info("   Response time: {$response->handlerStats()['total_time']}s");
            } else {
                $this->error("❌ Kết nối API thất bại: {$response->status()}");
            }
        } catch (\Exception $e) {
            $this->error("❌ Lỗi kết nối: {$e->getMessage()}");
        }
    }

    protected function testGetAllProvinces()
    {
        $this->info('📊 Test lấy danh sách tỉnh...');

        try {
            $provinces = $this->provinceService->getAllProvinces();
            
            if (!empty($provinces)) {
                $this->info("✅ Lấy danh sách tỉnh thành công: " . count($provinces) . " tỉnh");
                
                // Hiển thị 5 tỉnh đầu tiên
                $this->info('📋 5 tỉnh đầu tiên:');
                foreach (array_slice($provinces, 0, 5) as $province) {
                    $this->info("   - {$province['name']} (code: {$province['code']})");
                }
            } else {
                $this->error('❌ Không lấy được danh sách tỉnh');
            }
        } catch (\Exception $e) {
            $this->error("❌ Lỗi: {$e->getMessage()}");
        }
    }

    protected function testGetProvinceByCode($code = null)
    {
        $testCode = $code ?: '01'; // Hà Nội
        $this->info("🏙️  Test lấy thông tin tỉnh code: {$testCode}...");

        try {
            $province = $this->provinceService->getProvinceByCode($testCode);
            
            if ($province) {
                $this->info("✅ Lấy thông tin tỉnh thành công");
                $this->info("   Tên: {$province['name']}");
                $this->info("   Code: {$province['code']}");
                $this->info("   Codename: {$province['codename']}");
                if (isset($province['latitude']) && isset($province['longitude'])) {
                    $this->info("   Tọa độ: {$province['latitude']}, {$province['longitude']}");
                }
            } else {
                $this->error("❌ Không tìm thấy tỉnh với code: {$testCode}");
            }
        } catch (\Exception $e) {
            $this->error("❌ Lỗi: {$e->getMessage()}");
        }
    }

    protected function testGetDistricts($provinceCode = null)
    {
        $testCode = $provinceCode ?: '01'; // Hà Nội
        $this->info("🏘️  Test lấy quận/huyện tỉnh code: {$testCode}...");

        try {
            $districts = $this->provinceService->getDistrictsByProvinceCode($testCode);
            
            if (!empty($districts)) {
                $this->info("✅ Lấy quận/huyện thành công: " . count($districts) . " quận/huyện");
                
                // Hiển thị 5 quận/huyện đầu tiên
                $this->info('📋 5 quận/huyện đầu tiên:');
                foreach (array_slice($districts, 0, 5) as $district) {
                    $this->info("   - {$district['name']} (code: {$district['code']})");
                }
            } else {
                $this->error("❌ Không lấy được quận/huyện cho tỉnh code: {$testCode}");
            }
        } catch (\Exception $e) {
            $this->error("❌ Lỗi: {$e->getMessage()}");
        }
    }

    protected function testGetWards($districtCode = null)
    {
        $testCode = $districtCode ?: '001'; // Ba Đình - Hà Nội
        $this->info("🏠 Test lấy xã/phường quận/huyện code: {$testCode}...");

        try {
            $wards = $this->provinceService->getWardsByDistrictCode($testCode);
            
            if (!empty($wards)) {
                $this->info("✅ Lấy xã/phường thành công: " . count($wards) . " xã/phường");
                
                // Hiển thị 5 xã/phường đầu tiên
                $this->info('📋 5 xã/phường đầu tiên:');
                foreach (array_slice($wards, 0, 5) as $ward) {
                    $this->info("   - {$ward['name']} (code: {$ward['code']})");
                }
            } else {
                $this->error("❌ Không lấy được xã/phường cho quận/huyện code: {$testCode}");
            }
        } catch (\Exception $e) {
            $this->error("❌ Lỗi: {$e->getMessage()}");
        }
    }

    protected function testSearchProvinces()
    {
        $searchTerm = 'Hà';
        $this->info("🔍 Test tìm kiếm tỉnh với từ khóa: '{$searchTerm}'...");

        try {
            $provinces = $this->provinceService->searchProvincesByName($searchTerm);
            
            if (!empty($provinces)) {
                $this->info("✅ Tìm kiếm thành công: " . count($provinces) . " kết quả");
                
                foreach ($provinces as $province) {
                    $this->info("   - {$province['name']} (code: {$province['code']})");
                }
            } else {
                $this->info("ℹ️  Không tìm thấy tỉnh nào với từ khóa: '{$searchTerm}'");
            }
        } catch (\Exception $e) {
            $this->error("❌ Lỗi: {$e->getMessage()}");
        }
    }

    protected function testStats()
    {
        $this->info('📈 Test lấy thống kê...');

        try {
            $stats = $this->provinceService->getStats();
            
            $this->info("✅ Lấy thống kê thành công");
            $this->table(
                ['Thông số', 'Giá trị'],
                [
                    ['Tổng số tỉnh', $stats['total_provinces']],
                    ['Trạng thái API', $stats['api_status']],
                    ['Trạng thái Cache', $stats['cache_status']],
                    ['Cập nhật lần cuối', $stats['last_updated']],
                ]
            );
        } catch (\Exception $e) {
            $this->error("❌ Lỗi: {$e->getMessage()}");
        }
    }
} 