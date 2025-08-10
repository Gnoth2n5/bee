<?php

namespace App\Livewire;

use App\Models\VietnamCity;
use App\Services\WeatherService;
use App\Services\WeatherRecipeService;
use Livewire\Component;
use Livewire\Attributes\On;

class WeatherRecipeSlideshow extends Component
{
    public $currentSlide = 0;
    public $autoPlay = true;
    public $interval = 5000; // 5 seconds
    public $weatherData = null;
    public $suggestions = [];
    public $loading = false;
    public $error = null;
    public $selectedCity = 'HCM'; // Default to Ho Chi Minh City
    public $userLatitude = null;
    public $userLongitude = null;
    public $nearestCity = null;





    public function mount()
    {

        // Kiểm tra xem có thông tin vị trí từ session không
        if (session('user_location')) {
            $userLocation = session('user_location');
            $this->userLatitude = $userLocation['latitude'];
            $this->userLongitude = $userLocation['longitude'];
            $this->selectedCity = $userLocation['nearest_city_code'];
            $this->nearestCity = \App\Models\VietnamCity::where('code', $userLocation['nearest_city_code'])->first();

            \Log::info('Loaded user location from session: ' . $userLocation['nearest_city_name'] . ' (' . $userLocation['nearest_city_code'] . ')');
        } else {
            // Tự động lấy vị trí khi component được load
            $this->dispatch('auto-get-location');
        }

        $this->loadWeatherAndSuggestions();
    }

    /**
     * Random chọn thành phố khi người dùng không cho phép vị trí
     */
    public function randomCity()
    {
        \Log::info('randomCity called - user denied location permission');

        // Lấy danh sách tất cả thành phố có dữ liệu thời tiết
        $citiesWithWeather = \App\Models\WeatherData::select('city_code')
            ->distinct()
            ->whereNotNull('temperature')
            ->pluck('city_code')
            ->toArray();

        if (empty($citiesWithWeather)) {
            // Nếu không có thành phố nào có dữ liệu thời tiết, lấy tất cả thành phố
            $randomCity = \App\Models\VietnamCity::active()->inRandomOrder()->first();
        } else {
            // Random chọn từ các thành phố có dữ liệu thời tiết
            $randomCityCode = $citiesWithWeather[array_rand($citiesWithWeather)];
            $randomCity = \App\Models\VietnamCity::where('code', $randomCityCode)->first();
        }

        if ($randomCity) {
            \Log::info('Random city selected: ' . $randomCity->name . ' (' . $randomCity->code . ')');
            $this->selectedCity = $randomCity->code;
            $this->nearestCity = $randomCity;

            // Lưu vào session để dùng ở trang khác
            session([
                'user_location' => [
                    'latitude' => $randomCity->latitude,
                    'longitude' => $randomCity->longitude,
                    'nearest_city_code' => $randomCity->code,
                    'nearest_city_name' => $randomCity->name,
                    'is_random' => true
                ]
            ]);

            $this->loadWeatherAndSuggestions();
            $this->dispatch('alert', message: 'Đã chọn ngẫu nhiên thành phố: ' . $randomCity->name);
        } else {
            \Log::info('No random city found');
            $this->dispatch('alert', message: 'Không thể chọn thành phố ngẫu nhiên');
        }
    }

    public function detectUserLocation()
    {
        // Method này sẽ được gọi từ JavaScript để set tọa độ
    }

    public function getUserLocationFromBrowser()
    {
        \Log::info('getUserLocationFromBrowser called');

        // Test với tọa độ Ninh Bình
        $this->setUserLocation(20.2506, 105.9744);

        return 'Location request sent';
    }

    public function testMethod()
    {
        \Log::info('testMethod called from WeatherRecipeSlideshow');
        $this->selectedCity = 'NINHBINH';
        $this->loadWeatherAndSuggestions();
        $this->dispatch('alert', message: 'Test method executed');
    }

    public function simpleTest()
    {
        \Log::info('simpleTest called');
        $this->dispatch('alert', message: 'Simple test works!');
    }

    public function ping()
    {
        \Log::info('ping called');
        $this->dispatch('alert', message: 'pong');
    }

    public function debugTest()
    {
        \Log::info('debugTest called');
        $this->dispatch('alert', message: 'Debug test works!');
    }


