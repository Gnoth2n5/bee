<?php

namespace App\Livewire;

use App\Models\VietnamCity;
use App\Services\WeatherService;
use App\Services\WeatherRecipeService;
use App\Services\WeatherConditionRuleService;
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

    // Thêm các thuộc tính cho nhập thủ công
    public $manualTemperature = null;
    public $manualHumidity = null;
    public $useManualInput = false;
    public $showManualInput = false;

    protected $weatherService;
    protected $weatherRecipeService;
    protected $weatherConditionRuleService;

    public function boot(WeatherService $weatherService, WeatherRecipeService $weatherRecipeService, WeatherConditionRuleService $weatherConditionRuleService)
    {
        $this->weatherService = $weatherService;
        $this->weatherRecipeService = $weatherRecipeService;
        $this->weatherConditionRuleService = $weatherConditionRuleService;
    }

    public function mount()
    {
        // Kiểm tra xem có thông tin vị trí từ session không
        $sessionLocation = session('user_location');
        if ($sessionLocation) {
            \Log::info('💾 [WeatherRecipeSuggestions] Loading location from session', [
                'component' => 'WeatherRecipeSuggestions',
                'session_data' => $sessionLocation,
                'detection_method' => $sessionLocation['detection_method'] ?? 'unknown',
                'detected_at' => $sessionLocation['detected_at'] ?? 'unknown',
                'source_component' => $sessionLocation['component'] ?? 'unknown'
            ]);
            
            $this->userLatitude = $sessionLocation['latitude'];
            $this->userLongitude = $sessionLocation['longitude'];
            $this->selectedCity = $sessionLocation['nearest_city_code'];
            $this->nearestCity = \App\Models\VietnamCity::where('code', $sessionLocation['nearest_city_code'])->first();

            if ($this->nearestCity) {
                \Log::info('✅ [WeatherRecipeSuggestions] Session location loaded successfully', [
                    'loaded_city' => $this->nearestCity->name,
                    'loaded_code' => $this->nearestCity->code,
                    'coordinates' => [$this->userLatitude, $this->userLongitude]
                ]);
            } else {
                \Log::warning('❌ [WeatherRecipeSuggestions] City from session not found in database', [
                    'session_city_code' => $sessionLocation['nearest_city_code'],
                    'session_city_name' => $sessionLocation['nearest_city_name'] ?? 'unknown'
                ]);
            }
        } else {
            \Log::info('📍 [WeatherRecipeSuggestions] No session location found', [
                'component' => 'WeatherRecipeSuggestions',
                'will_use_default' => true
            ]);
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
        $this->useManualInput = false;
        $this->loadWeatherAndSuggestions();
    }

    /**
     * Toggle manual input mode
     */
    public function toggleManualInput()
    {
        $this->showManualInput = !$this->showManualInput;
        if ($this->showManualInput) {
            $this->useManualInput = true;
        }
    }

    /**
     * Apply manual temperature and humidity
     */
    public function applyManualConditions()
    {
        $this->validate([
            'manualTemperature' => 'nullable|numeric|min:-50|max:60',
            'manualHumidity' => 'nullable|numeric|min:0|max:100',
        ]);

        if ($this->manualTemperature !== null || $this->manualHumidity !== null) {
            $this->useManualInput = true;
            $this->loadSuggestionsByConditions();
        }
    }

    /**
     * Load suggestions based on manual conditions
     */
    public function loadSuggestionsByConditions()
    {
        $this->loading = true;
        $this->error = null;

        try {
            $temperature = $this->manualTemperature;
            $humidity = $this->manualHumidity;

            // Sử dụng service mới để lấy đề xuất (không có thành phố)
            $this->suggestions = $this->weatherConditionRuleService->getSuggestionsByConditions(
                $temperature,
                $humidity,
                12
            );
        } catch (\Exception $e) {
            $this->error = 'Có lỗi xảy ra khi tải dữ liệu: ' . $e->getMessage();
        } finally {
            $this->loading = false;
        }
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

            // Load weather data - sử dụng API trực tiếp thay vì cache
            $this->weatherData = $this->weatherService->getCurrentWeather($city);

            if (!$this->weatherData) {
                $this->error = 'Không có dữ liệu thời tiết cho thành phố này';
                $this->loading = false;
                return;
            }

            // Load recipe suggestions using new system (không có thành phố)
            if ($this->useManualInput && ($this->manualTemperature !== null || $this->manualHumidity !== null)) {
                $this->suggestions = $this->weatherConditionRuleService->getSuggestionsByConditions(
                    $this->manualTemperature,
                    $this->manualHumidity,
                    12
                );
            } else {
                // Sử dụng nhiệt độ và độ ẩm từ weather data để tìm quy tắc phù hợp
                $this->suggestions = $this->weatherConditionRuleService->getSuggestionsByConditions(
                    $this->weatherData['temperature'],
                    $this->weatherData['humidity'],
                    12
                );
            }
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
        if ($this->useManualInput && ($this->manualTemperature !== null || $this->manualHumidity !== null)) {
            return $this->weatherConditionRuleService->getSuggestionReason(
                $this->manualTemperature,
                $this->manualHumidity
            );
        }

        if (!$this->weatherData) {
            return 'Không có dữ liệu thời tiết';
        }

        return $this->weatherConditionRuleService->getSuggestionReason(
            $this->weatherData['temperature'],
            $this->weatherData['humidity']
        );
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

            // Debug log để tracking
            \Log::info('Location detection details:', [
                'input_coordinates' => [$latitude, $longitude],
                'detected_city' => $this->nearestCity->name,
                'detected_code' => $this->nearestCity->code,
                'city_coordinates' => [$this->nearestCity->latitude, $this->nearestCity->longitude]
            ]);

            $this->selectedCity = $this->nearestCity->code;
            \Log::info('Selected city updated to: ' . $this->selectedCity);

            // Lưu vào session với timestamp để track
            session([
                'user_location' => [
                    'latitude' => $latitude,
                    'longitude' => $longitude,
                    'nearest_city_code' => $this->nearestCity->code,
                    'nearest_city_name' => $this->nearestCity->name,
                    'detected_at' => now()->toDateTimeString(),
                    'detection_method' => 'browser_gps'
                ]
            ]);

            $this->dispatch('$refresh'); // Force refresh the component
            $this->loadWeatherAndSuggestions();
        } else {
            \Log::warning('No nearest city found for coordinates: ' . $latitude . ', ' . $longitude);
        }
    }

    public function findNearestCity($latitude, $longitude)
    {
        $cities = VietnamCity::all();
        $nearestCity = null;
        $shortestDistance = PHP_FLOAT_MAX;
        $candidates = [];

        foreach ($cities as $city) {
            if ($city->latitude && $city->longitude) {
                $distance = $this->calculateDistance($latitude, $longitude, $city->latitude, $city->longitude);
                $candidates[] = [
                    'city' => $city,
                    'distance' => $distance
                ];

                if ($distance < $shortestDistance) {
                    $shortestDistance = $distance;
                    $nearestCity = $city;
                }
            }
        }

        // Log top 3 candidates for debugging
        usort($candidates, function ($a, $b) {
            return $a['distance'] <=> $b['distance'];
        });

        $topCandidates = array_slice($candidates, 0, 3);
        \Log::info('Top 3 nearest cities for coordinates [' . $latitude . ', ' . $longitude . ']:');
        foreach ($topCandidates as $index => $candidate) {
            \Log::info(($index + 1) . '. ' . $candidate['city']->name . ' - ' . round($candidate['distance'], 2) . ' miles');
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

            // Lưu vào session với thông tin detection method
            session([
                'user_location' => [
                    'latitude' => $city->latitude,
                    'longitude' => $city->longitude,
                    'nearest_city_code' => $city->code,
                    'nearest_city_name' => $city->name,
                    'detected_at' => now()->toDateTimeString(),
                    'detection_method' => 'user_profile'
                ]
            ]);

            $this->dispatch('$refresh');
            $this->loadWeatherAndSuggestions();
        } else {
            \Log::info('City not found in database: ' . $provinceName);
        }
    }

    public function clearLocationCache()
    {
        \Log::info('Clearing location cache...');

        // Clear session
        session()->forget('user_location');

        // Reset component state
        $this->userLatitude = null;
        $this->userLongitude = null;
        $this->nearestCity = null;
        $this->selectedCity = 'HCM'; // Default to HCM

        // Dispatch event to clear localStorage on frontend
        $this->dispatch('clear-location-cache');

        // Reload with default city
        $this->loadWeatherAndSuggestions();

        \Log::info('Location cache cleared, reset to default city: ' . $this->selectedCity);
    }

    public function forceLocationRefresh()
    {
        \Log::info('Forcing location refresh...');

        // Clear cache first
        $this->clearLocationCache();

        // Trigger new location request
        $this->dispatch('get-user-location');

        \Log::info('Location refresh triggered');
    }

    public function debugLocationInfo()
    {
        $debugInfo = [
            'current_selected_city' => $this->selectedCity,
            'user_coordinates' => [$this->userLatitude, $this->userLongitude],
            'nearest_city' => $this->nearestCity ? [
                'name' => $this->nearestCity->name,
                'code' => $this->nearestCity->code,
                'coordinates' => [$this->nearestCity->latitude, $this->nearestCity->longitude]
            ] : null,
            'session_data' => session('user_location'),
            'user_profile' => auth()->check() ? [
                'province' => auth()->user()->province,
                'profile_city' => auth()->user()->profile?->city
            ] : null
        ];

        \Log::info('Location Debug Info:', $debugInfo);

        return response()->json($debugInfo);
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
