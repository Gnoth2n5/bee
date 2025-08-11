<div class="relative" x-data="{ open: @entangle('showResults') }" @click.away="open = false">
    <!-- Search Input -->
    <div class="relative">
        <input
            type="text"
            wire:model.live.debounce.300ms="searchQuery"
            placeholder="Tìm kiếm công thức..."
            class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition-colors"
            @focus="open = true"
            @keydown.enter="if($wire.searchQuery) $wire.goToSearchPage()"
        >
        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
            <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
            </svg>
        </div>
        
        @if($searchQuery)
            <button 
                wire:click="clearSearch"
                class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-gray-600"
            >
                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        @endif
    </div>

    <!-- Search Results Dropdown -->
    <div 
        x-show="open" 
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0 transform scale-95"
        x-transition:enter-end="opacity-100 transform scale-100"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100 transform scale-100"
        x-transition:leave-end="opacity-0 transform scale-95"
        class="absolute z-50 w-full mt-2 bg-white rounded-lg shadow-xl border border-gray-200 max-h-96 overflow-y-auto"
    >
        <!-- Loading State -->
        @if($isLoading)
            <div class="p-4 text-center">
                <div class="inline-flex items-center space-x-2">
                    <svg class="animate-spin h-5 w-5 text-orange-500" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    <span class="text-gray-600">Đang tìm kiếm...</span>
                </div>
            </div>
        @else
                         <!-- Search Results -->
             @if(!empty($searchQuery) && $searchResults['recipes']->count() > 0)
                 <div class="p-4">
                     <!-- Results Header -->
                     <div class="flex items-center justify-between mb-3">
                         <h3 class="text-sm font-medium text-gray-900">
                             {{ $searchResults['total'] }} kết quả cho "{{ $searchQuery }}"
                         </h3>
                         @if($searchResults['hasMore'])
                             <button 
                                 wire:click="goToSearchPage"
                                 class="text-sm text-orange-600 hover:text-orange-700 font-medium"
                             >
                                 Xem tất cả
                             </button>
                         @endif
                     </div>

                     <!-- Recipe Results -->
                     <div class="space-y-3">
                                                 @foreach($searchResults['recipes'] as $recipe)
                            <a 
                                href="{{ route('recipes.show', $recipe) }}"
                                class="flex items-center space-x-3 p-2 rounded-lg hover:bg-gray-50 transition-colors"
                                wire:click="$wire.showResults = false"
                            >
                                 @if($recipe->featured_image)
                                     <img 
                                         src="{{ Storage::url($recipe->featured_image) }}" 
                                         alt="{{ $recipe->title }}"
                                         class="w-12 h-12 object-cover rounded-lg"
                                     >
                                 @else
                                     <div class="w-12 h-12 bg-gray-200 rounded-lg flex items-center justify-center">
                                         <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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
                    @if($searchResults['recipes']->count() >= 8)
                    <div class="border-t border-gray-200 p-3">
                        <a 
                            href="{{ route('recipes.index', ['search' => $searchQuery]) }}"
                            class="block text-center text-sm font-medium text-orange-600 hover:text-orange-700 transition-colors"
                            wire:click="$wire.showResults = false"
                        >
                            Xem tất cả kết quả →
                        </a>
                    </div>
                    @endif
                </div>
                 </div>
                         @elseif(!empty($searchQuery) && $searchResults['recipes']->count() == 0)
                 <!-- No Results -->
                 <div class="p-4 text-center">
                     <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                         <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                     </svg>
                     <h3 class="mt-2 text-sm font-medium text-gray-900">Không tìm thấy kết quả</h3>
                     <p class="mt-1 text-sm text-gray-500">Thử tìm kiếm với từ khóa khác</p>
                 </div>
            
            @endif
        @endif
    </div>
</div> 