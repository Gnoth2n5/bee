<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900 mb-2">Quản lý Shopping List</h1>
        <p class="text-gray-600">Tạo và quản lý danh sách mua sắm của bạn</p>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-4 gap-6">
        <!-- Sidebar - Shopping Lists -->
        <div class="lg:col-span-1">
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-lg font-semibold text-gray-900">Shopping Lists</h2>
                    <button wire:click="$set('showCreateForm', true)" 
                            class="inline-flex items-center px-3 py-1.5 bg-orange-600 text-white text-sm font-medium rounded-md hover:bg-orange-700 transition-colors">
                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                        </svg>
                        Tạo mới
                    </button>
                </div>

                <!-- Create Form -->
                @if($showCreateForm)
                    <div class="mb-4 p-4 bg-gray-50 rounded-lg">
                        <h3 class="text-sm font-medium text-gray-900 mb-3">Tạo Shopping List mới</h3>
                        <div class="space-y-3">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Tên <span class="text-red-500">*</span></label>
                                <input type="text" wire:model="newShoppingList.name" 
                                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-orange-500">
                                @error('newShoppingList.name') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Mô tả</label>
                                <textarea wire:model="newShoppingList.description" rows="2"
                                          class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-orange-500"></textarea>
                            </div>
                            <div class="flex space-x-2">
                                <button wire:click="createShoppingList" 
                                        class="flex-1 px-3 py-2 bg-orange-600 text-white text-sm font-medium rounded-md hover:bg-orange-700 transition-colors">
                                    Tạo
                                </button>
                                <button wire:click="$set('showCreateForm', false)" 
                                        class="px-3 py-2 bg-gray-300 text-gray-700 text-sm font-medium rounded-md hover:bg-gray-400 transition-colors">
                                    Hủy
                                </button>
                            </div>
                        </div>
                    </div>
                @endif

                <!-- Shopping Lists -->
                <div class="space-y-2">
                    @forelse($shoppingLists as $list)
                        <div wire:click="selectShoppingList({{ $list->id }})" 
                             class="p-3 rounded-lg border cursor-pointer transition-colors {{ $selectedShoppingList && $selectedShoppingList->id == $list->id ? 'border-orange-500 bg-orange-50' : 'border-gray-200 hover:border-gray-300' }}">
                            <div class="flex items-center justify-between">
                                <div class="flex-1">
                                    <h3 class="text-sm font-medium text-gray-900">{{ $list->name }}</h3>
                                    <p class="text-xs text-gray-500">{{ $list->items->count() }} items</p>
                                    @if($list->description)
                                        <p class="text-xs text-gray-400 mt-1">{{ Str::limit($list->description, 50) }}</p>
                                    @endif
                                </div>
                                <div class="text-xs text-gray-400">
                                    {{ $list->completion_percentage }}%
                                </div>
                            </div>
                            @if($list->items->count() > 0)
                                <div class="mt-2">
                                    <div class="w-full bg-gray-200 rounded-full h-1.5">
                                        <div class="bg-orange-600 h-1.5 rounded-full" style="width: {{ $list->completion_percentage }}%"></div>
                                    </div>
                                </div>
                            @endif
                        </div>
                    @empty
                        <div class="text-center py-8">
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                            </svg>
                            <p class="mt-2 text-sm text-gray-500">Chưa có shopping list nào</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- Main Content -->
        <div class="lg:col-span-3">
            @if($selectedShoppingList)
                <div class="bg-white rounded-lg shadow">
                    <!-- Header -->
                    <div class="px-6 py-4 border-b border-gray-200">
                        <div class="flex items-center justify-between">
                            <div>
                                <h2 class="text-xl font-semibold text-gray-900">{{ $selectedShoppingList->name }}</h2>
                                @if($selectedShoppingList->description)
                                    <p class="text-sm text-gray-600 mt-1">{{ $selectedShoppingList->description }}</p>
                                @endif
                            </div>
                            <div class="flex items-center space-x-2">
                                <button wire:click="clearCheckedItems" 
                                        class="px-3 py-2 text-sm text-gray-600 hover:text-gray-900 transition-colors">
                                    Xóa đã check
                                </button>
                                <button wire:click="markAsCompleted" 
                                        class="px-4 py-2 bg-green-600 text-white text-sm font-medium rounded-md hover:bg-green-700 transition-colors">
                                    Hoàn thành
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Add Item Form -->
                    <div class="px-6 py-4 border-b border-gray-200">
                        <div class="grid grid-cols-1 md:grid-cols-5 gap-3">
                            <div class="md:col-span-2">
                                <input type="text" wire:model="newItem.ingredient_name" placeholder="Tên nguyên liệu *"
                                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-orange-500">
                                @error('newItem.ingredient_name') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            </div>
                            <div>
                                <input type="number" wire:model="newItem.amount" placeholder="Số lượng" step="0.01"
                                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-orange-500">
                            </div>
                            <div>
                                <input type="text" wire:model="newItem.unit" placeholder="Đơn vị"
                                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-orange-500">
                            </div>
                            <div>
                                <button wire:click="addItem" 
                                        class="w-full px-4 py-2 bg-orange-600 text-white text-sm font-medium rounded-md hover:bg-orange-700 transition-colors">
                                    Thêm
                                </button>
                            </div>
                        </div>
                        <div class="mt-3 grid grid-cols-1 md:grid-cols-2 gap-3">
                            <div>
                                <select wire:model="newItem.category" 
                                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-orange-500">
                                    <option value="">Chọn danh mục</option>
                                    @foreach($categories as $key => $value)
                                        <option value="{{ $key }}">{{ $value }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <input type="text" wire:model="newItem.notes" placeholder="Ghi chú"
                                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-orange-500">
                            </div>
                        </div>
                    </div>

                    <!-- Items by Category -->
                    <div class="divide-y divide-gray-200">
                        @php
                            $itemsByCategory = $selectedShoppingList->items->groupBy('category');
                        @endphp
                        
                        @forelse($itemsByCategory as $category => $items)
                            <div class="p-6">
                                <h3 class="text-lg font-medium text-gray-900 mb-4">{{ $category }}</h3>
                                <div class="space-y-3">
                                    @foreach($items as $item)
                                        <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                                            <div class="flex items-center space-x-3">
                                                <input type="checkbox" 
                                                       wire:click="toggleItemChecked({{ $item->id }})"
                                                       {{ $item->is_checked ? 'checked' : '' }}
                                                       class="h-4 w-4 text-orange-600 focus:ring-orange-500 border-gray-300 rounded">
                                                <div class="flex-1">
                                                    <div class="flex items-center space-x-2">
                                                        <span class="text-sm font-medium text-gray-900 {{ $item->is_checked ? 'line-through text-gray-500' : '' }}">
                                                            {{ $item->ingredient_name }}
                                                        </span>
                                                        @if($item->formatted_amount)
                                                            <span class="text-sm text-gray-500">({{ $item->formatted_amount }})</span>
                                                        @endif
                                                    </div>
                                                    @if($item->notes)
                                                        <p class="text-xs text-gray-500 mt-1">{{ $item->notes }}</p>
                                                    @endif
                                                </div>
                                            </div>
                                            <button wire:click="deleteItem({{ $item->id }})" 
                                                    class="text-red-500 hover:text-red-700 transition-colors">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                                </svg>
                                            </button>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @empty
                            <div class="p-6 text-center">
                                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                                </svg>
                                <p class="mt-2 text-sm text-gray-500">Chưa có items nào trong shopping list này</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            @else
                <div class="bg-white rounded-lg shadow p-8 text-center">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                    </svg>
                    <h3 class="mt-2 text-sm font-medium text-gray-900">Chọn shopping list</h3>
                    <p class="mt-1 text-sm text-gray-500">Chọn một shopping list từ sidebar để xem chi tiết</p>
                </div>
            @endif
        </div>
    </div>

    <!-- Generate from Meal Plan/Recipe Modal -->
    <div class="mt-8 bg-white rounded-lg shadow p-6">
        <h3 class="text-lg font-medium text-gray-900 mb-4">Tạo Shopping List từ</h3>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- From Meal Plan -->
            <div>
                <h4 class="text-md font-medium text-gray-900 mb-3">Từ Meal Plan</h4>
                <div class="space-y-2">
                    @forelse($mealPlans as $mealPlan)
                        <button wire:click="generateFromMealPlan({{ $mealPlan->id }})" 
                                class="w-full text-left p-3 border border-gray-200 rounded-lg hover:border-orange-300 hover:bg-orange-50 transition-colors">
                            <div class="font-medium text-gray-900">{{ $mealPlan->name }}</div>
                            <div class="text-sm text-gray-500">{{ $mealPlan->week_start->format('d/m/Y') }} - {{ $mealPlan->week_end->format('d/m/Y') }}</div>
                        </button>
                    @empty
                        <p class="text-sm text-gray-500">Chưa có meal plan nào</p>
                    @endforelse
                </div>
            </div>

            <!-- From Recipe -->
            <div>
                <h4 class="text-md font-medium text-gray-900 mb-3">Từ Recipe</h4>
                <div class="space-y-2 max-h-64 overflow-y-auto">
                    @forelse($recipes as $recipe)
                        <button wire:click="generateFromRecipe({{ $recipe->id }})" 
                                class="w-full text-left p-3 border border-gray-200 rounded-lg hover:border-orange-300 hover:bg-orange-50 transition-colors">
                            <div class="font-medium text-gray-900">{{ $recipe->title }}</div>
                            <div class="text-sm text-gray-500">{{ $recipe->cooking_time }} phút • {{ $recipe->difficulty }}</div>
                        </button>
                    @empty
                        <p class="text-sm text-gray-500">Chưa có recipe nào</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>
