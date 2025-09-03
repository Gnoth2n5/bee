<div class="min-h-screen bg-gradient-to-br from-gray-50 via-white to-gray-100 dark:from-slate-900 dark:via-slate-800 dark:to-slate-900 relative overflow-hidden transition-colors duration-500">
    <!-- Background Decorations -->
    <div class="absolute inset-0 overflow-hidden pointer-events-none">
        <div class="absolute top-20 left-10 w-32 h-32 bg-gradient-to-r from-orange-200 to-red-200 dark:from-orange-800/30 dark:to-red-800/30 rounded-full blur-3xl opacity-30 animate-pulse"></div>
        <div class="absolute bottom-20 right-20 w-40 h-40 bg-gradient-to-r from-blue-200 to-cyan-200 dark:from-blue-800/30 dark:to-cyan-800/30 rounded-full blur-3xl opacity-20 animate-bounce" style="animation-delay: 1s"></div>
        <div class="absolute top-1/2 left-1/2 w-24 h-24 bg-gradient-to-r from-yellow-200 to-orange-200 dark:from-yellow-800/30 dark:to-orange-800/30 rounded-full blur-2xl opacity-25 animate-ping" style="animation-delay: 2s"></div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12 relative z-10">
        <!-- Header -->
        <div class="text-center mb-16">
            <div class="inline-flex items-center px-4 py-2 rounded-full bg-gradient-to-r from-orange-100 to-red-100 dark:from-orange-900/30 dark:to-red-900/30 border border-orange-200 dark:border-orange-800 mb-6">
                <x-lucide-cloud-sun class="w-4 h-4 mr-2 text-orange-500 animate-pulse" />
                <span class="text-sm font-semibold text-orange-600 dark:text-orange-400">Đề xuất thông minh</span>
            </div>
            
            <h1 class="text-4xl md:text-5xl lg:text-6xl font-black text-gray-800 dark:text-white mb-6 animate-fade-in-up">
                Món Ăn Phù Hợp
                <span class="bg-gradient-to-r from-orange-500 to-red-600 bg-clip-text text-transparent animate-pulse">
                    Với Thời Tiết
                </span>
            </h1>
            
            <p class="text-lg text-gray-600 dark:text-gray-300 max-w-3xl mx-auto leading-relaxed animate-fade-in-up">
                Khám phá những món ăn được lựa chọn đặc biệt dựa trên điều kiện thời tiết hiện tại tại thành phố của bạn. AI của chúng tôi phân tích thời tiết và gợi ý những món ăn phù hợp nhất.
            </p>
            
            <div class="mt-8">
                @if(!$nearestCity)
                    <!-- Location Controls -->
                    <div class="bg-white/60 dark:bg-slate-800/60 backdrop-blur-sm border border-orange-200/50 dark:border-orange-800/50 rounded-2xl p-6 mb-8 max-w-lg mx-auto shadow-xl">
                        <div class="text-center space-y-4">
                            <div class="flex items-center justify-center mb-4">
                                <div class="w-12 h-12 bg-gradient-to-r from-orange-500 to-red-600 rounded-full flex items-center justify-center">
                                    <x-lucide-map-pin class="w-6 h-6 text-white" />
                                </div>
                            </div>
                            <h3 class="text-lg font-semibold text-gray-800 dark:text-white mb-2">Chọn vị trí của bạn</h3>
                            <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">Để nhận đề xuất món ăn phù hợp với thời tiết</p>
                            
                            <div class="flex flex-col sm:flex-row gap-3 justify-center">
                                <button onclick="showLocationModal()" 
                                        class="group inline-flex items-center justify-center px-6 py-3 bg-gradient-to-r from-orange-500 to-red-600 hover:from-orange-600 hover:to-red-700 text-white font-semibold rounded-xl transition-all duration-300 hover:scale-105 hover:shadow-lg">
                                    <x-lucide-navigation class="w-4 h-4 mr-2 group-hover:rotate-12 transition-transform duration-300" />
                                    Lấy vị trí hiện tại
                                </button>
                                <button wire:click="randomCity" 
                                        class="group inline-flex items-center justify-center px-6 py-3 bg-white/80 dark:bg-slate-700/80 hover:bg-white dark:hover:bg-slate-700 text-gray-800 dark:text-white font-semibold rounded-xl border border-gray-200 dark:border-slate-600 transition-all duration-300 hover:scale-105 hover:shadow-lg backdrop-blur-sm">
                                    <x-lucide-shuffle class="w-4 h-4 mr-2 group-hover:rotate-180 transition-transform duration-300" />
                                    Chọn ngẫu nhiên
                                </button>
                            </div>
                            
                            <!-- Debug Tools (only show in development) -->
                            @if(config('app.debug'))
                            <div class="mt-4 pt-4 border-t border-gray-200 dark:border-gray-700">
                                <p class="text-xs text-gray-500 dark:text-gray-400 mb-2 text-center">Debug Tools</p>
                                <div class="flex flex-wrap gap-2 justify-center">
                                    <button onclick="debugLocationInfo()" 
                                            class="text-xs px-3 py-1 bg-blue-100 hover:bg-blue-200 text-blue-700 rounded-lg transition-colors">
                                        Debug Info
                                    </button>
                                    <button wire:click="clearLocationCache" 
                                            class="text-xs px-3 py-1 bg-red-100 hover:bg-red-200 text-red-700 rounded-lg transition-colors">
                                        Clear Cache
                                    </button>
                                    <button onclick="forceLocationRefresh()" 
                                            class="text-xs px-3 py-1 bg-green-100 hover:bg-green-200 text-green-700 rounded-lg transition-colors">
                                        Force Refresh
                                    </button>
                                </div>
                            </div>
                            @endif
                        </div>
                    </div>
                @else
                    <!-- Current Location Display -->
                    <div class="bg-gradient-to-r from-green-50 to-emerald-50 dark:from-green-900/20 dark:to-emerald-900/20 border border-green-200 dark:border-green-800 rounded-2xl p-6 mb-8 max-w-lg mx-auto shadow-xl">
                        <div class="text-center">
                            <div class="flex items-center justify-center mb-4">
                                <div class="w-12 h-12 bg-gradient-to-r from-green-500 to-emerald-600 rounded-full flex items-center justify-center">
                                    <x-lucide-map-pin class="w-6 h-6 text-white" />
                                </div>
                            </div>
                            <h3 class="text-lg font-semibold text-green-800 dark:text-green-400 mb-2">Vị trí hiện tại</h3>
                            <p class="text-xl font-bold text-green-900 dark:text-green-300">{{ $nearestCity->name }}</p>
                            
                            @if(config('app.debug'))
                                <p class="text-xs text-gray-600 dark:text-gray-400 mt-1">
                                    Code: {{ $nearestCity->code }} | 
                                    Coordinates: {{ round($nearestCity->latitude, 4) }}, {{ round($nearestCity->longitude, 4) }}
                                </p>
                                @if($userLatitude && $userLongitude)
                                    <p class="text-xs text-gray-600 dark:text-gray-400">
                                        Your GPS: {{ round($userLatitude, 4) }}, {{ round($userLongitude, 4) }}
                                    </p>
                                @endif
                            @endif
                            
                            @if(session('user_location.is_random'))
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-blue-100 dark:bg-blue-900/30 text-blue-800 dark:text-blue-400 mt-2">
                                    <x-lucide-shuffle class="w-3 h-3 mr-1" />
                                    Ngẫu nhiên
                                </span>
                            @else
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-400 mt-2">
                                    <x-lucide-navigation class="w-3 h-3 mr-1" />
                                    GPS
                                </span>
                            @endif
                            
                            @if(config('app.debug'))
                            <div class="mt-3 pt-3 border-t border-green-200 dark:border-green-700">
                                <div class="flex flex-wrap gap-2 justify-center">
                                    <button onclick="debugLocationInfo()" 
                                            class="text-xs px-3 py-1 bg-blue-100 hover:bg-blue-200 text-blue-700 rounded-lg transition-colors">
                                        Debug Info
                                    </button>
                                    <button wire:click="clearLocationCache" 
                                            class="text-xs px-3 py-1 bg-red-100 hover:bg-red-200 text-red-700 rounded-lg transition-colors">
                                        Clear & Reset
                                    </button>
                                    <button onclick="forceLocationRefresh()" 
                                            class="text-xs px-3 py-1 bg-green-100 hover:bg-green-200 text-green-700 rounded-lg transition-colors">
                                        Force Refresh
                                    </button>
                                </div>
                            </div>
                            @endif
                        </div>
                    </div>
                @endif
            </div>
        </div>

        <!-- Test Component -->
        
        
        <!-- City Selector -->
        <div class="bg-white/80 dark:bg-slate-800/80 backdrop-blur-sm rounded-2xl shadow-xl border border-gray-200/50 dark:border-slate-700/50 p-8 mb-12 animate-fade-in-up">
            <div class="flex flex-col lg:flex-row lg:items-end gap-6">
                <div class="flex-1">
                    <div class="flex items-center mb-3">
                        <x-lucide-map class="w-5 h-5 mr-2 text-orange-500" />
                        <label for="city-select" class="text-sm font-semibold text-gray-700 dark:text-gray-300">
                            Chọn thành phố
                        </label>
                    </div>
                    <div class="relative">
                        <select 
                            wire:model.live="selectedCity" 
                            id="city-select"
                            class="w-full px-4 py-4 pr-10 border border-gray-300 dark:border-slate-600 rounded-xl shadow-sm bg-white dark:bg-slate-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-orange-500 transition-all duration-300 hover:border-orange-400 text-lg font-medium"
                        >
                            @foreach($cities as $region => $regionCities)
                                <optgroup label="{{ $region }} Miền">
                                    @foreach($regionCities as $city)
                                        <option value="{{ $city->code }}" {{ $selectedCity === $city->code ? 'selected' : '' }}>
                                            {{ $city->name }}
                                        </option>
                                    @endforeach
                                </optgroup>
                            @endforeach
                        </select>
                        <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                            <x-lucide-chevron-down class="w-5 h-5 text-gray-400" />
                        </div>
                    </div>
                </div>
                
                <button 
                    wire:click="loadWeatherAndSuggestions"
                    class="group inline-flex items-center justify-center px-8 py-4 bg-gradient-to-r from-orange-500 to-red-600 hover:from-orange-600 hover:to-red-700 text-white font-bold text-lg rounded-xl shadow-lg hover:shadow-xl transform hover:scale-105 transition-all duration-300 focus:outline-none focus:ring-2 focus:ring-orange-500 focus:ring-offset-2 min-w-[140px]"
                    :disabled="$wire.loading"
                >
                    <svg wire:loading wire:target="loadWeatherAndSuggestions" class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    <x-lucide-refresh-cw wire:loading.remove wire:target="loadWeatherAndSuggestions" class="w-5 h-5 mr-2 group-hover:rotate-180 transition-transform duration-300" />
                    Cập nhật
                </button>
            </div>
        </div>

        @if($error)
            <div class="bg-gradient-to-r from-red-50 to-rose-50 dark:from-red-900/20 dark:to-rose-900/20 border border-red-200 dark:border-red-800 rounded-2xl p-6 mb-12 shadow-lg animate-fade-in-up">
                <div class="flex items-start">
                    <div class="flex-shrink-0">
                        <div class="w-12 h-12 bg-gradient-to-r from-red-500 to-rose-600 rounded-full flex items-center justify-center">
                            <x-lucide-alert-circle class="w-6 h-6 text-white" />
                        </div>
                    </div>
                    <div class="ml-4 flex-1">
                        <h3 class="text-lg font-semibold text-red-800 dark:text-red-400 mb-2">Có lỗi xảy ra</h3>
                        <p class="text-red-700 dark:text-red-300 leading-relaxed">{{ $error }}</p>
                    </div>
                </div>
            </div>
        @endif

        @if($loading)
            <div class="text-center py-20">
                <div class="bg-white/80 dark:bg-slate-800/80 backdrop-blur-sm rounded-2xl p-12 shadow-xl border border-gray-200/50 dark:border-slate-700/50 max-w-md mx-auto">
                    <div class="w-16 h-16 bg-gradient-to-r from-orange-500 to-red-600 rounded-full flex items-center justify-center mx-auto mb-6 animate-pulse">
                        <x-lucide-cloud-sun class="w-8 h-8 text-white" />
                    </div>
                    <div class="flex items-center justify-center mb-4">
                        <svg class="animate-spin h-6 w-6 text-orange-500 mr-3" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        <span class="text-lg font-semibold text-gray-800 dark:text-white">Đang phân tích...</span>
                    </div>
                    <p class="text-gray-600 dark:text-gray-400 leading-relaxed">
                        Chúng tôi đang thu thập dữ liệu thời tiết và tìm kiếm những món ăn phù hợp nhất cho bạn.
                    </p>
                </div>
            </div>
        @elseif($weatherData)
            <!-- Weather Information -->
            <div class="bg-white/80 dark:bg-slate-800/80 backdrop-blur-sm rounded-2xl shadow-xl border border-gray-200/50 dark:border-slate-700/50 p-8 mb-12 animate-fade-in-up">
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                    <!-- Current Weather -->
                    <div class="text-center group">
                        <div class="flex justify-center mb-6">
                            <div class="w-20 h-20 bg-gradient-to-br from-orange-400 to-red-500 rounded-2xl flex items-center justify-center shadow-lg group-hover:scale-110 transition-transform duration-300">
                                <x-lucide-thermometer class="w-10 h-10 text-white" />
                            </div>
                        </div>
                        <h3 class="text-xl font-bold text-gray-800 dark:text-white mb-3">Thời tiết hiện tại</h3>
                        <p class="text-4xl font-black {{ $this->getTemperatureColor($weatherData['temperature']) }} mb-2 group-hover:scale-105 transition-transform duration-300">
                            {{ number_format($weatherData['temperature'], 1) }}°C
                        </p>
                        <p class="text-gray-600 dark:text-gray-400 font-medium">{{ $weatherData['description'] ?? 'Không có mô tả' }}</p>
                    </div>

                    <!-- Weather Details -->
                    <div class="space-y-6">
                        <h3 class="text-xl font-bold text-gray-800 dark:text-white mb-6">Chi tiết thời tiết</h3>
                        <div class="space-y-4">
                            <div class="flex items-center p-3 bg-gradient-to-r from-blue-50 to-cyan-50 dark:from-blue-900/20 dark:to-cyan-900/20 rounded-xl">
                                <div class="w-10 h-10 bg-gradient-to-r from-blue-500 to-cyan-600 rounded-lg flex items-center justify-center mr-3">
                                    <x-lucide-droplets class="w-5 h-5 text-white" />
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Độ ẩm</p>
                                    <p class="text-lg font-bold text-blue-700 dark:text-blue-400">{{ $weatherData['humidity'] }}%</p>
                                </div>
                            </div>
                            <div class="flex items-center p-3 bg-gradient-to-r from-yellow-50 to-orange-50 dark:from-yellow-900/20 dark:to-orange-900/20 rounded-xl">
                                <div class="w-10 h-10 bg-gradient-to-r from-yellow-500 to-orange-600 rounded-lg flex items-center justify-center mr-3">
                                    <x-lucide-user class="w-5 h-5 text-white" />
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Cảm giác</p>
                                    <p class="text-lg font-bold text-orange-700 dark:text-orange-400">{{ number_format($weatherData['feels_like'] ?? 0, 1) }}°C</p>
                                </div>
                            </div>
                            <div class="flex items-center p-3 bg-gradient-to-r from-green-50 to-emerald-50 dark:from-green-900/20 dark:to-emerald-900/20 rounded-xl">
                                <div class="w-10 h-10 bg-gradient-to-r from-green-500 to-emerald-600 rounded-lg flex items-center justify-center mr-3">
                                    <x-lucide-wind class="w-5 h-5 text-white" />
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Tốc độ gió</p>
                                    <p class="text-lg font-bold text-green-700 dark:text-green-400">{{ number_format($weatherData['wind_speed'] ?? 0, 1) }} m/s</p>
                                </div>
                            </div>
                            <div class="flex items-center p-3 bg-gradient-to-r from-purple-50 to-pink-50 dark:from-purple-900/20 dark:to-pink-900/20 rounded-xl">
                                <div class="w-10 h-10 bg-gradient-to-r from-purple-500 to-pink-600 rounded-lg flex items-center justify-center mr-3">
                                    <x-lucide-eye class="w-5 h-5 text-white" />
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Tầm nhìn</p>
                                    <p class="text-lg font-bold text-purple-700 dark:text-purple-400">{{ number_format(($weatherData['visibility'] ?? 0) / 1000, 1) }} km</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Suggestion Reason -->
                    <div>
                        <h3 class="text-xl font-bold text-gray-800 dark:text-white mb-6">AI Giải thích</h3>
                        <div class="bg-gradient-to-br from-orange-50 to-red-50 dark:from-orange-900/20 dark:to-red-900/20 rounded-2xl p-6 border border-orange-200/50 dark:border-orange-800/50">
                            <div class="flex items-start">
                                <div class="w-12 h-12 bg-gradient-to-r from-orange-500 to-red-600 rounded-xl flex items-center justify-center mr-4 flex-shrink-0">
                                    <x-lucide-brain class="w-6 h-6 text-white" />
                                </div>
                                <div>
                                    <h4 class="font-semibold text-orange-800 dark:text-orange-400 mb-2">Tại sao AI chọn những món này?</h4>
                                    <p class="text-orange-700 dark:text-orange-300 leading-relaxed">
                                        {{ $this->getSuggestionReason() }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Recipe Suggestions -->
            @if($suggestions->count() > 0)
                <div class="bg-white/80 dark:bg-slate-800/80 backdrop-blur-sm rounded-2xl shadow-xl border border-gray-200/50 dark:border-slate-700/50 p-8">
                    <div class="text-center mb-12">
                        <div class="inline-flex items-center px-4 py-2 rounded-full bg-gradient-to-r from-green-100 to-emerald-100 dark:from-green-900/30 dark:to-emerald-900/30 border border-green-200 dark:border-green-800 mb-6">
                            <x-lucide-chef-hat class="w-4 h-4 mr-2 text-green-500 animate-bounce" style="animation-delay: 0.5s" />
                            <span class="text-sm font-semibold text-green-600 dark:text-green-400">Đề xuất AI</span>
                        </div>
                        <h2 class="text-3xl md:text-4xl font-black text-gray-800 dark:text-white mb-4">
                            Món Ăn
                            <span class="bg-gradient-to-r from-green-500 to-emerald-600 bg-clip-text text-transparent">
                                Được Đề Xuất
                            </span>
                        </h2>
                        <p class="text-gray-600 dark:text-gray-400 max-w-2xl mx-auto leading-relaxed">
                            Các món ăn được AI chọn lọc đặc biệt dựa trên điều kiện thời tiết hiện tại
                        </p>
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                        @foreach($suggestions as $recipe)
                            <x-recipe-card :recipe="$recipe" />
                        @endforeach
                    </div>
                </div>
            @else
                <div class="bg-white/80 dark:bg-slate-800/80 backdrop-blur-sm rounded-2xl shadow-xl border border-gray-200/50 dark:border-slate-700/50 p-12 text-center">
                    <div class="w-24 h-24 bg-gradient-to-r from-gray-400 to-gray-500 rounded-full flex items-center justify-center mx-auto mb-6">
                        <x-lucide-chef-hat class="w-12 h-12 text-white" />
                    </div>
                    <h3 class="text-2xl font-bold text-gray-800 dark:text-white mb-4">Chưa có đề xuất</h3>
                    <p class="text-gray-600 dark:text-gray-400 leading-relaxed max-w-md mx-auto">
                        Hiện tại chưa có món ăn phù hợp với điều kiện thời tiết này. Hãy thử chọn thành phố khác hoặc quay lại sau.
                    </p>
                    <div class="mt-8">
                        <button wire:click="loadWeatherAndSuggestions" 
                                class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-orange-500 to-red-600 hover:from-orange-600 hover:to-red-700 text-white font-semibold rounded-xl transition-all duration-300 hover:scale-105 shadow-lg">
                            <x-lucide-refresh-cw class="w-4 h-4 mr-2" />
                            Thử lại
                        </button>
                    </div>
                </div>
            @endif
        @elseif(!$loading && !$error)
            <div class="bg-white/80 dark:bg-slate-800/80 backdrop-blur-sm rounded-2xl shadow-xl border border-gray-200/50 dark:border-slate-700/50 p-12 text-center">
                <div class="w-20 h-20 bg-gradient-to-r from-gray-400 to-gray-500 rounded-full flex items-center justify-center mx-auto mb-6">
                    <x-lucide-cloud-off class="w-10 h-10 text-white" />
                </div>
                <h3 class="text-xl font-bold text-gray-800 dark:text-white mb-3">Chưa có dữ liệu thời tiết</h3>
                <p class="text-gray-600 dark:text-gray-400 leading-relaxed max-w-md mx-auto">
                    Vui lòng chọn thành phố và nhấn "Cập nhật" để nhận đề xuất món ăn phù hợp với thời tiết.
                </p>
            </div>
        @endif
    </div>
    

    
    <style>
    .line-clamp-2 {
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }
    
    .line-clamp-3 {
        display: -webkit-box;
        -webkit-line-clamp: 3;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }
    
    /* Fade in animations */
    @keyframes fadeInUp {
        from {
            opacity: 0;
            transform: translateY(30px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
    
    .animate-fade-in-up {
        animation: fadeInUp 0.6s ease-out;
    }
    
    /* Ensure proper theme transitions */
    .bg-gradient-to-br, .bg-gradient-to-r {
        transition: background-color 0.5s ease-in-out;
    }
    

    

    
    /* Enhanced backdrop blur for supported browsers */
    .backdrop-blur-sm {
        backdrop-filter: blur(4px);
    }
    
    /* Smooth transitions for all interactive elements */
    .transition-all, .hover\\:scale-105, .hover\\:scale-110, .group-hover\\:scale-110 {
        transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1);
    }
    

    </style>

    <script>
    // Debug theme state
    document.addEventListener('DOMContentLoaded', function() {
        console.log('Weather page loaded');
        console.log('HTML classList:', document.documentElement.classList.toString());
        console.log('Theme in localStorage:', localStorage.getItem('theme'));
        console.log('System prefers dark:', window.matchMedia('(prefers-color-scheme: dark)').matches);
        
        // Force apply theme if needed
        if (typeof window.ThemeManager !== 'undefined') {
            const currentTheme = window.ThemeManager.getTheme();
            console.log('Current theme from ThemeManager:', currentTheme);
            window.ThemeManager.setTheme(currentTheme);
        }
    });

    // Hàm hiển thị modal chia sẻ vị trí với SweetAlert
    function showLocationModal() {
        Swal.fire({
            title: 'Chia sẻ vị trí',
            text: 'Bạn có muốn chia sẻ vị trí hiện tại để nhận đề xuất món ăn phù hợp với thời tiết không?',
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#3B82F6',
            cancelButtonColor: '#6B7280',
            confirmButtonText: 'Có, chia sẻ',
            cancelButtonText: 'Không, chọn ngẫu nhiên',
            customClass: {
                popup: 'rounded-lg',
                confirmButton: 'rounded-md',
                cancelButton: 'rounded-md'
            }
        }).then((result) => {
            if (result.isConfirmed) {
                // Lấy vị trí
                if (navigator.geolocation) {
                    navigator.geolocation.getCurrentPosition(
                        (position) => {
                            const latitude = position.coords.latitude;
                            const longitude = position.coords.longitude;
                            
                            console.log('Đã lấy được vị trí:', latitude, longitude);
                            
                            // Lưu vào localStorage
                            localStorage.setItem('user_location', JSON.stringify({
                                latitude: latitude,
                                longitude: longitude,
                                timestamp: new Date().getTime()
                            }));
                            console.log('Location saved to localStorage');
                            
                            // Hiển thị thông báo thành công
                            Swal.fire({
                                title: 'Thành công!',
                                text: 'Đã lấy được vị trí của bạn',
                                icon: 'success',
                                timer: 1500,
                                showConfirmButton: false
                            });
                            
                            // Gửi tọa độ về Livewire
                            @this.setUserLocation(latitude, longitude);
                        },
                        (error) => {
                            console.log('Lỗi lấy vị trí:', error.message);
                            
                            // Khi người dùng từ chối vị trí, tự động chọn ngẫu nhiên
                            if (error.code === 1) { // PERMISSION_DENIED
                                console.log('Người dùng từ chối vị trí, chọn ngẫu nhiên...');
                                Swal.fire({
                                    title: 'Đã chọn ngẫu nhiên',
                                    text: 'Sẽ hiển thị món ăn phù hợp với thời tiết ngẫu nhiên',
                                    icon: 'info',
                                    timer: 2000,
                                    showConfirmButton: false
                                });
                                @this.randomCity();
                            } else {
                                Swal.fire({
                                    title: 'Lỗi',
                                    text: 'Không thể lấy vị trí: ' + error.message,
                                    icon: 'error',
                                    confirmButtonText: 'OK'
                                });
                                @this.randomCity();
                            }
                        },
                        {
                            enableHighAccuracy: true,
                            timeout: 10000,
                            maximumAge: 60000
                        }
                    );
                } else {
                    Swal.fire({
                        title: 'Không hỗ trợ',
                        text: 'Trình duyệt không hỗ trợ lấy vị trí',
                        icon: 'warning',
                        confirmButtonText: 'OK'
                    });
                    @this.randomCity();
                }
            } else {
                // Chọn ngẫu nhiên
                console.log('Người dùng không muốn chia sẻ vị trí, chọn ngẫu nhiên...');
                Swal.fire({
                    title: 'Đã chọn ngẫu nhiên',
                    text: 'Sẽ hiển thị món ăn phù hợp với thời tiết ngẫu nhiên',
                    icon: 'info',
                    timer: 2000,
                    showConfirmButton: false
                });
                @this.randomCity();
            }
        });
    }

    document.addEventListener('livewire:init', () => {
        // Kiểm tra localStorage khi component được load
        const savedLocation = localStorage.getItem('user_location');
        if (savedLocation) {
            try {
                const locationData = JSON.parse(savedLocation);
                const now = new Date().getTime();
                const oneHour = 60 * 60 * 1000; // 1 giờ
                
                // Kiểm tra xem vị trí có còn mới không (trong vòng 1 giờ)
                if (now - locationData.timestamp < oneHour) {
                    console.log('Found saved location in localStorage:', locationData);
                    @this.setUserLocation(locationData.latitude, locationData.longitude);
                } else {
                    console.log('Saved location is too old, removing from localStorage');
                    localStorage.removeItem('user_location');
                }
            } catch (error) {
                console.error('Error parsing saved location:', error);
                localStorage.removeItem('user_location');
            }
        }
        
        // Hàm hiển thị modal
        }

        // Tự động lấy vị trí khi component được load
        Livewire.on('auto-get-location', () => {
            showLocationModal();
        });

        // Xử lý khi người dùng click nút lấy vị trí thủ công
        Livewire.on('get-user-location', () => {
            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(
                    (position) => {
                        const latitude = position.coords.latitude;
                        const longitude = position.coords.longitude;
                        
                        console.log('Đã lấy được vị trí:', latitude, longitude);
                        
                        // Lưu vào localStorage
                        localStorage.setItem('user_location', JSON.stringify({
                            latitude: latitude,
                            longitude: longitude,
                            timestamp: new Date().getTime()
                        }));
                        console.log('Location saved to localStorage');
                        
                        // Gửi tọa độ về Livewire
                        @this.setUserLocation(latitude, longitude);
                    },
                    (error) => {
                        console.log('Lỗi lấy vị trí:', error.message);
                        
                        // Khi người dùng từ chối vị trí, tự động chọn ngẫu nhiên
                        if (error.code === 1) { // PERMISSION_DENIED
                            console.log('Người dùng từ chối vị trí, chọn ngẫu nhiên...');
                            @this.randomCity();
                        } else {
                            alert('Không thể lấy vị trí: ' + error.message);
                        }
                    },
                    {
                        enableHighAccuracy: true,
                        timeout: 10000,
                        maximumAge: 60000
                    }
                );
            } else {
                alert('Trình duyệt không hỗ trợ lấy vị trí');
            }
        });
        
        // Xử lý clear location cache
        Livewire.on('clear-location-cache', () => {
            console.log('Clearing location cache from localStorage...');
            localStorage.removeItem('user_location');
            console.log('Location cache cleared');
        });
        
        // Debug function - show location info in console
        window.debugLocationInfo = function() {
            const stored = localStorage.getItem('user_location');
            console.log('=== LOCATION DEBUG INFO ===');
            console.log('localStorage data:', stored ? JSON.parse(stored) : 'No data');
            
            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(
                    (position) => {
                        console.log('Current browser location:', {
                            latitude: position.coords.latitude,
                            longitude: position.coords.longitude,
                            accuracy: position.coords.accuracy + 'm',
                            timestamp: new Date().toLocaleString()
                        });
                    },
                    (error) => {
                        console.log('Cannot get current location:', error.message);
                    },
                    { enableHighAccuracy: true, timeout: 5000, maximumAge: 0 }
                );
            }
            console.log('=== END DEBUG INFO ===');
        };
        
        // Force clear and refresh function
        window.forceLocationRefresh = function() {
            console.log('Forcing location refresh...');
            @this.forceLocationRefresh();
        };
    });
    </script>
</div> 