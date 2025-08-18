<?php

namespace App\Services;

use App\Models\Restaurant;
use App\Models\RestaurantRating;
use App\Models\User;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class RestaurantService
{
    protected $googleMapsService;

    public function __construct(GoogleMapsService $googleMapsService)
    {
        $this->googleMapsService = $googleMapsService;
    }

    /**
     * Lấy tất cả nhà hàng từ database
     */
    public function loadRestaurants()
    {
        try {
            return Restaurant::where('is_active', true)
                ->orderBy('rating', 'desc')
                ->orderBy('user_ratings_total', 'desc')
                ->get();
        } catch (\Exception $e) {
            Log::error('Error loading restaurants', [
                'message' => $e->getMessage()
            ]);
            return collect();
        }
    }

    /**
     * Tìm kiếm nhà hàng gần đây
     */
    public function searchNearbyRestaurants($latitude, $longitude, $radius = 5000, $filters = [])
    {
        try {
            // Lấy dữ liệu từ Google Places API
            $placesData = $this->googleMapsService->searchNearbyRestaurants($latitude, $longitude, $radius);

            // Lưu hoặc cập nhật vào database
            $restaurants = collect($placesData)->map(function ($placeData) {
                return $this->createOrUpdateRestaurant($placeData);
            });

            // Áp dụng filters
            $restaurants = $this->applyFilters($restaurants, $filters);

            return $restaurants;
        } catch (\Exception $e) {
            Log::error('Error searching nearby restaurants', [
                'message' => $e->getMessage(),
                'latitude' => $latitude,
                'longitude' => $longitude
            ]);
            return collect();
        }
    }

    /**
     * Tìm kiếm nhà hàng theo từ khóa
     */
    public function searchRestaurantsByKeyword($keyword, $latitude = null, $longitude = null, $radius = 5000, $filters = [])
    {
        try {
            // Lấy dữ liệu từ Google Places API
            $placesData = $this->googleMapsService->searchRestaurantsByKeyword($keyword, $latitude, $longitude, $radius);

            // Lưu hoặc cập nhật vào database
            $restaurants = collect($placesData)->map(function ($placeData) {
                return $this->createOrUpdateRestaurant($placeData);
            });

            // Áp dụng filters
            $restaurants = $this->applyFilters($restaurants, $filters);

            return $restaurants;
        } catch (\Exception $e) {
            Log::error('Error searching restaurants by keyword', [
                'message' => $e->getMessage(),
                'keyword' => $keyword
            ]);
            return collect();
        }
    }

    /**
     * Lấy chi tiết nhà hàng
     */
    public function getRestaurantDetails($placeId)
    {
        try {
            // Chỉ lấy từ database
            $restaurant = Restaurant::where('place_id', $placeId)->first();
            return $restaurant;
        } catch (\Exception $e) {
            Log::error('Error getting restaurant details', [
                'message' => $e->getMessage(),
                'place_id' => $placeId
            ]);
            return null;
        }
    }

    /**
     * Tạo hoặc cập nhật nhà hàng
     */
    protected function createOrUpdateRestaurant($placeData)
    {
        $restaurant = Restaurant::updateOrCreate(
            ['place_id' => $placeData['place_id']],
            [
                'name' => $placeData['name'],
                'formatted_address' => $placeData['formatted_address'] ?? $placeData['address'],
                'latitude' => $placeData['latitude'],
                'longitude' => $placeData['longitude'],
                'rating' => $placeData['rating'],
                'user_ratings_total' => $placeData['user_ratings_total'],
                'formatted_phone_number' => $placeData['formatted_phone_number'] ?? null,
                'website' => $placeData['website'] ?? null,
                'price_level' => $placeData['price_level'],
                'types' => $placeData['types'],
                'opening_hours' => $placeData['opening_hours'] ?? null,
                'photos' => $placeData['photos'],
                'reviews' => $placeData['reviews'] ?? [],
                'is_active' => true,
                'last_updated' => now()
            ]
        );

        return $restaurant;
    }

    /**
     * Kiểm tra dữ liệu có cũ không (24 giờ)
     */
    protected function isDataStale($restaurant)
    {
        if (!$restaurant->last_updated) {
            return true; // Dữ liệu cũ nếu không có last_updated
        }
        return $restaurant->last_updated->diffInHours(now()) > 24;
    }

    /**
     * Áp dụng filters cho danh sách nhà hàng
     */
    protected function applyFilters($restaurants, $filters)
    {
        if (isset($filters['min_rating'])) {
            $restaurants = $restaurants->filter(function ($restaurant) use ($filters) {
                return $restaurant->rating >= $filters['min_rating'];
            });
        }

        if (isset($filters['max_price_level'])) {
            $restaurants = $restaurants->filter(function ($restaurant) use ($filters) {
                return $restaurant->price_level <= $filters['max_price_level'];
            });
        }

        if (isset($filters['open_now']) && $filters['open_now']) {
            $restaurants = $restaurants->filter(function ($restaurant) {
                return $restaurant->opening_hours &&
                    isset($restaurant->opening_hours['open_now']) &&
                    $restaurant->opening_hours['open_now'];
            });
        }

        if (isset($filters['type'])) {
            $restaurants = $restaurants->filter(function ($restaurant) use ($filters) {
                return in_array($filters['type'], $restaurant->types);
            });
        }

        return $restaurants;
    }

    /**
     * Thêm nhà hàng vào danh sách yêu thích
     */
    public function addToFavorites($userId, $restaurantId)
    {
        try {
            $user = User::findOrFail($userId);
            $restaurant = Restaurant::findOrFail($restaurantId);

            if (!$user->favoriteRestaurants()->where('restaurant_id', $restaurantId)->exists()) {
                $user->favoriteRestaurants()->attach($restaurantId);
                return true;
            }

            return false;
        } catch (\Exception $e) {
            Log::error('Error adding restaurant to favorites', [
                'message' => $e->getMessage(),
                'user_id' => $userId,
                'restaurant_id' => $restaurantId
            ]);
            return false;
        }
    }

    /**
     * Xóa nhà hàng khỏi danh sách yêu thích
     */
    public function removeFromFavorites($userId, $restaurantId)
    {
        try {
            $user = User::findOrFail($userId);
            $user->favoriteRestaurants()->detach($restaurantId);
            return true;
        } catch (\Exception $e) {
            Log::error('Error removing restaurant from favorites', [
                'message' => $e->getMessage(),
                'user_id' => $userId,
                'restaurant_id' => $restaurantId
            ]);
            return false;
        }
    }

    /**
     * Lấy danh sách nhà hàng yêu thích của user
     */
    public function getUserFavorites($userId)
    {
        try {
            $user = User::findOrFail($userId);
            return $user->favoriteRestaurants()->orderBy('created_at', 'desc')->get();
        } catch (\Exception $e) {
            Log::error('Error getting user favorites', [
                'message' => $e->getMessage(),
                'user_id' => $userId
            ]);
            return collect();
        }
    }

    /**
     * Đánh giá nhà hàng
     */
    public function rateRestaurant($userId, $restaurantId, $rating, $comment = null)
    {
        try {
            $existingRating = RestaurantRating::where('user_id', $userId)
                ->where('restaurant_id', $restaurantId)
                ->first();

            if ($existingRating) {
                $existingRating->update([
                    'rating' => $rating,
                    'comment' => $comment,
                    'updated_at' => now()
                ]);
                return $existingRating;
            } else {
                return RestaurantRating::create([
                    'user_id' => $userId,
                    'restaurant_id' => $restaurantId,
                    'rating' => $rating,
                    'comment' => $comment,
                    'is_verified' => true
                ]);
            }
        } catch (\Exception $e) {
            Log::error('Error rating restaurant', [
                'message' => $e->getMessage(),
                'user_id' => $userId,
                'restaurant_id' => $restaurantId
            ]);
            return null;
        }
    }

    /**
     * Lấy đánh giá của user cho nhà hàng
     */
    public function getUserRating($userId, $restaurantId)
    {
        return RestaurantRating::where('user_id', $userId)
            ->where('restaurant_id', $restaurantId)
            ->first();
    }

    /**
     * Lấy đánh giá của nhà hàng
     */
    public function getRestaurantRatings($restaurantId, $limit = 10)
    {
        return RestaurantRating::where('restaurant_id', $restaurantId)
            ->where('is_verified', true)
            ->with('user')
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();
    }

    /**
     * Lấy nhà hàng phổ biến
     */
    public function getPopularRestaurants($limit = 10)
    {
        return Restaurant::where('is_active', true)
            ->where('rating', '>=', 4.0)
            ->where('user_ratings_total', '>=', 10)
            ->orderBy('rating', 'desc')
            ->orderBy('user_ratings_total', 'desc')
            ->limit($limit)
            ->get();
    }

    /**
     * Lấy nhà hàng mới
     */
    public function getRecentRestaurants($limit = 10)
    {
        return Restaurant::where('is_active', true)
            ->orderBy('last_updated', 'desc')
            ->limit($limit)
            ->get();
    }

    /**
     * Tìm kiếm nhà hàng trong database
     */
    public function searchRestaurantsInDatabase($keyword, $latitude = null, $longitude = null, $radius = 5)
    {
        $query = Restaurant::where('is_active', true)
            ->where(function ($q) use ($keyword) {
                $q->where('name', 'like', "%{$keyword}%")
                    ->orWhere('formatted_address', 'like', "%{$keyword}%")
                    ->orWhereJsonContains('types', $keyword);
            });

        if ($latitude && $longitude) {
            $query = $query->nearby($latitude, $longitude, $radius);
        }

        return $query->orderBy('rating', 'desc')->get();
    }

    /**
     * Cập nhật thông tin nhà hàng từ Google Places API
     */
    public function refreshRestaurantData($placeId)
    {
        try {
            $placeData = $this->googleMapsService->getRestaurantDetails($placeId);

            if ($placeData) {
                return $this->createOrUpdateRestaurant($placeData);
            }

            return null;
        } catch (\Exception $e) {
            Log::error('Error refreshing restaurant data', [
                'message' => $e->getMessage(),
                'place_id' => $placeId
            ]);
            return null;
        }
    }
}
