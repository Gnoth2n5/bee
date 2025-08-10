<div>
    @if($showModal)
    <div class="fixed inset-0 z-50 overflow-y-auto"
         aria-labelledby="modal-title"
         role="dialog"
         aria-modal="true"
         x-data
         @keydown.escape.window="$wire.closeModal()"
         @keydown.enter.window="if ($wire.ingredientVi.trim() && !$event.target.closest('button')) $wire.findSubstitutes()"
         wire:init="loadSearchHistoryFromLocalStorage"
         x-init="
             $wire.on('save-search-history-localstorage', (data) => {
                 localStorage.setItem('ingredient_search_history', JSON.stringify(data.history));
             });
             
             $wire.on('clear-search-history-localstorage', () => {
                 localStorage.removeItem('ingredient_search_history');
             });
             
             $wire.on('load-search-history-localstorage', () => {
                 const history = localStorage.getItem('ingredient_search_history');
                 if (history) {
                     $wire.set('searchHistory', JSON.parse(history));
                 }
             });
         ">
        
        <!-- Background overlay -->
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"
             wire:click="closeModal"></div>

        <!-- Modal panel -->
        <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
            <div class="relative transform overflow-hidden rounded-lg bg-white text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-4xl"
                 wire:click.stop>
                
                <!-- Modal Header -->
                <div class="bg-gradient-to-r from-green-500 to-emerald-600 px-6 py-4">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <x-heroicon-s-magnifying-glass class="h-6 w-6 text-white" />
                            </div>
                            <div class="ml-3">
                                <h3 class="text-lg font-medium text-white" id="modal-title">
                                    Tìm Nguyên Liệu Thay Thế
                                </h3>
                                <p class="text-sm text-green-100">
                                    Nhập nguyên liệu để tìm những lựa chọn thay thế
                                </p>
                            </div>
                        </div>
                        <button type="button" 
                                wire:click="closeModal"
                                class="rounded-md text-green-100 hover:text-white focus:outline-none focus:ring-2 focus:ring-white">
                            <span class="sr-only">Đóng</span>
                            <x-heroicon-s-x-mark class="h-6 w-6" />
                        </button>
                    </div>
                </div>

                <!-- Modal Content -->
                <div class="px-6 py-4 max-h-96 overflow-y-auto">
                    <!-- Search Form -->
                    <form wire:submit.prevent="findSubstitutes" class="mb-6">
                        <div class="flex gap-3">
                            <div class="flex-1">
                                <div class="relative">
                                    <input 
                                        type="text" 
                                        id="ingredient-input"
                                        wire:model="ingredientVi"
                                        placeholder="Ví dụ: bơ, trứng gà, hành tây..."
                                        class="w-full px-4 py-3 pl-10 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500 text-base"
                                        {{ $loading ? 'disabled' : '' }}
                                        x-data
                                        x-init="$nextTick(() => $el.focus())"
                                    >
                                    <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                                        <x-heroicon-s-magnifying-glass class="h-5 w-5 text-gray-400" />
                                    </div>
                                </div>
                                @error('ingredientVi')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                            
                            <button 
                                type="submit"
                                class="inline-flex items-center px-6 py-3 bg-green-600 text-white font-medium rounded-lg hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 transition-colors disabled:opacity-50 disabled:cursor-not-allowed whitespace-nowrap"
                                {{ $loading ? 'disabled' : '' }}
                            >
                                <svg wire:loading wire:target="findSubstitutes" class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                {{ $loading ? 'Đang tìm...' : 'Tìm kiếm' }}
                            </button>
                        </div>
                    </form>

                    <!-- Quick Examples -->
                    <div class="mb-6">
                        <p class="text-sm text-gray-600 mb-2">Gợi ý nhanh:</p>
                        <div class="flex flex-wrap gap-2">
                            @php
                                $examples = ['bơ', 'trứng gà', 'sữa tươi', 'đường trắng', 'bột mì', 'hành tây'];
                            @endphp
                            @foreach($examples as $example)
                                <button 
                                    type="button"
                                    onclick="document.getElementById('ingredient-input').value = '{{ $example }}'"
                                    class="inline-flex items-center px-3 py-1.5 bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm rounded-full transition-colors"
                                    {{ $loading ? 'disabled' : '' }}
                                >
                                    {{ $example }}
                                </button>
                            @endforeach
                        </div>
                    </div>

                    <!-- Search History -->
                    @if(!empty($searchHistory))
                        <div class="mb-6">
                            <div class="flex items-center justify-between mb-2">
                                <p class="text-sm text-gray-600">Tìm kiếm gần đây:</p>
                                <button 
                                    wire:click="clearSearchHistory"
                                    class="text-xs text-gray-500 hover:text-red-500 transition-colors"
                                >
                                    Xóa lịch sử
                                </button>
                            </div>
                            <div class="flex flex-wrap gap-2">
                                @foreach($searchHistory as $historyItem)
                                    <button 
                                        wire:click="searchFromHistory('{{ $historyItem }}')"
                                        class="inline-flex items-center px-3 py-1.5 bg-blue-50 hover:bg-blue-100 text-blue-700 text-sm rounded-full transition-colors"
                                        {{ $loading ? 'disabled' : '' }}
                                    >
                                        <x-heroicon-s-clock class="w-3 h-3 mr-1" />
                                        {{ $historyItem }}
                                    </button>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    <!-- Messages -->
                    @if($error)
                        <div class="mb-4 bg-red-50 border border-red-200 rounded-lg p-3">
                            <div class="flex">
                                <div class="flex-shrink-0">
                                    <x-heroicon-s-x-circle class="h-5 w-5 text-red-400" />
                                </div>
                                <div class="ml-3">
                                    <p class="text-sm text-red-800">{{ $error }}</p>
                                </div>
                            </div>
                        </div>
                    @endif

                    @if($success)
                        <div class="mb-4 bg-green-50 border border-green-200 rounded-lg p-3">
                            <div class="flex">
                                <div class="flex-shrink-0">
                                    <x-heroicon-s-check-circle class="h-5 w-5 text-green-400" />
                                </div>
                                <div class="ml-3">
                                    <p class="text-sm text-green-800">{{ $success }}</p>
                                </div>
                            </div>
                        </div>
                    @endif

                    <!-- Loading State -->
                    @if($loading)
                        <div class="text-center py-8">
                            <div class="inline-flex items-center px-4 py-2 font-semibold leading-6 text-green-600 transition ease-in-out duration-150">
                                <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-green-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                Đang dịch và tìm kiếm nguyên liệu thay thế...
                            </div>
                            <p class="text-sm text-gray-500 mt-2">Quá trình này có thể mất vài giây</p>
                        </div>
                    @endif

                    <!-- Results -->
                    @if(!empty($substitutes) && !$loading)
                        <div class="space-y-4">
                            <h4 class="text-lg font-semibold text-gray-900 flex items-center">
                                <x-heroicon-s-arrow-path class="w-5 h-5 mr-2 text-green-600" />
                                Nguyên liệu có thể thay thế cho "{{ $ingredientVi }}"
                            </h4>
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                                @foreach($substitutes as $index => $substitute)
                                    <div class="bg-gradient-to-r from-green-50 to-emerald-50 border border-green-200 rounded-lg p-4 hover:shadow-sm transition-shadow">
                                        <div class="flex items-start">
                                            <div class="flex-shrink-0">
                                                <div class="w-6 h-6 bg-green-500 text-white rounded-full flex items-center justify-center text-xs font-semibold">
                                                    {{ $index + 1 }}
                                                </div>
                                            </div>
                                            <div class="ml-3 flex-1">
                                                <h5 class="text-sm font-semibold text-gray-900 mb-1">
                                                    {{ $substitute['name'] }}
                                                </h5>
                                                
                                                @if(!empty($substitute['description']))
                                                    <p class="text-xs text-gray-600">
                                                        {{ $substitute['description'] }}
                                                    </p>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>

                            <!-- Usage Tips -->
                            <div class="mt-4 p-3 bg-yellow-50 border border-yellow-200 rounded-lg">
                                <h5 class="text-sm font-medium text-yellow-800 mb-2">
                                    <x-heroicon-s-light-bulb class="w-4 h-4 inline mr-1" />
                                    Lưu ý khi thay thế:
                                </h5>
                                <ul class="text-xs text-yellow-700 space-y-1">
                                    <li>• Tỉ lệ thay thế có thể khác nhau tùy từng nguyên liệu</li>
                                    <li>• Hương vị và kết cấu món ăn có thể thay đổi</li>
                                    <li>• Nên thử với lượng nhỏ trước khi áp dụng cho toàn bộ công thức</li>
                                </ul>
                            </div>
                        </div>
                    @elseif(!$loading && !$error && empty($substitutes) && !empty(trim($ingredientVi)))
                        <div class="text-center py-8">
                            <div class="text-gray-400 mb-4">
                                <x-heroicon-o-face-frown class="w-12 h-12 mx-auto" />
                            </div>
                            <h4 class="text-base font-medium text-gray-900 mb-2">Không tìm thấy nguyên liệu thay thế</h4>
                            <p class="text-sm text-gray-600">Hãy thử với nguyên liệu khác hoặc kiểm tra lại cách viết.</p>
                        </div>
                    @endif
                </div>

                <!-- Modal Footer -->
                <div class="bg-gray-50 px-6 py-3 flex justify-between items-center">
                    <div class="text-xs text-gray-500">
                        <span class="px-2 py-1 bg-gray-200 text-gray-700 rounded border text-xs font-mono">ESC</span> để đóng • <span class="px-2 py-1 bg-gray-200 text-gray-700 rounded border text-xs font-mono">Enter</span> để tìm kiếm
                    </div>
                    <div class="flex space-x-3">
                        @if(!empty($substitutes))
                            <button 
                                wire:click="resetForm"
                                class="inline-flex items-center px-3 py-2 bg-gray-500 text-white text-sm rounded-md hover:bg-gray-600 transition-colors"
                            >
                                <x-heroicon-s-arrow-path class="w-4 h-4 mr-1" />
                                Tìm mới
                            </button>
                        @endif
                        <button 
                            wire:click="closeModal"
                            class="inline-flex items-center px-4 py-2 bg-white text-gray-700 text-sm font-medium rounded-md border border-gray-300 hover:bg-gray-50 transition-colors"
                        >
                            Đóng
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>
