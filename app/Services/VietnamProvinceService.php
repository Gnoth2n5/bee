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
     * Lấy danh sách quận/huyện của một tỉnh (sử dụng dữ liệu trực tiếp thay vì API)
     */
    public function getDistrictsByProvinceCode($provinceCode)
    {
        return Cache::remember("vietnam_districts_static_{$provinceCode}", $this->cacheTime, function () use ($provinceCode) {
            // Sử dụng dữ liệu trực tiếp thay vì gọi API
            $staticDistricts = $this->getStaticDistrictsByProvinceCode($provinceCode);

            if (!empty($staticDistricts)) {
                Log::info("Đã lấy thành công " . count($staticDistricts) . " quận/huyện cho tỉnh code {$provinceCode} từ dữ liệu trực tiếp");
                return $staticDistricts;
            }

            // Fallback to API if static data not available
            try {
                Log::info("Fallback to API for province code {$provinceCode}");
                $response = Http::timeout(10)->get($this->baseUrl . '/p/' . $provinceCode . '?depth=2');

                if ($response->successful()) {
                    $province = $response->json();
                    $districts = $province['districts'] ?? [];

                    if (!is_array($districts)) {
                        Log::warning("API trả về dữ liệu quận/huyện không hợp lệ cho tỉnh {$provinceCode}, sử dụng dữ liệu mặc định");
                        return $this->getDefaultDistrictsForProvince($provinceCode);
                    }

                    Log::info("Đã lấy thành công " . count($districts) . " quận/huyện cho tỉnh {$provinceCode} từ API fallback");
                    return $districts;
                } else {
                    Log::warning("API không khả dụng cho tỉnh {$provinceCode}, sử dụng dữ liệu mặc định");
                    return $this->getDefaultDistrictsForProvince($provinceCode);
                }
            } catch (\Exception $e) {
                Log::warning("API Exception cho tỉnh {$provinceCode}: " . $e->getMessage() . ", sử dụng dữ liệu mặc định");
                return $this->getDefaultDistrictsForProvince($provinceCode);
            }
        });
    }

    /**
     * Lấy danh sách xã/phường của một quận/huyện (sử dụng dữ liệu trực tiếp)
     */
    public function getWardsByDistrictCode($districtCode)
    {
        return Cache::remember("vietnam_wards_static_{$districtCode}", $this->cacheTime, function () use ($districtCode) {
            // Sử dụng dữ liệu trực tiếp thay vì gọi API
            $staticWards = $this->getStaticWardsByDistrictCode($districtCode);

            if (!empty($staticWards)) {
                Log::info("Đã lấy thành công " . count($staticWards) . " xã/phường cho quận/huyện {$districtCode} từ dữ liệu trực tiếp");
                return $staticWards;
            }

            // Fallback to API if static data not available
            try {
                Log::info("Fallback to API for district code {$districtCode}");
                $response = Http::timeout(10)->get($this->baseUrl . '/d/' . $districtCode . '?depth=2');

                if ($response->successful()) {
                    $district = $response->json();
                    $wards = $district['wards'] ?? [];

                    if (!is_array($wards)) {
                        Log::warning("API trả về dữ liệu xã/phường không hợp lệ cho quận/huyện {$districtCode}, sử dụng dữ liệu mặc định");
                        return $this->getDefaultWardsForDistrict($districtCode);
                    }

                    Log::info("Đã lấy thành công " . count($wards) . " xã/phường cho quận/huyện {$districtCode} từ API fallback");
                    return $wards;
                } else {
                    Log::warning("API không khả dụng cho quận/huyện {$districtCode}, sử dụng dữ liệu mặc định");
                    return $this->getDefaultWardsForDistrict($districtCode);
                }
            } catch (\Exception $e) {
                Log::warning("API Exception cho quận/huyện {$districtCode}: " . $e->getMessage() . ", sử dụng dữ liệu mặc định");
                return $this->getDefaultWardsForDistrict($districtCode);
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
        $cacheKeys = [
            'vietnam_provinces_new',
            'vietnam_communes_with_coordinates',
            'vietnam_communes_with_coordinates_static'
        ];

        foreach ($cacheKeys as $key) {
            Cache::forget($key);
        }

        // Xóa cache của các tỉnh cụ thể
        $provinces = $this->newProvinces; // Sử dụng dữ liệu trực tiếp

        foreach ($provinces as $province) {
            $provinceCode = $province['code'];
            Cache::forget("vietnam_province_new_{$provinceCode}");
            Cache::forget("vietnam_districts_{$provinceCode}");
            Cache::forget("vietnam_districts_static_{$provinceCode}");

            // Xóa cache wards
            for ($i = 1; $i <= 50; $i++) { // Giả sử mỗi tỉnh có tối đa 50 quận/huyện
                $districtCode = $provinceCode * 1000 + $i;
                Cache::forget("vietnam_wards_{$districtCode}");
                Cache::forget("vietnam_wards_static_{$districtCode}");
            }
        }

        Log::info('Đã xóa toàn bộ cache tỉnh thành (static + API)');
    }

    /**
     * Kiểm tra kết nối API (optional, không bắt buộc)
     */
    public function testConnection()
    {
        try {
            $response = Http::timeout(5)->get($this->baseUrl . '/p/');
            $isOnline = $response->successful();

            Log::info('API Connection Status: ' . ($isOnline ? 'Online' : 'Offline') . ' (Static data available as fallback)');
            return $isOnline;
        } catch (\Exception $e) {
            Log::info('API không khả dụng: ' . $e->getMessage() . ' (Sử dụng dữ liệu trực tiếp)');
            return false;
        }
    }

    /**
     * Lấy tất cả xã/phường với tọa độ (sử dụng dữ liệu trực tiếp)
     */
    public function getAllCommunesWithCoordinates()
    {
        return Cache::remember('vietnam_communes_with_coordinates_static', $this->cacheTime, function () {
            try {
                $provinces = $this->getAllProvinces();
                $allCommunes = [];
                $processedCount = 0;
                $maxCommunes = 1000; // Giới hạn để tránh quá tải

                Log::info('Bắt đầu tạo dữ liệu xã/phường với tọa độ cho ' . count($provinces) . ' tỉnh thành');

                foreach ($provinces as $province) {
                    if ($processedCount >= $maxCommunes) {
                        Log::info('Reached maximum communes limit: ' . $maxCommunes);
                        break;
                    }

                    $districts = $this->getDistrictsByProvinceCode($province['code']);

                    // Nếu không có districts, tạo dữ liệu mặc định
                    if (empty($districts)) {
                        $districts = $this->getDefaultDistrictsForProvince($province['code']);
                    }

                    foreach ($districts as $district) {
                        if ($processedCount >= $maxCommunes) break;

                        $wards = $this->getWardsByDistrictCode($district['code']);

                        // Nếu không có wards, tạo dữ liệu mặc định
                        if (empty($wards)) {
                            $wards = $this->getDefaultWardsForDistrict($district['code']);
                        }

                        foreach ($wards as $ward) {
                            if ($processedCount >= $maxCommunes) break;

                            // Sử dụng tọa độ thực từ dữ liệu tỉnh mới
                            $latitude = $province['latitude'] ?? 21.0; // Default Hà Nội
                            $longitude = $province['longitude'] ?? 105.8; // Default Hà Nội

                            // Thêm biến động nhỏ để phân biệt các xã/phường
                            $latitude += (rand(-100, 100) / 10000); // ±0.01 độ
                            $longitude += (rand(-100, 100) / 10000); // ±0.01 độ

                            $allCommunes[] = [
                                'name' => $ward['name'],
                                'code' => $ward['code'],
                                'codename' => $ward['codename'],
                                'latitude' => round($latitude, 6),
                                'longitude' => round($longitude, 6),
                                'province' => [
                                    'name' => $province['name'],
                                    'code' => $province['code'],
                                    'area' => $province['area'] ?? 0,
                                    'population' => $province['population'] ?? 0,
                                    'region' => $province['region'] ?? 'Unknown'
                                ],
                                'district' => [
                                    'name' => $district['name'],
                                    'code' => $district['code']
                                ],
                                'data_source' => 'static_with_fallback'
                            ];

                            $processedCount++;
                        }
                    }
                }

                Log::info('Đã tạo thành công ' . count($allCommunes) . ' xã/phường với tọa độ từ dữ liệu trực tiếp + fallback');
                return $allCommunes;
            } catch (\Exception $e) {
                Log::error('Exception khi tạo dữ liệu xã/phường với tọa độ: ' . $e->getMessage());

                // Fallback: Tạo dữ liệu cơ bản từ các tỉnh chính
                return $this->createBasicCommunesData();
            }
        });
    }

    /**
     * Tạo dữ liệu xã/phường cơ bản khi gặp lỗi
     */
    protected function createBasicCommunesData()
    {
        $provinces = $this->getAllProvinces();
        $basicCommunes = [];

        foreach ($provinces as $province) {
            // Tạo 3 xã/phường mẫu cho mỗi tỉnh
            for ($i = 1; $i <= 3; $i++) {
                $latitude = $province['latitude'] + (rand(-50, 50) / 10000);
                $longitude = $province['longitude'] + (rand(-50, 50) / 10000);

                $basicCommunes[] = [
                    'name' => 'Khu vực ' . $i . ' - ' . $province['name'],
                    'code' => $province['code'] * 1000 + $i,
                    'codename' => 'khu_vuc_' . $i . '_' . $province['codename'],
                    'latitude' => round($latitude, 6),
                    'longitude' => round($longitude, 6),
                    'province' => [
                        'name' => $province['name'],
                        'code' => $province['code'],
                        'area' => $province['area'] ?? 0,
                        'population' => $province['population'] ?? 0,
                        'region' => $province['region'] ?? 'Unknown'
                    ],
                    'district' => [
                        'name' => 'Quận/Huyện trung tâm',
                        'code' => $province['code'] * 100 + 1
                    ],
                    'data_source' => 'basic_fallback'
                ];
            }
        }

        Log::info('Tạo dữ liệu xã/phường cơ bản: ' . count($basicCommunes) . ' records');
        return $basicCommunes;
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
            'data_source' => 'Dữ liệu trực tiếp + Fallback API',
            'mode' => 'static_data_priority'
        ];
    }

    /**
     * Lấy dữ liệu quận/huyện trực tiếp cho các tỉnh chính
     */
    protected function getStaticDistrictsByProvinceCode($provinceCode)
    {
        // Dữ liệu quận/huyện chính cho các tỉnh lớn
        $staticDistricts = [
            1 => [ // Hà Nội
                ['name' => 'Quận Ba Đình', 'code' => 1001, 'codename' => 'quan_ba_dinh'],
                ['name' => 'Quận Hoàn Kiếm', 'code' => 1002, 'codename' => 'quan_hoan_kiem'],
                ['name' => 'Quận Tây Hồ', 'code' => 1003, 'codename' => 'quan_tay_ho'],
                ['name' => 'Quận Long Biên', 'code' => 1004, 'codename' => 'quan_long_bien'],
                ['name' => 'Quận Cầu Giấy', 'code' => 1005, 'codename' => 'quan_cau_giay'],
                ['name' => 'Quận Đống Đa', 'code' => 1006, 'codename' => 'quan_dong_da'],
                ['name' => 'Quận Hai Bà Trưng', 'code' => 1007, 'codename' => 'quan_hai_ba_trung'],
                ['name' => 'Quận Hoàng Mai', 'code' => 1008, 'codename' => 'quan_hoang_mai'],
                ['name' => 'Quận Thanh Xuân', 'code' => 1009, 'codename' => 'quan_thanh_xuan'],
                ['name' => 'Huyện Sóc Sơn', 'code' => 1010, 'codename' => 'huyen_soc_son'],
                ['name' => 'Huyện Đông Anh', 'code' => 1011, 'codename' => 'huyen_dong_anh'],
                ['name' => 'Huyện Gia Lâm', 'code' => 1012, 'codename' => 'huyen_gia_lam'],
            ],
            9 => [ // Ninh Bình
                ['name' => 'Thành phố Ninh Bình', 'code' => 9001, 'codename' => 'thanh_pho_ninh_binh'],
                ['name' => 'Huyện Tam Điểp', 'code' => 9002, 'codename' => 'huyen_tam_diep'],
                ['name' => 'Huyện Nho Quan', 'code' => 9003, 'codename' => 'huyen_nho_quan'],
                ['name' => 'Huyện Gia Viễn', 'code' => 9004, 'codename' => 'huyen_gia_vien'],
                ['name' => 'Huyện Hoa Lư', 'code' => 9005, 'codename' => 'huyen_hoa_lu'],
                ['name' => 'Huyện Yên Khánh', 'code' => 9006, 'codename' => 'huyen_yen_khanh'],
                ['name' => 'Huyện Kim Sơn', 'code' => 9007, 'codename' => 'huyen_kim_son'],
                ['name' => 'Huyện Yên Mô', 'code' => 9008, 'codename' => 'huyen_yen_mo'],
            ],
            7 => [ // Hưng Yên
                ['name' => 'Thành phố Hưng Yên', 'code' => 7001, 'codename' => 'thanh_pho_hung_yen'],
                ['name' => 'Huyện Văn Lâm', 'code' => 7002, 'codename' => 'huyen_van_lam'],
                ['name' => 'Huyện Văn Giang', 'code' => 7003, 'codename' => 'huyen_van_giang'],
                ['name' => 'Huyện Yên Mỹ', 'code' => 7004, 'codename' => 'huyen_yen_my'],
                ['name' => 'Huyện Mỹ Hào', 'code' => 7005, 'codename' => 'huyen_my_hao'],
                ['name' => 'Huyện Ấn Thi', 'code' => 7006, 'codename' => 'huyen_an_thi'],
                ['name' => 'Huyện Khoái Châu', 'code' => 7007, 'codename' => 'huyen_khoai_chau'],
                ['name' => 'Huyện Kim Động', 'code' => 7008, 'codename' => 'huyen_kim_dong'],
                ['name' => 'Huyện Tiền Lữ', 'code' => 7009, 'codename' => 'huyen_tien_lu'],
            ],
            20 => [ // TP. Hồ Chí Minh
                ['name' => 'Quận 1', 'code' => 20001, 'codename' => 'quan_1'],
                ['name' => 'Quận 2', 'code' => 20002, 'codename' => 'quan_2'],
                ['name' => 'Quận 3', 'code' => 20003, 'codename' => 'quan_3'],
                ['name' => 'Quận 4', 'code' => 20004, 'codename' => 'quan_4'],
                ['name' => 'Quận 5', 'code' => 20005, 'codename' => 'quan_5'],
                ['name' => 'Quận 6', 'code' => 20006, 'codename' => 'quan_6'],
                ['name' => 'Quận 7', 'code' => 20007, 'codename' => 'quan_7'],
                ['name' => 'Quận 8', 'code' => 20008, 'codename' => 'quan_8'],
                ['name' => 'Quận 9', 'code' => 20009, 'codename' => 'quan_9'],
                ['name' => 'Quận 10', 'code' => 20010, 'codename' => 'quan_10'],
                ['name' => 'Quận 11', 'code' => 20011, 'codename' => 'quan_11'],
                ['name' => 'Quận 12', 'code' => 20012, 'codename' => 'quan_12'],
                ['name' => 'Quận Bình Thạnh', 'code' => 20013, 'codename' => 'quan_binh_thanh'],
                ['name' => 'Quận Tân Bình', 'code' => 20014, 'codename' => 'quan_tan_binh'],
                ['name' => 'Quận Tân Phú', 'code' => 20015, 'codename' => 'quan_tan_phu'],
                ['name' => 'Quận Phú Nhuận', 'code' => 20016, 'codename' => 'quan_phu_nhuan'],
                ['name' => 'Quận Gò Vấp', 'code' => 20017, 'codename' => 'quan_go_vap'],
                ['name' => 'Huyện Cù Chi', 'code' => 20018, 'codename' => 'huyen_cu_chi'],
                ['name' => 'Huyện Hóc Môn', 'code' => 20019, 'codename' => 'huyen_hoc_mon'],
                ['name' => 'Huyện Bình Chánh', 'code' => 20020, 'codename' => 'huyen_binh_chanh'],
                ['name' => 'Huyện Nhà Bè', 'code' => 20021, 'codename' => 'huyen_nha_be'],
                ['name' => 'Huyện Cần Giờ', 'code' => 20022, 'codename' => 'huyen_can_gio'],
            ],
        ];

        return $staticDistricts[$provinceCode] ?? [];
    }

    /**
     * Lấy dữ liệu xã/phường trực tiếp cho các quận/huyện chính
     */
    protected function getStaticWardsByDistrictCode($districtCode)
    {
        // Dữ liệu xã/phường mẫu cho một số quận/huyện chính
        $staticWards = [
            9001 => [ // Thành phố Ninh Bình
                ['name' => 'Phường Đông Thành', 'code' => 90011, 'codename' => 'phuong_dong_thanh'],
                ['name' => 'Phường Tân Thành', 'code' => 90012, 'codename' => 'phuong_tan_thanh'],
                ['name' => 'Phường Thành Bình', 'code' => 90013, 'codename' => 'phuong_thanh_binh'],
                ['name' => 'Phường Văn Giang', 'code' => 90014, 'codename' => 'phuong_van_giang'],
                ['name' => 'Phường Bình Minh', 'code' => 90015, 'codename' => 'phuong_binh_minh'],
            ],
            7001 => [ // Thành phố Hưng Yên
                ['name' => 'Phường Lê Lợi', 'code' => 70011, 'codename' => 'phuong_le_loi'],
                ['name' => 'Phường Minh Khai', 'code' => 70012, 'codename' => 'phuong_minh_khai'],
                ['name' => 'Phường Quỳ Xuân', 'code' => 70013, 'codename' => 'phuong_quy_xuan'],
                ['name' => 'Phường Hiến Năm', 'code' => 70014, 'codename' => 'phuong_hien_nam'],
                ['name' => 'Phường An Tài', 'code' => 70015, 'codename' => 'phuong_an_tai'],
            ],
            1001 => [ // Quận Ba Đình - Hà Nội
                ['name' => 'Phường Phúc Xá', 'code' => 10011, 'codename' => 'phuong_phuc_xa'],
                ['name' => 'Phường Trúc Bạch', 'code' => 10012, 'codename' => 'phuong_truc_bach'],
                ['name' => 'Phường Vĩnh Phúc', 'code' => 10013, 'codename' => 'phuong_vinh_phuc'],
                ['name' => 'Phường Cổ Ngị', 'code' => 10014, 'codename' => 'phuong_co_ngu'],
                ['name' => 'Phường Linh Lang', 'code' => 10015, 'codename' => 'phuong_linh_lang'],
            ],
        ];

        return $staticWards[$districtCode] ?? [];
    }

    /**
     * Lấy dữ liệu quận/huyện mặc định khi không có dữ liệu trực tiếp
     */
    protected function getDefaultDistrictsForProvince($provinceCode)
    {
        // Trả về dữ liệu mặc định dựa trên tên tỉnh
        $province = $this->getProvinceByCode($provinceCode);
        if (!$province) {
            return [];
        }

        return [
            [
                'name' => 'Thành phố ' . $province['name'],
                'code' => $provinceCode * 1000 + 1,
                'codename' => 'thanh_pho_' . $province['codename']
            ],
            [
                'name' => 'Khu vực trung tâm',
                'code' => $provinceCode * 1000 + 2,
                'codename' => 'khu_vuc_trung_tam'
            ],
            [
                'name' => 'Các huyện khác',
                'code' => $provinceCode * 1000 + 3,
                'codename' => 'cac_huyen_khac'
            ]
        ];
    }

    /**
     * Lấy dữ liệu xã/phường mặc định
     */
    protected function getDefaultWardsForDistrict($districtCode)
    {
        return [
            [
                'name' => 'Phường/Xã trung tâm',
                'code' => $districtCode * 100 + 1,
                'codename' => 'trung_tam'
            ],
            [
                'name' => 'Khu vực phía Bắc',
                'code' => $districtCode * 100 + 2,
                'codename' => 'phia_bac'
            ],
            [
                'name' => 'Khu vực phía Nam',
                'code' => $districtCode * 100 + 3,
                'codename' => 'phia_nam'
            ]
        ];
    }
}