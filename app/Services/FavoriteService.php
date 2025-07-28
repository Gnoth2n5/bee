<?php

namespace App\Services;

use App\Models\Favorite;
use App\Models\Recipe;
use App\Models\User;

class FavoriteService
{
    public function toggle(Recipe $recipe, User $user): array
    {
        $favorite = Favorite::where('user_id', $user->id)
            ->where('recipe_id', $recipe->id)
            ->first();

        if ($favorite) {
            $favorite->delete();
            $isFavorited = false;
            $message = 'Đã xóa khỏi danh sách yêu thích.';
        } else {
            Favorite::create([
                'user_id' => $user->id,
                'recipe_id' => $recipe->id,
            ]);
            $isFavorited = true;
            $message = 'Đã thêm vào danh sách yêu thích.';
        }

        return [
            'is_favorited' => $isFavorited,
            'message' => $message,
            'favorite_count' => $recipe->fresh()->favorites()->count(),
        ];
    }

    public function getUserFavorites(User $user)
    {
        return Favorite::where('user_id', $user->id)
            ->with('recipe')
            ->latest()
            ->paginate(12);
    }

    public function isFavorited(Recipe $recipe, User $user): bool
    {
        return Favorite::where('user_id', $user->id)
            ->where('recipe_id', $recipe->id)
            ->exists();
    }

    public function removeFavorite(Recipe $recipe, User $user): void
    {
        Favorite::where('user_id', $user->id)
            ->where('recipe_id', $recipe->id)
            ->delete();
    }
}