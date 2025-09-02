<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserProfile extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'phone',
        'address',
        'city',
        'country',
        'timezone',
        'language',
        'dietary_preferences',
        'allergies',
        'health_conditions',
        'cooking_experience',
        'isVipAccount',
        'vip_expires_at',
        'vip_plan'
    ];

    protected $casts = [
        'dietary_preferences' => 'array',
        'allergies' => 'array',
        'health_conditions' => 'array',
        'isVipAccount' => 'boolean',
        'vip_expires_at' => 'datetime'
    ];

    /**
     * Get the user that owns the profile.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
