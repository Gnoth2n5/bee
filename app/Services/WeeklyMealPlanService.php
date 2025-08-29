<?php

namespace App\Services;

use App\Models\WeeklyMealPlan;
use App\Models\Recipe;
use App\Models\User;
use App\Models\Favorite;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Support\Facades\Log;

use Illuminate\Support\Facades\App;
use App\Services\WeatherService;
use App\Services\OpenAiService;

class WeeklyMealPlanService
{
    protected $weatherService;
    protected $openAiService;

    public function __construct(WeatherService $weatherService = null, OpenAiService $openAiService = null)
    {
        $this->weatherService = $weatherService ?? app(WeatherService::class);
        $this->openAiService = $openAiService ?? app(OpenAiService::class);
    }

    /**
     * Create a new weekly meal plan.
     */
    public function createMealPlan(User $user, string $name, Carbon $weekStart): WeeklyMealPlan
    {
        return WeeklyMealPlan::create([
            'user_id' => $user->id,
            'name' => $name,
            'week_start' => $weekStart,
            'meals' => [],
            'is_active' => true
        ]);
    }

    /**
     * Get current active meal plan for user.
     */
    public function getCurrentMealPlan(User $user): ?WeeklyMealPlan
    {
        return WeeklyMealPlan::where('user_id', $user->id)
            ->where('is_active', true)
            ->orderBy('week_start', 'desc')
            ->first();
    }

    /**
     * Get meal plan for specific week.
     */
    public function getMealPlanForWeek(User $user, Carbon $weekStart): ?WeeklyMealPlan
    {
        return WeeklyMealPlan::where('user_id', $user->id)
            ->where('week_start', $weekStart->format('Y-m-d'))
            ->first();
    }

    /**
     * Add meal to specific day and meal type.
     */
    public function addMeal(WeeklyMealPlan $mealPlan, string $day, string $mealType, $recipeId): bool
    {
        // Check if recipeId is from JSON data (array) or database (int)
        if (is_array($recipeId)) {
            // JSON data - create a temporary recipe object
            $recipe = (object) $recipeId;
            $recipe->id = $recipeId['id'] ?? null;
            $recipe->title = $recipeId['title'] ?? '';
            $recipe->description = $recipeId['description'] ?? '';

            if (!$recipe->id) {
                return false;
            }
        } else {
            // Database recipe
            $recipe = Recipe::find($recipeId);
            if (!$recipe) {
                return false;
            }
        }

        $mealPlan->addMealForDay($day, $mealType, $recipe->id);
        $mealPlan->save();

        return true;
    }

    /**
     * Remove meal from specific day and meal type.
     */
    public function removeMeal(WeeklyMealPlan $mealPlan, string $day, string $mealType, int $recipeId): bool
    {
        $mealPlan->removeMealForDay($day, $mealType, $recipeId);
        $mealPlan->save();

        return true;
    }

    /**
     * Generate AI-powered meal suggestions based on user preferences and weather.
     */
    public function generateAiSuggestions(User $user, Carbon $weekStart): array
    {
        $suggestions = [];
        $days = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'];
        $mealTypes = [WeeklyMealPlan::MEAL_BREAKFAST, WeeklyMealPlan::MEAL_LUNCH, WeeklyMealPlan::MEAL_DINNER];

        // Load recipes from JSON file
        $jsonRecipes = $this->loadRecipesFromJson();

        // Get user's favorite recipes from database
        $favoriteRecipes = $user->favorites()->with('recipe')->get()->pluck('recipe');

        // Combine JSON recipes with database recipes
        $allRecipes = collect($jsonRecipes)->merge($favoriteRecipes);

        // Get weather data for the week
        $weatherData = $this->getWeatherDataForWeek($user, $weekStart);

        foreach ($days as $day) {
            $suggestions[$day] = [];

            foreach ($mealTypes as $mealType) {
                $weather = $weatherData[$day] ?? null;
                $suggestions[$day][$mealType] = $this->getWeatherOptimizedSuggestions(
                    $allRecipes,
                    $mealType,
                    $weather
                );
            }
        }

        return $suggestions;
    }

