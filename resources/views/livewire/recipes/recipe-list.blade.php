<div class="min-h-screen bg-gradient-to-br from-gray-50 via-white to-gray-100 dark:from-slate-900 dark:via-slate-800 dark:to-slate-900">
    <!-- Hero Section -->
    <section class="relative py-20 bg-gradient-to-br from-orange-500 via-red-500 to-pink-600 overflow-hidden">
        <!-- Background Decorations -->
        <div class="absolute inset-0 overflow-hidden pointer-events-none">
            <div class="absolute top-20 left-10 w-32 h-32 bg-white/10 rounded-full blur-3xl opacity-30 animate-pulse"></div>
            <div class="absolute bottom-20 right-20 w-40 h-40 bg-white/5 rounded-full blur-3xl opacity-20 animate-bounce" style="animation-delay: 1s"></div>
            <div class="absolute top-1/2 left-1/2 w-24 h-24 bg-white/8 rounded-full blur-2xl opacity-25 animate-ping" style="animation-delay: 2s"></div>
            <div class="absolute top-10 right-10 w-20 h-20 bg-white/5 rounded-full animate-pulse"></div>
            <div class="absolute bottom-32 left-1/4 w-16 h-16 bg-white/4 rounded-full animate-ping" style="animation-delay: 3s"></div>
        </div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
            <div class="text-center text-white">
                <!-- Badge -->
                <div class="inline-flex items-center px-4 py-2 rounded-full bg-white/10 backdrop-blur-sm border border-white/20 mb-6">
                    <x-lucide-chef-hat class="w-4 h-4 mr-2 animate-spin" style="animation-duration: 3s" />
                    <span class="text-sm font-semibold">Khám phá ẩm thực</span>
                </div>

                <!-- Main Title -->
                <h1 class="text-4xl md:text-5xl lg:text-6xl font-black mb-6 drop-shadow-lg">
                    <span class="block">Công Thức</span>
                    <span class="block bg-gradient-to-r from-yellow-200 to-white bg-clip-text text-transparent animate-pulse">Nấu Ăn</span>
                </h1>

                <!-- Subtitle -->
                <p class="text-lg md:text-xl text-white/90 max-w-3xl mx-auto leading-relaxed mb-8 drop-shadow-md">
                    Khám phá hàng nghìn công thức nấu ăn ngon từ cộng đồng BeeFood. Tìm kiếm, lọc và chia sẻ những món ăn yêu thích của bạn.
                </p>

                <!-- Quick Stats -->
                <div class="flex flex-wrap justify-center gap-6 mb-8">
                    <div class="flex items-center gap-2 bg-white/10 backdrop-blur-sm rounded-full px-4 py-2 border border-white/20">
                        <div class="w-3 h-3 rounded-full bg-gradient-to-r from-yellow-400 to-orange-500 animate-pulse"></div>
                        <span class="text-sm font-semibold">{{ $recipes->total() }}+ Công thức</span>
                    </div>
                    <div class="flex items-center gap-2 bg-white/10 backdrop-blur-sm rounded-full px-4 py-2 border border-white/20">
                        <x-lucide-users class="w-4 h-4" />
                        <span class="text-sm font-semibold">Cộng đồng sáng tạo</span>
                    </div>
                    <div class="flex items-center gap-2 bg-white/10 backdrop-blur-sm rounded-full px-4 py-2 border border-white/20">
                        <x-lucide-star class="w-4 h-4 fill-current text-yellow-300" />
                        <span class="text-sm font-semibold">Chất lượng cao</span>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Quick Search Bar -->
    <section class="py-12 bg-gradient-to-r from-purple-500 via-indigo-500 to-blue-500 relative overflow-hidden">
        <!-- Background Pattern -->
        <div class="absolute inset-0 opacity-10">
            <div class="absolute top-0 left-0 w-full h-full" style="background-image: radial-gradient(circle at 25% 25%, white 2px, transparent 2px), radial-gradient(circle at 75% 75%, white 2px, transparent 2px); background-size: 50px 50px;"></div>
        </div>
        
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
            <div class="text-center mb-8">
                <h3 class="text-2xl font-bold text-white mb-2">Tìm kiếm nhanh</h3>
                <p class="text-white/80">Nhập tên món ăn hoặc nguyên liệu để tìm kiếm</p>
            </div>
            
            <div class="relative max-w-2xl mx-auto">
                <div class="flex">
                    <div class="relative flex-1">
                        <input 
                            type="text" 
                            wire:model.live.debounce.500ms="search"
                            placeholder="Tìm kiếm công thức nấu ăn..."
                            class="w-full px-6 py-4 pr-16 text-lg border-0 rounded-l-2xl shadow-2xl bg-white/95 backdrop-blur-sm text-gray-900 placeholder-gray-500 focus:outline-none focus:ring-4 focus:ring-white/30 transition-all duration-300"
                        />
                        <div class="absolute inset-y-0 right-0 flex items-center pr-4">
                            <x-lucide-search class="w-6 h-6 text-gray-400" />
                        </div>
                    </div>
                    <button 
                        wire:click="performSearch"
                        class="px-8 py-4 bg-white/20 hover:bg-white/30 backdrop-blur-sm text-white font-bold rounded-r-2xl border-l border-white/20 transition-all duration-300 hover:scale-105 shadow-2xl"
                    >
                        <x-lucide-arrow-right class="w-6 h-6" />
                    </button>
                </div>
                
                <!-- Search Suggestions -->
                @if($search && strlen($search) > 2)
                <div class="absolute top-full left-0 right-0 mt-2 bg-white rounded-2xl shadow-2xl border border-gray-200 overflow-hidden z-50" wire:loading.remove>
                    <div class="p-4">
                        <div class="text-sm text-gray-500 mb-2">Gợi ý tìm kiếm:</div>
                        <div class="space-y-2">
                            @php
                            $suggestions = ['Phở bò', 'Bánh mì thịt nướng', 'Cơm tấm sườn bì', 'Bún bò Huế cay'];
                            @endphp
                            @foreach($suggestions as $suggestion)
                            <button 
                                wire:click="$set('search', '{{ $suggestion }}')"
                                class="block w-full text-left px-3 py-2 hover:bg-gray-50 rounded-lg transition-colors text-gray-700"
                            >
                                <x-lucide-search class="w-4 h-4 inline mr-2 text-gray-400" />
                                {{ $suggestion }}
                            </button>
                            @endforeach
                        </div>
                    </div>
                </div>
                @endif
            </div>
        </div>
    </section>

    <!-- Main Content Section -->
    <section class="relative py-12 bg-gradient-to-br from-gray-50 via-white to-gray-100 dark:from-slate-900 dark:via-slate-800 dark:to-slate-900">
        <!-- Background Decorations -->
        <div class="absolute inset-0 overflow-hidden pointer-events-none">
            <div class="absolute top-20 left-10 w-32 h-32 bg-gradient-to-r from-blue-200 to-cyan-200 dark:from-blue-800/30 dark:to-cyan-800/30 rounded-full blur-3xl opacity-30 animate-pulse"></div>
            <div class="absolute bottom-20 right-20 w-40 h-40 bg-gradient-to-r from-purple-200 to-pink-200 dark:from-purple-800/30 dark:to-pink-800/30 rounded-full blur-3xl opacity-20 animate-bounce" style="animation-delay: 1s"></div>
        </div>

        <!-- Controls Header -->
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mb-8 relative z-10">
            <div class="bg-white/80 dark:bg-slate-800/80 backdrop-blur-sm rounded-2xl shadow-xl border border-gray-200/50 dark:border-slate-700/50 p-6">
                <div class="flex flex-col lg:flex-row items-center justify-between gap-6">
                    <!-- Results Info -->
                    <div class="flex items-center space-x-4">
                        <div class="inline-flex items-center px-4 py-2 rounded-full bg-gradient-to-r from-blue-100 to-cyan-100 dark:from-blue-900/30 dark:to-cyan-900/30 border border-blue-200 dark:border-blue-800">
                            <x-lucide-book-open class="w-4 h-4 mr-2 text-blue-500" />
                            <span class="text-sm font-semibold text-blue-600 dark:text-blue-400">{{ $recipes->total() }} công thức</span>
                        </div>
                        @if (
                            ($search ?? '') ||
                                ($category ?? '') ||
                                ($difficulty ?? '') ||
                                ($cookingTime ?? '') ||
                                !empty($selectedTags ?? []) ||
                                ($minRating ?? '') ||
                                ($maxCalories ?? '') ||
                                ($servings ?? ''))
                            <div class="inline-flex items-center px-4 py-2 rounded-full bg-gradient-to-r from-orange-100 to-red-100 dark:from-orange-900/30 dark:to-red-900/30 border border-orange-200 dark:border-orange-800">
                                <x-lucide-filter class="w-4 h-4 mr-2 text-orange-500" />
                                <span class="text-sm font-semibold text-orange-600 dark:text-orange-400">Đang lọc</span>
                            </div>
                        @endif
                </div>

                <!-- Export and View Mode Controls -->
                <div class="flex items-center space-x-3">
                    <!-- Export Dropdown -->
                    <div class="relative" x-data="{ open: false }">
                        {{-- <button @click="open = !open" 
                                class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg transition-colors flex items-center">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                            Xuất dữ liệu
                        </button> --}}
                        
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
                    <div class="flex border border-gray-300 dark:border-slate-600 rounded-xl overflow-hidden bg-white dark:bg-slate-700 shadow-sm">
                        <button 
                            class="px-4 py-3 text-sm font-semibold transition-all duration-300 flex items-center {{ $viewMode === 'grid' ? 'bg-gradient-to-r from-blue-500 to-cyan-600 text-white shadow-md' : 'text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-slate-600' }}"
                            wire:click="$set('viewMode', 'grid')"
                        >
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z" />
                            </svg>
                            Lưới
                        </button>
                        <button 
                            class="px-4 py-3 text-sm font-semibold transition-all duration-300 flex items-center {{ $viewMode === 'list' ? 'bg-gradient-to-r from-blue-500 to-cyan-600 text-white shadow-md' : 'text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-slate-600' }}"
                            wire:click="$set('viewMode', 'list')"
                        >
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16" />
                            </svg>
                            Danh sách
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Enhanced Filters Section -->
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mb-8 relative z-10" data-filters-section>
            <div class="bg-white/80 dark:bg-slate-800/80 backdrop-blur-sm rounded-2xl shadow-xl border border-gray-200/50 dark:border-slate-700/50 p-8">
                <!-- Filters Header -->
                <div class="flex items-center justify-between mb-8">
                    <div class="flex items-center">
                        <div class="w-12 h-12 bg-gradient-to-r from-purple-500 to-indigo-600 rounded-xl flex items-center justify-center mr-4">
                            <x-lucide-filter class="w-6 h-6 text-white" />
                        </div>
                        <div>
                            <h3 class="text-xl font-bold text-gray-800 dark:text-white">Bộ lọc nâng cao</h3>
                            <p class="text-sm text-gray-600 dark:text-gray-400">Tùy chỉnh để tìm công thức phù hợp nhất</p>
                        </div>
                    </div>
                    <button 
                        wire:click="clearFilters"
                        class="inline-flex items-center px-4 py-2 bg-gradient-to-r from-red-500 to-pink-600 hover:from-red-600 hover:to-pink-700 text-white font-semibold rounded-xl transition-all duration-300 hover:scale-105 shadow-lg hover:shadow-xl"
                    >
                        <x-lucide-rotate-ccw class="w-4 h-4 mr-2" />
                        Xóa tất cả
                    </button>
                </div>

                <!-- Main Filters Grid -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                    <!-- Category Filter -->
                    <div class="space-y-3">
                        <label class="flex items-center text-sm font-semibold text-gray-700 dark:text-gray-300">
                            <div class="w-6 h-6 bg-gradient-to-r from-blue-500 to-cyan-600 rounded-lg flex items-center justify-center mr-2">
                                <svg class="w-3 h-3 text-white" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M7 3a1 1 0 000 2h6a1 1 0 100-2H7zM4 7a1 1 0 011-1h10a1 1 0 110 2H5a1 1 0 01-1-1zM2 11a1 1 0 011-1h14a1 1 0 110 2H3a1 1 0 01-1-1z"/>
                                </svg>
                            </div>
                            Danh mục
                        </label>
                        <select 
                            wire:model.live="category"
                            class="w-full px-4 py-3 border border-gray-300 dark:border-slate-600 rounded-xl shadow-sm bg-white dark:bg-slate-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition-all duration-300"
                        >
                            <option value="">Tất cả danh mục</option>
                            @foreach($categories as $cat)
                                <option value="{{ $cat->slug }}">{{ $cat->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Difficulty Filter -->
                    <div class="space-y-3">
                        <label class="flex items-center text-sm font-semibold text-gray-700 dark:text-gray-300">
                            <div class="w-6 h-6 bg-gradient-to-r from-green-500 to-emerald-600 rounded-lg flex items-center justify-center mr-2">
                                <svg class="w-3 h-3 text-white" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                </svg>
                            </div>
                            Độ khó
                        </label>
                        <select 
                            wire:model.live="difficulty"
                            class="w-full px-4 py-3 border border-gray-300 dark:border-slate-600 rounded-xl shadow-sm bg-white dark:bg-slate-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition-all duration-300"
                        >
                            <option value="">Tất cả độ khó</option>
                            <option value="easy">Dễ</option>
                            <option value="medium">Trung bình</option>
                            <option value="hard">Khó</option>
                        </select>
                    </div>

                    <!-- Cooking Time Filter -->
                    <div class="space-y-3">
                        <label class="flex items-center text-sm font-semibold text-gray-700 dark:text-gray-300">
                            <div class="w-6 h-6 bg-gradient-to-r from-orange-500 to-red-600 rounded-lg flex items-center justify-center mr-2">
                                <svg class="w-3 h-3 text-white" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"/>
                                </svg>
                            </div>
                            Thời gian nấu
                        </label>
                        <select 
                            wire:model.live="cookingTime"
                            class="w-full px-4 py-3 border border-gray-300 dark:border-slate-600 rounded-xl shadow-sm bg-white dark:bg-slate-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition-all duration-300"
                        >
                            <option value="">Tất cả thời gian</option>
                            <option value="quick">Nhanh (< 30 phút)</option>
                            <option value="medium">Trung bình (30-60 phút)</option>
                            <option value="long">Lâu (> 60 phút)</option>
                        </select>
                    </div>

                    <!-- Sort Filter -->
                    <div class="space-y-3">
                        <label class="flex items-center text-sm font-semibold text-gray-700 dark:text-gray-300">
                            <div class="w-6 h-6 bg-gradient-to-r from-pink-500 to-rose-600 rounded-lg flex items-center justify-center mr-2">
                                <svg class="w-3 h-3 text-white" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M3 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zM3 10a1 1 0 011-1h6a1 1 0 110 2H4a1 1 0 01-1-1zM3 16a1 1 0 011-1h4a1 1 0 110 2H4a1 1 0 01-1-1z"/>
                                </svg>
                            </div>
                            Sắp xếp
                        </label>
                        <select 
                            wire:model.live="sort"
                            class="w-full px-4 py-3 border border-gray-300 dark:border-slate-600 rounded-xl shadow-sm bg-white dark:bg-slate-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition-all duration-300"
                        >
                            <option value="latest">Mới nhất</option>
                            <option value="popular">Phổ biến</option>
                            <option value="rating">Đánh giá cao</option>
                            <option value="oldest">Cũ nhất</option>
                        </select>
                    </div>
                </div>

                <!-- Advanced Filters Toggle -->
                @if($showAdvancedFilters)
                <div class="border-t border-gray-200 dark:border-slate-700 pt-8">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <!-- Rating Filter -->
                        <div class="space-y-3">
                            <label class="flex items-center text-sm font-semibold text-gray-700 dark:text-gray-300">
                                <x-lucide-star class="w-4 h-4 mr-2 text-yellow-500" />
                                Đánh giá tối thiểu
                            </label>
                            <select 
                                wire:model.live="minRating"
                                class="w-full px-4 py-3 border border-gray-300 dark:border-slate-600 rounded-xl shadow-sm bg-white dark:bg-slate-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition-all duration-300"
                            >
                                <option value="">Tất cả đánh giá</option>
                                <option value="4">4+ sao</option>
                                <option value="3">3+ sao</option>
                                <option value="2">2+ sao</option>
                            </select>
                        </div>

                        <!-- Calories Filter -->
                        <div class="space-y-3">
                            <label class="flex items-center text-sm font-semibold text-gray-700 dark:text-gray-300">
                                <svg class="w-4 h-4 mr-2 text-red-500" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M17.293 13.293A8 8 0 016.707 2.707a8.001 8.001 0 1010.586 10.586z"/>
                                </svg>
                                Calories tối đa
                            </label>
                            <input 
                                type="number" 
                                wire:model.live="maxCalories"
                                placeholder="VD: 500"
                                class="w-full px-4 py-3 border border-gray-300 dark:border-slate-600 rounded-xl shadow-sm bg-white dark:bg-slate-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition-all duration-300"
                            />
                        </div>

                        <!-- Servings Filter -->
                        <div class="space-y-3">
                            <label class="flex items-center text-sm font-semibold text-gray-700 dark:text-gray-300">
                                <x-lucide-users class="w-4 h-4 mr-2 text-blue-500" />
                                Số người ăn
                            </label>
                            <select 
                                wire:model.live="servings"
                                class="w-full px-4 py-3 border border-gray-300 dark:border-slate-600 rounded-xl shadow-sm bg-white dark:bg-slate-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition-all duration-300"
                            >
                                <option value="">Tất cả</option>
                                <option value="1">1 người</option>
                                <option value="2">2 người</option>
                                <option value="4">4 người</option>
                                <option value="6">6+ người</option>
                            </select>
                        </div>
                    </div>
                </div>
                        @endif

                <!-- Toggle Advanced Filters -->
                <div class="border-t border-gray-200 dark:border-slate-700 pt-6 text-center">
                    <button 
                        wire:click="toggleAdvancedFilters"
                        class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-purple-500 to-indigo-600 hover:from-purple-600 hover:to-indigo-700 text-white font-semibold rounded-xl transition-all duration-300 hover:scale-105 shadow-lg hover:shadow-xl"
                    >
                        <x-lucide-filter class="w-5 h-5 mr-2" />
                        {{ $showAdvancedFilters ? 'Ẩn bộ lọc nâng cao' : 'Hiện bộ lọc nâng cao' }}
                    </button>
            </div>
        </div>
    </div>

        <!-- Main Content Container -->
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
            <!-- Main Content -->
            <div class="w-full" data-results-section>
                    <!-- Active Filters Display -->
                            @if (
                                ($search ?? '') ||
                                    ($category ?? '') ||
                                    ($difficulty ?? '') ||
                                    ($cookingTime ?? '') ||
                                    !empty($selectedTags ?? []) ||
                                    ($minRating ?? '') ||
                                    ($maxCalories ?? '') ||
                                    ($servings ?? ''))
                        <div class="bg-gradient-to-r from-blue-50 to-cyan-50 dark:from-blue-900/20 dark:to-cyan-900/20 rounded-xl p-4 mb-6 border border-blue-200 dark:border-blue-800">
                            <div class="flex flex-wrap items-center gap-3">
                                <div class="flex items-center">
                                    <x-lucide-filter class="w-4 h-4 mr-2 text-blue-500" />
                                    <span class="text-sm font-semibold text-blue-700 dark:text-blue-400">Bộ lọc đang áp dụng:</span>
                        </div>
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
                    @endif

                    <!-- Enhanced Recipes Grid/List -->
                @if ($recipes->count() > 0)
                        <div class="{{ $viewMode === 'grid' ? 'grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6' : 'space-y-6' }}">
                            @foreach ($recipes as $index => $recipe)
                                <div class="animate-fade-in-up" style="animation-delay: {{ $index * 50 }}ms">
                            @if ($viewMode === 'grid')
                                <x-recipe-grid-card :recipe="$recipe" />
                            @else
                                <x-recipe-list-item :recipe="$recipe" />
                            @endif
                                </div>
                        @endforeach
                    </div>

                        <!-- Enhanced Pagination -->
                        <div class="mt-12">
                            <div class="bg-white/80 dark:bg-slate-800/80 backdrop-blur-sm rounded-2xl shadow-lg border border-gray-200/50 dark:border-slate-700/50 p-6">
                                <div class="flex flex-col sm:flex-row items-center justify-between gap-6">
                                    <!-- Pagination Info -->
                                    <div class="flex items-center gap-4">
                                        <div class="text-sm text-gray-600 dark:text-gray-400">
                                            Hiển thị <span class="font-semibold text-gray-900 dark:text-white">{{ $recipes->firstItem() ?? 0 }}</span> - 
                                            <span class="font-semibold text-gray-900 dark:text-white">{{ $recipes->lastItem() ?? 0 }}</span> 
                                            trong tổng số <span class="font-semibold text-gray-900 dark:text-white">{{ $recipes->total() }}</span> công thức
                                        </div>
                                    </div>

                                    <!-- Pagination Links -->
                                    <div class="flex items-center gap-2">
                                        @if ($recipes->hasPages())
                                            <!-- Previous Button -->
                                            @if ($recipes->onFirstPage())
                                                <span class="px-3 py-2 text-sm text-gray-400 dark:text-gray-600 cursor-not-allowed">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                                                    </svg>
                                                </span>
                                            @else
                                                <button wire:click="previousPage" class="px-3 py-2 text-sm bg-white dark:bg-slate-700 border border-gray-300 dark:border-slate-600 rounded-lg text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-slate-600 transition-colors">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                                                    </svg>
                                                </button>
                                            @endif

                                            <!-- Page Numbers -->
                                            @foreach ($recipes->getUrlRange(1, $recipes->lastPage()) as $page => $url)
                                                @if ($page == $recipes->currentPage())
                                                    <span class="px-4 py-2 text-sm bg-gradient-to-r from-purple-500 to-indigo-600 text-white rounded-lg font-semibold">
                                                        {{ $page }}
                                                    </span>
                                                @else
                                                    <button wire:click="gotoPage({{ $page }})" class="px-4 py-2 text-sm bg-white dark:bg-slate-700 border border-gray-300 dark:border-slate-600 rounded-lg text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-slate-600 transition-colors">
                                                        {{ $page }}
                                                    </button>
                                                @endif
                                            @endforeach

                                            <!-- Next Button -->
                                            @if ($recipes->hasMorePages())
                                                <button wire:click="nextPage" class="px-3 py-2 text-sm bg-white dark:bg-slate-700 border border-gray-300 dark:border-slate-600 rounded-lg text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-slate-600 transition-colors">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                                    </svg>
                                                </button>
                                            @else
                                                <span class="px-3 py-2 text-sm text-gray-400 dark:text-gray-600 cursor-not-allowed">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                                    </svg>
                                                </span>
                                            @endif
                                        @endif
                                    </div>
                                </div>
                            </div>
                    </div>
                @else
                        <!-- Enhanced Empty State -->
                        <div class="text-center py-20">
                            <div class="w-24 h-24 bg-gradient-to-r from-gray-100 to-gray-200 dark:from-slate-700 dark:to-slate-800 rounded-2xl flex items-center justify-center mx-auto mb-6">
                                <x-lucide-search class="w-12 h-12 text-gray-400 dark:text-gray-500" />
                            </div>
                            <h3 class="text-2xl font-bold text-gray-800 dark:text-white mb-4">Không tìm thấy công thức</h3>
                            <p class="text-lg text-gray-600 dark:text-gray-400 mb-8 max-w-md mx-auto">
                                Thử thay đổi bộ lọc hoặc tìm kiếm với từ khóa khác để khám phá những món ăn tuyệt vời.
                            </p>
                            <button 
                                wire:click="clearFilters"
                                class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-orange-500 to-red-600 hover:from-orange-600 hover:to-red-700 text-white font-bold rounded-xl shadow-lg hover:shadow-xl transform hover:scale-105 transition-all duration-300"
                            >
                                <x-lucide-rotate-ccw class="w-5 h-5 mr-2" />
                                Xóa tất cả bộ lọc
                            </button>
                        </div>
                @endif
            </div>
        </div>
    </div>
    </section>



    <!-- Export Modal -->
    <x-export-modal />

    <!-- Smooth Scroll Script -->
    <script>
        document.addEventListener('livewire:init', () => {
            // Store original scroll position
            let shouldPreventScroll = false;
            let originalScrollPosition = 0;

            // Before Livewire update, store scroll position
            Livewire.hook('morph.updating', ({ el, component, toEl, childrenOnly, skip }) => {
                // Check if this is a filter update (not pagination)
                if (component.fingerprint.name === 'recipes.recipe-list') {
                    shouldPreventScroll = true;
                    originalScrollPosition = window.pageYOffset;
                }
            });

            // After Livewire update, restore scroll position if needed
            Livewire.hook('morph.updated', ({ el, component }) => {
                if (shouldPreventScroll && component.fingerprint.name === 'recipes.recipe-list') {
                    // Small delay to ensure DOM is updated
                    setTimeout(() => {
                        window.scrollTo(0, originalScrollPosition);
                        shouldPreventScroll = false;
                    }, 10);
                }
            });

            // Scroll to filters when clearing filters
            Livewire.on('scroll-to-filters', () => {
                shouldPreventScroll = false; // Allow this scroll
                const filtersSection = document.querySelector('[data-filters-section]');
                if (filtersSection) {
                    filtersSection.scrollIntoView({ 
                        behavior: 'smooth', 
                        block: 'center' 
                    });
                }
            });

            // Scroll to results when performing search
            Livewire.on('scroll-to-results', () => {
                shouldPreventScroll = false; // Allow this scroll
                const resultsSection = document.querySelector('[data-results-section]');
                if (resultsSection) {
                    resultsSection.scrollIntoView({ 
                        behavior: 'smooth', 
                        block: 'start' 
                    });
                }
            });

            // Handle pagination scroll
            Livewire.on('scroll-to-top', (data) => {
                shouldPreventScroll = false; // Allow pagination scroll
                if (data && data.fromPagination) {
                    window.scrollTo({ 
                        top: 0, 
                        behavior: 'smooth' 
                    });
                }
            });
        });
    </script>
</div>
