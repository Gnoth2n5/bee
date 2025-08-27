<?php

namespace App\Http\Controllers;

use App\Models\WeeklyMealPlan;
use App\Models\Favorite;
use App\Services\WeeklyMealPlanService;
use App\Services\WeatherService;
use App\Services\OpenAiService;
use App\Exports\WeeklyMealPlanExport;
use App\Exports\AllMealPlansExport;
use App\Exports\MealPlansCsvExport;
use App\Exports\MealPlansPdfExport;
use App\Exports\MealPlansZipExport;
use App\Exports\MealPlansXmlExport;
use App\Exports\MealPlansMarkdownExport;
use App\Exports\MealPlansJsonExport;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

use Illuminate\Support\Facades\App;
use Illuminate\Http\JsonResponse;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;

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
    public function store(Request $request)
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
            return back()->withErrors(['week_start' => 'Đã có kế hoạch bữa ăn cho tuần này']);
        }

        $mealPlan = $this->mealPlanService->createMealPlan($user, $request->name, $weekStart);

        // Update additional fields if provided
        if ($request->has('is_active')) {
            $mealPlan->is_active = $request->boolean('is_active');
        }
        if ($request->has('weather_optimized')) {
            $mealPlan->weather_optimized = $request->boolean('weather_optimized');
        }
        if ($request->has('ai_suggestions_used')) {
            $mealPlan->ai_suggestions_used = $request->boolean('ai_suggestions_used');
        }
        $mealPlan->save();

        return redirect()->route('meal-plans.show', $mealPlan)
            ->with('success', 'Tạo kế hoạch bữa ăn thành công');
    }

    /**
     * Display the specified meal plan.
     */
    public function show(WeeklyMealPlan $mealPlan)
    {
        $user = Auth::user();

        if ($mealPlan->user_id !== $user->id) {
            abort(403, 'Không có quyền truy cập');
        }

        return view('meal-plans.show', compact('mealPlan'));
    }

    /**
     * Display the specified meal plan as JSON (API).
     */
    public function showJson(WeeklyMealPlan $mealPlan): JsonResponse
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
     * Show the form for editing the specified meal plan.
     */
    public function edit(WeeklyMealPlan $mealPlan)
    {
        $user = Auth::user();

        if ($mealPlan->user_id !== $user->id) {
            abort(403, 'Không có quyền truy cập');
        }

        return view('meal-plans.edit', compact('mealPlan'));
    }

    /**
     * Show the form for creating a new meal plan.
     */
    public function create()
    {
        return view('meal-plans.create');
    }

    /**
     * Update the specified meal plan.
     */
    public function update(Request $request, WeeklyMealPlan $mealPlan)
    {
        $user = Auth::user();

        if ($mealPlan->user_id !== $user->id) {
            abort(403, 'Không có quyền truy cập');
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'week_start' => 'required|date'
        ]);

        // Check if week_start is changed and if it conflicts with existing plan
        if ($mealPlan->week_start->format('Y-m-d') !== $request->week_start) {
            $weekStart = Carbon::parse($request->week_start);
            $existingPlan = $this->mealPlanService->getMealPlanForWeek($user, $weekStart);
            if ($existingPlan && $existingPlan->id !== $mealPlan->id) {
                return back()->withErrors(['week_start' => 'Đã có kế hoạch bữa ăn cho tuần này']);
            }
        }

        $mealPlan->update([
            'name' => $request->name,
            'week_start' => $request->week_start,
            'is_active' => $request->boolean('is_active'),
            'weather_optimized' => $request->boolean('weather_optimized'),
            'ai_suggestions_used' => $request->boolean('ai_suggestions_used')
        ]);

        return redirect()->route('meal-plans.show', $mealPlan)
            ->with('success', 'Cập nhật kế hoạch bữa ăn thành công');
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

    /**
     * Export a specific meal plan to Excel.
     */
    public function exportMealPlan(WeeklyMealPlan $mealPlan)
    {
        $user = Auth::user();

        if ($mealPlan->user_id !== $user->id) {
            abort(403, 'Không có quyền truy cập');
        }

        $fileName = 'ke-hoach-bua-an-' . $mealPlan->week_start->format('Y-m-d') . '.xlsx';

        return Excel::download(new WeeklyMealPlanExport($mealPlan), $fileName);
    }

    /**
     * Export all meal plans to Excel.
     */
    public function exportAllMealPlans()
    {
        $user = Auth::user();
        $fileName = 'danh-sach-ke-hoach-bua-an-' . now()->format('Y-m-d') . '.xlsx';

        return Excel::download(new AllMealPlansExport($user), $fileName);
    }

    /**
     * Export a specific meal plan to CSV.
     */
    public function exportMealPlanCsv(WeeklyMealPlan $mealPlan)
    {
        $user = Auth::user();

        if ($mealPlan->user_id !== $user->id) {
            abort(403, 'Không có quyền truy cập');
        }

        $fileName = 'ke-hoach-bua-an-' . $mealPlan->week_start->format('Y-m-d') . '.csv';

        return Excel::download(new MealPlansCsvExport(null, $mealPlan), $fileName);
    }

    /**
     * Export all meal plans to CSV.
     */
    public function exportAllMealPlansCsv()
    {
        $user = Auth::user();
        $fileName = 'danh-sach-ke-hoach-bua-an-' . now()->format('Y-m-d') . '.csv';

        return Excel::download(new MealPlansCsvExport($user), $fileName);
    }

    /**
     * Export a specific meal plan to PDF.
     */
    public function exportMealPlanPdf(WeeklyMealPlan $mealPlan)
    {
        $user = Auth::user();

        if ($mealPlan->user_id !== $user->id) {
            abort(403, 'Không có quyền truy cập');
        }

        $pdfExport = new MealPlansPdfExport(null, $mealPlan);
        $pdfContent = $pdfExport->export();

        $fileName = 'ke-hoach-bua-an-' . $mealPlan->week_start->format('Y-m-d') . '.pdf';

        return response($pdfContent)
            ->header('Content-Type', 'application/pdf')
            ->header('Content-Disposition', 'attachment; filename="' . $fileName . '"');
    }

    /**
     * Export all meal plans to PDF.
     */
    public function exportAllMealPlansPdf()
    {
        $user = Auth::user();
        $pdfExport = new MealPlansPdfExport($user);
        $pdfContent = $pdfExport->export();

        $fileName = 'danh-sach-ke-hoach-bua-an-' . now()->format('Y-m-d') . '.pdf';

        return response($pdfContent)
            ->header('Content-Type', 'application/pdf')
            ->header('Content-Disposition', 'attachment; filename="' . $fileName . '"');
    }

    /**
     * Export a specific meal plan to ZIP.
     */
    public function exportMealPlanZip(WeeklyMealPlan $mealPlan, Request $request)
    {
        $user = Auth::user();

        if ($mealPlan->user_id !== $user->id) {
            abort(403, 'Không có quyền truy cập');
        }

        $template = $request->get('template', 'default');
        $zipExport = new MealPlansZipExport(null, $mealPlan, $template);
        $zipPath = $zipExport->export();

        $fileName = 'ke-hoach-bua-an-' . $mealPlan->week_start->format('Y-m-d') . '.zip';

        return response()->download($zipPath, $fileName)->deleteFileAfterSend();
    }

    /**
     * Export all meal plans to ZIP.
     */
    public function exportAllMealPlansZip(Request $request)
    {
        $user = Auth::user();
        $template = $request->get('template', 'default');

        $zipExport = new MealPlansZipExport($user, null, $template);
        $zipPath = $zipExport->export();

        $fileName = 'danh-sach-ke-hoach-bua-an-' . now()->format('Y-m-d_H-i-s') . '.zip';

        return response()->download($zipPath, $fileName)->deleteFileAfterSend();
    }

    /**
     * Export a specific meal plan to XML.
     */
    public function exportMealPlanXml(WeeklyMealPlan $mealPlan)
    {
        $user = Auth::user();

        if ($mealPlan->user_id !== $user->id) {
            abort(403, 'Không có quyền truy cập');
        }

        $xmlExport = new MealPlansXmlExport(null, $mealPlan);
        $xmlContent = $xmlExport->export();

        $fileName = 'ke-hoach-bua-an-' . $mealPlan->week_start->format('Y-m-d') . '.xml';

        return response($xmlContent)
            ->header('Content-Type', 'application/xml')
            ->header('Content-Disposition', 'attachment; filename="' . $fileName . '"');
    }

    /**
     * Export all meal plans to XML.
     */
    public function exportAllMealPlansXml()
    {
        $user = Auth::user();
        $xmlExport = new MealPlansXmlExport($user);
        $xmlContent = $xmlExport->export();

        $fileName = 'danh-sach-ke-hoach-bua-an-' . now()->format('Y-m-d') . '.xml';

        return response($xmlContent)
            ->header('Content-Type', 'application/xml')
            ->header('Content-Disposition', 'attachment; filename="' . $fileName . '"');
    }

    /**
     * Export a specific meal plan to Markdown.
     */
    public function exportMealPlanMarkdown(WeeklyMealPlan $mealPlan)
    {
        $user = Auth::user();

        if ($mealPlan->user_id !== $user->id) {
            abort(403, 'Không có quyền truy cập');
        }

        $markdownExport = new MealPlansMarkdownExport(null, $mealPlan);
        $markdownContent = $markdownExport->export();

        $fileName = 'ke-hoach-bua-an-' . $mealPlan->week_start->format('Y-m-d') . '.md';

        return response($markdownContent)
            ->header('Content-Type', 'text/markdown')
            ->header('Content-Disposition', 'attachment; filename="' . $fileName . '"');
    }

    /**
     * Export all meal plans to Markdown.
     */
    public function exportAllMealPlansMarkdown()
    {
        $user = Auth::user();
        $markdownExport = new MealPlansMarkdownExport($user);
        $markdownContent = $markdownExport->export();

        $fileName = 'danh-sach-ke-hoach-bua-an-' . now()->format('Y-m-d') . '.md';

        return response($markdownContent)
            ->header('Content-Type', 'text/markdown')
            ->header('Content-Disposition', 'attachment; filename="' . $fileName . '"');
    }

    /**
     * Export a specific meal plan to JSON with different formats.
     */
    public function exportMealPlanJson(WeeklyMealPlan $mealPlan, Request $request)
    {
        $user = Auth::user();

        if ($mealPlan->user_id !== $user->id) {
            abort(403, 'Không có quyền truy cập');
        }

        $format = $request->get('format', 'detailed');
        $jsonExport = new MealPlansJsonExport(null, $mealPlan, $format);
        $jsonContent = $jsonExport->export();

        $fileName = 'ke-hoach-bua-an-' . $mealPlan->week_start->format('Y-m-d') . '-' . $format . '.json';

        return response($jsonContent)
            ->header('Content-Type', 'application/json')
            ->header('Content-Disposition', 'attachment; filename="' . $fileName . '"');
    }

    /**
     * Export all meal plans to JSON.
     */
    public function exportAllMealPlansJson()
    {
        $user = Auth::user();
        $jsonExport = new MealPlansJsonExport($user);
        $jsonContent = $jsonExport->export();

        $fileName = 'danh-sach-ke-hoach-bua-an-' . now()->format('Y-m-d') . '.json';

        return response($jsonContent)
            ->header('Content-Type', 'application/json')
            ->header('Content-Disposition', 'attachment; filename="' . $fileName . '"');
    }
}
