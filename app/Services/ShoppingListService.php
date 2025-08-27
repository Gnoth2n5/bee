<?php

namespace App\Services;

use App\Models\ShoppingList;
use App\Models\ShoppingListItem;
use App\Models\Recipe;
use App\Models\WeeklyMealPlan;
use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class ShoppingListService
{
    /**
     * Create a new shopping list for user.
     */
    public function createShoppingList(User $user, array $data): ShoppingList
    {
        return ShoppingList::create([
            'user_id' => $user->id,
            'name' => $data['name'] ?? 'Shopping List ' . now()->format('Y-m-d'),
            'description' => $data['description'] ?? null,
            'is_active' => $data['is_active'] ?? true,
            'is_shared' => $data['is_shared'] ?? false,
        ]);
    }

    /**
     * Generate shopping list from meal plan.
     */
    public function generateFromMealPlan(WeeklyMealPlan $mealPlan, ?ShoppingList $shoppingList = null): ShoppingList
    {
        if (!$shoppingList) {
            $shoppingList = $this->createShoppingList($mealPlan->user, [
                'name' => 'Shopping List - ' . $mealPlan->name,
                'description' => 'Generated from meal plan: ' . $mealPlan->name,
            ]);
        }

        $ingredients = $this->extractIngredientsFromMealPlan($mealPlan);
        $aggregatedIngredients = $this->aggregateIngredients($ingredients);

        foreach ($aggregatedIngredients as $ingredient) {
            $this->addItemToShoppingList($shoppingList, $ingredient);
        }

        return $shoppingList->fresh();
    }

    /**
     * Generate shopping list from recipe.
     */
    public function generateFromRecipe(Recipe $recipe, User $user, ?ShoppingList $shoppingList = null): ShoppingList
    {
        if (!$shoppingList) {
            $shoppingList = $this->createShoppingList($user, [
                'name' => 'Shopping List - ' . $recipe->title,
                'description' => 'Generated from recipe: ' . $recipe->title,
            ]);
        }

        $ingredients = $this->extractIngredientsFromRecipe($recipe);

        foreach ($ingredients as $ingredient) {
            $this->addItemToShoppingList($shoppingList, $ingredient, $recipe);
        }

        return $shoppingList->fresh();
    }

    /**
     * Add item to shopping list.
     */
    public function addItemToShoppingList(ShoppingList $shoppingList, array $ingredient, ?Recipe $recipe = null, ?WeeklyMealPlan $mealPlan = null): ShoppingListItem
    {
        // Ensure amount is numeric
        $amount = $ingredient['amount'] ?? null;
        $unit = $ingredient['unit'] ?? null;

        if (is_string($amount)) {
            $parsed = $this->parseAmountAndUnit($amount);
            $amount = $parsed['amount'];
            // Only use parsed unit if original unit is empty
            if (empty($unit)) {
                $unit = $parsed['unit'];
            }
        }

        return ShoppingListItem::create([
            'shopping_list_id' => $shoppingList->id,
            'ingredient_name' => $ingredient['name'],
            'amount' => $amount,
            'unit' => $unit,
            'notes' => $ingredient['notes'] ?? null,
            'category' => $ingredient['category'] ?? $this->categorizeIngredient($ingredient['name']),
            'recipe_id' => $recipe?->id,
            'weekly_meal_plan_id' => $mealPlan?->id,
            'sort_order' => $this->getNextSortOrder($shoppingList),
        ]);
    }

    /**
     * Extract ingredients from meal plan.
     */
    private function extractIngredientsFromMealPlan(WeeklyMealPlan $mealPlan): Collection
    {
        $ingredients = collect();
        $meals = $mealPlan->meals ?? [];

        foreach ($meals as $day => $dayMeals) {
            foreach ($dayMeals as $mealType => $recipeData) {
                $recipeIds = is_array($recipeData) ? $recipeData : [$recipeData];

                foreach ($recipeIds as $recipeId) {
                    $recipe = Recipe::find($recipeId);
                    if ($recipe) {
                        $recipeIngredients = $this->extractIngredientsFromRecipe($recipe);
                        $ingredients = $ingredients->merge($recipeIngredients);
                    }
                }
            }
        }

        return $ingredients;
    }

    /**
     * Extract ingredients from recipe.
     */
    private function extractIngredientsFromRecipe(Recipe $recipe): Collection
    {
        $ingredients = collect();
        $recipeIngredients = $recipe->ingredients ?? [];

        foreach ($recipeIngredients as $ingredient) {
            if (is_array($ingredient)) {
                $amount = $ingredient['amount'] ?? $ingredient['quantity'] ?? null;
                $unit = $ingredient['unit'] ?? null;

                // Parse amount and unit if amount is a string
                if (is_string($amount)) {
                    $parsed = $this->parseAmountAndUnit($amount);
                    $amount = $parsed['amount'];
                    // Only use parsed unit if original unit is empty
                    if (empty($unit)) {
                        $unit = $parsed['unit'];
                    }
                }

                // Skip if no name
                $name = $ingredient['name'] ?? $ingredient['ingredient'] ?? '';
                if (empty($name)) {
                    continue;
                }

                $ingredients->push([
                    'name' => $name,
                    'amount' => $amount,
                    'unit' => $unit,
                    'notes' => $ingredient['notes'] ?? null,
                ]);
            }
        }

        return $ingredients;
    }

    /**
     * Aggregate ingredients by name and unit.
     */
    private function aggregateIngredients(Collection $ingredients): Collection
    {
        $aggregated = collect();

        foreach ($ingredients as $ingredient) {
            $key = strtolower(trim($ingredient['name'])) . '|' . ($ingredient['unit'] ?? '');

            if ($aggregated->has($key)) {
                $existing = $aggregated->get($key);
                $existingAmount = is_numeric($existing['amount']) ? (float) $existing['amount'] : 0;
                $ingredientAmount = is_numeric($ingredient['amount']) ? (float) $ingredient['amount'] : 0;
                $existing['amount'] = $existingAmount + $ingredientAmount;
                $aggregated->put($key, $existing);
            } else {
                $aggregated->put($key, $ingredient);
            }
        }

        return $aggregated->values();
    }

    /**
     * Categorize ingredient based on name.
     */
    private function categorizeIngredient(string $ingredientName): string
    {
        $name = strtolower($ingredientName);

        // Vegetables
        if (preg_match('/(rau|cải|bắp cải|su hào|carrot|cà rốt|tomato|cà chua|onion|hành|garlic|tỏi|potato|khoai|lettuce|xà lách|cucumber|dưa leo|pepper|ớt|mushroom|nấm|spinach|rau chân vịt)/', $name)) {
            return 'Rau củ';
        }

        // Meat
        if (preg_match('/(thịt|meat|pork|heo|beef|bò|chicken|gà|fish|cá|shrimp|tôm|beef|bò|pork|heo)/', $name)) {
            return 'Thịt cá';
        }

        // Dairy
        if (preg_match('/(milk|sữa|cheese|phô mai|butter|bơ|yogurt|sữa chua|cream|kem)/', $name)) {
            return 'Sữa và bơ sữa';
        }

        // Grains
        if (preg_match('/(rice|gạo|noodle|mì|bread|bánh mì|flour|bột|pasta|mì ống)/', $name)) {
            return 'Ngũ cốc';
        }

        // Spices
        if (preg_match('/(salt|muối|sugar|đường|pepper|tiêu|spice|gia vị|herb|rau thơm)/', $name)) {
            return 'Gia vị';
        }

        // Fruits
        if (preg_match('/(apple|táo|banana|chuối|orange|cam|grape|nho|fruit|trái cây)/', $name)) {
            return 'Trái cây';
        }

        // Eggs
        if (preg_match('/(egg|trứng)/', $name)) {
            return 'Trứng';
        }

        return 'Khác';
    }

    /**
     * Parse amount and unit from string like "500g", "2 cups", "1.5 kg"
     */
    private function parseAmountAndUnit(string $amountString): array
    {
        $amountString = trim($amountString);

        // Common patterns: "500g", "2 cups", "1.5 kg", "1/2 cup", "3-4 pieces"
        if (preg_match('/^(\d+(?:\.\d+)?(?:\/\d+)?(?:-\d+)?)\s*([a-zA-Z]+)$/', $amountString, $matches)) {
            $amount = $matches[1];
            $unit = trim($matches[2]);

            // Convert fractions to decimal
            if (strpos($amount, '/') !== false) {
                $parts = explode('/', $amount);
                if (count($parts) === 2 && is_numeric($parts[0]) && is_numeric($parts[1])) {
                    $amount = $parts[0] / $parts[1];
                }
            }

            // Handle ranges like "3-4"
            if (strpos($amount, '-') !== false) {
                $parts = explode('-', $amount);
                if (count($parts) === 2 && is_numeric($parts[0]) && is_numeric($parts[1])) {
                    $amount = ($parts[0] + $parts[1]) / 2; // Use average
                }
            }

            return [
                'amount' => is_numeric($amount) ? (float) $amount : null,
                'unit' => $unit
            ];
        }

        // If no unit found, try to extract just the number
        if (preg_match('/^(\d+(?:\.\d+)?(?:\/\d+)?)$/', $amountString, $matches)) {
            $amount = $matches[1];

            // Convert fractions to decimal
            if (strpos($amount, '/') !== false) {
                $parts = explode('/', $amount);
                if (count($parts) === 2 && is_numeric($parts[0]) && is_numeric($parts[1])) {
                    $amount = $parts[0] / $parts[1];
                }
            }

            return [
                'amount' => is_numeric($amount) ? (float) $amount : null,
                'unit' => null
            ];
        }

        // If no pattern matches, return null values
        return [
            'amount' => null,
            'unit' => null
        ];
    }

    /**
     * Get next sort order for shopping list item.
     */
    private function getNextSortOrder(ShoppingList $shoppingList): int
    {
        $maxOrder = $shoppingList->items()->max('sort_order') ?? 0;
        return $maxOrder + 1;
    }

    /**
     * Toggle item checked status.
     */
    public function toggleItemChecked(ShoppingListItem $item): void
    {
        $item->toggleChecked();
    }

    /**
     * Delete shopping list item.
     */
    public function deleteItem(ShoppingListItem $item): bool
    {
        return $item->delete();
    }

    /**
     * Clear checked items from shopping list.
     */
    public function clearCheckedItems(ShoppingList $shoppingList): int
    {
        $checkedItems = $shoppingList->checkedItems()->get();
        $deletedCount = 0;

        foreach ($checkedItems as $item) {
            $item->delete();
            $deletedCount++;
        }

        return $deletedCount;
    }

    /**
     * Mark shopping list as completed.
     */
    public function markShoppingListCompleted(ShoppingList $shoppingList): void
    {
        $shoppingList->markAsCompleted();
    }

    /**
     * Get shopping lists for user.
     */
    public function getUserShoppingLists(User $user, bool $activeOnly = true): Collection
    {
        $query = ShoppingList::where('user_id', $user->id);

        if ($activeOnly) {
            $query->where('is_active', true)->whereNull('completed_at');
        }

        return $query->with([
            'items' => function ($query) {
                $query->orderBy('sort_order');
            }
        ])->get();
    }

    /**
     * Get shopping list with items grouped by category.
     */
    public function getShoppingListWithCategories(ShoppingList $shoppingList): array
    {
        $items = $shoppingList->uncheckedItems()->get();

        return $items->groupBy('category')->toArray();
    }
}
