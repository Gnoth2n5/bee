<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class GoogleMapsService
{
    protected $apiKey;
    protected $placesApiKey;
    protected $baseUrl = 'https://maps.googleapis.com/maps/api';

    public function __construct()
    {
        $this->apiKey = config('services.google_maps.api_key');
        $this->placesApiKey = config('services.google_maps.places_api_key');
    }

    /**
     * Tìm kiếm nhà hàng gần đây
     */
    public function searchNearbyRestaurants($latitude, $longitude, $radius = 5000, $type = 'restaurant')
    {
        $cacheKey = "nearby_restaurants_{$latitude}_{$longitude}_{$radius}_{$type}";

        return Cache::remember($cacheKey, 3600, function () use ($latitude, $longitude, $radius, $type) {
            try {
                $response = Http::get("{$this->baseUrl}/place/nearbysearch/json", [
                    'location' => "{$latitude},{$longitude}",
                    'radius' => $radius,
                    'type' => $type,
                    'key' => $this->placesApiKey,
                    'language' => 'vi'
                ]);

                if ($response->successful()) {
                    $data = $response->json();

                    if ($data['status'] === 'OK') {
                        return $this->formatRestaurantData($data['results']);
                    }

                    Log::warning('Google Places API error', [
                        'status' => $data['status'],
                        'error_message' => $data['error_message'] ?? 'Unknown error'
                    ]);
                }

                return [];
            } catch (\Exception $e) {
                Log::error('Google Places API exception', [
                    'message' => $e->getMessage(),
                    'latitude' => $latitude,
                    'longitude' => $longitude
                ]);
                return [];
            }
        });
    }

    /**
     * Tìm kiếm nhà hàng theo từ khóa
     */
    public function searchRestaurantsByKeyword($keyword, $latitude = null, $longitude = null, $radius = 5000)
    {
        $cacheKey = "restaurant_search_{$keyword}_{$latitude}_{$longitude}_{$radius}";

        return Cache::remember($cacheKey, 1800, function () use ($keyword, $latitude, $longitude, $radius) {
            try {
                $params = [
                    'query' => $keyword,
                    'type' => 'restaurant',
                    'key' => $this->placesApiKey,
                    'language' => 'vi'
                ];

                if ($latitude && $longitude) {
                    $params['location'] = "{$latitude},{$longitude}";
                    $params['radius'] = $radius;
                }

                $response = Http::get("{$this->baseUrl}/place/textsearch/json", $params);

                if ($response->successful()) {
                    $data = $response->json();

                    if ($data['status'] === 'OK') {
                        return $this->formatRestaurantData($data['results']);
                    }

                    Log::warning('Google Places Text Search API error', [
                        'status' => $data['status'],
                        'error_message' => $data['error_message'] ?? 'Unknown error'
                    ]);
                }

                return [];
            } catch (\Exception $e) {
                Log::error('Google Places Text Search API exception', [
                    'message' => $e->getMessage(),
                    'keyword' => $keyword
                ]);
                return [];
            }
        });
    }

    /**
     * Lấy chi tiết nhà hàng
     */
    public function getRestaurantDetails($placeId)
    {
        $cacheKey = "restaurant_details_{$placeId}";

        return Cache::remember($cacheKey, 7200, function () use ($placeId) {
            try {
                $response = Http::get("{$this->baseUrl}/place/details/json", [
                    'place_id' => $placeId,
                    'fields' => 'name,formatted_address,geometry,rating,user_ratings_total,photos,formatted_phone_number,website,opening_hours,price_level,reviews,types',
                    'key' => $this->placesApiKey,
                    'language' => 'vi'
                ]);

                if ($response->successful()) {
                    $data = $response->json();

                    if ($data['status'] === 'OK') {
                        return $this->formatRestaurantDetail($data['result']);
                    }

                    Log::warning('Google Places Details API error', [
                        'status' => $data['status'],
                        'error_message' => $data['error_message'] ?? 'Unknown error'
                    ]);
                }

                return null;
            } catch (\Exception $e) {
                Log::error('Google Places Details API exception', [
                    'message' => $e->getMessage(),
                    'place_id' => $placeId
                ]);
                return null;
            }
        });
    }

    /**
     * Lấy ảnh của địa điểm
     */
    public function getPlacePhoto($photoReference, $maxWidth = 400, $maxHeight = 300)
    {
        return "{$this->baseUrl}/place/photo?maxwidth={$maxWidth}&maxheight={$maxHeight}&photo_reference={$photoReference}&key={$this->placesApiKey}";
    }

    /**
     * Geocoding - chuyển đổi địa chỉ thành tọa độ
     */
    public function geocodeAddress($address)
    {
        $cacheKey = "geocode_{$address}";

        return Cache::remember($cacheKey, 86400, function () use ($address) {
            try {
                $response = Http::get("{$this->baseUrl}/geocode/json", [
                    'address' => $address,
                    'key' => $this->apiKey,
                    'language' => 'vi'
                ]);

                if ($response->successful()) {
                    $data = $response->json();

                    if ($data['status'] === 'OK' && !empty($data['results'])) {
                        $location = $data['results'][0]['geometry']['location'];
                        return [
                            'latitude' => $location['lat'],
                            'longitude' => $location['lng'],
                            'formatted_address' => $data['results'][0]['formatted_address']
                        ];
                    }
                }

                return null;
            } catch (\Exception $e) {
                Log::error('Google Geocoding API exception', [
                    'message' => $e->getMessage(),
                    'address' => $address
                ]);
                return null;
            }
        });
    }

    /**
     * Reverse Geocoding - chuyển đổi tọa độ thành địa chỉ
     */
    public function reverseGeocode($latitude, $longitude)
    {
        $cacheKey = "reverse_geocode_{$latitude}_{$longitude}";

        return Cache::remember($cacheKey, 86400, function () use ($latitude, $longitude) {
            try {
                $response = Http::get("{$this->baseUrl}/geocode/json", [
                    'latlng' => "{$latitude},{$longitude}",
                    'key' => $this->apiKey,
                    'language' => 'vi'
                ]);

                if ($response->successful()) {
                    $data = $response->json();

                    if ($data['status'] === 'OK' && !empty($data['results'])) {
                        return [
                            'formatted_address' => $data['results'][0]['formatted_address'],
                            'components' => $data['results'][0]['address_components']
                        ];
                    }
                }

                return null;
            } catch (\Exception $e) {
                Log::error('Google Reverse Geocoding API exception', [
                    'message' => $e->getMessage(),
                    'latitude' => $latitude,
                    'longitude' => $longitude
                ]);
                return null;
            }
        });
    }

    /**
     * Format dữ liệu nhà hàng từ API response
     */
    protected function formatRestaurantData($results)
    {
        return collect($results)->map(function ($place) {
            return [
                'place_id' => $place['place_id'],
                'name' => $place['name'],
                'address' => $place['vicinity'] ?? $place['formatted_address'] ?? '',
                'latitude' => $place['geometry']['location']['lat'],
                'longitude' => $place['geometry']['location']['lng'],
                'rating' => $place['rating'] ?? null,
                'user_ratings_total' => $place['user_ratings_total'] ?? 0,
                'price_level' => $place['price_level'] ?? null,
                'types' => $place['types'] ?? [],
                'photos' => isset($place['photos']) ? collect($place['photos'])->take(3)->map(function ($photo) {
                    return $this->getPlacePhoto($photo['photo_reference']);
                })->toArray() : [],
                'open_now' => $place['opening_hours']['open_now'] ?? null,
                'distance' => $place['distance'] ?? null
            ];
        })->toArray();
    }

    /**
     * Format chi tiết nhà hàng
     */
    protected function formatRestaurantDetail($place)
    {
        return [
            'place_id' => $place['place_id'],
            'name' => $place['name'],
            'formatted_address' => $place['formatted_address'],
            'latitude' => $place['geometry']['location']['lat'],
            'longitude' => $place['geometry']['location']['lng'],
            'rating' => $place['rating'] ?? null,
            'user_ratings_total' => $place['user_ratings_total'] ?? 0,
            'formatted_phone_number' => $place['formatted_phone_number'] ?? null,
            'website' => $place['website'] ?? null,
            'price_level' => $place['price_level'] ?? null,
            'types' => $place['types'] ?? [],
            'photos' => isset($place['photos']) ? collect($place['photos'])->take(5)->map(function ($photo) {
                return $this->getPlacePhoto($photo['photo_reference']);
            })->toArray() : [],
            'opening_hours' => $place['opening_hours'] ?? null,
            'reviews' => isset($place['reviews']) ? collect($place['reviews'])->take(5)->map(function ($review) {
                return [
                    'author_name' => $review['author_name'],
                    'rating' => $review['rating'],
                    'text' => $review['text'],
                    'time' => $review['time'],
                    'profile_photo_url' => $review['profile_photo_url'] ?? null
                ];
            })->toArray() : []
        ];
    }

    /**
     * Tính khoảng cách giữa hai điểm
     */
    public function calculateDistance($lat1, $lon1, $lat2, $lon2)
    {
        $theta = $lon1 - $lon2;
        $dist = sin(deg2rad($lat1)) * sin(deg2rad($lat2)) + cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * cos(deg2rad($theta));
        $dist = acos($dist);
        $dist = rad2deg($dist);
        $miles = $dist * 60 * 1.1515;
        return $miles * 1.609344; // Chuyển đổi sang km
    }
}
