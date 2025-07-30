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
        }

        // Load dữ liệu cho thành phố
        $this->loadWeatherAndSuggestions();
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
            return 'Đang tải thông tin thời tiết...';
        }

        $reasons = [];

        // Temperature-based reasons
        if ($this->weatherData->temperature < 15) {
            $reasons[] = "Thời tiết lạnh ({$this->weatherData->temperature}°C) - phù hợp với các món ăn nóng, giàu dinh dưỡng";
        } elseif ($this->weatherData->temperature > 30) {
            $reasons[] = "Thời tiết nóng ({$this->weatherData->temperature}°C) - phù hợp với các món ăn mát, nhẹ";
        }

        // Weather condition-based reasons
        switch ($this->weatherData->weather_category) {
            case 'rainy':
                $reasons[] = "Trời mưa - gợi ý các món súp, canh ấm áp";
                break;
            case 'sunny':
                $reasons[] = "Trời nắng - gợi ý các món salad, đồ uống mát";
                break;
            case 'cloudy':
                $reasons[] = "Trời âm u - gợi ý các món ăn đa dạng";
                break;
        }

        // Humidity-based reasons
        if ($this->weatherData->humidity > 70) {
            $reasons[] = "Độ ẩm cao ({$this->weatherData->humidity}%) - phù hợp với các món khô, cay";
        } elseif ($this->weatherData->humidity < 40) {
            $reasons[] = "Độ ẩm thấp ({$this->weatherData->humidity}%) - phù hợp với các món có nước, mát";
        }

        return implode('. ', $reasons);
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