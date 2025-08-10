<?php

namespace App\Http\Controllers;

use App\Services\VietnamProvinceService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class VietnamProvinceController extends Controller
{
    protected $provinceService;

    public function __construct(VietnamProvinceService $provinceService)
    {
        $this->provinceService = $provinceService;
    }

    /**
     * Lấy danh sách tất cả tỉnh thành
     */
    public function index(): JsonResponse
    {
        try {
            $provinces = $this->provinceService->getAllProvinces();

            // Đảm bảo provinces là array
            if (!is_array($provinces)) {
                $provinces = [];
            }

            return response()->json([
                'success' => true,
                'data' => $provinces,
                'message' => 'Lấy danh sách tỉnh thành thành công',
                'total' => count($provinces)
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Lỗi khi lấy danh sách tỉnh thành: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Lấy thông tin chi tiết của một tỉnh
     */
    public function show($code): JsonResponse
    {
        try {
            $province = $this->provinceService->getProvinceByCode($code);

            if (!$province) {
                return response()->json([
                    'success' => false,
                    'message' => 'Không tìm thấy tỉnh thành với mã: ' . $code
                ], 404);
            }

            return response()->json([
                'success' => true,
                'data' => $province,
                'message' => 'Lấy thông tin tỉnh thành thành công'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Lỗi khi lấy thông tin tỉnh thành: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Lấy danh sách quận/huyện của một tỉnh
     */
    public function districts($provinceCode): JsonResponse
    {
        try {
            $districts = $this->provinceService->getDistrictsByProvinceCode($provinceCode);

            // Đảm bảo districts là array
            if (!is_array($districts)) {
                $districts = [];
            }

            return response()->json([
                'success' => true,
                'data' => $districts,
                'message' => 'Lấy danh sách xã/phường thành công',
                'total' => count($districts)
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Lỗi khi lấy danh sách quận/huyện: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Lấy danh sách xã/phường của một quận/huyện
     */
    public function wards($districtCode): JsonResponse
    {
        try {
            $wards = $this->provinceService->getWardsByDistrictCode($districtCode);

            // Đảm bảo wards là array
            if (!is_array($wards)) {
                $wards = [];
            }

            return response()->json([
                'success' => true,
                'data' => $wards,
                'message' => 'Lấy danh sách xã/phường thành công',
                'total' => count($wards)
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Lỗi khi lấy danh sách xã/phường: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Tìm kiếm tỉnh thành theo tên
     */
    public function search(Request $request): JsonResponse
    {
        try {
            $request->validate([
                'name' => 'required|string|min:2'
            ]);

            $name = $request->input('name');
            $provinces = $this->provinceService->searchProvincesByName($name);

            // Đảm bảo provinces là array
            if (!is_array($provinces)) {
                $provinces = [];
            }

            return response()->json([
                'success' => true,
                'data' => $provinces,
                'message' => 'Tìm kiếm tỉnh thành thành công',
                'total' => count($provinces),
                'search_term' => $name
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Lỗi khi tìm kiếm tỉnh thành: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Lấy thống kê tỉnh thành
     */
    public function stats(): JsonResponse
    {
        try {
            $stats = $this->provinceService->getStats();

            return response()->json([
                'success' => true,
                'data' => $stats,
                'message' => 'Lấy thống kê tỉnh thành thành công'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Lỗi khi lấy thống kê tỉnh thành: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Kiểm tra trạng thái API
     */
    public function health(): JsonResponse
    {
        try {
            $isOnline = $this->provinceService->testConnection();

            return response()->json([
                'success' => true,
                'data' => [
                    'status' => $isOnline ? 'online' : 'offline',
                    'timestamp' => now()->toISOString(),
                    'message' => $isOnline ? 'API hoạt động bình thường' : 'API không khả dụng'
                ],
                'message' => 'Kiểm tra trạng thái API thành công'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'data' => [
                    'status' => 'error',
                    'timestamp' => now()->toISOString(),
                    'message' => 'Lỗi khi kiểm tra API'
                ],
                'message' => 'Lỗi khi kiểm tra trạng thái API: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Lấy tỉnh theo vùng miền
     */
    public function provincesByRegion($region): JsonResponse
    {
        try {
            $provinces = $this->provinceService->getProvincesByRegion($region);

            // Đảm bảo provinces là array
            if (!is_array($provinces)) {
                $provinces = [];
            }

            return response()->json([
                'success' => true,
                'data' => $provinces,
                'message' => "Lấy danh sách tỉnh thành {$region} thành công",
                'total' => count($provinces),
                'region' => $region
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => "Lỗi khi lấy danh sách tỉnh thành {$region}: " . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Lấy tất cả xã/phường với tọa độ
     */
    public function communesWithCoordinates(): JsonResponse
    {
        try {
            $communes = $this->provinceService->getAllCommunesWithCoordinates();

            // Đảm bảo communes là array
            if (!is_array($communes)) {
                $communes = [];
            }

            return response()->json([
                'success' => true,
                'data' => $communes,
                'message' => 'Lấy danh sách xã/phường với tọa độ thành công',
                'total' => count($communes)
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Lỗi khi lấy danh sách xã/phường với tọa độ: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Xóa cache tỉnh thành
     */
    public function clearCache(): JsonResponse
    {
        try {
            $this->provinceService->clearCache();

            return response()->json([
                'success' => true,
                'message' => 'Đã xóa cache tỉnh thành thành công'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Lỗi khi xóa cache: ' . $e->getMessage()
            ], 500);
        }
    }
}