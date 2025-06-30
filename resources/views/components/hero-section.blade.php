<section class="bg-gradient-to-r from-orange-500 to-red-500 text-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
        <div class="text-center">
            <h1 class="text-4xl md:text-5xl font-bold mb-4">Khám phá thế giới ẩm thực</h1>
            <p class="text-xl mb-8 opacity-90 max-w-2xl mx-auto">
                Chia sẻ và khám phá những công thức nấu ăn ngon nhất từ cộng đồng BeeFood
            </p>

            <!-- Featured Search -->
            <div class="max-w-2xl mx-auto">
                <div class="relative">
                    <input
                        type="text"
                        class="w-full pl-12 pr-4 py-4 text-gray-900 rounded-lg shadow-lg text-lg"
                        placeholder="Tìm kiếm món ăn yêu thích..."
                        wire:model.live="search"
                        wire:keydown.enter="performSearch"
                    />
                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center">
                        <svg class="h-6 w-6 text-gray-400" fill="none" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </div>
                    <button 
                        class="absolute inset-y-0 right-0 px-6 bg-orange-500 hover:bg-orange-600 text-white rounded-r-lg transition-colors"
                        wire:click="performSearch"
                    >
                        Tìm kiếm
                    </button>
                </div>
            </div>

            <!-- Quick Stats -->
            <div class="mt-12 grid grid-cols-1 md:grid-cols-3 gap-8">
                <div class="text-center">
                    <div class="text-3xl font-bold mb-2">{{ $stats['recipes'] ?? 0 }}+</div>
                    <div class="text-orange-100">Công thức</div>
                </div>
                <div class="text-center">
                    <div class="text-3xl font-bold mb-2">{{ $stats['users'] ?? 0 }}+</div>
                    <div class="text-orange-100">Thành viên</div>
                </div>
                <div class="text-center">
                    <div class="text-3xl font-bold mb-2">{{ $stats['categories'] ?? 0 }}+</div>
                    <div class="text-orange-100">Danh mục</div>
                </div>
            </div>
        </div>
    </div>
</section> 