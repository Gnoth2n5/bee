<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VietnamCity extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'code',
        'region',
        'latitude',
        'longitude',
        'timezone',
        'is_active',
        'sort_order'
    ];

    protected $casts = [
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8',
        'is_active' => 'boolean',
        'sort_order' => 'integer'
    ];

    /**
     * Get the weather data for this city.
     */
    public function weatherData()
    {
        return $this->hasMany(WeatherData::class, 'city_code', 'code');
    }

    /**
     * Get the latest weather data for this city.
     */
    public function latestWeatherData()
    {
        return $this->hasOne(WeatherData::class, 'city_code', 'code')
            ->latest('last_updated');
    }

    /**
     * Get the weather recipe suggestions for this city.
     */
    public function weatherRecipeSuggestions()
    {
        return $this->hasMany(WeatherRecipeSuggestion::class, 'city_code', 'code');
    }

    /**
     * Scope a query to only include active cities.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope a query to only include cities in a specific region.
     */
    public function scopeInRegion($query, $region)
    {
        return $query->where('region', $region);
    }

    /**
     * Scope a query to order by sort order.
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order', 'asc')
            ->orderBy('name', 'asc');
    }

    /**
     * Get cities grouped by region.
     */
    public static function getGroupedByRegion()
    {
        return static::active()
            ->ordered()
            ->get()
            ->groupBy('region');
    }

    /**
     * Get city by code.
     */
    public static function findByCode($code)
    {
        return static::where('code', $code)->first();
    }

    /**
     * Get city by name.
     */
    public static function findByName($name)
    {
        return static::where('name', 'like', "%{$name}%")->first();
    }

    /**
     * Get nearby cities within a certain radius.
     */
    public function getNearbyCities($radiusKm = 50)
    {
        $lat = $this->latitude;
        $lng = $this->longitude;

        return static::where('id', '!=', $this->id)
            ->whereRaw("
                        (6371 * acos(cos(radians(?)) * cos(radians(latitude)) * 
                        cos(radians(longitude) - radians(?)) + 
                        sin(radians(?)) * sin(radians(latitude)))) <= ?
                    ", [$lat, $lng, $lat, $radiusKm])
            ->get();
    }
}