<div class="p-6">
    <div class="mb-6">
        <h2 class="text-2xl font-bold text-gray-900 mb-4">Kiểm tra Tự động Phê duyệt</h2>
        
        <div class="flex space-x-4 mb-6">
            <button wire:click="runAutoModeration" 
                    class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                Chạy Tự động Phê duyệt
            </button>
        </div>
    </div>

    <!-- Danh sách công thức đang chờ duyệt -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200">
        <div class="p-6 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900">Công thức đang chờ duyệt ({{ $pendingRecipes->total() }})</h3>
        </div>

        @if($pendingRecipes->count() > 0)
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Công thức
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Tác giả
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Ngày tạo
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Thao tác
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($pendingRecipes as $recipe)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="flex-shrink-0 h-10 w-10">
                                            @if($recipe->featured_image)
                                                <img class="h-10 w-10 rounded-lg object-cover" 
                                                     src="{{ Storage::url($recipe->featured_image) }}" 
                                                     alt="{{ $recipe->title }}">
                                            @else
                                                <div class="h-10 w-10 rounded-lg bg-gray-200 flex items-center justify-center">
                                                    <svg class="h-6 w-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                                    </svg>
                                                </div>
                                            @endif
                                        </div>
                                        <div class="ml-4">
                                            <div class="text-sm font-medium text-gray-900">
                                                {{ $recipe->title }}
                                            </div>
                                            <div class="text-sm text-gray-500">
                                                {{ Str::limit($recipe->description, 50) }}
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ $recipe->user->name }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ $recipe->created_at->format('d/m/Y H:i') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <button wire:click="testRecipe({{ $recipe->id }})" 
                                            class="text-indigo-600 hover:text-indigo-900">
                                        Kiểm tra
                                    </button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="px-6 py-3 border-t border-gray-200">
                {{ $pendingRecipes->links() }}
            </div>
        @else
            <div class="p-6 text-center text-gray-500">
                Không có công thức nào đang chờ duyệt.
            </div>
        @endif
    </div>

    <!-- Kết quả kiểm tra -->
    @if($showResults && $selectedRecipe)
        <div class="mt-8 bg-white rounded-lg shadow-sm border border-gray-200">
            <div class="p-6 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900">
                    Kết quả kiểm tra: {{ $selectedRecipe->title }}
                </h3>
            </div>

            <div class="p-6">
                @foreach($moderationResults as $result)
                    <div class="mb-4 p-4 rounded-lg border {{ $result['violated'] ? 'border-red-200 bg-red-50' : 'border-green-200 bg-green-50' }}">
                        <div class="flex items-center justify-between">
                            <div>
                                <h4 class="font-medium {{ $result['violated'] ? 'text-red-800' : 'text-green-800' }}">
                                    {{ $result['name'] ?? $result['rule']?->name ?? 'Kiểm tra' }}
                                </h4>
                                @if($result['violated'])
                                    <div class="mt-2 text-sm text-red-700">
                                        @if(isset($result['violation_details']['field']))
                                            <p><strong>Trường:</strong> {{ $result['violation_details']['field'] }}</p>
                                        @endif
                                        @if(isset($result['violation_details']['content']))
                                            <p><strong>Nội dung:</strong> {{ $result['violation_details']['content'] }}</p>
                                        @endif
                                        @if(isset($result['violation_details']['message']))
                                            <p>{{ $result['violation_details']['message'] }}</p>
                                        @endif
                                    </div>
                                @else
                                    <div class="mt-2 text-sm text-green-700">
                                        ✅ Không vi phạm
                                    </div>
                                @endif
                            </div>
                            <div class="flex-shrink-0">
                                @if($result['violated'])
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                        Vi phạm
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                        OK
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif
</div> 