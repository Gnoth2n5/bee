<?php

namespace App\Livewire\Recipes;

use App\Models\Recipe;
use App\Models\Category;
use App\Models\Tag;
use App\Services\RecipeService;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Url;
use Livewire\Attributes\Layout;

#[Layout('layouts.app')]
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

    #[Url(as: 'tags')]
    public $selectedTags = [];

    #[Url(as: 'min_rating')]
    public $minRating = '';

    #[Url(as: 'max_calories')]
    public $maxCalories = '';

    #[Url(as: 'servings')]
    public $servings = '';

    #[Url(as: 'price_range')]
    public $priceRange = '';

    public $perPage = 12;
    public $showAdvancedFilters = false;
    public $viewMode = 'grid'; // grid, list



    public function mount()
    {
        // Initialize filters from URL parameters
        if (is_string($this->selectedTags)) {
            $this->selectedTags = explode(',', $this->selectedTags);
        }
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

    public function updatedSelectedTags()
    {
        $this->resetPage();
    }

    public function updatedMinRating()
    {
        $this->resetPage();
    }

    public function updatedMaxCalories()
    {
        $this->resetPage();
    }

    public function updatedServings()
    {
        $this->resetPage();
    }

    public function updatedPriceRange()
    {
        $this->resetPage();
    }

    public function toggleAdvancedFilters()
    {
        $this->showAdvancedFilters = !$this->showAdvancedFilters;
    }

    public function toggleViewMode()
    {
        $this->viewMode = $this->viewMode === 'grid' ? 'list' : 'grid';
    }

    public function toggleTag($tagId)
    {
        if (in_array($tagId, $this->selectedTags)) {
            $this->selectedTags = array_diff($this->selectedTags, [$tagId]);
        } else {
            $this->selectedTags[] = $tagId;
        }
        $this->resetPage();
    }

    public function clearFilters()
    {
        $this->reset([
            'category',
            'difficulty',
            'cookingTime',
            'search',
            'sort',
            'selectedTags',
            'minRating',
            'maxCalories',
            'servings',
            'priceRange'
        ]);
        $this->resetPage();
    }

    public function confirmToggleFavorite($recipeId)
    {
        if (!\Illuminate\Support\Facades\Auth::check()) {
            $this->redirect(route('login'));
            return;
        }

        $user = \Illuminate\Support\Facades\Auth::user();
        $recipe = \App\Models\Recipe::find($recipeId);

        if (!$recipe) {
            return;
        }

        $favorite = \App\Models\Favorite::where('user_id', $user->id)
            ->where('recipe_id', $recipeId)
            ->first();

        if ($favorite) {
            $favorite->delete();
            $this->dispatch('favorite-removed', recipeId: $recipeId);
            $this->dispatch('flash-message', message: 'Đã xóa khỏi danh sách yêu thích!', type: 'success');
        } else {
            \App\Models\Favorite::create([
                'user_id' => $user->id,
                'recipe_id' => $recipeId,
            ]);
            $this->dispatch('favorite-added', recipeId: $recipeId);
            $this->dispatch('flash-message', message: 'Đã thêm vào danh sách yêu thích!', type: 'success');
        }

        // Refresh component để cập nhật UI
        $this->dispatch('$refresh');
    }

    /**
     * Scroll to top when page changes
     */
    public function updatedPage()
    {
        $this->dispatch('scroll-to-top');
    }

    /**
     * Custom pagination methods for better UX
     */
    public function nextPage()
    {
        $this->setPage($this->getPage() + 1);
        $this->dispatch('scroll-to-top');
    }

    public function previousPage()
    {
        if ($this->getPage() > 1) {
            $this->setPage($this->getPage() - 1);
            $this->dispatch('scroll-to-top');
        }
    }

    public function gotoPage($page)
    {
        $this->setPage($page);
        $this->dispatch('scroll-to-top');
    }

    public function render()
    {
        try {
            $filters = [
                'category' => $this->category,
                'difficulty' => $this->difficulty,
                'cooking_time' => $this->cookingTime,
                'search' => $this->search,
                'sort' => $this->sort,
                'tags' => $this->selectedTags,
                'min_rating' => $this->minRating,
                'max_calories' => $this->maxCalories,
                'servings' => $this->servings,
                'price_range' => $this->priceRange,
            ];

            $recipeService = app(RecipeService::class);
            $recipes = $recipeService->getFilteredRecipes($filters, $this->perPage);

            $categories = Category::all();
            $tags = Tag::orderBy('usage_count', 'desc')->limit(30)->get();

            return view('livewire.recipes.recipe-list', [
                'recipes' => $recipes,
                'categories' => $categories,
                'tags' => $tags,
            ]);
        } catch (\Exception $e) {
            // Log error for debugging
            \Log::error('RecipeList render error: ' . $e->getMessage());

            // Return empty results on error
            return view('livewire.recipes.recipe-list', [
                'recipes' => collect([])->paginate($this->perPage),
                'categories' => collect([]),
                'tags' => collect([]),
            ]);
        }
    }
}