    /**
     * Get weather-optimized recipe suggestions.
     */
    protected function getWeatherOptimizedSuggestions(Collection $recipes, string $mealType, $weather = null): array
    {
        $suggestions = [];

        if ($weather) {
            $temperature = $weather['temperature'] ?? 25;
            $condition = $weather['condition'] ?? 'clear';

            // Filter recipes based on weather conditions
            $filteredRecipes = $recipes->filter(function ($recipe) use ($temperature, $condition, $mealType) {
                // Hot weather: suggest light, cold dishes
                if ($temperature > 30) {
                    return $this->isLightDish($recipe) && $this->isSuitableForMealType($recipe, $mealType);
                }

                // Cold weather: suggest warm, hearty dishes
                if ($temperature < 15) {
                    return $this->isHeartyDish($recipe) && $this->isSuitableForMealType($recipe, $mealType);
                }

                // Rainy weather: suggest comfort food
                if (str_contains(strtolower($condition), 'rain')) {
                    return $this->isComfortFood($recipe) && $this->isSuitableForMealType($recipe, $mealType);
                }

                return $this->isSuitableForMealType($recipe, $mealType);
            });

            $suggestions = $filteredRecipes->take(5)->toArray();
        } else {
            // Fallback to random suggestions based on meal type
            $filteredRecipes = $recipes->filter(function ($recipe) use ($mealType) {
                return $this->isSuitableForMealType($recipe, $mealType);
            });

            $suggestions = $filteredRecipes->take(5)->toArray();
        }

        return $suggestions;
    }

