<div class="min-h-screen bg-gray-50">
    <!-- Header Section -->
    <div class="bg-white border-b border-gray-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">Công Thức Nấu Ăn</h1>
                    <p class="text-sm text-gray-600 mt-1">Khám phá hàng nghìn công thức nấu ăn ngon</p>
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
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
        <div class="flex gap-6">
            <!-- Sidebar Filters -->
            <div class="w-80 flex-shrink-0">
                <x-recipe-filters 
                    :categories="$categories" 
                    :tags="$tags" 
                    :filters="['showAdvancedFilters' => $showAdvancedFilters]" 
                />
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
                            @if(($search ?? '') || ($category ?? '') || ($difficulty ?? '') || ($cookingTime ?? '') || !empty($selectedTags ?? []) || ($minRating ?? '') || ($maxCalories ?? '') || ($servings ?? ''))
                                <span class="text-sm text-orange-600">
                                    Đang lọc
                                </span>
                            @endif
                        </div>
                        
                        <!-- Active Filters Display -->
                        <x-active-filters 
                            :filters="[
                                'search' => $search,
                                'category' => $category,
                                'difficulty' => $difficulty,
                                'cookingTime' => $cookingTime,
                                'selectedTags' => $selectedTags,
                                'minRating' => $minRating,
                                'maxCalories' => $maxCalories,
                                'servings' => $servings
                            ]"
                            :categories="$categories"
                        />
                    </div>
                </div>

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
                    <x-empty-state 
                        title="Không tìm thấy công thức"
                        description="Thử thay đổi bộ lọc hoặc tìm kiếm với từ khóa khác."
                        actionText="Xóa bộ lọc"
                        actionMethod="clearFilters"
                    />
                @endif
            </div>
        </div>
    </div>
</div> 