<?php

namespace App\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\Log;
use App\Models\Recipe;
use App\Models\WeatherData;
use App\Models\VietnamCity;

class WeatherSlideshowSimple extends Component
{
    public $selectedCity = 'HCM';
    public $currentSlide = 0;
    public $recipes = [];
    public $weatherData = null;
    public $userLatitude = null;
    public $userLongitude = null;
    public $nearestCity = null;

    public function mount()
    {
        Log::info('WeatherSlideshowSimple mounted');

        // Kiểm tra xem có thông tin vị trí từ session không
        if (session('user_location')) {
            $userLocation = session('user_location');
            $this->userLatitude = $userLocation['latitude'];
            $this->userLongitude = $userLocation['longitude'];
            $this->selectedCity = $userLocation['nearest_city_code'];
            $this->nearestCity = \App\Models\VietnamCity::where('code', $userLocation['nearest_city_code'])->first();

            Log::info('Loaded user location from session: ' . $userLocation['nearest_city_name'] . ' (' . $userLocation['nearest_city_code'] . ')');
        } else {
            // Tự động lấy vị trí khi component được load
            $this->dispatch('auto-get-location');
        }

        $this->loadData();
    }

    public function loadData()
    {
        Log::info('loadData called for city: ' . $this->selectedCity);
        $this->recipes = Recipe::take(3)->get();
        $this->weatherData = WeatherData::where('city_code', $this->selectedCity)->first();

        if (!$this->weatherData) {
            Log::info('No weather data found for city: ' . $this->selectedCity);
        } else {
            Log::info('Weather data found: ' . $this->weatherData->temperature . '°C, ' . $this->weatherData->humidity . '%');
        }
    }

    /**
     * Random chọn thành phố khi người dùng không cho phép vị trí
     */
    public function randomCity()
    {
        Log::info('randomCity called - user denied location permission');

        // Lấy danh sách tất cả thành phố có dữ liệu thời tiết
        $citiesWithWeather = WeatherData::select('city_code')
            ->distinct()
            ->whereNotNull('temperature')
            ->pluck('city_code')
            ->toArray();

        if (empty($citiesWithWeather)) {
            // Nếu không có thành phố nào có dữ liệu thời tiết, lấy tất cả thành phố
            $randomCity = VietnamCity::active()->inRandomOrder()->first();
        } else {
            // Random chọn từ các thành phố có dữ liệu thời tiết
            $randomCityCode = $citiesWithWeather[array_rand($citiesWithWeather)];
            $randomCity = VietnamCity::where('code', $randomCityCode)->first();
        }

        if ($randomCity) {
            Log::info('Random city selected: ' . $randomCity->name . ' (' . $randomCity->code . ')');
            $this->selectedCity = $randomCity->code;
            $this->currentSlide = 0;

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

            $this->loadData();
            $this->dispatch('alert', message: 'Đã chọn ngẫu nhiên thành phố: ' . $randomCity->name);
        } else {
            Log::info('No random city found');
            $this->dispatch('alert', message: 'Không thể chọn thành phố ngẫu nhiên');
        }
    }

    public function changeCity($cityCode)
    {
        Log::info('changeCity called with: ' . $cityCode);
        $this->selectedCity = $cityCode;
        $this->currentSlide = 0;
        $this->loadData();
    }

    public function nextSlide()
    {
        Log::info('nextSlide called');
        if ($this->currentSlide < count($this->recipes) - 1) {
            $this->currentSlide++;
        }
    }

    public function previousSlide()
    {
        Log::info('previousSlide called');
        if ($this->currentSlide > 0) {
            $this->currentSlide--;
        }
    }

    public function goToSlide($index)
    {
        Log::info('goToSlide called with: ' . $index);
        if ($index >= 0 && $index < count($this->recipes)) {
            $this->currentSlide = $index;
        }
    }

    public function setUserLocation($latitude, $longitude)
    {
        Log::info('setUserLocation called with: ' . $latitude . ', ' . $longitude);
        $this->userLatitude = $latitude;
        $this->userLongitude = $longitude;

        // Tìm thành phố gần nhất
        $this->nearestCity = $this->findNearestCity($latitude, $longitude);

        if ($this->nearestCity) {
            Log::info('Nearest city found: ' . $this->nearestCity->name . ' (' . $this->nearestCity->code . ')');
            $this->selectedCity = $this->nearestCity->code;
            $this->currentSlide = 0; // Reset slide

            // Lưu vào session để dùng ở trang khác
            session([
                'user_location' => [
                    'latitude' => $latitude,
                    'longitude' => $longitude,
                    'nearest_city_code' => $this->nearestCity->code,
                    'nearest_city_name' => $this->nearestCity->name
                ]
            ]);

            $this->loadData();
        } else {
            Log::info('No nearest city found');
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
        Log::info('getUserLocationFromBrowser called');
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
            $this->currentSlide = 0;

            // Lưu vào session để dùng ở trang khác
            session([
                'user_location' => [
                    'latitude' => $city->latitude,
                    'longitude' => $city->longitude,
                    'nearest_city_code' => $city->code,
                    'nearest_city_name' => $city->name
                ]
            ]);

            $this->loadData();
        } else {
            \Log::info('City not found in database: ' . $provinceName);
        }
    }

    public function testMethod()
    {
        Log::info('testMethod called in WeatherSlideshowSimple');
        $this->selectedCity = 'NINHBINH';
        $this->loadData();
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
        return view('livewire.weather-slideshow-simple');
    }
}