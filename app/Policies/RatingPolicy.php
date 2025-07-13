<?php

namespace App\Policies;

use App\Models\Rating;
use App\Models\User;
use App\Models\Recipe;

class RatingPolicy
{
    /**
     * Determine whether the user can view any ratings.
     */
    public function viewAny(User $user): bool
    {
        return true; // Tất cả user đều có thể xem đánh giá
    }

    /**
     * Determine whether the user can create ratings.
     */
    public function create(User $user): bool
    {
        return true; // Tất cả user đã đăng nhập đều có thể đánh giá
    }

    /**
     * Determine whether the user can update the rating.
     */
    public function update(User $user, Rating $rating): bool
    {
        return $user->id === $rating->user_id;
    }

    /**
     * Determine whether the user can delete the rating.
     */
    public function delete(User $user, Rating $rating): bool
    {
        return $user->id === $rating->user_id;
    }
} 