<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ShoppingList;
use App\Models\ShoppingListItem;
use App\Models\Recipe;
use App\Models\WeeklyMealPlan;
use App\Services\ShoppingListService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class ShoppingListController extends Controller
{
    protected $shoppingListService;

    public function __construct(ShoppingListService $shoppingListService)
    {
        $this->shoppingListService = $shoppingListService;
    }

    /**
     * Get all shopping lists for authenticated user.
     */
    public function index(Request $request): JsonResponse
    {
        $user = Auth::user();
        $activeOnly = $request->boolean('active_only', true);

        $shoppingLists = $this->shoppingListService->getUserShoppingLists($user, $activeOnly);

        return response()->json([
            'success' => true,
            'data' => $shoppingLists
        ]);
    }

    /**
     * Get a specific shopping list with items.
     */
    public function show(ShoppingList $shoppingList): JsonResponse
    {
        // Check if user owns this shopping list
        if ($shoppingList->user_id !== Auth::id()) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized'
            ], 403);
        }

        $shoppingList->load([
            'items' => function ($query) {
                $query->orderBy('sort_order');
            }
        ]);

        return response()->json([
            'success' => true,
            'data' => $shoppingList
        ]);
    }

    /**
     * Create a new shopping list.
     */
    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
            'is_shared' => 'boolean'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $user = Auth::user();
        $shoppingList = $this->shoppingListService->createShoppingList($user, $request->all());

        return response()->json([
            'success' => true,
            'message' => 'Shopping list created successfully',
            'data' => $shoppingList
        ], 201);
    }

    /**
     * Update a shopping list.
     */
    public function update(Request $request, ShoppingList $shoppingList): JsonResponse
    {
        // Check if user owns this shopping list
        if ($shoppingList->user_id !== Auth::id()) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized'
            ], 403);
        }

        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|required|string|max:255',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
            'is_shared' => 'boolean'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $shoppingList->update($request->only(['name', 'description', 'is_active', 'is_shared']));

        return response()->json([
            'success' => true,
            'message' => 'Shopping list updated successfully',
            'data' => $shoppingList->fresh()
        ]);
    }

    /**
     * Delete a shopping list.
     */
    public function destroy(ShoppingList $shoppingList): JsonResponse
    {
        // Check if user owns this shopping list
        if ($shoppingList->user_id !== Auth::id()) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized'
            ], 403);
        }

        $shoppingList->delete();

        return response()->json([
            'success' => true,
            'message' => 'Shopping list deleted successfully'
        ]);
    }

    /**
     * Add item to shopping list.
     */
    public function addItem(Request $request, ShoppingList $shoppingList): JsonResponse
    {
        // Check if user owns this shopping list
        if ($shoppingList->user_id !== Auth::id()) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized'
            ], 403);
        }

        $validator = Validator::make($request->all(), [
            'ingredient_name' => 'required|string|max:255',
            'amount' => 'nullable|numeric|min:0',
            'unit' => 'nullable|string|max:50',
            'notes' => 'nullable|string',
            'category' => 'nullable|string|max:100'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $item = $this->shoppingListService->addItemToShoppingList($shoppingList, $request->all());

        return response()->json([
            'success' => true,
            'message' => 'Item added successfully',
            'data' => $item
        ], 201);
    }

    /**
     * Update shopping list item.
     */
    public function updateItem(Request $request, ShoppingList $shoppingList, ShoppingListItem $item): JsonResponse
    {
        // Check if user owns this shopping list and item belongs to it
        if ($shoppingList->user_id !== Auth::id() || $item->shopping_list_id !== $shoppingList->id) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized'
            ], 403);
        }

        $validator = Validator::make($request->all(), [
            'ingredient_name' => 'sometimes|required|string|max:255',
            'amount' => 'nullable|numeric|min:0',
            'unit' => 'nullable|string|max:50',
            'notes' => 'nullable|string',
            'category' => 'nullable|string|max:100',
            'is_checked' => 'boolean'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $item->update($request->all());

        return response()->json([
            'success' => true,
            'message' => 'Item updated successfully',
            'data' => $item->fresh()
        ]);
    }

    /**
     * Delete shopping list item.
     */
    public function deleteItem(ShoppingList $shoppingList, ShoppingListItem $item): JsonResponse
    {
        // Check if user owns this shopping list and item belongs to it
        if ($shoppingList->user_id !== Auth::id() || $item->shopping_list_id !== $shoppingList->id) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized'
            ], 403);
        }

        $this->shoppingListService->deleteItem($item);

        return response()->json([
            'success' => true,
            'message' => 'Item deleted successfully'
        ]);
    }

    /**
     * Toggle item checked status.
     */
    public function toggleItem(ShoppingList $shoppingList, ShoppingListItem $item): JsonResponse
    {
        // Check if user owns this shopping list and item belongs to it
        if ($shoppingList->user_id !== Auth::id() || $item->shopping_list_id !== $shoppingList->id) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized'
            ], 403);
        }

        $this->shoppingListService->toggleItemChecked($item);

        return response()->json([
            'success' => true,
            'message' => 'Item status toggled successfully',
            'data' => $item->fresh()
        ]);
    }

    /**
     * Clear checked items from shopping list.
     */
    public function clearChecked(ShoppingList $shoppingList): JsonResponse
    {
        // Check if user owns this shopping list
        if ($shoppingList->user_id !== Auth::id()) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized'
            ], 403);
        }

        $deletedCount = $this->shoppingListService->clearCheckedItems($shoppingList);

        return response()->json([
            'success' => true,
            'message' => "{$deletedCount} checked items cleared successfully"
        ]);
    }

    /**
     * Mark shopping list as completed.
     */
    public function markCompleted(ShoppingList $shoppingList): JsonResponse
    {
        // Check if user owns this shopping list
        if ($shoppingList->user_id !== Auth::id()) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized'
            ], 403);
        }

        $this->shoppingListService->markShoppingListCompleted($shoppingList);

        return response()->json([
            'success' => true,
            'message' => 'Shopping list marked as completed'
        ]);
    }

    /**
     * Generate shopping list from meal plan.
     */
    public function generateFromMealPlan(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'meal_plan_id' => 'required|exists:weekly_meal_plans,id',
            'shopping_list_id' => 'nullable|exists:shopping_lists,id'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $mealPlan = WeeklyMealPlan::find($request->meal_plan_id);

        // Check if user owns this meal plan
        if ($mealPlan->user_id !== Auth::id()) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized'
            ], 403);
        }

        $shoppingList = null;
        if ($request->shopping_list_id) {
            $shoppingList = ShoppingList::find($request->shopping_list_id);
            if ($shoppingList->user_id !== Auth::id()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized'
                ], 403);
            }
        }

        $result = $this->shoppingListService->generateFromMealPlan($mealPlan, $shoppingList);

        return response()->json([
            'success' => true,
            'message' => 'Shopping list generated from meal plan successfully',
            'data' => $result
        ]);
    }

    /**
     * Generate shopping list from recipe.
     */
    public function generateFromRecipe(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'recipe_id' => 'required|exists:recipes,id',
            'shopping_list_id' => 'nullable|exists:shopping_lists,id'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $recipe = Recipe::find($request->recipe_id);
        $user = Auth::user();

        $shoppingList = null;
        if ($request->shopping_list_id) {
            $shoppingList = ShoppingList::find($request->shopping_list_id);
            if ($shoppingList->user_id !== $user->id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized'
                ], 403);
            }
        }

        $result = $this->shoppingListService->generateFromRecipe($recipe, $user, $shoppingList);

        return response()->json([
            'success' => true,
            'message' => 'Shopping list generated from recipe successfully',
            'data' => $result
        ]);
    }

    /**
     * Get shopping list with items grouped by category.
     */
    public function getByCategories(ShoppingList $shoppingList): JsonResponse
    {
        // Check if user owns this shopping list
        if ($shoppingList->user_id !== Auth::id()) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized'
            ], 403);
        }

        $categories = $this->shoppingListService->getShoppingListWithCategories($shoppingList);

        return response()->json([
            'success' => true,
            'data' => $categories
        ]);
    }
}
