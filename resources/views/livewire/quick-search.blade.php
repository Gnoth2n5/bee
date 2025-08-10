<div class="relative">
    <!-- Search Input -->
    <div class="relative">
        <input
            type="text"
            wire:model.live.debounce.300ms="searchQuery"
            placeholder="Tìm kiếm công thức nấu ăn..."
            class="w-full pl-12 pr-20 py-4 text-gray-900 rounded-lg border border-gray-300 focus:ring-2 focus:ring-orange-500 focus:border-orange-500 text-lg shadow-lg"
            wire:focus="showSuggestions = true"
            wire:keydown.enter="goToSearchPage"
        >
        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
            <svg class="h-6 w-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
            </svg>
        </div>
        
        <!-- Search Button -->
        <button 
            wire:click="goToSearchPage"
            class="absolute inset-y-0 right-0 px-4 flex items-center bg-orange-500 hover:bg-orange-600 text-white rounded-r-lg transition-colors"
        >
            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
            </svg>
        </button>
        
        @if($searchQuery)
            <button 
                wire:click="clearSearch"
                class="absolute inset-y-0 right-12 pr-2 flex items-center text-gray-400 hover:text-gray-600"
            >
                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        @endif
    </div>

    <!-- Search Suggestions Dropdown -->
    @if($showSuggestions)
    <div 
        class="absolute z-50 w-full mt-2 bg-white rounded-lg shadow-xl border border-gray-200 max-h-96 overflow-y-auto"
        wire:click.away="showSuggestions = false"
    >
        @if(!empty($searchQuery) && $suggestions['recipes']->count() > 0)
            <!-- Search Results -->
            <div class="p-4">
                <div class="space-y-2">
                    @foreach($suggestions['recipes'] as $recipe)
                        <a 
                            href="{{ route('recipes.show', $recipe) }}"
                            class="flex items-center space-x-3 p-2 rounded-lg hover:bg-gray-50 transition-colors"
                            wire:click="showSuggestions = false"
                        >
                            @if($recipe->featured_image)
                                <img 
                                    src="{{ Storage::url($recipe->featured_image) }}" 
                                    alt="{{ $recipe->title }}"
                                    class="w-10 h-10 object-cover rounded-lg"
                                >
                            @else
                                <div class="w-10 h-10 bg-gray-200 rounded-lg flex items-center justify-center">
                                    <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                    </svg>
                                </div>
                            @endif
                            
                            <div class="flex-1 min-w-0">
                                <h4 class="text-sm font-medium text-gray-900 truncate">
                                    {{ $recipe->title }}
                                </h4>
                                <div class="flex items-center space-x-2 text-xs text-gray-500">
                                    <span>{{ $recipe->cooking_time }} phút</span>
                                    <span>•</span>
                                    <span>{{ ucfirst($recipe->difficulty) }}</span>
                                    @if($recipe->average_rating > 0)
                                        <span>•</span>
                                        <div class="flex items-center space-x-1">
                                            <svg class="w-3 h-3 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                            </svg>
                                            <span>{{ number_format($recipe->average_rating, 1) }}</span>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </a>
                    @endforeach
                </div>
                
                <!-- View All Results Button -->
                @if($suggestions['recipes']->count() >= 8)
                <div class="border-t border-gray-200 p-3">
                    <a 
                        href="{{ route('recipes.index', ['search' => $searchQuery]) }}"
                        class="block text-center text-sm font-medium text-orange-600 hover:text-orange-700 transition-colors"
                        wire:click="showSuggestions = false"
                    >
                        Xem tất cả kết quả →
                    </a>
                </div>
                @endif
            </div>
        @elseif(!empty($searchQuery) && $suggestions['recipes']->count() == 0)
            <!-- No Results -->
            <div class="p-4 text-center">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
                <h3 class="mt-2 text-sm font-medium text-gray-900">Không tìm thấy kết quả</h3>
                <p class="mt-1 text-sm text-gray-500">Thử tìm kiếm với từ khóa khác</p>
            </div>
        @endif
    </div>
    @endif
</div> 