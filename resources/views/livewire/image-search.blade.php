<div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
    <div class="text-center mb-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-2">Tìm kiếm theo ảnh</h3>
        <p class="text-sm text-gray-600">Tải lên ảnh món ăn để tìm công thức tương tự</p>
    </div>

    <!-- Image Upload Section -->
    @if($searchImage)
        <div class="mb-4 p-4 bg-orange-50 rounded-lg border border-orange-200">
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
                        @if(!empty($searchQuery))
                            <div class="mt-3">
                                <button 
                                    wire:click="performSearch"
                                    class="px-4 py-2 bg-green-500 hover:bg-green-600 text-white rounded-lg transition-colors text-sm"
                                >
                                    Tìm kiếm với "{{ $searchQuery }}"
                                </button>
                            </div>
                        @endif
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

    <!-- Upload Area -->
    @if(!$searchImage)
        <div class="border-2 border-dashed border-gray-300 rounded-lg p-6 text-center hover:border-orange-400 transition-colors">
            <label for="image-upload" class="cursor-pointer">
                <div class="space-y-4">
                    <div class="mx-auto w-16 h-16 bg-orange-100 rounded-full flex items-center justify-center">
                        <svg class="w-8 h-8 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                    </div>
                    <div>
                        <p class="text-lg font-medium text-gray-900">Tải lên ảnh món ăn</p>
                        <p class="text-sm text-gray-500">JPG, PNG hoặc GIF tối đa 5MB</p>
                    </div>
                </div>
            </label>
            <input 
                id="image-upload" 
                type="file" 
                wire:model="searchImage" 
                accept="image/*" 
                class="hidden"
            />
        </div>
    @endif

    <!-- Tips -->
    <div class="mt-4 p-4 bg-blue-50 rounded-lg">
        <h4 class="text-sm font-medium text-blue-900 mb-2">💡 Mẹo tìm kiếm</h4>
        <ul class="text-xs text-blue-800 space-y-1">
            <li>• Chọn ảnh rõ nét của món ăn</li>
            <li>• Tránh ảnh có nhiều món ăn khác nhau</li>
            <li>• Ảnh có ánh sáng tốt sẽ cho kết quả chính xác hơn</li>
        </ul>
    </div>
</div> 