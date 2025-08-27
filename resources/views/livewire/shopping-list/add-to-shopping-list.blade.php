<div class="bg-white rounded-lg shadow p-4">
    <h3 class="text-lg font-semibold text-gray-900 mb-4">Thêm vào Shopping List</h3>
    
    @if($shoppingLists->count() > 0)
        <div class="space-y-3">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Chọn Shopping List</label>
                <select wire:model="selectedShoppingListId" 
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-orange-500">
                    <option value="">Chọn shopping list...</option>
                    @foreach($shoppingLists as $list)
                        <option value="{{ $list->id }}">{{ $list->name }}</option>
                    @endforeach
                </select>
            </div>
            
            <button wire:click="addToShoppingList" 
                    class="w-full px-4 py-2 bg-orange-600 text-white text-sm font-medium rounded-md hover:bg-orange-700 transition-colors">
                Thêm vào Shopping List
            </button>
        </div>
    @endif
    
    <div class="mt-4 pt-4 border-t border-gray-200">
        <button wire:click="$set('showCreateForm', true)" 
                class="w-full px-4 py-2 border border-orange-600 text-orange-600 text-sm font-medium rounded-md hover:bg-orange-50 transition-colors">
            Tạo Shopping List mới
        </button>
    </div>
    
    @if($showCreateForm)
        <div class="mt-4 p-4 bg-gray-50 rounded-lg">
            <h4 class="text-sm font-medium text-gray-900 mb-3">Tạo Shopping List mới</h4>
            <div class="space-y-3">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Tên Shopping List</label>
                    <input type="text" wire:model="newShoppingListName" 
                           placeholder="Nhập tên shopping list..."
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-orange-500">
                </div>
                <div class="flex space-x-2">
                    <button wire:click="createAndAdd" 
                            class="flex-1 px-3 py-2 bg-orange-600 text-white text-sm font-medium rounded-md hover:bg-orange-700 transition-colors">
                        Tạo và thêm
                    </button>
                    <button wire:click="$set('showCreateForm', false)" 
                            class="px-3 py-2 bg-gray-300 text-gray-700 text-sm font-medium rounded-md hover:bg-gray-400 transition-colors">
                        Hủy
                    </button>
                </div>
            </div>
        </div>
    @endif
</div>
