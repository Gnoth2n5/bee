<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WeatherData extends Model
{
    use HasFactory;

    protected $fillable = [
        'city_name',
        'city_code',
        'temperature',
        'feels_like',
        'humidity',
        'wind_speed',
        'weather_condition',
        'weather_description',
        'weather_icon',
        'pressure',
        'visibility',
        'uv_index',
        'forecast_data',
        'last_updated'
    ];

    protected $casts = [
        'temperature' => 'decimal:2',
        'feels_like' => 'decimal:2',
        'humidity' => 'integer',
        'wind_speed' => 'decimal:2',
        'pressure' => 'integer',
        'visibility' => 'integer',
        'uv_index' => 'decimal:1',
        'forecast_data' => 'array',
        'last_updated' => 'datetime'
    ];

    /**
     * Get the city that owns the weather data.
     */
    public function city()
    {
        return $this->belongsTo(VietnamCity::class, 'city_code', 'code');
    }

    /**
     * Scope a query to only include recent weather data.
     */
    public function scopeRecent($query, $hours = 6)
    {
        return $query->where('last_updated', '>=', now()->subHours($hours));
    }

    /**
     * Scope a query to only include weather data for a specific city.
     */
    public function scopeForCity($query, $cityCode)
    {
        return $query->where('city_code', $cityCode);
    }

    /**
     * Get weather condition category.
     */
    public function getWeatherCategoryAttribute()
    {
        $condition = strtolower($this->weather_condition);

        if (str_contains($condition, 'rain') || str_contains($condition, 'drizzle')) {
            return 'rainy';
        }

        if (str_contains($condition, 'snow')) {
            return 'snowy';
        }

        if (str_contains($condition, 'cloud')) {
            return 'cloudy';
        }

        if (str_contains($condition, 'clear') || str_contains($condition, 'sun')) {
            return 'sunny';
        }

        if (str_contains($condition, 'storm') || str_contains($condition, 'thunder')) {
            return 'stormy';
        }

        return 'normal';
    }

    /**
     * Get temperature category.
     */
    public function getTemperatureCategoryAttribute()
    {
        if ($this->temperature < 15) {
            return 'cold';
        } elseif ($this->temperature > 30) {
            return 'hot';
        } else {
            return 'moderate';
        }
    }

    /**
     * Get humidity category.
     */
    public function getHumidityCategoryAttribute()
    {
        if ($this->humidity < 40) {
            return 'dry';
        } elseif ($this->humidity > 70) {
            return 'humid';
        } else {
            return 'normal';
        }
    }
}