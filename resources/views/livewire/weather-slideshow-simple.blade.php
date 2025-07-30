<div class="relative bg-gradient-to-br from-blue-50 to-indigo-100 py-12">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="text-center mb-8">
            <h2 class="text-3xl font-bold text-gray-900 mb-4">
                🌤️ Món Ăn Phù Hợp Với Thời Tiết
            </h2>
            <p class="text-lg text-gray-600 max-w-2xl mx-auto mb-6">
                Khám phá những món ăn ngon được đề xuất theo thời tiết hiện tại
            </p>
            
            @if(!$nearestCity)
                <!-- Nút lấy vị trí -->
                <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-3 mb-6 max-w-md mx-auto">
                    <div class="text-center space-y-3">
                        <button wire:click="getUserLocationFromBrowser" 
                                class="inline-flex items-center px-4 py-2 bg-yellow-500 hover:bg-yellow-600 text-white text-sm font-medium rounded-lg transition-colors">
                            <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd" />
                            </svg>
                            Lấy vị trí của tôi
                        </button>
                        <p class="text-xs text-yellow-700">Click để lấy vị trí và tìm thành phố gần nhất</p>
                        

                    </div>
                </div>
            @endif
        </div>

        @if(count($recipes) > 0)
            <!-- Weather Info -->
            <div class="bg-white rounded-lg shadow-md p-6 mb-8">
                <div class="flex flex-col md:flex-row items-center justify-between">
                    <div class="flex items-center mb-4 md:mb-0">
                        <div class="w-12 h-12 bg-gradient-to-br from-blue-400 to-blue-600 rounded-full flex items-center justify-center mr-4">
                            <svg class="w-6 h-6 text-white" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd" />
                            </svg>
                        </div>
                        <div class="mr-4">
                            <!-- City Selector -->
                            <div class="relative">
                                <select wire:model.live="selectedCity" wire:change="changeCity($event.target.value)" 
                                        class="appearance-none bg-transparent border-none text-lg font-semibold text-gray-900 focus:outline-none focus:ring-0 cursor-pointer pr-6">
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
                            </div>
                            @if($weatherData)
                                <p class="text-sm text-gray-600">{{ $weatherData->weather_description ?? 'Thời tiết đẹp' }}</p>
                            @endif

                        </div>
                    </div>
                    <div class="flex items-center space-x-6">
                        @if($weatherData)
                            <div class="text-center">
                                <p class="text-2xl font-bold text-blue-500">
                                    {{ number_format($weatherData->temperature, 1) }}°C
                                </p>
                                <p class="text-sm text-gray-600">Nhiệt độ</p>
                            </div>
                            <div class="text-center">
                                <p class="text-2xl font-bold text-blue-500">{{ $weatherData->humidity }}%</p>
                                <p class="text-sm text-gray-600">Độ ẩm</p>
                            </div>
                        @endif
                        <div class="text-center">
                            <p class="text-sm text-gray-800 bg-blue-50 px-3 py-1 rounded-full">
                                Món phù hợp với thời tiết
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Slideshow Container -->
            <div class="relative bg-white rounded-lg shadow-lg overflow-hidden">
                <!-- Slides -->
                <div class="relative h-96">
                    @foreach($recipes as $index => $recipe)
                        <div class="absolute inset-0 transition-opacity duration-500 ease-in-out {{ $index === $currentSlide ? 'opacity-100' : 'opacity-0' }}"
                             id="slide-{{ $index }}">
                            <div class="flex h-full">
                                <!-- Recipe Image -->
                                <div class="w-1/2 relative">
                                    @if($recipe->featured_image)
                                        <img src="{{ asset('storage/' . $recipe->featured_image) }}" 
                                             alt="{{ $recipe->title }}" 
                                             class="w-full h-full object-cover">
                                    @else
                                        <div class="w-full h-full bg-gradient-to-br from-orange-100 to-orange-200 flex items-center justify-center">
                                            <svg class="w-24 h-24 text-orange-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 002 2z"></path>
                                            </svg>
                                        </div>
                                    @endif
                                    <div class="absolute top-4 left-4">
                                        <span class="bg-orange-500 text-white px-3 py-1 rounded-full text-sm font-medium">
                                            Món {{ $index + 1 }}/{{ count($recipes) }}
                                        </span>
                                    </div>
                                </div>

                                <!-- Recipe Info -->
                                <div class="w-1/2 p-8 flex flex-col justify-center">
                                    <div class="mb-4">
                                        <h3 class="text-2xl font-bold text-gray-900 mb-2">{{ $recipe->title }}</h3>
                                        <p class="text-gray-600 line-clamp-3">{{ $recipe->summary }}</p>
                                    </div>

                                    <div class="flex items-center space-x-4 mb-4 text-sm text-gray-500">
                                        <div class="flex items-center">
                                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                            </svg>
                                            {{ $recipe->cooking_time }} phút
                                        </div>
                                        <div class="flex items-center">
                                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"></path>
                                            </svg>
                                            {{ number_format($recipe->average_rating, 1) }}
                                        </div>
                                    </div>

                                    <div class="flex space-x-3">
                                        <a href="{{ route('recipes.show', $recipe->slug) }}" 
                                           class="flex-1 bg-orange-600 text-white py-3 px-4 rounded-md hover:bg-orange-700 transition-colors text-center font-medium">
                                            Xem chi tiết
                                        </a>
                                        <button class="bg-gray-100 text-gray-700 p-3 rounded-md hover:bg-gray-200 transition-colors">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
                                            </svg>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- Navigation Arrows -->
                <button wire:click="previousSlide" 
                        class="absolute left-4 top-1/2 transform -translate-y-1/2 bg-white bg-opacity-80 hover:bg-opacity-100 text-gray-800 p-2 rounded-full shadow-lg transition-all duration-200">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                    </svg>
                </button>

                <button wire:click="nextSlide" 
                        class="absolute right-4 top-1/2 transform -translate-y-1/2 bg-white bg-opacity-80 hover:bg-opacity-100 text-gray-800 p-2 rounded-full shadow-lg transition-all duration-200">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                    </svg>
                </button>

                <!-- Dots Indicator -->
                <div class="absolute bottom-4 left-1/2 transform -translate-x-1/2 flex space-x-2">
                    @foreach($recipes as $index => $recipe)
                        <button wire:click="goToSlide({{ $index }})" 
                                class="w-3 h-3 rounded-full transition-all duration-200 {{ $index === $currentSlide ? 'bg-orange-500' : 'bg-gray-300 hover:bg-gray-400' }}">
                        </button>
                    @endforeach
                </div>
            </div>

            <!-- View All Button -->
            <div class="text-center mt-8">
                <a href="{{ route('weather.suggestions') }}" 
                   class="inline-flex items-center px-6 py-3 border border-orange-600 text-base font-medium rounded-md text-orange-600 bg-white hover:bg-orange-50 transition-colors">
                    Xem tất cả đề xuất theo thời tiết
                    <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                    </svg>
                </a>
            </div>
        @else
            <!-- No Suggestions State -->
            <div class="bg-white rounded-lg shadow-md p-12 text-center">
                <div class="text-gray-400 mb-4">
                    <svg class="w-16 h-16 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 12h6m-6-4h6m2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                </div>
                <h3 class="text-lg font-medium text-gray-900 mb-2">Không có đề xuất</h3>
                <p class="text-gray-600">Hiện tại chưa có món ăn phù hợp với thời tiết này.</p>
            </div>
        @endif
    </div>
    
    <style>
    .line-clamp-3 {
        display: -webkit-box;
        -webkit-line-clamp: 3;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }
    </style>

    <script>
    document.addEventListener('livewire:init', () => {
        Livewire.on('get-user-location', () => {
            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(
                    (position) => {
                        const latitude = position.coords.latitude;
                        const longitude = position.coords.longitude;
                        
                        console.log('Đã lấy được vị trí:', latitude, longitude);
                        
                        // Gửi tọa độ về Livewire
                        @this.setUserLocation(latitude, longitude);
                    },
                    (error) => {
                        console.log('Lỗi lấy vị trí:', error.message);
                        alert('Không thể lấy vị trí: ' + error.message);
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
    });
    </script>
</div> 