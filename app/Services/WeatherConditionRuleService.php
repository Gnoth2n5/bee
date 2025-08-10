<?php

namespace App\Services;

use App\Models\WeatherConditionRule;
use App\Models\Recipe;
use App\Models\Category;
use App\Models\Tag;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class WeatherConditionRuleService
{
    /**
     * Get recipe suggestions based on temperature and humidity conditions.
     */
    public function getSuggestionsByConditions($temperature, $humidity = null, $limit = 12)
    {
        $cacheKey = "weather_suggestions_{$temperature}_{$humidity}_{$limit}";

        return Cache::remember($cacheKey, 1800, function () use ($temperature, $humidity, $limit) {
            return $this->generateSuggestionsByConditions($temperature, $humidity, $limit);
        });
    }

    /**
     * Generate suggestions based on weather conditions.
     */
    protected function generateSuggestionsByConditions($temperature, $humidity = null, $limit = 12)
    {
        // Tìm các quy tắc phù hợp với điều kiện thời tiết
        $matchingRules = $this->findMatchingRules($temperature, $humidity);

        if ($matchingRules->isEmpty()) {
            return $this->getDefaultSuggestions($limit);
        }

        // Lấy recipes từ các quy tắc phù hợp
        $recipes = collect();
        foreach ($matchingRules as $rule) {
            $ruleRecipes = $rule->getMatchingRecipes($limit);
            $recipes = $recipes->merge($ruleRecipes);
        }

        // Loại bỏ trùng lặp và sắp xếp theo độ ưu tiên
        $recipes = $recipes->unique('id')
            ->sortByDesc('average_rating')
            ->take($limit);

        return $recipes;
    }

    /**
     * Find rules that match the given weather conditions.
     */
    protected function findMatchingRules($temperature, $humidity = null)
    {
        $query = WeatherConditionRule::active()
            ->forTemperature($temperature)
            ->orderByPriority();

        if ($humidity !== null) {
            $query->forHumidity($humidity);
        }

        return $query->get();
    }

    /**
     * Get default suggestions when no rules match.
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
     * Create predefined weather condition rules.
     */
    public function createPredefinedRules()
    {
        $rules = [
            // Nhiệt độ cao (>= 30°C)
            [
                'name' => 'Nhiệt độ cao (>= 30°C)',
                'description' => 'Thời tiết nóng, cần món ăn mát, nhẹ',
                'temperature_min' => 30,
                'temperature_max' => null,
                'humidity_min' => null,
                'humidity_max' => null,
                'suggested_categories' => $this->getCategoryIds(['Salad', 'Đồ uống', 'Tráng miệng', 'Món mát']),
                'suggested_tags' => $this->getTagIds(['mát', 'nhẹ', 'giải nhiệt', 'tươi']),
                'suggestion_reason' => 'Nhiệt độ cao trên 30°C - phù hợp với các món ăn mát, nhẹ để giải nhiệt',
                'priority' => 5
            ],

            // Nhiệt độ cao độ ẩm cao (24-30°C, >70%)
            [
                'name' => 'Nhiệt độ cao độ ẩm cao',
                'description' => 'Nhiệt độ 24-30°C và độ ẩm cao >70%',
                'temperature_min' => 24,
                'temperature_max' => 30,
                'humidity_min' => 70,
                'humidity_max' => null,
                'suggested_categories' => $this->getCategoryIds(['Súp', 'Salad', 'Món mát', 'Đồ uống']),
                'suggested_tags' => $this->getTagIds(['mát', 'nhẹ', 'giải nhiệt', 'tươi', 'súp']),
                'suggestion_reason' => 'Nhiệt độ cao (24-30°C) và độ ẩm cao (>70%) - gợi ý các món nhẹ như súp và salad để giải nhiệt',
                'priority' => 4
            ],

            // Nhiệt độ cao độ ẩm thấp (24-30°C, <70%)
            [
                'name' => 'Nhiệt độ cao độ ẩm thấp',
                'description' => 'Nhiệt độ 24-30°C và độ ẩm thấp <70%',
                'temperature_min' => 24,
                'temperature_max' => 30,
                'humidity_min' => null,
                'humidity_max' => 70,
                'suggested_categories' => $this->getCategoryIds(['Canh', 'Súp', 'Đồ uống', 'Món nước', 'Cháo']),
                'suggested_tags' => $this->getTagIds(['nước', 'canh', 'súp', 'cháo', 'dễ tiêu']),
                'suggestion_reason' => 'Nhiệt độ cao (24-30°C) và độ ẩm thấp (<70%) - gợi ý các món nước và món chế biến nhanh',
                'priority' => 4
            ],

            // Nhiệt độ mát (15-24°C)
            [
                'name' => 'Nhiệt độ mát mẻ',
                'description' => 'Nhiệt độ mát mẻ 15-24°C',
                'temperature_min' => 15,
                'temperature_max' => 24,
                'humidity_min' => null,
                'humidity_max' => null,
                'suggested_categories' => $this->getCategoryIds(['Món chính', 'Món nóng', 'Thịt', 'Hải sản']),
                'suggested_tags' => $this->getTagIds(['cân bằng', 'đa dạng', 'dinh dưỡng']),
                'suggestion_reason' => 'Thời tiết mát mẻ (15-24°C) - gợi ý các món ăn đa dạng, cân bằng dinh dưỡng',
                'priority' => 3
            ],

            // Nhiệt độ lạnh (< 15°C)
            [
                'name' => 'Nhiệt độ lạnh',
                'description' => 'Nhiệt độ lạnh dưới 15°C',
                'temperature_min' => null,
                'temperature_max' => 15,
                'humidity_min' => null,
                'humidity_max' => null,
                'suggested_categories' => $this->getCategoryIds(['Lẩu', 'Cháo', 'Súp nóng', 'Món nóng', 'Thịt']),
                'suggested_tags' => $this->getTagIds(['nóng', 'ấm', 'dinh dưỡng', 'lẩu', 'cháo']),
                'suggestion_reason' => 'Thời tiết lạnh (dưới 15°C) - phù hợp với các món ăn nóng, giàu dinh dưỡng để giữ ấm',
                'priority' => 5
            ],

            // Độ ẩm cao (>80%)
            [
                'name' => 'Độ ẩm cao',
                'description' => 'Độ ẩm cao trên 80%',
                'temperature_min' => null,
                'temperature_max' => null,
                'humidity_min' => 80,
                'humidity_max' => null,
                'suggested_categories' => $this->getCategoryIds(['Món khô', 'Món cay', 'Nướng', 'Chiên']),
                'suggested_tags' => $this->getTagIds(['khô', 'cay', 'nướng', 'chiên']),
                'suggestion_reason' => 'Độ ẩm cao (>80%) - gợi ý các món ăn khô, cay để cân bằng',
                'priority' => 3
            ],

            // Độ ẩm thấp (<40%)
            [
                'name' => 'Độ ẩm thấp',
                'description' => 'Độ ẩm thấp dưới 40%',
                'temperature_min' => null,
                'temperature_max' => null,
                'humidity_min' => null,
                'humidity_max' => 40,
                'suggested_categories' => $this->getCategoryIds(['Canh', 'Súp', 'Đồ uống', 'Món mát']),
                'suggested_tags' => $this->getTagIds(['nước', 'canh', 'súp', 'mát']),
                'suggestion_reason' => 'Độ ẩm thấp (<40%) - gợi ý các món ăn có nước, mát để bổ sung độ ẩm',
                'priority' => 3
            ],
            // Nóng khô rất cao (>=32°C, độ ẩm <=60%)
            [
                'name' => 'Nóng khô rất cao',
                'description' => 'Nhiệt độ >= 32°C và độ ẩm <= 60%',
                'temperature_min' => 32,
                'temperature_max' => null,
                'humidity_min' => null,
                'humidity_max' => 60,
                'suggested_categories' => $this->getCategoryIds(['Canh', 'Súp', 'Món nước', 'Cháo']),
                'suggested_tags' => $this->getTagIds(['nước', 'canh', 'súp', 'cháo', 'dễ tiêu']),
                'suggestion_reason' => 'Trời nóng khô (>=32°C, ẩm <=60%) - ưu tiên món có nước để bù ẩm',
                'priority' => 6
            ]
        ];

        foreach ($rules as $ruleData) {
            WeatherConditionRule::updateOrCreate(
                ['name' => $ruleData['name']],
                $ruleData
            );
        }

        Log::info('Created/Updated ' . count($rules) . ' predefined weather condition rules');
    }

    /**
     * Get category IDs by names.
     */
    protected function getCategoryIds($categoryNames)
    {
        return Category::whereIn('name', $categoryNames)
            ->pluck('id')
            ->toArray();
    }

    /**
     * Get tag IDs by names.
     */
    protected function getTagIds($tagNames)
    {
        return Tag::whereIn('name', $tagNames)
            ->pluck('id')
            ->toArray();
    }

    /**
     * Get suggestion reason for current conditions.
     */
    public function getSuggestionReason($temperature, $humidity = null)
    {
        $matchingRules = $this->findMatchingRules($temperature, $humidity);

        if ($matchingRules->isEmpty()) {
            return 'Không có quy tắc phù hợp cho điều kiện thời tiết hiện tại';
        }

        $reasons = $matchingRules->pluck('suggestion_reason')->toArray();
        return implode('. ', $reasons);
    }

    /**
     * Get weather condition statistics.
     */
    public function getStats()
    {
        return Cache::remember('weather_rules_stats', 3600, function () {
            $totalRules = WeatherConditionRule::count();
            $activeRules = WeatherConditionRule::active()->count();
            $temperatureRules = WeatherConditionRule::whereNotNull('temperature_min')
                ->orWhereNotNull('temperature_max')
                ->count();
            $humidityRules = WeatherConditionRule::whereNotNull('humidity_min')
                ->orWhereNotNull('humidity_max')
                ->count();

            return [
                'total_rules' => $totalRules,
                'active_rules' => $activeRules,
                'temperature_rules' => $temperatureRules,
                'humidity_rules' => $humidityRules
            ];
        });
    }

    /**
     * Clear all caches.
     */
    public function clearCaches()
    {
        Cache::forget('weather_rules_stats');
        // Clear weather suggestion caches
        for ($temp = 0; $temp <= 50; $temp += 5) {
            for ($humidity = 0; $humidity <= 100; $humidity += 10) {
                Cache::forget("weather_suggestions_{$temp}_{$humidity}_12");
            }
        }
    }
}