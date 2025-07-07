<div>
    <!-- Nút "Thêm vào bộ sưu tập" -->
    <button 
        wire:click="openModal"
        class="bg-white border border-orange-500 text-orange-600 px-5 py-2 rounded-lg font-semibold hover:bg-orange-50 transition flex items-center gap-2"
        type="button"
    >
        <x-heroicon-o-rectangle-stack class="w-5 h-5" />
        Thêm vào bộ sưu tập
    </button>

    <!-- Modal chọn bộ sưu tập -->
    @if($showModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50" wire:click="closeModal">
            <div class="bg-white rounded-xl shadow-lg p-6 w-96 max-w-full mx-4" wire:click.stop>
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-bold text-gray-900">Thêm vào bộ sưu tập</h3>
                    <button wire:click="closeModal" class="text-gray-400 hover:text-gray-600">
                        <x-heroicon-o-x-mark class="w-6 h-6" />
                    </button>
                </div>

                @if($collections->count() > 0)
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Chọn bộ sưu tập:</label>
                        <select 
                            wire:model="selectedCollectionId"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500"
                        >
                            <option value="">-- Chọn bộ sưu tập --</option>
                            @foreach($collections as $collection)
                                @php
                                    $isInCollection = $this->recipeCollections->contains('id', $collection->id);
                                @endphp
                                <option value="{{ $collection->id }}" {{ $isInCollection ? 'disabled' : '' }}>
                                    {{ $collection->name }} ({{ $collection->recipe_count }} công thức){{ $isInCollection ? ' - Đã có' : '' }}
                                </option>
                            @endforeach
                        </select>
                        @error('selectedCollectionId')
                            <p class="text-red-500 text-sm mt-1">{{ $error->first() }}</p>
                        @enderror
                        
                        @if($this->recipeCollections->count() > 0)
                            <div class="mt-3 p-3 bg-green-50 border border-green-200 rounded-lg">
                                <p class="text-sm font-medium text-green-800 mb-2">Công thức này đã có trong:</p>
                                <ul class="text-sm text-green-700 space-y-1">
                                                                    @foreach($this->recipeCollections as $collection)
                                    <li class="flex items-center justify-between">
                                        <div class="flex items-center gap-2">
                                            <x-heroicon-s-check-circle class="w-4 h-4 text-green-600" />
                                            {{ $collection->name }}
                                        </div>
                                        <button 
                                            wire:click="removeFromCollection({{ $collection->id }})"
                                            wire:loading.attr="disabled"
                                            class="text-red-600 hover:text-red-800 text-xs font-medium disabled:opacity-50"
                                            title="Xóa khỏi bộ sưu tập"
                                        >
                                            <span wire:loading.remove>Xóa</span>
                                            <span wire:loading>Đang xóa...</span>
                                        </button>
                                    </li>
                                @endforeach
                                </ul>
                            </div>
                        @endif
                    </div>

                    <div class="flex gap-2">
                        <button 
                            wire:click="addToCollection"
                            wire:loading.attr="disabled"
                            class="flex-1 bg-orange-500 hover:bg-orange-600 text-white px-4 py-2 rounded-lg font-semibold transition disabled:opacity-50"
                        >
                            <span wire:loading.remove>Thêm vào bộ sưu tập</span>
                            <span wire:loading>Đang thêm...</span>
                        </button>
                        <button 
                            wire:click="openCreateModal"
                            class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg font-semibold transition"
                        >
                            Tạo mới
                        </button>
                    </div>
                @else
                    <div class="text-center py-6">
                        <x-heroicon-o-rectangle-stack class="w-16 h-16 text-gray-300 mx-auto mb-4" />
                        <p class="text-gray-500 mb-4">Bạn chưa có bộ sưu tập nào</p>
                        <p class="text-sm text-gray-400 mb-4">Tạo bộ sưu tập để tổ chức công thức yêu thích của bạn</p>
                        <button 
                            wire:click="openCreateModal"
                            class="bg-orange-500 hover:bg-orange-600 text-white px-6 py-2 rounded-lg font-semibold transition"
                        >
                            Tạo bộ sưu tập đầu tiên
                        </button>
                    </div>
                @endif
            </div>
        </div>
    @endif

    <!-- Modal tạo bộ sưu tập mới -->
    @if($showCreateModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50" wire:click="closeCreateModal">
            <div class="bg-white rounded-xl shadow-lg p-6 w-96 max-w-full mx-4" wire:click.stop>
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-bold text-gray-900">Tạo bộ sưu tập mới</h3>
                    <button wire:click="closeCreateModal" class="text-gray-400 hover:text-gray-600">
                        <x-heroicon-o-x-mark class="w-6 h-6" />
                    </button>
                </div>

                <form wire:submit.prevent="createCollection">
                    <div class="mb-4">
                        <label for="newCollectionName" class="block text-sm font-medium text-gray-700 mb-2">
                            Tên bộ sưu tập <span class="text-red-500">*</span>
                        </label>
                        <input 
                            type="text" 
                            id="newCollectionName"
                            wire:model="newCollectionName"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500"
                            placeholder="Nhập tên bộ sưu tập"
                        >
                        @error('newCollectionName')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="mb-4">
                        <label for="newCollectionDescription" class="block text-sm font-medium text-gray-700 mb-2">
                            Mô tả (tùy chọn)
                        </label>
                        <textarea 
                            id="newCollectionDescription"
                            wire:model="newCollectionDescription"
                            rows="3"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500"
                            placeholder="Mô tả về bộ sưu tập của bạn"
                        ></textarea>
                        @error('newCollectionDescription')
                            <p class="text-red-500 text-sm mt-1">{{ $error->first() }}</p>
                        @enderror
                    </div>

                    <div class="mb-6">
                        <label class="flex items-center">
                            <input 
                                type="checkbox" 
                                wire:model="newCollectionIsPublic"
                                class="w-4 h-4 text-orange-600 bg-gray-100 border-gray-300 rounded focus:ring-orange-500 focus:ring-2"
                            >
                            <span class="ml-2 text-sm text-gray-700">Công khai bộ sưu tập</span>
                        </label>
                        <p class="text-xs text-gray-500 mt-1">Bộ sưu tập công khai sẽ hiển thị cho tất cả mọi người</p>
                    </div>

                    <div class="flex gap-2">
                        <button 
                            type="submit"
                            wire:loading.attr="disabled"
                            class="flex-1 bg-orange-500 hover:bg-orange-600 text-white px-4 py-2 rounded-lg font-semibold transition disabled:opacity-50"
                        >
                            <span wire:loading.remove>Tạo bộ sưu tập</span>
                            <span wire:loading>Đang tạo...</span>
                        </button>
                        <button 
                            type="button"
                            wire:click="closeCreateModal"
                            class="bg-gray-300 hover:bg-gray-400 text-gray-700 px-4 py-2 rounded-lg font-semibold transition"
                        >
                            Hủy
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div> 