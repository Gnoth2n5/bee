@extends('layouts.admin')

@section('content')
<div class="bg-white shadow-sm rounded-lg">
    <div class="px-6 py-4 border-b border-gray-200">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Công thức chờ duyệt</h1>
                <p class="text-gray-600 mt-1">Quản lý các công thức đang chờ phê duyệt</p>
            </div>
            <div class="flex items-center space-x-3">
                <span class="text-sm text-gray-500">
                    {{ $recipes->total() }} công thức chờ duyệt
                </span>
            </div>
        </div>
    </div>

    <div class="p-6">
        @if($recipes->count() > 0)
            <div class="space-y-6">
                @foreach($recipes as $recipe)
                    <div class="bg-gray-50 rounded-lg p-6 border border-gray-200">
                        <div class="flex items-start justify-between">
                            <div class="flex-1">
                                <div class="flex items-center space-x-3 mb-3">
                                    <h3 class="text-lg font-semibold text-gray-900">{{ $recipe->title }}</h3>
                                    <span class="px-2 py-1 text-xs font-medium text-white bg-yellow-500 rounded-full">
                                        Chờ duyệt
                                    </span>
                                </div>
                                
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                                    <div>
                                        <p class="text-sm text-gray-600 mb-2">
                                            <strong>Tác giả:</strong> {{ $recipe->user->name }}
                                        </p>
                                        <p class="text-sm text-gray-600 mb-2">
                                            <strong>Ngày tạo:</strong> {{ $recipe->created_at->format('d/m/Y H:i') }}
                                        </p>
                                        <p class="text-sm text-gray-600 mb-2">
                                            <strong>Độ khó:</strong> {{ $recipe->difficulty }}
                                        </p>
                                        <p class="text-sm text-gray-600 mb-2">
                                            <strong>Thời gian nấu:</strong> {{ $recipe->cooking_time }} phút
                                        </p>
                                    </div>
                                    <div>
                                        <p class="text-sm text-gray-600 mb-2">
                                            <strong>Danh mục:</strong>
                                            @foreach($recipe->categories as $category)
                                                <span class="inline-block px-2 py-1 text-xs bg-orange-100 text-orange-700 rounded mr-1">
                                                    {{ $category->name }}
                                                </span>
                                            @endforeach
                                        </p>
                                        <p class="text-sm text-gray-600 mb-2">
                                            <strong>Tags:</strong>
                                            @foreach($recipe->tags as $tag)
                                                <span class="inline-block px-2 py-1 text-xs bg-gray-100 text-gray-700 rounded mr-1">
                                                    #{{ $tag->name }}
                                                </span>
                                            @endforeach
                                        </p>
                                        <p class="text-sm text-gray-600 mb-2">
                                            <strong>Khẩu phần:</strong> {{ $recipe->servings }} người
                                        </p>
                                    </div>
                                </div>

                                @if($recipe->description)
                                    <div class="mb-4">
                                        <p class="text-sm text-gray-700">
                                            <strong>Mô tả:</strong> {{ Str::limit($recipe->description, 200) }}
                                        </p>
                                    </div>
                                @endif

                                @if($recipe->ingredients && count($recipe->ingredients) > 0)
                                    <div class="mb-4">
                                        <p class="text-sm font-medium text-gray-900 mb-2">Nguyên liệu:</p>
                                        <ul class="text-sm text-gray-600 space-y-1">
                                            @foreach(array_slice($recipe->ingredients, 0, 5) as $ingredient)
                                                <li>• {{ $ingredient['name'] ?? 'N/A' }}: {{ $ingredient['amount'] ?? '' }} {{ $ingredient['unit'] ?? '' }}</li>
                                            @endforeach
                                            @if(count($recipe->ingredients) > 5)
                                                <li class="text-gray-500">... và {{ count($recipe->ingredients) - 5 }} nguyên liệu khác</li>
                                            @endif
                                        </ul>
                                    </div>
                                @endif

                                @if($recipe->instructions && count($recipe->instructions) > 0)
                                    <div class="mb-4">
                                        <p class="text-sm font-medium text-gray-900 mb-2">Cách làm:</p>
                                        <div class="text-sm text-gray-600">
                                            @foreach(array_slice($recipe->instructions, 0, 3) as $instruction)
                                                <p class="mb-1">{{ $instruction['step'] ?? '' }}. {{ $instruction['instruction'] ?? 'N/A' }}</p>
                                            @endforeach
                                            @if(count($recipe->instructions) > 3)
                                                <p class="text-gray-500">... và {{ count($recipe->instructions) - 3 }} bước khác</p>
                                            @endif
                                        </div>
                                    </div>
                                @endif
                            </div>

                            <div class="flex flex-col space-y-2 ml-6">
                                <button onclick="approveRecipe({{ $recipe->id }})" 
                                        class="px-4 py-2 bg-green-600 text-white text-sm font-medium rounded-md hover:bg-green-700 transition-colors">
                                    ✅ Duyệt
                                </button>
                                <button onclick="rejectRecipe({{ $recipe->id }})" 
                                        class="px-4 py-2 bg-red-600 text-white text-sm font-medium rounded-md hover:bg-red-700 transition-colors">
                                    ❌ Từ chối
                                </button>
                                <a href="{{ route('recipes.show', $recipe) }}" 
                                   class="px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-md hover:bg-blue-700 transition-colors text-center">
                                    👁️ Xem chi tiết
                                </a>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Pagination -->
            <div class="mt-6">
                {{ $recipes->links() }}
            </div>
        @else
            <div class="text-center py-12">
                <svg class="mx-auto h-16 w-16 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                <h3 class="mt-4 text-lg font-medium text-gray-900">Không có công thức nào chờ duyệt</h3>
                <p class="mt-2 text-sm text-gray-500">Tất cả công thức đã được xử lý!</p>
            </div>
        @endif
    </div>
