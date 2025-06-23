<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Collection extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'name',
        'description',
        'is_public',
        'cover_image',
        'recipe_count'
    ];

    protected $casts = [
        'is_public' => 'boolean',
        'recipe_count' => 'integer'
    ];

    /**
     * Get the user that owns the collection.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the recipes for the collection.
     */
    public function recipes()
    {
        return $this->belongsToMany(Recipe::class, 'collection_recipes');
    }

    /**
     * Scope a query to only include public collections.
     */
    public function scopePublic($query)
    {
        return $query->where('is_public', true);
    }

    /**
     * Scope a query to only include collections by a specific user.
     */
    public function scopeByUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Scope a query to order by recipe count.
     */
    public function scopeMostRecipes($query)
    {
        return $query->orderBy('recipe_count', 'desc');
    }

    /**
     * Scope a query to order by creation date.
     */
    public function scopeLatest($query)
    {
        return $query->orderBy('created_at', 'desc');
    }

    /**
     * Update recipe count.
     */
    public function updateRecipeCount()
    {
        $this->recipe_count = $this->recipes()->count();
        $this->save();
    }

    /**
     * Check if collection is public.
     */
    public function isPublic()
    {
        return $this->is_public;
    }

    /**
     * Check if collection is private.
     */
    public function isPrivate()
    {
        return !$this->is_public;
    }
} 