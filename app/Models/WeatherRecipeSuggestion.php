<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WeatherRecipeSuggestion extends Model
{
    use HasFactory;

    protected $fillable = [
        'city_code',
        'weather_condition',
        'temperature_min',
        'temperature_max',
        'humidity_min',
        'humidity_max',
        'recipe_ids',
        'categories',
        'suggestion_reason',
        'is_active',
        'priority',
        'last_generated'
    ];

    protected $casts = [
        'temperature_min' => 'decimal:2',
        'temperature_max' => 'decimal:2',
        'humidity_min' => 'integer',
        'humidity_max' => 'integer',
        'recipe_ids' => 'array',
        'categories' => 'array',
        'is_active' => 'boolean',
        'priority' => 'integer',
        'last_generated' => 'datetime'
    ];

    /**
     * Get the city that owns the suggestion.
     */
    public function city()
    {
        return $this->belongsTo(VietnamCity::class, 'city_code', 'code');
    }

    /**
     * Get the suggested recipes.
     */
    public function recipes()
    {
        return Recipe::whereIn('id', $this->recipe_ids ?? []);
    }

    /**
     * Get the suggested categories.
     */
    public function categoryModels()
    {
        return Category::whereIn('id', $this->categories ?? []);
    }

    /**
     * Scope a query to only include active suggestions.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope a query to only include suggestions for a specific city.
     */
    public function scopeForCity($query, $cityCode)
    {
        return $query->where('city_code', $cityCode);
    }

    /**
     * Scope a query to only include suggestions for a specific weather condition.
     */
    public function scopeForWeather($query, $weatherCondition)
    {
        return $query->where('weather_condition', $weatherCondition);
    }

    /**
     * Scope a query to only include suggestions for a specific temperature range.
     */
    public function scopeForTemperature($query, $temperature)
    {
        return $query->where('temperature_min', '<=', $temperature)
            ->where('temperature_max', '>=', $temperature);
    }

    /**
     * Scope a query to only include suggestions for a specific humidity range.
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
        return $query->orderBy('priority', 'desc')
            ->orderBy('last_generated', 'desc');
    }

    /**
     * Check if suggestion matches current weather conditions.
     */
    public function matchesWeatherConditions($temperature, $humidity = null)
    {
        $matches = $this->temperature_min <= $temperature && $this->temperature_max >= $temperature;

        if ($humidity !== null && $this->humidity_min !== null && $this->humidity_max !== null) {
            $matches = $matches && $this->humidity_min <= $humidity && $this->humidity_max >= $humidity;
        }

        return $matches;
    }
}