@props(['filters', 'categories'])

@if(($filters['search'] ?? '') || ($filters['category'] ?? '') || ($filters['difficulty'] ?? '') || ($filters['cookingTime'] ?? '') || !empty($filters['selectedTags'] ?? []) || ($filters['minRating'] ?? '') || ($filters['maxCalories'] ?? '') || ($filters['servings'] ?? ''))
    <div class="flex items-center space-x-2">
        <span class="text-sm text-gray-500">Bộ lọc:</span>
        @if($filters['search'] ?? '')
            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-orange-100 text-orange-800">
                "{{ $filters['search'] }}"
                <button wire:click="$set('search', '')" class="ml-1 hover:text-orange-600">×</button>
            </span>
        @endif
        @if($filters['category'] ?? '')
            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                {{ $categories->firstWhere('slug', $filters['category'])?->name ?? $filters['category'] }}
                <button wire:click="$set('category', '')" class="ml-1 hover:text-blue-600">×</button>
            </span>
        @endif
        @if($filters['difficulty'] ?? '')
            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                {{ ucfirst($filters['difficulty']) }}
                <button wire:click="$set('difficulty', '')" class="ml-1 hover:text-green-600">×</button>
            </span>
        @endif
        @if($filters['cookingTime'] ?? '')
            @php
                $timeLabels = [
                    'quick' => 'Dưới 30 phút',
                    'medium' => '30-60 phút',
                    'long' => 'Trên 60 phút'
                ];
            @endphp
            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-purple-100 text-purple-800">
                {{ $timeLabels[$filters['cookingTime']] ?? $filters['cookingTime'] }}
                <button wire:click="$set('cookingTime', '')" class="ml-1 hover:text-purple-600">×</button>
            </span>
        @endif
        @if($filters['minRating'] ?? '')
            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                {{ $filters['minRating'] }}+ sao
                <button wire:click="$set('minRating', '')" class="ml-1 hover:text-yellow-600">×</button>
            </span>
        @endif
        @if($filters['maxCalories'] ?? '')
            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800">
                Dưới {{ $filters['maxCalories'] }} cal
                <button wire:click="$set('maxCalories', '')" class="ml-1 hover:text-red-600">×</button>
            </span>
        @endif
        @if($filters['servings'] ?? '')
            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-indigo-100 text-indigo-800">
                {{ $filters['servings'] }} người
                <button wire:click="$set('servings', '')" class="ml-1 hover:text-indigo-600">×</button>
            </span>
        @endif
    </div>
@endif 