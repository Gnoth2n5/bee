<div class="min-h-screen bg-gray-50">
    <!-- Header Section -->
    <div class="bg-white border-b border-gray-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">Công Thức Nấu Ăn</h1>
                    <p class="text-sm text-gray-600 mt-1">Khám phá hàng nghìn công thức nấu ăn ngon</p>
                </div>

                <!-- Export and View Mode Controls -->
                <div class="flex items-center space-x-3">
                    <!-- Export Dropdown -->
                    <div class="relative" x-data="{ open: false }">
                        <button @click="open = !open" 
                                class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg transition-colors flex items-center">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                            Xuất dữ liệu
                        </button>
                        
                        <div x-show="open" @click.away="open = false" 
                             x-transition:enter="transition ease-out duration-100"
                             x-transition:enter-start="transform opacity-0 scale-95"
                             x-transition:enter-end="transform opacity-100 scale-100"
                             x-transition:leave="transition ease-in duration-75"
                             x-transition:leave-start="transform opacity-100 scale-100"
                             x-transition:leave-end="transform opacity-0 scale-95"
                             class="absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg z-50 border border-gray-200">
                            <div class="py-1">
                                <a href="{{ route('recipes.export.excel') }}?{{ http_build_query(request()->query()) }}" 
                                   class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                    <svg class="w-4 h-4 mr-2 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                    </svg>
                                    Excel (.xlsx)
                                </a>
                                <a href="{{ route('recipes.export.csv') }}?{{ http_build_query(request()->query()) }}" 
                                   class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                    <svg class="w-4 h-4 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                    </svg>
                                    CSV (.csv)
                                </a>
                                <button @click="$dispatch('open-export-modal')" 
                                   class="flex items-center w-full px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                    <svg class="w-4 h-4 mr-2 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                                    </svg>
                                    ZIP với template
                                </button>
                                <a href="{{ route('recipes.export.pdf') }}?{{ http_build_query(request()->query()) }}" 
                                   class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                    <svg class="w-4 h-4 mr-2 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                                    </svg>
                                    PDF (.pdf)
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- View Mode Toggle -->
                    <button wire:click="toggleViewMode"
                        class="p-2 rounded-lg border border-gray-300 hover:bg-gray-50 transition-colors"
                        title="{{ $viewMode === 'grid' ? 'Chế độ danh sách' : 'Chế độ lưới' }}">
                        @if ($viewMode === 'grid')
                            <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M4 6h16M4 10h16M4 14h16M4 18h16" />
                            </svg>
                        @else
                            <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z" />
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
                <x-recipe-filters :categories="$categories" :tags="$tags" :filters="['showAdvancedFilters' => $showAdvancedFilters]" />
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
                            @if (
                                ($search ?? '') ||
                                    ($category ?? '') ||
                                    ($difficulty ?? '') ||
                                    ($cookingTime ?? '') ||
                                    !empty($selectedTags ?? []) ||
                                    ($minRating ?? '') ||
                                    ($maxCalories ?? '') ||
                                    ($servings ?? ''))
                                <span class="text-sm text-orange-600">
                                    Đang lọc
                                </span>
                            @endif
                        </div>

                        <!-- Active Filters Display -->
                        <x-active-filters :filters="[
                            'search' => $search,
                            'category' => $category,
                            'difficulty' => $difficulty,
                            'cookingTime' => $cookingTime,
                            'selectedTags' => $selectedTags,
                            'minRating' => $minRating,
                            'maxCalories' => $maxCalories,
                            'servings' => $servings,
                        ]" :categories="$categories" />
                    </div>
                </div>

                <!-- Recipes Grid/List -->
                @if ($recipes->count() > 0)
                    <div
                        class="{{ $viewMode === 'grid' ? 'grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6' : 'space-y-4' }}">
                        @foreach ($recipes as $recipe)
                            @if ($viewMode === 'grid')
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
                    <x-empty-state title="Không tìm thấy công thức"
                        description="Thử thay đổi bộ lọc hoặc tìm kiếm với từ khóa khác." actionText="Xóa bộ lọc"
                        actionMethod="clearFilters" />
                @endif
            </div>
        </div>
    </div>

    <!-- Export Modal -->
    <x-export-modal />
</div>
