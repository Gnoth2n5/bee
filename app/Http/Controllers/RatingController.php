<?php

namespace App\Http\Controllers;

use App\Http\Requests\Rating\StoreRatingRequest;
use App\Models\Recipe;
use App\Models\Rating;
use App\Services\RatingService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;

class RatingController extends Controller
{
    public function __construct(
        private RatingService $ratingService
    ) {}

    /**
     * Store a newly created rating.
     */
    public function store(StoreRatingRequest $request, Recipe $recipe): JsonResponse|RedirectResponse
    {
        try {
            $rating = $this->ratingService->create($request->validated(), $recipe, $request->user());

            if ($request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Đánh giá của bạn đã được ghi nhận.',
                    'rating' => $rating,
                    'recipe_stats' => [
                        'average_rating' => $this->ratingService->getAverageRating($recipe),
                        'rating_count' => $this->ratingService->getRatingCount($recipe),
                    ]
                ]);
            }

            return back()->with('success', 'Đánh giá của bạn đã được ghi nhận.');
        } catch (\Exception $e) {
            if ($request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => $e->getMessage()
                ], 422);
            }

            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * Update the specified rating.
     */
    public function update(StoreRatingRequest $request, Recipe $recipe): JsonResponse|RedirectResponse
    {
        $rating = $this->ratingService->getUserRating($recipe, $request->user());

        if (!$rating) {
            $message = 'Không tìm thấy đánh giá.';
            
            if ($request->wantsJson()) {
                return response()->json(['success' => false, 'message' => $message], 404);
            }
            
            return back()->with('error', $message);
        }

        $rating = $this->ratingService->update($rating, $request->validated());

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Đánh giá đã được cập nhật.',
                'rating' => $rating,
                'recipe_stats' => [
                    'average_rating' => $this->ratingService->getAverageRating($recipe),
                    'rating_count' => $this->ratingService->getRatingCount($recipe),
                ]
            ]);
        }

        return back()->with('success', 'Đánh giá đã được cập nhật.');
    }

    /**
     * Remove the specified rating.
     */
    public function destroy(Recipe $recipe): JsonResponse|RedirectResponse
    {
        $rating = $this->ratingService->getUserRating($recipe, request()->user());

        if (!$rating) {
            $message = 'Không tìm thấy đánh giá.';
            
            if (request()->wantsJson()) {
                return response()->json(['success' => false, 'message' => $message], 404);
            }
            
            return back()->with('error', $message);
        }

        $this->ratingService->delete($rating);

        if (request()->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Đánh giá đã được xóa.',
                'recipe_stats' => [
                    'average_rating' => $this->ratingService->getAverageRating($recipe),
                    'rating_count' => $this->ratingService->getRatingCount($recipe),
                ]
            ]);
        }

        return back()->with('success', 'Đánh giá đã được xóa.');
    }

    /**
     * Get rating statistics for a recipe.
     */
    public function getStats(Recipe $recipe): JsonResponse
    {
        $stats = [
            'average_rating' => $this->ratingService->getAverageRating($recipe),
            'rating_count' => $this->ratingService->getRatingCount($recipe),
            'distribution' => $this->ratingService->getRatingDistribution($recipe),
        ];

        // Get user's rating if authenticated
        if (auth()->check()) {
            $userRating = $this->ratingService->getUserRating($recipe, auth()->user());
            $stats['user_rating'] = $userRating;
        }

        return response()->json($stats);
    }
} 