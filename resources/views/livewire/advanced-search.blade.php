<div class="min-h-screen bg-gray-50">
    <!-- Header Section -->
    <div class="bg-white border-b border-gray-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
            <div class="text-center">
                <h1 class="text-3xl font-bold text-gray-900 mb-2">Tìm Kiếm Công Thức</h1>
                <p class="text-lg text-gray-600">Khám phá hàng nghìn công thức nấu ăn ngon với bộ lọc thông minh</p>
            </div>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
        <!-- Search Bar with Image Upload -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-6">
            <!-- Image Upload Section -->
            @if($searchImage)
                <div class="mb-4 p-4 bg-orange-50 rounded-lg border border-orange-200">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center space-x-3">
                            <div class="w-12 h-12 bg-orange-100 rounded-lg flex items-center justify-center">
                                <svg class="w-6 h-6 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-900">{{ $searchImage->getClientOriginalName() }}</p>
                                <p class="text-xs text-gray-500">{{ number_format($searchImage->getSize() / 1024, 1) }} KB</p>
                            </div>
                        </div>
                        <div class="flex items-center space-x-2">
                            <button 
                                wire:click="analyzeImage"
                                wire:loading.attr="disabled"
                                class="px-4 py-2 bg-orange-500 hover:bg-orange-600 text-white rounded-lg transition-colors disabled:opacity-50"
                            >
                                <span wire:loading.remove>Phân tích ảnh</span>
                                <span wire:loading>Đang phân tích...</span>
                            </button>
                            <button 
                                wire:click="clearImageSearch"
                                class="p-2 text-gray-400 hover:text-gray-600 transition-colors"
                            >
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Image Analysis Result -->
            @if($imageAnalysisResult)
                <div class="mb-4 p-4 {{ $imageAnalysisResult['success'] ? 'bg-green-50 border border-green-200' : 'bg-red-50 border border-red-200' }} rounded-lg">
                    <div class="flex items-start space-x-3">
                        @if($imageAnalysisResult['success'])
                            <svg class="w-5 h-5 text-green-600 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        @else
                            <svg class="w-5 h-5 text-red-600 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        @endif
                        <div class="flex-1">
                            <p class="text-sm {{ $imageAnalysisResult['success'] ? 'text-green-800' : 'text-red-800' }}">
                                {{ $imageAnalysisResult['message'] }}
                            </p>
                            @if($imageAnalysisResult['success'] && isset($imageAnalysisResult['keywords']))
                                <div class="mt-2 flex flex-wrap gap-1">
                                    @foreach($imageAnalysisResult['keywords'] as $keyword)
                                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                            {{ $keyword }}
                                        </span>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                        <button 
                            wire:click="clearImageSearch"
                            class="text-gray-400 hover:text-gray-600 transition-colors"
                        >
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>
                </div>
            @endif

            <!-- Search Bar -->
            <div class="relative">
                <input
                    type="text"
                    wire:model.live.debounce.300ms="searchQuery"
                    placeholder="Tìm kiếm món ăn yêu thích..."
                    class="w-full pl-12 pr-32 py-4 text-gray-900 rounded-lg border border-gray-300 focus:ring-2 focus:ring-orange-500 focus:border-orange-500 text-lg"
                >
                <div class="absolute inset-y-0 left-0 pl-4 flex items-center">
                    <svg class="h-6 w-6 text-gray-400" fill="none" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                </div>
                
                <!-- Image Upload Button -->
                <div class="absolute inset-y-0 right-0 flex items-center">
                    <label for="image-upload" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-l-lg transition-colors cursor-pointer border-r border-gray-300">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                    </label>
                    <input 
                        id="image-upload" 
                        type="file" 
                        wire:model="searchImage" 
                        accept="image/*" 
                        class="hidden"
                    />
                </div>
            </div>
        </div>

        <div class="flex gap-6">
            <!-- Sidebar Filters -->
            <div class="w-80 flex-shrink-0">
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 sticky top-4">
                    <!-- Category Filter -->
                    <div class="mb-6">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Danh mục</label>
                        <select wire:model.live="selectedCategory" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-orange-500 focus:border-orange-500">
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
                        <select wire:model.live="selectedDifficulty" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-orange-500 focus:border-orange-500">
                            <option value="">Tất cả độ khó</option>
                            <option value="easy">Dễ</option>
                            <option value="medium">Trung bình</option>
                            <option value="hard">Khó</option>
                        </select>
                    </div>

                    <!-- Cooking Time Filter -->
                    <div class="mb-6">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Thời gian nấu</label>
                        <select wire:model.live="selectedCookingTime" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-orange-500 focus:border-orange-500">
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
                            <svg class="w-4 h-4 transform transition-transform {{ $showAdvancedFilters ? 'rotate-180' : '' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                            </svg>
                        </button>
                    </div>

                    <!-- Advanced Filters -->
                    @if($showAdvancedFilters)
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
                        </div>
                    @endif

                    <!-- Clear Filters -->
                    @if($searchQuery || $selectedCategory || $selectedDifficulty || $selectedCookingTime || $minRating || $maxCalories || !empty($selectedTags))
                        <div class="mt-6 pt-4 border-t border-gray-200">
                            <button 
                                wire:click="clearFilters"
                                class="w-full px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-lg transition-colors"
                            >
                                Xóa bộ lọc
                            </button>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Main Content -->
            <div class="flex-1">
                <!-- Results Header -->
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4 mb-6">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center space-x-4">
                            <span class="text-sm text-gray-600">
                                {{ $recipes->total() }} công thức
                            </span>
                            @if($searchQuery || $selectedCategory || $selectedDifficulty || $selectedCookingTime || $minRating || $maxCalories || !empty($selectedTags))
                                <span class="text-sm text-orange-600">
                                    Đang lọc
                                </span>
                            @endif
                        </div>
                        
                        <!-- View Mode Toggle -->
                        <div class="flex items-center space-x-2">
                            <button 
                                wire:click="toggleViewMode"
                                class="p-2 rounded-lg border border-gray-300 hover:bg-gray-50 transition-colors"
                                title="{{ $viewMode === 'grid' ? 'Chế độ danh sách' : 'Chế độ lưới' }}"
                            >
                                @if($viewMode === 'grid')
                                    <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16" />
                                    </svg>
                                @else
                                    <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z" />
                                    </svg>
                                @endif
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Tags Filter -->
                @if($tags->count() > 0)
                    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4 mb-6">
                        <h3 class="text-sm font-medium text-gray-700 mb-3">Tags phổ biến</h3>
                        <div class="flex flex-wrap gap-2">
                            @foreach($tags as $tag)
                                <button 
                                    wire:click="toggleTag({{ $tag->id }})"
                                    class="px-3 py-1 text-sm rounded-full transition-colors {{ in_array($tag->id, $selectedTags) ? 'bg-orange-500 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}"
                                >
                                    {{ $tag->name }}
                                </button>
                            @endforeach
                        </div>
                    </div>
                @endif

                <!-- Recipes Grid/List -->
                @if($recipes->count() > 0)
                    <div class="{{ $viewMode === 'grid' ? 'grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6' : 'space-y-4' }}">
                        @foreach($recipes as $recipe)
                            @if($viewMode === 'grid')
                                <x-recipe-grid-card :recipe="$recipe" />
                            @else
                                <x-recipe-list-item :recipe="$recipe" />
                            @endif
                        @endforeach
                    </div>

                    <!-- Pagination -->
                    <div class="mt-8">
                        {{ $recipes->links() }}
                    </div>
                @else
                    <!-- Empty State -->
                    <div class="text-center py-12">
                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                        <h3 class="mt-2 text-sm font-medium text-gray-900">Không tìm thấy công thức</h3>
                        <p class="mt-1 text-sm text-gray-500">Thử thay đổi bộ lọc hoặc tìm kiếm với từ khóa khác.</p>
                        <div class="mt-6">
                            <button 
                                wire:click="clearFilters"
                                class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-orange-600 hover:bg-orange-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-orange-500"
                            >
                                Xóa bộ lọc
                            </button>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div> 