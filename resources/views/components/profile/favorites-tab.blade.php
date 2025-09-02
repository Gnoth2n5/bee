@props(['favorites'])

<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
    @forelse($favorites as $favorite)
        @if($favorite->recipe)
            <x-favorite-recipe-card :recipe="$favorite->recipe" />
        @endif
    @empty
        <div class="col-span-full text-center py-16">
            <div class="w-20 h-20 bg-gradient-to-br from-red-100 to-pink-100 dark:from-red-900/30 dark:to-pink-900/30 rounded-full flex items-center justify-center mx-auto mb-6">
                <svg class="w-10 h-10 text-red-500" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M3.172 5.172a4 4 0 015.656 0L10 6.343l1.172-1.171a4 4 0 115.656 5.656L10 17.657l-6.828-6.829a4 4 0 010-5.656z" clip-rule="evenodd" />
                </svg>
            </div>
            <h3 class="text-xl font-bold bg-gradient-to-r from-red-600 to-pink-600 bg-clip-text text-transparent mb-3">Chưa có công thức yêu thích</h3>
            <p class="text-gray-600 dark:text-gray-400 mb-8 max-w-md mx-auto">Khám phá và lưu các công thức yêu thích để xem lại sau!</p>
            <a href="{{ route('recipes.index') }}" 
               class="group inline-flex items-center px-6 py-3 bg-gradient-to-r from-red-500 to-pink-600 hover:from-red-600 hover:to-pink-700 text-white font-semibold rounded-lg shadow-md hover:shadow-lg transform hover:scale-105 transition-all duration-300">
                <svg class="w-4 h-4 mr-2 group-hover:scale-110 transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                </svg>
                Khám phá công thức
            </a>
        </div>
    @endforelse
</div>

@if($favorites->hasPages())
    <div class="mt-8">
        {{ $favorites->links() }}
    </div>
@endif 