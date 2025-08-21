<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Restaurant extends Model
{
    use HasFactory;

    protected $fillable = [
        'place_id',
        'name',
        'formatted_address',
        'latitude',
        'longitude',
        'rating',
        'user_ratings_total',
        'formatted_phone_number',
        'website',
        'price_level',
        'types',
        'opening_hours',
        'photos',
        'reviews',
        'is_active',
        'last_updated'
    ];

    protected $casts = [
        'latitude' => 'float',
        'longitude' => 'float',
        'rating' => 'float',
        'user_ratings_total' => 'integer',
        'price_level' => 'integer',
        'types' => 'array',
        'opening_hours' => 'array',
        'photos' => 'array',
        'reviews' => 'array',
        'is_active' => 'boolean',
        'last_updated' => 'datetime'
    ];

    /**
     * Mối quan hệ với Recipe (nhiều-nhiều)
     */
    public function recipes(): BelongsToMany
    {
        return $this->belongsToMany(Recipe::class, 'recipe_restaurant');
    }

    /**
     * Mối quan hệ với User (favorites)
     */
    public function favoritedBy(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'restaurant_favorites');
    }

    /**
     * Mối quan hệ với Rating
     */
    public function ratings(): HasMany
    {
        return $this->hasMany(RestaurantRating::class);
    }

    /**
     * Scope để lọc nhà hàng theo khoảng cách
     */
    public function scopeNearby($query, $latitude, $longitude, $radius = 5)
    {
        $haversine = "(6371 * acos(cos(radians($latitude)) * cos(radians(latitude)) * cos(radians(longitude) - radians($longitude)) + sin(radians($latitude)) * sin(radians(latitude))))";

        return $query->selectRaw("*, $haversine AS distance")
            ->having('distance', '<=', $radius)
            ->orderBy('distance');
    }

    /**
     * Scope để lọc nhà hàng theo rating
     */
    public function scopeWithRating($query, $minRating = 0)
    {
        return $query->where('rating', '>=', $minRating);
    }

    /**
     * Scope để lọc nhà hàng theo loại
     */
    public function scopeByType($query, $type)
    {
        return $query->whereJsonContains('types', $type);
    }

    /**
     * Scope để lọc nhà hàng đang mở cửa
     */
    public function scopeOpenNow($query)
    {
        return $query->whereJsonPath('opening_hours.open_now', true);
    }

    /**
     * Lấy ảnh đầu tiên
     */
    public function getFirstPhotoAttribute()
    {
        return $this->photos[0] ?? null;
    }

    /**
     * Lấy đánh giá trung bình
     */
    public function getAverageRatingAttribute()
    {
        return $this->ratings()->avg('rating') ?? $this->rating ?? 0;
    }

    /**
     * Lấy số lượng đánh giá
     */
    public function getTotalRatingsAttribute()
    {
        return $this->ratings()->count() + $this->user_ratings_total;
    }

    /**
     * Kiểm tra xem nhà hàng có được yêu thích bởi user không
     */
    public function isFavoritedBy($userId)
    {
        return $this->favoritedBy()->where('user_id', $userId)->exists();
    }

    /**
     * Lấy khoảng cách từ một điểm
     */
    public function getDistanceFrom($latitude, $longitude)
    {
        $haversine = "(6371 * acos(cos(radians($latitude)) * cos(radians($this->latitude)) * cos(radians($this->longitude) - radians($longitude)) + sin(radians($latitude)) * sin(radians($this->latitude))))";

        return \DB::select("SELECT $haversine AS distance")[0]->distance;
    }

    /**
     * Format giá tiền
     */
    public function getPriceLevelTextAttribute()
    {
        $levels = [
            0 => 'Miễn phí',
            1 => 'Rẻ',
            2 => 'Trung bình',
            3 => 'Đắt',
            4 => 'Rất đắt'
        ];

        return $levels[$this->price_level] ?? 'Không xác định';
    }

    /**
     * Lấy thông tin giờ mở cửa
     */
    public function getOpeningHoursTextAttribute()
    {
        if (!$this->opening_hours || !isset($this->opening_hours['weekday_text'])) {
            return 'Không có thông tin';
        }

        return $this->opening_hours['weekday_text'];
    }

    /**
     * Lấy địa chỉ ngắn gọn
     */
    public function getShortAddressAttribute()
    {
        $address = $this->formatted_address;
        $parts = explode(',', $address);

        if (count($parts) >= 2) {
            return trim($parts[0]) . ', ' . trim($parts[1]);
        }

        return $address;
    }
}
