<div class="relative bg-gradient-to-br from-blue-50 to-indigo-100 py-12" x-data="weatherSlideshow()">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="text-center mb-8">
            <h2 class="text-3xl font-bold text-gray-900 mb-4">
                🌤️ Món Ăn Phù Hợp Với Thời Tiết
            </h2>
            <p class="text-lg text-gray-600 max-w-2xl mx-auto mb-6">
                Khám phá những món ăn ngon được đề xuất theo thời tiết hiện tại
            </p>
            
            @if($nearestCity)
                <div class="bg-green-50 border border-green-200 rounded-lg p-3 mb-6 max-w-md mx-auto">
                    <div class="flex items-center">
                        <svg class="w-5 h-5 text-green-500 mr-2" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd" />
                        </svg>
                        <span class="text-sm text-green-700">
                            Đã tự động chọn <strong>{{ $nearestCity->name }}</strong> (thành phố gần nhất)
                        </span>
                    </div>
                </div>
            @else
                <!-- Nút test lấy vị trí thủ công -->
                <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-3 mb-6 max-w-md mx-auto">
                    <div class="text-center space-y-3">
                        <div class="flex flex-col sm:flex-row gap-3 justify-center">
                            <button wire:click="getUserLocationFromBrowser"
                                    class="inline-flex items-center px-4 py-2 bg-yellow-500 hover:bg-yellow-600 text-white text-sm font-medium rounded-lg transition-colors">
                                <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd" />
                                </svg>
                                Lấy vị trí của tôi
                            </button>
                            <button wire:click="randomCity"
                                    class="inline-flex items-center px-4 py-2 bg-blue-500 hover:bg-blue-600 text-white text-sm font-medium rounded-lg transition-colors">
                                <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M3 4a1 1 0 011-1h12a1 1 0 011 1v2a1 1 0 01-1 1H4a1 1 0 01-1-1V4zM3 10a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H4a1 1 0 01-1-1v-6zM14 9a1 1 0 00-1 1v6a1 1 0 001 1h2a1 1 0 001-1v-6a1 1 0 00-1-1h-2z" clip-rule="evenodd" />
                                </svg>
                                Chọn ngẫu nhiên
                            </button>
                        </div>
                        <p class="text-xs text-yellow-700">Click để lấy vị trí hoặc chọn thành phố ngẫu nhiên</p>
                    </div>
                </div>
            @endif
            

            

        </div>

        @if($loading)
            <!-- Loading State -->
            <div class="text-center py-12">
                <div class="inline-flex items-center px-4 py-2 font-semibold leading-6 text-blue-600 transition ease-in-out duration-150">
                    <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-blue-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    Đang tải đề xuất món ăn...
                </div>
            </div>
        @elseif($error)
            <!-- Error State -->
            <div class="bg-red-50 border border-red-200 rounded-lg p-6 text-center">
                <div class="flex justify-center mb-4">
                    <svg class="h-12 w-12 text-red-400" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                    </svg>
                </div>
                <h3 class="text-lg font-medium text-red-800 mb-2">Không thể tải dữ liệu</h3>
                <p class="text-red-600">{{ $error }}</p>
            </div>
        @elseif($suggestions->count() > 0)
            <!-- Weather Info -->
            <div class="bg-white rounded-lg shadow-md p-6 mb-8">
                <div class="flex flex-col md:flex-row items-center justify-between">
                    <div class="flex items-center mb-4 md:mb-0">
                        <div class="w-12 h-12 bg-gradient-to-br from-blue-400 to-blue-600 rounded-full flex items-center justify-center mr-4">
                            <svg class="w-6 h-6 text-white {{ $this->getWeatherIcon($weatherData->weather_category) }}" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd" />
                            </svg>
                        </div>
                        <div class="mr-4">
                            <!-- City Selector -->
                            <div class="relative">
                                <select wire:model.live="selectedCity" 
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
                            <p class="text-sm text-gray-600">{{ $weatherData->weather_description }}</p>
                            @if($nearestCity && $selectedCity === $nearestCity->code)
                                <p class="text-xs text-green-600 mt-1">✓ Đã tự động chọn {{ $nearestCity->name }}</p>
                            @endif
                        </div>
                    </div>
                    <div class="flex items-center space-x-6">
                        <div class="text-center">
                            <p class="text-2xl font-bold {{ $this->getTemperatureColor($weatherData->temperature) }}">
                                {{ number_format($weatherData->temperature, 1) }}°C
                            </p>
                            <p class="text-sm text-gray-600">Nhiệt độ</p>
                        </div>
                        <div class="text-center">
                            <p class="text-2xl font-bold text-blue-500">{{ $weatherData->humidity }}%</p>
                            <p class="text-sm text-gray-600">Độ ẩm</p>
                        </div>
                        <div class="text-center">
                            <p class="text-sm text-gray-800 bg-blue-50 px-3 py-1 rounded-full">
                                {{ $this->getSuggestionReason() }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Slideshow Container -->
            <div class="relative bg-white rounded-lg shadow-lg overflow-hidden">
                <!-- Slides -->
                <div class="relative h-96">
                    @foreach($suggestions as $index => $recipe)
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
                                            Món {{ $index + 1 }}/{{ $suggestions->count() }}
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
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"></path>
                                            </svg>
                                            {{ number_format($recipe->average_rating, 1) }}
                                        </div>
                                    </div>

                                    <div class="flex flex-wrap gap-2 mb-6">
                                        @foreach($recipe->categories->take(3) as $category)
                                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                                {{ $category->name }}
                                            </span>
                                        @endforeach
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
                    @foreach($suggestions as $index => $recipe)
                        <button wire:click="goToSlide({{ $index }})" 
                                class="w-3 h-3 rounded-full transition-all duration-200 {{ $index === $currentSlide ? 'bg-orange-500' : 'bg-gray-300 hover:bg-gray-400' }}">
                        </button>
                    @endforeach
                </div>

                <!-- Auto-play Toggle -->
                <button wire:click="toggleAutoPlay" 
                        class="absolute top-4 right-4 bg-white bg-opacity-80 hover:bg-opacity-100 text-gray-800 p-2 rounded-full shadow-lg transition-all duration-200">
                    @if($autoPlay)
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 9v6m4-6v6m7-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    @else
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.828 14.828a4 4 0 01-5.656 0M9 10h1m4 0h1m-6 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    @endif
                </button>
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
    
    <!-- Modal thông báo vị trí -->
    <div id="location-modal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
            <div class="mt-3 text-center">
                <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-blue-100 mb-4">
                    <svg class="h-6 w-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                    </svg>
                </div>
                <h3 class="text-lg leading-6 font-medium text-gray-900 mb-2">Chia sẻ vị trí</h3>
                <div class="mt-2 px-7 py-3">
                    <p class="text-sm text-gray-500 mb-4">
                        Bạn có muốn chia sẻ vị trí hiện tại để nhận đề xuất món ăn phù hợp với thời tiết không?
                    </p>
                    <div class="flex items-center justify-center space-x-4">
                        <button id="location-yes" class="px-4 py-2 bg-blue-500 text-white text-base font-medium rounded-md shadow-sm hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-300 transition-colors">
                            Có, chia sẻ
                        </button>
                        <button id="location-no" class="px-4 py-2 bg-gray-300 text-gray-700 text-base font-medium rounded-md shadow-sm hover:bg-gray-400 focus:outline-none focus:ring-2 focus:ring-gray-300 transition-colors">
                            Không, chọn ngẫu nhiên
                        </button>
                    </div>
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
    </style>

    <script>
    function weatherSlideshow() {
        return {
            init() {
                // Không tự động lấy vị trí khi khởi tạo
                console.log('WeatherSlideshow initialized');
            },
            
            getUserLocation() {
                if (navigator.geolocation) {
                    navigator.geolocation.getCurrentPosition(
                        (position) => {
                            const latitude = position.coords.latitude;
                            const longitude = position.coords.longitude;
                            
                            console.log('Đã lấy được vị trí:', latitude, longitude);
                            console.log('Tọa độ đã lấy được, nhưng không thể gửi về Livewire');
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
            }
        }
    }

    document.addEventListener('livewire:init', () => {
        // Hàm hiển thị modal
        function showLocationModal() {
            const modal = document.getElementById('location-modal');
            modal.classList.remove('hidden');
        }
        
        // Hàm ẩn modal
        function hideLocationModal() {
            const modal = document.getElementById('location-modal');
            modal.classList.add('hidden');
        }
        
        // Xử lý sự kiện click nút trong modal
        document.getElementById('location-yes').addEventListener('click', function() {
            hideLocationModal();
            // Lấy vị trí
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
                        
                        // Khi người dùng từ chối vị trí, tự động chọn ngẫu nhiên
                        if (error.code === 1) { // PERMISSION_DENIED
                            console.log('Người dùng từ chối vị trí, chọn ngẫu nhiên...');
                            @this.randomCity();
                        } else {
                            alert('Không thể lấy vị trí: ' + error.message);
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
                alert('Trình duyệt không hỗ trợ lấy vị trí');
                @this.randomCity();
            }
        });
        
        document.getElementById('location-no').addEventListener('click', function() {
            hideLocationModal();
            // Chọn ngẫu nhiên
            console.log('Người dùng không muốn chia sẻ vị trí, chọn ngẫu nhiên...');
            @this.randomCity();
        });

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

        Livewire.on('slide-changed', (event) => {
            const currentSlide = event.currentSlide;
            const totalSlides = event.totalSlides;
            
            // Hide all slides
            for (let i = 0; i < totalSlides; i++) {
                const slide = document.getElementById(`slide-${i}`);
                if (slide) {
                    slide.classList.remove('opacity-100');
                    slide.classList.add('opacity-0');
                }
            }
            
            // Show current slide
            const currentSlideElement = document.getElementById(`slide-${currentSlide}`);
            if (currentSlideElement) {
                currentSlideElement.classList.remove('opacity-0');
                currentSlideElement.classList.add('opacity-100');
            }
        });

        Livewire.on('alert', (event) => {
            alert(event.message);
        });
    });
    </script>
</div> 