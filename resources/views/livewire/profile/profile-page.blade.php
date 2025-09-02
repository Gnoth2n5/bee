<div>
    <div class="min-h-screen bg-gradient-to-br from-gray-50 via-white to-orange-50 dark:from-slate-900 dark:via-slate-800 dark:to-slate-900 relative overflow-hidden">
        <!-- Background Decorations -->
        <div class="absolute inset-0 overflow-hidden pointer-events-none">
            <div class="absolute top-20 left-10 w-32 h-32 bg-gradient-to-r from-orange-200 to-red-200 dark:from-orange-800/30 dark:to-red-800/30 rounded-full blur-3xl opacity-30 animate-pulse"></div>
            <div class="absolute bottom-20 right-20 w-40 h-40 bg-gradient-to-r from-pink-200 to-rose-200 dark:from-pink-800/30 dark:to-rose-800/30 rounded-full blur-3xl opacity-20 animate-bounce" style="animation-delay: 1s"></div>
            <div class="absolute top-1/2 left-1/2 w-24 h-24 bg-gradient-to-r from-yellow-200 to-orange-200 dark:from-yellow-800/30 dark:to-orange-800/30 rounded-full blur-2xl opacity-25 animate-ping" style="animation-delay: 2s"></div>
        </div>
        
        <!-- Profile Info Section -->
        <div class="relative px-4 sm:px-6 lg:px-8 pt-8 z-10">
            <div class="max-w-7xl mx-auto">
                <div class="bg-white/80 dark:bg-slate-800/80 backdrop-blur-sm rounded-xl shadow-xl border border-orange-200/50 dark:border-orange-800/50 overflow-hidden">
                    <!-- Header Component -->
                    <x-profile.header 
                        :user="$user" 
                        :profile="$profile" 
                        :isEditing="$isEditing" 
                        :avatar="$avatar" 
                        :experienceOptions="$experienceOptions"
                        :nearestCity="$nearestCity"
                    />

                    <!-- Stats Component -->
                    <x-profile.stats 
                        :recipesCount="$recipesCount" 
                        :collectionsCount="$collectionsCount" 
                        :favoritesCount="$favoritesCount" 
                    />

                    <!-- Tabs Component -->
                    <x-profile.tabs 
                        :activeTab="$activeTab" 
                        :isEditing="$isEditing" 
                    />

                    <!-- Tab Content -->
                    <div class="p-6">
                        @if($activeTab === 'recipes')
                            <x-profile.recipes-tab :recipes="$this->recipes" />
                        @endif

                        @if($activeTab === 'collections')
                            <x-profile.collections-tab 
                                :collections="$this->collections"
                                :showCreateModal="$showCreateModal"
                                :newName="$newName"
                                :newDescription="$newDescription"
                                :newIsPublic="$newIsPublic"
                                :newCoverImage="$newCoverImage"
                                :newCoverImagePreview="$newCoverImagePreview"
                            />
                        @endif

                        @if($activeTab === 'favorites')
                            <x-profile.favorites-tab :favorites="$this->favorites" />
                        @endif

                        @if($activeTab === 'settings')
                            @if($isEditing)
                                <x-profile.settings-tab 
                                    :name="$name"
                                    :email="$email"
                                    :province="$province"
                                    :bio="$bio"
                                    :phone="$phone"
                                    :address="$address"
                                    :city="$city"
                                    :country="$country"
                                    :cooking_experience="$cooking_experience"
                                    :dietary_preferences="$dietary_preferences"
                                    :allergies="$allergies"
                                    :health_conditions="$health_conditions"
                                    :experienceOptions="$experienceOptions"
                                    :dietaryOptions="$dietaryOptions"
                                />
                            @else
                                <div class="text-center py-12">
                                    <div class="mx-auto h-12 w-12 text-gray-400">
                                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                        </svg>
                                    </div>
                                    <h3 class="mt-2 text-sm font-medium text-gray-900">Cài đặt hồ sơ</h3>
                                    <p class="mt-1 text-sm text-gray-500">Nhấn "Chỉnh sửa hồ sơ" để cập nhật thông tin của bạn.</p>
                                </div>
                            @endif
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Flash Messages -->
        <x-flash-message />
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

    <script>
    document.addEventListener('livewire:init', () => {
        // Kiểm tra localStorage khi component được load
        const savedLocation = LocationManager.getLocation();
        if (savedLocation) {
            console.log('Using saved location from localStorage:', savedLocation);
            @this.setUserLocation(savedLocation.latitude, savedLocation.longitude);
        }
        
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
            LocationManager.getCurrentLocation()
                .then((result) => {
                    console.log('Location obtained:', result.latitude, result.longitude);
                    // Gửi tọa độ về Livewire component
                    @this.setUserLocation(result.latitude, result.longitude);
                })
                .catch((error) => {
                    console.error('Geolocation error:', error);
                    
                    // Khi người dùng từ chối vị trí, tự động chọn ngẫu nhiên
                    if (error.code === 1) { // PERMISSION_DENIED
                        console.log('Người dùng từ chối vị trí, chọn ngẫu nhiên...');
                        @this.randomCity();
                    } else {
                        alert('Không thể lấy vị trí của bạn. Vui lòng kiểm tra quyền truy cập vị trí.');
                        @this.randomCity();
                    }
                });
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
            LocationManager.getCurrentLocation()
                .then((result) => {
                    console.log('Location obtained:', result.latitude, result.longitude);
                    // Gửi tọa độ về Livewire component
                    @this.setUserLocation(result.latitude, result.longitude);
                })
                .catch((error) => {
                    console.error('Geolocation error:', error);
                    
                    // Khi người dùng từ chối vị trí, tự động chọn ngẫu nhiên
                    if (error.code === 1) { // PERMISSION_DENIED
                        console.log('Người dùng từ chối vị trí, chọn ngẫu nhiên...');
                        @this.randomCity();
                    } else {
                        alert('Không thể lấy vị trí của bạn. Vui lòng kiểm tra quyền truy cập vị trí.');
                    }
                });
        });
    });
    </script>
</div> 