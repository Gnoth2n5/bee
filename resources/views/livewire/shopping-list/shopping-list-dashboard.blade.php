<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- Header -->
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900 mb-2">Shopping Lists Dashboard</h1>
        <p class="text-gray-600">Quản lý tất cả danh sách mua sắm của bạn</p>
    </div>

    <!-- Quick Actions -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <!-- Create New Shopping List -->
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center mb-4">
                <div class="flex-shrink-0">
                    <svg class="h-8 w-8 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                    </svg>
                </div>
                <div class="ml-4">
                    <h3 class="text-lg font-medium text-gray-900">Tạo Shopping List Mới</h3>
                    <p class="text-sm text-gray-500">Tạo danh sách mua sắm trống</p>
                </div>
            </div>
            <button wire:click="$set('showCreateForm', true)" 
                    class="w-full bg-orange-600 text-white px-4 py-2 rounded-md hover:bg-orange-700 transition-colors">
                Tạo mới
            </button>
        </div>

        <!-- Generate from Meal Plan -->
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center mb-4">
                <div class="flex-shrink-0">
                    <svg class="h-8 w-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                </div>
                <div class="ml-4">
                    <h3 class="text-lg font-medium text-gray-900">Từ Meal Plan</h3>
                    <p class="text-sm text-gray-500">Tạo từ kế hoạch bữa ăn</p>
                </div>
            </div>
            <select wire:model="selectedMealPlanId" class="w-full mb-3 px-3 py-2 border border-gray-300 rounded-md">
                <option value="">Chọn Meal Plan</option>
                @foreach($mealPlans as $mealPlan)
                    <option value="{{ $mealPlan->id }}">{{ $mealPlan->name }}</option>
                @endforeach
            </select>
            <button wire:click="generateFromMealPlan" 
                    class="w-full bg-green-600 text-white px-4 py-2 rounded-md hover:bg-green-700 transition-colors">
                Tạo từ Meal Plan
            </button>
        </div>

        <!-- Generate from Recipe -->
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center mb-4">
                <div class="flex-shrink-0">
                    <svg class="h-8 w-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                </div>
                <div class="ml-4">
                    <h3 class="text-lg font-medium text-gray-900">Từ Recipe</h3>
                    <p class="text-sm text-gray-500">Tạo từ công thức nấu ăn</p>
                </div>
            </div>
            <select wire:model="selectedRecipeId" class="w-full mb-3 px-3 py-2 border border-gray-300 rounded-md">
                <option value="">Chọn Recipe</option>
                @foreach($recipes as $recipe)
                    <option value="{{ $recipe->id }}">{{ $recipe->title }}</option>
                @endforeach
            </select>
            <button wire:click="generateFromRecipe" 
                    class="w-full bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 transition-colors">
                Tạo từ Recipe
            </button>
        </div>
    </div>

    <!-- Create Form Modal -->
    @if($showCreateForm)
        <div class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
            <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
                <div class="mt-3">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Tạo Shopping List Mới</h3>
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Tên Shopping List <span class="text-red-500">*</span></label>
                            <input type="text" wire:model="newShoppingList.name" 
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-orange-500"
                                   placeholder="Nhập tên shopping list">
                            @error('newShoppingList.name') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Mô tả (tùy chọn)</label>
                            <textarea wire:model="newShoppingList.description" rows="3"
                                      class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-orange-500"
                                      placeholder="Mô tả shopping list"></textarea>
                        </div>
                        <div class="flex space-x-3">
                            <button wire:click="createShoppingList" 
                                    class="flex-1 bg-orange-600 text-white px-4 py-2 rounded-md hover:bg-orange-700 transition-colors">
                                Tạo
                            </button>
                            <button wire:click="$set('showCreateForm', false)" 
                                    class="flex-1 bg-gray-300 text-gray-700 px-4 py-2 rounded-md hover:bg-gray-400 transition-colors">
                                Hủy
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Shopping Lists Grid -->
    <div class="bg-white rounded-lg shadow">
        <div class="px-6 py-4 border-b border-gray-200">
            <h2 class="text-xl font-semibold text-gray-900">Tất cả Shopping Lists</h2>
            <p class="text-sm text-gray-600 mt-1">{{ $shoppingLists->count() }} shopping lists</p>
        </div>

        <div class="p-6">
            @if($shoppingLists->count() > 0)
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach($shoppingLists as $shoppingList)
                        <div class="bg-gray-50 rounded-lg p-6 border border-gray-200 hover:border-orange-300 transition-colors">
                            <div class="flex items-start justify-between mb-4">
                                <div class="flex-1">
                                    <h3 class="text-lg font-medium text-gray-900 mb-1">{{ $shoppingList->name }}</h3>
                                    @if($shoppingList->description)
                                        <p class="text-sm text-gray-600 mb-2">{{ Str::limit($shoppingList->description, 80) }}</p>
                                    @endif
                                    <div class="flex items-center text-sm text-gray-500">
                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                                        </svg>
                                        {{ $shoppingList->items->count() }} items
                                    </div>
                                </div>
                                <div class="flex space-x-2">
                                    <a href="{{ route('shopping-lists.index') }}?list={{ $shoppingList->id }}" 
                                       class="text-orange-600 hover:text-orange-700">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                        </svg>
                                    </a>
                                    <button wire:click="deleteShoppingList({{ $shoppingList->id }})" 
                                            onclick="return confirm('Bạn có chắc muốn xóa shopping list này?')"
                                            class="text-red-600 hover:text-red-700">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                        </svg>
                                    </button>
                                </div>
                            </div>

                            <!-- Progress Bar -->
                            @if($shoppingList->items->count() > 0)
                                <div class="mb-4">
                                    <div class="flex justify-between text-sm text-gray-600 mb-1">
                                        <span>Tiến độ</span>
                                        <span>{{ $shoppingList->completion_percentage }}%</span>
                                    </div>
                                    <div class="w-full bg-gray-200 rounded-full h-2">
                                        <div class="bg-orange-600 h-2 rounded-full" style="width: {{ $shoppingList->completion_percentage }}%"></div>
                                    </div>
                                </div>
                            @endif

                            <!-- Status -->
                            <div class="flex items-center justify-between">
                                <div class="text-sm text-gray-500">
                                    Tạo: {{ $shoppingList->created_at->format('d/m/Y') }}
                                </div>
                                <div class="flex items-center">
                                    @if($shoppingList->isCompleted())
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                            Hoàn thành
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                            Đang thực hiện
                                        </span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-12">
                    <svg class="mx-auto h-16 w-16 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                    </svg>
                    <h3 class="mt-4 text-lg font-medium text-gray-900">Chưa có shopping list nào</h3>
                    <p class="mt-2 text-sm text-gray-500">Bắt đầu tạo shopping list đầu tiên của bạn!</p>
                    <div class="mt-6">
                        <button wire:click="$set('showCreateForm', true)" 
                                class="inline-flex items-center px-4 py-2 bg-orange-600 text-white text-sm font-medium rounded-md hover:bg-orange-700 transition-colors">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                            </svg>
                            Tạo Shopping List đầu tiên
                        </button>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
