<?php

namespace App\Exports;

use App\Models\WeeklyMealPlan;
use App\Models\User;
use Carbon\Carbon;

class MealPlansJsonExport
{
    protected $user;
    protected $mealPlan;
    protected $format;

    public function __construct(User $user = null, WeeklyMealPlan $mealPlan = null, string $format = 'detailed')
    {
        $this->user = $user;
        $this->mealPlan = $mealPlan;
        $this->format = $format; // 'detailed', 'summary', 'calendar', 'nutrition'
    }

    public function export()
    {
        if ($this->mealPlan) {
            return $this->exportSingleMealPlan();
        }

        return $this->exportAllMealPlans();
    }

    private function exportSingleMealPlan()
    {
        $mealPlan = $this->mealPlan;

        switch ($this->format) {
            case 'calendar':
                return $this->exportCalendarFormat($mealPlan);
            case 'nutrition':
                return $this->exportNutritionFormat($mealPlan);
            case 'summary':
                return $this->exportSummaryFormat($mealPlan);
            default:
                return $this->exportDetailedFormat($mealPlan);
        }
    }

    private function exportDetailedFormat(WeeklyMealPlan $mealPlan)
    {
        $data = [
            'metadata' => [
                'export_type' => 'detailed_meal_plan',
                'exported_at' => now()->toISOString(),
                'version' => '1.0'
            ],
            'meal_plan' => [
                'id' => $mealPlan->id,
                'name' => $mealPlan->name,
                'week_start' => $mealPlan->week_start->toISOString(),
                'week_end' => $mealPlan->week_end->toISOString(),
                'is_active' => $mealPlan->is_active,
                'weather_optimized' => $mealPlan->weather_optimized,
                'ai_generated' => $mealPlan->ai_generated,
                'shopping_list_created' => $mealPlan->shopping_list_created,
                'created_at' => $mealPlan->created_at->toISOString(),
                'updated_at' => $mealPlan->updated_at->toISOString()
            ],
            'statistics' => $mealPlan->getStatistics(),
            'shopping_list' => $mealPlan->generateShoppingList(),
            'weekly_schedule' => $this->getWeeklySchedule($mealPlan)
        ];

        return json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    }

    private function exportSummaryFormat(WeeklyMealPlan $mealPlan)
    {
        $statistics = $mealPlan->getStatistics();
        $shoppingList = $mealPlan->generateShoppingList();

        $data = [
            'metadata' => [
                'export_type' => 'summary_meal_plan',
                'exported_at' => now()->toISOString(),
                'version' => '1.0'
            ],
            'summary' => [
                'name' => $mealPlan->name,
                'week_period' => $mealPlan->week_start->format('d/m/Y') . ' - ' . $mealPlan->week_end->format('d/m/Y'),
                'total_meals' => $statistics['total_meals'],
                'unique_recipes' => $statistics['unique_recipes'],
                'completion_rate' => $statistics['completion_rate'],
                'total_calories' => $statistics['total_calories'],
                'total_cost' => $statistics['total_cost'],
                'shopping_items_count' => count($shoppingList)
            ],
            'features_used' => [
                'weather_optimized' => $mealPlan->weather_optimized,
                'ai_generated' => $mealPlan->ai_generated,
                'shopping_list_created' => $mealPlan->shopping_list_created
            ]
        ];

        return json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    }

