<div id="weather-section" class="py-20 bg-gradient-to-br from-gray-50 via-white to-gray-100 dark:from-slate-900 dark:via-slate-800 dark:to-slate-900 relative overflow-hidden">
    <!-- Background Decorations -->
    <div class="absolute inset-0 overflow-hidden pointer-events-none">
        <div class="absolute top-20 left-10 w-32 h-32 bg-gradient-to-r from-orange-200 to-red-200 rounded-full blur-3xl opacity-30 animate-pulse"></div>
        <div class="absolute bottom-20 right-20 w-40 h-40 bg-gradient-to-r from-blue-200 to-cyan-200 rounded-full blur-3xl opacity-20 animate-bounce" style="animation-delay: 1s"></div>
        <div class="absolute top-1/2 left-1/2 w-24 h-24 bg-gradient-to-r from-yellow-200 to-orange-200 rounded-full blur-2xl opacity-25 animate-ping" style="animation-delay: 2s"></div>
    </div>

    <div class="container mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
        <!-- Header -->
        <div class="text-center mb-16">
            <div class="inline-flex items-center px-4 py-2 rounded-full bg-gradient-to-r from-orange-100 to-red-100 dark:from-orange-900/30 dark:to-red-900/30 border border-orange-200 dark:border-orange-800 mb-6">
                <x-lucide-cloud-sun class="w-4 h-4 mr-2 text-orange-500 animate-spin" style="animation-duration: 3s" />
                <span class="text-sm font-semibold text-orange-600 dark:text-orange-400">Dựa trên thời tiết</span>
            </div>
            
            <h2 class="text-4xl md:text-5xl lg:text-6xl font-black text-gray-800 dark:text-white mb-6 animate-fade-in-up">
                Món ăn phù hợp với 
                <span class="bg-gradient-to-r from-orange-500 to-red-600 bg-clip-text text-transparent animate-fade-in-up">
                    Thời tiết
                </span>
            </h2>
            
            <p class="text-lg text-gray-600 dark:text-gray-300 max-w-3xl mx-auto leading-relaxed animate-fade-in-up">
                Khám phá những món ăn ngon được đề xuất theo thời tiết hiện tại, mang đến trải nghiệm ẩm thực hoàn hảo cho từng thời điểm.
            </p>
        </div>

        <!-- Main Container -->
        <div class="bg-white/80 dark:bg-slate-800/80 backdrop-blur-sm rounded-2xl shadow-xl overflow-hidden border border-gray-200/50 dark:border-slate-700/50">
            <!-- Weather Header Section -->
            <div class="bg-gradient-to-r from-orange-500 to-red-600 text-white px-8 py-8 relative overflow-hidden">
                <!-- Header Background Decoration -->
                <div class="absolute inset-0 opacity-20">
                    <div class="absolute top-0 left-0 w-24 h-24 bg-white rounded-full blur-2xl animate-pulse"></div>
                    <div class="absolute bottom-0 right-0 w-32 h-32 bg-white rounded-full blur-3xl animate-bounce" style="animation-delay: 1s"></div>
                </div>
                
                <div class="text-center relative z-10">
                    <div class="inline-flex items-center justify-center w-16 h-16 bg-white/20 rounded-full mb-4 backdrop-blur-sm">
                        <x-lucide-thermometer class="w-8 h-8 text-white" />
                    </div>
                    <h3 class="text-2xl md:text-3xl font-bold mb-2 drop-shadow-lg">
                        Đề xuất theo thời tiết hiện tại
                    </h3>
                    <p class="text-lg opacity-90 max-w-2xl mx-auto drop-shadow-md">
                        Những món ăn được chọn lọc phù hợp với điều kiện thời tiết của bạn
                    </p>
                    
                    @if(config('app.debug'))
                    <!-- Debug Tools (only in debug mode) -->
                    <div class="mt-4 flex flex-wrap justify-center gap-2">
                        <button onclick="debugLocation()" 
                                class="px-3 py-1 bg-white/20 text-white text-sm rounded-lg hover:bg-white/30 transition-colors">
                            🔍 Debug Info
                        </button>
                        <button onclick="clearLocationCache()" 
                                class="px-3 py-1 bg-white/20 text-white text-sm rounded-lg hover:bg-white/30 transition-colors">
                            🧹 Clear Cache
                        </button>
                        <button onclick="forceNinhBinh()" 
                                class="px-3 py-1 bg-white/20 text-white text-sm rounded-lg hover:bg-white/30 transition-colors">
                            🎯 Force Ninh Bình
                        </button>
                    </div>
                    @endif
                </div>
                
                @if(!$nearestCity)
                    <div class="mt-6 bg-white/20 backdrop-blur-sm rounded-xl p-4 max-w-md mx-auto border border-white/20">
                        <div class="text-center space-y-4">
                            <div class="flex flex-col sm:flex-row gap-3 justify-center">
                                <button onclick="showLocationModal()" 
                                        class="group inline-flex items-center px-6 py-3 bg-white/20 hover:bg-white/30 text-white text-sm font-semibold rounded-full transition-all duration-300 backdrop-blur-sm border border-white/20 hover:scale-105 transform">
                                    <x-lucide-crosshair class="w-4 h-4 mr-2 group-hover:rotate-90 transition-transform duration-300" />
                                    Lấy vị trí của tôi
                                </button>
                                <button wire:click="randomCity" 
                                        class="group inline-flex items-center px-6 py-3 bg-white/20 hover:bg-white/30 text-white text-sm font-semibold rounded-full transition-all duration-300 backdrop-blur-sm border border-white/20 hover:scale-105 transform">
                                    <x-lucide-shuffle class="w-4 h-4 mr-2 group-hover:rotate-12 transition-transform duration-300" />
                                    Chọn ngẫu nhiên
                                </button>
                            </div>
                            <p class="text-xs opacity-80 font-medium">Chọn vị trí để nhận đề xuất phù hợp nhất</p>
                        </div>
                    </div>
                @else
                    <div class="mt-6 bg-white/20 backdrop-blur-sm rounded-xl p-4 max-w-md mx-auto border border-white/20">
                        <div class="flex items-center justify-center">
                            <x-lucide-map-pin class="w-5 h-5 mr-2 text-white animate-bounce" />
                            <span class="text-sm font-medium drop-shadow-sm">
                                Thành phố hiện tại: <strong>{{ $nearestCity->name }}</strong>
                            </span>
                        </div>
                        @if(session('user_location.is_random'))
                            <p class="text-xs opacity-80 text-center mt-2">Được chọn ngẫu nhiên</p>
                        @else
                            <p class="text-xs opacity-80 text-center mt-2">Dựa trên vị trí của bạn</p>
                        @endif
                    </div>
                @endif
            </div>

            <!-- Content Section -->
            <div class="p-8 bg-gradient-to-br from-gray-50/50 to-white/50 dark:from-slate-800/50 dark:to-slate-900/50">
                @if(count($recipes) > 0)
                    <!-- Weather Info Card -->
                    <div class="bg-gradient-to-br from-white to-gray-50/50 dark:from-slate-800 dark:to-slate-900 rounded-2xl p-8 mb-8 border border-gray-200/50 dark:border-slate-700/50 shadow-xl backdrop-blur-sm">
                        <div class="flex flex-col lg:flex-row items-center justify-between">
                            <div class="flex items-center mb-6 lg:mb-0">
                                <div class="w-20 h-20 bg-gradient-to-br from-orange-500 to-red-600 rounded-2xl flex items-center justify-center mr-6 shadow-2xl group-hover:scale-110 transition-transform duration-300">
                                    <x-lucide-cloud-sun class="w-10 h-10 text-white animate-pulse" />
                                </div>
                                <div class="mr-6">
                                    <!-- City Selector -->
                                    <div class="relative group">
                                        <select wire:model.live="selectedCity" wire:change="changeCity($event.target.value)" 
                                                class="appearance-none bg-gradient-to-r from-orange-100 to-red-100 dark:from-orange-900/30 dark:to-red-900/30 border-2 border-orange-200 dark:border-orange-800 rounded-xl px-4 py-2 text-xl font-bold text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-orange-500 cursor-pointer pr-10 hover:shadow-lg transition-all duration-300">
                                            @foreach(\App\Models\VietnamCity::active()->ordered()->get()->groupBy('region') as $region => $cities)
                                                <optgroup label="{{ $region }}">
                                                    @foreach($cities as $city)
                                                        <option value="{{ $city->code }}" {{ $selectedCity === $city->code ? 'selected' : '' }}>
                                                            {{ $city->name }}
                                                        </option>
                                                    @endforeach
                                                </optgroup>
                                            @endforeach
                                        </select>
                                        <div class="absolute right-3 top-1/2 transform -translate-y-1/2 pointer-events-none">
                                            <x-lucide-chevron-down class="w-5 h-5 text-orange-500 group-hover:text-red-600 transition-colors duration-300" />
                                        </div>
                                    </div>
                                    @if($weatherData)
                                        <p class="text-sm text-gray-600 dark:text-gray-400 mt-2 font-medium">{{ $weatherData['weather_description'] ?? 'Thời tiết đẹp' }}</p>
                                    @endif
                                </div>
                            </div>
                            <div class="grid grid-cols-1 sm:grid-cols-3 gap-6 lg:gap-8 w-full lg:w-auto">
                                @if($weatherData)
                                    <div class="text-center group">
                                        <div class="bg-gradient-to-br from-orange-100 to-red-100 dark:from-orange-900/30 dark:to-red-900/30 rounded-2xl p-4 border border-orange-200 dark:border-orange-800 group-hover:shadow-lg transition-all duration-300">
                                            <div class="flex items-center justify-center mb-2">
                                                <x-lucide-thermometer class="w-5 h-5 text-orange-500 mr-2" />
                                                <span class="text-sm font-semibold text-gray-600 dark:text-gray-400">Nhiệt độ</span>
                                            </div>
                                            <p class="text-3xl font-black text-orange-600 dark:text-orange-400">
                                                {{ number_format($weatherData['temperature'], 1) }}°C
                                            </p>
                                        </div>
                                    </div>
                                    <div class="text-center group">
                                        <div class="bg-gradient-to-br from-blue-100 to-cyan-100 dark:from-blue-900/30 dark:to-cyan-900/30 rounded-2xl p-4 border border-blue-200 dark:border-blue-800 group-hover:shadow-lg transition-all duration-300">
                                            <div class="flex items-center justify-center mb-2">
                                                <x-lucide-droplets class="w-5 h-5 text-blue-500 mr-2" />
                                                <span class="text-sm font-semibold text-gray-600 dark:text-gray-400">Độ ẩm</span>
                                            </div>
                                            <p class="text-3xl font-black text-blue-600 dark:text-blue-400">{{ $weatherData['humidity'] }}%</p>
                                        </div>
                                    </div>
                                @endif
                                <div class="text-center group">
                                    <div class="bg-gradient-to-br from-green-100 to-emerald-100 dark:from-green-900/30 dark:to-emerald-900/30 rounded-2xl p-4 border border-green-200 dark:border-green-800 group-hover:shadow-lg transition-all duration-300">
                                        <div class="flex items-center justify-center mb-2">
                                            <x-lucide-lightbulb class="w-5 h-5 text-green-500 mr-2" />
                                            <span class="text-sm font-semibold text-gray-600 dark:text-gray-400">Đề xuất</span>
                                        </div>
                                        <p class="text-sm font-bold text-green-700 dark:text-green-300">
                                            Phù hợp với thời tiết
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Slideshow Container -->
                    <div class="relative bg-gradient-to-br from-white to-gray-50/50 dark:from-slate-800 dark:to-slate-900 rounded-2xl shadow-2xl overflow-hidden border border-gray-200/50 dark:border-slate-700/50 backdrop-blur-sm">
                        <!-- Slides -->
                        <div class="relative h-[500px] lg:h-[400px]">
                            @foreach($recipes as $index => $recipe)
                                <div class="absolute inset-0 transition-all duration-700 ease-in-out {{ $index === $currentSlide ? 'opacity-100 scale-100' : 'opacity-0 scale-95' }}"
                                     id="slide-{{ $index }}">
                                    <div class="flex flex-col lg:flex-row h-full">
                                        <!-- Recipe Image -->
                                        <div class="w-full lg:w-1/2 relative overflow-hidden">
                                            @if($recipe->featured_image)
                                                <img src="{{ asset('storage/' . $recipe->featured_image) }}" 
                                                     alt="{{ $recipe->title }}" 
                                                     class="w-full h-full object-cover transform hover:scale-110 transition-transform duration-700">
                                            @else
                                                <div class="w-full h-full bg-gradient-to-br from-orange-200 to-red-200 dark:from-orange-800 dark:to-red-900 flex items-center justify-center">
                                                    <x-lucide-utensils class="w-24 h-24 text-orange-400 dark:text-orange-300 animate-pulse" />
                                                </div>
                                            @endif
                                            <!-- Gradient Overlay -->
                                            <div class="absolute inset-0 bg-gradient-to-r from-black/20 via-transparent to-transparent lg:bg-gradient-to-t lg:from-black/30 lg:via-transparent lg:to-transparent"></div>
                                            
                                            <!-- Badge -->
                                            <div class="absolute top-6 left-6">
                                                <div class="bg-gradient-to-r from-orange-500 to-red-600 text-white px-4 py-2 rounded-full text-sm font-bold shadow-xl backdrop-blur-sm border border-white/20">
                                                    <span class="flex items-center">
                                                        <x-lucide-chef-hat class="w-4 h-4 mr-2" />
                                                        Món {{ $index + 1 }}/{{ count($recipes) }}
                                                    </span>
                                                </div>
                                            </div>

                                            <!-- Weather Indicator -->
                                            <div class="absolute top-6 right-6">
                                                <div class="bg-white/90 dark:bg-slate-800/90 backdrop-blur-sm rounded-full p-3 shadow-lg">
                                                    <x-lucide-cloud-sun class="w-5 h-5 text-orange-500" />
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Recipe Info -->
                                        <div class="w-full lg:w-1/2 p-8 flex flex-col justify-center bg-gradient-to-br from-white/80 to-gray-50/80 dark:from-slate-800/80 dark:to-slate-900/80 backdrop-blur-sm">
                                            <div class="mb-6">
                                                <div class="inline-flex items-center px-3 py-1 rounded-full bg-gradient-to-r from-orange-100 to-red-100 dark:from-orange-900/30 dark:to-red-900/30 border border-orange-200 dark:border-orange-800 mb-3">
                                                    <x-lucide-sparkles class="w-3 h-3 mr-1 text-orange-500" />
                                                    <span class="text-xs font-semibold text-orange-600 dark:text-orange-400">Được đề xuất</span>
                                                </div>
                                                <h3 class="text-2xl lg:text-3xl font-black text-gray-900 dark:text-white mb-3 leading-tight">{{ $recipe->title }}</h3>
                                                <p class="text-gray-600 dark:text-gray-300 line-clamp-3 text-base leading-relaxed">{{ $recipe->summary }}</p>
                                            </div>

                                            <div class="grid grid-cols-2 gap-4 mb-6">
                                                <div class="flex items-center bg-gradient-to-r from-blue-50 to-cyan-50 dark:from-blue-900/20 dark:to-cyan-900/20 rounded-xl p-3 border border-blue-100 dark:border-blue-800">
                                                    <x-lucide-clock class="w-5 h-5 mr-2 text-blue-500" />
                                                    <div>
                                                        <p class="text-xs text-gray-500 dark:text-gray-400 font-medium">Thời gian</p>
                                                        <p class="text-sm font-bold text-blue-600 dark:text-blue-400">{{ $recipe->cooking_time }} phút</p>
                                                    </div>
                                                </div>
                                                <div class="flex items-center bg-gradient-to-r from-yellow-50 to-orange-50 dark:from-yellow-900/20 dark:to-orange-900/20 rounded-xl p-3 border border-yellow-100 dark:border-yellow-800">
                                                    <x-lucide-star class="w-5 h-5 mr-2 text-yellow-500 fill-current" />
                                                    <div>
                                                        <p class="text-xs text-gray-500 dark:text-gray-400 font-medium">Đánh giá</p>
                                                        <p class="text-sm font-bold text-yellow-600 dark:text-yellow-400">{{ number_format($recipe->average_rating, 1) }}/5</p>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="flex flex-col sm:flex-row gap-3">
                                                <a href="{{ route('recipes.show', $recipe->slug) }}" 
                                                   class="group flex-1 inline-flex items-center justify-center bg-gradient-to-r from-orange-500 to-red-600 text-white py-4 px-6 rounded-2xl hover:shadow-2xl transform hover:scale-105 transition-all duration-300 font-bold text-base">
                                                    <span>Xem chi tiết</span>
                                                    <x-lucide-arrow-right class="w-5 h-5 ml-2 group-hover:translate-x-1 transition-transform duration-300" />
                                                </a>
                                                <button class="group bg-gradient-to-r from-gray-100 to-gray-200 dark:from-slate-700 dark:to-slate-800 text-gray-700 dark:text-gray-300 p-4 rounded-2xl hover:shadow-lg transform hover:scale-105 transition-all duration-300 border border-gray-200 dark:border-slate-600">
                                                    <x-lucide-heart class="w-6 h-6 group-hover:text-red-500 group-hover:fill-current transition-all duration-300" />
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <!-- Navigation Arrows -->
                        <button wire:click="previousSlide" 
                                class="group absolute left-6 top-1/2 transform -translate-y-1/2 bg-white/90 dark:bg-slate-800/90 backdrop-blur-sm hover:bg-white dark:hover:bg-slate-800 text-gray-800 dark:text-white p-4 rounded-2xl shadow-2xl transition-all duration-300 hover:scale-110 border border-gray-200/50 dark:border-slate-700/50">
                            <x-lucide-chevron-left class="w-6 h-6 group-hover:-translate-x-1 transition-transform duration-300" />
                        </button>

                        <button wire:click="nextSlide" 
                                class="group absolute right-6 top-1/2 transform -translate-y-1/2 bg-white/90 dark:bg-slate-800/90 backdrop-blur-sm hover:bg-white dark:hover:bg-slate-800 text-gray-800 dark:text-white p-4 rounded-2xl shadow-2xl transition-all duration-300 hover:scale-110 border border-gray-200/50 dark:border-slate-700/50">
                            <x-lucide-chevron-right class="w-6 h-6 group-hover:translate-x-1 transition-transform duration-300" />
                        </button>

                        <!-- Dots Indicator -->
                        <div class="absolute bottom-6 left-1/2 transform -translate-x-1/2 flex space-x-3 bg-white/20 dark:bg-slate-800/20 backdrop-blur-sm rounded-full px-4 py-2 border border-white/20 dark:border-slate-700/20">
                            @foreach($recipes as $index => $recipe)
                                <button wire:click="goToSlide({{ $index }})" 
                                        class="dot-indicator w-3 h-3 rounded-full transition-all duration-300 {{ $index === $currentSlide ? 'bg-gradient-to-r from-orange-500 to-red-600 scale-125' : 'bg-gray-400 dark:bg-gray-500 hover:bg-gray-600 dark:hover:bg-gray-400 hover:scale-110' }}">
                                </button>
                            @endforeach
                        </div>
                    </div>

                    <!-- View All Button -->
                    <div class="text-center mt-12">
                        <a href="{{ route('weather.suggestions') }}" 
                           class="group inline-flex items-center px-8 py-4 bg-gradient-to-r from-orange-500 to-red-600 text-white font-bold text-lg rounded-full shadow-xl hover:shadow-2xl transform hover:-translate-y-1 transition-all duration-300 hover:from-orange-600 hover:to-red-700">
                            <x-lucide-eye class="w-5 h-5 mr-2 group-hover:scale-110 transition-transform duration-300" />
                            <span>Xem tất cả đề xuất theo thời tiết</span>
                            <x-lucide-arrow-right class="w-5 h-5 ml-2 group-hover:translate-x-1 transition-transform duration-300" />
                        </a>
                    </div>
                @else
                    <!-- No Suggestions State -->
                    <div class="bg-gradient-to-br from-gray-50 to-white dark:from-slate-800 dark:to-slate-900 rounded-2xl p-16 text-center border border-gray-200/50 dark:border-slate-700/50 shadow-xl">
                        <div class="w-20 h-20 bg-gradient-to-r from-orange-200 to-red-200 dark:from-orange-800 dark:to-red-900 rounded-full flex items-center justify-center mx-auto mb-6 shadow-lg">
                            <x-lucide-cloud-off class="w-10 h-10 text-orange-400 dark:text-orange-300" />
                        </div>
                        <h3 class="text-2xl font-bold text-gray-900 dark:text-white mb-4">Chưa có đề xuất</h3>
                        <p class="text-gray-600 dark:text-gray-300 max-w-md mx-auto leading-relaxed">Hiện tại chưa có món ăn phù hợp với thời tiết này. Hãy thử chọn một thành phố khác hoặc thử lại sau.</p>
                        <button wire:click="randomCity" 
                                class="mt-6 inline-flex items-center px-6 py-3 bg-gradient-to-r from-orange-500 to-red-600 text-white font-semibold rounded-full hover:shadow-xl transform hover:scale-105 transition-all duration-300">
                            <x-lucide-shuffle class="w-4 h-4 mr-2" />
                            Thử thành phố khác
                        </button>
                    </div>
                @endif
            </div>
        </div>

        <!-- Bottom Stats Section -->
        <div class="text-center mt-16">
            <div class="inline-flex items-center gap-8 px-8 py-4 rounded-2xl bg-white/50 dark:bg-slate-800/50 backdrop-blur-sm border border-gray-200 dark:border-slate-700 shadow-lg">
                <div class="text-center">
                    <div class="text-2xl font-bold text-gray-900 dark:text-white">{{ count($recipes) ?? 0 }}</div>
                    <div class="text-sm text-gray-600 dark:text-gray-400">Món đề xuất</div>
                </div>
                @if($weatherData)
                <div class="w-px h-8 bg-gray-300 dark:bg-gray-600"></div>
                <div class="text-center">
                    <div class="text-2xl font-bold text-gray-900 dark:text-white">{{ $weatherData['temperature'] ?? 0 }}°C</div>
                    <div class="text-sm text-gray-600 dark:text-gray-400">Nhiệt độ hiện tại</div>
                </div>
                @endif
                <div class="w-px h-8 bg-gray-300 dark:bg-gray-600"></div>
                <div class="text-center">
                    <div class="text-2xl font-bold text-gray-900 dark:text-white">AI</div>
                    <div class="text-sm text-gray-600 dark:text-gray-400">Đề xuất thông minh</div>
                </div>
            </div>
        </div>
    </div>
    

    
    <style>
    .line-clamp-3 {
        display: -webkit-box;
        -webkit-line-clamp: 3;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }

    /* Weather slideshow animations */
    [id^="slide-"] {
        transition: opacity 0.7s cubic-bezier(0.25, 0.46, 0.45, 0.94), 
                    transform 0.7s cubic-bezier(0.25, 0.46, 0.45, 0.94);
    }

    /* Smooth dot indicator transitions */
    .dot-indicator,
    [wire\\:click*="goToSlide"] {
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }

    /* Enhanced hover effects for slideshow */
    #weather-section:hover [id^="slide-"] {
        transition-duration: 0.5s;
    }

    /* Accessibility: Reduced motion support */
    @media (prefers-reduced-motion: reduce) {
        [id^="slide-"] {
            transition: opacity 0.3s ease, transform 0.3s ease;
        }
        
        .dot-indicator,
        [wire\\:click*="goToSlide"] {
            transition: all 0.2s ease;
        }
    }

    /* Smooth scaling for active slides */
    [id^="slide-"].opacity-100 {
        transform: scale(1);
    }

    [id^="slide-"].opacity-0 {
        transform: scale(0.95);
    }

    /* Progressive enhancement for modern browsers */
    #weather-section .backdrop-blur-sm {
        backdrop-filter: blur(4px);
    }
    </style>

    <script>
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

        // Initialize weather slideshow after component updates
        Livewire.on('weather-slideshow-refresh', () => {
            if (window.weatherSlideshow) {
                window.weatherSlideshow.refresh();
            }
        });

        // Refresh slideshow when component is updated
        document.addEventListener('livewire:update', () => {
            setTimeout(() => {
                if (window.weatherSlideshow) {
                    window.weatherSlideshow.refresh();
                }
            }, 100);
        });

        // Listen for clear cache event
        Livewire.on('clear-location-cache', () => {
            console.log('🧹 Clearing browser location cache');
            localStorage.removeItem('user_location');
            sessionStorage.removeItem('user_location');
            console.log('✅ Browser cache cleared');
        });

        // Listen for location forced event
        Livewire.on('location-forced', (data) => {
            console.log('🎯 Location forced:', data.message);
            // Show toast notification
            if (typeof showToast === 'function') {
                showToast(data.message, 'success');
            } else {
                alert(data.message);
            }
        });

        // Debug functions
        window.debugLocation = function() {
            console.log('📍 LOCATION DEBUG INFO:');
            console.log('==================');
            
            // Check localStorage
            const localStorage_location = localStorage.getItem('user_location');
            console.log('🗄️ localStorage:', localStorage_location ? JSON.parse(localStorage_location) : 'None');
            
            // Check sessionStorage
            const sessionStorage_location = sessionStorage.getItem('user_location');
            console.log('💾 sessionStorage:', sessionStorage_location ? JSON.parse(sessionStorage_location) : 'None');
            
            // Check if geolocation is available
            if (navigator.geolocation) {
                console.log('🌐 Geolocation: Available');
                navigator.geolocation.getCurrentPosition(
                    (position) => {
                        console.log('📍 Current position:', {
                            latitude: position.coords.latitude,
                            longitude: position.coords.longitude,
                            accuracy: position.coords.accuracy
                        });
                    },
                    (error) => {
                        console.log('❌ Geolocation error:', error.message);
                    }
                );
            } else {
                console.log('❌ Geolocation: Not available');
            }
        };

        window.clearLocationCache = function() {
            console.log('🧹 Clearing all location cache...');
            localStorage.removeItem('user_location');
            sessionStorage.removeItem('user_location');
            console.log('✅ Browser cache cleared');
            @this.clearLocationCache();
        };

        window.forceNinhBinh = function() {
            console.log('🎯 Forcing location to Ninh Bình...');
            @this.forceNinhBinh();
        };
    });
    </script>
</div> 