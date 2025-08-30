<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class DiseaseCondition extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'symptoms',
        'restricted_foods',
        'recommended_foods',
        'nutritional_requirements',
        'cooking_methods',
        'meal_timing',
        'severity_level',
        'is_active'
    ];

    protected $casts = [
        'symptoms' => 'array',
        'restricted_foods' => 'array',
        'recommended_foods' => 'array',
        'nutritional_requirements' => 'array',
        'cooking_methods' => 'array',
        'meal_timing' => 'array',
        'severity_level' => 'integer',
        'is_active' => 'boolean'
    ];

    /**
     * Boot the model.
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($diseaseCondition) {
            if (empty($diseaseCondition->slug)) {
                $diseaseCondition->slug = Str::slug($diseaseCondition->name);
            }
        });
    }

    /**
     * Get the dietary rules for the disease condition.
     */
    public function dietaryRules()
    {
        return $this->hasMany(DietaryRule::class);
    }

    /**
     * Get the recipes for the disease condition.
     */
    public function recipes()
    {
        return $this->belongsToMany(Recipe::class, 'recipe_disease_conditions')
            ->withPivot('suitability', 'notes', 'modifications')
            ->withTimestamps();
    }

    /**
     * Get suitable recipes for this disease condition.
     */
    public function suitableRecipes()
    {
        return $this->belongsToMany(Recipe::class, 'recipe_disease_conditions')
            ->wherePivot('suitability', 'suitable')
            ->withPivot('notes', 'modifications')
            ->withTimestamps();
    }

    /**
     * Get moderate recipes for this disease condition.
     */
    public function moderateRecipes()
    {
        return $this->belongsToMany(Recipe::class, 'recipe_disease_conditions')
            ->wherePivot('suitability', 'moderate')
            ->withPivot('notes', 'modifications')
            ->withTimestamps();
    }

    /**
     * Scope a query to only include active disease conditions.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope a query to only include disease conditions by severity level.
     */
    public function scopeBySeverity($query, $level)
    {
        return $query->where('severity_level', $level);
    }

    /**
     * Get recipes that are suitable for this disease condition.
     */
    public function getRecommendedRecipes($limit = 10)
    {
        return $this->suitableRecipes()
            ->where('status', 'approved')
            ->orderBy('average_rating', 'desc')
            ->limit($limit)
            ->get();
    }

    /**
     * Check if a recipe is suitable for this disease condition.
     */
    public function isRecipeSuitable(Recipe $recipe)
    {
        $pivot = $this->recipes()->where('recipe_id', $recipe->id)->first();
        return $pivot && $pivot->pivot->suitability === 'suitable';
    }

    /**
     * Get dietary restrictions as array.
     */
    public function getRestrictions()
    {
        return $this->restricted_foods ?? [];
    }

    /**
     * Get recommended foods as array.
     */
    public function getRecommendations()
    {
        return $this->recommended_foods ?? [];
    }
}