</div>

<!-- Reject Modal -->
<div id="rejectModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50 hidden">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Từ chối công thức</h3>
            <form id="rejectForm">
                <input type="hidden" id="recipeId" name="recipe_id">
                <div class="mb-4">
                    <label for="rejection_reason" class="block text-sm font-medium text-gray-700 mb-2">
                        Lý do từ chối <span class="text-red-500">*</span>
                    </label>
                    <textarea id="rejection_reason" name="rejection_reason" rows="4" 
                              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-red-500"
                              placeholder="Nhập lý do từ chối công thức này..."></textarea>
                </div>
                <div class="flex space-x-3">
                    <button type="submit" 
                            class="flex-1 px-4 py-2 bg-red-600 text-white text-sm font-medium rounded-md hover:bg-red-700 transition-colors">
                        Từ chối
                    </button>
                    <button type="button" onclick="closeRejectModal()" 
                            class="flex-1 px-4 py-2 bg-gray-300 text-gray-700 text-sm font-medium rounded-md hover:bg-gray-400 transition-colors">
                        Hủy
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function approveRecipe(recipeId) {
    if (confirm('Bạn có chắc muốn duyệt công thức này?')) {
        fetch(`/admin/recipes/${recipeId}/approve`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Content-Type': 'application/json',
            },
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showFlashMessage(data.message, 'success');
                setTimeout(() => {
                    window.location.reload();
                }, 1000);
            } else {
                showFlashMessage('Có lỗi xảy ra khi duyệt công thức', 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showFlashMessage('Có lỗi xảy ra khi duyệt công thức', 'error');
        });
    }
}

function rejectRecipe(recipeId) {
    document.getElementById('recipeId').value = recipeId;
    document.getElementById('rejectModal').classList.remove('hidden');
}

function closeRejectModal() {
    document.getElementById('rejectModal').classList.add('hidden');
    document.getElementById('rejection_reason').value = '';
}

document.getElementById('rejectForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const recipeId = document.getElementById('recipeId').value;
    const rejectionReason = document.getElementById('rejection_reason').value;
    
    if (!rejectionReason.trim()) {
        showFlashMessage('Vui lòng nhập lý do từ chối', 'error');
        return;
    }
    
    fetch(`/admin/recipes/${recipeId}/reject`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({
            rejection_reason: rejectionReason
        }),
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showFlashMessage(data.message, 'success');
            closeRejectModal();
            setTimeout(() => {
                window.location.reload();
            }, 1000);
        } else {
            showFlashMessage('Có lỗi xảy ra khi từ chối công thức', 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showFlashMessage('Có lỗi xảy ra khi từ chối công thức', 'error');
    });
});
</script>
@endsection

