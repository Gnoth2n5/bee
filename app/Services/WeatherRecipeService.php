<?php

namespace App\Services;

use App\Models\Recipe;
use App\Models\Category;
use App\Models\WeatherData;
use App\Models\WeatherRecipeSuggestion;
use App\Models\VietnamCity;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class WeatherRecipeService
{
    protected $weatherService;
    protected $weatherConditionRuleService;

    public function __construct(WeatherService $weatherService, WeatherConditionRuleService $weatherConditionRuleService)
    {
        $this->weatherService = $weatherService;
        $this->weatherConditionRuleService = $weatherConditionRuleService;
    }

    public function getWeatherService()
    {
        return $this->weatherService;
    }

    /**
     * Get recipe suggestions based on weather conditions.
     * Sử dụng hệ thống quy tắc mới thay vì logic cũ
     */
    public function getWeatherBasedSuggestions($cityCode, $limit = 12)
    {
        // Lấy weather data trực tiếp từ database
        $weatherData = \App\Models\WeatherData::where('city_code', $cityCode)->first();

        if (!$weatherData) {
            return $this->getDefaultSuggestions($limit);
        }

        // Sử dụng service mới để lấy đề xuất dựa trên điều kiện thời tiết (không có thành phố)
        return $this->weatherConditionRuleService->getSuggestionsByConditions(
            $weatherData->temperature,
            $weatherData->humidity,
            $limit
        );
    }

    /**
     * Generate recipe suggestions based on weather data.
     * Phương thức này giữ lại để tương thích ngược
     */
    protected function generateSuggestions(WeatherData $weatherData, $limit = 12)
    {
        // Sử dụng hệ thống quy tắc mới
        return $this->weatherConditionRuleService->getSuggestionsByConditions(
            $weatherData->temperature,
            $weatherData->humidity,
            $limit
        );
    }

    /**
     * Logic đề xuất thông minh dựa trên nhiệt độ và độ ẩm
     * Phương thức này giữ lại để tương thích ngược
     */
    protected function getSmartWeatherSuggestions(WeatherData $weatherData, $limit = 12)
    {
        return $this->weatherConditionRuleService->getSuggestionsByConditions(
            $weatherData->temperature,
            $weatherData->humidity,
            $limit
        );
    }

    /**
     * Get suggestions based on weather condition.
     * Phương thức này giữ lại để tương thích ngược
     */
    protected function getWeatherConditionSuggestions(WeatherData $weatherData, $limit = 6)
    {
        return $this->weatherConditionRuleService->getSuggestionsByConditions(
            $weatherData->temperature,
            $weatherData->humidity,
            $limit
        );
    }

    /**
     * Get suggestions based on temperature.
     * Phương thức này giữ lại để tương thích ngược
     */
    protected function getTemperatureBasedSuggestions(WeatherData $weatherData, $limit = 3)
    {
        return $this->weatherConditionRuleService->getSuggestionsByConditions(
            $weatherData->temperature,
            null,
            $limit
        );
    }

    /**
     * Get suggestions based on humidity.
     * Phương thức này giữ lại để tương thích ngược
     */
    protected function getHumidityBasedSuggestions(WeatherData $weatherData, $limit = 3)
    {
        return $this->weatherConditionRuleService->getSuggestionsByConditions(
            null,
            $weatherData->humidity,
            $limit
        );
    }

    /**
     * Get default suggestions when no weather data available.
     */
    protected function getDefaultSuggestions($limit = 12)
    {
        return Recipe::with(['user', 'categories', 'tags', 'images'])
            ->where('status', 'approved')
            ->whereNotNull('published_at')
            ->orderBy('view_count', 'desc')
            ->limit($limit)
            ->get();
    }

    /**
     * Generate and save weather-based suggestions for all cities.
     */
    public function generateAllCitiesSuggestions()
    {
        $cities = VietnamCity::active()->get();
        $generatedCount = 0;

        foreach ($cities as $city) {
            $weatherData = $this->weatherService->getCachedWeather($city);
            if ($weatherData) {
                $this->saveWeatherSuggestions($city, $weatherData);
                $generatedCount++;
            }
        }

        Log::info("Generated weather suggestions for {$generatedCount} cities");
        return $generatedCount;
    }

    /**
     * Save weather-based suggestions for a city.
     */
    public function saveWeatherSuggestions(VietnamCity $city, WeatherData $weatherData)
    {
        $suggestions = $this->generateSuggestions($weatherData, 20);
        $recipeIds = $suggestions->pluck('id')->toArray();
        $categories = $suggestions->flatMap->categories->pluck('id')->unique()->toArray();

        $suggestionReason = $this->generateSuggestionReason($weatherData);

        WeatherRecipeSuggestion::updateOrCreate(
            [
                'city_code' => $city->code,
                'weather_condition' => $weatherData->weather_category,
                'temperature_min' => $weatherData->temperature - 5,
                'temperature_max' => $weatherData->temperature + 5,
            ],
            [
                'humidity_min' => $weatherData->humidity - 10,
                'humidity_max' => $weatherData->humidity + 10,
                'recipe_ids' => $recipeIds,
                'categories' => $categories,
                'suggestion_reason' => $suggestionReason,
                'is_active' => true,
                'priority' => 1,
                'last_generated' => now()
            ]
        );
    }

    /**
     * Generate suggestion reason based on weather conditions.
     * Sử dụng service mới để lấy lý do đề xuất
     */
    protected function generateSuggestionReason(WeatherData $weatherData)
    {
        return $this->weatherConditionRuleService->getSuggestionReason(
            $weatherData->temperature,
            $weatherData->humidity
        );
    }

    /**
     * Get suggestion statistics.
     */
    public function getSuggestionStats()
    {
        $stats = Cache::remember('suggestion_stats', 3600, function () {
            $totalSuggestions = WeatherRecipeSuggestion::count();
            $activeSuggestions = WeatherRecipeSuggestion::active()->count();
            $citiesWithSuggestions = WeatherRecipeSuggestion::distinct('city_code')->count();
            $lastGenerated = WeatherRecipeSuggestion::latest('last_generated')->first();

            // Thêm thống kê từ hệ thống quy tắc mới
            $ruleStats = $this->weatherConditionRuleService->getStats();

            return [
                'total_suggestions' => $totalSuggestions,
                'active_suggestions' => $activeSuggestions,
                'cities_with_suggestions' => $citiesWithSuggestions,
                'last_generated' => $lastGenerated ? $lastGenerated->last_generated : null,
                'weather_rules' => $ruleStats
            ];
        });

        return $stats;
    }

    /**
     * Clean old suggestions.
     */
    public function cleanOldSuggestions($days = 7)
    {
        $deletedCount = WeatherRecipeSuggestion::where('last_generated', '<', now()->subDays($days))
            ->delete();

        Log::info("Cleaned {$deletedCount} old weather suggestions");

        return $deletedCount;
    }

    /**
     * Get suggestions by temperature and humidity directly (không cần city)
     */
    public function getSuggestionsByTemperatureAndHumidity($temperature, $humidity = null, $limit = 12)
    {
        return $this->weatherConditionRuleService->getSuggestionsByConditions(
            $temperature,
            $humidity,
            $limit
        );
    }

    /**
     * Get suggestion reason by temperature and humidity directly
     */
    public function getSuggestionReasonByConditions($temperature, $humidity = null)
    {
        return $this->weatherConditionRuleService->getSuggestionReason(
            $temperature,
            $humidity
        );
    }
}