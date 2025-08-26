<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Collection as SupportCollection;

use Illuminate\Support\Facades\App;
use Carbon\Carbon;
use App\Models\Recipe;
use App\Models\User;
use App\Models\Favorite;
use App\Services\WeatherService;
use App\Services\OpenAiService;

class WeeklyMealPlan extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'name',
        'week_start',
        'meals',
        'is_active',
        'total_calories',
        'total_cost',
        'shopping_list_generated',
        'weather_optimized',
        'ai_suggestions_used'
    ];

    protected $casts = [
        'week_start' => 'date',
        'meals' => 'array',
        'is_active' => 'boolean',
        'total_calories' => 'integer',
        'total_cost' => 'decimal:2',
        'shopping_list_generated' => 'boolean',
        'weather_optimized' => 'boolean',
        'ai_suggestions_used' => 'boolean'
    ];

    // Meal types constants
    const MEAL_BREAKFAST = 'breakfast';
    const MEAL_LUNCH = 'lunch';
    const MEAL_DINNER = 'dinner';
    const MEAL_SNACK = 'snack';

    public static function getMealTypes(): array
    {
        return [
            self::MEAL_BREAKFAST => 'Bữa sáng',
            self::MEAL_LUNCH => 'Bữa trưa',
            self::MEAL_DINNER => 'Bữa tối',
            self::MEAL_SNACK => 'Bữa phụ'
        ];
    }

    /**
     * Get the user that owns the meal plan.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the week end date.
     */
    public function getWeekEndAttribute(): Carbon
    {
        return $this->week_start->copy()->addDays(6);
    }

    /**
     * Get meal for specific day and meal type.
     */
    public function getMealForDay(string $day, string $mealType = self::MEAL_DINNER): ?Recipe
    {
        $meals = $this->meals ?? [];
        $recipeId = $meals[$day][$mealType] ?? null;

        return $recipeId ? Recipe::find($recipeId) : null;
    }

    /**
     * Get all meals for specific day and meal type.
     */
    public function getMealsForDay(string $day, string $mealType = self::MEAL_DINNER): array
    {
        $meals = $this->meals ?? [];
        $dayMeals = $meals[$day][$mealType] ?? [];

        // Handle both single recipe (old format) and multiple recipes (new format)
        if (is_numeric($dayMeals)) {
            return [$dayMeals];
        }

        return is_array($dayMeals) ? $dayMeals : [];
    }

    /**
     * Add meal for specific day and meal type.
     */
    public function addMealForDay(string $day, string $mealType, int $recipeId): void
    {
        $meals = $this->meals ?? [];
        $currentMeals = $this->getMealsForDay($day, $mealType);

        // Ensure we have a flat array of numeric recipe IDs
        $currentMeals = array_values(array_filter($currentMeals, 'is_numeric'));

        if (!in_array($recipeId, $currentMeals)) {
            $currentMeals[] = $recipeId;
            $meals[$day][$mealType] = array_values($currentMeals);
            $this->meals = $meals;
            $this->updateTotals();
        }
    }

    /**
     * Remove meal for specific day and meal type.
     */
    public function removeMealForDay(string $day, string $mealType, int $recipeId): void
    {
        $meals = $this->meals ?? [];
        $currentMeals = $this->getMealsForDay($day, $mealType);

        $currentMeals = array_filter($currentMeals, fn($id) => $id != $recipeId);

        if (empty($currentMeals)) {
            unset($meals[$day][$mealType]);
            if (empty($meals[$day])) {
                unset($meals[$day]);
            }
        } else {
            $meals[$day][$mealType] = array_values($currentMeals);
        }

        $this->meals = $meals;
        $this->updateTotals();
    }

    /**
     * Set meal for specific day and meal type.
     */
    public function setMealForDay(string $day, string $mealType, ?int $recipeId): void
    {
        $meals = $this->meals ?? [];

        if ($recipeId) {
            $meals[$day][$mealType] = $recipeId;
        } else {
            unset($meals[$day][$mealType]);
            if (empty($meals[$day])) {
                unset($meals[$day]);
            }
        }

        $this->meals = $meals;
        $this->updateTotals();
    }

    /**
     * Get all recipes in the meal plan.
     */
    public function getAllRecipes(): Collection
    {
        $recipes = new Collection();
        $jsonRecipes = $this->loadRecipesFromJson();

        foreach ($this->meals ?? [] as $day => $dayMeals) {
            foreach ($dayMeals as $mealType => $recipeData) {
                // Handle both single recipe (old format) and multiple recipes (new format)
                if (is_array($recipeData)) {
                    foreach ($recipeData as $recipeId) {
                        if (is_numeric($recipeId)) {
                            // Database recipe
                            $recipe = Recipe::find($recipeId);
                            if ($recipe) {
                                $recipes->push($recipe);
                            }
                        } else {
                            // JSON recipe - find by ID in JSON data
                            foreach ($jsonRecipes as $jsonRecipe) {
                                if (($jsonRecipe['id'] ?? '') == $recipeId) {
                                    $recipes->push((object) $jsonRecipe);
                                    break;
                                }
                            }
                        }
                    }
                } elseif (is_numeric($recipeData)) {
                    // Single database recipe
                    $recipe = Recipe::find($recipeData);
                    if ($recipe) {
                        $recipes->push($recipe);
                    }
                } else {
                    // Single JSON recipe
                    foreach ($jsonRecipes as $jsonRecipe) {
                        if (($jsonRecipe['id'] ?? '') == $recipeData) {
                            $recipes->push((object) $jsonRecipe);
                            break;
                        }
                    }
                }
            }
        }

        return $recipes->filter(function ($recipe) {
            return $recipe && isset($recipe->id);
        })->unique('id');
    }

    /**
     * Load recipes from JSON file.
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
     * Update total calories and cost.
     */
    public function updateTotals(): void
    {
        $recipes = $this->getAllRecipes();

        $totalCalories = 0;
        $totalCost = 0;

        foreach ($recipes as $recipe) {
            $totalCalories += $recipe->calories_per_serving ?? 0;
            // TODO: Add cost calculation based on ingredients
        }

        $this->total_calories = $totalCalories;
        $this->total_cost = $totalCost;
    }

    /**
     * Generate shopping list from meal plan.
     */
    public function generateShoppingList(): array
    {
        $recipes = $this->getAllRecipes();
        $shoppingList = [];

        foreach ($recipes as $recipe) {
            // Handle both Eloquent models and JSON objects
            $ingredients = [];
            $title = '';

            if (is_object($recipe) && method_exists($recipe, 'getAttribute')) {
                // Eloquent model
                $ingredients = $recipe->ingredients ?? [];
                $title = $recipe->title ?? '';
            } else {
                // JSON object
                $ingredients = $recipe->ingredients ?? [];
                $title = $recipe->title ?? '';
            }

            foreach ($ingredients as $ingredient) {
                $name = $ingredient['name'] ?? '';
                $amount = $ingredient['amount'] ?? '';
                $unit = $ingredient['unit'] ?? '';

                if (empty($name))
                    continue;

                if (!isset($shoppingList[$name])) {
                    $shoppingList[$name] = [
                        'amount' => 0,
                        'unit' => $unit,
                        'recipes' => []
                    ];
                }

                $shoppingList[$name]['amount'] += floatval($amount);
                if (!in_array($title, $shoppingList[$name]['recipes'])) {
                    $shoppingList[$name]['recipes'][] = $title;
                }
            }
        }

        $this->shopping_list_generated = true;
        $this->save();

        return $shoppingList;
    }

    /**
     * Get weather-optimized meal suggestions.
     */
    public function getWeatherOptimizedSuggestions(): array
    {
        // TODO: Integrate with WeatherService
        return [];
    }

    /**
     * Get AI-powered meal suggestions.
     */
    public function getAiSuggestions(): array
    {
        // TODO: Integrate with OpenAiService
        return [];
    }

    /**
     * Check if meal plan is complete for the week.
     */
    public function isComplete(): bool
    {
        $days = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'];
        $mealTypes = [self::MEAL_BREAKFAST, self::MEAL_LUNCH, self::MEAL_DINNER];

        foreach ($days as $day) {
            foreach ($mealTypes as $mealType) {
                if (empty($this->getMealsForDay($day, $mealType))) {
                    return false;
                }
            }
        }

        return true;
    }

    /**
     * Get meal plan statistics.
     */
    public function getStatistics(): array
    {
        $recipes = $this->getAllRecipes();
        $totalRecipes = $recipes->count();
        $uniqueRecipes = $recipes->unique('id')->count();

        return [
            'total_meals' => $totalRecipes,
            'unique_recipes' => $uniqueRecipes,
            'completion_percentage' => $this->isComplete() ? 100 : round(($totalRecipes / 21) * 100, 2), // 21 meals per week
            'total_calories' => $this->total_calories,
            'total_cost' => $this->total_cost,
            'weather_optimized' => $this->weather_optimized,
            'ai_suggestions_used' => $this->ai_suggestions_used
        ];
    }
}
