<?php

namespace App\Http\Controllers;

use App\Models\Recipe;
use App\Services\FavoriteService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;

class FavoriteController extends Controller
{
    public function __construct(
        private FavoriteService $favoriteService
    ) {}

    /**
     * Display a listing of user's favorite recipes.
     */
    public function index(Request $request): View
    {
        $favorites = $this->favoriteService->getUserFavorites($request->user());

        return view('favorites.index', compact('favorites'));
    }

    /**
     * Toggle favorite status for a recipe.
     */
    public function toggle(Recipe $recipe): JsonResponse
    {
        $result = $this->favoriteService->toggle($recipe, request()->user());

        return response()->json([
            'success' => true,
            'message' => $result['message'],
            'is_favorited' => $result['is_favorited'],
            'favorite_count' => $result['favorite_count']
        ]);
    }

    /**
     * Check if recipe is favorited by user.
     */
    public function check(Recipe $recipe): JsonResponse
    {
        $isFavorited = $this->favoriteService->isFavorited($recipe, request()->user());
        $favoriteCount = $this->favoriteService->getFavoriteCount($recipe);

        return response()->json([
            'is_favorited' => $isFavorited,
            'favorite_count' => $favoriteCount
        ]);
    }

    /**
     * Remove recipe from favorites.
     */
    public function remove(Recipe $recipe): JsonResponse
    {
        $removed = $this->favoriteService->removeFavorite($recipe, request()->user());

        if ($removed) {
            return response()->json([
                'success' => true,
                'message' => 'Đã xóa khỏi danh sách yêu thích.',
                'favorite_count' => $this->favoriteService->getFavoriteCount($recipe)
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Không tìm thấy trong danh sách yêu thích.'
        ], 404);
    }

    /**
     * Get user's favorite count.
     */
    public function count(): JsonResponse
    {
        $count = $this->favoriteService->getUserFavoriteCount(request()->user());

        return response()->json([
            'count' => $count
        ]);
    }
} 