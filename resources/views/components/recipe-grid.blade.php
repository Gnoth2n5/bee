@props([
    'recipes',
    'title' => 'Công thức mới nhất',
    'subtitle' => null,
    'viewMode' => 'grid',
    'hasActiveFilters' => false,
    'difficulty' => '',
    'cookingTime' => ''
])

<section class="py-12">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-8 gap-4">
            <div>
                <h2 class="text-3xl font-bold text-gray-900">{{ $title }}</h2>
                @if($subtitle)
                    <p class="text-gray-600 mt-2">{{ $subtitle }}</p>
                @endif
            </div>

            <!-- Filter Controls -->
            <div class="flex flex-wrap gap-4">
                <!-- Sort Dropdown -->
                <select 
                    class="border border-gray-300 rounded-lg px-3 py-2 bg-white focus:ring-2 focus:ring-orange-500 focus:border-transparent"
                    wire:model.live="sort"
                >
                    <option value="latest">Mới nhất</option>
                    <option value="popular">Phổ biến</option>
                    <option value="rating">Đánh giá cao</option>
                    <option value="oldest">Cũ nhất</option>
                </select>

                <!-- Difficulty Filter -->
                <div class="relative">
                    <select 
                        class="form-select border border-gray-300 rounded-lg px-3 pr-10 py-2 bg-white focus:ring-2 focus:ring-orange-500 focus:border-transparent appearance-none"
                        wire:model.live="difficulty"
                    >
                        <option value="">Tất cả độ khó</option>
                        <option value="easy">Dễ</option>
                        <option value="medium">Trung bình</option>
                        <option value="hard">Khó</option>
                    </select>
                    
                </div>

                <!-- Cooking Time Filter -->
                <select 
                    class="border border-gray-300 rounded-lg px-3 py-2 bg-white focus:ring-2 focus:ring-orange-500 focus:border-transparent"
                    wire:model.live="cookingTime"
                >
                    <option value="">Tất cả thời gian</option>
                    <option value="quick">Nhanh (< 30 phút)</option>
                    <option value="medium">Trung bình (30-60 phút)</option>
                    <option value="long">Lâu (> 60 phút)</option>
                </select>

                <!-- View Toggle -->
                <div class="flex border border-gray-300 rounded-lg overflow-hidden">
                    <button 
                        class="px-3 py-2 {{ $viewMode === 'grid' ? 'bg-orange-500 text-white' : 'bg-white text-gray-600 hover:bg-gray-50' }} transition-colors"
                        wire:click="$set('viewMode', 'grid')"
                    >
                        <svg class="w-5 h-5" fill="none" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z" />
                        </svg>
                    </button>
                    <button 
                        class="px-3 py-2 {{ $viewMode === 'list' ? 'bg-orange-500 text-white' : 'bg-white text-gray-600 hover:bg-gray-50' }} transition-colors"
                        wire:click="$set('viewMode', 'list')"
                    >
                        <svg class="w-5 h-5" fill="none" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16" />
                        </svg>
                    </button>
                </div>
            </div>
        </div>

        <!-- Active Filters -->
        @if($hasActiveFilters)
            <div class="mb-6 flex flex-wrap gap-2">
                <span class="text-sm text-gray-600">Bộ lọc:</span>
                @if($difficulty)
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm bg-orange-100 text-orange-700">
                        Độ khó: {{ ucfirst($difficulty) }}
                        <button wire:click="$set('difficulty', '')" class="ml-2 hover:text-orange-900">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </span>
                @endif
                @if($cookingTime)
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm bg-orange-100 text-orange-700">
                        Thời gian: {{ $cookingTime }}
                        <button wire:click="$set('cookingTime', '')" class="ml-2 hover:text-orange-900">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </span>
                @endif
                <button 
                    wire:click="clearFilters"
                    class="text-sm text-orange-600 hover:text-orange-700 underline"
                >
                    Xóa tất cả
                </button>
            </div>
        @endif

        <!-- Recipe Cards Grid -->
        @if($recipes->count() > 0)
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                @foreach($recipes as $recipe)
                    <x-recipe-card :recipe="$recipe" />
                @endforeach
            </div>

            <!-- Pagination -->
            <div class="mt-8" wire:loading.class="opacity-50">
                <x-livewire-pagination :paginator="$recipes" />
            </div>
        @else
            <!-- Empty State -->
            <div class="text-center py-12">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                </svg>
                <h3 class="mt-2 text-sm font-medium text-gray-900">Không tìm thấy công thức</h3>
                <p class="mt-1 text-sm text-gray-500">
                    @if($hasActiveFilters)
                        Thử thay đổi bộ lọc hoặc tìm kiếm với từ khóa khác.
                    @else
                        Chưa có công thức nào được đăng tải.
                    @endif
                </p>
                @if($hasActiveFilters)
                    <div class="mt-6">
                        <button 
                            wire:click="clearFilters"
                            class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-orange-600 hover:bg-orange-700"
                        >
                            Xóa bộ lọc
                        </button>
                    </div>
                @endif
            </div>
        @endif
    </div>
</section> 