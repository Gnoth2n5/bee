@php
$gradients = [
    'from-green-500 to-emerald-600',
    'from-red-500 to-rose-600', 
    'from-pink-500 to-purple-600',
    'from-blue-500 to-cyan-600',
    'from-orange-500 to-amber-600',
    'from-indigo-500 to-purple-600',
    'from-teal-500 to-green-600',
    'from-rose-500 to-pink-600'
];
@endphp

<section id="categories" class="py-20 bg-gradient-to-br from-gray-50 via-white to-gray-100 dark:from-slate-900 dark:via-slate-800 dark:to-slate-900 relative overflow-hidden">
    <!-- Background Decorations -->
    <div class="absolute inset-0 overflow-hidden pointer-events-none">
        <div class="absolute top-20 left-10 w-32 h-32 bg-gradient-to-r from-red-200 to-pink-200 rounded-full blur-3xl opacity-30 animate-pulse"></div>
        <div class="absolute bottom-20 right-20 w-40 h-40 bg-gradient-to-r from-blue-200 to-cyan-200 rounded-full blur-3xl opacity-20 animate-bounce" style="animation-delay: 1s"></div>
        <div class="absolute top-1/2 left-1/2 w-24 h-24 bg-gradient-to-r from-green-200 to-emerald-200 rounded-full blur-2xl opacity-25 animate-ping" style="animation-delay: 2s"></div>
    </div>

    <div class="container mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
        <!-- Header -->
        <div class="text-center mb-16">
            <div class="inline-flex items-center px-4 py-2 rounded-full bg-gradient-to-r from-red-100 to-pink-100 dark:from-red-900/30 dark:to-pink-900/30 border border-red-200 dark:border-red-800 mb-6">
                <x-lucide-sparkles class="w-4 h-4 mr-2 text-red-500 animate-spin" style="animation-duration: 3s" />
                <span class="text-sm font-semibold text-red-600 dark:text-red-400">Khám phá danh mục</span>
            </div>
            
            <h2 class="text-4xl md:text-5xl lg:text-6xl font-black text-gray-800 dark:text-white mb-6 animate-fade-in-up">
                Tìm kiếm theo 
                <span class="bg-gradient-to-r from-red-500 to-pink-600 bg-clip-text text-transparent animate-fade-in-up">
                    Danh mục
                </span>
            </h2>
            
            <p class="text-lg text-gray-600 dark:text-gray-300 max-w-3xl mx-auto leading-relaxed animate-fade-in-up">
                Khám phá món ăn yêu thích tiếp theo của bạn từ các danh mục được chia sẻ bởi mọi người, mỗi danh mục chứa đựng những công thức nấu ăn đích thực từ khắp nơi trên thế giới.
            </p>
        </div>

        <!-- Categories Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8 animate-fade-in-up">
            @foreach($categories->take(4) as $index => $category)
            @php
                $gradient = $gradients[$index % count($gradients)];
            @endphp
            <div class="group relative overflow-hidden rounded-2xl bg-white dark:bg-slate-800 shadow-lg hover:shadow-2xl transform hover:-translate-y-2 transition-all duration-700 ease-out"
                 style="animation-delay: {{ $index * 150 }}ms">
                
                <!-- Background Image with Overlay -->
                <div class="relative h-48 overflow-hidden">
                    @if($category->image)
                        <img src="{{ asset('storage/' . $category->image) }}" 
                             alt="{{ $category->name }}" 
                             class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-110">
                    @else
                        <div class="w-full h-full bg-gradient-to-br {{ $gradient }} flex items-center justify-center">
                            @if($category->icon)
                                <i class="{{ $category->icon }} text-white text-6xl"></i>
                            @else
                                <svg class="w-16 h-16 text-white" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M8 2v4l-3 12h14L16 6V2a2 2 0 00-2-2H10a2 2 0 00-2 2z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 7h6"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 10h6"/>
                                </svg>
                            @endif
                        </div>
                    @endif
                    <div class="absolute inset-0 bg-gradient-to-t {{ $gradient }} opacity-60 group-hover:opacity-40 transition-opacity duration-500"></div>
                    
                    <!-- Floating Elements -->
                    <div class="absolute top-4 right-4">
                        <div class="bg-white/20 backdrop-blur-sm rounded-full p-2 group-hover:animate-bounce transition-all duration-300">
                            <x-lucide-sparkles class="w-4 h-4 text-white" />
                        </div>
                    </div>

                    <!-- Recipe Count Badge -->
                    <div class="absolute top-4 left-4 bg-white/90 backdrop-blur-sm rounded-full px-3 py-1">
                        <span class="text-sm font-bold text-gray-800">{{ $category->recipes_count ?? 0 }}+</span>
                    </div>

                    <!-- Hover Overlay -->
                    <div class="absolute inset-0 bg-black/20 opacity-0 group-hover:opacity-100 transition-opacity duration-300">
                        <div class="absolute bottom-4 left-4 right-4">
                            <p class="text-white text-sm opacity-90">{{ $category->description ?? 'Khám phá những công thức tuyệt vời' }}</p>
                        </div>
                    </div>
                </div>

                <!-- Content -->
                <div class="p-6 relative">
                    <div class="flex items-center justify-between">
                        <div>
                            <h3 class="text-xl font-bold text-gray-800 dark:bg-gradient-to-r dark:from-red-400 dark:to-pink-400 dark:bg-clip-text dark:text-transparent dark:group-hover:from-red-300 dark:group-hover:to-pink-300 transition-all duration-300 transform group-hover:scale-110">
                                {{ $category->name }}
                            </h3>

                            <div class="flex items-center gap-2 mt-2 text-sm text-gray-500 dark:text-gray-400">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>
                                </svg>
                                <span>Công thức thịnh hành</span>
                            </div>
                        </div>
                        
                        <a href="{{ route('recipes.index', ['category' => $category->slug]) }}"
                           class="inline-flex items-center justify-center w-12 h-12 rounded-full bg-gradient-to-r {{ $gradient }} text-white shadow-lg group-hover:shadow-xl transform group-hover:scale-110 transition-all duration-300">
                            <svg class="w-5 h-5 group-hover:translate-x-1 transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                            </svg>
                        </a>
                    </div>
                </div>

                <!-- Animated Border -->
                <div class="absolute inset-0 rounded-2xl border-2 border-transparent bg-gradient-to-r {{ $gradient }} opacity-0 group-hover:opacity-100 transition-opacity duration-500 -z-10" 
                     style="padding: 2px">
                    <div class="w-full h-full rounded-2xl bg-white dark:bg-slate-800"></div>
                </div>
            </div>
            @endforeach
        </div>

        <!-- Bottom Stats -->
        <div class="text-center mt-16">
            <div class="inline-flex items-center gap-8 px-8 py-4 rounded-2xl bg-white/50 dark:bg-slate-800/50 backdrop-blur-sm border border-gray-200 dark:border-slate-700">
                <div class="text-center">
                    <div class="text-2xl font-bold text-gray-900 dark:text-white">{{ $categories->sum('recipes_count') ?? '8.5K' }}+</div>
                    <div class="text-sm text-gray-600 dark:text-gray-400">Tổng số công thức</div>
                </div>
                <div class="w-px h-8 bg-gray-300 dark:bg-gray-600"></div>
                <div class="text-center">
                    <div class="text-2xl font-bold text-gray-900 dark:text-white">125K+</div>
                    <div class="text-sm text-gray-600 dark:text-gray-400">Đầu bếp hạnh phúc</div>
                </div>
                <div class="w-px h-8 bg-gray-300 dark:bg-gray-600"></div>
                <div class="text-center">
                    <div class="text-2xl font-bold text-gray-900 dark:text-white">4.9★</div>
                    <div class="text-sm text-gray-600 dark:text-gray-400">Đánh giá trung bình</div>
                </div>
            </div>
        </div>

        <!-- View All Categories Button -->
        <div class="text-center mt-12">
            <a href="{{ route('recipes.index') }}" 
               class="inline-flex items-center px-8 py-4 bg-gradient-to-r from-red-500 to-pink-600 text-white font-semibold rounded-full shadow-lg hover:shadow-xl transform hover:-translate-y-1 transition-all duration-300 group">
                <span>Xem tất cả danh mục</span>
                <svg class="ml-2 w-5 h-5 group-hover:translate-x-1 transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                </svg>
            </a>
        </div>
    </div>
</section> 