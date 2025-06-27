<?php

namespace App\Policies;

use App\Models\Collection;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class CollectionPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return true; // Anyone can view collections list
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Collection $collection): bool
    {
        // Public collections can be viewed by anyone
        if ($collection->is_public) {
            return true;
        }

        // Private collections can only be viewed by owner
        return $user->id === $collection->user_id;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->hasPermissionTo('collection.create');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Collection $collection): bool
    {
        return $user->id === $collection->user_id && 
               $user->hasPermissionTo('collection.edit');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Collection $collection): bool
    {
        return $user->id === $collection->user_id && 
               $user->hasPermissionTo('collection.delete');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Collection $collection): bool
    {
        return $user->id === $collection->user_id && 
               $user->hasPermissionTo('collection.edit');
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Collection $collection): bool
    {
        return $user->id === $collection->user_id && 
               $user->hasPermissionTo('collection.delete');
    }

    /**
     * Determine whether the user can add recipes to the collection.
     */
    public function addRecipe(User $user, Collection $collection): bool
    {
        return $user->id === $collection->user_id && 
               $user->hasPermissionTo('collection.edit');
    }

    /**
     * Determine whether the user can remove recipes from the collection.
     */
    public function removeRecipe(User $user, Collection $collection): bool
    {
        return $user->id === $collection->user_id && 
               $user->hasPermissionTo('collection.edit');
    }
} 