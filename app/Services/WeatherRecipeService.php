<?php

namespace App\Services;

use App\Models\Recipe;
use App\Models\Category;
use App\Models\WeatherData;
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
     * Sử dụng hệ thống quy tắc mới
     */
    public function getWeatherBasedSuggestions($cityCode, $limit = 12)
    {
        // Lấy weather data trực tiếp từ database
        $weatherData = \App\Models\WeatherData::where('city_code', $cityCode)->first();

        if (!$weatherData) {
            return $this->getDefaultSuggestions($limit);
        }

        // Sử dụng service mới để lấy đề xuất dựa trên điều kiện thời tiết
        return $this->weatherConditionRuleService->getSuggestionsByConditions(
            $weatherData->temperature,
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
     * Get suggestions by temperature and humidity directly
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