<?php

namespace App\Services;

use App\Models\Collection;
use App\Models\Recipe;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class CollectionService
{
    /**
     * Create a new collection.
     */
    public function create(array $data, User $user): Collection
    {
        $collection = new Collection($data);
        $collection->user_id = $user->id;
        $collection->slug = Str::slug($data['name']);
        $collection->is_public = $data['is_public'] ?? false;

        // Handle cover image
        if (isset($data['cover_image']) && $data['cover_image'] instanceof UploadedFile) {
            $this->handleCoverImage($collection, $data['cover_image']);
        }

        $collection->save();

        return $collection;
    }

    /**
     * Update an existing collection.
     */
    public function update(Collection $collection, array $data): Collection
    {
        $collection->update($data);
        $collection->slug = Str::slug($data['name']);
        $collection->is_public = $data['is_public'] ?? false;

        // Handle cover image
        if (isset($data['cover_image']) && $data['cover_image'] instanceof UploadedFile) {
            $this->handleCoverImage($collection, $data['cover_image'], true);
        }

        $collection->save();

        return $collection;
    }

    /**
     * Delete a collection.
     */
    public function delete(Collection $collection): bool
    {
        // Delete cover image
        if ($collection->cover_image) {
            Storage::disk('public')->delete($collection->cover_image);
        }

        return $collection->delete();
    }

    /**
     * Get user's collections.
     */
    public function getUserCollections(User $user, int $perPage = 12)
    {
        return Collection::where('user_id', $user->id)
                        ->with(['recipes'])
                        ->orderBy('created_at', 'desc')
                        ->paginate($perPage);
    }

    /**
     * Get public collections.
     */
    public function getPublicCollections(int $perPage = 12)
    {
        return Collection::where('is_public', true)
                        ->with(['user', 'recipes'])
                        ->orderBy('created_at', 'desc')
                        ->paginate($perPage);
    }

    /**
     * Add recipe to collection.
     */
    public function addRecipe(Collection $collection, Recipe $recipe): bool
    {
        // Check if recipe is already in collection
        if ($collection->recipes()->where('recipe_id', $recipe->id)->exists()) {
            return false;
        }

        $collection->recipes()->attach($recipe->id);
        $collection->increment('recipe_count');

        return true;
    }

    /**
     * Remove recipe from collection.
     */
    public function removeRecipe(Collection $collection, Recipe $recipe): bool
    {
        $detached = $collection->recipes()->detach($recipe->id);
        
        if ($detached > 0) {
            $collection->decrement('recipe_count');
            return true;
        }

        return false;
    }

    /**
     * Check if user can view collection.
     */
    public function canView(Collection $collection, User $user): bool
    {
        return $collection->is_public || $collection->user_id === $user->id;
    }

    /**
     * Check if user can edit collection.
     */
    public function canEdit(Collection $collection, User $user): bool
    {
        return $collection->user_id === $user->id;
    }

    /**
     * Get collection with recipes.
     */
    public function getCollectionWithRecipes(Collection $collection)
    {
        return $collection->load(['recipes.user', 'recipes.categories', 'recipes.tags', 'user']);
    }

    /**
     * Handle cover image upload.
     */
    protected function handleCoverImage(Collection $collection, UploadedFile $image, bool $deleteOld = false): void
    {
        if ($deleteOld && $collection->cover_image) {
            Storage::disk('public')->delete($collection->cover_image);
        }

        $path = $image->store('collections', 'public');
        $collection->cover_image = $path;
    }

    /**
     * Search collections.
     */
    public function searchCollections(string $search, int $perPage = 12)
    {
        return Collection::where('is_public', true)
                        ->where(function ($query) use ($search) {
                            $query->where('name', 'like', "%{$search}%")
                                  ->orWhere('description', 'like', "%{$search}%");
                        })
                        ->with(['user', 'recipes'])
                        ->orderBy('created_at', 'desc')
                        ->paginate($perPage);
    }
} 