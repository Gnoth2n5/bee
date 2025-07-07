@props(['collections'])

<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
    @forelse($collections as $collection)
        <a href="{{ route('collections.show', $collection) }}" class="block bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden hover:shadow-md transition-shadow">
            <div class="p-6">
                <h3 class="font-semibold text-gray-900 mb-2">{{ $collection->name }}</h3>
                <p class="text-gray-600 text-sm mb-4 line-clamp-2">{{ $collection->description }}</p>
                <div class="flex items-center justify-between text-sm text-gray-500">
                    <span>{{ $collection->recipes_count }} công thức</span>
                    <span>{{ $collection->created_at->diffForHumans() }}</span>
                </div>
            </div>
        </a>
    @empty
        <div class="col-span-full text-center py-12">
            <svg class="w-16 h-16 text-gray-300 mx-auto mb-4" fill="currentColor" viewBox="0 0 20 20">
                <path d="M7 3a1 1 0 000 2h6a1 1 0 100-2H7zM4 7a1 1 0 011-1h10a1 1 0 110 2H5a1 1 0 01-1-1zM2 11a2 2 0 012-2h12a2 2 0 012 2v4a2 2 0 01-2 2H4a2 2 0 01-2-2v-4z" />
            </svg>
            <h3 class="text-lg font-medium text-gray-900 mb-2">Chưa có bộ sưu tập nào</h3>
            <p class="text-gray-500 mb-4">Tạo bộ sưu tập để tổ chức công thức yêu thích!</p>
            <a href="{{ route('collections.create') }}" class="inline-flex items-center px-4 py-2 bg-orange-500 hover:bg-orange-600 text-white rounded-lg font-medium transition-colors">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                </svg>
                Tạo bộ sưu tập
            </a>
        </div>
    @endforelse
</div>

@if($collections->hasPages())
    <div class="mt-8">
        {{ $collections->links() }}
    </div>
@endif 