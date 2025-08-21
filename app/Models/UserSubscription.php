<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserSubscription extends Model
{
    use HasFactory;

    protected $guarded = [];
    protected $table = 'user_subscriptions';

    protected $casts = [
        'start_date' => 'datetime',
        'end_date' => 'datetime',
        'amount' => 'decimal:2',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function isActive()
    {
        return $this->status === 'active' && now()->between($this->start_date, $this->end_date);
    }

    public function isVip()
    {
        return $this->subscription_type === 'vip' && $this->isActive();
    }

    public function isPremium()
    {
        return $this->subscription_type === 'premium' && $this->isActive();
    }

    public function getRemainingDays()
    {
        if (!$this->isActive()) {
            return 0;
        }

        return now()->diffInDays($this->end_date, false);
    }
}
