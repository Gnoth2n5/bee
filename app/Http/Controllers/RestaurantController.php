<?php

namespace App\Http\Controllers;

use App\Services\RestaurantService;
use App\Services\GoogleMapsService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;

class RestaurantController extends Controller
{
    protected $restaurantService;
    protected $googleMapsService;

    public function __construct(RestaurantService $restaurantService, GoogleMapsService $googleMapsService)
    {
        $this->restaurantService = $restaurantService;
        $this->googleMapsService = $googleMapsService;
    }

    /**
     * Hiển thị trang bản đồ nhà hàng
     */
    public function index()
    {
        return view('restaurants.index');
    }

    /**
     * API: Tìm kiếm nhà hàng gần đây
     */
    public function searchNearby(Request $request): JsonResponse
    {
        $request->validate([
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
            'radius' => 'nullable|integer|min:100|max:50000',
            'filters' => 'nullable|array'
        ]);

        try {
            $latitude = $request->input('latitude');
            $longitude = $request->input('longitude');
            $radius = $request->input('radius', 5000);
            $filters = $request->input('filters', []);

            $restaurants = $this->restaurantService->searchNearbyRestaurants(
                $latitude,
                $longitude,
                $radius,
                $filters
            );

            return response()->json([
                'success' => true,
                'data' => $restaurants,
                'count' => $restaurants->count()
            ]);
        } catch (\Exception $e) {
            Log::error('Error searching nearby restaurants', [
                'message' => $e->getMessage(),
                'request' => $request->all()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra khi tìm kiếm nhà hàng'
            ], 500);
        }
    }

    /**
     * API: Tìm kiếm nhà hàng theo từ khóa
     */
    public function searchByKeyword(Request $request): JsonResponse
    {
        $request->validate([
            'keyword' => 'required|string|min:1|max:255',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
            'radius' => 'nullable|integer|min:100|max:50000',
            'filters' => 'nullable|array'
        ]);

        try {
            $keyword = $request->input('keyword');
            $latitude = $request->input('latitude');
            $longitude = $request->input('longitude');
            $radius = $request->input('radius', 5000);
            $filters = $request->input('filters', []);

            $restaurants = $this->restaurantService->searchRestaurantsByKeyword(
                $keyword,
                $latitude,
                $longitude,
                $radius,
                $filters
            );

            return response()->json([
                'success' => true,
                'data' => $restaurants,
                'count' => $restaurants->count()
            ]);
        } catch (\Exception $e) {
            Log::error('Error searching restaurants by keyword', [
                'message' => $e->getMessage(),
                'request' => $request->all()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra khi tìm kiếm nhà hàng'
            ], 500);
        }
    }

