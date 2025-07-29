<div>
    <!-- Image Upload Section -->
    @if($searchImage)
        <div class="mb-4 p-4 bg-white rounded-lg shadow-lg">
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

    <!-- Search Bar with Image Upload -->
    <div class="relative">
        <input
            type="text"
            class="w-full pl-12 pr-32 py-4 text-gray-900 rounded-lg shadow-lg text-lg"
            placeholder="Tìm kiếm món ăn yêu thích..."
            wire:model.live="search"
            wire:keydown.enter="performSearch"
        />
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
            
            <button 
                class="px-6 py-2 bg-orange-500 hover:bg-orange-600 text-white rounded-r-lg transition-colors"
                wire:click="performSearch"
            >
                Tìm kiếm
            </button>
        </div>
    </div>
</div> 