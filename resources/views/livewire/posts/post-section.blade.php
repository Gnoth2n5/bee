<div>
    <div class="bg-white py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Tiêu đề section -->
            <div class="text-center mb-12">
                <h2 class="text-3xl font-bold text-gray-900 mb-4">
                    Bài viết nổi bật
                </h2>
                <p class="text-lg text-gray-600 max-w-2xl mx-auto">
                    Khám phá những bài viết hay nhất về ẩm thực, công thức nấu ăn và mẹo vặt trong bếp
                </p>
            </div>

            <!-- Grid 2 cột -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <!-- Cột trái - Slide bài viết phổ biến -->
                <div class="lg:col-span-2">
                    <div class="relative bg-white rounded-lg shadow-lg overflow-hidden">
                        @if($popularPosts->count() > 0)
                            <!-- Slide container -->
                            <div class="relative h-96">
                                @foreach($popularPosts as $index => $post)
                                    <div class="absolute inset-0 transition-opacity duration-500 {{ $index === $currentSlide ? 'opacity-100' : 'opacity-0' }}">
                                        <div class="relative h-full">
                                            <!-- Ảnh nền -->
                                            <div class="absolute inset-0 bg-gradient-to-br from-orange-500 to-red-600">
                                                @if($post->featured_image)
                                                    <img src="{{ Storage::url($post->featured_image) }}" 
                                                         alt="{{ $post->title }}" 
                                                         class="w-full h-full object-cover opacity-75">
                                                @endif
                                            </div>
                                            
                                            <!-- Overlay gradient -->
                                            <div class="absolute inset-0 bg-gradient-to-t from-black/70 via-black/30 to-transparent"></div>
                                            
                                            <!-- Nội dung -->
                                            <div class="absolute bottom-0 left-0 right-0 p-6 text-white">
                                                <div class="flex items-center text-sm text-orange-200 mb-2">
                                                    <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                                        <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"></path>
                                                    </svg>
                                                    {{ $post->user->name }}
                                                    <span class="mx-2">•</span>
                                                    <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"></path>
                                                    </svg>
                                                    {{ $post->published_at->diffForHumans() }}
                                                </div>
                                                
                                                <h3 class="text-2xl font-bold mb-3 line-clamp-2">
                                                    {{ $post->title }}
                                                </h3>
                                                
                                                <p class="text-gray-200 mb-4 line-clamp-3">
                                                    {{ $post->excerpt ?: Str::limit(strip_tags($post->content), 150) }}
                                                </p>
                                                
                                                <div class="flex items-center justify-between">
                                                    <div class="flex items-center text-sm text-orange-200">
                                                        <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                            <path d="M10 12a2 2 0 100-4 2 2 0 000 4z"></path>
                                                            <path fill-rule="evenodd" d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z" clip-rule="evenodd"></path>
                                                        </svg>
                                                        {{ number_format($post->view_count) }} lượt xem
                                                    </div>
                                                    
                                                    <a href="{{ route('posts.show', $post->slug) }}" 
                                                       class="inline-flex items-center px-4 py-2 bg-orange-500 hover:bg-orange-600 text-white text-sm font-medium rounded-lg transition-colors duration-200">
                                                        Đọc thêm
                                                        <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                                        </svg>
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                            
                            <!-- Navigation buttons -->
                            <button wire:click="previousSlide" 
                                    class="absolute left-4 top-1/2 transform -translate-y-1/2 bg-white/20 hover:bg-white/30 text-white p-2 rounded-full transition-colors duration-200">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                                </svg>
                            </button>
                            
                            <button wire:click="nextSlide" 
                                    class="absolute right-4 top-1/2 transform -translate-y-1/2 bg-white/20 hover:bg-white/30 text-white p-2 rounded-full transition-colors duration-200">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                </svg>
                            </button>
                            
                            <!-- Dots indicator -->
                            <div class="absolute bottom-4 left-1/2 transform -translate-x-1/2 flex space-x-2">
                                @foreach($popularPosts as $index => $post)
                                    <button wire:click="goToSlide({{ $index }})" 
                                            class="w-3 h-3 rounded-full transition-colors duration-200 {{ $index === $currentSlide ? 'bg-white' : 'bg-white/50' }}">
                                    </button>
                                @endforeach
                            </div>
                        @else
                            <div class="h-96 flex items-center justify-center bg-gray-50">
                                <div class="text-center">
                                    <svg class="w-16 h-16 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                    </svg>
                                    <p class="text-gray-500">Chưa có bài viết nào</p>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Cột phải - Danh sách bài viết mới nhất -->
                <div class="lg:col-span-1">
                    <div class="bg-white rounded-lg shadow-lg p-6">
                        <h3 class="text-xl font-bold text-gray-900 mb-6 flex items-center">
                            <svg class="w-5 h-5 mr-2 text-orange-500" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"></path>
                            </svg>
                            Bài viết mới nhất
                        </h3>
                        
                        <div class="space-y-4">
                            @forelse($latestPosts as $post)
                                <article class="group">
                                    <a href="{{ route('posts.show', $post->slug) }}" class="block">
                                        <div class="flex space-x-4">
                                            <!-- Ảnh thumbnail -->
                                            <div class="flex-shrink-0">
                                                @if($post->featured_image)
                                                    <img src="{{ Storage::url($post->featured_image) }}" 
                                                         alt="{{ $post->title }}" 
                                                         class="w-20 h-20 object-cover rounded-lg">
                                                @else
                                                    <div class="w-20 h-20 bg-gradient-to-br from-orange-400 to-red-500 rounded-lg flex items-center justify-center">
                                                        <svg class="w-8 h-8 text-white" fill="currentColor" viewBox="0 0 20 20">
                                                            <path fill-rule="evenodd" d="M4 3a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V5a2 2 0 00-2-2H4zm12 12H4l4-8 3 6 2-4 3 6z" clip-rule="evenodd"></path>
                                                        </svg>
                                                    </div>
                                                @endif
                                            </div>
                                            
                                            <!-- Nội dung -->
                                            <div class="flex-1 min-w-0">
                                                <h4 class="text-sm font-semibold text-gray-900 group-hover:text-orange-600 transition-colors duration-200 line-clamp-2">
                                                    {{ $post->title }}
                                                </h4>
                                                
                                                <div class="flex items-center text-xs text-gray-500 mt-2">
                                                    <span>{{ $post->user->name }}</span>
                                                    <span class="mx-1">•</span>
                                                    <span>{{ $post->published_at->diffForHumans() }}</span>
                                                </div>
                                                
                                                <div class="flex items-center text-xs text-gray-400 mt-1">
                                                    <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                        <path d="M10 12a2 2 0 100-4 2 2 0 000 4z"></path>
                                                        <path fill-rule="evenodd" d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z" clip-rule="evenodd"></path>
                                                    </svg>
                                                    {{ number_format($post->view_count) }} lượt xem
                                                </div>
                                            </div>
                                        </div>
                                    </a>
                                </article>
                            @empty
                                <div class="text-center py-8">
                                    <svg class="w-12 h-12 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                    </svg>
                                    <p class="text-gray-500">Chưa có bài viết nào</p>
                                </div>
                            @endforelse
                        </div>
                        
                        @if($latestPosts->count() > 0)
                            <div class="mt-6 pt-4 border-t border-gray-200">
                                <a href="{{ route('posts.index') }}" 
                                   class="block text-center text-orange-600 hover:text-orange-700 font-medium text-sm">
                                    Xem tất cả bài viết
                                    <svg class="w-4 h-4 inline ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                    </svg>
                                </a>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>
    .line-clamp-2 {
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }

    .line-clamp-3 {
        display: -webkit-box;
        -webkit-line-clamp: 3;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }
    </style>
</div> 