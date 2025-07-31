<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WeatherConditionRule extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'temperature_min',
        'temperature_max',
        'humidity_min',
        'humidity_max',
        'suggested_categories',
        'suggested_tags',
        'suggestion_reason',
        'is_active',
        'priority',
        'seasonal_rules'
    ];

    protected $casts = [
        'temperature_min' => 'decimal:2',
        'temperature_max' => 'decimal:2',
        'humidity_min' => 'integer',
        'humidity_max' => 'integer',
        'suggested_categories' => 'array',
        'suggested_tags' => 'array',
        'seasonal_rules' => 'array',
        'is_active' => 'boolean',
        'priority' => 'integer'
    ];

    /**
     * Get the suggested categories.
     */
    public function categories()
    {
        return Category::whereIn('id', $this->suggested_categories ?? []);
    }

    /**
     * Get the suggested tags.
     */
    public function tags()
    {
        return Tag::whereIn('id', $this->suggested_tags ?? []);
    }

    /**
     * Scope a query to only include active rules.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope a query to only include rules for a specific temperature range.
     */
    public function scopeForTemperature($query, $temperature)
    {
        return $query->where(function ($q) use ($temperature) {
            $q->whereNull('temperature_min')
                ->orWhere('temperature_min', '<=', $temperature);
        })->where(function ($q) use ($temperature) {
            $q->whereNull('temperature_max')
                ->orWhere('temperature_max', '>=', $temperature);
        });
    }

    /**
     * Scope a query to only include rules for a specific humidity range.
     */
    public function scopeForHumidity($query, $humidity)
    {
        return $query->where(function ($q) use ($humidity) {
            $q->whereNull('humidity_min')
                ->orWhere('humidity_min', '<=', $humidity);
        })->where(function ($q) use ($humidity) {
            $q->whereNull('humidity_max')
                ->orWhere('humidity_max', '>=', $humidity);
        });
    }

    /**
     * Scope a query to order by priority.
     */
    public function scopeOrderByPriority($query)
    {
        return $query->orderBy('priority', 'desc');
    }

    /**
     * Check if rule matches current weather conditions.
     */
    public function matchesConditions($temperature, $humidity = null)
    {
        $matches = true;

        // Kiểm tra nhiệt độ
        if ($this->temperature_min !== null && $temperature < $this->temperature_min) {
            $matches = false;
        }
        if ($this->temperature_max !== null && $temperature > $this->temperature_max) {
            $matches = false;
        }

        // Kiểm tra độ ẩm nếu có
        if ($humidity !== null) {
            if ($this->humidity_min !== null && $humidity < $this->humidity_min) {
                $matches = false;
            }
            if ($this->humidity_max !== null && $humidity > $this->humidity_max) {
                $matches = false;
            }
        }

        return $matches;
    }

    /**
     * Get recipes that match this rule.
     */
    public function getMatchingRecipes($limit = 12)
    {
        $query = Recipe::with(['user', 'categories', 'tags', 'images'])
            ->where('status', 'approved')
            ->whereNotNull('published_at');

        // Filter by suggested categories
        if (!empty($this->suggested_categories)) {
            $query->whereHas('categories', function ($q) {
                $q->whereIn('id', $this->suggested_categories);
            });
        }

        // Filter by suggested tags
        if (!empty($this->suggested_tags)) {
            $query->whereHas('tags', function ($q) {
                $q->whereIn('id', $this->suggested_tags);
            });
        }

        return $query->orderBy('average_rating', 'desc')
            ->limit($limit)
            ->get();
    }

    /**
     * Get temperature range description.
     */
    public function getTemperatureRangeDescription()
    {
        if ($this->temperature_min === null && $this->temperature_max === null) {
            return 'Mọi nhiệt độ';
        }

        if ($this->temperature_min === null) {
            return "Dưới {$this->temperature_max}°C";
        }

        if ($this->temperature_max === null) {
            return "Trên {$this->temperature_min}°C";
        }

        return "{$this->temperature_min}°C - {$this->temperature_max}°C";
    }

    /**
     * Get humidity range description.
     */
    public function getHumidityRangeDescription()
    {
        if ($this->humidity_min === null && $this->humidity_max === null) {
            return 'Mọi độ ẩm';
        }

        if ($this->humidity_min === null) {
            return "Dưới {$this->humidity_max}%";
        }

        if ($this->humidity_max === null) {
            return "Trên {$this->humidity_min}%";
        }

        return "{$this->humidity_min}% - {$this->humidity_max}%";
    }
} 