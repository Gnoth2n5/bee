<?php

namespace App\Http\Controllers;

use App\Models\WeeklyMealPlan;
use App\Models\Favorite;
use App\Services\WeeklyMealPlanService;
use App\Services\WeatherService;
use App\Services\OpenAiService;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

use Illuminate\Support\Facades\App;
use Illuminate\Http\JsonResponse;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class WeeklyMealPlanController extends Controller
{
    protected $mealPlanService;

    public function __construct(WeeklyMealPlanService $mealPlanService)
    {
        $this->mealPlanService = $mealPlanService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $user = $request->user();
        $mealPlans = WeeklyMealPlan::where('user_id', $user->id)
            ->orderBy('week_start', 'desc')
            ->paginate(10);

        return view('meal-plans.index', compact('mealPlans'));
    }

    /**
     * Store a newly created meal plan.
     */
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'week_start' => 'required|date'
        ]);

        $user = Auth::user();
        $weekStart = Carbon::parse($request->week_start);

        // Check if meal plan already exists for this week
        $existingPlan = $this->mealPlanService->getMealPlanForWeek($user, $weekStart);
        if ($existingPlan) {
            return response()->json([
                'success' => false,
                'message' => 'Đã có kế hoạch bữa ăn cho tuần này'
            ], 400);
        }

        $mealPlan = $this->mealPlanService->createMealPlan($user, $request->name, $weekStart);

        return response()->json([
            'success' => true,
            'message' => 'Tạo kế hoạch bữa ăn thành công',
            'data' => $mealPlan
        ], 201);
    }

    /**
     * Display the specified meal plan.
     */
    public function show(WeeklyMealPlan $mealPlan): JsonResponse
    {
        $user = Auth::user();

        if ($mealPlan->user_id !== $user->id) {
            return response()->json([
                'success' => false,
                'message' => 'Không có quyền truy cập'
            ], 403);
        }

        $statistics = $this->mealPlanService->getStatistics($mealPlan);

        return response()->json([
            'success' => true,
            'data' => [
                'meal_plan' => $mealPlan,
                'statistics' => $statistics,
                'recipes' => $mealPlan->getAllRecipes()
            ]
        ]);
    }

    /**
     * Update the specified meal plan.
     */
    public function update(Request $request, WeeklyMealPlan $mealPlan): JsonResponse
    {
        $user = Auth::user();

        if ($mealPlan->user_id !== $user->id) {
            return response()->json([
                'success' => false,
                'message' => 'Không có quyền truy cập'
            ], 403);
        }

        $request->validate([
            'name' => 'sometimes|string|max:255',
            'is_active' => 'sometimes|boolean'
        ]);

        $mealPlan->update($request->only(['name', 'is_active']));

        return response()->json([
            'success' => true,
            'message' => 'Cập nhật kế hoạch bữa ăn thành công',
            'data' => $mealPlan
        ]);
    }

    /**
     * Remove the specified meal plan.
     */
    public function destroy(WeeklyMealPlan $mealPlan)
    {
        $user = Auth::user();

        if ($mealPlan->user_id !== $user->id) {
            abort(403, 'Không có quyền truy cập');
        }

        $mealPlan->delete();

        return redirect()->route('meal-plans.index')
            ->with('success', 'Xóa kế hoạch bữa ăn thành công');
    }

    /**
     * Get current active meal plan.
     */
    public function current(): JsonResponse
    {
        $user = Auth::user();
        $mealPlan = $this->mealPlanService->getCurrentMealPlan($user);

        if (!$mealPlan) {
            return response()->json([
                'success' => false,
                'message' => 'Không có kế hoạch bữa ăn hiện tại'
            ], 404);
        }

        $statistics = $this->mealPlanService->getStatistics($mealPlan);

        return response()->json([
            'success' => true,
            'data' => [
                'meal_plan' => $mealPlan,
                'statistics' => $statistics,
                'recipes' => $mealPlan->getAllRecipes()
            ]
        ]);
    }

    /**
     * Add meal to specific day and meal type.
     */
    public function addMeal(Request $request, WeeklyMealPlan $mealPlan): JsonResponse
    {
        $user = Auth::user();

        if ($mealPlan->user_id !== $user->id) {
            return response()->json([
                'success' => false,
                'message' => 'Không có quyền truy cập'
            ], 403);
        }

        $request->validate([
            'day' => 'required|string|in:monday,tuesday,wednesday,thursday,friday,saturday,sunday',
            'meal_type' => 'required|string|in:breakfast,lunch,dinner,snack',
            'recipe_id' => 'required|integer|exists:recipes,id'
        ]);

        $success = $this->mealPlanService->addMeal(
            $mealPlan,
            $request->day,
            $request->meal_type,
            $request->recipe_id
        );

        if (!$success) {
            return response()->json([
                'success' => false,
                'message' => 'Không thể thêm món ăn'
            ], 400);
        }

        return response()->json([
            'success' => true,
            'message' => 'Thêm món ăn thành công',
            'data' => $mealPlan->fresh()
        ]);
    }

    /**
     * Remove meal from specific day and meal type.
     */
    public function removeMeal(Request $request, WeeklyMealPlan $mealPlan): JsonResponse
    {
        $user = Auth::user();

        if ($mealPlan->user_id !== $user->id) {
            return response()->json([
                'success' => false,
                'message' => 'Không có quyền truy cập'
            ], 403);
        }

        $request->validate([
            'day' => 'required|string|in:monday,tuesday,wednesday,thursday,friday,saturday,sunday',
            'meal_type' => 'required|string|in:breakfast,lunch,dinner,snack',
            'recipe_id' => 'required|integer|exists:recipes,id'
        ]);

        $success = $this->mealPlanService->removeMeal(
            $mealPlan,
            $request->day,
            $request->meal_type,
            $request->recipe_id
        );

        return response()->json([
            'success' => true,
            'message' => 'Xóa món ăn thành công',
            'data' => $mealPlan->fresh()
        ]);
    }

    /**
     * Generate AI suggestions for meal plan.
     */
    public function generateSuggestions(Request $request): JsonResponse
    {
        $request->validate([
            'week_start' => 'required|date'
        ]);

        $user = Auth::user();
        $weekStart = Carbon::parse($request->week_start);

        $suggestions = $this->mealPlanService->generateAiSuggestions($user, $weekStart);

        return response()->json([
            'success' => true,
            'data' => $suggestions
        ]);
    }

    /**
     * Generate shopping list from meal plan.
     */
    public function generateShoppingList(WeeklyMealPlan $mealPlan): JsonResponse
    {
        $user = Auth::user();

        if ($mealPlan->user_id !== $user->id) {
            return response()->json([
                'success' => false,
                'message' => 'Không có quyền truy cập'
            ], 403);
        }

        $shoppingList = $this->mealPlanService->generateShoppingList($mealPlan);

        return response()->json([
            'success' => true,
            'data' => $shoppingList
        ]);
    }

    /**
     * Duplicate meal plan for next week.
     */
    public function duplicateForNextWeek(WeeklyMealPlan $mealPlan): JsonResponse
    {
        $user = Auth::user();

        if ($mealPlan->user_id !== $user->id) {
            return response()->json([
                'success' => false,
                'message' => 'Không có quyền truy cập'
            ], 403);
        }

        $newMealPlan = $this->mealPlanService->duplicateForNextWeek($mealPlan);

        return response()->json([
            'success' => true,
            'message' => 'Sao chép kế hoạch bữa ăn thành công',
            'data' => $newMealPlan
        ]);
    }

    /**
     * Get personalized suggestions for meal type.
     */
    public function getPersonalizedSuggestions(Request $request): JsonResponse
    {
        $request->validate([
            'meal_type' => 'required|string|in:breakfast,lunch,dinner,snack'
        ]);

        $user = Auth::user();
        $suggestions = $this->mealPlanService->getPersonalizedSuggestions($user, $request->meal_type);

        return response()->json([
            'success' => true,
            'data' => $suggestions
        ]);
    }

    /**
     * Get meal plan statistics.
     */
    public function getStatistics(WeeklyMealPlan $mealPlan): JsonResponse
    {
        $user = Auth::user();

        if ($mealPlan->user_id !== $user->id) {
            return response()->json([
                'success' => false,
                'message' => 'Không có quyền truy cập'
            ], 403);
        }

        $statistics = $this->mealPlanService->getStatistics($mealPlan);

        return response()->json([
            'success' => true,
            'data' => $statistics
        ]);
    }

    /**
     * Show weekly meals for a specific meal plan.
     */
    public function showWeeklyMeals(WeeklyMealPlan $mealPlan)
    {
        $user = Auth::user();

        if ($mealPlan->user_id !== $user->id) {
            abort(403, 'Không có quyền truy cập');
        }

        $weeklyMeals = $this->mealPlanService->generateWeeklyMeals($mealPlan);

        return view('meal-plans.weekly-meals', compact('mealPlan', 'weeklyMeals'));
    }
}
