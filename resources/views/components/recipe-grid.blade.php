@props([
    'recipes',
    'title' => 'Công thức mới nhất',
    'subtitle' => null,
    'viewMode' => 'grid',
    'hasActiveFilters' => false,
    'difficulty' => '',
    'cookingTime' => ''
])

<section class="py-20 bg-gradient-to-br from-gray-50 via-white to-gray-100 dark:from-slate-900 dark:via-slate-800 dark:to-slate-900 relative overflow-hidden">
    <!-- Background Decorations -->
    <div class="absolute inset-0 overflow-hidden pointer-events-none">
        <div class="absolute top-20 left-10 w-32 h-32 bg-gradient-to-r from-blue-200 to-cyan-200 dark:from-blue-800/30 dark:to-cyan-800/30 rounded-full blur-3xl opacity-30 animate-pulse"></div>
        <div class="absolute bottom-20 right-20 w-40 h-40 bg-gradient-to-r from-purple-200 to-pink-200 dark:from-purple-800/30 dark:to-pink-800/30 rounded-full blur-3xl opacity-20 animate-bounce" style="animation-delay: 1s"></div>
        <div class="absolute top-1/2 left-1/2 w-24 h-24 bg-gradient-to-r from-green-200 to-emerald-200 dark:from-green-800/30 dark:to-emerald-800/30 rounded-full blur-2xl opacity-25 animate-ping" style="animation-delay: 2s"></div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
        <!-- Header -->
        <div class="text-center mb-16">
            <div class="inline-flex items-center px-4 py-2 rounded-full bg-gradient-to-r from-blue-100 to-cyan-100 dark:from-blue-900/30 dark:to-cyan-900/30 border border-blue-200 dark:border-blue-800 mb-6">
                <x-lucide-chef-hat class="w-4 h-4 mr-2 text-blue-500 animate-bounce" style="animation-delay: 0.5s" />
                <span class="text-sm font-semibold text-blue-600 dark:text-blue-400">Cộng đồng sáng tạo</span>
            </div>
            
            <h2 class="text-4xl md:text-5xl lg:text-6xl font-black text-gray-800 dark:text-white mb-6 animate-fade-in-up">
                {{ $title }}
            </h2>
            
            @if($subtitle)
                <p class="text-lg text-gray-600 dark:text-gray-300 max-w-3xl mx-auto leading-relaxed animate-fade-in-up">
                    {{ $subtitle }}
                </p>
            @endif
        </div>

        <!-- Filters Section -->
        <div class="bg-white/80 dark:bg-slate-800/80 backdrop-blur-sm rounded-2xl shadow-xl border border-gray-200/50 dark:border-slate-700/50 p-6 mb-12">
            <div class="flex flex-col lg:flex-row lg:items-end justify-between gap-6">
                <div class="flex-1">
                    <div class="flex items-center mb-3">
                        <x-lucide-filter class="w-5 h-5 mr-2 text-blue-500" />
                        <label class="text-sm font-semibold text-gray-700 dark:text-gray-300">
                            Lọc và sắp xếp
                        </label>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <!-- Sort Dropdown -->
                        <div class="relative">
                            <select 
                                class="w-full px-4 py-3 pr-10 border border-gray-300 dark:border-slate-600 rounded-xl shadow-sm bg-white dark:bg-slate-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-300 hover:border-blue-400 font-medium appearance-none"
                                wire:model.live="sort"
                            >
                                <option value="latest">Mới nhất</option>
                                <option value="popular">Phổ biến</option>
                                <option value="rating">Đánh giá cao</option>
                                <option value="oldest">Cũ nhất</option>
                            </select>
                            <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                                <x-lucide-chevron-down class="w-5 h-5 text-gray-400" />
                            </div>
                        </div>

                        <!-- Difficulty Filter -->
                        <div class="relative">
                            <select 
                                class="w-full px-4 py-3 pr-10 border border-gray-300 dark:border-slate-600 rounded-xl shadow-sm bg-white dark:bg-slate-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-300 hover:border-blue-400 font-medium appearance-none"
                                wire:model.live="difficulty"
                            >
                                <option value="">Tất cả độ khó</option>
                                <option value="easy">Dễ</option>
                                <option value="medium">Trung bình</option>
                                <option value="hard">Khó</option>
                            </select>
                            <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                                <x-lucide-chevron-down class="w-5 h-5 text-gray-400" />
                            </div>
                        </div>

                        <!-- Cooking Time Filter -->
                        <div class="relative">
                            <select 
                                class="w-full px-4 py-3 pr-10 border border-gray-300 dark:border-slate-600 rounded-xl shadow-sm bg-white dark:bg-slate-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-300 hover:border-blue-400 font-medium appearance-none"
                                wire:model.live="cookingTime"
                            >
                                <option value="">Tất cả thời gian</option>
                                <option value="quick">Nhanh (< 30 phút)</option>
                                <option value="medium">Trung bình (30-60 phút)</option>
                                <option value="long">Lâu (> 60 phút)</option>
                            </select>
                            <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                                <x-lucide-chevron-down class="w-5 h-5 text-gray-400" />
                            </div>
                        </div>
                    </div>
                </div>

                <!-- View Toggle -->
                <div class="flex flex-col items-center">
                    <div class="flex items-center mb-3">
                        <x-lucide-layout-grid class="w-5 h-5 mr-2 text-blue-500" />
                        <label class="text-sm font-semibold text-gray-700 dark:text-gray-300">
                            Chế độ hiển thị
                        </label>
                    </div>
                    <div class="flex border border-gray-300 dark:border-slate-600 rounded-xl overflow-hidden bg-white dark:bg-slate-700 shadow-sm">
                        <button 
                            class="px-4 py-3 text-sm font-semibold transition-all duration-300 flex items-center {{ $viewMode === 'grid' ? 'bg-gradient-to-r from-blue-500 to-cyan-600 text-white shadow-md' : 'text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-slate-600' }}"
                            wire:click="$set('viewMode', 'grid')"
                        >
                            <x-lucide-grid-3x3 class="w-4 h-4 mr-2" />
                            Lưới
                        </button>
                        <button 
                            class="px-4 py-3 text-sm font-semibold transition-all duration-300 flex items-center {{ $viewMode === 'list' ? 'bg-gradient-to-r from-blue-500 to-cyan-600 text-white shadow-md' : 'text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-slate-600' }}"
                            wire:click="$set('viewMode', 'list')"
                        >
                            <x-lucide-list class="w-4 h-4 mr-2" />
                            Danh sách
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Active Filters -->
        @if($hasActiveFilters)
            <div class="bg-blue-50 dark:bg-blue-900/20 rounded-xl p-4 mb-8 border border-blue-200 dark:border-blue-800">
                <div class="flex flex-wrap items-center gap-3">
                    <div class="flex items-center">
                        <x-lucide-filter class="w-4 h-4 mr-2 text-blue-500" />
                        <span class="text-sm font-semibold text-blue-700 dark:text-blue-400">Bộ lọc đang áp dụng:</span>
                    </div>
                    @if($difficulty)
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-gradient-to-r from-orange-100 to-red-100 dark:from-orange-900/30 dark:to-red-900/30 text-orange-700 dark:text-orange-400 border border-orange-200 dark:border-orange-800">
                            <x-lucide-trending-up class="w-3 h-3 mr-1" />
                            Độ khó: {{ ucfirst($difficulty) }}
                            <button wire:click="$set('difficulty', '')" class="ml-2 text-orange-600 dark:text-orange-400 hover:text-orange-800 dark:hover:text-orange-300 transition-colors">
                                <x-lucide-x class="w-3 h-3" />
                            </button>
                        </span>
                    @endif
                    @if($cookingTime)
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-gradient-to-r from-green-100 to-emerald-100 dark:from-green-900/30 dark:to-emerald-900/30 text-green-700 dark:text-green-400 border border-green-200 dark:border-green-800">
                            <x-lucide-clock class="w-3 h-3 mr-1" />
                            Thời gian: {{ $cookingTime === 'quick' ? 'Nhanh' : ($cookingTime === 'medium' ? 'Trung bình' : 'Lâu') }}
                            <button wire:click="$set('cookingTime', '')" class="ml-2 text-green-600 dark:text-green-400 hover:text-green-800 dark:hover:text-green-300 transition-colors">
                                <x-lucide-x class="w-3 h-3" />
                            </button>
                        </span>
                    @endif
                    <button 
                        wire:click="clearFilters"
                        class="inline-flex items-center px-3 py-1 text-sm font-semibold text-blue-600 dark:text-blue-400 hover:text-blue-800 dark:hover:text-blue-300 transition-colors"
                    >
                        <x-lucide-rotate-ccw class="w-3 h-3 mr-1" />
                        Xóa tất cả
                    </button>
                </div>
            </div>
        @endif

        <!-- Recipe Cards Grid -->
        @if($recipes->count() > 0)
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                @foreach($recipes as $index => $recipe)
                    <div class="animate-fade-in-up" style="animation-delay: {{ $index * 100 }}ms">
                        <x-recipe-card :recipe="$recipe" />
                    </div>
                @endforeach
            </div>

            <!-- Pagination -->
            <div class="mt-8" wire:loading.class="opacity-50">
                <x-basic-pagination :paginator="$recipes" />
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