    /**
     * API: Lấy chi tiết nhà hàng
     */
    public function show($placeId): JsonResponse
    {
        try {
            $restaurant = $this->restaurantService->getRestaurantDetails($placeId);

            if (!$restaurant) {
                return response()->json([
                    'success' => false,
                    'message' => 'Không tìm thấy nhà hàng'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'data' => $restaurant
            ]);
        } catch (\Exception $e) {
            Log::error('Error getting restaurant details', [
                'message' => $e->getMessage(),
                'place_id' => $placeId
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra khi lấy thông tin nhà hàng'
            ], 500);
        }
    }

    /**
     * API: Thêm nhà hàng vào danh sách yêu thích
     */
    public function addToFavorites(Request $request): JsonResponse
    {
        $request->validate([
            'restaurant_id' => 'required|integer|exists:restaurants,id'
        ]);

        if (!auth()->check()) {
            return response()->json([
                'success' => false,
                'message' => 'Vui lòng đăng nhập để thực hiện chức năng này'
            ], 401);
        }

        try {
            $success = $this->restaurantService->addToFavorites(
                auth()->id(),
                $request->input('restaurant_id')
            );

            if ($success) {
                return response()->json([
                    'success' => true,
                    'message' => 'Đã thêm vào danh sách yêu thích'
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Nhà hàng đã có trong danh sách yêu thích'
                ], 400);
            }
        } catch (\Exception $e) {
            Log::error('Error adding restaurant to favorites', [
                'message' => $e->getMessage(),
                'user_id' => auth()->id(),
                'restaurant_id' => $request->input('restaurant_id')
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra khi thêm vào danh sách yêu thích'
            ], 500);
        }
    }

    /**
     * API: Xóa nhà hàng khỏi danh sách yêu thích
     */
    public function removeFromFavorites(Request $request): JsonResponse
    {
        $request->validate([
            'restaurant_id' => 'required|integer|exists:restaurants,id'
        ]);

        if (!auth()->check()) {
            return response()->json([
                'success' => false,
                'message' => 'Vui lòng đăng nhập để thực hiện chức năng này'
            ], 401);
        }

        try {
            $this->restaurantService->removeFromFavorites(
                auth()->id(),
                $request->input('restaurant_id')
            );

            return response()->json([
                'success' => true,
                'message' => 'Đã xóa khỏi danh sách yêu thích'
            ]);
        } catch (\Exception $e) {
            Log::error('Error removing restaurant from favorites', [
                'message' => $e->getMessage(),
                'user_id' => auth()->id(),
                'restaurant_id' => $request->input('restaurant_id')
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra khi xóa khỏi danh sách yêu thích'
            ], 500);
        }
    }

    /**
     * API: Lấy danh sách nhà hàng yêu thích của user
     */
    public function getFavorites(): JsonResponse
    {
        if (!auth()->check()) {
            return response()->json([
                'success' => false,
                'message' => 'Vui lòng đăng nhập để xem danh sách yêu thích'
            ], 401);
        }

        try {
            $favorites = $this->restaurantService->getUserFavorites(auth()->id());

            return response()->json([
                'success' => true,
                'data' => $favorites,
                'count' => $favorites->count()
            ]);
        } catch (\Exception $e) {
            Log::error('Error getting user favorites', [
                'message' => $e->getMessage(),
                'user_id' => auth()->id()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra khi lấy danh sách yêu thích'
            ], 500);
        }
    }

    /**
     * API: Đánh giá nhà hàng
     */
    public function rate(Request $request): JsonResponse
    {
        $request->validate([
            'restaurant_id' => 'required|integer|exists:restaurants,id',
            'rating' => 'required|integer|between:1,5',
            'comment' => 'nullable|string|max:1000'
        ]);

        if (!auth()->check()) {
            return response()->json([
                'success' => false,
                'message' => 'Vui lòng đăng nhập để đánh giá'
            ], 401);
        }

        try {
            $rating = $this->restaurantService->rateRestaurant(
                auth()->id(),
                $request->input('restaurant_id'),
                $request->input('rating'),
                $request->input('comment')
            );

            return response()->json([
                'success' => true,
                'message' => 'Đã đánh giá nhà hàng thành công',
                'data' => $rating
            ]);
        } catch (\Exception $e) {
            Log::error('Error rating restaurant', [
                'message' => $e->getMessage(),
                'user_id' => auth()->id(),
                'restaurant_id' => $request->input('restaurant_id')
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra khi đánh giá nhà hàng'
            ], 500);
        }
    }

    /**
     * API: Lấy đánh giá của nhà hàng
     */
    public function getRatings($restaurantId): JsonResponse
    {
        try {
            $ratings = $this->restaurantService->getRestaurantRatings($restaurantId);

            return response()->json([
                'success' => true,
                'data' => $ratings,
                'count' => $ratings->count()
            ]);
        } catch (\Exception $e) {
            Log::error('Error getting restaurant ratings', [
                'message' => $e->getMessage(),
                'restaurant_id' => $restaurantId
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra khi lấy đánh giá'
            ], 500);
        }
    }

    /**
     * API: Geocoding - chuyển đổi địa chỉ thành tọa độ
     */
    public function geocode(Request $request): JsonResponse
    {
        $request->validate([
            'address' => 'required|string|min:1|max:255'
        ]);

        try {
            $result = $this->googleMapsService->geocodeAddress($request->input('address'));

            if ($result) {
                return response()->json([
                    'success' => true,
                    'data' => $result
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Không tìm thấy địa chỉ này'
                ], 404);
            }
        } catch (\Exception $e) {
            Log::error('Error geocoding address', [
                'message' => $e->getMessage(),
                'address' => $request->input('address')
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra khi tìm kiếm địa chỉ'
            ], 500);
        }
    }

    /**
     * API: Lấy nhà hàng phổ biến
     */
    public function getPopular(): JsonResponse
    {
        try {
            $restaurants = $this->restaurantService->getPopularRestaurants();

            return response()->json([
                'success' => true,
                'data' => $restaurants,
                'count' => $restaurants->count()
            ]);
        } catch (\Exception $e) {
            Log::error('Error getting popular restaurants', [
                'message' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra khi lấy danh sách nhà hàng phổ biến'
            ], 500);
        }
    }

    /**
     * API: Lấy nhà hàng mới
     */
    public function getRecent(): JsonResponse
    {
        try {
            $restaurants = $this->restaurantService->getRecentRestaurants();

            return response()->json([
                'success' => true,
                'data' => $restaurants,
                'count' => $restaurants->count()
            ]);
        } catch (\Exception $e) {
            Log::error('Error getting recent restaurants', [
                'message' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra khi lấy danh sách nhà hàng mới'
            ], 500);
        }
    }
}
