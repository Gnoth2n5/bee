<div class="max-w-5xl mx-auto py-8 px-4">
    <!-- Header với thông tin bộ sưu tập -->
    <div class="bg-white rounded-xl shadow overflow-hidden mb-8">
        <div class="flex flex-col md:flex-row items-stretch min-h-[180px]">
            <div class="w-full md:w-64 bg-gray-200 flex items-center justify-center md:rounded-l-xl overflow-hidden relative" style="min-height:100%;height:auto;">
                @if($isEditing)
                    @if($editCoverImagePreview)
                        <div class="relative w-full h-full">
                            <img src="{{ $editCoverImagePreview }}" alt="Preview" class="w-full h-full object-cover" style="min-height:100%;height:100%;" />
                            <!-- Nút X để huỷ file -->
                            <button type="button" wire:click="resetEditCoverImage" class="absolute top-2 right-2 bg-white/80 hover:bg-white text-gray-700 rounded-full p-1 shadow border border-gray-200">
                                <x-heroicon-o-x-mark class="w-4 h-4" />
                            </button>
                        </div>
                    @elseif($collection->cover_image)
                        <img src="{{ Storage::url($collection->cover_image) }}" alt="Ảnh bìa" class="w-full h-full object-cover" style="min-height:100%;height:100%;" />
                    @else
                        <x-heroicon-o-rectangle-stack class="w-16 h-16 text-gray-400" />
                    @endif
                    <!-- Nút đổi ảnh -->
                    <label for="editCoverImageInput" class="absolute bottom-2 right-2 bg-white/80 hover:bg-white shadow rounded-full p-2 cursor-pointer border border-gray-200 flex items-center justify-center transition">
                        <x-heroicon-o-camera class="w-5 h-5 text-gray-700" />
                        <input id="editCoverImageInput" type="file" wire:model="editCoverImage" accept="image/*" class="hidden" />
                    </label>
                @else
                    @if($collection->cover_image)
                        <img src="{{ Storage::url($collection->cover_image) }}" alt="{{ $collection->name }}" class="w-full h-full object-cover" style="min-height:100%;height:100%;" />
                    @else
                        <x-heroicon-o-rectangle-stack class="w-16 h-16 text-gray-400" />
                    @endif
                @endif
            </div>
            <div class="flex-1 p-6 flex flex-col justify-between">
                @if($isEditing)
                    <form wire:submit.prevent="updateCollection" class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Tên bộ sưu tập <span class="text-red-500">*</span></label>
                            <input type="text" wire:model.defer="editName" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500" maxlength="255">
                            @error('editName')<p class="text-red-500 text-sm mt-1">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Mô tả</label>
                            <textarea wire:model.defer="editDescription" rows="3" maxlength="1000" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500"></textarea>
                            @error('editDescription')<p class="text-red-500 text-sm mt-1">{{ $message }}</p>@enderror
                        </div>
                        <div class="flex items-center gap-3 mb-2">
                            <label class="flex items-center cursor-pointer">
                                <input type="checkbox" wire:model.defer="editIsPublic" class="w-4 h-4 text-orange-600 bg-gray-100 border-gray-300 rounded focus:ring-orange-500 focus:ring-2">
                                <span class="ml-2 text-sm text-gray-700">Công khai bộ sưu tập</span>
                            </label>
                            <span class="text-xs text-gray-500">Bộ sưu tập công khai sẽ hiển thị cho tất cả mọi người</span>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Ảnh bìa</label>
                            @if($editCoverImagePreview)
                                <img src="{{ $editCoverImagePreview }}" alt="Preview" class="w-16 h-16 object-cover rounded-lg border" />
                            @elseif($collection->cover_image)
                                <img src="{{ Storage::url($collection->cover_image) }}" alt="Ảnh bìa" class="w-16 h-16 object-cover rounded-lg border" />
                            @endif
                            @error('editCoverImage')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                            @if($editCoverImage)
                                <p class="text-xs text-gray-400 mt-1">Đang xem trước ảnh mới...</p>
                            @endif
                        </div>
                        <div class="flex gap-2 mt-4">
                            <button type="submit" class="bg-orange-500 hover:bg-orange-600 text-white px-5 py-2 rounded-lg font-semibold transition">Lưu</button>
                            <button type="button" wire:click="cancelEdit" class="bg-gray-300 hover:bg-gray-400 text-gray-700 px-5 py-2 rounded-lg font-semibold transition">Huỷ</button>
                        </div>
                    </form>
                @else
                    <div>
                        <h1 class="text-2xl font-bold text-gray-900 mb-2">{{ $collection->name }}</h1>
                        <div class="flex items-center gap-2 mb-2">
                            @if($collection->is_public)
                                <span class="px-2 py-1 text-xs bg-green-100 text-green-700 rounded-full">Công khai</span>
                            @else
                                <span class="px-2 py-1 text-xs bg-gray-200 text-gray-600 rounded-full">Riêng tư</span>
                            @endif
                            <span class="px-2 py-1 text-xs bg-orange-100 text-orange-700 rounded-full">{{ $collection->recipe_count }} công thức</span>
                        </div>
                        <p class="text-gray-700 mb-4">{{ $collection->description ?: 'Chưa có mô tả' }}</p>
                    </div>
                    <div class="flex items-center gap-3 mt-2">
                        <div class="flex items-center gap-2">
                            @if($collection->user->profile && $collection->user->profile->avatar)
                                <img src="{{ Storage::url($collection->user->profile->avatar) }}" alt="{{ $collection->user->name }}" class="w-8 h-8 rounded-full object-cover" />
                            @else
                                <span class="w-8 h-8 rounded-full bg-orange-100 flex items-center justify-center text-orange-600 font-bold">{{ strtoupper(substr($collection->user->name,0,1)) }}</span>
                            @endif
                            <span class="font-medium text-gray-800">{{ $collection->user->name }}</span>
                        </div>
                        <span class="text-xs text-gray-400">Tạo lúc {{ $collection->created_at->format('d/m/Y') }}</span>
                    </div>
                    @if($isOwner)
                        <div class="mt-4 flex gap-2">
                            <button wire:click="showEdit" class="bg-orange-500 hover:bg-orange-600 text-white px-4 py-2 rounded-lg font-semibold transition flex items-center gap-2">
                                <x-heroicon-o-pencil-square class="w-4 h-4" />
                                Chỉnh sửa
                            </button>
                            <button 
                                wire:click="deleteCollection"
                                wire:confirm="Bạn có chắc muốn xóa bộ sưu tập này? Hành động này không thể hoàn tác."
                                class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded-lg font-semibold transition flex items-center gap-2"
                            >
                                <x-heroicon-o-trash class="w-4 h-4" />
                                Xóa
                            </button>
                        </div>
                    @endif
                @endif
            </div>
        </div>
    </div>

    <!-- Danh sách công thức -->
    <div class="mb-6 flex items-center justify-between">
        <h2 class="text-xl font-semibold text-gray-900">Danh sách công thức</h2>
        @if($isOwner && $recipes->count() > 0)
            <span class="text-sm text-gray-500">Bạn có thể xóa công thức khỏi bộ sưu tập bằng nút bên dưới mỗi card.</span>
        @endif
    </div>

    @if($recipes->count() > 0)
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-6">
            @foreach($recipes as $recipe)
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden hover:shadow-md transition-shadow flex flex-col">
                    <a href="{{ route('recipes.show', $recipe) }}" class="block">
                        @if($recipe->primary_image)
                            <img src="{{ Storage::url($recipe->primary_image->image_path) }}" alt="{{ $recipe->title }}" class="w-full h-40 object-cover" />
                        @else
                            <div class="w-full h-40 bg-gray-200 flex items-center justify-center text-gray-400 text-2xl">No Image</div>
                        @endif
                        <div class="p-4">
                            <h3 class="font-semibold text-gray-900 mb-1 line-clamp-2">{{ $recipe->title }}</h3>
                            <div class="flex items-center gap-2 text-xs text-gray-500 mb-1">
                                <span>{{ $recipe->cooking_time }} phút</span>
                                <span>•</span>
                                <span>{{ $recipe->servings }} khẩu phần</span>
                            </div>
                            <div class="flex items-center gap-2">
                                @foreach($recipe->categories->take(2) as $cat)
                                    <span class="px-2 py-1 text-xs bg-orange-100 text-orange-700 rounded-full">{{ $cat->name }}</span>
                                @endforeach
                                @if($recipe->categories->count() > 2)
                                    <span class="px-2 py-1 text-xs bg-gray-100 text-gray-600 rounded-full">+{{ $recipe->categories->count() - 2 }}</span>
                                @endif
                            </div>
                        </div>
                    </a>
                    @if($isOwner)
                        <div class="p-2 border-t flex justify-end">
                            <button 
                                onclick="confirmRemoveRecipe({{ $recipe->id }}, '{{ $recipe->title }}')"
                                wire:loading.attr="disabled"
                                class="text-red-600 hover:text-red-800 text-xs font-medium disabled:opacity-50 flex items-center gap-1"
                                title="Xóa khỏi bộ sưu tập"
                            >
                                <x-heroicon-o-trash class="w-3 h-3" />
                                <span wire:loading.remove>Xóa khỏi bộ sưu tập</span>
                                <span wire:loading>Đang xóa...</span>
                            </button>
                        </div>
                    @endif
                </div>
            @endforeach
        </div>

        <!-- Phân trang -->
        @if($recipes->hasPages())
            <div class="mt-8">
                {{ $recipes->links() }}
            </div>
        @endif
    @else
        <div class="text-center py-16">
            <x-heroicon-o-rectangle-stack class="w-16 h-16 text-gray-300 mx-auto mb-4" />
            <h3 class="text-lg font-medium text-gray-900 mb-2">Chưa có công thức nào trong bộ sưu tập này</h3>
            <p class="text-gray-500 mb-4">Hãy thêm công thức vào bộ sưu tập để bắt đầu!</p>
            @if($isOwner)
                <a href="{{ route('recipes.index') }}" class="inline-flex items-center px-4 py-2 bg-orange-500 hover:bg-orange-600 text-white rounded-lg font-medium transition-colors">
                    <x-heroicon-o-plus class="w-4 h-4 mr-2" />
                    Khám phá công thức
                </a>
            @endif
        </div>
    @endif

    <!-- Flash Messages -->
    <x-flash-message />
