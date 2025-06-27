<?php

namespace App\Livewire\Recipes;

use App\Models\Recipe;
use App\Models\Category;
use App\Models\Tag;
use App\Services\RecipeService;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Url;

class RecipeList extends Component
{
    use WithPagination;

    #[Url(as: 'category')]
    public $category = '';

    #[Url(as: 'difficulty')]
    public $difficulty = '';

    #[Url(as: 'cooking_time')]
    public $cookingTime = '';

    #[Url(as: 'search')]
    public $search = '';

    #[Url(as: 'sort')]
    public $sort = 'latest';

    public $perPage = 12;

    protected $queryString = [
        'category' => ['except' => ''],
        'difficulty' => ['except' => ''],
        'cookingTime' => ['except' => ''],
        'search' => ['except' => ''],
        'sort' => ['except' => 'latest'],
    ];

    public function mount()
    {
        // Initialize filters from URL parameters
    }

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function updatedCategory()
    {
        $this->resetPage();
    }

    public function updatedDifficulty()
    {
        $this->resetPage();
    }

    public function updatedCookingTime()
    {
        $this->resetPage();
    }

    public function updatedSort()
    {
        $this->resetPage();
    }

    public function clearFilters()
    {
        $this->reset(['category', 'difficulty', 'cookingTime', 'search', 'sort']);
        $this->resetPage();
    }

    public function render()
    {
        $filters = [
            'category' => $this->category,
            'difficulty' => $this->difficulty,
            'cooking_time' => $this->cookingTime,
            'search' => $this->search,
            'sort' => $this->sort,
        ];

        $recipeService = app(RecipeService::class);
        $recipes = $recipeService->getFilteredRecipes($filters, $this->perPage);
        
        $categories = Category::where('parent_id', null)->with('children')->get();
        $tags = Tag::orderBy('usage_count', 'desc')->limit(20)->get();

        return view('livewire.recipes.recipe-list', [
            'recipes' => $recipes,
            'categories' => $categories,
            'tags' => $tags,
        ]);
    }
} 