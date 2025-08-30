<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DietaryRule extends Model
{
    use HasFactory;

    protected $fillable = [
        'disease_condition_id',
        'name',
        'description',
        'food_categories',
        'ingredients',
        'cooking_restrictions',
        'portion_limits',
        'substitutions',
        'priority',
        'is_active'
    ];

    protected $casts = [
        'food_categories' => 'array',
        'ingredients' => 'array',
        'cooking_restrictions' => 'array',
        'portion_limits' => 'array',
        'substitutions' => 'array',
        'priority' => 'integer',
        'is_active' => 'boolean'
    ];

    /**
     * Get the disease condition that owns the dietary rule.
     */
    public function diseaseCondition()
    {
        return $this->belongsTo(DiseaseCondition::class);
    }

    /**
     * Scope a query to only include active dietary rules.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope a query to only include dietary rules by priority.
     */
    public function scopeByPriority($query, $priority)
    {
        return $query->where('priority', $priority);
    }

    /**
     * Scope a query to only include high priority dietary rules.
     */
    public function scopeHighPriority($query)
    {
        return $query->where('priority', '>=', 3);
    }

    /**
     * Check if a recipe violates this dietary rule.
     */
    public function checkRecipeViolation(Recipe $recipe)
    {
        $violations = [];

        // Check ingredients
        if ($this->ingredients) {
            foreach ($recipe->ingredients as $ingredient) {
                $ingredientName = strtolower($ingredient['name']);
                foreach ($this->ingredients as $restrictedIngredient) {
                    if (str_contains($ingredientName, strtolower($restrictedIngredient))) {
                        $violations[] = "Chứa nguyên liệu bị hạn chế: {$ingredient['name']}";
                    }
                }
            }
        }

        // Check cooking methods
        if ($this->cooking_restrictions) {
            foreach ($recipe->instructions as $instruction) {
                $instructionText = strtolower($instruction['instruction']);
                foreach ($this->cooking_restrictions as $restriction) {
                    if (str_contains($instructionText, strtolower($restriction))) {
                        $violations[] = "Phương pháp nấu bị hạn chế: {$restriction}";
                    }
                }
            }
        }

        return $violations;
    }

    /**
     * Get suggested substitutions for restricted ingredients.
     */
    public function getSubstitutions()
    {
        return $this->substitutions ?? [];
    }

    /**
     * Get portion limits.
     */
    public function getPortionLimits()
    {
        return $this->portion_limits ?? [];
    }

    /**
     * Get food categories affected by this rule.
     */
    public function getAffectedCategories()
    {
        return $this->food_categories ?? [];
    }
}
