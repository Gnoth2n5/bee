<?php

namespace App\Livewire;

use App\Models\Recipe;
use App\Models\Category;
use App\Models\User;
use App\Services\RecipeService;
use App\Services\FavoriteService;
use App\Services\GeminiService;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\WithFileUploads;
use Livewire\Attributes\Url;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On;
use Illuminate\Support\Facades\Auth;

#[Layout('layouts.app')]
class HomePage extends Component
{
    use WithPagination, WithFileUploads;

    #[Url(as: 'search')]
    public $search = '';

    #[Url(as: 'sort')]
    public $sort = 'latest';

    #[Url(as: 'difficulty')]
    public $difficulty = '';

    #[Url(as: 'cooking_time')]
    public $cookingTime = '';

    public $viewMode = 'grid';

    protected $queryString = [
        'search' => ['except' => ''],
        'sort' => ['except' => 'latest'],
        'difficulty' => ['except' => ''],
        'cookingTime' => ['except' => ''],
    ];

    public function mount()
    {
        // Initialize component
    }

    public function performSearch()
    {
        $this->resetPage();
    }

    #[On('search-performed')]
    public function handleSearchPerformed($search)
    {
        $this->search = $search;
        $this->resetPage();
    }

    public function clearFilters()
    {
        $this->reset(['search', 'difficulty', 'cookingTime', 'sort']);
        $this->resetPage();
    }

    public function toggleFavorite($recipeId)
    {
        if (!Auth::check()) {
            session()->flash('message', 'Vui lòng đăng nhập để thêm vào yêu thích.');
            return redirect()->route('login');
        }

        $recipe = Recipe::findOrFail($recipeId);
        $favoriteService = app(FavoriteService::class);
        $result = $favoriteService->toggle($recipe, Auth::user());

        session()->flash('success', $result['message']);
        $this->dispatch('favorite-toggled', recipeId: $recipeId);
        $this->dispatch('flash-message', message: $result['message'], type: 'success');

        // Refresh component để cập nhật UI
        $this->dispatch('$refresh');
    }

    public function confirmToggleFavorite($recipeId)
    {
        $recipe = Recipe::findOrFail($recipeId);
        $isFavorited = $recipe->isFavoritedBy(Auth::user());

        if ($isFavorited) {
            $this->removeFavorite($recipe->slug);
        } else {
            $this->toggleFavorite($recipeId);
        }
    }

    public function removeFavorite($recipeSlug)
    {
        \Log::info('removeFavorite called with slug: ' . $recipeSlug);

        if (!Auth::check()) {
            \Log::warning('User not authenticated');
            session()->flash('message', 'Vui lòng đăng nhập để thực hiện thao tác này.');
            return;
        }

        $recipe = Recipe::where('slug', $recipeSlug)->first();
        if ($recipe) {
            \Log::info('Recipe found: ' . $recipe->title);
            $favoriteService = app(FavoriteService::class);
            $favoriteService->removeFavorite($recipe, Auth::user());

            \Log::info('Favorite removed successfully');
            session()->flash('success', 'Đã xóa khỏi danh sách yêu thích.');
            $this->dispatch('favorite-toggled', recipeId: $recipe->id);
            $this->dispatch('flash-message', message: 'Đã xóa khỏi danh sách yêu thích.', type: 'success');

            // Refresh component để cập nhật UI
            $this->dispatch('$refresh');
        } else {
            \Log::warning('Recipe not found with slug: ' . $recipeSlug);
        }
    }

    public function getHasActiveFiltersProperty()
    {
        return !empty($this->difficulty) || !empty($this->cookingTime);
    }

    public function getStatsProperty()
    {
        return [
            'recipes' => Recipe::where('status', 'approved')->count(),
            'users' => User::count(),
            'categories' => Category::count(),
        ];
    }

    public function getFeaturedRecipeProperty()
    {
        return Recipe::where('status', 'approved')
            ->orderBy('view_count', 'desc')
            ->with(['user', 'categories'])
            ->withCount('ratings')
            ->withAvg('ratings', 'rating')
            ->first();
    }

    public function getCategoriesProperty()
    {
        return Category::query()
            ->with(['recipes'])
            ->withCount('recipes')
            ->limit(6)
            ->get();
    }

    public function getRecipesProperty()
    {
        $recipeService = app(RecipeService::class);

        $filters = [
            'search' => $this->search,
            'sort' => $this->sort,
            'difficulty' => $this->difficulty,
            'cooking_time' => $this->cookingTime,
        ];

        return $recipeService->getFilteredRecipes($filters, 12);
    }

    public function render()
    {
        return view('livewire.home-page', [
            'recipes' => $this->recipes,
            'categories' => $this->categories,
            'stats' => $this->stats,
            'featuredRecipe' => $this->featuredRecipe,
        ]);
    }
}