    public function setUserLocation($latitude, $longitude)
    {
        $this->userLatitude = $latitude;
        $this->userLongitude = $longitude;

        // Debug log
        \Log::info('setUserLocation called', [
            'latitude' => $latitude,
            'longitude' => $longitude
        ]);

        // Tìm thành phố gần nhất
        $this->findNearestCity();

        // Debug log
        \Log::info('findNearestCity result', [
            'nearestCity' => $this->nearestCity ? $this->nearestCity->name : 'null',
            'nearestCityCode' => $this->nearestCity ? $this->nearestCity->code : 'null'
        ]);

        // Cập nhật thành phố được chọn nếu tìm thấy
        if ($this->nearestCity) {
            $this->selectedCity = $this->nearestCity->code;
            \Log::info('Updated selectedCity', ['selectedCity' => $this->selectedCity]);

            // Load lại weather và suggestions với thành phố mới
            $this->loadWeatherAndSuggestions();
        } else {
            \Log::warning('No nearest city found');
        }
    }

    public function findNearestCity()
    {
        if (!$this->userLatitude || !$this->userLongitude) {
            return;
        }

        $cities = \App\Models\VietnamCity::active()->get();
        $nearestCity = null;
        $shortestDistance = PHP_FLOAT_MAX;

        foreach ($cities as $city) {
            $distance = $this->calculateDistance(
                $this->userLatitude,
                $this->userLongitude,
                $city->latitude,
                $city->longitude
            );

            if ($distance < $shortestDistance) {
                $shortestDistance = $distance;
                $nearestCity = $city;
            }
        }

        $this->nearestCity = $nearestCity;
    }

