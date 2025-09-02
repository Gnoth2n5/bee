<?php

namespace App\Livewire;

use App\Models\Recipe;
use App\Models\Category;
use App\Models\Tag;
use App\Services\RecipeService;
use Livewire\Component;
use Livewire\Attributes\Url;

class QuickSearch extends Component
{
    #[Url(as: 'q')]
    public $searchQuery = '';

    public $showSuggestions = false;
    public $suggestions = [];
    public $popularCategories = [];
    public $popularTags = [];

    protected $listeners = ['searchUpdated' => 'updateSearch'];

    public function mount()
    {
        // Khởi tạo suggestions
        $this->suggestions = [
            'recipes' => collect(),
            'categories' => collect(),
            'tags' => collect()
        ];
    }

    public function updatedSearchQuery()
    {
        if (strlen($this->searchQuery) >= 1) {
            $this->performSearch();
            $this->showSuggestions = true;
        } else {
            $this->suggestions = [];
            $this->showSuggestions = false;
        }
    }

    public function performSearch()
    {
        if (empty($this->searchQuery)) {
            $this->suggestions = [];
            return;
        }

        // Tìm kiếm trong recipes
        $recipes = Recipe::where('status', 'approved')
            ->where(function ($q) {
                $q->where('title', 'like', "%{$this->searchQuery}%")
                    ->orWhere('description', 'like', "%{$this->searchQuery}%");
            })
            ->with(['user', 'categories', 'tags'])
            ->limit(8)
            ->get();

        $this->suggestions = [
            'recipes' => $recipes,
            'categories' => collect(),
            'tags' => collect()
        ];
    }

    public function updateSearch($search)
    {
        $this->searchQuery = $search;
        $this->performSearch();
    }

    public function selectSuggestion($suggestion)
    {
        $this->searchQuery = $suggestion;
        $this->showSuggestions = false;
        $this->performSearch();
    }

    public function selectCategory($categorySlug)
    {
        return redirect()->route('recipes.index', ['category' => $categorySlug]);
    }

    public function selectTag($tagName)
    {
        $this->searchQuery = $tagName;
        $this->showSuggestions = false;
        $this->performSearch();
    }

    public function goToSearchPage()
    {
        if (!empty($this->searchQuery)) {
            $this->showSuggestions = false;
            // Luôn chuyển đến trang tìm kiếm đầy đủ thay vì redirect trực tiếp đến recipe
            return redirect()->route('recipes.index', ['search' => $this->searchQuery]);
        }
    }

    public function clearSearch()
    {
        $this->searchQuery = '';
        $this->suggestions = [];
        $this->showSuggestions = false;
    }





    public function render()
    {
        return view('livewire.quick-search');
    }
}
