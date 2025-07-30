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

    public function render()
    {
        return view('livewire.weather-slideshow-simple');
    }
}