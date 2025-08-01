<?php

namespace App\Livewire;

use App\Models\Recipe;
use App\Models\Category;
use App\Models\Tag;
use App\Services\RecipeService;
use App\Services\GeminiService;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;
use Livewire\Attributes\Url;

class AdvancedSearch extends Component
{
    use WithFileUploads, WithPagination;

    #[Url(as: 'search')]
    public $searchQuery = '';

    #[Url(as: 'category')]
    public $selectedCategory = '';

    #[Url(as: 'difficulty')]
    public $selectedDifficulty = '';

    #[Url(as: 'cooking_time')]
    public $selectedCookingTime = '';

    #[Url(as: 'min_rating')]
    public $minRating = '';

    #[Url(as: 'max_calories')]
    public $maxCalories = '';

    #[Url(as: 'tags')]
    public $selectedTags = [];

    public $searchImage = null;
    public $isAnalyzingImage = false;
    public $imageAnalysisResult = null;
    public $showAdvancedFilters = false;
    public $viewMode = 'grid'; // grid, list
    public $perPage = 12;

    protected $listeners = ['searchUpdated' => 'updateSearch'];

    public function mount()
    {
        // Initialize tags from URL parameters
        if (is_string($this->selectedTags)) {
            $this->selectedTags = explode(',', $this->selectedTags);
        }
    }

    public function updateSearch($search)
    {
        $this->searchQuery = $search;
        $this->performSearch();
    }

    public function updatedSearchQuery()
    {
        $this->resetPage();
        $this->performSearch();
    }

    public function updatedSelectedCategory()
    {
        $this->resetPage();
        $this->performSearch();
    }

    public function updatedSelectedDifficulty()
    {
        $this->resetPage();
        $this->performSearch();
    }

    public function updatedSelectedCookingTime()
    {
        $this->resetPage();
        $this->performSearch();
    }

    public function updatedMinRating()
    {
        $this->resetPage();
        $this->performSearch();
    }

    public function updatedMaxCalories()
    {
        $this->resetPage();
        $this->performSearch();
    }

    public function updatedSelectedTags()
    {
        $this->resetPage();
        $this->performSearch();
    }

    public function toggleTag($tagId)
    {
        if (in_array($tagId, $this->selectedTags)) {
            $this->selectedTags = array_diff($this->selectedTags, [$tagId]);
        } else {
            $this->selectedTags[] = $tagId;
        }
        $this->resetPage();
        $this->performSearch();
    }

    public function toggleAdvancedFilters()
    {
        $this->showAdvancedFilters = !$this->showAdvancedFilters;
    }

    public function toggleViewMode()
    {
        $this->viewMode = $this->viewMode === 'grid' ? 'list' : 'grid';
    }

    public function clearFilters()
    {
        $this->reset([
            'searchQuery',
            'selectedCategory',
            'selectedDifficulty',
            'selectedCookingTime',
            'minRating',
            'maxCalories',
            'selectedTags'
        ]);
        $this->resetPage();
        $this->performSearch();
    }

    public function analyzeImage()
    {
        $this->validate([
            'searchImage' => 'required|image|max:5120', // 5MB max
        ]);

        $this->isAnalyzingImage = true;
        $this->imageAnalysisResult = null;

        try {
            $geminiService = app(GeminiService::class);
            $result = $geminiService->searchRecipesByImage($this->searchImage);

            if ($result['success']) {
                // Sử dụng từ khóa đầu tiên để tìm kiếm
                $this->searchQuery = $result['keywords'][0] ?? '';
                $this->performSearch();

                $this->imageAnalysisResult = [
                    'success' => true,
                    'keywords' => $result['keywords'],
                    'message' => 'Đã tìm kiếm với từ khóa: ' . implode(', ', $result['keywords'])
                ];
            } else {
                $this->imageAnalysisResult = [
                    'success' => false,
                    'message' => $result['error']
                ];
            }
        } catch (\Exception $e) {
            $this->imageAnalysisResult = [
                'success' => false,
                'message' => 'Có lỗi xảy ra khi phân tích ảnh.'
            ];
        }

        $this->isAnalyzingImage = false;
        $this->searchImage = null;
    }

    public function clearImageSearch()
    {
        $this->searchImage = null;
        $this->imageAnalysisResult = null;
    }

    public function performSearch()
    {
        // This method will be called by Livewire automatically when properties change
        // The actual search logic is in the render method
    }

    public function render()
    {
        $filters = [
            'search' => $this->searchQuery,
            'category' => $this->selectedCategory,
            'difficulty' => $this->selectedDifficulty,
            'cooking_time' => $this->selectedCookingTime,
            'min_rating' => $this->minRating,
            'max_calories' => $this->maxCalories,
            'tags' => $this->selectedTags,
        ];

        $recipeService = app(RecipeService::class);
        $recipes = $recipeService->getFilteredRecipes($filters, $this->perPage);

        $categories = Category::where('parent_id', null)->with('children')->get();
        $tags = Tag::orderBy('usage_count', 'desc')->limit(30)->get();

        return view('livewire.advanced-search', [
            'recipes' => $recipes,
            'categories' => $categories,
            'tags' => $tags,
        ]);
    }
}