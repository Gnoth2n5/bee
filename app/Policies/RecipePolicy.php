<?php

namespace App\Policies;

use App\Models\Recipe;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class RecipePolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return true; // Anyone can view recipes
    }

        /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Recipe $recipe): bool
    {
        // Published recipes can be viewed by anyone
        if ($recipe->status === 'approved' && $recipe->published_at) {
            return true;
        }

        // Draft/pending recipes can only be viewed by owner or admins
        return $user->id === $recipe->user_id || 
               $user->hasRole(['manager', 'admin']);
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Recipe $recipe): bool
    {
        // Owner can update their own recipes
        if ($user->id === $recipe->user_id) {
            return $user->hasRole(['user', 'manager', 'admin']);
        }

        // Admins/managers can update any recipe
        return $user->hasRole(['manager', 'admin']);
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Recipe $recipe): bool
    {
        // Owner can delete their own recipes
        if ($user->id === $recipe->user_id) {
            return $user->hasRole(['user', 'manager', 'admin']);
        }

        // Admins can delete any recipe
        return $user->hasRole(['manager', 'admin']);
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Recipe $recipe): bool
    {
        return $user->hasRole(['manager', 'admin']);
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Recipe $recipe): bool
    {
        return $user->hasRole(['manager', 'admin']);
    }

    /**
     * Determine whether the user can approve the model.
     */
    public function approve(User $user, Recipe $recipe): bool
    {
        return $user->hasRole(['manager', 'admin']);
    }

    /**
     * Determine whether the user can reject the model.
     */
    public function reject(User $user, Recipe $recipe): bool
    {
        return $user->hasRole(['manager', 'admin']);
    }
}
