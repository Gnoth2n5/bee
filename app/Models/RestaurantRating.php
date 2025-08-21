<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RestaurantRating extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'restaurant_id',
        'rating',
        'comment',
        'is_verified'
    ];

    protected $casts = [
        'rating' => 'integer',
        'is_verified' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    /**
     * Mối quan hệ với User
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Mối quan hệ với Restaurant
     */
    public function restaurant(): BelongsTo
    {
        return $this->belongsTo(Restaurant::class);
    }

    /**
     * Scope để lọc đánh giá đã xác minh
     */
    public function scopeVerified($query)
    {
        return $query->where('is_verified', true);
    }

    /**
     * Scope để lọc đánh giá theo rating
     */
    public function scopeWithRating($query, $rating)
    {
        return $query->where('rating', $rating);
    }

    /**
     * Scope để lọc đánh giá theo user
     */
    public function scopeByUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Scope để lọc đánh giá theo nhà hàng
     */
    public function scopeByRestaurant($query, $restaurantId)
    {
        return $query->where('restaurant_id', $restaurantId);
    }

    /**
     * Lấy đánh giá trung bình của nhà hàng
     */
    public static function getAverageRatingForRestaurant($restaurantId)
    {
        return self::where('restaurant_id', $restaurantId)
            ->where('is_verified', true)
            ->avg('rating');
    }

    /**
     * Lấy số lượng đánh giá của nhà hàng
     */
    public static function getRatingCountForRestaurant($restaurantId)
    {
        return self::where('restaurant_id', $restaurantId)
            ->where('is_verified', true)
            ->count();
    }

    /**
     * Kiểm tra xem user đã đánh giá nhà hàng chưa
     */
    public static function hasUserRated($userId, $restaurantId)
    {
        return self::where('user_id', $userId)
            ->where('restaurant_id', $restaurantId)
            ->exists();
    }

    /**
     * Lấy đánh giá của user cho nhà hàng
     */
    public static function getUserRating($userId, $restaurantId)
    {
        return self::where('user_id', $userId)
            ->where('restaurant_id', $restaurantId)
            ->first();
    }

    /**
     * Format rating thành sao
     */
    public function getRatingStarsAttribute()
    {
        $stars = '';
        for ($i = 1; $i <= 5; $i++) {
            if ($i <= $this->rating) {
                $stars .= '★';
            } else {
                $stars .= '☆';
            }
        }
        return $stars;
    }

    /**
     * Lấy text mô tả rating
     */
    public function getRatingTextAttribute()
    {
        $texts = [
            1 => 'Rất tệ',
            2 => 'Tệ',
            3 => 'Bình thường',
            4 => 'Tốt',
            5 => 'Rất tốt'
        ];

        return $texts[$this->rating] ?? 'Không xác định';
    }
}
