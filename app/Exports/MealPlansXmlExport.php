<?php

namespace App\Exports;

use App\Models\WeeklyMealPlan;
use App\Models\User;
use Carbon\Carbon;

class MealPlansXmlExport
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
        $xml = new \SimpleXMLElement('<?xml version="1.0" encoding="UTF-8"?><meal_plan></meal_plan>');

        // Thông tin cơ bản
        $xml->addChild('id', $mealPlan->id);
        $xml->addChild('name', $mealPlan->name);
        $xml->addChild('week_start', $mealPlan->week_start->format('Y-m-d'));
        $xml->addChild('week_end', $mealPlan->week_end->format('Y-m-d'));
        $xml->addChild('status', $mealPlan->is_active ? 'active' : 'inactive');
        $xml->addChild('created_at', $mealPlan->created_at->format('Y-m-d H:i:s'));
        $xml->addChild('updated_at', $mealPlan->updated_at->format('Y-m-d H:i:s'));

        // Thống kê
        $statistics = $mealPlan->getStatistics();
        $stats = $xml->addChild('statistics');
        $stats->addChild('total_meals', $statistics['total_meals']);
        $stats->addChild('unique_recipes', $statistics['unique_recipes']);
        $stats->addChild('completion_rate', $statistics['completion_rate']);
        $stats->addChild('total_calories', $statistics['total_calories']);
        $stats->addChild('total_cost', $statistics['total_cost']);

        // Tính năng sử dụng
        $features = $xml->addChild('features');
        $features->addChild('weather_optimized', $mealPlan->weather_optimized ? 'true' : 'false');
        $features->addChild('ai_generated', $mealPlan->ai_generated ? 'true' : 'false');
        $features->addChild('shopping_list_created', $mealPlan->shopping_list_created ? 'true' : 'false');

        // Danh sách mua sắm
        $shoppingList = $mealPlan->generateShoppingList();
        $shopping = $xml->addChild('shopping_list');
        foreach ($shoppingList as $item) {
            $itemXml = $shopping->addChild('item');
            $itemXml->addChild('name', $item['name']);
            $itemXml->addChild('quantity', $item['quantity']);
            $itemXml->addChild('unit', $item['unit']);
            $itemXml->addChild('recipes', implode(', ', $item['recipes']));
        }

        // Bữa ăn theo ngày
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
        $weeklyMeals = $xml->addChild('weekly_meals');

        foreach ($days as $dayKey => $dayLabel) {
            $dayDate = $mealPlan->week_start->copy()->addDays(array_search($dayKey, array_keys($days)));
            $dayXml = $weeklyMeals->addChild('day');
            $dayXml->addAttribute('key', $dayKey);
            $dayXml->addAttribute('label', $dayLabel);
            $dayXml->addAttribute('date', $dayDate->format('Y-m-d'));

            foreach ($mealTypes as $mealType => $mealTypeLabel) {
                $meals = $mealPlan->getMealsForDay($dayKey, $mealType);

                if (!empty($meals)) {
                    $mealTypeXml = $dayXml->addChild('meal_type');
                    $mealTypeXml->addAttribute('type', $mealType);
                    $mealTypeXml->addAttribute('label', $mealTypeLabel);

                    foreach ($meals as $recipeId) {
                        $recipe = \App\Models\Recipe::find($recipeId);
                        if ($recipe) {
                            $recipeXml = $mealTypeXml->addChild('recipe');
                            $recipeXml->addChild('id', $recipe->id);
                            $recipeXml->addChild('name', $recipe->name);
                            $recipeXml->addChild('description', $recipe->description);
                            $recipeXml->addChild('calories', $recipe->calories);
                            $recipeXml->addChild('cooking_time', $recipe->cooking_time);
                            $recipeXml->addChild('difficulty', $recipe->difficulty);
                            $recipeXml->addChild('servings', $recipe->servings);

                            // Nguyên liệu
                            $ingredients = $recipeXml->addChild('ingredients');
                            foreach ($recipe->ingredients as $ingredient) {
                                $ingredientXml = $ingredients->addChild('ingredient');
                                $ingredientXml->addChild('name', $ingredient->name);
                                $ingredientXml->addChild('quantity', $ingredient->pivot->quantity);
                                $ingredientXml->addChild('unit', $ingredient->pivot->unit);
                            }

                            // Hướng dẫn nấu
                            $instructions = $recipeXml->addChild('instructions');
                            foreach ($recipe->instructions as $instruction) {
                                $instructionXml = $instructions->addChild('instruction');
                                $instructionXml->addAttribute('step', $instruction->step);
                                $instructionXml->addChild('content', $instruction->content);
                            }
                        }
                    }
                }
            }
        }

        return $xml->asXML();
    }

    private function exportAllMealPlans()
    {
        $mealPlans = WeeklyMealPlan::where('user_id', $this->user->id)
            ->orderBy('week_start', 'desc')
            ->get();

        $xml = new \SimpleXMLElement('<?xml version="1.0" encoding="UTF-8"?><meal_plans></meal_plans>');

        // Thông tin người dùng
        $user = $xml->addChild('user');
        $user->addChild('id', $this->user->id);
        $user->addChild('name', $this->user->name);
        $user->addChild('email', $this->user->email);

        // Thống kê tổng hợp
        $totalStats = $xml->addChild('total_statistics');
        $totalMealPlans = $mealPlans->count();
        $totalMeals = 0;
        $totalCalories = 0;
        $totalCost = 0;
        $activePlans = 0;

        foreach ($mealPlans as $mealPlan) {
            $stats = $mealPlan->getStatistics();
            $totalMeals += $stats['total_meals'];
            $totalCalories += $stats['total_calories'];
            $totalCost += $stats['total_cost'];
            if ($mealPlan->is_active)
                $activePlans++;
        }

        $totalStats->addChild('total_meal_plans', $totalMealPlans);
        $totalStats->addChild('active_meal_plans', $activePlans);
        $totalStats->addChild('total_meals', $totalMeals);
        $totalStats->addChild('total_calories', $totalCalories);
        $totalStats->addChild('total_cost', $totalCost);
        $totalStats->addChild('average_meals_per_plan', $totalMealPlans > 0 ? round($totalMeals / $totalMealPlans, 2) : 0);

        // Danh sách meal plans
        $plans = $xml->addChild('plans');
        foreach ($mealPlans as $mealPlan) {
            $planXml = $plans->addChild('meal_plan');
            $planXml->addChild('id', $mealPlan->id);
            $planXml->addChild('name', $mealPlan->name);
            $planXml->addChild('week_start', $mealPlan->week_start->format('Y-m-d'));
            $planXml->addChild('week_end', $mealPlan->week_end->format('Y-m-d'));
            $planXml->addChild('status', $mealPlan->is_active ? 'active' : 'inactive');
            $planXml->addChild('created_at', $mealPlan->created_at->format('Y-m-d H:i:s'));
            $planXml->addChild('updated_at', $mealPlan->updated_at->format('Y-m-d H:i:s'));

            // Thống kê của meal plan này
            $stats = $mealPlan->getStatistics();
            $planStats = $planXml->addChild('statistics');
            $planStats->addChild('total_meals', $stats['total_meals']);
            $planStats->addChild('unique_recipes', $stats['unique_recipes']);
            $planStats->addChild('completion_rate', $stats['completion_rate']);
            $planStats->addChild('total_calories', $stats['total_calories']);
            $planStats->addChild('total_cost', $stats['total_cost']);

            // Tính năng
            $features = $planXml->addChild('features');
            $features->addChild('weather_optimized', $mealPlan->weather_optimized ? 'true' : 'false');
            $features->addChild('ai_generated', $mealPlan->ai_generated ? 'true' : 'false');
            $features->addChild('shopping_list_created', $mealPlan->shopping_list_created ? 'true' : 'false');
        }

        return $xml->asXML();
    }
}
