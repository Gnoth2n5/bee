<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Services\CategoryService;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;

class CategoryController extends Controller
{
    use AuthorizesRequests;

    public function __construct(
        private CategoryService $categoryService
    ) {}

    /**
     * Display a listing of the resource.
     */
    public function index(): View
    {
        $categories = $this->categoryService->getAllWithRecipeCount();

        return view('categories.index', compact('categories'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        $this->authorize('create', Category::class);

        return view('categories.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:categories',
            'description' => 'nullable|string|max:1000',
        ], [
            'name.required' => 'Tên danh mục là bắt buộc.',
            'name.unique' => 'Tên danh mục đã tồn tại.',
            'name.max' => 'Tên danh mục không được vượt quá 255 ký tự.',
            'description.max' => 'Mô tả không được vượt quá 1000 ký tự.',
        ]);

        $category = $this->categoryService->create($validated);

        return redirect()->route('categories.index')
                        ->with('success', 'Danh mục đã được tạo thành công.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Category $category): View
    {
        $category = $this->categoryService->getWithRecipes($category);

        return view('categories.show', compact('category'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Category $category): View
    {
        $this->authorize('update', $category);

        return view('categories.edit', compact('category'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Category $category): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:categories,name,' . $category->id,
            'description' => 'nullable|string|max:1000',
        ], [
            'name.required' => 'Tên danh mục là bắt buộc.',
            'name.unique' => 'Tên danh mục đã tồn tại.',
            'name.max' => 'Tên danh mục không được vượt quá 255 ký tự.',
            'description.max' => 'Mô tả không được vượt quá 1000 ký tự.',
        ]);

        $this->categoryService->update($category, $validated);

        return redirect()->route('categories.index')
                        ->with('success', 'Danh mục đã được cập nhật thành công.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Category $category): RedirectResponse
    {
        $this->authorize('delete', $category);

        $deleted = $this->categoryService->delete($category);

        if ($deleted) {
            return redirect()->route('categories.index')
                            ->with('success', 'Danh mục đã được xóa thành công.');
        }

        return redirect()->route('categories.index')
                        ->with('error', 'Không thể xóa danh mục có chứa công thức.');
    }

    /**
     * Get categories for select dropdown.
     */
    public function getForSelect(): JsonResponse
    {
        $categories = $this->categoryService->getForSelect();

        return response()->json($categories);
    }

    /**
     * Search categories.
     */
    public function search(Request $request): JsonResponse
    {
        $search = $request->get('q', '');
        $categories = $this->categoryService->search($search);

        return response()->json($categories);
    }

    /**
     * Get popular categories.
     */
    public function popular(): JsonResponse
    {
        $categories = $this->categoryService->getPopular();

        return response()->json($categories);
    }
} 