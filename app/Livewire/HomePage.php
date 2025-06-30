<?php

namespace App\Livewire;

use App\Models\Recipe;
use App\Models\Category;
use App\Models\User;
use App\Services\RecipeService;
use App\Services\FavoriteService;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Url;
use Livewire\Attributes\Layout;
use Illuminate\Support\Facades\Auth;

#[Layout('layouts.app')]
class HomePage extends Component
{
    use WithPagination;

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
        $favoriteService->toggle($recipe, Auth::user());
        
        $this->dispatch('favorite-toggled', recipeId: $recipeId);
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

    public function getCategoriesProperty()
    {
        return Category::where('parent_id', null)
                      ->with(['children', 'recipes'])
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
        ]);
    }
} 