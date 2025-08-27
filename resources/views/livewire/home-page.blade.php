<div>
    <!-- Hero Section với thiết kế mới -->
    <section class="relative min-h-screen flex items-center justify-center overflow-hidden">
        <!-- Background với gradient và pattern -->
        <div class="absolute inset-0 bg-gradient-to-br from-orange-50 via-yellow-50 to-red-50 dark:from-[#1D0002] dark:via-[#391800] dark:to-[#733000]"></div>
        
        <!-- Decorative elements -->
        <div class="absolute inset-0 opacity-10">
            <div class="absolute top-20 left-20 w-32 h-32 bg-orange-200 dark:bg-orange-800 rounded-full blur-3xl"></div>
            <div class="absolute bottom-20 right-20 w-40 h-40 bg-red-200 dark:bg-red-800 rounded-full blur-3xl"></div>
            <div class="absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 w-60 h-60 bg-yellow-200 dark:bg-yellow-800 rounded-full blur-3xl"></div>
        </div>

        <div class="relative z-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-20">
            <div class="text-center">
                <!-- Logo và Brand -->
                <div class="mb-8">
                    <div class="flex items-center justify-center space-x-4 mb-6">
                        <div class="w-16 h-16 bg-orange-500 rounded-2xl flex items-center justify-center shadow-2xl">
                            <svg class="w-10 h-10 text-white" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M12 2L2 7l10 5 10-5-10-5zM2 17l10 5 10-5M2 12l10 5 10-5"/>
                            </svg>
                        </div>
                        <h1 class="text-6xl md:text-7xl font-bold bg-gradient-to-r from-orange-600 to-red-600 bg-clip-text text-transparent">
                            BeeFood
                        </h1>
                    </div>
                    <p class="text-2xl md:text-3xl text-gray-700 dark:text-gray-300 font-light mb-4">
                        Khám phá thế giới ẩm thực
                    </p>
                    <p class="text-lg text-gray-600 dark:text-gray-400 max-w-2xl mx-auto leading-relaxed">
                        Chia sẻ và khám phá những công thức nấu ăn ngon nhất từ cộng đồng BeeFood. 
                        Từ món ăn truyền thống đến hiện đại, tất cả đều có tại đây.
                    </p>
                </div>

                <!-- Search Bar -->
                <div class="max-w-2xl mx-auto mb-12">
                    <livewire:quick-search />
                </div>

                <!-- Stats Cards -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-8 max-w-4xl mx-auto mb-16">
                    <div class="group">
                        <div class="bg-white/80 dark:bg-[#161615]/80 backdrop-blur-sm rounded-2xl p-8 border border-white/20 dark:border-[#3E3E3A] shadow-xl hover:shadow-2xl transition-all duration-500 hover:scale-105">
                            <div class="text-center">
                                <div class="w-16 h-16 bg-orange-100 dark:bg-orange-900/30 rounded-full flex items-center justify-center mx-auto mb-4 group-hover:bg-orange-200 dark:group-hover:bg-orange-900/50 transition-colors">
                                    <svg class="w-8 h-8 text-orange-600 dark:text-orange-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                                    </svg>
                                </div>
                                <div class="text-4xl font-bold text-gray-900 dark:text-white mb-2 group-hover:text-orange-600 dark:group-hover:text-orange-400 transition-colors">
                                    {{ $stats['recipes'] ?? 0 }}+
                                </div>
                                <div class="text-gray-600 dark:text-gray-400 font-medium">Công thức</div>
                            </div>
                        </div>
                    </div>

                    <div class="group">
                        <div class="bg-white/80 dark:bg-[#161615]/80 backdrop-blur-sm rounded-2xl p-8 border border-white/20 dark:border-[#3E3E3A] shadow-xl hover:shadow-2xl transition-all duration-500 hover:scale-105">
                            <div class="text-center">
                                <div class="w-16 h-16 bg-red-100 dark:bg-red-900/30 rounded-full flex items-center justify-center mx-auto mb-4 group-hover:bg-red-200 dark:group-hover:bg-red-900/50 transition-colors">
                                    <svg class="w-8 h-8 text-red-600 dark:text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                                    </svg>
                                </div>
                                <div class="text-4xl font-bold text-gray-900 dark:text-white mb-2 group-hover:text-red-600 dark:group-hover:text-red-400 transition-colors">
                                    {{ $stats['users'] ?? 0 }}+
                                </div>
                                <div class="text-gray-600 dark:text-gray-400 font-medium">Thành viên</div>
                            </div>
                        </div>
                    </div>

                    <div class="group">
                        <div class="bg-white/80 dark:bg-[#161615]/80 backdrop-blur-sm rounded-2xl p-8 border border-white/20 dark:border-[#3E3E3A] shadow-xl hover:shadow-2xl transition-all duration-500 hover:scale-105">
                            <div class="text-center">
                                <div class="w-16 h-16 bg-yellow-100 dark:bg-yellow-900/30 rounded-full flex items-center justify-center mx-auto mb-4 group-hover:bg-yellow-200 dark:group-hover:bg-yellow-900/50 transition-colors">
                                    <svg class="w-8 h-8 text-yellow-600 dark:text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
                                    </svg>
                                </div>
                                <div class="text-4xl font-bold text-gray-900 dark:text-white mb-2 group-hover:text-yellow-600 dark:group-hover:text-yellow-400 transition-colors">
                                    {{ $stats['categories'] ?? 0 }}+
                                </div>
                                <div class="text-gray-600 dark:text-gray-400 font-medium">Danh mục</div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- CTA Buttons -->
                <div class="flex flex-col sm:flex-row gap-4 justify-center">
                    @auth
                        <a href="{{ route('recipes.index') }}" 
                           class="inline-flex items-center px-8 py-4 bg-orange-600 hover:bg-orange-700 text-white font-semibold rounded-xl shadow-lg hover:shadow-xl transition-all duration-300 transform hover:scale-105">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                            </svg>
                            Khám phá công thức
                        </a>
                    @else
                        <a href="{{ route('register') }}" 
                           class="inline-flex items-center px-8 py-4 bg-orange-600 hover:bg-orange-700 text-white font-semibold rounded-xl shadow-lg hover:shadow-xl transition-all duration-300 transform hover:scale-105">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/>
                            </svg>
                            Bắt đầu ngay
                        </a>
                    @endauth
                    <a href="{{ route('recipes.index') }}" 
                       class="inline-flex items-center px-8 py-4 border-2 border-orange-600 text-orange-600 hover:bg-orange-600 hover:text-white font-semibold rounded-xl transition-all duration-300 transform hover:scale-105">
                        Tìm hiểu thêm
                        <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                        </svg>
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- VIP Banner -->
    @auth
        @unless(auth()->user()->isVip())
            <section class="bg-gradient-to-r from-yellow-400 to-orange-500 py-8">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                    <div class="flex flex-col md:flex-row items-center justify-between">
                        <div class="text-center md:text-left mb-4 md:mb-0">
                            <h3 class="text-xl font-bold text-white mb-2">
                                🚀 Nâng cấp lên VIP ngay hôm nay!
                            </h3>
                            <p class="text-white/90">
                                Tận hưởng tính năng tìm món ăn theo bản đồ nâng cao và nhiều ưu đãi đặc biệt
                            </p>
                        </div>
                        <div class="flex space-x-3">
                            <a href="{{ route('subscriptions.packages') }}" 
                               class="inline-flex items-center px-5 py-1.5 dark:text-[#EDEDEC] border-[#19140035] hover:border-[#1915014a] border text-[#1b1b18] dark:border-[#3E3E3A] dark:hover:border-[#62605b] rounded-sm text-sm leading-normal">
                                Nâng cấp VIP
                            </a>
                            <button onclick="this.parentElement.parentElement.parentElement.style.display='none'" 
                                    class="px-4 py-3 text-white/80 hover:text-white transition-colors">
                                ✕
                            </button>
                        </div>
                    </div>
                </div>
            </section>
        @endunless
    @endauth

    <!-- Weather Recipe Slideshow -->
    <livewire:weather-slideshow-simple />

    <!-- Featured Categories -->
    <x-featured-categories :categories="$categories" />

    <!-- Recipe Grid -->
    <x-recipe-grid 
        :recipes="$recipes" 
        :view-mode="$viewMode"
        :has-active-filters="$this->hasActiveFilters"
        :difficulty="$difficulty"
        :cooking-time="$cookingTime"
        title="Công thức mới nhất"
        subtitle="Khám phá những món ăn ngon nhất từ cộng đồng BeeFood"
    />

    <!-- Post Section -->
    <livewire:posts.post-section />

    <!-- Call to Action Section -->
    <section class="py-16 bg-orange-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <h2 class="text-3xl font-bold text-gray-900 mb-4">Chia sẻ công thức của bạn</h2>
            <p class="text-lg text-gray-600 mb-8 max-w-2xl mx-auto">
                Bạn có công thức nấu ăn ngon? Hãy chia sẻ với cộng đồng BeeFood và nhận được phản hồi từ những người yêu ẩm thực khác.
            </p>
            <div class="flex flex-col sm:flex-row gap-4 justify-center">
                @auth
                    <a href="{{ route('filament.user.resources.user-recipes.create') }}" 
                       class="inline-flex items-center px-6 py-3 border border-transparent text-base font-medium rounded-md text-white bg-orange-600 hover:bg-orange-700 transition-colors">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                        </svg>
                        Tạo công thức mới
                    </a>
                @else
                    <a href="{{ route('register') }}" 
                       class="inline-flex items-center px-6 py-3 border border-transparent text-base font-medium rounded-md text-white bg-orange-600 hover:bg-orange-700 transition-colors">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" />
                        </svg>
                        Đăng ký ngay
                    </a>
                @endauth
                <a href="{{ route('recipes.index') }}" 
                   class="inline-flex items-center px-6 py-3 border border-orange-600 text-base font-medium rounded-md text-orange-600 bg-white hover:bg-orange-50 transition-colors">
                    Xem tất cả công thức
                    <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                    </svg>
                </a>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section class="py-16 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-12">
                <h2 class="text-3xl font-bold text-gray-900 mb-4">Tại sao chọn BeeFood?</h2>
                <p class="text-lg text-gray-600 max-w-3xl mx-auto">
                    BeeFood là nền tảng chia sẻ công thức nấu ăn hàng đầu với những tính năng độc đáo
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <!-- Feature 1 -->
                <div class="text-center">
                    <div class="w-16 h-16 bg-orange-50 rounded-full flex items-center justify-center mx-auto mb-4">
                        <svg class="w-8 h-8 text-orange-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 19.5A2.5 2.5 0 0 0 6.5 22h11a2.5 2.5 0 0 0 2.5-2.5v-13A2.5 2.5 0 0 0 17.5 4h-11A2.5 2.5 0 0 0 4 6.5v13z" /><path stroke-linecap="round" stroke-linejoin="round" d="M8 2v4m8-4v4" /></svg>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-900 mb-2">Công thức đa dạng</h3>
                    <p class="text-gray-600">Hàng nghìn công thức từ món ăn truyền thống đến hiện đại, phù hợp mọi khẩu vị</p>
                </div>

                <!-- Feature 2 -->
                <div class="text-center">
                    <div class="w-16 h-16 bg-orange-50 rounded-full flex items-center justify-center mx-auto mb-4">
                        <svg class="w-8 h-8 text-orange-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 0 0-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 0 1 5.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 0 1 9.288 0M15 7a3 3 0 1 1-6 0 3 3 0 0 1 6 0zm6 3a2 2 0 1 1-4 0 2 2 0 0 1 4 0zM7 10a2 2 0 1 1-4 0 2 2 0 0 1 4 0z" /></svg>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-900 mb-2">Cộng đồng sôi động</h3>
                    <p class="text-gray-600">Kết nối với những người yêu ẩm thực, chia sẻ kinh nghiệm và học hỏi lẫn nhau</p>
                </div>

                <!-- Feature 3 -->
                <div class="text-center">
                    <div class="w-16 h-16 bg-orange-50 rounded-full flex items-center justify-center mx-auto mb-4">
                        <svg class="w-8 h-8 text-orange-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 1 1-18 0 9 9 0 0 1 18 0z" /></svg>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-900 mb-2">Chất lượng đảm bảo</h3>
                    <p class="text-gray-600">Tất cả công thức đều được kiểm duyệt kỹ lưỡng để đảm bảo chất lượng và độ chính xác</p>
                </div>
            </div>
        </div>
    </section>
</div> 