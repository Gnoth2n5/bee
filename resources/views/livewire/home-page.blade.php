<div>
    <!-- Hero Section -->

    <x-hero-section :stats="$stats" :featured-recipe="$featuredRecipe"></x-hero-section>

    <!-- VIP Banner - Hidden -->
    {{-- @auth
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
    @endauth --}}

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