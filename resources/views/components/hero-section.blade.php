@props(['stats'])

<section class="relative h-[83vh] bg-gradient-to-r from-orange-500 to-red-500 text-white overflow-hidden" style="background-image: url('/images/banner.webp'); background-size: cover; background-position: center;">
    <!-- Shadow overlay for better text readability -->
    <div class="absolute inset-0 bg-gradient-to-b from-black/40 via-black/20 to-black/60"></div>
    
    <div class="relative h-full z-10 max-w-10xl mx-auto px-4 sm:px-6 lg:px-8 py-16 flex flex-col justify-center">
        <div class="text-center h-full flex flex-col justify-center">
            <h1 class="text-4xl md:text-5xl font-bold mb-4 drop-shadow-lg">Khám phá thế giới ẩm thực</h1>
            <p class="text-xl mb-8 opacity-95 max-w-2xl mx-auto drop-shadow-md">
                Chia sẻ và khám phá những công thức nấu ăn ngon nhất từ cộng đồng BeeFood
            </p>

            <!-- Featured Search -->
            <div class="w-[50%] mx-auto">
                <livewire:quick-search />
            </div>

          <!-- Stats Grid with Circular Backgrounds -->
          <div class="flex justify-center mt-10  gap-[100px] mx-auto">
                <!-- Recipes Stat -->
                <div class="group">
                    <div class="bg-white/15 backdrop-blur-sm rounded-full p-6 border border-white/25 shadow-xl hover:bg-white/20 transition-all duration-300 hover:scale-105">
                        <div class="text-center">
                            <div class="text-5xl font-bold mb-2 drop-shadow-lg group-hover:text-orange-200 transition-colors">
                                {{ $stats['recipes'] ?? 0 }}+
                            </div>
                            <div class="text-orange-100 drop-shadow-md font-medium">Công thức</div>
                        </div>
                    </div>
                </div>

                <!-- Users Stat -->
                <div class="group">
                    <div class="bg-white/15 backdrop-blur-sm rounded-full p-6 border border-white/25 shadow-xl hover:bg-white/20 transition-all duration-300 hover:scale-105">
                        <div class="text-center">
                            <div class="text-5xl font-bold mb-2 drop-shadow-lg group-hover:text-orange-200 transition-colors">
                                {{ $stats['users'] ?? 0 }}+
                            </div>
                            <div class="text-orange-100 drop-shadow-md font-medium">Thành viên</div>
                        </div>
                    </div>
                </div>

                <!-- Categories Stat -->
                <div class="group">
                    <div class="bg-white/15 backdrop-blur-sm rounded-full p-6 border border-white/25 shadow-xl hover:bg-white/20 transition-all duration-300 hover:scale-105">
                        <div class="text-center">
                            <div class="text-5xl font-bold mb-2 drop-shadow-lg group-hover:text-orange-200 transition-colors">
                                {{ $stats['categories'] ?? 0 }}+
                            </div>
                            <div class="text-orange-100 drop-shadow-md font-medium">Danh mục</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section> 