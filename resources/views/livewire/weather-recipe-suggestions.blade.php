<div class="min-h-screen bg-gradient-to-br from-blue-50 to-indigo-100 py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="text-center mb-8">
            <h1 class="text-4xl font-bold text-gray-900 mb-4">
                🌤️ Đề Xuất Món Ăn Theo Thời Tiết
            </h1>
            <p class="text-lg text-gray-600 max-w-2xl mx-auto">
                Khám phá những món ăn phù hợp với thời tiết hiện tại tại thành phố của bạn
            </p>
            
            @if(!$nearestCity)
                <!-- Nút lấy vị trí -->
                <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-3 mb-6 max-w-md mx-auto">
                    <div class="text-center space-y-3">
                        <div class="flex flex-col sm:flex-row gap-3 justify-center">
                            <button onclick="showLocationModal()" 
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
            @else
                <!-- Hiển thị thông tin thành phố hiện tại -->
                <div class="bg-green-50 border border-green-200 rounded-lg p-3 mb-6 max-w-md mx-auto">
                    <div class="text-center">
                        <div class="flex items-center justify-center mb-2">
                            <svg class="w-4 h-4 mr-2 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd" />
                            </svg>
                            <span class="text-sm font-medium text-green-800">Thành phố <span class="text-red-500">*</span> hiện tại: {{ $nearestCity->name }}</span>
                        </div>
                        @if(session('user_location.is_random'))
                            <p class="text-xs text-green-700">Được chọn ngẫu nhiên</p>
                        @else
                            <p class="text-xs text-green-700">Dựa trên vị trí của bạn</p>
                        @endif
                    </div>
                </div>
            @endif
        </div>

        <!-- Test Component -->
        
        
        <!-- City Selector -->
        <div class="bg-white rounded-lg shadow-md p-6 mb-8">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div class="flex-1">
                    <label for="city-select" class="block text-sm font-medium text-gray-700 mb-2">
                        Chọn thành phố
                    </label>
                    <select 
                        wire:model.live="selectedCity" 
                        id="city-select"
                        class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
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

                </div>
                
                <button 
                    wire:click="loadWeatherAndSuggestions"
                    class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-colors"
                    :disabled="$wire.loading"
                >
                    <svg wire:loading wire:target="loadWeatherAndSuggestions" class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    <svg wire:loading.remove wire:target="loadWeatherAndSuggestions" class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                    </svg>
                    Cập nhật
                </button>
            </div>
        </div>

        @if($error)
            <div class="bg-red-50 border border-red-200 rounded-md p-4 mb-8">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-red-400" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm text-red-800">{{ $error }}</p>
                    </div>
                </div>
            </div>
        @endif

        @if($loading)
            <div class="text-center py-12">
                <div class="inline-flex items-center px-4 py-2 font-semibold leading-6 text-blue-600 transition ease-in-out duration-150">
                    <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-blue-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    Đang tải thông tin thời tiết và đề xuất món ăn...
                </div>
            </div>
        @elseif($weatherData)
            <!-- Weather Information -->
            <div class="bg-white rounded-lg shadow-md p-6 mb-8">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <!-- Current Weather -->
                    <div class="text-center">
                        <div class="flex justify-center mb-4">
                            <div class="w-16 h-16 bg-gradient-to-br from-blue-400 to-blue-600 rounded-full flex items-center justify-center">
                                <svg class="w-8 h-8 text-white {{ $this->getWeatherIcon($weatherData['weather_category'] ?? 'normal') }}" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd" />
                                </svg>
                            </div>
                        </div>
                        <h3 class="text-lg font-semibold text-gray-900 mb-2">Thời tiết hiện tại</h3>
                        <p class="text-3xl font-bold {{ $this->getTemperatureColor($weatherData['temperature']) }} mb-1">
                            {{ number_format($weatherData['temperature'], 1) }}°C
                        </p>
                        <p class="text-gray-600">{{ $weatherData['description'] ?? 'Không có mô tả' }}</p>
                    </div>

                    <!-- Weather Details -->
                    <div class="space-y-4">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Chi tiết thời tiết</h3>
                        <div class="grid grid-cols-2 gap-4">
                            <div class="flex items-center">
                                <svg class="w-5 h-5 text-gray-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 15a4 4 0 004 4h9a5 5 0 10-.1-9.999 5.002 5.002 0 10-9.78 2.096A4.001 4.001 0 003 15z"></path>
                                </svg>
                                <span class="text-sm text-gray-600">Độ ẩm: {{ $weatherData['humidity'] }}%</span>
                            </div>
                            <div class="flex items-center">
                                <svg class="w-5 h-5 text-gray-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.828 14.828a4 4 0 01-5.656 0M9 10h1m4 0h1m-6 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                <span class="text-sm text-gray-600">Cảm giác: {{ number_format($weatherData['feels_like'] ?? 0, 1) }}°C</span>
                            </div>
                            <div class="flex items-center">
                                <svg class="w-5 h-5 text-gray-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                                </svg>
                                <span class="text-sm text-gray-600">Gió: {{ number_format($weatherData['wind_speed'] ?? 0, 1) }} m/s</span>
                            </div>
                            <div class="flex items-center">
                                <svg class="w-5 h-5 text-gray-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                <span class="text-sm text-gray-600">Tầm nhìn: {{ number_format(($weatherData['visibility'] ?? 0) / 1000, 1) }} km</span>
                            </div>
                        </div>
                    </div>

                    <!-- Suggestion Reason -->
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Lý do đề xuất</h3>
                        <div class="bg-blue-50 rounded-lg p-4">
                            <p class="text-sm text-blue-800 leading-relaxed">
                                {{ $this->getSuggestionReason() }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Recipe Suggestions -->
            @if($suggestions->count() > 0)
                <div class="bg-white rounded-lg shadow-md p-6">
                    <h2 class="text-2xl font-bold text-gray-900 mb-6">
                        🍽️ Món ăn phù hợp với thời tiết
                    </h2>
                    
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                        @foreach($suggestions as $recipe)
                            <div class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-lg transition-shadow duration-300">
                                @if($recipe->featured_image)
                                    <img src="{{ asset('storage/' . $recipe->featured_image) }}" 
                                         alt="{{ $recipe->title }}" 
                                         class="w-full h-48 object-cover">
                                @else
                                    <div class="w-full h-48 bg-gray-200 flex items-center justify-center">
                                        <svg class="w-12 h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                        </svg>
                                    </div>
                                @endif
                                
                                <div class="p-4">
                                    <h3 class="text-lg font-semibold text-gray-900 mb-2 line-clamp-2">
                                        {{ $recipe->title }}
                                    </h3>
                                    
                                    <p class="text-sm text-gray-600 mb-3 line-clamp-2">
                                        {{ $recipe->summary }}
                                    </p>
                                    
                                    <div class="flex items-center justify-between text-sm text-gray-500 mb-3">
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
                                    
                                    <div class="flex flex-wrap gap-1 mb-3">
                                        @foreach($recipe->categories->take(2) as $category)
                                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                                {{ $category->name }}
                                            </span>
                                        @endforeach
                                    </div>
                                    
                                    <a href="{{ route('recipes.show', $recipe->slug) }}" 
                                       class="block w-full text-center bg-blue-600 text-white py-2 px-4 rounded-md hover:bg-blue-700 transition-colors text-sm font-medium">
                                        Xem chi tiết
                                    </a>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @else
                <div class="bg-white rounded-lg shadow-md p-6 text-center">
                    <div class="text-gray-400 mb-4">
                        <svg class="w-16 h-16 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 12h6m-6-4h6m2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                    </div>
                    <h3 class="text-lg font-medium text-gray-900 mb-2">Không có đề xuất</h3>
                    <p class="text-gray-600">Hiện tại chưa có món ăn phù hợp với thời tiết này.</p>
                </div>
            @endif
        @elseif(!$loading && !$error)
            <div class="bg-white rounded-lg shadow-md p-6 text-center">
                <div class="text-gray-400 mb-4">
                    <svg class="w-16 h-16 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 15a4 4 0 004 4h9a5 5 0 10-.1-9.999 5.002 5.002 0 10-9.78 2.096A4.001 4.001 0 003 15z"></path>
                    </svg>
                </div>
                <h3 class="text-lg font-medium text-gray-900 mb-2">Không có dữ liệu thời tiết</h3>
                <p class="text-gray-600">Vui lòng chọn thành phố khác hoặc thử lại sau.</p>
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
    });
    </script>
</div> 