    /**
     * Load recipes from JSON file for suggestions.
     */
    protected function loadRecipesFromJson(): array
    {
        $jsonPath = storage_path('app/recipes_data.json');

        if (!file_exists($jsonPath)) {
            return [];
        }

        try {
            $jsonData = file_get_contents($jsonPath);
            $recipes = json_decode($jsonData, true);

            if (!is_array($recipes)) {
                return [];
            }

            return $recipes;
        } catch (\Exception $e) {
            \Log::error('Error loading recipes from JSON: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Get weather data for the week.
     */
    protected function getWeatherDataForWeek(User $user, Carbon $weekStart): array
    {
        $weatherData = [];

        // Get user's city
        $city = $user->city;
        if (!$city) {
            return $weatherData;
        }

        // Get weather forecast for the week
        try {
            $forecast = $this->weatherService->getForecast($city);
            if ($forecast && isset($forecast['list'])) {
                foreach ($forecast['list'] as $weather) {
                    $date = Carbon::createFromTimestamp($weather['dt']);
                    $dayName = strtolower($date->format('l'));

                    if ($date->between($weekStart, $weekStart->copy()->addDays(6))) {
                        $weatherData[$dayName] = [
                            'temperature' => $weather['main']['temp'] ?? 25,
                            'condition' => $weather['weather'][0]['main'] ?? 'clear',
                            'description' => $weather['weather'][0]['description'] ?? ''
                        ];
                    }
                }
            }
        } catch (Exception $e) {
            // Log error but continue without weather data
            Log::error('Failed to get weather data for meal plan: ' . $e->getMessage());
        }

        return $weatherData;
    }

    /**
     * Check if recipe is suitable for light dishes (hot weather).
     */
    protected function isLightDish($recipe): bool
    {
        $lightKeywords = ['salad', 'soup', 'cold', 'fresh', 'light', 'vegetable', 'gỏi', 'nộm', 'rau'];

        $title = '';
        $description = '';

        if (is_array($recipe)) {
            $title = strtolower($recipe['title'] ?? '');
            $description = strtolower($recipe['description'] ?? '');
        } else {
            $title = strtolower($recipe->title ?? '');
            $description = strtolower($recipe->description ?? '');
        }

        foreach ($lightKeywords as $keyword) {
            if (str_contains($title, $keyword) || str_contains($description, $keyword)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Check if recipe is suitable for hearty dishes (cold weather).
     */
    protected function isHeartyDish($recipe): bool
    {
        $heartyKeywords = ['stew', 'soup', 'hot', 'warm', 'meat', 'beef', 'pork', 'chicken', 'lẩu', 'nướng', 'canh'];

        $title = '';
        $description = '';

        if (is_array($recipe)) {
            $title = strtolower($recipe['title'] ?? '');
            $description = strtolower($recipe['description'] ?? '');
        } else {
            $title = strtolower($recipe->title ?? '');
            $description = strtolower($recipe->description ?? '');
        }

        foreach ($heartyKeywords as $keyword) {
            if (str_contains($title, $keyword) || str_contains($description, $keyword)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Check if recipe is comfort food (rainy weather).
     */
    protected function isComfortFood($recipe): bool
    {
        $comfortKeywords = ['comfort', 'cozy', 'warm', 'soup', 'stew', 'casserole', 'cháo', 'súp', 'lẩu'];

        $title = '';
        $description = '';

        if (is_array($recipe)) {
            $title = strtolower($recipe['title'] ?? '');
            $description = strtolower($recipe['description'] ?? '');
        } else {
            $title = strtolower($recipe->title ?? '');
            $description = strtolower($recipe->description ?? '');
        }

        foreach ($comfortKeywords as $keyword) {
            if (str_contains($title, $keyword) || str_contains($description, $keyword)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Check if recipe is suitable for specific meal type.
     */
    protected function isSuitableForMealType($recipe, string $mealType): bool
    {
        // Handle both JSON data and Eloquent models
        $title = '';
        $description = '';
        $categories = [];

        if (is_array($recipe)) {
            // JSON data
            $title = strtolower($recipe['title'] ?? '');
            $description = strtolower($recipe['description'] ?? '');
            $categories = $recipe['categories'] ?? [];
        } else {
            // Eloquent model
            $title = strtolower($recipe->title ?? '');
            $description = strtolower($recipe->description ?? '');
            $categories = $recipe->categories ?? [];
        }

        // Vietnamese meal type keywords
        switch ($mealType) {
            case WeeklyMealPlan::MEAL_BREAKFAST:
                $breakfastKeywords = [
                    'sáng',
                    'breakfast',
                    'morning',
                    'egg',
                    'toast',
                    'cereal',
                    'bánh',
                    'cháo',
                    'phở',
                    'bún',
                    'xôi',
                    'bánh mì'
                ];
                break;
            case WeeklyMealPlan::MEAL_LUNCH:
                $lunchKeywords = [
                    'trưa',
                    'lunch',
                    'midday',
                    'quick',
                    'light',
                    'cơm',
                    'bún',
                    'phở',
                    'mì',
                    'noodle'
                ];
                break;
            case WeeklyMealPlan::MEAL_DINNER:
                $dinnerKeywords = [
                    'tối',
                    'dinner',
                    'evening',
                    'main',
                    'course',
                    'cơm',
                    'canh',
                    'súp',
                    'lẩu',
                    'nướng'
                ];
                break;
            default:
                return true;
        }

        $keywords = $breakfastKeywords ?? $lunchKeywords ?? $dinnerKeywords ?? [];

        // Check title and description
        foreach ($keywords as $keyword) {
            if (str_contains($title, $keyword) || str_contains($description, $keyword)) {
                return true;
            }
        }

        // Check categories
        foreach ($categories as $category) {
            $category = strtolower($category);
            foreach ($keywords as $keyword) {
                if (str_contains($category, $keyword)) {
                    return true;
                }
            }
        }

        return true; // Default to true if no specific keywords found
    }

    /**
     * Generate shopping list from meal plan.
     */
    public function generateShoppingList(WeeklyMealPlan $mealPlan): array
    {
        return $mealPlan->generateShoppingList();
    }

    /**
     * Generate weekly meals summary from meal plan.
     */
    public function generateWeeklyMeals(WeeklyMealPlan $mealPlan): array
    {
        $weeklyMeals = [];
        $days = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'];
        $mealTypes = [WeeklyMealPlan::MEAL_BREAKFAST, WeeklyMealPlan::MEAL_LUNCH, WeeklyMealPlan::MEAL_DINNER];

        $dayLabels = [
            'monday' => 'Thứ 2',
            'tuesday' => 'Thứ 3',
            'wednesday' => 'Thứ 4',
            'thursday' => 'Thứ 5',
            'friday' => 'Thứ 6',
            'saturday' => 'Thứ 7',
            'sunday' => 'Chủ nhật'
        ];

        $mealTypeLabels = [
            WeeklyMealPlan::MEAL_BREAKFAST => 'Bữa sáng',
            WeeklyMealPlan::MEAL_LUNCH => 'Bữa trưa',
            WeeklyMealPlan::MEAL_DINNER => 'Bữa tối'
        ];

        foreach ($days as $day) {
            $weeklyMeals[$day] = [
                'day_label' => $dayLabels[$day] ?? $day,
                'meals' => []
            ];

            foreach ($mealTypes as $mealType) {
                $meals = $mealPlan->getMealsForDay($day, $mealType);
                $mealInfo = [];

                foreach ($meals as $recipeId) {
                    $recipe = Recipe::find($recipeId);
                    if ($recipe) {
                        $mealInfo[] = [
                            'id' => $recipe->id,
                            'slug' => $recipe->slug,
                            'title' => $recipe->title,
                            'description' => $recipe->description,
                            'calories' => $recipe->calories_per_serving,
                            'cooking_time' => $recipe->cooking_time,
                            'difficulty' => $recipe->difficulty
                        ];
                    }
                }

                if (!empty($mealInfo)) {
                    $weeklyMeals[$day]['meals'][$mealType] = [
                        'type_label' => $mealTypeLabels[$mealType] ?? $mealType,
                        'recipes' => $mealInfo
                    ];
                }
            }
        }

        return $weeklyMeals;
    }

    /**
     * Get meal plan statistics.
     */
    public function getStatistics(WeeklyMealPlan $mealPlan): array
    {
        return $mealPlan->getStatistics();
    }

    /**
     * Duplicate meal plan for next week.
     */
    public function duplicateForNextWeek(WeeklyMealPlan $mealPlan): WeeklyMealPlan
    {
        $nextWeekStart = $mealPlan->week_start->copy()->addWeek();

        $newMealPlan = $this->createMealPlan(
            $mealPlan->user,
            $mealPlan->name . ' (Tuần tiếp theo)',
            $nextWeekStart
        );

        // Copy meals structure
        $newMealPlan->meals = $mealPlan->meals;
        $newMealPlan->save();

        return $newMealPlan;
    }

    /**
     * Get meal plan suggestions based on user preferences.
     */
    public function getPersonalizedSuggestions(User $user, string $mealType): Collection
    {
        // Get user's favorite recipes
        $favoriteRecipes = $user->favorites()->with('recipe')->get()->pluck('recipe');

        // Get recently viewed recipes
        $recentRecipes = Recipe::where('user_id', $user->id)
            ->orderBy('view_count', 'desc')
            ->take(10)
            ->get();

        // Get popular recipes
        $popularRecipes = Recipe::where('status', 'approved')
            ->orderBy('view_count', 'desc')
            ->take(10)
            ->get();

        // Combine and filter by meal type
        $allRecipes = $favoriteRecipes->merge($recentRecipes)->merge($popularRecipes);

        return $allRecipes->unique('id')->take(10);
    }
}
