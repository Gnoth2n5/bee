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

    public function removeFavorite($recipeSlug)
    {
        \Log::info('FavoritesPage::removeFavorite called with slug: ' . $recipeSlug);

        $user = \Illuminate\Support\Facades\Auth::user();
        $recipe = \App\Models\Recipe::where('slug', $recipeSlug)->first();

        if ($recipe) {
            \Log::info('Recipe found: ' . $recipe->title);
            app(FavoriteService::class)->removeFavorite($recipe, $user);
            session()->flash('success', 'Đã xóa công thức khỏi danh sách yêu thích!');
            $this->dispatch('flash-message', message: 'Đã xóa công thức khỏi danh sách yêu thích!', type: 'success');

            // Refresh component để cập nhật UI
            $this->dispatch('$refresh');
        } else {
            \Log::warning('Recipe not found with slug: ' . $recipeSlug);
        }
    }

    public function confirmRemoveFavorite($recipeSlug)
    {
        \Log::info('FavoritesPage::confirmRemoveFavorite called with slug: ' . $recipeSlug);

        $user = \Illuminate\Support\Facades\Auth::user();
        $recipe = \App\Models\Recipe::where('slug', $recipeSlug)->first();

        if ($recipe) {
            \Log::info('Recipe found: ' . $recipe->title);
            app(FavoriteService::class)->removeFavorite($recipe, $user);
            session()->flash('success', 'Đã xóa công thức khỏi danh sách yêu thích!');
            $this->dispatch('flash-message', message: 'Đã xóa công thức khỏi danh sách yêu thích!', type: 'success');

            // Refresh component để cập nhật UI
            $this->dispatch('$refresh');
        } else {
            \Log::warning('Recipe not found with slug: ' . $recipeSlug);
        }
    }



    #[On('remove-favorite')]
    public function handleRemoveFavorite($recipeSlug)
    {
        \Log::info('FavoritesPage::handleRemoveFavorite called with slug: ' . $recipeSlug);
        $this->removeFavorite($recipeSlug);
    }

    public function getFavoritesProperty()
    {
        return Favorite::where('user_id', \Illuminate\Support\Facades\Auth::id())
            ->with(['recipe.categories', 'recipe.images', 'recipe.user.profile', 'recipe.favorites'])
            ->latest()
            ->paginate(12);
    }

    public function render()
    {
        return view('livewire.favorites.favorites-page');
    }
}