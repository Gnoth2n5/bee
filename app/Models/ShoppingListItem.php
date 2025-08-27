<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ShoppingListItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'shopping_list_id',
        'ingredient_name',
        'amount',
        'unit',
        'notes',
        'is_checked',
        'sort_order',
        'category',
        'recipe_id',
        'weekly_meal_plan_id'
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'is_checked' => 'boolean',
        'sort_order' => 'integer'
    ];

    /**
     * Get the shopping list that owns the item.
     */
    public function shoppingList(): BelongsTo
    {
        return $this->belongsTo(ShoppingList::class);
    }

    /**
     * Get the recipe that this item is from.
     */
    public function recipe(): BelongsTo
    {
        return $this->belongsTo(Recipe::class);
    }

    /**
     * Get the weekly meal plan that this item is from.
     */
    public function weeklyMealPlan(): BelongsTo
    {
        return $this->belongsTo(WeeklyMealPlan::class);
    }

    /**
     * Get the user that owns this item.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'id', 'shopping_lists');
    }

    /**
     * Toggle the checked status of the item.
     */
    public function toggleChecked(): void
    {
        $this->update(['is_checked' => !$this->is_checked]);
    }

    /**
     * Mark item as checked.
     */
    public function markAsChecked(): void
    {
        $this->update(['is_checked' => true]);
    }

    /**
     * Mark item as unchecked.
     */
    public function markAsUnchecked(): void
    {
        $this->update(['is_checked' => false]);
    }

    /**
     * Get formatted amount with unit.
     */
    public function getFormattedAmountAttribute(): string
    {
        if ($this->amount === null) {
            return '';
        }

        $formatted = number_format($this->amount, 2);
        if ($this->unit) {
            $formatted .= ' ' . $this->unit;
        }

        return $formatted;
    }

    /**
     * Get display name with amount.
     */
    public function getDisplayNameAttribute(): string
    {
        $name = $this->ingredient_name;

        if ($this->formatted_amount) {
            $name = $this->formatted_amount . ' ' . $name;
        }

        return $name;
    }

    /**
     * Scope a query to only include checked items.
     */
    public function scopeChecked($query)
    {
        return $query->where('is_checked', true);
    }

    /**
     * Scope a query to only include unchecked items.
     */
    public function scopeUnchecked($query)
    {
        return $query->where('is_checked', false);
    }

    /**
     * Scope a query to only include items by category.
     */
    public function scopeByCategory($query, $category)
    {
        return $query->where('category', $category);
    }

    /**
     * Scope a query to only include items from a specific recipe.
     */
    public function scopeByRecipe($query, $recipeId)
    {
        return $query->where('recipe_id', $recipeId);
    }

    /**
     * Scope a query to only include items from a specific meal plan.
     */
    public function scopeByMealPlan($query, $mealPlanId)
    {
        return $query->where('weekly_meal_plan_id', $mealPlanId);
    }
}
