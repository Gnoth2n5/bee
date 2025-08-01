<?php

namespace App\Livewire;

use App\Models\Recipe;
use App\Models\Category;
use App\Models\Tag;
use App\Services\RecipeService;
use Livewire\Component;
use Livewire\Attributes\Url;

class GlobalSearch extends Component
{
    #[Url(as: 'q')]
    public $searchQuery = '';

    public $showResults = false;
    public $isLoading = false;
    public $searchResults = [];
    public $recentSearches = [];
    public $popularSearches = [];

    protected $listeners = ['closeSearch' => 'closeSearch'];

    public function mount()
    {
        // Khởi tạo searchResults
        $this->searchResults = [
            'recipes' => collect(),
            'total' => 0,
            'hasMore' => false
        ];

        $this->loadRecentSearches();
        $this->loadPopularSearches();
    }

    public function updatedSearchQuery()
    {
        if (strlen($this->searchQuery) >= 1) {
            $this->performSearch();
        } else {
            $this->searchResults = [];
            $this->showResults = false;
        }
    }

    public function performSearch()
    {
        if (empty($this->searchQuery)) {
            $this->searchResults = [];
            $this->showResults = false;
            return;
        }

        $this->isLoading = true;
        $this->showResults = true;

        try {
            // Tìm kiếm trực tiếp trong database thay vì qua service
            $recipes = Recipe::where('status', 'approved')
                ->where(function ($query) {
                    $query->where('title', 'like', "%{$this->searchQuery}%")
                        ->orWhere('description', 'like', "%{$this->searchQuery}%")
                        ->orWhere('summary', 'like', "%{$this->searchQuery}%");
                })
                ->with(['user', 'categories', 'tags'])
                ->limit(8)
                ->get();

            $this->searchResults = [
                'recipes' => $recipes,
                'total' => $recipes->count(),
                'hasMore' => $recipes->count() >= 8
            ];

            // Lưu tìm kiếm vào lịch sử
            $this->saveToRecentSearches();
        } catch (\Exception $e) {
            $this->searchResults = [
                'recipes' => collect(),
                'total' => 0,
                'hasMore' => false,
                'error' => 'Có lỗi xảy ra khi tìm kiếm'
            ];
        }

        $this->isLoading = false;
    }

    public function selectSearch($query)
    {
        $this->searchQuery = $query;
        $this->performSearch();
    }

    public function clearSearch()
    {
        $this->searchQuery = '';
        $this->searchResults = [];
        $this->showResults = false;
    }

    public function closeSearch()
    {
        $this->showResults = false;
    }

    public function goToRecipe($recipeId)
    {
        $this->closeSearch();
        return redirect()->route('recipes.show', $recipeId);
    }

    public function goToSearchPage()
    {
        if (!empty($this->searchQuery)) {
            // Nếu có kết quả tìm kiếm, chuyển đến công thức đầu tiên
            if ($this->searchResults['recipes']->count() > 0) {
                $firstRecipe = $this->searchResults['recipes']->first();
                $this->closeSearch();
                return redirect()->route('recipes.show', $firstRecipe);
            }
            // Nếu không có kết quả, chuyển đến trang tìm kiếm đầy đủ
            $this->closeSearch();
            return redirect()->route('recipes.index', ['search' => $this->searchQuery]);
        }
    }

    protected function loadRecentSearches()
    {
        $this->recentSearches = session('recent_searches', []);
    }

    protected function loadPopularSearches()
    {
        // Lấy các từ khóa tìm kiếm phổ biến từ tags và categories
        $popularTags = Tag::orderBy('usage_count', 'desc')->limit(5)->pluck('name')->toArray();
        $popularCategories = Category::where('parent_id', null)->limit(3)->pluck('name')->toArray();

        $this->popularSearches = array_merge($popularTags, $popularCategories);
    }

    protected function saveToRecentSearches()
    {
        $recentSearches = session('recent_searches', []);

        // Thêm tìm kiếm mới vào đầu danh sách
        array_unshift($recentSearches, $this->searchQuery);

        // Loại bỏ trùng lặp và giới hạn 10 tìm kiếm gần nhất
        $recentSearches = array_unique($recentSearches);
        $recentSearches = array_slice($recentSearches, 0, 10);

        session(['recent_searches' => $recentSearches]);
        $this->recentSearches = $recentSearches;
    }

    public function render()
    {
        return view('livewire.global-search');
    }
}