    private function exportCalendarFormat(WeeklyMealPlan $mealPlan)
    {
        $days = [
            'monday' => 'Thứ 2',
            'tuesday' => 'Thứ 3',
            'wednesday' => 'Thứ 4',
            'thursday' => 'Thứ 5',
            'friday' => 'Thứ 6',
            'saturday' => 'Thứ 7',
            'sunday' => 'Chủ nhật'
        ];

        $mealTypes = WeeklyMealPlan::getMealTypes();
        $calendar = [];

        foreach ($days as $dayKey => $dayLabel) {
            $dayDate = $mealPlan->week_start->copy()->addDays(array_search($dayKey, array_keys($days)));
            $dayMeals = [];

            foreach ($mealTypes as $mealType => $mealTypeLabel) {
                $meals = $mealPlan->getMealsForDay($dayKey, $mealType);
                $recipes = [];

                if (!empty($meals)) {
                    foreach ($meals as $recipeId) {
                        $recipe = \App\Models\Recipe::find($recipeId);
                        if ($recipe) {
                            $recipes[] = [
                                'id' => $recipe->id,
                                'name' => $recipe->name,
                                'calories' => $recipe->calories,
                                'cooking_time' => $recipe->cooking_time,
                                'difficulty' => $recipe->difficulty
                            ];
                        }
                    }
                }

                $dayMeals[$mealType] = [
                    'label' => $mealTypeLabel,
                    'recipes' => $recipes
                ];
            }

            $calendar[] = [
                'date' => $dayDate->toISOString(),
                'day_of_week' => $dayKey,
                'day_label' => $dayLabel,
                'meals' => $dayMeals
            ];
        }

        $data = [
            'metadata' => [
                'export_type' => 'calendar_meal_plan',
                'exported_at' => now()->toISOString(),
                'version' => '1.0'
            ],
            'meal_plan' => [
                'id' => $mealPlan->id,
                'name' => $mealPlan->name,
                'week_start' => $mealPlan->week_start->toISOString(),
                'week_end' => $mealPlan->week_end->toISOString()
            ],
            'calendar' => $calendar
        ];

        return json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    }

    private function exportNutritionFormat(WeeklyMealPlan $mealPlan)
    {
        $days = [
            'monday' => 'Thứ 2',
            'tuesday' => 'Thứ 3',
            'wednesday' => 'Thứ 4',
            'thursday' => 'Thứ 5',
            'friday' => 'Thứ 6',
            'saturday' => 'Thứ 7',
            'sunday' => 'Chủ nhật'
        ];

        $mealTypes = WeeklyMealPlan::getMealTypes();
        $nutritionData = [];

        foreach ($days as $dayKey => $dayLabel) {
            $dayDate = $mealPlan->week_start->copy()->addDays(array_search($dayKey, array_keys($days)));
            $dayNutrition = [
                'date' => $dayDate->toISOString(),
                'day_label' => $dayLabel,
                'total_calories' => 0,
                'meals' => []
            ];

            foreach ($mealTypes as $mealType => $mealTypeLabel) {
                $meals = $mealPlan->getMealsForDay($dayKey, $mealType);
                $mealNutrition = [
                    'type' => $mealType,
                    'label' => $mealTypeLabel,
                    'calories' => 0,
                    'recipes' => []
                ];

                if (!empty($meals)) {
                    foreach ($meals as $recipeId) {
                        $recipe = \App\Models\Recipe::find($recipeId);
                        if ($recipe) {
                            $mealNutrition['calories'] += $recipe->calories;
                            $dayNutrition['total_calories'] += $recipe->calories;

                            $mealNutrition['recipes'][] = [
                                'id' => $recipe->id,
                                'name' => $recipe->name,
                                'calories' => $recipe->calories,
                                'servings' => $recipe->servings
                            ];
                        }
                    }
                }

                $dayNutrition['meals'][] = $mealNutrition;
            }

            $nutritionData[] = $dayNutrition;
        }

        $data = [
            'metadata' => [
                'export_type' => 'nutrition_meal_plan',
                'exported_at' => now()->toISOString(),
                'version' => '1.0'
            ],
            'meal_plan' => [
                'id' => $mealPlan->id,
                'name' => $mealPlan->name,
                'week_start' => $mealPlan->week_start->toISOString(),
                'week_end' => $mealPlan->week_end->toISOString()
            ],
            'nutrition_summary' => [
                'total_weekly_calories' => array_sum(array_column($nutritionData, 'total_calories')),
                'average_daily_calories' => array_sum(array_column($nutritionData, 'total_calories')) / 7,
                'days_with_meals' => count(array_filter($nutritionData, fn($day) => $day['total_calories'] > 0))
            ],
            'daily_nutrition' => $nutritionData
        ];

        return json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    }

