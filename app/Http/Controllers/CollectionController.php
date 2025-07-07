<?php

namespace App\Http\Controllers;

use App\Http\Requests\Collection\StoreCollectionRequest;
use App\Http\Requests\Collection\UpdateCollectionRequest;
use App\Models\Collection;
use App\Models\Recipe;
use App\Services\CollectionService;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;

class CollectionController extends Controller
{
    use AuthorizesRequests;

    public function __construct(
        private CollectionService $collectionService
    ) {}

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        $collections = $this->collectionService->getUserCollections($request->user());

        return view('collections.index', compact('collections'));
    }

    /**
     * Display public collections.
     */
    public function public(Request $request): View
    {
        $collections = $this->collectionService->getPublicCollections();

        return view('collections.public', compact('collections'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        $this->authorize('create', Collection::class);

        return view('collections.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreCollectionRequest $request): RedirectResponse
    {
        $collection = $this->collectionService->create($request->validated(), $request->user());

        return redirect()->route('collections.show', $collection)
                        ->with('success', 'Bộ sưu tập đã được tạo thành công.');
    }



    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Collection $collection): View
    {
        $this->authorize('update', $collection);

        return view('collections.edit', compact('collection'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateCollectionRequest $request, Collection $collection): RedirectResponse
    {
        $this->collectionService->update($collection, $request->validated());

        return redirect()->route('collections.show', $collection)
                        ->with('success', 'Bộ sưu tập đã được cập nhật thành công.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Collection $collection): RedirectResponse
    {
        $this->authorize('delete', $collection);

        $this->collectionService->delete($collection);

        return redirect()->route('collections.index')
                        ->with('success', 'Bộ sưu tập đã được xóa thành công.');
    }

    /**
     * Add recipe to collection.
     */
    public function addRecipe(Collection $collection, Recipe $recipe): JsonResponse
    {
        $this->authorize('addRecipe', $collection);

        $added = $this->collectionService->addRecipe($collection, $recipe);

        if ($added) {
            return response()->json([
                'success' => true,
                'message' => 'Đã thêm công thức vào bộ sưu tập.',
                'recipe_count' => $collection->fresh()->recipe_count
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Công thức đã có trong bộ sưu tập.'
        ], 400);
    }

    /**
     * Remove recipe from collection.
     */
    public function removeRecipe(Collection $collection, Recipe $recipe): JsonResponse
    {
        $this->authorize('removeRecipe', $collection);

        $removed = $this->collectionService->removeRecipe($collection, $recipe);

        if ($removed) {
            return response()->json([
                'success' => true,
                'message' => 'Đã xóa công thức khỏi bộ sưu tập.',
                'recipe_count' => $collection->fresh()->recipe_count
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Không tìm thấy công thức trong bộ sưu tập.'
        ], 404);
    }

    /**
     * Search collections.
     */
    public function search(Request $request): View
    {
        $search = $request->get('q', '');
        $collections = $this->collectionService->searchCollections($search);

        return view('collections.search', compact('collections', 'search'));
    }
} 