<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Carbon\Carbon;

class ShoppingList extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'name',
        'description',
        'is_active',
        'is_shared',
        'completed_at'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'is_shared' => 'boolean',
        'completed_at' => 'datetime'
    ];

    /**
     * Get the user that owns the shopping list.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the items for the shopping list.
     */
    public function items(): HasMany
    {
        return $this->hasMany(ShoppingListItem::class)->orderBy('sort_order');
    }

    /**
     * Get unchecked items for the shopping list.
     */
    public function uncheckedItems(): HasMany
    {
        return $this->hasMany(ShoppingListItem::class)
            ->where('is_checked', false)
            ->orderBy('sort_order');
    }

    /**
     * Get checked items for the shopping list.
     */
    public function checkedItems(): HasMany
    {
        return $this->hasMany(ShoppingListItem::class)
            ->where('is_checked', true)
            ->orderBy('sort_order');
    }

    /**
     * Get items grouped by category.
     */
    public function itemsByCategory(): HasMany
    {
        return $this->hasMany(ShoppingListItem::class)
            ->where('is_checked', false)
            ->orderBy('category')
            ->orderBy('sort_order');
    }

    /**
     * Check if shopping list is completed.
     */
    public function isCompleted(): bool
    {
        return $this->completed_at !== null;
    }

    /**
     * Mark shopping list as completed.
     */
    public function markAsCompleted(): void
    {
        $this->update(['completed_at' => now()]);
    }

    /**
     * Mark shopping list as active.
     */
    public function markAsActive(): void
    {
        $this->update(['completed_at' => null]);
    }

    /**
     * Get total items count.
     */
    public function getTotalItemsCountAttribute(): int
    {
        return $this->items()->count();
    }

    /**
     * Get checked items count.
     */
    public function getCheckedItemsCountAttribute(): int
    {
        return $this->checkedItems()->count();
    }

    /**
     * Get unchecked items count.
     */
    public function getUncheckedItemsCountAttribute(): int
    {
        return $this->uncheckedItems()->count();
    }

    /**
     * Get completion percentage.
     */
    public function getCompletionPercentageAttribute(): float
    {
        $total = $this->total_items_count;
        if ($total === 0) {
            return 0;
        }

        return round(($this->checked_items_count / $total) * 100, 1);
    }

    /**
     * Scope a query to only include active shopping lists.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope a query to only include completed shopping lists.
     */
    public function scopeCompleted($query)
    {
        return $query->whereNotNull('completed_at');
    }

    /**
     * Scope a query to only include incomplete shopping lists.
     */
    public function scopeIncomplete($query)
    {
        return $query->whereNull('completed_at');
    }

    /**
     * Scope a query to only include shopping lists by a specific user.
     */
    public function scopeByUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }
}
