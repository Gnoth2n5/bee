@props(['collections', 'showCreateModal', 'newName', 'newDescription', 'newIsPublic', 'newCoverImage', 'newCoverImagePreview'])

<div class="flex justify-between items-center mb-4">
    <h2 class="text-xl font-semibold text-gray-900">Bộ sưu tập của bạn</h2>
    <button 
        class="inline-flex items-center px-4 py-2 bg-orange-500 hover:bg-orange-600 text-white rounded-lg font-medium transition-colors"
        wire:click="$set('showCreateModal', true)"
        type="button"
    >
        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
        </svg>
        Tạo bộ sưu tập
    </button>
</div>

<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
    @forelse($collections as $collection)
        <a href="{{ route('collections.show', $collection) }}" class="block bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden hover:shadow-md transition-shadow">
            <div class="aspect-video bg-gray-200 relative overflow-hidden">
                @if($collection->cover_image)
                    <img src="{{ Storage::url($collection->cover_image) }}" alt="{{ $collection->name }}" class="w-full h-full object-cover">
                @else
                    <div class="w-full h-full flex items-center justify-center">
                        <svg class="w-16 h-16 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M7 3a1 1 0 000 2h6a1 1 0 100-2H7zM4 7a1 1 0 011-1h10a1 1 0 110 2H5a1 1 0 01-1-1zM2 11a2 2 0 012-2h12a2 2 0 012 2v4a2 2 0 01-2 2H4a2 2 0 01-2-2v-4z" />
                        </svg>
                    </div>
                @endif
                @if($collection->is_public)
                    <div class="absolute top-2 right-2 bg-green-500 text-white text-xs px-2 py-1 rounded-full">
                        Công khai
                    </div>
                @else
                    <div class="absolute top-2 right-2 bg-gray-500 text-white text-xs px-2 py-1 rounded-full">
                        Riêng tư
                    </div>
                @endif
            </div>
            <div class="p-6">
                <h3 class="font-semibold text-gray-900 mb-2">{{ $collection->name }}</h3>
                <p class="text-gray-600 text-sm mb-4 line-clamp-2">{{ $collection->description ?: 'Chưa có mô tả' }}</p>
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
            <button 
                wire:click="$set('showCreateModal', true)"
                class="inline-flex items-center px-4 py-2 bg-orange-500 hover:bg-orange-600 text-white rounded-lg font-medium transition-colors"
            >
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                </svg>
                Tạo bộ sưu tập
            </button>
        </div>
    @endforelse
</div>

@if($collections->hasPages())
    <div class="mt-8">
        {{ $collections->links() }}
    </div>
@endif

<!-- Modal tạo bộ sưu tập -->
@if($showCreateModal)
    <div class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-40">
        <div class="bg-white rounded-lg shadow-lg w-full max-w-lg p-6 relative">
            <button class="absolute top-2 right-2 text-gray-400 hover:text-gray-600" wire:click="resetCollectionForm">
                <x-heroicon-o-x-mark class="w-5 h-5" />
            </button>
            <h3 class="text-lg font-semibold mb-4">Tạo bộ sưu tập mới</h3>
            <form wire:submit.prevent="createCollection" class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Tên bộ sưu tập <span class="text-red-500">*</span></label>
                    <input type="text" wire:model.defer="newName" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500" maxlength="255" placeholder="Nhập tên bộ sưu tập">
                    @error('newName')<p class="text-red-500 text-sm mt-1">{{ $error->first() }}</p>@enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Mô tả</label>
                    <textarea wire:model.defer="newDescription" rows="3" maxlength="1000" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500" placeholder="Mô tả về bộ sưu tập của bạn"></textarea>
                    @error('newDescription')<p class="text-red-500 text-sm mt-1">{{ $error->first() }}</p>@enderror
                </div>
                <div class="flex items-center gap-3 mb-2">
                    <label class="flex items-center cursor-pointer">
                        <input type="checkbox" wire:model.defer="newIsPublic" class="w-4 h-4 text-orange-600 bg-gray-100 border-gray-300 rounded focus:ring-orange-500 focus:ring-2">
                        <span class="ml-2 text-sm text-gray-700">Công khai bộ sưu tập</span>
                    </label>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Ảnh bìa</label>
                    <input type="file" wire:model="newCoverImage" accept="image/*" class="block w-full text-sm text-gray-900 border border-gray-300 rounded-lg cursor-pointer bg-gray-50 focus:outline-none" />
                    @if($newCoverImagePreview)
                        <div class="mt-2 relative inline-block">
                            <img src="{{ $newCoverImagePreview }}" alt="Preview" class="w-20 h-20 object-cover rounded-lg border" />
                            <button type="button" wire:click="$set('newCoverImage', null)" class="absolute -top-2 -right-2 bg-red-500 text-white rounded-full w-6 h-6 flex items-center justify-center text-xs hover:bg-red-600">
                                ×
                            </button>
                        </div>
                    @endif
                    @error('newCoverImage')<p class="text-red-500 text-sm mt-1">{{ $error->first() }}</p>@enderror
                </div>
                <div class="flex gap-2 mt-4">
                    <button type="submit" class="bg-orange-500 hover:bg-orange-600 text-white px-5 py-2 rounded-lg font-semibold transition">Lưu</button>
                    <button type="button" wire:click="resetCollectionForm" class="bg-gray-300 hover:bg-gray-400 text-gray-700 px-5 py-2 rounded-lg font-semibold transition">Huỷ</button>
                </div>
            </form>
        </div>
    </div>
@endif 