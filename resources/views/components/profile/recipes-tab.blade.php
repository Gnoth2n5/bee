@props(['recipes'])

<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
    @forelse($recipes as $recipe)
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden hover:shadow-md transition-shadow">
            <div class="aspect-w-16 aspect-h-9">
                @if($recipe->images->first())
                    <img src="{{ Storage::url($recipe->images->first()->image_path) }}" alt="{{ $recipe->title }}" class="w-full h-48 object-cover">
                @else
                    <div class="w-full h-48 bg-gradient-to-br from-orange-100 to-red-100 flex items-center justify-center">
                        <svg class="w-12 h-12 text-orange-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M4 3a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V5a2 2 0 00-2-2H4zm12 12H4l4-8 3 6 2-4 3 6z" clip-rule="evenodd" />
                        </svg>
                    </div>
                @endif
            </div>
            <div class="p-4">
                <h3 class="font-semibold text-gray-900 mb-2 line-clamp-2">{{ $recipe->title }}</h3>
                <div class="flex items-center justify-between text-sm text-gray-500">
                    <span>{{ $recipe->category->name ?? 'Không phân loại' }}</span>
                    <span>{{ $recipe->created_at->diffForHumans() }}</span>
                </div>
            </div>
        </div>
    @empty
        <div class="col-span-full text-center py-12">
            <svg class="w-16 h-16 text-gray-300 mx-auto mb-4" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M4 3a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V5a2 2 0 00-2-2H4zm12 12H4l4-8 3 6 2-4 3 6z" clip-rule="evenodd" />
            </svg>
            <h3 class="text-lg font-medium text-gray-900 mb-2">Chưa có công thức nào</h3>
            <p class="text-gray-500 mb-4">Bắt đầu chia sẻ công thức nấu ăn của bạn!</p>
            <a href="{{ route('recipes.create') }}" class="inline-flex items-center px-4 py-2 bg-orange-500 hover:bg-orange-600 text-white rounded-lg font-medium transition-colors">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                </svg>
                Tạo công thức
            </a>
        </div>
    @endforelse
</div>

@if($recipes->hasPages())
    <div class="mt-8">
        {{ $recipes->links() }}
    </div>
@endif 