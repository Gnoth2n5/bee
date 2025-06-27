<?php

namespace App\Services;

use App\Models\Recipe;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class RecipeService
{
    /**
     * Create a new recipe.
     */
    public function create(array $data, User $user): Recipe
    {
        $recipe = new Recipe($data);
        $recipe->user_id = $user->id;
        $recipe->slug = Str::slug($data['title']);
        $recipe->status = 'pending';
        $recipe->save();

        // Attach categories and tags
        $recipe->categories()->attach($data['category_ids']);
        if (!empty($data['tag_ids'])) {
            $recipe->tags()->attach($data['tag_ids']);
        }

        // Handle featured image
        if (isset($data['featured_image']) && $data['featured_image'] instanceof UploadedFile) {
            $this->handleFeaturedImage($recipe, $data['featured_image']);
        }

        return $recipe;
    }

    /**
     * Update an existing recipe.
     */
    public function update(Recipe $recipe, array $data): Recipe
    {
        $recipe->update($data);
        $recipe->slug = Str::slug($data['title']);
        $recipe->status = 'pending';
        $recipe->save();

        // Sync categories and tags
        $recipe->categories()->sync($data['category_ids']);
        $recipe->tags()->sync($data['tag_ids'] ?? []);

        // Handle featured image
        if (isset($data['featured_image']) && $data['featured_image'] instanceof UploadedFile) {
            $this->handleFeaturedImage($recipe, $data['featured_image'], true);
        }

        return $recipe;
    }

    /**
     * Delete a recipe.
     */
    public function delete(Recipe $recipe): bool
    {
        // Delete featured image
        if ($recipe->featured_image) {
            Storage::disk('public')->delete($recipe->featured_image);
        }

        return $recipe->delete();
    }

    /**
     * Approve a recipe.
     */
    public function approve(Recipe $recipe, User $approver): Recipe
    {
        $recipe->update([
            'status' => 'approved',
            'approved_by' => $approver->id,
            'approved_at' => now(),
            'published_at' => now(),
        ]);

        return $recipe;
    }

    /**
     * Reject a recipe.
     */
    public function reject(Recipe $recipe, User $rejecter, string $reason): Recipe
    {
        $recipe->update([
            'status' => 'rejected',
            'approved_by' => $rejecter->id,
            'approved_at' => now(),
            'rejection_reason' => $reason,
        ]);

        return $recipe;
    }

    /**
     * Handle featured image upload.
     */
    protected function handleFeaturedImage(Recipe $recipe, UploadedFile $image, bool $deleteOld = false): void
    {
        if ($deleteOld && $recipe->featured_image) {
            Storage::disk('public')->delete($recipe->featured_image);
        }

        $path = $image->store('recipes', 'public');
        $recipe->update(['featured_image' => $path]);
    }

    /**
     * Get recipes with filters.
     */
    public function getFilteredRecipes(array $filters = [], int $perPage = 12)
    {
        $query = Recipe::with(['user', 'categories', 'tags', 'images'])
                      ->where('status', 'approved')
                      ->whereNotNull('published_at');

        // Apply filters
        $this->applyFilters($query, $filters);

        return $query->paginate($perPage);
    }

    /**
     * Apply filters to recipe query.
     */
    protected function applyFilters($query, array $filters): void
    {
        // Category filter
        if (!empty($filters['category'])) {
            $query->whereHas('categories', function ($q) use ($filters) {
                $q->where('slug', $filters['category']);
            });
        }

        // Difficulty filter
        if (!empty($filters['difficulty'])) {
            $query->where('difficulty', $filters['difficulty']);
        }

        // Cooking time filter
        if (!empty($filters['cooking_time'])) {
            $this->applyCookingTimeFilter($query, $filters['cooking_time']);
        }

        // Search filter
        if (!empty($filters['search'])) {
            $this->applySearchFilter($query, $filters['search']);
        }

        // Sort
        $this->applySorting($query, $filters['sort'] ?? 'latest');
    }

    /**
     * Apply cooking time filter.
     */
    protected function applyCookingTimeFilter($query, string $time): void
    {
        switch ($time) {
            case 'quick':
                $query->where('cooking_time', '<=', 30);
                break;
            case 'medium':
                $query->whereBetween('cooking_time', [31, 60]);
                break;
            case 'long':
                $query->where('cooking_time', '>', 60);
                break;
        }
    }

    /**
     * Apply search filter.
     */
    protected function applySearchFilter($query, string $search): void
    {
        $query->where(function ($q) use ($search) {
            $q->where('title', 'like', "%{$search}%")
              ->orWhere('description', 'like', "%{$search}%")
              ->orWhere('summary', 'like', "%{$search}%");
        });
    }

    /**
     * Apply sorting.
     */
    protected function applySorting($query, string $sort): void
    {
        switch ($sort) {
            case 'popular':
                $query->orderBy('view_count', 'desc');
                break;
            case 'rating':
                $query->orderBy('average_rating', 'desc');
                break;
            case 'oldest':
                $query->orderBy('created_at', 'asc');
                break;
            default:
                $query->orderBy('created_at', 'desc');
        }
    }

    /**
     * Get related recipes.
     */
    public function getRelatedRecipes(Recipe $recipe, int $limit = 6)
    {
        return Recipe::where('status', 'approved')
                    ->where('id', '!=', $recipe->id)
                    ->whereHas('categories', function ($q) use ($recipe) {
                        $q->whereIn('categories.id', $recipe->categories->pluck('id'));
                    })
                    ->limit($limit)
                    ->get();
    }

    /**
     * Increment view count.
     */
    public function incrementViewCount(Recipe $recipe): void
    {
        $recipe->increment('view_count');
    }
} 