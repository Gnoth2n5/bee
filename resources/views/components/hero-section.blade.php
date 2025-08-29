@props(['stats', 'featuredRecipe' => null])

<section id="hero-section" class="relative h-screen overflow-hidden bg-gradient-to-br from-slate-900 via-slate-800 to-slate-900">
    <!-- Background Slider -->
    <div class="absolute inset-0">
        <div class="hero-background active" data-slide="0">
            <div class="absolute inset-0 bg-cover bg-center bg-no-repeat" style="background-image: url('/images/hero-section-1.jpg')"></div>
            <div class="absolute inset-0 bg-gradient-to-r from-black/70 via-black/50 to-black/30"></div>
            <div class="absolute inset-0 bg-gradient-to-br from-red-500 to-pink-600 opacity-20"></div>
        </div>
        <div class="hero-background" data-slide="1">
            <div class="absolute inset-0 bg-cover bg-center bg-no-repeat" style="background-image: url('/images/hero-section-2.jpg')"></div>
            <div class="absolute inset-0 bg-gradient-to-r from-black/70 via-black/50 to-black/30"></div>
            <div class="absolute inset-0 bg-gradient-to-br from-orange-500 to-red-600 opacity-20"></div>
        </div>
        <div class="hero-background" data-slide="2">
            <div class="absolute inset-0 bg-cover bg-center bg-no-repeat" style="background-image: url('/images/hero-section-3.webp')"></div>
            <div class="absolute inset-0 bg-gradient-to-r from-black/70 via-black/50 to-black/30"></div>
            <div class="absolute inset-0 bg-gradient-to-br from-yellow-500 to-orange-600 opacity-20"></div>
        </div>
    </div>

    <!-- Animated Background Elements -->
    <div class="absolute inset-0 overflow-hidden pointer-events-none">
        <div class="absolute top-20 left-10 w-20 h-20 bg-white/5 rounded-full animate-pulse"></div>
        <div class="absolute top-40 right-20 w-32 h-32 bg-white/3 rounded-full animate-bounce" style="animation-delay: 1s"></div>
        <div class="absolute bottom-32 left-1/4 w-16 h-16 bg-white/4 rounded-full animate-ping" style="animation-delay: 2s"></div>
        <div class="absolute bottom-20 right-1/3 w-24 h-24 bg-white/3 rounded-full animate-pulse" style="animation-delay: 3s"></div>
    </div>

    <!-- Main Content -->
    <div class="relative z-10 h-full flex items-center">
        <div class="container mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 lg:grid-cols-12 md:gap-8 gap-10 items-center min-h-[80vh]">
                <!-- Text Content -->
                <div class="lg:col-span-7 text-white mb-10 lg:mb-0 relative bottom-[30px] z-20">
                    <div id="hero-badge" class="inline-flex items-center px-4 py-2 rounded-full bg-white bg-opacity-10 backdrop-filter backdrop-blur-sm border border-white border-opacity-20 mb-6 hero-animate">
                        <x-lucide-chef-hat class="w-4 h-4 mr-2 animate-spin" style="animation-duration: 3s" />
                        <span class="text-sm font-semibold">Chào mừng đến với BeeFood</span>
                    </div>

                    <div class="mb-4 overflow-hidden relative z-30">
                        <h1 id="hero-title" class="text-4xl sm:text-4xl lg:text-6xl font-black leading-tight hero-animate drop-shadow-lg">
                            <span id="hero-title-line1" class="block drop-shadow-md">Khám phá thế giới</span>
                            <span id="hero-title-line2" class="block bg-gradient-to-r from-red-500 to-pink-600 bg-clip-text text-transparent animate-pulse drop-shadow-md ">Ẩm thực tuyệt vời</span>
                        </h1>
                    </div>

                    <div class="mb-6 overflow-hidden relative z-20">
                        <p id="hero-description" class="text-lg sm:text-xl text-gray-200 max-w-2xl leading-relaxed hero-animate drop-shadow-md">
                            Hàng nghìn công thức nấu ăn ngon từ khắp thế giới, được chia sẻ bởi những người yêu thích ẩm thực như bạn.
                        </p>
                    </div>

                    <!-- Featured Search -->
                    <div class="mb-8">
                        <div class="w-full max-w-2xl">
                            <livewire:quick-search />
                        </div>
                    </div>

                    <div id="hero-stats" class="flex flex-wrap gap-6 md:mb-4 mb-6 hero-animate">
                        <div class="flex items-center gap-2 bg-white bg-opacity-10 backdrop-filter backdrop-blur-sm rounded-full px-4 py-2 border border-white border-opacity-20">
                            <div id="hero-stat-dot" class="w-3 h-3 rounded-full bg-gradient-to-r from-red-500 to-pink-600 animate-pulse"></div>
                            <span id="hero-stat-recipes" class="text-sm font-semibold">{{ $stats['recipes'] ?? '10K' }}+ Công thức</span>
                        </div>
                        <div class="flex items-center gap-2 bg-white bg-opacity-10 backdrop-filter backdrop-blur-sm rounded-full px-4 py-2 border border-white border-opacity-20">
                            <x-lucide-users class="w-4 h-4" />
                            <span id="hero-stat-community" class="text-sm font-semibold">{{ $stats['users'] ?? '2K' }}+ Cộng đồng</span>
                        </div>
                    </div>

                    <div id="hero-buttons" class="flex flex-col sm:flex-row gap-4 hero-animate">
                        <a id="hero-primary-btn" href="#categories" class="group inline-flex items-center justify-center px-6 py-3 bg-gradient-to-r from-red-500 to-pink-600 text-white font-bold text-lg rounded-full hover:shadow-2xl transform hover:scale-105 transition-all duration-300 hover:-translate-y-1">
                            <span id="hero-btn-text">Bắt đầu nấu ăn</span>
                            <x-lucide-play class="w-5 h-5 ml-2 group-hover:translate-x-1 transition-transform duration-300" />
                        </a>
                        <a href="/register" class="inline-flex items-center justify-center px-7 py-3 bg-white bg-opacity-10 backdrop-filter backdrop-blur-sm text-white font-bold text-lg rounded-full border-2 border-white border-opacity-30 hover:bg-white hover:text-gray-900 transform hover:scale-105 transition-all duration-300 hover:-translate-y-1">
                            Tham gia cộng đồng
                            <x-lucide-users class="w-5 h-5 ml-2" />
                        </a>
                    </div>
                </div>

                <!-- Desktop Featured Card - visible on large screens -->
                <div class="hidden lg:block lg:col-span-5 lg:pl-8">
                    @if($featuredRecipe)
                    <a href="{{ route('recipes.show', $featuredRecipe) }}" class="block bg-white bg-opacity-10 backdrop-filter backdrop-blur-lg rounded-2xl p-6 border border-white border-opacity-20 hover:bg-white hover:bg-opacity-15 transition-all duration-300 group max-w-md mx-auto lg:mx-0">
                        <div class="flex items-center gap-4 mb-4">
                            @if($featuredRecipe->featured_image)
                                <div class="w-12 h-12 rounded-full overflow-hidden group-hover:scale-110 transition-transform duration-300">
                                    <img src="{{ Storage::url($featuredRecipe->featured_image) }}" 
                                         alt="{{ $featuredRecipe->title }}" 
                                         class="w-full h-full object-cover">
                                </div>
                            @else
                                <div id="featured-icon" class="w-12 h-12 rounded-full bg-gradient-to-r from-red-500 to-pink-600 flex items-center justify-center group-hover:scale-110 transition-transform duration-300">
                                    <x-lucide-chef-hat class="w-6 h-6 text-white" />
                                </div>
                            @endif
                            <div class="flex-1 min-w-0">
                                <h3 class="text-white font-bold text-lg truncate">{{ $featuredRecipe->title }}</h3>
                                <div class="flex items-center gap-2 text-gray-300 text-sm">
                                    <x-lucide-eye class="w-3 h-3" />
                                    <span>{{ number_format($featuredRecipe->view_count ?? 0) }} lượt xem</span>
                                </div>
                            </div>
                        </div>
                        @if($featuredRecipe->average_rating > 0)
                        <div class="flex items-center gap-2 text-yellow-400">
                            @for($i = 1; $i <= 5; $i++)
                                @if($i <= floor($featuredRecipe->average_rating))
                                    <x-lucide-star class="w-4 h-4 fill-current" />
                                @elseif($i - 0.5 <= $featuredRecipe->average_rating)
                                    <x-lucide-star class="w-4 h-4 fill-current opacity-50" />
                                @else
                                    <x-lucide-star class="w-4 h-4" />
                                @endif
                            @endfor
                            <span class="text-white ml-2 text-sm">{{ number_format($featuredRecipe->average_rating, 1) }} ({{ $featuredRecipe->ratings_count ?? 0 }} đánh giá)</span>
                        </div>
                        @else
                        <div class="flex items-center gap-2 text-gray-400">
                            <x-lucide-star class="w-4 h-4" />
                            <span class="text-sm">Chưa có đánh giá</span>
                        </div>
                        @endif
                    </a>
                    @else
                    <div class="bg-white bg-opacity-10 backdrop-filter backdrop-blur-lg rounded-2xl p-6 border border-white border-opacity-20 max-w-md mx-auto lg:mx-0">
                        <div class="flex items-center gap-4 mb-4">
                            <div class="w-12 h-12 rounded-full bg-gradient-to-r from-gray-400 to-gray-500 flex items-center justify-center">
                                <x-lucide-chef-hat class="w-6 h-6 text-white" />
                            </div>
                            <div>
                                <h3 class="text-white font-bold text-lg">Chưa có công thức</h3>
                                <p class="text-gray-300 text-sm">Hãy thêm công thức mới</p>
                            </div>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Mobile Featured Card - visible below hero content -->
    <div class="block lg:hidden mt-8 px-4">
        @if($featuredRecipe)
        <a href="{{ route('recipes.show', $featuredRecipe) }}" class="block bg-white bg-opacity-10 backdrop-filter backdrop-blur-lg rounded-2xl p-6 border border-white border-opacity-20 hover:bg-white hover:bg-opacity-15 transition-all duration-300 group max-w-md mx-auto lg:mx-0">
            <div class="flex items-center gap-4 mb-4">
                @if($featuredRecipe->featured_image)
                    <div class="w-12 h-12 rounded-full overflow-hidden group-hover:scale-110 transition-transform duration-300">
                        <img src="{{ Storage::url($featuredRecipe->featured_image) }}" 
                             alt="{{ $featuredRecipe->title }}" 
                             class="w-full h-full object-cover">
                    </div>
                @else
                    <div id="featured-icon-mobile" class="w-12 h-12 rounded-full bg-gradient-to-r from-red-500 to-pink-600 flex items-center justify-center group-hover:scale-110 transition-transform duration-300">
                        <x-lucide-chef-hat class="w-6 h-6 text-white" />
                    </div>
                @endif
                <div class="flex-1 min-w-0">
                    <h3 class="text-white font-bold text-lg truncate">{{ $featuredRecipe->title }}</h3>
                    <div class="flex items-center gap-2 text-gray-300 text-sm">
                        <x-lucide-eye class="w-3 h-3" />
                        <span>{{ number_format($featuredRecipe->view_count ?? 0) }} lượt xem</span>
                    </div>
                </div>
            </div>
            @if($featuredRecipe->average_rating > 0)
            <div class="flex items-center gap-2 text-yellow-400">
                @for($i = 1; $i <= 5; $i++)
                    @if($i <= floor($featuredRecipe->average_rating))
                        <x-lucide-star class="w-4 h-4 fill-current" />
                    @elseif($i - 0.5 <= $featuredRecipe->average_rating)
                        <x-lucide-star class="w-4 h-4 fill-current opacity-50" />
                    @else
                        <x-lucide-star class="w-4 h-4" />
                    @endif
                @endfor
                <span class="text-white ml-2 text-sm">{{ number_format($featuredRecipe->average_rating, 1) }} ({{ $featuredRecipe->ratings_count ?? 0 }} đánh giá)</span>
            </div>
            @else
            <div class="flex items-center gap-2 text-gray-400">
                <x-lucide-star class="w-4 h-4" />
                <span class="text-sm">Chưa có đánh giá</span>
            </div>
            @endif
        </a>
        @endif
    </div>

    <!-- Desktop Bottom Navigation (visible lg+) -->
    <div class="hidden lg:flex absolute bottom-12 left-1/2 transform -translate-x-1/2 z-50 items-center gap-4 bg-white bg-opacity-10 backdrop-filter backdrop-blur-lg rounded-full p-2 border border-white border-opacity-20 shadow-lg">
        <!-- Previous Button -->
        <button id="hero-prev-btn" class="w-12 h-12 rounded-full bg-white bg-opacity-20 hover:bg-white hover:bg-opacity-30 flex items-center justify-center transition-all duration-300 disabled:opacity-50 group cursor-pointer">
            <x-lucide-chevron-left class="w-6 h-6 text-white group-hover:-translate-x-1 transition-transform duration-300" />
        </button>

        <!-- Slide Indicators -->
        <div class="flex gap-2">
            <button class="hero-dot active" data-slide="0"></button>
            <button class="hero-dot" data-slide="1"></button>
            <button class="hero-dot" data-slide="2"></button>
        </div>

        <!-- Next Button -->
        <button id="hero-next-btn" class="w-12 h-12 rounded-full bg-white bg-opacity-20 hover:bg-white hover:bg-opacity-30 flex items-center justify-center transition-all duration-300 disabled:opacity-50 group cursor-pointer">
            <x-lucide-chevron-right class="w-6 h-6 text-white group-hover:translate-x-1 transition-transform duration-300" />
        </button>
    </div>

    <!-- Mobile Side Navigation Buttons (visible below lg) -->
    <div class="lg:hidden">
        <!-- Prev Button Left Side -->
        <button id="hero-mobile-prev" class="fixed top-1/2 left-4 transform -translate-y-1/2 z-50 bg-black bg-opacity-30 hover:bg-black hover:bg-opacity-50 text-white rounded-full p-3 transition cursor-pointer shadow-lg">
            <x-lucide-chevron-left class="w-6 h-6" />
        </button>

        <!-- Next Button Right Side -->
        <button id="hero-mobile-next" class="fixed top-1/2 right-4 transform -translate-y-1/2 z-50 bg-black bg-opacity-30 hover:bg-black hover:bg-opacity-50 text-white rounded-full p-3 transition cursor-pointer shadow-lg">
            <x-lucide-chevron-right class="w-6 h-6" />
        </button>
    </div>
</section> 