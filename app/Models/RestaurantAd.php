<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RestaurantAd extends Model
{
    use HasFactory;

    protected $guarded = [];
    protected $table = 'restaurant_ads';

    protected $casts = [
        'start_date' => 'datetime',
        'end_date' => 'datetime',
        'amount' => 'decimal:2',
    ];

    public function restaurant()
    {
        return $this->belongsTo(Restaurant::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function isActive()
    {
        return $this->status === 'active' && now()->between($this->start_date, $this->end_date);
    }

    public function incrementViews()
    {
        $this->increment('views');
    }

    public function incrementClicks()
    {
        $this->increment('clicks');
    }

    public function getCTR()
    {
        if ($this->views === 0) {
            return 0;
        }

        return round(($this->clicks / $this->views) * 100, 2);
    }
}
