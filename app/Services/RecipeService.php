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
        // Xử lý dữ liệu trước khi tạo
        $recipeData = $this->prepareRecipeData($data);
        
        $recipe = new Recipe($recipeData);
        $recipe->user_id = $user->id;
        $recipe->slug = Str::slug($data['title']);
        $recipe->status = $data['status'] ?? 'pending';
        $recipe->save();

        // Attach categories and tags
        if (!empty($data['category_ids'])) {
            $recipe->categories()->attach($data['category_ids']);
        }
        
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
        // Xử lý dữ liệu trước khi cập nhật
        $recipeData = $this->prepareRecipeData($data);
        
        $recipe->update($recipeData);
        $recipe->slug = Str::slug($data['title']);
        $recipe->status = $data['status'] ?? 'pending';
        $recipe->save();

        // Sync categories and tags
        if (isset($data['category_ids'])) {
            $recipe->categories()->sync($data['category_ids']);
        }
        
        if (isset($data['tag_ids'])) {
            $recipe->tags()->sync($data['tag_ids']);
        }

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

        // Tags filter
        if (!empty($filters['tags']) && is_array($filters['tags'])) {
            $query->whereHas('tags', function ($q) use ($filters) {
                $q->whereIn('id', $filters['tags']);
            });
        }

        // Min rating filter
        if (!empty($filters['min_rating'])) {
            $query->where('average_rating', '>=', $filters['min_rating']);
        }

        // Max calories filter
        if (!empty($filters['max_calories'])) {
            $query->where('calories', '<=', $filters['max_calories']);
        }

        // Servings filter
        if (!empty($filters['servings'])) {
            $query->where('servings', $filters['servings']);
        }

        // Price range filter (placeholder for future implementation)
        if (!empty($filters['price_range'])) {
            // TODO: Implement price range filter when price field is added
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
            case 'cooking_time':
                $query->orderBy('cooking_time', 'asc');
                break;
            case 'title':
                $query->orderBy('title', 'asc');
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

    /**
     * Prepare recipe data for saving.
     */
    protected function prepareRecipeData(array $data): array
    {
        $recipeData = $data;

        // Xử lý ingredients
        if (isset($data['ingredients']) && is_array($data['ingredients'])) {
            $recipeData['ingredients'] = array_values(array_filter($data['ingredients'], function ($item) {
                return !empty($item['name']) && !empty($item['amount']);
            }));
        }

        // Xử lý instructions
        if (isset($data['instructions']) && is_array($data['instructions'])) {
            $recipeData['instructions'] = array_values(array_filter($data['instructions'], function ($item) {
                return !empty($item['instruction']);
            }));
            
            // Đánh số lại các bước
            foreach ($recipeData['instructions'] as $index => &$instruction) {
                $instruction['step'] = $index + 1;
            }
        }

        // Tính toán total_time
        $cookingTime = $data['cooking_time'] ?? 0;
        $preparationTime = $data['preparation_time'] ?? 0;
        $recipeData['total_time'] = $cookingTime + $preparationTime;

        // Loại bỏ các trường không cần thiết
        unset($recipeData['category_ids'], $recipeData['tag_ids']);

        return $recipeData;
    }
} 