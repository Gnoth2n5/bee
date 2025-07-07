@props(['categories', 'tags', 'filters'])

<div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 sticky top-4">
    <!-- Search Box -->
    <div class="mb-6">
        <label class="block text-sm font-medium text-gray-700 mb-2">Tìm kiếm</label>
        <div class="relative">
            <input 
                type="text" 
                wire:model.live.debounce.300ms="search"
                placeholder="Tìm công thức..."
                class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500"
            >
            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
            </div>
        </div>
    </div>

    <!-- Sort Options -->
    <div class="mb-6">
        <label class="block text-sm font-medium text-gray-700 mb-2">Sắp xếp</label>
        <select wire:model.live="sort" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-orange-500 focus:border-orange-500">
            <option value="latest">Mới nhất</option>
            <option value="popular">Phổ biến</option>
            <option value="rating">Đánh giá cao</option>
            <option value="cooking_time">Thời gian nấu</option>
            <option value="title">Tên A-Z</option>
        </select>
    </div>

    <!-- Category Filter -->
    <div class="mb-6">
        <label class="block text-sm font-medium text-gray-700 mb-2">Danh mục</label>
        <select wire:model.live="category" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-orange-500 focus:border-orange-500">
            <option value="">Tất cả danh mục</option>
            @foreach($categories as $cat)
                <option value="{{ $cat->slug }}">{{ $cat->name }}</option>
                @foreach($cat->children as $child)
                    <option value="{{ $child->slug }}">— {{ $child->name }}</option>
                @endforeach
            @endforeach
        </select>
    </div>

    <!-- Difficulty Filter -->
    <div class="mb-6">
        <label class="block text-sm font-medium text-gray-700 mb-2">Độ khó</label>
        <select wire:model.live="difficulty" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-orange-500 focus:border-orange-500">
            <option value="">Tất cả độ khó</option>
            <option value="easy">Dễ</option>
            <option value="medium">Trung bình</option>
            <option value="hard">Khó</option>
        </select>
    </div>

    <!-- Cooking Time Filter -->
    <div class="mb-6">
        <label class="block text-sm font-medium text-gray-700 mb-2">Thời gian nấu</label>
        <select wire:model.live="cookingTime" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-orange-500 focus:border-orange-500">
            <option value="">Tất cả thời gian</option>
            <option value="quick">Dưới 30 phút</option>
            <option value="medium">30-60 phút</option>
            <option value="long">Trên 60 phút</option>
        </select>
    </div>

    <!-- Advanced Filters Toggle -->
    <div class="mb-4">
        <button 
            wire:click="toggleAdvancedFilters"
            class="flex items-center justify-between w-full text-sm font-medium text-gray-700 hover:text-orange-600 transition-colors"
        >
            <span>Bộ lọc nâng cao</span>
            <svg class="w-4 h-4 transform transition-transform {{ $filters['showAdvancedFilters'] ?? false ? 'rotate-180' : '' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
            </svg>
        </button>
    </div>

    <!-- Advanced Filters -->
    @if($filters['showAdvancedFilters'] ?? false)
        <div class="space-y-4 border-t border-gray-200 pt-4">
            <!-- Min Rating -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Đánh giá tối thiểu</label>
                <select wire:model.live="minRating" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-orange-500 focus:border-orange-500">
                    <option value="">Tất cả</option>
                    <option value="4">4+ sao</option>
                    <option value="3">3+ sao</option>
                    <option value="2">2+ sao</option>
                </select>
            </div>

            <!-- Max Calories -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Calories tối đa</label>
                <select wire:model.live="maxCalories" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-orange-500 focus:border-orange-500">
                    <option value="">Tất cả</option>
                    <option value="200">Dưới 200 cal</option>
                    <option value="400">Dưới 400 cal</option>
                    <option value="600">Dưới 600 cal</option>
                </select>
            </div>

            <!-- Servings -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Khẩu phần</label>
                <select wire:model.live="servings" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-orange-500 focus:border-orange-500">
                    <option value="">Tất cả</option>
                    <option value="1">1 người</option>
                    <option value="2">2 người</option>
                    <option value="4">4 người</option>
                    <option value="6">6+ người</option>
                </select>
            </div>
        </div>
    @endif

    <!-- Tags Filter -->
    <div class="mb-6">
        <label class="block text-sm font-medium text-gray-700 mb-2">Tags phổ biến</label>
        <div class="space-y-2 max-h-48 overflow-y-auto">
            @foreach($tags as $tag)
                <label class="flex items-center">
                    <input 
                        type="checkbox" 
                        wire:model.live="selectedTags" 
                        value="{{ $tag->id }}"
                        class="rounded border-gray-300 text-orange-600 focus:ring-orange-500"
                    >
                    <span class="ml-2 text-sm text-gray-700">{{ $tag->name }}</span>
                </label>
            @endforeach
        </div>
    </div>

    <!-- Clear Filters -->
    <button 
        wire:click="clearFilters"
        class="w-full bg-gray-100 text-gray-700 py-2 px-4 rounded-lg hover:bg-gray-200 transition-colors text-sm font-medium"
    >
        Xóa bộ lọc
    </button>
</div> 