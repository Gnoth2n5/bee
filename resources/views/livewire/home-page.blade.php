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

    <!-- Weather Recipe Section -->
    <livewire:weather-recipe-section />

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
    <section class="py-20 bg-gradient-to-br from-orange-50 via-red-50 to-pink-50 dark:from-orange-900/20 dark:via-red-900/20 dark:to-pink-900/20 relative overflow-hidden">
        <!-- Background Decorations -->
        <div class="absolute inset-0 overflow-hidden pointer-events-none">
            <div class="absolute top-20 left-10 w-32 h-32 bg-gradient-to-r from-orange-200 to-red-200 dark:from-orange-800/30 dark:to-red-800/30 rounded-full blur-3xl opacity-30 animate-pulse"></div>
            <div class="absolute bottom-20 right-20 w-40 h-40 bg-gradient-to-r from-pink-200 to-rose-200 dark:from-pink-800/30 dark:to-rose-800/30 rounded-full blur-3xl opacity-20 animate-bounce" style="animation-delay: 1s"></div>
            <div class="absolute top-1/2 left-1/2 w-24 h-24 bg-gradient-to-r from-yellow-200 to-orange-200 dark:from-yellow-800/30 dark:to-orange-800/30 rounded-full blur-2xl opacity-25 animate-ping" style="animation-delay: 2s"></div>
        </div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center relative z-10">
            <div class="inline-flex items-center px-4 py-2 rounded-full bg-gradient-to-r from-orange-100 to-red-100 dark:from-orange-900/30 dark:to-red-900/30 border border-orange-200 dark:border-orange-800 mb-6">
                <x-lucide-heart class="w-4 h-4 mr-2 text-orange-500 animate-pulse" />
                <span class="text-sm font-semibold text-orange-600 dark:text-orange-400">Tham gia cộng đồng</span>
            </div>

            <h2 class="text-4xl md:text-5xl lg:text-6xl font-black text-gray-800 dark:text-white mb-6">
                Chia Sẻ 
                <span class="bg-gradient-to-r from-orange-500 to-red-600 bg-clip-text text-transparent">
                    Đam Mê
                </span>
                <br>Của Bạn
            </h2>
            
            <p class="text-lg text-gray-600 dark:text-gray-300 mb-12 max-w-3xl mx-auto leading-relaxed">
                Bạn có công thức nấu ăn tuyệt vời? Hãy chia sẻ với cộng đồng BeeFood và cùng nhau khám phá thế giới ẩm thực đầy màu sắc. Mỗi công thức của bạn là một câu chuyện đáng quý!
            </p>
            
            <div class="flex flex-col sm:flex-row gap-6 justify-center items-center">
                @auth
                    <a href="{{ route('filament.user.resources.user-recipes.create') }}" 
                       class="group inline-flex items-center justify-center px-8 py-4 bg-gradient-to-r from-orange-500 to-red-600 hover:from-orange-600 hover:to-red-700 text-white font-bold text-lg rounded-xl shadow-lg hover:shadow-xl transform hover:scale-105 transition-all duration-300">
                        <x-lucide-plus class="w-5 h-5 mr-3 group-hover:rotate-90 transition-transform duration-300" />
                        Tạo công thức mới
                        <div class="ml-3 w-2 h-2 bg-white/30 rounded-full animate-pulse"></div>
                    </a>
                @else
                    <a href="{{ route('register') }}" 
                       class="group inline-flex items-center justify-center px-8 py-4 bg-gradient-to-r from-orange-500 to-red-600 hover:from-orange-600 hover:to-red-700 text-white font-bold text-lg rounded-xl shadow-lg hover:shadow-xl transform hover:scale-105 transition-all duration-300">
                        <x-lucide-user-plus class="w-5 h-5 mr-3 group-hover:scale-110 transition-transform duration-300" />
                        Đăng ký ngay
                        <div class="ml-3 w-2 h-2 bg-white/30 rounded-full animate-pulse"></div>
                    </a>
                @endauth
                
                <a href="{{ route('recipes.index') }}" 
                   class="group inline-flex items-center justify-center px-8 py-4 bg-white/90 dark:bg-slate-800/90 hover:bg-white dark:hover:bg-slate-800 text-orange-600 dark:text-orange-400 font-bold text-lg rounded-xl border-2 border-orange-300 dark:border-orange-700 hover:border-orange-400 dark:hover:border-orange-600 transition-all duration-300 hover:scale-105 shadow-lg hover:shadow-xl backdrop-blur-sm">
                    <x-lucide-compass class="w-5 h-5 mr-3 group-hover:rotate-12 transition-transform duration-300" />
                    Khám phá công thức
                    <x-lucide-arrow-right class="w-5 h-5 ml-3 group-hover:translate-x-1 transition-transform duration-300" />
                </a>
            </div>

            <!-- Stats Section -->
            <div class="mt-16 grid grid-cols-1 md:grid-cols-3 gap-8">
                <div class="bg-white/60 dark:bg-slate-800/60 backdrop-blur-sm rounded-2xl p-6 border border-white/50 dark:border-slate-700/50 shadow-lg">
                    <div class="text-3xl font-black text-orange-600 dark:text-orange-400 mb-2">1K+</div>
                    <div class="text-sm font-semibold text-gray-600 dark:text-gray-400">Công thức được chia sẻ</div>
                </div>
                <div class="bg-white/60 dark:bg-slate-800/60 backdrop-blur-sm rounded-2xl p-6 border border-white/50 dark:border-slate-700/50 shadow-lg">
                    <div class="text-3xl font-black text-red-600 dark:text-red-400 mb-2">500+</div>
                    <div class="text-sm font-semibold text-gray-600 dark:text-gray-400">Thành viên cộng đồng</div>
                </div>
                <div class="bg-white/60 dark:bg-slate-800/60 backdrop-blur-sm rounded-2xl p-6 border border-white/50 dark:border-slate-700/50 shadow-lg">
                    <div class="text-3xl font-black text-pink-600 dark:text-pink-400 mb-2">4.8★</div>
                    <div class="text-sm font-semibold text-gray-600 dark:text-gray-400">Đánh giá trung bình</div>
                </div>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section class="py-20 bg-gradient-to-br from-white via-gray-50 to-blue-50 dark:from-slate-900 dark:via-slate-800 dark:to-slate-900 relative overflow-hidden">
        <!-- Background Decorations -->
        <div class="absolute inset-0 overflow-hidden pointer-events-none">
            <div class="absolute top-20 left-10 w-32 h-32 bg-gradient-to-r from-emerald-200 to-green-200 dark:from-emerald-800/30 dark:to-green-800/30 rounded-full blur-3xl opacity-30 animate-pulse"></div>
            <div class="absolute bottom-20 right-20 w-40 h-40 bg-gradient-to-r from-purple-200 to-indigo-200 dark:from-purple-800/30 dark:to-indigo-800/30 rounded-full blur-3xl opacity-20 animate-bounce" style="animation-delay: 1s"></div>
            <div class="absolute top-1/2 left-1/2 w-24 h-24 bg-gradient-to-r from-cyan-200 to-blue-200 dark:from-cyan-800/30 dark:to-blue-800/30 rounded-full blur-2xl opacity-25 animate-ping" style="animation-delay: 2s"></div>
        </div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
            <div class="text-center mb-16">
                <div class="inline-flex items-center px-4 py-2 rounded-full bg-gradient-to-r from-purple-100 to-indigo-100 dark:from-purple-900/30 dark:to-indigo-900/30 border border-purple-200 dark:border-purple-800 mb-6">
                    <x-lucide-star class="w-4 h-4 mr-2 text-purple-500 animate-spin" style="animation-duration: 3s" />
                    <span class="text-sm font-semibold text-purple-600 dark:text-purple-400">Tính năng vượt trội</span>
                </div>
                
                <h2 class="text-4xl md:text-5xl lg:text-6xl font-black text-gray-800 dark:text-white mb-6">
                    Tại Sao Chọn 
                    <span class="bg-gradient-to-r from-purple-500 to-indigo-600 bg-clip-text text-transparent">
                        BeeFood?
                    </span>
                </h2>
                
                <p class="text-lg text-gray-600 dark:text-gray-300 max-w-3xl mx-auto leading-relaxed">
                    BeeFood là nền tảng chia sẻ công thức nấu ăn hàng đầu với những tính năng độc đáo, được thiết kế để mang đến trải nghiệm tuyệt vời nhất cho người yêu ẩm thực.
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <!-- Feature 1 -->
                <div class="group text-center bg-white/80 dark:bg-slate-800/80 backdrop-blur-sm rounded-2xl p-8 shadow-lg hover:shadow-2xl transition-all duration-500 border border-gray-200/50 dark:border-slate-700/50 hover:-translate-y-2">
                    <div class="w-20 h-20 bg-gradient-to-r from-orange-100 to-red-100 dark:from-orange-900/30 dark:to-red-900/30 rounded-2xl flex items-center justify-center mx-auto mb-6 group-hover:scale-110 transition-transform duration-300 shadow-lg">
                        <x-lucide-book-open class="w-10 h-10 text-orange-500 group-hover:rotate-12 transition-transform duration-300" />
                    </div>
                    <h3 class="text-xl font-bold text-gray-800 dark:text-white mb-4 group-hover:text-orange-600 dark:group-hover:text-orange-400 transition-colors">Công thức đa dạng</h3>
                    <p class="text-gray-600 dark:text-gray-400 leading-relaxed">Hàng nghìn công thức từ món ăn truyền thống đến hiện đại, phù hợp mọi khẩu vị và trình độ nấu ăn</p>
                </div>

                <!-- Feature 2 -->
                <div class="group text-center bg-white/80 dark:bg-slate-800/80 backdrop-blur-sm rounded-2xl p-8 shadow-lg hover:shadow-2xl transition-all duration-500 border border-gray-200/50 dark:border-slate-700/50 hover:-translate-y-2">
                    <div class="w-20 h-20 bg-gradient-to-r from-blue-100 to-cyan-100 dark:from-blue-900/30 dark:to-cyan-900/30 rounded-2xl flex items-center justify-center mx-auto mb-6 group-hover:scale-110 transition-transform duration-300 shadow-lg">
                        <x-lucide-users class="w-10 h-10 text-blue-500 group-hover:rotate-12 transition-transform duration-300" />
                    </div>
                    <h3 class="text-xl font-bold text-gray-800 dark:text-white mb-4 group-hover:text-blue-600 dark:group-hover:text-blue-400 transition-colors">Cộng đồng sôi động</h3>
                    <p class="text-gray-600 dark:text-gray-400 leading-relaxed">Kết nối với những người yêu ẩm thực, chia sẻ kinh nghiệm và học hỏi từ nhau trong môi trường thân thiện</p>
                </div>

                <!-- Feature 3 -->
                <div class="group text-center bg-white/80 dark:bg-slate-800/80 backdrop-blur-sm rounded-2xl p-8 shadow-lg hover:shadow-2xl transition-all duration-500 border border-gray-200/50 dark:border-slate-700/50 hover:-translate-y-2">
                    <div class="w-20 h-20 bg-gradient-to-r from-green-100 to-emerald-100 dark:from-green-900/30 dark:to-emerald-900/30 rounded-2xl flex items-center justify-center mx-auto mb-6 group-hover:scale-110 transition-transform duration-300 shadow-lg">
                        <x-lucide-shield-check class="w-10 h-10 text-green-500 group-hover:rotate-12 transition-transform duration-300" />
                    </div>
                    <h3 class="text-xl font-bold text-gray-800 dark:text-white mb-4 group-hover:text-green-600 dark:group-hover:text-green-400 transition-colors">Chất lượng đảm bảo</h3>
                    <p class="text-gray-600 dark:text-gray-400 leading-relaxed">Tất cả công thức đều được kiểm duyệt kỹ lưỡng bởi đội ngũ chuyên gia để đảm bảo chất lượng cao nhất</p>
                </div>
            </div>

            <!-- Additional Features -->
            <div class="mt-20 grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
                <div class="bg-white/80 dark:bg-slate-800/80 backdrop-blur-sm rounded-2xl p-8 shadow-xl border border-gray-200/50 dark:border-slate-700/50">
                    <div class="space-y-6">
                        <div class="flex items-start">
                            <div class="w-12 h-12 bg-gradient-to-r from-purple-500 to-indigo-600 rounded-xl flex items-center justify-center mr-4 flex-shrink-0">
                                <x-lucide-cloud-sun class="w-6 h-6 text-white" />
                            </div>
                            <div>
                                <h4 class="text-lg font-bold text-gray-800 dark:text-white mb-2">Gợi ý thông minh theo thời tiết</h4>
                                <p class="text-gray-600 dark:text-gray-400">AI tự động đề xuất món ăn phù hợp với thời tiết hiện tại</p>
                            </div>
                        </div>
                        
                        <div class="flex items-start">
                            <div class="w-12 h-12 bg-gradient-to-r from-pink-500 to-rose-600 rounded-xl flex items-center justify-center mr-4 flex-shrink-0">
                                <x-lucide-heart class="w-6 h-6 text-white" />
                            </div>
                            <div>
                                <h4 class="text-lg font-bold text-gray-800 dark:text-white mb-2">Danh sách yêu thích</h4>
                                <p class="text-gray-600 dark:text-gray-400">Lưu và quản lý các công thức yêu thích một cách dễ dàng</p>
                            </div>
                        </div>
                        
                        <div class="flex items-start">
                            <div class="w-12 h-12 bg-gradient-to-r from-yellow-500 to-orange-600 rounded-xl flex items-center justify-center mr-4 flex-shrink-0">
                                <x-lucide-search class="w-6 h-6 text-white" />
                            </div>
                            <div>
                                <h4 class="text-lg font-bold text-gray-800 dark:text-white mb-2">Tìm kiếm nâng cao</h4>
                                <p class="text-gray-600 dark:text-gray-400">Tìm kiếm bằng hình ảnh, nguyên liệu có sẵn hoặc theo sở thích</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="relative">
                    <div class="bg-gradient-to-br from-orange-400 to-red-500 rounded-2xl p-8 text-white shadow-2xl">
                        <h3 class="text-2xl font-bold mb-4">🎉 Tham gia ngay hôm nay!</h3>
                        <p class="text-orange-100 mb-6 leading-relaxed">
                            Khám phá thế giới ẩm thực đầy màu sắc với hàng nghìn công thức từ cộng đồng BeeFood
                        </p>
                        <div class="space-y-3">
                            <div class="flex items-center">
                                <x-lucide-check class="w-5 h-5 mr-3" />
                                <span>Miễn phí hoàn toàn</span>
                            </div>
                            <div class="flex items-center">
                                <x-lucide-check class="w-5 h-5 mr-3" />
                                <span>Không quảng cáo phiền phức</span>
                            </div>
                            <div class="flex items-center">
                                <x-lucide-check class="w-5 h-5 mr-3" />
                                <span>Cập nhật liên tục</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div> 