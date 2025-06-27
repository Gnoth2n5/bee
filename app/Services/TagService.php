<?php

namespace App\Services;

use App\Models\Tag;
use Illuminate\Support\Str;

class TagService
{
    /**
     * Create a new tag.
     */
    public function create(array $data): Tag
    {
        $tag = new Tag($data);
        $tag->slug = Str::slug($data['name']);
        $tag->save();

        return $tag;
    }

    /**
     * Update an existing tag.
     */
    public function update(Tag $tag, array $data): Tag
    {
        $tag->update($data);
        $tag->slug = Str::slug($data['name']);
        $tag->save();

        return $tag;
    }

    /**
     * Delete a tag.
     */
    public function delete(Tag $tag): bool
    {
        // Check if tag has recipes
        if ($tag->recipes()->count() > 0) {
            return false;
        }

        return $tag->delete();
    }

    /**
     * Get all tags with recipe count.
     */
    public function getAllWithRecipeCount()
    {
        return Tag::withCount('recipes')
                 ->orderBy('name')
                 ->get();
    }

    /**
     * Get tags for select dropdown.
     */
    public function getForSelect()
    {
        return Tag::select('id', 'name')
                 ->orderBy('name')
                 ->get();
    }

    /**
     * Get tag with recipes.
     */
    public function getWithRecipes(Tag $tag, int $perPage = 12)
    {
        return $tag->load(['recipes' => function ($query) use ($perPage) {
            $query->with(['user', 'categories', 'tags'])
                  ->orderBy('created_at', 'desc')
                  ->paginate($perPage);
        }]);
    }

    /**
     * Search tags.
     */
    public function search(string $search)
    {
        return Tag::where('name', 'like', "%{$search}%")
                 ->withCount('recipes')
                 ->orderBy('name')
                 ->get();
    }

    /**
     * Get popular tags.
     */
    public function getPopular(int $limit = 20)
    {
        return Tag::withCount('recipes')
                 ->orderBy('recipes_count', 'desc')
                 ->limit($limit)
                 ->get();
    }

    /**
     * Get tag by slug.
     */
    public function findBySlug(string $slug): ?Tag
    {
        return Tag::where('slug', $slug)->first();
    }

    /**
     * Create or find tags by names.
     */
    public function createOrFindByNames(array $names): array
    {
        $tags = [];
        
        foreach ($names as $name) {
            $name = trim($name);
            if (empty($name)) continue;

            $tag = Tag::firstOrCreate(
                ['name' => $name],
                ['slug' => Str::slug($name)]
            );
            
            $tags[] = $tag;
        }

        return $tags;
    }

    /**
     * Get tags for autocomplete.
     */
    public function getForAutocomplete(string $search, int $limit = 10)
    {
        return Tag::where('name', 'like', "%{$search}%")
                 ->select('id', 'name')
                 ->orderBy('name')
                 ->limit($limit)
                 ->get();
    }
} 