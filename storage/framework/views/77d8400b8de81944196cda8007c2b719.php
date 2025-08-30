<div id="ingredient-substitute-modal" class="fixed inset-0 z-50 overflow-y-auto hidden" 
     aria-labelledby="modal-title" 
     role="dialog" 
     aria-modal="true">
     
    <!-- Background overlay -->
    <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" 
         onclick="closeIngredientModal()"></div>

    <!-- Modal panel -->
    <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
        <div class="relative transform overflow-hidden rounded-lg bg-white text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-4xl"
             onclick="event.stopPropagation()">
             
            <!-- Modal Header -->
            <div class="bg-gradient-to-r from-green-500 to-emerald-600 px-6 py-4">
                <div class="flex items-center justify-between">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                            </svg>
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
                            onclick="closeIngredientModal()"
                            class="rounded-md text-green-100 hover:text-white focus:outline-none focus:ring-2 focus:ring-white">
                        <span class="sr-only">Đóng</span>
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
            </div>

            <!-- Modal Content -->
            <div class="px-6 py-4 max-h-96 overflow-y-auto">
                <!-- Search Form -->
                <form id="ingredient-search-form" class="mb-6">
                    <div class="flex gap-3">
                        <div class="flex-1">
                            <div class="relative">
                                <input 
                                    type="text" 
                                    id="ingredient-input"
                                    placeholder="Ví dụ: bơ, trứng gà, hành tây..."
                                    class="w-full px-4 py-3 pl-10 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-green-500 text-base"
                                />
                                <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                                    <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                    </svg>
                                </div>
                            </div>
                            <div id="validation-error" class="mt-1 text-sm text-red-600 hidden"></div>
                        </div>
                        
                        <button 
                            type="submit"
                            id="search-button"
                            class="inline-flex items-center px-6 py-3 bg-green-600 text-white font-medium rounded-lg hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 transition-colors disabled:opacity-50 disabled:cursor-not-allowed whitespace-nowrap"
                        >
                            <svg id="loading-spinner" class="animate-spin -ml-1 mr-2 h-4 w-4 text-white hidden" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            <span id="button-text">Tìm kiếm</span>
                        </button>
                    </div>
                </form>

                <!-- Quick Examples -->
                <div class="mb-6">
                    <p class="text-sm text-gray-600 mb-2">Gợi ý nhanh:</p>
                    <div class="flex flex-wrap gap-2">
                        <button type="button" onclick="setIngredientValue('bơ')" class="inline-flex items-center px-3 py-1.5 bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm rounded-full transition-colors">bơ</button>
                        <button type="button" onclick="setIngredientValue('trứng gà')" class="inline-flex items-center px-3 py-1.5 bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm rounded-full transition-colors">trứng gà</button>
                        <button type="button" onclick="setIngredientValue('sữa tươi')" class="inline-flex items-center px-3 py-1.5 bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm rounded-full transition-colors">sữa tươi</button>
                        <button type="button" onclick="setIngredientValue('đường trắng')" class="inline-flex items-center px-3 py-1.5 bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm rounded-full transition-colors">đường trắng</button>
                        <button type="button" onclick="setIngredientValue('bột mì')" class="inline-flex items-center px-3 py-1.5 bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm rounded-full transition-colors">bột mì</button>
                        <button type="button" onclick="setIngredientValue('hành tây')" class="inline-flex items-center px-3 py-1.5 bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm rounded-full transition-colors">hành tây</button>
                    </div>
                </div>

                <!-- Search History -->
                <div id="search-history-section" class="mb-6" style="display: none;">
                    <div class="flex items-center justify-between mb-2">
                        <p class="text-sm text-gray-600">Tìm kiếm gần đây:</p>
                        <button 
                            onclick="clearSearchHistory()"
                            class="text-xs text-gray-500 hover:text-red-500 transition-colors"
                        >
                            Xóa lịch sử
                        </button>
                    </div>
                    <div id="search-history-list" class="flex flex-wrap gap-2">
                        <!-- History items will be inserted here by JavaScript -->
                    </div>
                </div>

                <!-- Messages -->
                <div id="success-message" class="mb-4 bg-green-50 border border-green-200 rounded-lg p-3 hidden">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-green-400" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                            </svg>
                        </div>
                        <div class="ml-3">
                            <p id="success-text" class="text-sm text-green-800"></p>
                        </div>
                    </div>
                </div>

                <div id="error-message" class="mb-4 bg-red-50 border border-red-200 rounded-lg p-3 hidden">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-red-400" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                            </svg>
                        </div>
                        <div class="ml-3">
                            <p id="error-text" class="text-sm text-red-800"></p>
                        </div>
                    </div>
                </div>

                <!-- Results -->
                <div id="results-section" class="hidden">
                    <h4 class="text-lg font-semibold text-gray-900 flex items-center mb-4">
                        <svg class="w-5 h-5 mr-2 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M4 2a1 1 0 011 1v2.101a7.002 7.002 0 0111.601 2.566 1 1 0 11-1.885.666A5.002 5.002 0 005.999 7H9a1 1 0 010 2H4a1 1 0 01-1-1V3a1 1 0 011-1zm.008 9.057a1 1 0 011.276.61A5.002 5.002 0 0014.001 13H11a1 1 0 110-2h5a1 1 0 011 1v5a1 1 0 11-2 0v-2.101a7.002 7.002 0 01-11.601-2.566 1 1 0 01.61-1.276z" clip-rule="evenodd"></path>
                        </svg>
                        <span id="results-title">Nguyên liệu có thể thay thế</span>
                    </h4>
                    
                    <div id="results-list" class="grid grid-cols-1 md:grid-cols-2 gap-3">
                        <!-- Results will be inserted here by JavaScript -->
                    </div>

                    <!-- Usage Tips -->
                    <div class="mt-4 p-3 bg-yellow-50 border border-yellow-200 rounded-lg">
                        <h5 class="text-sm font-medium text-yellow-800 mb-2">
                            <svg class="w-4 h-4 inline mr-1" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                            </svg>
                            Lưu ý khi thay thế:
                        </h5>
                        <ul class="text-xs text-yellow-700 space-y-1">
                            <li>• Tỉ lệ thay thế có thể khác nhau tùy từng nguyên liệu</li>
                            <li>• Hương vị và kết cấu món ăn có thể thay đổi</li>
                            <li>• Nên thử với lượng nhỏ trước khi áp dụng cho toàn bộ công thức</li>
                        </ul>
                    </div>
                </div>

                <!-- No Results -->
                <div id="no-results-section" class="text-center py-8 hidden">
                    <div class="text-gray-400 mb-4">
                        <svg class="w-12 h-12 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 12h6m-6-4h6m2 5.291A7.962 7.962 0 0112 20a7.962 7.962 0 01-5.657-2.343m0 0A7.953 7.953 0 014 12c0-1.657.502-3.19 1.343-4.657m0 0A7.953 7.953 0 0112 4c0 1.657-.502 3.19-1.343 4.657m15.314 0C21.502 8.81 21 10.343 21 12s-.502 3.19-1.343 4.657M6.343 7.343A7.953 7.953 0 0112 4c1.657 0 3.19.502 4.657 1.343"></path>
                        </svg>
                    </div>
                    <h4 class="text-base font-medium text-gray-900 mb-2">Không tìm thấy nguyên liệu thay thế</h4>
                    <p class="text-sm text-gray-600">Hãy thử với nguyên liệu khác hoặc kiểm tra lại cách viết.</p>
                </div>
            </div>

            <!-- Modal Footer -->
            <div class="bg-gray-50 px-6 py-3 flex justify-between items-center">
                <div class="text-xs text-gray-500">
                    <span class="px-2 py-1 bg-gray-200 text-gray-700 rounded border text-xs font-mono">ESC</span> để đóng • <span class="px-2 py-1 bg-gray-200 text-gray-700 rounded border text-xs font-mono">Enter</span> để tìm kiếm
                </div>
                <div class="flex space-x-3">
                    <button 
                        id="reset-button"
                        onclick="resetForm()"
                        class="items-center px-3 py-2 bg-gray-500 text-white text-sm rounded-md hover:bg-gray-600 transition-colors hidden"
                        style="display: none;"
                    >
                        <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M4 2a1 1 0 011 1v2.101a7.002 7.002 0 0111.601 2.566 1 1 0 11-1.885.666A5.002 5.002 0 005.999 7H9a1 1 0 010 2H4a1 1 0 01-1-1V3a1 1 0 011-1zm.008 9.057a1 1 0 011.276.61A5.002 5.002 0 0014.001 13H11a1 1 0 110-2h5a1 1 0 011 1v5a1 1 0 11-2 0v-2.101a7.002 7.002 0 01-11.601-2.566 1 1 0 01.61-1.276z" clip-rule="evenodd"></path>
                        </svg>
                        Tìm mới
                    </button>
                    <button 
                        onclick="closeIngredientModal()"
                        class="inline-flex items-center px-4 py-2 bg-white text-gray-700 text-sm font-medium rounded-md border border-gray-300 hover:bg-gray-50 transition-colors"
                    >
                        Đóng
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
<?php /**PATH D:\DuAn1\test\bee\resources\views/components/ingredient-substitute-modal.blade.php ENDPATH**/ ?>