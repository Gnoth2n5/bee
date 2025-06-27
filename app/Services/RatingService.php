<?php

namespace App\Services;

use App\Models\Rating;
use App\Models\Recipe;
use App\Models\User;

class RatingService
{
    /**
     * Create a new rating.
     */
    public function create(array $data, Recipe $recipe, User $user): Rating
    {
        // Check if user already rated this recipe
        $existingRating = Rating::where('user_id', $user->id)
                               ->where('recipe_id', $recipe->id)
                               ->first();

        if ($existingRating) {
            throw new \Exception('Bạn đã đánh giá công thức này rồi.');
        }

        $rating = new Rating($data);
        $rating->user_id = $user->id;
        $rating->recipe_id = $recipe->id;
        $rating->save();

        // Update recipe rating stats
        $recipe->updateRatingStats();

        return $rating;
    }

    /**
     * Update an existing rating.
     */
    public function update(Rating $rating, array $data): Rating
    {
        $rating->update($data);

        // Update recipe rating stats
        $rating->recipe->updateRatingStats();

        return $rating;
    }

    /**
     * Delete a rating.
     */
    public function delete(Rating $rating): bool
    {
        $recipe = $rating->recipe;
        $deleted = $rating->delete();

        if ($deleted) {
            // Update recipe rating stats
            $recipe->updateRatingStats();
        }

        return $deleted;
    }

    /**
     * Get user's rating for a recipe.
     */
    public function getUserRating(Recipe $recipe, User $user): ?Rating
    {
        return Rating::where('user_id', $user->id)
                    ->where('recipe_id', $recipe->id)
                    ->first();
    }

    /**
     * Get average rating for a recipe.
     */
    public function getAverageRating(Recipe $recipe): float
    {
        return $recipe->ratings()->avg('rating') ?? 0;
    }

    /**
     * Get rating count for a recipe.
     */
    public function getRatingCount(Recipe $recipe): int
    {
        return $recipe->ratings()->count();
    }

    /**
     * Get rating distribution for a recipe.
     */
    public function getRatingDistribution(Recipe $recipe): array
    {
        $distribution = [];
        
        for ($i = 1; $i <= 5; $i++) {
            $count = $recipe->ratings()->where('rating', $i)->count();
            $distribution[$i] = $count;
        }

        return $distribution;
    }
} 