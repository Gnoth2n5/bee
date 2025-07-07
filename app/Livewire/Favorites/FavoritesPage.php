<?php

namespace App\Livewire\Favorites;

use App\Models\Favorite;
use App\Services\FavoriteService;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;

#[Layout('layouts.app')]
class FavoritesPage extends Component
{
    use WithPagination;

    public function mount()
    {
        // Initialize component
    }

    public function confirmRemoveFavorite($recipeSlug)
    {
        $this->dispatch('confirm-remove-favorite', recipeSlug: $recipeSlug, componentId: $this->getId());
    }

    public function removeFavorite($recipeSlug)
    {
        $user = \Illuminate\Support\Facades\Auth::user();
        $recipe = \App\Models\Recipe::where('slug', $recipeSlug)->first();
        
        if ($recipe) {
            app(FavoriteService::class)->removeFavorite($recipe, $user);
            session()->flash('success', 'Đã xóa công thức khỏi danh sách yêu thích!');
            $this->dispatch('flash-message', message: 'Đã xóa công thức khỏi danh sách yêu thích!', type: 'success');
        }
    }

    public function getFavoritesProperty()
    {
        return Favorite::where('user_id', \Illuminate\Support\Facades\Auth::id())
            ->with(['recipe.categories', 'recipe.images', 'recipe.user.profile'])
            ->latest()
            ->paginate(12);
    }

    public function render()
    {
        return view('livewire.favorites.favorites-page');
    }
} 