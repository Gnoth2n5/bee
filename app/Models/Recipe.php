<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Recipe extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'title',
        'slug',
        'description',
        'summary',
        'cooking_time',
        'preparation_time',
        'total_time',
        'difficulty',
        'servings',
        'calories_per_serving',
        'ingredients',
        'instructions',
        'tips',
        'notes',
        'featured_image',
        'video_url',
        'status',
        'approved_by',
        'approved_at',
        'rejection_reason',
        'auto_approve_at',
        'meta_title',
        'meta_description',
        'meta_keywords',
        'view_count',
        'favorite_count',
        'rating_count',
        'average_rating',
        'published_at'
    ];

    protected $casts = [
        'ingredients' => 'array',
        'instructions' => 'array',
        'approved_at' => 'datetime',
        'auto_approve_at' => 'datetime',
        'published_at' => 'datetime',
        'cooking_time' => 'integer',
        'preparation_time' => 'integer',
        'total_time' => 'integer',
        'servings' => 'integer',
        'calories_per_serving' => 'integer',
        'view_count' => 'integer',
        'favorite_count' => 'integer',
        'rating_count' => 'integer',
        'average_rating' => 'decimal:2'
    ];

    /**
     * Boot the model.
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($recipe) {
            if (empty($recipe->slug)) {
                $recipe->slug = Str::slug($recipe->title);
            }

            if (empty($recipe->total_time) && ($recipe->cooking_time || $recipe->preparation_time)) {
                $recipe->total_time = ($recipe->cooking_time ?? 0) + ($recipe->preparation_time ?? 0);
            }
        });

        static::updating(function ($recipe) {
            if ($recipe->isDirty('cooking_time') || $recipe->isDirty('preparation_time')) {
                $recipe->total_time = ($recipe->cooking_time ?? 0) + ($recipe->preparation_time ?? 0);
            }
        });
    }

    /**
     * Get the user that owns the recipe.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the user who approved the recipe.
     */
    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    /**
     * Get the categories for the recipe.
     */
    public function categories()
    {
        return $this->belongsToMany(Category::class, 'recipe_categories');
    }

    /**
     * Get the tags for the recipe.
     */
    public function tags()
    {
        return $this->belongsToMany(Tag::class, 'recipe_tags');
    }

    /**
     * Get the images for the recipe.
     */
    public function images()
    {
        return $this->hasMany(RecipeImage::class);
    }

    /**
     * Get the ratings for the recipe.
     */
    public function ratings()
    {
        return $this->hasMany(Rating::class);
    }

    /**
     * Get the favorites for the recipe.
     */
    public function favorites()
    {
        return $this->hasMany(Favorite::class);
    }

    /**
     * Get the collections that include this recipe.
     */
    public function collections()
    {
        return $this->belongsToMany(Collection::class, 'collection_recipes');
    }

    /**
     * Scope a query to only include published recipes.
     */
    public function scopePublished($query)
    {
        return $query->where('status', 'approved')
            ->whereNotNull('published_at');
    }

    /**
     * Scope a query to only include recipes by a specific user.
     */
    public function scopeByUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Scope a query to only include recipes in a specific category.
     */
    public function scopeInCategory($query, $categoryId)
    {
        return $query->whereHas('categories', function ($q) use ($categoryId) {
            $q->where('categories.id', $categoryId);
        });
    }

    /**
     * Scope a query to only include recipes with a specific difficulty.
     */
    public function scopeDifficulty($query, $difficulty)
    {
        return $query->where('difficulty', $difficulty);
    }

    /**
     * Scope a query to only include recipes with cooking time within range.
     */
    public function scopeCookingTime($query, $minMinutes = null, $maxMinutes = null)
    {
        if ($minMinutes) {
            $query->where('cooking_time', '>=', $minMinutes);
        }
        if ($maxMinutes) {
            $query->where('cooking_time', '<=', $maxMinutes);
        }
        return $query;
    }

    /**
     * Scope a query to only include recipes with minimum rating.
     */
    public function scopeMinRating($query, $rating)
    {
        return $query->where('average_rating', '>=', $rating);
    }

    /**
     * Scope a query to order by popularity.
     */
    public function scopePopular($query)
    {
        return $query->orderBy('view_count', 'desc')
            ->orderBy('favorite_count', 'desc');
    }

    /**
     * Scope a query to order by rating.
     */
    public function scopeTopRated($query)
    {
        return $query->orderBy('average_rating', 'desc')
            ->orderBy('rating_count', 'desc');
    }

    /**
     * Scope a query to order by newest.
     */
    public function scopeLatest($query)
    {
        return $query->orderBy('created_at', 'desc');
    }

    /**
     * Check if recipe is published.
     */
    public function isPublished()
    {
        return $this->status === 'approved' && $this->published_at !== null;
    }

    /**
     * Check if recipe is draft.
     */
    public function isDraft()
    {
        return $this->status === 'draft';
    }

    /**
     * Check if recipe is pending approval.
     */
    public function isPending()
    {
        return $this->status === 'pending';
    }

    /**
     * Check if recipe is rejected.
     */
    public function isRejected()
    {
        return $this->status === 'rejected';
    }

    /**
     * Check if recipe is favorited by user.
     */
    public function isFavoritedBy(?User $user): bool
    {
        if (!$user) {
            return false;
        }
        return $this->favorites()->where('user_id', $user->id)->exists();
    }

    /**
     * Check if recipe is in collection.
     */
    public function isInCollection(Collection $collection): bool
    {
        return $this->collections()->where('collection_id', $collection->id)->exists();
    }

    /**
     * Get collections that contain this recipe for a specific user.
     */
    public function getUserCollections(User $user)
    {
        return $this->collections()->where('user_id', $user->id)->get();
    }

    /**
     * Get the primary image.
     */
    public function getPrimaryImageAttribute()
    {
        return $this->images()->where('is_primary', true)->first() ?? $this->images()->first();
    }

    /**
     * Increment view count.
     */
    public function incrementViewCount()
    {
        $this->increment('view_count');
    }

    /**
     * Update rating statistics.
     */
    public function updateRatingStats()
    {
        $ratings = $this->ratings();
        $this->rating_count = $ratings->count();
        $this->average_rating = $ratings->avg('rating') ?? 0;
        $this->save();
    }

    /**
     * Update favorite count.
     */
    public function updateFavoriteCount()
    {
        $this->favorite_count = $this->favorites()->count();
        $this->save();
    }

    /**
     * Get the route key for the model.
     */
    public function getRouteKeyName()
    {
        return 'slug';
    }
}