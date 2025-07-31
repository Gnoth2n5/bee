<?php

namespace App\Livewire;

use App\Models\VietnamCity;
use App\Services\WeatherService;
use App\Services\WeatherRecipeService;
use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On;

#[Layout('layouts.app')]
class WeatherRecipeSuggestions extends Component
{
    public $selectedCity = 'HCM'; // Default to Ho Chi Minh City
    public $weatherData = null;
    public $suggestions = [];
    public $loading = false;
    public $error = null;
    public $userLatitude = null;
    public $userLongitude = null;
    public $nearestCity = null;

    protected $weatherService;
    protected $weatherRecipeService;

    public function boot(WeatherService $weatherService, WeatherRecipeService $weatherRecipeService)
    {
        $this->weatherService = $weatherService;
        $this->weatherRecipeService = $weatherRecipeService;
    }

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

        // Load dữ liệu cho thành phố
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

    public function updatedSelectedCity()
    {
        $this->loadWeatherAndSuggestions();
    }

    public function loadWeatherAndSuggestions()
    {
        $this->loading = true;
        $this->error = null;

        try {
            $city = VietnamCity::where('code', $this->selectedCity)->first();
            if (!$city) {
                $this->error = 'Không tìm thấy thông tin thành phố: ' . $this->selectedCity;
                $this->loading = false;
                return;
            }

            // Load weather data
            $this->weatherData = $this->weatherService->getCachedWeather($city);

            if (!$this->weatherData) {
                $this->error = 'Không có dữ liệu thời tiết cho thành phố này';
                $this->loading = false;
                return;
            }

            // Load recipe suggestions
            $this->suggestions = $this->weatherRecipeService->getWeatherBasedSuggestions($this->selectedCity, 12);

        } catch (\Exception $e) {
            $this->error = 'Có lỗi xảy ra khi tải dữ liệu: ' . $e->getMessage();
        } finally {
            $this->loading = false;
        }
    }

    #[On('refresh-weather')]
    public function refreshWeather()
    {
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

    public function getWeatherColor($condition)
    {
        $colors = [
            'sunny' => 'text-yellow-500',
            'cloudy' => 'text-gray-500',
            'rainy' => 'text-blue-500',
            'stormy' => 'text-purple-500',
            'snowy' => 'text-blue-300',
            'normal' => 'text-gray-400'
        ];

        return $colors[$condition] ?? 'text-gray-400';
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

    public function setUserLocation($latitude, $longitude)
    {
        \Log::info('setUserLocation called with: ' . $latitude . ', ' . $longitude);
        $this->userLatitude = $latitude;
        $this->userLongitude = $longitude;

        // Tìm thành phố gần nhất
        $this->nearestCity = $this->findNearestCity($latitude, $longitude);

        if ($this->nearestCity) {
            \Log::info('Nearest city found: ' . $this->nearestCity->name . ' (' . $this->nearestCity->code . ')');
            $this->selectedCity = $this->nearestCity->code;
            \Log::info('Selected city updated to: ' . $this->selectedCity);

            // Lưu vào session để dùng ở trang khác
            session([
                'user_location' => [
                    'latitude' => $latitude,
                    'longitude' => $longitude,
                    'nearest_city_code' => $this->nearestCity->code,
                    'nearest_city_name' => $this->nearestCity->name
                ]
            ]);

            $this->dispatch('$refresh'); // Force refresh the component
            $this->loadWeatherAndSuggestions();
        } else {
            \Log::info('No nearest city found');
        }
    }

    public function findNearestCity($latitude, $longitude)
    {
        $cities = VietnamCity::all();
        $nearestCity = null;
        $shortestDistance = PHP_FLOAT_MAX;

        foreach ($cities as $city) {
            $distance = $this->calculateDistance($latitude, $longitude, $city->latitude, $city->longitude);
            if ($distance < $shortestDistance) {
                $shortestDistance = $distance;
                $nearestCity = $city;
            }
        }

        return $nearestCity;
    }

    public function calculateDistance($lat1, $lon1, $lat2, $lon2)
    {
        $theta = $lon1 - $lon2;
        $dist = sin(deg2rad($lat1)) * sin(deg2rad($lat2)) + cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * cos(deg2rad($theta));
        $dist = acos($dist);
        $dist = rad2deg($dist);
        $miles = $dist * 60 * 1.1515;
        return $miles;
    }

    public function getUserLocationFromBrowser()
    {
        \Log::info('getUserLocationFromBrowser called');
        $this->dispatch('get-user-location');
    }

    public function getLocationFromProfile()
    {
        if (!auth()->check()) {
            return;
        }

        $user = auth()->user();

        // Ưu tiên lấy từ cột province của user trước
        $provinceName = $user->province;

        // Nếu không có, lấy từ profile
        if (!$provinceName && $user->profile) {
            $provinceName = $user->profile->city;
        }

        if (!$provinceName) {
            \Log::info('User does not have province information');
            return;
        }

        \Log::info('Getting location from profile: ' . $provinceName);

        // Tìm thành phố trong database
        $city = \App\Models\VietnamCity::where('name', 'LIKE', '%' . $provinceName . '%')->first();

        if ($city) {
            \Log::info('Found city in database: ' . $city->name . ' (' . $city->code . ')');
            $this->selectedCity = $city->code;
            $this->nearestCity = $city;

            // Lưu vào session để dùng ở trang khác
            session([
                'user_location' => [
                    'latitude' => $city->latitude,
                    'longitude' => $city->longitude,
                    'nearest_city_code' => $city->code,
                    'nearest_city_name' => $city->name
                ]
            ]);

            $this->dispatch('$refresh');
            $this->loadWeatherAndSuggestions();
        } else {
            \Log::info('City not found in database: ' . $provinceName);
        }
    }

    public function render()
    {
        $cities = VietnamCity::active()->ordered()->get()->groupBy('region');

        return view('livewire.weather-recipe-suggestions', [
            'cities' => $cities,
            'weatherData' => $this->weatherData,
            'suggestions' => $this->suggestions,
            'loading' => $this->loading,
            'error' => $this->error
        ]);
    }
}