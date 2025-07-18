@props(['title' => 'Không tìm thấy kết quả', 'description' => 'Thử thay đổi bộ lọc hoặc tìm kiếm với từ khóa khác.', 'actionText' => 'Xóa bộ lọc', 'actionMethod' => 'clearFilters'])

<div class="bg-white rounded-lg shadow-sm border border-gray-200 p-12 text-center">
    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
    </svg>
    <h3 class="mt-2 text-sm font-medium text-gray-900">{{ $title }}</h3>
    <p class="mt-1 text-sm text-gray-500">
        {{ $description }}
    </p>
    <div class="mt-6">
        <button 
            wire:click="{{ $actionMethod }}"
            class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-orange-600 hover:bg-orange-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-orange-500"
        >
            {{ $actionText }}
        </button>
    </div>
</div> 