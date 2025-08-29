<div>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="bg-gradient-to-br from-blue-50 to-indigo-100 rounded-2xl shadow-xl p-8">
            <div class="text-center mb-10">
                <div class="inline-flex items-center justify-center w-20 h-20 bg-gradient-to-r from-blue-500 to-indigo-600 rounded-full mb-6">
                    <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <h1 class="text-4xl font-bold text-gray-900 mb-4">
                    🏥 Phân tích bệnh án & Đề xuất món ăn
                </h1>
                <p class="text-lg text-gray-600 max-w-2xl mx-auto">
                    Tải lên hình ảnh bệnh án để nhận đề xuất món ăn phù hợp với tình trạng sức khỏe của bạn
                </p>
            </div>

            <div class="mb-10">
                <div class="bg-white rounded-2xl border-2 border-dashed border-blue-300 p-8 text-center hover:border-blue-400 transition-colors">
                    <div class="mb-6">
                        <div class="inline-flex items-center justify-center w-16 h-16 bg-blue-100 rounded-full mb-4">
                            <svg class="w-8 h-8 text-blue-600" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                                <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                            </svg>
                        </div>
                        <h3 class="text-xl font-semibold text-gray-900 mb-2">Tải lên bệnh án</h3>
                        <p class="text-gray-600 mb-6">Chọn hình ảnh bệnh án để bắt đầu phân tích</p>
                    </div>
                    
                    @if($medicalImage)
                        <!-- Preview ảnh đã tải -->
                        <div class="mb-6">
                            <div class="relative inline-block">
                                <img src="{{ $medicalImage->temporaryUrl() }}" 
                                     alt="Preview" 
                                     class="max-w-full h-auto max-h-64 rounded-lg shadow-lg border-2 border-blue-200">
                                <button wire:click="$set('medicalImage', null)" 
                                        class="absolute -top-2 -right-2 bg-red-500 hover:bg-red-600 text-white rounded-full w-8 h-8 flex items-center justify-center shadow-lg transition-colors">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                    </svg>
                                </button>
                            </div>
                            <p class="text-sm text-gray-600 mt-2">{{ $medicalImage->getClientOriginalName() }}</p>
                        </div>
                    @else
                        <!-- Upload area khi chưa có ảnh -->
                        <div class="mb-6">
                            <label for="medical-image" class="cursor-pointer">
                                <span class="bg-gradient-to-r from-blue-500 to-indigo-600 hover:from-blue-600 hover:to-indigo-700 text-white px-8 py-4 rounded-xl font-semibold text-lg shadow-lg hover:shadow-xl transition-all duration-200 transform hover:scale-105">
                                    📁 Chọn hình ảnh bệnh án
                                </span>
                            </label>
                            <input wire:model="medicalImage" type="file" id="medical-image" class="hidden" accept="image/*">
                        </div>
                    @endif
                    
                    <div class="bg-blue-50 rounded-lg p-4 max-w-md mx-auto">
                        <p class="text-sm text-blue-700 font-medium">
                            📋 Hỗ trợ: JPG, PNG, GIF (tối đa 5MB)
                        </p>
                    </div>
                </div>

                @error('medicalImage')
                    <div class="mt-4 bg-red-50 border border-red-200 rounded-lg p-4">
                        <p class="text-red-600 text-sm">{{ $message }}</p>
                    </div>
                @enderror

                @if($medicalImage)
                    <div class="mt-6 text-center">
                        <button wire:click="analyzeImage" 
                                wire:loading.attr="disabled"
                                class="bg-gradient-to-r from-green-500 to-emerald-600 hover:from-green-600 hover:to-emerald-700 text-white px-8 py-4 rounded-xl font-semibold text-lg shadow-lg hover:shadow-xl transition-all duration-200 transform hover:scale-105 disabled:opacity-50 disabled:transform-none">
                            <span wire:loading.remove>🔍 Bắt đầu phân tích</span>
                            <span wire:loading>⏳ Đang phân tích...</span>
                        </button>
                    </div>
                @endif
            </div>

            @if($analysisResult)
                <div class="mb-10 bg-white rounded-2xl shadow-lg p-8 border border-gray-100">
                    <div class="flex items-center mb-6">
                        <div class="w-12 h-12 bg-gradient-to-r from-blue-500 to-indigo-600 rounded-full flex items-center justify-center mr-4">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                            </svg>
                        </div>
                        <h2 class="text-2xl font-bold text-gray-900">📋 Kết quả phân tích</h2>
                    </div>
                    
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                        <div class="bg-gradient-to-br from-red-50 to-pink-50 rounded-xl p-6 border border-red-100">
                            <h3 class="font-semibold text-red-800 mb-4 flex items-center">
                                <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                                </svg>
                                Bệnh được phát hiện
                            </h3>
                            <ul class="space-y-2">
                                @foreach($analysisResult['diseases'] ?? [] as $disease)
                                    <li class="flex items-center text-red-700">
                                        <span class="w-2 h-2 bg-red-500 rounded-full mr-3"></span>
                                        {{ $disease }}
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                        
                        <div class="bg-gradient-to-br from-yellow-50 to-orange-50 rounded-xl p-6 border border-yellow-100">
                            <h3 class="font-semibold text-yellow-800 mb-4 flex items-center">
                                <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                                </svg>
                                Triệu chứng
                            </h3>
                            <ul class="space-y-2">
                                @foreach($analysisResult['symptoms'] ?? [] as $symptom)
                                    <li class="flex items-center text-yellow-700">
                                        <span class="w-2 h-2 bg-yellow-500 rounded-full mr-3"></span>
                                        {{ $symptom }}
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    </div>

                    @if(isset($analysisResult['lab_results']))
                        <div class="mt-8">
                            <h3 class="text-xl font-semibold text-gray-900 mb-6 flex items-center">
                                <svg class="w-6 h-6 mr-3 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                                </svg>
                                📊 Kết quả xét nghiệm máu
                            </h3>
                            <div class="bg-white rounded-xl border border-gray-200 p-6">
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    @foreach($analysisResult['lab_results'] as $test => $result)
                                        @if($result)
                                            <div class="bg-gradient-to-r from-gray-50 to-gray-100 rounded-lg p-4 border-l-4 border-{{ $result['status_color'] }}-500">
                                                <div class="flex justify-between items-center mb-2">
                                                    <span class="font-semibold text-gray-900">{{ ucfirst(str_replace('_', ' ', $test)) }}</span>
                                                    <span class="text-{{ $result['status_color'] }}-600 font-bold text-lg">{{ $result['value'] }} {{ $result['unit'] }}</span>
                                                </div>
                                                <div class="text-sm text-gray-600 mb-1">
                                                    Bình thường: {{ $result['normal_text'] }}
                                                </div>
                                                <div class="text-sm font-semibold text-{{ $result['status_color'] }}-600">
                                                    {{ $result['status'] }}
                                                </div>
                                            </div>
                                        @endif
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    @endif

                    @if(!empty($matchingDiseases))
                        <div class="mt-8">
                            <h3 class="text-xl font-semibold text-gray-900 mb-4">Bệnh tương ứng trong hệ thống:</h3>
                            <div class="flex flex-wrap gap-3">
                                @foreach($matchingDiseases as $disease)
                                    <button wire:click="selectDisease({{ $disease->id }})"
                                            class="bg-gradient-to-r from-orange-500 to-red-500 hover:from-orange-600 hover:to-red-600 text-white px-4 py-2 rounded-full text-sm font-medium shadow-md hover:shadow-lg transition-all duration-200 transform hover:scale-105">
                                        {{ $disease->name }}
                                    </button>
                                @endforeach
                            </div>
                        </div>
                    @else
                        <div class="mt-8 text-center">
                            <button wire:click="createDiseaseFromAnalysis"
                                    class="bg-gradient-to-r from-green-500 to-emerald-600 hover:from-green-600 hover:to-emerald-700 text-white px-6 py-3 rounded-xl text-sm font-semibold shadow-lg hover:shadow-xl transition-all duration-200 transform hover:scale-105">
                                ➕ Tạo bệnh mới từ kết quả phân tích
                            </button>
                        </div>
                    @endif
                </div>
            @endif

            @if(!$selectedDisease && !empty($diseaseConditions))
                <div class="mb-10">
                    <h2 class="text-2xl font-bold text-gray-900 mb-6 text-center">🏥 Hoặc chọn bệnh từ danh sách</h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        @foreach($diseaseConditions as $disease)
                            <button wire:click="selectDisease({{ $disease->id }})"
                                    class="bg-white border-2 border-gray-200 hover:border-blue-500 rounded-xl p-6 text-left transition-all duration-200 transform hover:scale-105 hover:shadow-lg">
                                <div class="flex items-center mb-3">
                                    <div class="w-10 h-10 bg-gradient-to-r from-blue-500 to-indigo-600 rounded-full flex items-center justify-center mr-3">
                                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
                                        </svg>
                                    </div>
                                    <h3 class="font-semibold text-gray-900">{{ $disease->name }}</h3>
                                </div>
                                <p class="text-sm text-gray-600">{{ Str::limit($disease->description, 100) }}</p>
                            </button>
                        @endforeach
                    </div>
                </div>
            @endif

            @if($showRecommendations && $selectedDisease)
                <div class="mb-10">
                    <div class="flex items-center justify-between mb-8">
                        <div class="flex items-center">
                            <div class="w-12 h-12 bg-gradient-to-r from-green-500 to-emerald-600 rounded-full flex items-center justify-center mr-4">
                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                                </svg>
                            </div>
                            <h2 class="text-2xl font-bold text-gray-900">
                                🍽️ Đề xuất món ăn cho: {{ $selectedDisease->name }}
                            </h2>
                        </div>
                        <button wire:click="resetAnalysis" class="text-gray-500 hover:text-gray-700 bg-white rounded-lg p-3 shadow-md hover:shadow-lg transition-all duration-200">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                            </svg>
                        </button>
                    </div>

                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-10">
                        <div class="bg-gradient-to-br from-red-50 to-pink-50 rounded-xl p-6 border border-red-200">
                            <h3 class="font-semibold text-red-800 mb-4 flex items-center">
                                <svg class="w-6 h-6 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                                </svg>
                                🚫 Thực phẩm cần tránh
                            </h3>
                            <ul class="space-y-3">
                                <li class="flex items-start text-red-700">
                                    <span class="w-2 h-2 bg-red-500 rounded-full mr-3 mt-2 flex-shrink-0"></span>
                                    <span>Hạn chế thực phẩm giàu purine (thịt đỏ, hải sản)</span>
                                </li>
                                <li class="flex items-start text-red-700">
                                    <span class="w-2 h-2 bg-red-500 rounded-full mr-3 mt-2 flex-shrink-0"></span>
                                    <span>Giảm muối và đường</span>
                                </li>
                                <li class="flex items-start text-red-700">
                                    <span class="w-2 h-2 bg-red-500 rounded-full mr-3 mt-2 flex-shrink-0"></span>
                                    <span>Tránh rượu bia, đồ uống có cồn</span>
                                </li>
                                <li class="flex items-start text-red-700">
                                    <span class="w-2 h-2 bg-red-500 rounded-full mr-3 mt-2 flex-shrink-0"></span>
                                    <span>Không ăn nội tạng động vật</span>
                                </li>
                                <li class="flex items-start text-red-700">
                                    <span class="w-2 h-2 bg-red-500 rounded-full mr-3 mt-2 flex-shrink-0"></span>
                                    <span>Hạn chế thực phẩm chế biến sẵn</span>
                                </li>
                            </ul>
                        </div>
                        
                        <div class="bg-gradient-to-br from-green-50 to-emerald-50 rounded-xl p-6 border border-green-200">
                            <h3 class="font-semibold text-green-800 mb-4 flex items-center">
                                <svg class="w-6 h-6 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                </svg>
                                ✅ Thực phẩm nên ăn
                            </h3>
                            <ul class="space-y-3">
                                <li class="flex items-start text-green-700">
                                    <span class="w-2 h-2 bg-green-500 rounded-full mr-3 mt-2 flex-shrink-0"></span>
                                    <span>Ăn nhiều rau xanh, trái cây tươi</span>
                                </li>
                                <li class="flex items-start text-green-700">
                                    <span class="w-2 h-2 bg-green-500 rounded-full mr-3 mt-2 flex-shrink-0"></span>
                                    <span>Tăng cường protein từ thực vật (đậu, hạt)</span>
                                </li>
                                <li class="flex items-start text-green-700">
                                    <span class="w-2 h-2 bg-green-500 rounded-full mr-3 mt-2 flex-shrink-0"></span>
                                    <span>Sử dụng dầu olive thay vì mỡ động vật</span>
                                </li>
                                <li class="flex items-start text-green-700">
                                    <span class="w-2 h-2 bg-green-500 rounded-full mr-3 mt-2 flex-shrink-0"></span>
                                    <span>Uống nhiều nước (2-3 lít/ngày)</span>
                                </li>
                                <li class="flex items-start text-green-700">
                                    <span class="w-2 h-2 bg-green-500 rounded-full mr-3 mt-2 flex-shrink-0"></span>
                                    <span>Ăn ngũ cốc nguyên hạt</span>
                                </li>
                            </ul>
                        </div>
                    </div>

                    @if(!empty($suitableRecipes))
                        <div class="mb-10">
                            <div class="flex items-center justify-between mb-6">
                                <h3 class="text-2xl font-bold text-gray-900 flex items-center">
                                    <svg class="w-8 h-8 mr-3 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                    </svg>
                                    ⭐ Món ăn phù hợp
                                </h3>
                                                         <div class="flex gap-3">
                             <button wire:click="addAllSuitableToMealPlan()"
                                     class="bg-gradient-to-r from-green-500 to-emerald-600 hover:from-green-600 hover:to-emerald-700 text-white px-6 py-3 rounded-xl font-semibold shadow-lg hover:shadow-xl transition-all duration-200 transform hover:scale-105">
                                 📅 Thêm tất cả vào Meal Plan
                             </button>
                             @if(session()->has('meal_plan_recipes') && count(session('meal_plan_recipes', [])) > 0)
                                 <button wire:click="goToMealPlan()"
                                         class="bg-gradient-to-r from-orange-500 to-red-600 hover:from-orange-600 hover:to-red-700 text-white px-6 py-3 rounded-xl font-semibold shadow-lg hover:shadow-xl transition-all duration-200 transform hover:scale-105">
                                     🚀 Đi đến Meal Plan
                                 </button>
                             @endif
                         </div>
                            </div>
                            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                                @foreach($suitableRecipes as $recipe)
                                    <div class="bg-white rounded-xl overflow-hidden shadow-lg hover:shadow-xl transition-all duration-300 transform hover:scale-105 border border-gray-100">
                                        @if($recipe->featured_image)
                                            <div class="relative h-48 overflow-hidden">
                                                <img src="{{ asset('storage/' . $recipe->featured_image) }}" 
                                                     alt="{{ $recipe->title }}" 
                                                     class="w-full h-full object-cover">
                                                <div class="absolute top-3 right-3 bg-green-500 text-white px-2 py-1 rounded-full text-xs font-semibold">
                                                    Phù hợp
                                                </div>
                                            </div>
                                        @endif
                                        <div class="p-6">
                                            <h4 class="font-semibold text-gray-900 mb-3 text-lg">{{ $recipe->title }}</h4>
                                            <p class="text-gray-600 mb-4 text-sm leading-relaxed">{{ Str::limit($recipe->summary, 120) }}</p>
                                            <div class="flex items-center justify-between mb-3">
                                                <span class="text-xs text-gray-500 bg-gray-100 px-3 py-1 rounded-full">
                                                    ⏱️ {{ $recipe->cooking_time }} phút
                                                </span>
                                                <a href="{{ route('recipes.show', $recipe->slug) }}" 
                                                   class="bg-gradient-to-r from-blue-500 to-indigo-600 hover:from-blue-600 hover:to-indigo-700 text-white px-4 py-2 rounded-lg text-sm font-semibold transition-all duration-200 transform hover:scale-105">
                                                    Xem chi tiết →
                                                </a>
                                            </div>
                                            <div class="flex justify-center">
                                                <button wire:click="addToMealPlan({{ $recipe->id }})"
                                                        class="bg-gradient-to-r from-green-500 to-emerald-600 hover:from-green-600 hover:to-emerald-700 text-white px-4 py-2 rounded-lg text-sm font-semibold transition-all duration-200 transform hover:scale-105 w-full">
                                                    📅 Thêm vào Meal Plan
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                            
                            <!-- Phân trang cho món ăn phù hợp -->
                            <div class="mt-8 flex justify-center">
                                {{ $suitableRecipes->links() }}
                            </div>
                        </div>
                    @endif

                    @if(!empty($moderateRecipes))
                        <div class="mb-10">
                            <div class="flex items-center justify-between mb-6">
                                <h3 class="text-2xl font-bold text-gray-900 flex items-center">
                                    <svg class="w-8 h-8 mr-3 text-yellow-600" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                                    </svg>
                                    ⚠️ Món ăn cần điều chỉnh
                                </h3>
                                                                 <div class="flex gap-3">
                                     <button wire:click="addAllModerateToMealPlan()"
                                             class="bg-gradient-to-r from-yellow-500 to-orange-600 hover:from-yellow-600 hover:to-orange-700 text-white px-6 py-3 rounded-xl font-semibold shadow-lg hover:shadow-xl transition-all duration-200 transform hover:scale-105">
                                         📅 Thêm tất cả vào Meal Plan
                                     </button>
                                     @if(session()->has('meal_plan_recipes') && count(session('meal_plan_recipes', [])) > 0)
                                         <button wire:click="goToMealPlan()"
                                                 class="bg-gradient-to-r from-orange-500 to-red-600 hover:from-orange-600 hover:to-red-700 text-white px-6 py-3 rounded-xl font-semibold shadow-lg hover:shadow-xl transition-all duration-200 transform hover:scale-105">
                                             🚀 Đi đến Meal Plan
                                         </button>
                                     @endif
                                 </div>
                            </div>
                            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                                @foreach($moderateRecipes as $recipe)
                                    <div class="bg-white rounded-xl overflow-hidden shadow-lg hover:shadow-xl transition-all duration-300 transform hover:scale-105 border border-yellow-200">
                                        @if($recipe->featured_image)
                                            <div class="relative h-48 overflow-hidden">
                                                <img src="{{ asset('storage/' . $recipe->featured_image) }}" 
                                                     alt="{{ $recipe->title }}" 
                                                     class="w-full h-full object-cover">
                                                <div class="absolute top-3 right-3 bg-yellow-500 text-white px-2 py-1 rounded-full text-xs font-semibold">
                                                    Cần điều chỉnh
                                                </div>
                                            </div>
                                        @endif
                                        <div class="p-6">
                                            <h4 class="font-semibold text-gray-900 mb-3 text-lg">{{ $recipe->title }}</h4>
                                            <p class="text-gray-600 mb-4 text-sm leading-relaxed">{{ Str::limit($recipe->summary, 120) }}</p>
                                            <div class="flex items-center justify-between mb-3">
                                                <span class="text-xs text-yellow-600 bg-yellow-100 px-3 py-1 rounded-full">
                                                    ⚠️ Cần điều chỉnh
                                                </span>
                                                <button wire:click="checkRecipeSuitability({{ $recipe->id }})"
                                                        class="bg-gradient-to-r from-orange-500 to-red-600 hover:from-orange-600 hover:to-red-700 text-white px-4 py-2 rounded-lg text-sm font-semibold transition-all duration-200 transform hover:scale-105">
                                                    Kiểm tra phù hợp
                                                </button>
                                            </div>
                                            <div class="flex justify-center">
                                                <button wire:click="addToMealPlan({{ $recipe->id }})"
                                                        class="bg-gradient-to-r from-yellow-500 to-orange-600 hover:from-yellow-600 hover:to-orange-700 text-white px-4 py-2 rounded-lg text-sm font-semibold transition-all duration-200 transform hover:scale-105 w-full">
                                                    📅 Thêm vào Meal Plan
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                            
                            <!-- Phân trang cho món ăn cần điều chỉnh -->
                            <div class="mt-8 flex justify-center">
                                {{ $moderateRecipes->links() }}
                            </div>
                        </div>
                    @endif
                </div>
            @endif
        </div>
    </div>

    <!-- Toast Notifications -->
    <div id="toast-container" class="fixed top-4 right-4 z-50 space-y-2"></div>

    <!-- Meal Plan Selection Modal -->
    @if($showMealPlanModal)
        <div class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
            <div class="bg-white rounded-2xl shadow-2xl max-w-4xl w-full max-h-[90vh] overflow-y-auto">
                <div class="p-6 border-b border-gray-200">
                    <div class="flex items-center justify-between">
                        <h3 class="text-2xl font-bold text-gray-900">
                            📅 Chọn Meal Plan cho: {{ $selectedRecipeForMealPlan->title ?? 'Công thức' }}
                        </h3>
                        <button wire:click="closeMealPlanModal" class="text-gray-400 hover:text-gray-600">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>
                </div>

                <div class="p-6">
                    @if($availableMealPlans->count() > 0)
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                            @foreach($availableMealPlans as $mealPlan)
                                <div class="bg-gradient-to-br from-blue-50 to-indigo-50 rounded-xl p-6 border border-blue-200 hover:border-blue-300 transition-all duration-200">
                                    <div class="flex items-center mb-4">
                                        <div class="w-10 h-10 bg-gradient-to-r from-blue-500 to-indigo-600 rounded-full flex items-center justify-center mr-3">
                                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                            </svg>
                                        </div>
                                        <h4 class="font-semibold text-gray-900">{{ $mealPlan->name }}</h4>
                                    </div>
                                    
                                    <div class="text-sm text-gray-600 mb-4">
                                        <p>Tuần: {{ $mealPlan->week_start->format('d/m/Y') }} - {{ $mealPlan->week_end->format('d/m/Y') }}</p>
                                        <p>Calories: {{ number_format($mealPlan->total_calories) }}</p>
                                    </div>

                                    <div class="space-y-2">
                                        <h5 class="font-medium text-gray-900 text-sm">Chọn bữa ăn:</h5>
                                        <div class="grid grid-cols-2 gap-2">
                                            <button wire:click="addRecipeToMealPlan({{ $mealPlan->id }}, 'monday', 'breakfast')"
                                                    class="bg-gradient-to-r from-green-500 to-emerald-600 hover:from-green-600 hover:to-emerald-700 text-white px-3 py-2 rounded-lg text-xs font-medium transition-all duration-200 transform hover:scale-105">
                                                🌅 Bữa sáng
                                            </button>
                                            <button wire:click="addRecipeToMealPlan({{ $mealPlan->id }}, 'monday', 'lunch')"
                                                    class="bg-gradient-to-r from-orange-500 to-red-600 hover:from-orange-600 hover:to-red-700 text-white px-3 py-2 rounded-lg text-xs font-medium transition-all duration-200 transform hover:scale-105">
                                                🌞 Bữa trưa
                                            </button>
                                            <button wire:click="addRecipeToMealPlan({{ $mealPlan->id }}, 'monday', 'dinner')"
                                                    class="bg-gradient-to-r from-purple-500 to-pink-600 hover:from-purple-600 hover:to-pink-700 text-white px-3 py-2 rounded-lg text-xs font-medium transition-all duration-200 transform hover:scale-105">
                                                🌙 Bữa tối
                                            </button>
                                            <button wire:click="addRecipeToMealPlan({{ $mealPlan->id }}, 'monday', 'snack')"
                                                    class="bg-gradient-to-r from-yellow-500 to-orange-600 hover:from-yellow-600 hover:to-orange-700 text-white px-3 py-2 rounded-lg text-xs font-medium transition-all duration-200 transform hover:scale-105">
                                                🍎 Bữa phụ
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-12">
                            <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                                <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                </svg>
                            </div>
                            <h4 class="text-lg font-semibold text-gray-900 mb-2">Chưa có Meal Plan nào</h4>
                            <p class="text-gray-600 mb-6">Bạn cần tạo Meal Plan trước khi thêm công thức</p>
                            <a href="{{ route('meal-plans.create') }}" 
                               class="bg-gradient-to-r from-blue-500 to-indigo-600 hover:from-blue-600 hover:to-indigo-700 text-white px-6 py-3 rounded-xl font-semibold shadow-lg hover:shadow-xl transition-all duration-200 transform hover:scale-105">
                                🚀 Tạo Meal Plan mới
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    @endif

    <!-- Meal Plan Button -->
    @if(session()->has('meal_plan_recipes') && count(session('meal_plan_recipes', [])) > 0)
        <div class="fixed bottom-6 right-6 z-50">
            <a href="{{ route('meal-plans.create') }}" 
               class="bg-gradient-to-r from-orange-500 to-red-600 hover:from-orange-600 hover:to-red-700 text-white px-6 py-4 rounded-full font-semibold shadow-lg hover:shadow-xl transition-all duration-200 transform hover:scale-105 flex items-center">
                <svg class="w-6 h-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                </svg>
                📅 Xem Meal Plan ({{ count(session('meal_plan_recipes', [])) }} món)
            </a>
        </div>
    @endif

    <script>
        document.addEventListener('livewire:init', () => {
            Livewire.on('meal-plan-success', (event) => {
                showToast(event.message, 'success');
            });

            Livewire.on('meal-plan-error', (event) => {
                showToast(event.message, 'error');
            });
        });

        function showToast(message, type = 'success') {
            const container = document.getElementById('toast-container');
            const toast = document.createElement('div');
            
            const bgColor = type === 'success' ? 'bg-green-500' : 'bg-red-500';
            const icon = type === 'success' ? '✅' : '❌';
            
            toast.className = `${bgColor} text-white px-6 py-4 rounded-lg shadow-lg transform transition-all duration-300 translate-x-full`;
            toast.innerHTML = `
                <div class="flex items-center">
                    <span class="mr-2">${icon}</span>
                    <span>${message}</span>
                </div>
            `;
            
            container.appendChild(toast);
            
            // Animate in
            setTimeout(() => {
                toast.classList.remove('translate-x-full');
            }, 100);
            
            // Animate out and remove
            setTimeout(() => {
                toast.classList.add('translate-x-full');
                setTimeout(() => {
                    if (container.contains(toast)) {
                        container.removeChild(toast);
                    }
                }, 300);
            }, 3000);
        }
    </script>
</div>