</div>

<script>
function confirmRemoveRecipe(recipeId, recipeTitle) {
    // Tạo modal với Tailwind CSS
    const modal = document.createElement('div');
    modal.className = 'fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50';
    modal.innerHTML = `
        <div class="bg-white rounded-xl shadow-2xl p-6 w-96 mx-4 transform transition-all">
            <div class="flex items-center justify-center w-12 h-12 mx-auto bg-red-100 rounded-full mb-4">
                <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z" />
                </svg>
            </div>
            <h3 class="text-lg font-semibold text-gray-900 text-center mb-2">Xác nhận xóa</h3>
            <p class="text-gray-600 text-center mb-6">Xóa công thức <strong>"${recipeTitle}"</strong> khỏi bộ sưu tập?</p>
            <div class="flex gap-3">
                <button id="cancelBtn" class="flex-1 px-4 py-2 text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-lg font-medium transition-colors">
                    Hủy
                </button>
                <button id="confirmBtn" class="flex-1 px-4 py-2 bg-red-500 hover:bg-red-600 text-white rounded-lg font-medium transition-colors">
                    Xóa
                </button>
            </div>
        </div>
    `;
    
    document.body.appendChild(modal);
    
    // Xử lý sự kiện
    const cancelBtn = modal.querySelector('#cancelBtn');
    const confirmBtn = modal.querySelector('#confirmBtn');
    
    const removeModal = () => {
        modal.remove();
    };
    
    // Đóng modal khi click bên ngoài
    modal.addEventListener('click', (e) => {
        if (e.target === modal) {
            removeModal();
        }
    });
    
    // Đóng modal khi nhấn ESC
    const handleEsc = (e) => {
        if (e.key === 'Escape') {
            removeModal();
            document.removeEventListener('keydown', handleEsc);
        }
    };
    document.addEventListener('keydown', handleEsc);
    
    cancelBtn.addEventListener('click', () => {
        removeModal();
        document.removeEventListener('keydown', handleEsc);
    });
    
    confirmBtn.addEventListener('click', () => {
        removeModal();
        document.removeEventListener('keydown', handleEsc);
        // Gọi Livewire method
        @this.removeRecipe(recipeId);
    });
}
</script> 