    private function exportAllMealPlans()
    {
        $mealPlans = WeeklyMealPlan::where('user_id', $this->user->id)
            ->orderBy('week_start', 'desc')
            ->get();

        $data = [
            'metadata' => [
                'export_type' => 'all_meal_plans',
                'exported_at' => now()->toISOString(),
                'version' => '1.0'
            ],
            'user' => [
                'id' => $this->user->id,
                'name' => $this->user->name,
                'email' => $this->user->email
            ],
            'summary' => $this->getAllMealPlansSummary($mealPlans),
            'meal_plans' => $mealPlans->map(function ($mealPlan) {
                return [
                    'id' => $mealPlan->id,
                    'name' => $mealPlan->name,
                    'week_start' => $mealPlan->week_start->toISOString(),
                    'week_end' => $mealPlan->week_end->toISOString(),
                    'is_active' => $mealPlan->is_active,
                    'statistics' => $mealPlan->getStatistics(),
                    'features' => [
                        'weather_optimized' => $mealPlan->weather_optimized,
                        'ai_generated' => $mealPlan->ai_generated,
                        'shopping_list_created' => $mealPlan->shopping_list_created
                    ],
                    'created_at' => $mealPlan->created_at->toISOString(),
                    'updated_at' => $mealPlan->updated_at->toISOString()
                ];
            })->toArray()
        ];

        return json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    }

    private function getWeeklySchedule(WeeklyMealPlan $mealPlan)
    {
        $days = [
            'monday' => 'Thứ 2',
            'tuesday' => 'Thứ 3',
            'wednesday' => 'Thứ 4',
            'thursday' => 'Thứ 5',
            'friday' => 'Thứ 6',
            'saturday' => 'Thứ 7',
            'sunday' => 'Chủ nhật'
        ];

        $mealTypes = WeeklyMealPlan::getMealTypes();
        $schedule = [];

        foreach ($days as $dayKey => $dayLabel) {
            $dayDate = $mealPlan->week_start->copy()->addDays(array_search($dayKey, array_keys($days)));
            $daySchedule = [
                'date' => $dayDate->toISOString(),
                'day_label' => $dayLabel,
                'meals' => []
            ];

            foreach ($mealTypes as $mealType => $mealTypeLabel) {
                $meals = $mealPlan->getMealsForDay($dayKey, $mealType);
                $recipes = [];

                if (!empty($meals)) {
                    foreach ($meals as $recipeId) {
                        $recipe = \App\Models\Recipe::find($recipeId);
                        if ($recipe) {
                            $recipes[] = [
                                'id' => $recipe->id,
                                'name' => $recipe->name,
                                'description' => $recipe->description,
                                'calories' => $recipe->calories,
                                'cooking_time' => $recipe->cooking_time,
                                'difficulty' => $recipe->difficulty,
                                'servings' => $recipe->servings,
                                'ingredients' => $recipe->ingredients->map(function ($ingredient) {
                                    return [
                                        'name' => $ingredient->name,
                                        'quantity' => $ingredient->pivot->quantity,
                                        'unit' => $ingredient->pivot->unit
                                    ];
                                })->toArray(),
                                'instructions' => $recipe->instructions->map(function ($instruction) {
                                    return [
                                        'step' => $instruction->step,
                                        'content' => $instruction->content
                                    ];
                                })->toArray()
                            ];
                        }
                    }
                }

                $daySchedule['meals'][$mealType] = [
                    'label' => $mealTypeLabel,
                    'recipes' => $recipes
                ];
            }

            $schedule[] = $daySchedule;
        }

        return $schedule;
    }

    private function getAllMealPlansSummary($mealPlans)
    {
        $totalMeals = 0;
        $totalCalories = 0;
        $totalCost = 0;
        $activePlans = 0;
        $weatherOptimized = 0;
        $aiGenerated = 0;
        $shoppingListCreated = 0;

        foreach ($mealPlans as $mealPlan) {
            $stats = $mealPlan->getStatistics();
            $totalMeals += $stats['total_meals'];
            $totalCalories += $stats['total_calories'];
            $totalCost += $stats['total_cost'];
            if ($mealPlan->is_active)
                $activePlans++;
            if ($mealPlan->weather_optimized)
                $weatherOptimized++;
            if ($mealPlan->ai_generated)
                $aiGenerated++;
            if ($mealPlan->shopping_list_created)
                $shoppingListCreated++;
        }

        return [
            'total_meal_plans' => $mealPlans->count(),
            'active_meal_plans' => $activePlans,
            'total_meals' => $totalMeals,
            'total_calories' => $totalCalories,
            'total_cost' => $totalCost,
            'average_meals_per_plan' => $mealPlans->count() > 0 ? round($totalMeals / $mealPlans->count(), 2) : 0,
            'features_usage' => [
                'weather_optimized' => $weatherOptimized,
                'ai_generated' => $aiGenerated,
                'shopping_list_created' => $shoppingListCreated
            ]
        ];
    }
}
