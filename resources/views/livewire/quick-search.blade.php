<div class="relative hero-search-wrapper">
    <!-- Hero Search Input -->
    <div class="relative group">
        <div class="relative overflow-hidden rounded-2xl bg-white bg-opacity-10 backdrop-filter backdrop-blur-lg border border-white border-opacity-20 shadow-2xl hover:bg-opacity-15 transition-all duration-300">
            <input
                type="text"
                wire:model.live.debounce.300ms="searchQuery"
                placeholder="Tìm kiếm công thức nấu ăn tuyệt vời..."
                class="w-full pl-14 pr-24 py-5 text-white placeholder-white placeholder-opacity-70 bg-transparent border-0 focus:ring-0 focus:outline-none text-lg font-medium"
                wire:focus="showSuggestions = true"
                wire:keydown.enter="goToSearchPage"
            >
            
            <!-- Search Icon -->
            <div class="absolute inset-y-0 left-0 pl-5 flex items-center pointer-events-none">
                <div class="w-6 h-6 text-white text-opacity-70 group-hover:text-opacity-90 transition-opacity duration-300">
                    <x-lucide-search class="w-6 h-6" />
                </div>
            </div>
            
            <!-- Search Button -->
            <button 
                wire:click="goToSearchPage"
                class="absolute inset-y-0 right-0 px-6 flex items-center bg-gradient-to-r from-orange-500 to-red-500 hover:from-orange-600 hover:to-red-600 text-white rounded-r-2xl transition-all duration-300 transform hover:scale-105 group/btn"
            >
                <x-lucide-search class="w-5 h-5 mr-2 group-hover/btn:scale-110 transition-transform duration-300" />
                <span class="hidden sm:inline font-semibold">Tìm kiếm</span>
            </button>
            
            <!-- Clear Button -->
            @if($searchQuery)
                <button 
                    wire:click="clearSearch"
                    class="absolute inset-y-0 right-20 sm:right-36 flex items-center justify-center w-10 h-full text-white text-opacity-60 hover:text-opacity-90 transition-opacity duration-300 hover:bg-white hover:bg-opacity-10 rounded-l-lg"
                >
                    <x-lucide-x class="w-4 h-4" />
                </button>
            @endif
            
            <!-- Animated Border -->
            <div class="absolute inset-0 rounded-2xl bg-gradient-to-r from-orange-500 via-red-500 to-pink-500 opacity-0 group-hover:opacity-20 transition-opacity duration-300 pointer-events-none"></div>
        </div>
        
        <!-- Quick Suggestions - Only show when search is empty -->
        @if(empty($searchQuery))
        <div class="flex flex-wrap gap-2 mt-3 opacity-0 group-hover:opacity-100 transition-all duration-500 transform translate-y-2 group-hover:translate-y-0">
            <span class="text-xs text-white text-opacity-60 font-medium mr-2 flex items-center">Gợi ý:</span>
            <button wire:click="$set('searchQuery', 'phở')" class="px-3 py-1 text-xs font-medium text-white bg-white bg-opacity-10 rounded-full hover:bg-opacity-20 hover:scale-105 transition-all duration-300 backdrop-filter backdrop-blur-sm">
                🍜 Phở
            </button>
            <button wire:click="$set('searchQuery', 'bánh mì')" class="px-3 py-1 text-xs font-medium text-white bg-white bg-opacity-10 rounded-full hover:bg-opacity-20 hover:scale-105 transition-all duration-300 backdrop-filter backdrop-blur-sm">
                🥖 Bánh mì
            </button>
            <button wire:click="$set('searchQuery', 'cơm rang')" class="px-3 py-1 text-xs font-medium text-white bg-white bg-opacity-10 rounded-full hover:bg-opacity-20 hover:scale-105 transition-all duration-300 backdrop-filter backdrop-blur-sm">
                🍚 Cơm rang
            </button>
            <button wire:click="$set('searchQuery', 'chả cá')" class="px-3 py-1 text-xs font-medium text-white bg-white bg-opacity-10 rounded-full hover:bg-opacity-20 hover:scale-105 transition-all duration-300 backdrop-filter backdrop-blur-sm">
                🐟 Chả cá
            </button>
        </div>
        @endif
    </div>
</div>