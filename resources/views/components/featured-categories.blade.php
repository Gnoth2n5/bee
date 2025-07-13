<section class="py-12 bg-gray-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-8">
            <h2 class="text-3xl font-bold text-gray-900 mb-4">Danh mục nổi bật</h2>
            <p class="text-gray-600 max-w-2xl mx-auto">Khám phá các danh mục món ăn phổ biến và đa dạng</p>
        </div>

        <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-6">
            @foreach($categories as $category)
            <a href="{{ route('recipes.index', ['category' => $category->slug]) }}" 
               class="bg-white rounded-lg shadow-sm hover:shadow-md transition-all duration-300 transform hover:-translate-y-1 cursor-pointer group flex flex-col">
                <div class="aspect-square rounded-t-lg overflow-hidden bg-gradient-to-br from-orange-400 to-red-500 flex items-center justify-center">
                    @if($category->image)
                        <img src="{{ asset('storage/' . $category->image) }}" 
                             alt="{{ $category->name }}" 
                             class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                    @else
                        <div class="w-full h-full bg-gradient-to-br from-orange-400 to-red-500 flex items-center justify-center">
                            @if($category->icon)
                                <i class="{{ $category->icon }} text-white text-3xl"></i>
                            @else
                                <svg class="w-10 h-10 text-white mx-auto" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h6" />
                                    <circle cx="12" cy="12" r="10" />
                                </svg>
                            @endif
                        </div>
                    @endif
                </div>
                <div class="p-4 text-center flex-1 flex flex-col justify-center">
                    <h3 class="font-medium text-gray-900 group-hover:text-orange-600 transition-colors">{{ $category->name }}</h3>
                    <p class="text-sm text-gray-500 mt-1">{{ $category->recipes_count ?? 0 }} công thức</p>
                </div>
            </a>
            @endforeach
        </div>

        <div class="text-center mt-8">
            <a href="{{ route('recipes.index') }}" 
               class="inline-flex items-center px-6 py-3 border border-orange-500 text-orange-600 rounded-lg hover:bg-orange-500 hover:text-white transition-colors">
                Xem tất cả danh mục
                <svg class="ml-2 w-5 h-5" fill="none" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                </svg>
            </a>
        </div>
    </div>
</section> 