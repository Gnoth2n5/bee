<?php

namespace App\Livewire\Restaurants;

use App\Services\RestaurantService;
use App\Services\GoogleMapsService;
use Livewire\Component;
use Livewire\Attributes\On;

class RestaurantMap extends Component
{
    public $latitude;
    public $longitude;
    public $zoom;
    public $apiKey;
    public $restaurants = [];
    public $selectedRestaurant = null;
    public $searchQuery = '';
    public $selectedArea = 'all'; // Thêm biến lọc khu vực

    protected $restaurantService;
    protected $googleMapsService;

    public function boot(RestaurantService $restaurantService, GoogleMapsService $googleMapsService)
    {
        $this->restaurantService = $restaurantService;
        $this->googleMapsService = $googleMapsService;
    }

    public function mount()
    {
        $this->latitude = 20.8; // Tọa độ giữa Hà Nội và Hà Nam
        $this->longitude = 105.9; // Tọa độ giữa Hà Nội và Hà Nam
        $this->zoom = 9; // Zoom level để hiển thị cả hai tỉnh
        $this->apiKey = config('services.google_maps.api_key');
        $this->loadRestaurants();
    }

    public function loadRestaurants()
    {
        $this->restaurants = $this->restaurantService->loadRestaurants();
    }

    public function search()
    {
        $this->loadRestaurants();
    }

    public function clearSearch()
    {
        $this->searchKeyword = '';
        $this->loadRestaurants();
    }

    public function selectRestaurant($placeId)
    {
        $this->selectedRestaurant = $this->restaurantService->getRestaurantDetails($placeId);

        if ($this->selectedRestaurant) {
            $this->dispatch('focusMapTo', [
                'place_id' => $this->selectedRestaurant->place_id,
                'latitude' => $this->selectedRestaurant->latitude,
                'longitude' => $this->selectedRestaurant->longitude,
                'name' => $this->selectedRestaurant->name,
                'address' => $this->selectedRestaurant->formatted_address ?? $this->selectedRestaurant->short_address ?? null,
            ]);
        }
    }

    public function closeRestaurantDetails()
    {
        $this->selectedRestaurant = null;
    }

    public function updateFilters()
    {
        $this->loadRestaurants();
    }

    public function resetFilters()
    {
        $this->filters = [
            'min_rating' => 0,
            'max_price_level' => 4,
            'open_now' => false,
            'type' => ''
        ];
        $this->loadRestaurants();
    }

    public function getCurrentLocation()
    {
        $this->dispatch('getCurrentLocation');
    }

    #[On('locationUpdated')]
    public function updateLocation($latitude, $longitude)
    {
        $this->latitude = $latitude;
        $this->longitude = $longitude;
        $this->userLocation = ['lat' => $latitude, 'lng' => $longitude];
        $this->loadRestaurants();
    }

    public function geocodeAddress()
    {
        if (empty($this->searchKeyword)) {
            return;
        }

        $this->loading = true;

        try {
            $geocodeResult = $this->googleMapsService->geocodeAddress($this->searchKeyword);

            if ($geocodeResult) {
                $this->latitude = $geocodeResult['latitude'];
                $this->longitude = $geocodeResult['longitude'];
                $this->loadRestaurants();
            } else {
                $this->error = 'Không tìm thấy địa chỉ này';
            }
        } catch (\Exception $e) {
            $this->error = 'Có lỗi xảy ra khi tìm kiếm địa chỉ: ' . $e->getMessage();
        }

        $this->loading = false;
    }

    public function toggleFilters()
    {
        $this->showFilters = !$this->showFilters;
    }

    public function addToFavorites($restaurantId)
    {
        if (!auth()->check()) {
            $this->error = 'Vui lòng đăng nhập để thêm vào danh sách yêu thích';
            return;
        }

        $success = $this->restaurantService->addToFavorites(auth()->id(), $restaurantId);

        if ($success) {
            $this->dispatch('restaurantAddedToFavorites', $restaurantId);
        } else {
            $this->error = 'Nhà hàng đã có trong danh sách yêu thích';
        }
    }

    public function removeFromFavorites($restaurantId)
    {
        if (!auth()->check()) {
            return;
        }

        $this->restaurantService->removeFromFavorites(auth()->id(), $restaurantId);
        $this->dispatch('restaurantRemovedFromFavorites', $restaurantId);
    }

    public function rateRestaurant($restaurantId, $rating, $comment = null)
    {
        if (!auth()->check()) {
            $this->error = 'Vui lòng đăng nhập để đánh giá';
            return;
        }

        $this->restaurantService->rateRestaurant(auth()->id(), $restaurantId, $rating, $comment);
        $this->dispatch('restaurantRated', $restaurantId);

        // Cập nhật thông tin nhà hàng
        if ($this->selectedRestaurant && $this->selectedRestaurant->id == $restaurantId) {
            $this->selectedRestaurant = $this->restaurantService->getRestaurantDetails($this->selectedRestaurant->place_id);
        }
    }

    public function filterByArea($area)
    {
        $this->selectedArea = $area;

        if ($area === 'hanoi') {
            $this->latitude = 21.0368;
            $this->longitude = 105.8342;
            $this->zoom = 12;
        } elseif ($area === 'hanam') {
            $this->latitude = 20.5456;
            $this->longitude = 105.9189;
            $this->zoom = 12;
        } else {
            $this->latitude = 20.8;
            $this->longitude = 105.9;
            $this->zoom = 9;
        }

        $this->dispatch('updateMapCenter', [
            'latitude' => $this->latitude,
            'longitude' => $this->longitude,
            'zoom' => $this->zoom
        ]);

        // Cập nhật markers trên bản đồ
        $this->dispatch('updateMapMarkers', [
            'restaurants' => $this->filteredRestaurants
        ]);
    }

    public function getFilteredRestaurantsProperty()
    {
        if ($this->selectedArea === 'hanoi') {
            return $this->restaurants->filter(function ($restaurant) {
                return str_contains(strtolower($restaurant->formatted_address), 'hà nội') ||
                    str_contains(strtolower($restaurant->formatted_address), 'ha noi');
            });
        } elseif ($this->selectedArea === 'hanam') {
            return $this->restaurants->filter(function ($restaurant) {
                return str_contains(strtolower($restaurant->formatted_address), 'hà nam') ||
                    str_contains(strtolower($restaurant->formatted_address), 'ha nam');
            });
        }

        return $this->restaurants;
    }

    public function render()
    {
        return view('livewire.restaurants.restaurant-map', [
            'apiKey' => config('services.google_maps.api_key'),
            'placesApiKey' => config('services.google_maps.places_api_key'),
        ]);
    }
}
