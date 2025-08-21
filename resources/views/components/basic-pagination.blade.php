@if ($paginator->hasPages())
    <div class="flex items-center justify-center space-x-2">
        {{-- Previous Page --}}
        @if ($paginator->onFirstPage())
            <span class="px-3 py-2 text-sm text-gray-400 bg-gray-100 border border-gray-300 rounded cursor-not-allowed">
                Trước
            </span>
        @else
            <button wire:click="previousPage" class="px-3 py-2 text-sm text-gray-700 bg-white border border-gray-300 rounded hover:bg-gray-50">
                Trước
            </button>
        @endif

        {{-- Current Page Info --}}
        <span class="px-3 py-2 text-sm text-gray-700">
            Trang {{ $paginator->currentPage() }} / {{ $paginator->lastPage() }}
        </span>

        {{-- Next Page --}}
        @if ($paginator->hasMorePages())
            <button wire:click="nextPage" class="px-3 py-2 text-sm text-gray-700 bg-white border border-gray-300 rounded hover:bg-gray-50">
                Sau
            </button>
        @else
            <span class="px-3 py-2 text-sm text-gray-400 bg-gray-100 border border-gray-300 rounded cursor-not-allowed">
                Sau
            </span>
        @endif
    </div>
@endif
