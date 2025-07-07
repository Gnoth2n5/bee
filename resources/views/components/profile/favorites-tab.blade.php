@props(['favorites'])

<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
    @forelse($favorites as $favorite)
        <x-recipe-card :recipe="$favorite->recipe" :showRemoveButton="true" />
    @empty
        <div class="col-span-full text-center py-12">
            <svg class="w-16 h-16 text-gray-300 mx-auto mb-4" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M3.172 5.172a4 4 0 015.656 0L10 6.343l1.172-1.171a4 4 0 115.656 5.656L10 17.657l-6.828-6.829a4 4 0 010-5.656z" clip-rule="evenodd" />
            </svg>
            <h3 class="text-lg font-medium text-gray-900 mb-2">Chưa có công thức yêu thích</h3>
            <p class="text-gray-500 mb-4">Khám phá và lưu công thức yêu thích để xem lại sau!</p>
            <a href="{{ route('recipes.index') }}" 
               class="inline-flex items-center px-4 py-2 bg-orange-500 hover:bg-orange-600 text-white rounded-lg font-medium transition-colors">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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