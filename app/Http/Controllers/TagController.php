<?php

namespace App\Http\Controllers;

use App\Models\Tag;
use App\Services\TagService;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TagController extends Controller
{
    use AuthorizesRequests;

    public function __construct(
        private TagService $tagService
    ) {}

    /**
     * Display a listing of the resource.
     */
    public function index(): View
    {
        $tags = $this->tagService->getAllWithRecipeCount();

        return view('tags.index', compact('tags'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        $this->authorize('create', Tag::class);

        return view('tags.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:tags',
            'description' => 'nullable|string|max:1000',
        ], [
            'name.required' => 'Tên tag là bắt buộc.',
            'name.unique' => 'Tên tag đã tồn tại.',
            'name.max' => 'Tên tag không được vượt quá 255 ký tự.',
            'description.max' => 'Mô tả không được vượt quá 1000 ký tự.',
        ]);

        $tag = $this->tagService->create($validated);

        return redirect()->route('tags.index')
                        ->with('success', 'Tag đã được tạo thành công.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Tag $tag): View
    {
        $tag = $this->tagService->getWithRecipes($tag);

        return view('tags.show', compact('tag'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Tag $tag): View
    {
        $this->authorize('update', $tag);

        return view('tags.edit', compact('tag'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Tag $tag): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:tags,name,' . $tag->id,
            'description' => 'nullable|string|max:1000',
        ], [
            'name.required' => 'Tên tag là bắt buộc.',
            'name.unique' => 'Tên tag đã tồn tại.',
            'name.max' => 'Tên tag không được vượt quá 255 ký tự.',
            'description.max' => 'Mô tả không được vượt quá 1000 ký tự.',
        ]);

        $this->tagService->update($tag, $validated);

        return redirect()->route('tags.index')
                        ->with('success', 'Tag đã được cập nhật thành công.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Tag $tag): RedirectResponse
    {
        $this->authorize('delete', $tag);

        $deleted = $this->tagService->delete($tag);

        if ($deleted) {
            return redirect()->route('tags.index')
                            ->with('success', 'Tag đã được xóa thành công.');
        }

        return redirect()->route('tags.index')
                        ->with('error', 'Không thể xóa tag có chứa công thức.');
    }

    /**
     * Get tags for select dropdown.
     */
    public function getForSelect(): JsonResponse
    {
        $tags = $this->tagService->getForSelect();

        return response()->json($tags);
    }

    /**
     * Search tags.
     */
    public function search(Request $request): JsonResponse
    {
        $search = $request->get('q', '');
        $tags = $this->tagService->search($search);

        return response()->json($tags);
    }

    /**
     * Get popular tags.
     */
    public function popular(): JsonResponse
    {
        $tags = $this->tagService->getPopular();

        return response()->json($tags);
    }

    /**
     * Get tags for autocomplete.
     */
    public function autocomplete(Request $request): JsonResponse
    {
        $search = $request->get('q', '');
        $tags = $this->tagService->getForAutocomplete($search);

        return response()->json($tags);
    }
} 