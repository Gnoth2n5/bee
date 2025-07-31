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

    public function __construct(WeatherService $weatherService)
    {
        $this->weatherService = $weatherService;
    }

    public function getWeatherService()
    {
        return $this->weatherService;
    }

    /**
     * Get recipe suggestions based on weather conditions.
     */
    public function getWeatherBasedSuggestions($cityCode, $limit = 12)
    {
        // Lấy weather data trực tiếp từ database
        $weatherData = \App\Models\WeatherData::where('city_code', $cityCode)->first();

        if (!$weatherData) {
            return $this->getDefaultSuggestions($limit);
        }

        return $this->generateSuggestions($weatherData, $limit);
    }

    /**
     * Generate recipe suggestions based on weather data.
     */
    protected function generateSuggestions(WeatherData $weatherData, $limit = 12)
    {
        // Sử dụng logic thông minh mới dựa trên nhiệt độ và độ ẩm
        return $this->getSmartWeatherSuggestions($weatherData, $limit);
    }

    /**
     * Logic đề xuất thông minh dựa trên nhiệt độ và độ ẩm
     */
    protected function getSmartWeatherSuggestions(WeatherData $weatherData, $limit = 12)
    {
        $temperature = $weatherData->temperature;
        $humidity = $weatherData->humidity;

        $query = Recipe::with(['user', 'categories', 'tags', 'images'])
            ->where('status', 'approved')
            ->whereNotNull('published_at');

        // Logic chính theo yêu cầu
        if ($temperature >= 24) {
            if ($humidity > 70) {
                // Trên 24°C và độ ẩm cao -> Món nhẹ như súp và salad
                $query->whereHas('categories', function ($q) {
                    $q->whereIn('name', ['Súp', 'Salad', 'Món mát', 'Tráng miệng', 'Đồ uống']);
                });
            } else {
                // Trên 24°C và độ ẩm thấp -> Thời gian ngắn và các món nước
                $query->whereHas('categories', function ($q) {
                    $q->whereIn('name', ['Canh', 'Súp', 'Đồ uống', 'Món nước', 'Cháo']);
                });
            }
        } else {
            // Dưới 24°C - Logic cho thời tiết mát/lạnh
            if ($temperature < 15) {
                // Dưới 15°C - Món ăn nóng, giàu dinh dưỡng
                $query->whereHas('categories', function ($q) {
                    $q->whereIn('name', ['Lẩu', 'Cháo', 'Súp nóng', 'Món nóng', 'Thịt']);
                });
            } else {
                // 15-24°C - Món ăn đa dạng, cân bằng
                $query->orderBy('average_rating', 'desc');
            }
        }

        return $query->limit($limit)->get();
    }

    /**
     * Get suggestions based on weather condition.
     */
    protected function getWeatherConditionSuggestions(WeatherData $weatherData, $limit = 6)
    {
        $weatherCategory = $weatherData->weather_category;

        $query = Recipe::with(['user', 'categories', 'tags', 'images'])
            ->where('status', 'approved')
            ->whereNotNull('published_at');

        switch ($weatherCategory) {
            case 'rainy':
                // Món ăn nóng, súp, canh
                $query->whereHas('categories', function ($q) {
                    $q->whereIn('name', ['Súp', 'Canh', 'Cháo', 'Lẩu']);
                });
                break;

            case 'sunny':
                // Món ăn mát, salad, đồ uống
                $query->whereHas('categories', function ($q) {
                    $q->whereIn('name', ['Salad', 'Đồ uống', 'Tráng miệng', 'Món mát']);
                });
                break;

            case 'cold':
                // Món ăn nóng, giàu dinh dưỡng
                $query->whereHas('categories', function ($q) {
                    $q->whereIn('name', ['Món nóng', 'Thịt', 'Hải sản', 'Lẩu']);
                });
                break;

            case 'hot':
                // Món ăn mát, nhẹ
                $query->whereHas('categories', function ($q) {
                    $q->whereIn('name', ['Món mát', 'Salad', 'Đồ uống', 'Tráng miệng']);
                });
                break;

            default:
                // Món ăn phổ biến
                $query->orderBy('view_count', 'desc');
                break;
        }

        return $query->limit($limit)->get();
    }

    /**
     * Get suggestions based on temperature.
     */
    protected function getTemperatureBasedSuggestions(WeatherData $weatherData, $limit = 3)
    {
        $temperature = $weatherData->temperature;

        $query = Recipe::with(['user', 'categories', 'tags', 'images'])
            ->where('status', 'approved')
            ->whereNotNull('published_at');

        if ($temperature < 15) {
            // Thời tiết lạnh - món ăn nóng, giàu dinh dưỡng
            $query->whereHas('categories', function ($q) {
                $q->whereIn('name', ['Lẩu', 'Cháo', 'Súp', 'Món nóng']);
            });
        } elseif ($temperature > 30) {
            // Thời tiết nóng - món ăn mát, nhẹ
            $query->whereHas('categories', function ($q) {
                $q->whereIn('name', ['Salad', 'Món mát', 'Đồ uống', 'Tráng miệng']);
            });
        } else {
            // Thời tiết mát mẻ - món ăn đa dạng
            $query->orderBy('average_rating', 'desc');
        }

        return $query->limit($limit)->get();
    }

    /**
     * Get suggestions based on humidity.
     */
    protected function getHumidityBasedSuggestions(WeatherData $weatherData, $limit = 3)
    {
        $humidity = $weatherData->humidity;

        $query = Recipe::with(['user', 'categories', 'tags', 'images'])
            ->where('status', 'approved')
            ->whereNotNull('published_at');

        if ($humidity > 70) {
            // Độ ẩm cao - món ăn khô, cay
            $query->whereHas('categories', function ($q) {
                $q->whereIn('name', ['Món khô', 'Món cay', 'Nướng', 'Chiên']);
            });
        } elseif ($humidity < 40) {
            // Độ ẩm thấp - món ăn có nước, mát
            $query->whereHas('categories', function ($q) {
                $q->whereIn('name', ['Canh', 'Súp', 'Đồ uống', 'Món mát']);
            });
        } else {
            // Độ ẩm bình thường
            $query->orderBy('favorite_count', 'desc');
        }

        return $query->limit($limit)->get();
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
     */
    protected function generateSuggestionReason(WeatherData $weatherData)
    {
        $temperature = $weatherData->temperature;
        $humidity = $weatherData->humidity;
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
        if ($weatherData->weather_description) {
            $reasons[] = "Thời tiết: " . $weatherData->weather_description;
        }

        return implode('. ', $reasons);
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

            return [
                'total_suggestions' => $totalSuggestions,
                'active_suggestions' => $activeSuggestions,
                'cities_with_suggestions' => $citiesWithSuggestions,
                'last_generated' => $lastGenerated ? $lastGenerated->last_generated : null
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
}