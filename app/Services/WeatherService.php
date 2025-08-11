<?php

namespace App\Services;

use App\Models\WeatherData;
use App\Models\VietnamCity;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class WeatherService
{
    protected $apiKey;
    protected $baseUrl = 'https://api.openweathermap.org/data/2.5';

    public function __construct()
    {
        $this->apiKey = config('services.openweather.api_key');
    }

    /**
     * Get current weather for a city.
     */
    public function getCurrentWeather(VietnamCity $city)
    {
        try {
            $response = Http::get("{$this->baseUrl}/weather", [
                'lat' => $city->latitude,
                'lon' => $city->longitude,
                'appid' => $this->apiKey,
                'units' => 'metric',
                'lang' => 'vi'
            ]);

            if ($response->successful()) {
                $data = $response->json();
                return $this->processWeatherData($city, $data);
            }

            // Log chi tiết lỗi API với thông tin đầy đủ
            Log::error('OpenWeatherMap API request failed', [
                'city_id' => $city->id,
                'city_name' => $city->name,
                'city_code' => $city->code,
                'coordinates' => [
                    'lat' => $city->latitude,
                    'lon' => $city->longitude
                ],
                'status_code' => $response->status(),
                'response_body' => $response->body(),
                'response_headers' => $response->headers(),
                'request_url' => "{$this->baseUrl}/weather",
                'timestamp' => now()->toISOString()
            ]);

            return null;

        } catch (\Exception $e) {
            // Log chi tiết exception với stack trace
            Log::error('Weather API request exception', [
                'city_id' => $city->id,
                'city_name' => $city->name,
                'city_code' => $city->code,
                'coordinates' => [
                    'lat' => $city->latitude,
                    'lon' => $city->longitude
                ],
                'exception_class' => get_class($e),
                'exception_message' => $e->getMessage(),
                'exception_code' => $e->getCode(),
                'exception_file' => $e->getFile(),
                'exception_line' => $e->getLine(),
                'stack_trace' => $e->getTraceAsString(),
                'request_url' => "{$this->baseUrl}/weather",
                'timestamp' => now()->toISOString()
            ]);

            return null;
        }
    }

    /**
     * Get 5-day forecast for a city.
     */
    public function getForecast(VietnamCity $city)
    {
        try {
            $response = Http::get("{$this->baseUrl}/forecast", [
                'lat' => $city->latitude,
                'lon' => $city->longitude,
                'appid' => $this->apiKey,
                'units' => 'metric',
                'lang' => 'vi'
            ]);

            if ($response->successful()) {
                return $response->json();
            }

            Log::error('OpenWeatherMap Forecast API error', [
                'city' => $city->name,
                'response' => $response->body()
            ]);

            return null;

        } catch (\Exception $e) {
            Log::error('Weather Forecast API request failed', [
                'city' => $city->name,
                'error' => $e->getMessage()
            ]);

            return null;
        }
    }

    /**
     * Process and save weather data.
     */
    protected function processWeatherData(VietnamCity $city, array $data)
    {
        $weatherData = [
            'city_name' => $city->name,
            'city_code' => $city->code,
            'temperature' => $data['main']['temp'],
            'feels_like' => $data['main']['feels_like'],
            'humidity' => $data['main']['humidity'],
            'wind_speed' => $data['wind']['speed'] ?? 0,
            'weather_condition' => $data['weather'][0]['main'],
            'weather_description' => $data['weather'][0]['description'],
            'weather_icon' => $data['weather'][0]['icon'],
            'pressure' => $data['main']['pressure'],
            'visibility' => $data['visibility'] ?? 10000,
            'uv_index' => null, // UV index requires separate API call
            'forecast_data' => null,
            'last_updated' => now(),
            'weather_category' => $this->getWeatherCategory($data['weather'][0]['main']),
            'description' => $data['weather'][0]['description']
        ];

        // Save weather data
        WeatherData::updateOrCreate(
            ['city_code' => $city->code, 'last_updated' => now()->startOfHour()],
            $weatherData
        );

        return $weatherData;
    }

    /**
     * Update weather data for all cities.
     */
    public function updateAllCitiesWeather()
    {
        $cities = VietnamCity::active()->get();
        $updatedCount = 0;

        foreach ($cities as $city) {
            $weatherData = $this->getCurrentWeather($city);
            if ($weatherData) {
                $updatedCount++;
            }

            // Add delay to avoid rate limiting
            usleep(100000); // 0.1 second delay
        }

        Log::info("Weather data updated for {$updatedCount} cities");

        return $updatedCount;
    }

    /**
     * Get cached weather data for a city.
     */
    public function getCachedWeather(VietnamCity $city)
    {
        $cacheKey = "weather_{$city->code}";

        return Cache::remember($cacheKey, 1800, function () use ($city) { // 30 minutes cache
            return $city->latestWeatherData;
        });
    }

    /**
     * Get weather data for multiple cities.
     */
    public function getMultipleCitiesWeather(array $cityCodes)
    {
        $weatherData = [];

        foreach ($cityCodes as $cityCode) {
            $city = VietnamCity::findByCode($cityCode);
            if ($city) {
                $weatherData[$cityCode] = $this->getCachedWeather($city);
            }
        }

        return $weatherData;
    }

    /**
     * Get weather statistics.
     */
    public function getWeatherStats()
    {
        $stats = Cache::remember('weather_stats', 3600, function () {
            $totalCities = VietnamCity::active()->count();
            $citiesWithWeather = WeatherData::recent(6)->distinct('city_code')->count();
            $lastUpdate = WeatherData::latest('last_updated')->first();

            return [
                'total_cities' => $totalCities,
                'cities_with_weather' => $citiesWithWeather,
                'coverage_percentage' => $totalCities > 0 ? round(($citiesWithWeather / $totalCities) * 100, 2) : 0,
                'last_update' => $lastUpdate ? $lastUpdate->last_updated : null
            ];
        });

        return $stats;
    }

    /**
     * Get cities with outdated weather data.
     */
    public function getCitiesWithOutdatedWeather($hours = 6)
    {
        $citiesWithRecentWeather = WeatherData::recent($hours)
            ->pluck('city_code')
            ->toArray();

        return VietnamCity::active()
            ->whereNotIn('code', $citiesWithRecentWeather)
            ->get();
    }

    /**
     * Clean old weather data.
     */
    public function cleanOldWeatherData($days = 7)
    {
        $deletedCount = WeatherData::where('last_updated', '<', now()->subDays($days))
            ->delete();

        Log::info("Cleaned {$deletedCount} old weather records");

        return $deletedCount;
    }

    /**
     * Get weather category from weather condition.
     */
    protected function getWeatherCategory($condition)
    {
        $condition = strtolower($condition);

        if (str_contains($condition, 'rain') || str_contains($condition, 'drizzle')) {
            return 'rainy';
        }

        if (str_contains($condition, 'snow')) {
            return 'snowy';
        }

        if (str_contains($condition, 'cloud')) {
            return 'cloudy';
        }

        if (str_contains($condition, 'clear') || str_contains($condition, 'sun')) {
            return 'sunny';
        }

        if (str_contains($condition, 'storm') || str_contains($condition, 'thunder')) {
            return 'stormy';
        }

        return 'normal';
    }
}