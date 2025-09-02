<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tag extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'usage_count'
    ];

    protected $casts = [
        'usage_count' => 'integer'
    ];

    /**
     * Get the recipes for the tag.
     */
    public function recipes()
    {
        return $this->belongsToMany(Recipe::class, 'recipe_tags');
    }

    /**
     * Scope a query to only include popular tags.
     */
    public function scopePopular($query, $limit = 10)
    {
        return $query->orderBy('usage_count', 'desc')->limit($limit);
    }

    /**
     * Increment usage count.
     */
    public function incrementUsage()
    {
        $this->increment('usage_count');
    }

    /**
     * Decrement usage count.
     */
    public function decrementUsage()
    {
        $this->decrement('usage_count');
    }
}
