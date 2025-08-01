<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Storage;
use Spatie\Permission\Traits\HasRoles;
use Spatie\Permission\Traits\HasPermissions;
use App\Models\UserProfile;
use App\Models\Recipe;
use App\Models\Rating;
use App\Models\Favorite;
use App\Models\Collection;
use App\Models\Post;
use Illuminate\Support\Facades\Auth;

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasRoles, HasPermissions;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'province',
        'password',
        'avatar',
        'bio',
        'preferences',
        'status',
        'email_verified_at',
        'last_login_at',
        'login_count',
        'google_id',
        'google_token',
        'google_refresh_token'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'preferences' => 'array',
            'last_login_at' => 'datetime'
        ];
    }

    /**
     * Get the user's profile.
     */
    public function profile()
    {
        return $this->hasOne(UserProfile::class);
    }

    /**
     * Get the recipes for the user.
     */
    public function recipes()
    {
        return $this->hasMany(Recipe::class);
    }

    /**
     * Get the ratings for the user.
     */
    public function ratings()
    {
        return $this->hasMany(Rating::class);
    }

    /**
     * Get the favorites for the user.
     */
    public function favorites()
    {
        return $this->hasMany(Favorite::class);
    }

    /**
     * Get the collections for the user.
     */
    public function collections()
    {
        return $this->hasMany(Collection::class);
    }

    /**
     * Get the posts for the user.
     */
    public function posts()
    {
        return $this->hasMany(Post::class);
    }

    /**
     * Get the user's avatar URL, prioritizing Google avatar if available.
     */
    public function getAvatarUrl()
    {
        // Nếu user có google_id và avatar là URL (bắt đầu bằng http/https), ưu tiên sử dụng
        if ($this->google_id && $this->avatar && filter_var($this->avatar, FILTER_VALIDATE_URL)) {
            return $this->avatar;
        }
        
        // Nếu có avatar local, sử dụng Storage URL
        if ($this->avatar && !filter_var($this->avatar, FILTER_VALIDATE_URL)) {
            return Storage::url($this->avatar);
        }
        
        // Nếu không có avatar, trả về null
        return null;
    }

    /**
     * Check if user has avatar (either Google or local).
     */
    public function hasAvatar()
    {
        return !empty($this->avatar);
    }

    /**
     * Check if user has Google avatar (URL).
     */
    public function hasGoogleAvatar()
    {
        return $this->google_id && $this->avatar && filter_var($this->avatar, FILTER_VALIDATE_URL);
    }

    /**
     * Check if user has local avatar (file).
     */
    public function hasLocalAvatar()
    {
        return $this->avatar && !filter_var($this->avatar, FILTER_VALIDATE_URL);
    }

  

}