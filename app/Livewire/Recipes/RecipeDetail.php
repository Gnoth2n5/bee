<?php

namespace App\Livewire\Recipes;

use App\Models\Recipe;
use App\Services\FavoriteService;
use Livewire\Component;
use Livewire\Attributes\Layout;
use Illuminate\Support\Facades\Auth;

#[Layout('layouts.app')]
class RecipeDetail extends Component
{
    public Recipe $recipe;

    public function mount(Recipe $recipe)
    {
        $this->recipe = $recipe->load(['user.profile', 'categories', 'tags', 'images', 'ratings.user', 'favorites']);
        $this->recipe->incrementViewCount();
    }

    public function toggleFavorite()
    {
        if (!Auth::check()) {
            session()->flash('message', 'Vui lòng đăng nhập để thêm vào yêu thích.');
            return redirect()->route('login');
        }

        $favoriteService = app(FavoriteService::class);
        $result = $favoriteService->toggle($this->recipe, Auth::user());

        $this->recipe->refresh();

        session()->flash('success', $result['message']);
        $this->dispatch('flash-message', message: $result['message'], type: 'success');

        // Refresh component để cập nhật UI
        $this->dispatch('$refresh');
    }

    public function confirmToggleFavorite()
    {
        if (!Auth::check()) {
            session()->flash('message', 'Vui lòng đăng nhập để thêm vào yêu thích.');
            return redirect()->route('login');
        }

        $isFavorited = $this->recipe->isFavoritedBy(Auth::user());

        if ($isFavorited) {
            $this->dispatch('confirm-remove-favorite', recipeSlug: $this->recipe->slug, componentId: $this->getId(), action: 'toggle');
        } else {
            $this->toggleFavorite();
        }
    }

    public function removeFavorite($recipeSlug)
    {
        $this->toggleFavorite();
    }

    public function render()
    {
        return view('livewire.recipes.recipe-detail', [
            'recipe' => $this->recipe,
        ]);
    }
}