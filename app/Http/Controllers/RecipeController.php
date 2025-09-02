<?php

namespace App\Http\Controllers;

use App\Http\Requests\Recipe\StoreRecipeRequest;
use App\Http\Requests\Recipe\UpdateRecipeRequest;
use App\Models\Recipe;
use App\Models\Category;
use App\Models\Tag;
use App\Services\RecipeService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use App\Models\Collection;
use App\Models\Favorite;
use App\Exports\RecipesExcelExport;
use App\Exports\RecipesCsvExport;
use App\Exports\RecipesZipExport;
use App\Exports\RecipesPdfExport;
use Maatwebsite\Excel\Facades\Excel;

class RecipeController extends Controller
{
    public function __construct(
        private RecipeService $recipeService
    ) {}

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        $filters = $request->only(['category', 'difficulty', 'cooking_time', 'search', 'sort']);
        $recipes = $this->recipeService->getFilteredRecipes($filters);

        $categories = Category::all();
        $tags = Tag::orderBy('usage_count', 'desc')->limit(20)->get();

        return view('recipes.index', compact('recipes', 'categories', 'tags', 'filters'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        $this->authorize('create', Recipe::class);

        $categories = Category::all();
        $tags = Tag::all();

        return view('recipes.create', compact('categories', 'tags'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreRecipeRequest $request): JsonResponse|RedirectResponse
    {
        $recipe = $this->recipeService->create($request->validated(), $request->user());

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Công thức đã được tạo thành công và đang chờ duyệt.',
                'recipe' => $recipe->load(['categories', 'tags']),
                'redirect' => route('recipes.show', $recipe)
            ]);
        }

        return redirect()->route('recipes.show', $recipe)
            ->with('success', 'Công thức đã được tạo thành công và đang chờ duyệt.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Recipe $recipe): View
    {
        $this->authorize('view', $recipe);

        $this->recipeService->incrementViewCount($recipe);
        $recipe->load(['user', 'categories', 'tags', 'images', 'ratings.user']);
        $relatedRecipes = $this->recipeService->getRelatedRecipes($recipe);

        return view('recipes.show', compact('recipe', 'relatedRecipes'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Recipe $recipe): View
    {
        $this->authorize('update', $recipe);

        $recipe->load(['categories', 'tags']);
        $categories = Category::all();
        $tags = Tag::all();

        return view('recipes.edit', compact('recipe', 'categories', 'tags'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateRecipeRequest $request, Recipe $recipe): JsonResponse|RedirectResponse
    {
        $recipe = $this->recipeService->update($recipe, $request->validated());

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Công thức đã được cập nhật thành công.',
                'recipe' => $recipe->load(['categories', 'tags']),
                'redirect' => route('recipes.show', $recipe)
            ]);
        }

        return redirect()->route('recipes.show', $recipe)
            ->with('success', 'Công thức đã được cập nhật thành công.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Recipe $recipe): JsonResponse|RedirectResponse
    {
        $this->authorize('delete', $recipe);

        $this->recipeService->delete($recipe);

        if (request()->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Công thức đã được xóa thành công.',
                'redirect' => route('recipes.index')
            ]);
        }

        return redirect()->route('recipes.index')
            ->with('success', 'Công thức đã được xóa thành công.');
    }

    /**
     * Display user's recipes.
     */
    public function myRecipes(Request $request): View
    {
        $user = $request->user();

        $recipes = Recipe::where('user_id', $user->id)
            ->with(['categories', 'tags', 'images'])
            ->orderBy('created_at', 'desc')
            ->paginate(12);

        $collections = Collection::where('user_id', $user->id)
            ->withCount('recipes')
            ->orderBy('created_at', 'desc')
            ->get();

        $stats = [
            'recipes_count' => Recipe::where('user_id', $user->id)->count(),
            'collections_count' => Collection::where('user_id', $user->id)->count(),
            'favorites_count' => Favorite::where('user_id', $user->id)->count(),
        ];

        return view('recipes.my-recipes', compact('recipes', 'collections', 'stats', 'user'));
    }

    /**
     * Display pending recipes for admin approval.
     */
    public function pending(Request $request): View
    {
        $this->authorize('approveAny', Recipe::class);

        $recipes = Recipe::where('status', 'pending')
            ->with(['user', 'categories', 'tags'])
            ->orderBy('created_at', 'asc')
            ->paginate(20);

        return view('admin.recipes.pending', compact('recipes'));
    }

    /**
     * Approve a recipe.
     */
    public function approve(Request $request, Recipe $recipe): JsonResponse
    {
        $this->authorize('approve', $recipe);

        $recipe = $this->recipeService->approve($recipe, $request->user());

        return response()->json([
            'success' => true,
            'message' => 'Công thức đã được duyệt thành công.',
            'recipe' => $recipe
        ]);
    }

    /**
     * Reject a recipe.
     */
    public function reject(Request $request, Recipe $recipe): JsonResponse
    {
        $this->authorize('reject', $recipe);

        $request->validate([
            'rejection_reason' => 'required|string|max:500'
        ]);

        $recipe = $this->recipeService->reject($recipe, $request->user(), $request->rejection_reason);

        return response()->json([
            'success' => true,
            'message' => 'Công thức đã bị từ chối.',
            'recipe' => $recipe
        ]);
    }

    /**
     * Get recipes for AJAX requests (Livewire compatibility).
     */
    public function getRecipes(Request $request): JsonResponse
    {
        $filters = $request->only(['category', 'difficulty', 'cooking_time', 'search', 'sort']);
        $recipes = $this->recipeService->getFilteredRecipes($filters, 12);

        return response()->json([
            'recipes' => $recipes->items(),
            'pagination' => [
                'current_page' => $recipes->currentPage(),
                'last_page' => $recipes->lastPage(),
                'per_page' => $recipes->perPage(),
                'total' => $recipes->total(),
            ]
        ]);
    }

    /**
     * Get recipe details for AJAX requests.
     */
    public function getRecipe(Recipe $recipe): JsonResponse
    {
        $this->authorize('view', $recipe);

        $recipe->load(['user', 'categories', 'tags', 'images', 'ratings.user']);

        return response()->json([
            'recipe' => $recipe,
            'related_recipes' => $this->recipeService->getRelatedRecipes($recipe)
        ]);
    }

    /**
     * Export recipes to Excel format.
     */
    public function exportExcel(Request $request)
    {
        $user = $request->user();
        $filters = $request->only(['category', 'difficulty', 'search', 'status']);

        $fileName = 'danh-sach-cong-thuc-' . now()->format('Y-m-d_H-i-s') . '.xlsx';

        return Excel::download(new RecipesExcelExport($user, $filters), $fileName);
    }

    /**
     * Export recipes to CSV format.
     */
    public function exportCsv(Request $request)
    {
        $user = $request->user();
        $filters = $request->only(['category', 'difficulty', 'search', 'status']);

        $fileName = 'danh-sach-cong-thuc-' . now()->format('Y-m-d_H-i-s') . '.csv';

        return Excel::download(new RecipesCsvExport($user, $filters), $fileName);
    }

    /**
     * Export recipes to ZIP format with custom templates.
     */
    public function exportZip(Request $request)
    {
        $user = $request->user();
        $filters = $request->only(['category', 'difficulty', 'search', 'status']);
        $template = $request->get('template', 'default');

        $zipExport = new RecipesZipExport($user, $filters, $template);
        $zipPath = $zipExport->export();

        $fileName = 'cong-thuc-' . now()->format('Y-m-d_H-i-s') . '.zip';

        return response()->download($zipPath, $fileName)->deleteFileAfterSend();
    }

    /**
     * Export recipes to PDF format.
     */
    public function exportPdf(Request $request)
    {
        $user = $request->user();
        $filters = $request->only(['category', 'difficulty', 'search', 'status']);

        $pdfExport = new RecipesPdfExport($user, $filters);
        $pdfContent = $pdfExport->export();

        $fileName = 'danh-sach-cong-thuc-' . now()->format('Y-m-d_H-i-s') . '.pdf';

        return response($pdfContent)
            ->header('Content-Type', 'application/pdf')
            ->header('Content-Disposition', 'attachment; filename="' . $fileName . '"');
    }

    /**
     * Export user's own recipes to Excel format.
     */
    public function exportMyRecipesExcel(Request $request)
    {
        $user = $request->user();
        $filters = $request->only(['category', 'difficulty', 'search', 'status']);

        $fileName = 'cong-thuc-cua-toi-' . now()->format('Y-m-d_H-i-s') . '.xlsx';

        return Excel::download(new RecipesExcelExport($user, $filters), $fileName);
    }

    /**
     * Export user's own recipes to ZIP format.
     */
    public function exportMyRecipesZip(Request $request)
    {
        $user = $request->user();
        $filters = $request->only(['category', 'difficulty', 'search', 'status']);
        $template = $request->get('template', 'default');

        $zipExport = new RecipesZipExport($user, $filters, $template);
        $zipPath = $zipExport->export();

        $fileName = 'cong-thuc-cua-toi-' . now()->format('Y-m-d_H-i-s') . '.zip';

        return response()->download($zipPath, $fileName)->deleteFileAfterSend();
    }
}
