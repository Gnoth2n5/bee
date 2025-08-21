@if ($paginator->hasPages())
    <div class="flex items-center justify-between">
        {{-- Previous Page Link --}}
        <div>
            @if ($paginator->onFirstPage())
                <span class="px-3 py-2 text-sm text-gray-500 bg-white border border-gray-300 rounded-md cursor-default">
                    Trước
                </span>
            @else
                <button wire:click="previousPage" class="px-3 py-2 text-sm text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50">
                    Trước
                </button>
            @endif
        </div>

        {{-- Page Numbers --}}
        <div class="flex space-x-1">
            @php
                $currentPage = $paginator->currentPage();
                $lastPage = $paginator->lastPage();
                $start = max(1, $currentPage - 1);
                $end = min($lastPage, $currentPage + 1);
            @endphp

            {{-- First Page --}}
            @if ($start > 1)
                <button wire:click="gotoPage(1)" class="px-3 py-2 text-sm text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50">
                    1
                </button>
                @if ($start > 2)
                    <span class="px-3 py-2 text-sm text-gray-700 bg-white border border-gray-300 rounded-md">
                        ...
                    </span>
                @endif
            @endif

            {{-- Page Numbers --}}
            @for ($page = $start; $page <= $end; $page++)
                @if ($page == $currentPage)
                    <span class="px-3 py-2 text-sm text-white bg-orange-600 border border-orange-600 rounded-md">
                        {{ $page }}
                    </span>
                @else
                    <button wire:click="gotoPage({{ $page }})" class="px-3 py-2 text-sm text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50">
                        {{ $page }}
                    </button>
                @endif
            @endfor

            {{-- Last Page --}}
            @if ($end < $lastPage)
                @if ($end < $lastPage - 1)
                    <span class="px-3 py-2 text-sm text-gray-700 bg-white border border-gray-300 rounded-md">
                        ...
                    </span>
                @endif
                <button wire:click="gotoPage({{ $lastPage }})" class="px-3 py-2 text-sm text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50">
                    {{ $lastPage }}
                </button>
            @endif
        </div>

        {{-- Next Page Link --}}
        <div>
            @if ($paginator->hasMorePages())
                <button wire:click="nextPage" class="px-3 py-2 text-sm text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50">
                    Sau
                </button>
            @else
                <span class="px-3 py-2 text-sm text-gray-500 bg-white border border-gray-300 rounded-md cursor-default">
                    Sau
                </span>
            @endif
        </div>
    </div>
@endif