    public function calculateDistance($lat1, $lon1, $lat2, $lon2)
    {
        // Công thức Haversine để tính khoảng cách giữa 2 điểm
        $earthRadius = 6371; // Bán kính Trái Đất (km)

        $latDelta = deg2rad($lat2 - $lat1);
        $lonDelta = deg2rad($lon2 - $lon1);

        $a = sin($latDelta / 2) * sin($latDelta / 2) +
            cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
            sin($lonDelta / 2) * sin($lonDelta / 2);

        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));
        $distance = $earthRadius * $c;

        return $distance;
    }

    public function loadWeatherAndSuggestions()
    {
        $this->loading = true;
        $this->error = null;

        try {
            // Load weather data from database
            $this->weatherData = \App\Models\WeatherData::where('city_code', $this->selectedCity)->first();

            if (!$this->weatherData) {
                // Create demo weather data if not exists
                $city = VietnamCity::where('code', $this->selectedCity)->first();
                if ($city) {
                    $this->weatherData = \App\Models\WeatherData::create([
                        'city_code' => $city->code,
                        'city_name' => $city->name,
                        'temperature' => 30.0,
                        'humidity' => 79,
                        'weather_description' => 'Có mây',
                        'weather_category' => 'cloudy',
                        'last_updated' => now(),
                    ]);
                }
            }

            // Load recipe suggestions using weather service (limit to 3 for slideshow)
            $weatherRecipeService = app(\App\Services\WeatherRecipeService::class);
            $this->suggestions = $weatherRecipeService->getWeatherBasedSuggestions($this->selectedCity, 3);

            // If no weather-based suggestions, get default recipes
            if ($this->suggestions->count() === 0) {
                $this->suggestions = \App\Models\Recipe::where('status', 'approved')
                    ->with(['user', 'categories', 'tags', 'images'])
                    ->limit(3)
                    ->get();
            }

        } catch (\Exception $e) {
            $this->error = 'Có lỗi xảy ra khi tải dữ liệu: ' . $e->getMessage();
        } finally {
            $this->loading = false;
        }
    }

    public function nextSlide()
    {
        if ($this->suggestions->count() > 0) {
            $this->currentSlide = ($this->currentSlide + 1) % $this->suggestions->count();
            $this->dispatch('slide-changed', currentSlide: $this->currentSlide, totalSlides: $this->suggestions->count());
        }
    }

    public function previousSlide()
    {
        if ($this->suggestions->count() > 0) {
            $this->currentSlide = $this->currentSlide === 0
                ? $this->suggestions->count() - 1
                : $this->currentSlide - 1;
            $this->dispatch('slide-changed', currentSlide: $this->currentSlide, totalSlides: $this->suggestions->count());
        }
    }

    public function goToSlide($index)
    {
        if ($index >= 0 && $index < $this->suggestions->count()) {
            $this->currentSlide = $index;
            $this->dispatch('slide-changed', currentSlide: $this->currentSlide, totalSlides: $this->suggestions->count());
        }
    }

    public function toggleAutoPlay()
    {
        $this->autoPlay = !$this->autoPlay;
    }

    public function updatedSelectedCity()
    {
        \Log::info('updatedSelectedCity called', ['selectedCity' => $this->selectedCity]);
        $this->currentSlide = 0; // Reset to first slide
        $this->loadWeatherAndSuggestions();
    }

    public function changeCity($cityCode)
    {
        \Log::info('changeCity called', ['cityCode' => $cityCode]);
        $this->selectedCity = $cityCode;
        $this->currentSlide = 0; // Reset to first slide
        $this->loadWeatherAndSuggestions();
    }

    public function setCity($cityCode)
    {
        \Log::info('setCity called', ['cityCode' => $cityCode]);
        $this->selectedCity = $cityCode;
        $this->currentSlide = 0;
        $this->loadWeatherAndSuggestions();
    }

    public function getWeatherIcon($condition)
    {
        $icons = [
            'sunny' => 'heroicon-o-sun',
            'cloudy' => 'heroicon-o-cloud',
            'rainy' => 'heroicon-o-cloud-rain',
            'stormy' => 'heroicon-o-bolt',
            'snowy' => 'heroicon-o-snowflake',
            'normal' => 'heroicon-o-cloud'
        ];

        return $icons[$condition] ?? 'heroicon-o-cloud';
    }

    public function getTemperatureColor($temperature)
    {
        if ($temperature < 15) {
            return 'text-blue-500';
        } elseif ($temperature > 30) {
            return 'text-red-500';
        } else {
            return 'text-green-500';
        }
    }

    public function getSuggestionReason()
    {
        if (!$this->weatherData) {
            return 'Không có dữ liệu thời tiết';
        }

        $temperature = $this->weatherData->temperature;
        $humidity = $this->weatherData->humidity;
        $reasons = [];

        // Logic mới theo yêu cầu
        if ($temperature >= 24) {
            if ($humidity > 70) {
                $reasons[] = "Nhiệt độ cao ({$temperature}°C) và độ ẩm cao ({$humidity}%) - gợi ý các món nhẹ như súp và salad để giải nhiệt";
            } else {
                $reasons[] = "Nhiệt độ cao ({$temperature}°C) và độ ẩm thấp ({$humidity}%) - gợi ý các món nước và món chế biến nhanh";
            }
        } else {
            if ($temperature < 15) {
                $reasons[] = "Thời tiết lạnh ({$temperature}°C) - phù hợp với các món ăn nóng, giàu dinh dưỡng để giữ ấm";
            } else {
                $reasons[] = "Thời tiết mát mẻ ({$temperature}°C) - gợi ý các món ăn đa dạng, cân bằng dinh dưỡng";
            }
        }

        // Thêm thông tin thời tiết
        if ($this->weatherData->weather_description) {
            $reasons[] = "Thời tiết: " . $this->weatherData->weather_description;
        }

        return implode('. ', $reasons);
    }

    /**
     * Toggle favorite status for a recipe
     */
    public function toggleFavorite($recipeId)
    {
        if (!\Illuminate\Support\Facades\Auth::check()) {
            session()->flash('message', 'Vui lòng đăng nhập để thêm vào yêu thích.');
            return redirect()->route('login');
        }

        $recipe = \App\Models\Recipe::findOrFail($recipeId);
        $favoriteService = app(\App\Services\FavoriteService::class);
        $result = $favoriteService->toggle($recipe, \Illuminate\Support\Facades\Auth::user());

        session()->flash('success', $result['message']);
        $this->dispatch('favorite-toggled', recipeId: $recipeId);
        $this->dispatch('flash-message', message: $result['message'], type: 'success');

        // Refresh component để cập nhật UI
        $this->dispatch('$refresh');
    }

    /**
     * Confirm toggle favorite with confirmation dialog
     */
    public function confirmToggleFavorite($recipeId)
    {
        $recipe = \App\Models\Recipe::findOrFail($recipeId);
        $isFavorited = $recipe->isFavoritedBy(\Illuminate\Support\Facades\Auth::user());

        if ($isFavorited) {
            $this->removeFavorite($recipe->slug);
        } else {
            $this->toggleFavorite($recipeId);
        }
    }

    /**
     * Remove favorite (fallback method)
     */
    public function removeFavorite($recipeSlug)
    {
        if (!Auth::check()) {
            session()->flash('message', 'Vui lòng đăng nhập để thực hiện thao tác này.');
            return;
        }

        $recipe = \App\Models\Recipe::where('slug', $recipeSlug)->first();
        if ($recipe) {
            $favoriteService = app(FavoriteService::class);
            $favoriteService->removeFavorite($recipe, Auth::user());
            
            session()->flash('success', 'Đã xóa khỏi danh sách yêu thích.');
            $this->dispatch('favorite-toggled', recipeId: $recipe->id);
            $this->dispatch('flash-message', message: 'Đã xóa khỏi danh sách yêu thích.', type: 'success');
            
            // Refresh component để cập nhật UI
            $this->dispatch('$refresh');
        }
    }

    public function render()
    {
        return view('livewire.weather-recipe-slideshow');
    }
}