<?php

namespace App\Exports;

use App\Models\WeeklyMealPlan;
use App\Models\User;
use Illuminate\Support\Facades\View;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;

class MealPlansPdfExport
{
    protected $user;
    protected $mealPlan;

    public function __construct(User $user = null, WeeklyMealPlan $mealPlan = null)
    {
        $this->user = $user;
        $this->mealPlan = $mealPlan;
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
        $weeklyMeals = [];

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
                            $recipes[] = $recipe;
                        }
                    }
                }

                $dayMeals[$mealType] = [
                    'label' => $mealTypeLabel,
                    'recipes' => $recipes
                ];
            }

            $weeklyMeals[$dayKey] = [
                'label' => $dayLabel,
                'date' => $dayDate,
                'meals' => $dayMeals
            ];
        }

        $statistics = $mealPlan->getStatistics();
        $shoppingList = $mealPlan->generateShoppingList();

        $html = View::make('exports.meal-plan-pdf', compact('mealPlan', 'weeklyMeals', 'statistics', 'shoppingList'))->render();

        return PDF::loadHTML($html)->output();
    }

    private function exportAllMealPlans()
    {
        $mealPlans = WeeklyMealPlan::where('user_id', $this->user->id)
            ->orderBy('week_start', 'desc')
            ->get();

        $mealPlansData = [];
        foreach ($mealPlans as $mealPlan) {
            $statistics = $mealPlan->getStatistics();
            $mealPlansData[] = [
                'mealPlan' => $mealPlan,
                'statistics' => $statistics
            ];
        }

        $html = View::make('exports.all-meal-plans-pdf', compact('mealPlansData', 'user'))->render();

        return PDF::loadHTML($html)->output();
    }
}
