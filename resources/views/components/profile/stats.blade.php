@props(['recipesCount', 'collectionsCount', 'favoritesCount'])

<div class="border-t border-gray-200 bg-gray-50">
    <div class="px-6 py-4">
        <div class="flex items-center justify-around text-center">
            <div class="flex-1">
                <div class="text-2xl font-bold text-gray-900">{{ $recipesCount }}</div>
                <div class="text-sm text-gray-600">Công thức</div>
            </div>
            <div class="flex-1 border-l border-r border-gray-200">
                <div class="text-2xl font-bold text-gray-900">{{ $collectionsCount }}</div>
                <div class="text-sm text-gray-600">Bộ sưu tập</div>
            </div>
            <div class="flex-1">
                <div class="text-2xl font-bold text-gray-900">{{ $favoritesCount }}</div>
                <div class="text-sm text-gray-600">Yêu thích</div>
            </div>
        </div>
    </div>
</div> 