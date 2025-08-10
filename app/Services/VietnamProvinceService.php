<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class VietnamProvinceService
{
    protected $baseUrl = 'https://provinces.open-api.vn/api';
    protected $cacheTime = 86400; // 24 giờ

    // Dữ liệu 34 tỉnh thành mới theo Nghị quyết 2025
    protected $newProvinces = [
        // MIỀN BẮC
        [
            'name' => 'Thành phố Hà Nội',
            'code' => 1,
            'codename' => 'thanh_pho_ha_noi',
            'latitude' => 21.0278,
            'longitude' => 105.8342,
            'area' => 3358.6,
            'population' => 8547000,
            'region' => 'Miền Bắc'
        ],
        [
            'name' => 'Tuyên Quang',
            'code' => 2,
            'codename' => 'tuyen_quang',
            'latitude' => 21.8233,
            'longitude' => 105.2142,
            'area' => 13795.50,
            'population' => 1865270,
            'region' => 'Miền Bắc'
        ],
        [
            'name' => 'Lào Cai',
            'code' => 3,
            'codename' => 'lao_cai',
            'latitude' => 22.3984,
            'longitude' => 103.9781,
            'area' => 13256.92,
            'population' => 1778785,
            'region' => 'Miền Bắc'
        ],
        [
            'name' => 'Thái Nguyên',
            'code' => 4,
            'codename' => 'thai_nguyen',
            'latitude' => 21.5944,
            'longitude' => 105.8482,
            'area' => 8375.21,
            'population' => 1799489,
            'region' => 'Miền Bắc'
        ],
        [
            'name' => 'Phú Thọ',
            'code' => 5,
            'codename' => 'phu_tho',
            'latitude' => 21.2682,
            'longitude' => 105.4018,
            'area' => 9361.38,
            'population' => 4022638,
            'region' => 'Miền Bắc'
        ],
        [
            'name' => 'Bắc Ninh',
            'code' => 6,
            'codename' => 'bac_ninh',
            'latitude' => 21.1861,
            'longitude' => 106.0763,
            'area' => 4718.6,
            'population' => 3619433,
            'region' => 'Miền Bắc'
        ],
        [
            'name' => 'Hưng Yên',
            'code' => 7,
            'codename' => 'hung_yen',
            'latitude' => 20.8525,
            'longitude' => 106.0169,
            'area' => 2514.81,
            'population' => 3567943,
            'region' => 'Miền Bắc'
        ],
        [
            'name' => 'Thành phố Hải Phòng',
            'code' => 8,
            'codename' => 'thanh_pho_hai_phong',
            'latitude' => 20.8449,
            'longitude' => 106.6881,
            'area' => 3194.72,
            'population' => 4664124,
            'region' => 'Miền Bắc'
        ],
        [
            'name' => 'Ninh Bình',
            'code' => 9,
            'codename' => 'ninh_binh',
            'latitude' => 20.2506,
            'longitude' => 105.9744,
            'area' => 3942.62,
            'population' => 4412264,
            'region' => 'Miền Bắc'
        ],
        [
            'name' => 'Thanh Hóa',
            'code' => 10,
            'codename' => 'thanh_hoa',
            'latitude' => 19.8066,
            'longitude' => 105.7852,
            'area' => 11114.7,
            'population' => 3640000,
            'region' => 'Miền Bắc'
        ],
        [
            'name' => 'Nghệ An',
            'code' => 11,
            'codename' => 'nghe_an',
            'latitude' => 19.2342,
            'longitude' => 104.9200,
            'area' => 16493.7,
            'population' => 3340000,
            'region' => 'Miền Bắc'
        ],
        [
            'name' => 'Hà Tĩnh',
            'code' => 12,
            'codename' => 'ha_tinh',
            'latitude' => 18.3333,
            'longitude' => 105.9000,
            'area' => 5992.8,
            'population' => 1280000,
            'region' => 'Miền Bắc'
        ],
        [
            'name' => 'Lạng Sơn',
            'code' => 28,
            'codename' => 'lang_son',
            'latitude' => 21.8533,
            'longitude' => 106.7610,
            'area' => 8310.2,
            'population' => 781655,
            'region' => 'Miền Bắc'
        ],
        [
            'name' => 'Lai Châu',
            'code' => 30,
            'codename' => 'lai_chau',
            'latitude' => 22.3964,
            'longitude' => 103.4703,
            'area' => 9068.8,
            'population' => 460196,
            'region' => 'Miền Bắc'
        ],
        [
            'name' => 'Điện Biên',
            'code' => 31,
            'codename' => 'dien_bien',
            'latitude' => 21.8042,
            'longitude' => 103.1077,
            'area' => 9540.9,
            'population' => 598856,
            'region' => 'Miền Bắc'
        ],
        [
            'name' => 'Cao Bằng',
            'code' => 32,
            'codename' => 'cao_bang',
            'latitude' => 22.6667,
            'longitude' => 106.2500,
            'area' => 6707.9,
            'population' => 530341,
            'region' => 'Miền Bắc'
        ],
        [
            'name' => 'Quảng Ninh',
            'code' => 33,
            'codename' => 'quang_ninh',
            'latitude' => 21.0063,
            'longitude' => 107.2925,
            'area' => 6177.7,
            'population' => 1320320,
            'region' => 'Miền Bắc'
        ],
        [
            'name' => 'Sơn La',
            'code' => 34,
            'codename' => 'son_la',
            'latitude' => 21.1023,
            'longitude' => 103.7289,
            'area' => 14123.5,
            'population' => 1248000,
            'region' => 'Miền Bắc'
        ],

        // MIỀN TRUNG
        [
            'name' => 'Quảng Trị',
            'code' => 13,
            'codename' => 'quang_tri',
            'latitude' => 16.7942,
            'longitude' => 107.0024,
            'area' => 12700,
            'population' => 1870845,
            'region' => 'Miền Trung'
        ],
        [
            'name' => 'Thành phố Đà Nẵng',
            'code' => 14,
            'codename' => 'thanh_pho_da_nang',
            'latitude' => 16.0544,
            'longitude' => 108.2022,
            'area' => 11859.59,
            'population' => 3065628,
            'region' => 'Miền Trung'
        ],
        [
            'name' => 'Quảng Ngãi',
            'code' => 15,
            'codename' => 'quang_ngai',
            'latitude' => 15.1213,
            'longitude' => 108.8044,
            'area' => 14832.55,
            'population' => 2161755,
            'region' => 'Miền Trung'
        ],
        [
            'name' => 'Gia Lai',
            'code' => 16,
            'codename' => 'gia_lai',
            'latitude' => 13.8079,
            'longitude' => 108.1094,
            'area' => 21576.53,
            'population' => 3583693,
            'region' => 'Miền Trung'
        ],
        [
            'name' => 'Khánh Hòa',
            'code' => 17,
            'codename' => 'khanh_hoa',
            'latitude' => 12.2583,
            'longitude' => 109.0527,
            'area' => 8555.86,
            'population' => 2243554,
            'region' => 'Miền Trung'
        ],
        [
            'name' => 'Lâm Đồng',
            'code' => 18,
            'codename' => 'lam_dong',
            'latitude' => 11.9404,
            'longitude' => 108.4582,
            'area' => 24233.07,
            'population' => 3872999,
            'region' => 'Miền Trung'
        ],
        [
            'name' => 'Đắk Lắk',
            'code' => 19,
            'codename' => 'dak_lak',
            'latitude' => 12.7103,
            'longitude' => 108.2377,
            'area' => 18096.40,
            'population' => 3346853,
            'region' => 'Miền Trung'
        ],
        [
            'name' => 'Thành phố Huế',
            'code' => 29,
            'codename' => 'thanh_pho_hue',
            'latitude' => 16.4637,
            'longitude' => 107.5909,
            'area' => 5033.2,
            'population' => 652572,
            'region' => 'Miền Trung'
        ],

        // MIỀN NAM
        [
            'name' => 'Thành phố Hồ Chí Minh',
            'code' => 20,
            'codename' => 'thanh_pho_ho_chi_minh',
            'latitude' => 10.8231,
            'longitude' => 106.6297,
            'area' => 6772.59,
            'population' => 14002598,
            'region' => 'Miền Nam'
        ],
        [
            'name' => 'Đồng Nai',
            'code' => 21,
            'codename' => 'dong_nai',
            'latitude' => 10.9574,
            'longitude' => 106.8426,
            'area' => 12737.18,
            'population' => 4491408,
            'region' => 'Miền Nam'
        ],
        [
            'name' => 'Tây Ninh',
            'code' => 22,
            'codename' => 'tay_ninh',
            'latitude' => 11.3353,
            'longitude' => 106.1094,
            'area' => 8536.44,
            'population' => 3254170,
            'region' => 'Miền Nam'
        ],
        [
            'name' => 'Thành phố Cần Thơ',
            'code' => 23,
            'codename' => 'thanh_pho_can_tho',
            'latitude' => 10.0342,
            'longitude' => 105.7883,
            'area' => 6360.83,
            'population' => 4199824,
            'region' => 'Miền Nam'
        ],
        [
            'name' => 'Vĩnh Long',
            'code' => 24,
            'codename' => 'vinh_long',
            'latitude' => 10.2537,
            'longitude' => 105.9722,
            'area' => 6296.20,
            'population' => 4257581,
            'region' => 'Miền Nam'
        ],
        [
            'name' => 'Đồng Tháp',
            'code' => 25,
            'codename' => 'dong_thap',
            'latitude' => 10.4937,
            'longitude' => 105.6882,
            'area' => 5938.64,
            'population' => 4370046,
            'region' => 'Miền Nam'
        ],
        [
            'name' => 'Cà Mau',
            'code' => 26,
            'codename' => 'ca_mau',
            'latitude' => 9.1527,
            'longitude' => 105.1967,
            'area' => 7942.39,
            'population' => 2606672,
            'region' => 'Miền Nam'
        ],
        [
            'name' => 'An Giang',
            'code' => 27,
            'codename' => 'an_giang',
            'latitude' => 10.5216,
            'longitude' => 105.1258,
            'area' => 9888.91,
            'population' => 4952238,
            'region' => 'Miền Nam'
        ]
    ];

    /**
     * Lấy danh sách tất cả tỉnh thành mới (34 tỉnh)
     */
    public function getAllProvinces()
    {
        return Cache::remember('vietnam_provinces_new', $this->cacheTime, function () {
            Log::info('Đã lấy thành công ' . count($this->newProvinces) . ' tỉnh thành mới theo Nghị quyết 2025');
            return $this->newProvinces;
        });
    }

    /**
     * Lấy thông tin chi tiết của một tỉnh theo code
     */
    public function getProvinceByCode($code)
    {
        return Cache::remember("vietnam_province_new_{$code}", $this->cacheTime, function () use ($code) {
            $province = collect($this->newProvinces)->firstWhere('code', $code);

            if ($province) {
                Log::info("Đã lấy thông tin tỉnh: {$province['name']} (code: {$code}) từ dữ liệu mới");
                return $province;
            }

            Log::error("Không tìm thấy tỉnh với code {$code} trong dữ liệu mới");
            return null;
        });
    }

    /**
     * Lấy danh sách quận/huyện của một tỉnh (giữ nguyên từ OpenAPI Vietnam)
     */
    public function getDistrictsByProvinceCode($provinceCode)
    {
        return Cache::remember("vietnam_districts_{$provinceCode}", $this->cacheTime, function () use ($provinceCode) {
            try {
                $response = Http::timeout(30)->get($this->baseUrl . '/p/' . $provinceCode . '?depth=2');

                if ($response->successful()) {
                    $province = $response->json();
                    $districts = $province['districts'] ?? [];

                    // Kiểm tra dữ liệu trả về có phải array không
                    if (!is_array($districts)) {
                        Log::error("OpenAPI Vietnam API trả về dữ liệu quận/huyện không hợp lệ cho tỉnh code {$provinceCode}: " . json_encode($districts));
                        return [];
                    }

                    Log::info("Đã lấy thành công " . count($districts) . " quận/huyện cho tỉnh code {$provinceCode} từ OpenAPI Vietnam");
                    return $districts;
                } else {
                    Log::error("Lỗi khi lấy quận/huyện tỉnh code {$provinceCode} từ OpenAPI Vietnam API: " . $response->status());
                    return [];
                }
            } catch (\Exception $e) {
                Log::error("Exception khi lấy quận/huyện tỉnh code {$provinceCode} từ OpenAPI Vietnam API: " . $e->getMessage());
                return [];
            }
        });
    }

    /**
     * Lấy danh sách xã/phường của một quận/huyện (giữ nguyên từ OpenAPI Vietnam)
     */
    public function getWardsByDistrictCode($districtCode)
    {
        return Cache::remember("vietnam_wards_{$districtCode}", $this->cacheTime, function () use ($districtCode) {
            try {
                $response = Http::timeout(30)->get($this->baseUrl . '/d/' . $districtCode . '?depth=2');

                if ($response->successful()) {
                    $district = $response->json();
                    $wards = $district['wards'] ?? [];

                    // Kiểm tra dữ liệu trả về có phải array không
                    if (!is_array($wards)) {
                        Log::error("OpenAPI Vietnam API trả về dữ liệu xã/phường không hợp lệ cho quận/huyện code {$districtCode}: " . json_encode($wards));
                        return [];
                    }

                    Log::info("Đã lấy thành công " . count($wards) . " xã/phường cho quận/huyện code {$districtCode} từ OpenAPI Vietnam");
                    return $wards;
                } else {
                    Log::error("Lỗi khi lấy xã/phường quận/huyện code {$districtCode} từ OpenAPI Vietnam API: " . $response->status());
                    return [];
                }
            } catch (\Exception $e) {
                Log::error("Exception khi lấy xã/phường quận/huyện code {$districtCode} từ OpenAPI Vietnam API: " . $e->getMessage());
                return [];
            }
        });
    }

    /**
     * Tìm tỉnh theo tên (tìm kiếm mờ)
     */
    public function searchProvincesByName($name)
    {
        $provinces = $this->getAllProvinces();

        // Đảm bảo provinces là array
        if (!is_array($provinces)) {
            return [];
        }

        return collect($provinces)->filter(function ($province) use ($name) {
            return stripos($province['name'], $name) !== false ||
                stripos($province['codename'], $name) !== false;
        })->values()->all();
    }

    /**
     * Lấy tỉnh theo tên chính xác
     */
    public function getProvinceByName($name)
    {
        $provinces = $this->getAllProvinces();

        // Đảm bảo provinces là array
        if (!is_array($provinces)) {
            return null;
        }

        return collect($provinces)->first(function ($province) use ($name) {
            return strtolower($province['name']) === strtolower($name);
        });
    }

    /**
     * Xóa cache
     */
    public function clearCache()
    {
        Cache::forget('vietnam_provinces_new');
        Cache::forget('vietnam_communes_with_coordinates');

        // Xóa cache của các tỉnh cụ thể
        $provinces = $this->getAllProvinces();

        // Đảm bảo provinces là array
        if (is_array($provinces)) {
            foreach ($provinces as $province) {
                Cache::forget("vietnam_province_new_{$province['code']}");
                Cache::forget("vietnam_districts_{$province['code']}");
            }
        }

        Log::info('Đã xóa cache tỉnh thành mới');
    }

    /**
     * Kiểm tra kết nối API
     */
    public function testConnection()
    {
        try {
            $response = Http::timeout(10)->get($this->baseUrl . '/p/');
            return $response->successful();
        } catch (\Exception $e) {
            Log::error('Lỗi kết nối OpenAPI Vietnam API tỉnh thành: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Lấy tất cả xã/phường với tọa độ (tạo từ dữ liệu mới)
     */
    public function getAllCommunesWithCoordinates()
    {
        return Cache::remember('vietnam_communes_with_coordinates', $this->cacheTime, function () {
            try {
                $provinces = $this->getAllProvinces();
                $allCommunes = [];

                foreach ($provinces as $province) {
                    $districts = $this->getDistrictsByProvinceCode($province['code']);

                    foreach ($districts as $district) {
                        $wards = $this->getWardsByDistrictCode($district['code']);

                        foreach ($wards as $ward) {
                            // Sử dụng tọa độ thực từ dữ liệu tỉnh mới
                            $latitude = $province['latitude'] ?? 0;
                            $longitude = $province['longitude'] ?? 0;

                            // Thêm một chút random để phân biệt các xã/phường
                            $latitude += (rand(-50, 50) / 1000);
                            $longitude += (rand(-50, 50) / 1000);

                            $allCommunes[] = [
                                'name' => $ward['name'],
                                'code' => $ward['code'],
                                'codename' => $ward['codename'],
                                'latitude' => $latitude,
                                'longitude' => $longitude,
                                'province' => [
                                    'name' => $province['name'],
                                    'code' => $province['code'],
                                    'area' => $province['area'],
                                    'population' => $province['population'],
                                    'region' => $province['region']
                                ],
                                'district' => [
                                    'name' => $district['name'],
                                    'code' => $district['code']
                                ]
                            ];
                        }
                    }
                }

                Log::info('Đã tạo thành công ' . count($allCommunes) . ' xã/phường với tọa độ từ dữ liệu tỉnh thành mới');
                return $allCommunes;
            } catch (\Exception $e) {
                Log::error('Exception khi tạo dữ liệu xã/phường với tọa độ: ' . $e->getMessage());
                return [];
            }
        });
    }

    /**
     * Lấy tỉnh theo vùng miền
     */
    public function getProvincesByRegion($region)
    {
        $provinces = $this->getAllProvinces();

        // Đảm bảo provinces là array
        if (!is_array($provinces)) {
            return [];
        }

        return collect($provinces)->filter(function ($province) use ($region) {
            return $province['region'] === $region;
        })->values()->all();
    }

    /**
     * Lấy thống kê tỉnh thành
     */
    public function getStats()
    {
        $provinces = $this->getAllProvinces();

        // Đảm bảo provinces là array
        if (!is_array($provinces)) {
            $provinces = [];
        }

        // Thống kê theo vùng miền
        $regionStats = collect($provinces)->groupBy('region')->map(function ($group) {
            return count($group);
        })->toArray();

        return [
            'total_provinces' => count($provinces),
            'region_stats' => $regionStats,
            'last_updated' => now()->format('Y-m-d H:i:s'),
            'api_status' => $this->testConnection() ? 'online' : 'offline',
            'cache_status' => Cache::has('vietnam_provinces_new') ? 'cached' : 'not_cached',
            'data_source' => 'Nghị quyết sắp xếp hành chính 2025'
        ];
    }
}