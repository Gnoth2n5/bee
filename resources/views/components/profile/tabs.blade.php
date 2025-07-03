@props(['activeTab', 'isEditing'])

<div class="border-t border-gray-200">
    <nav class="flex space-x-8 px-6" aria-label="Tabs">
        <button wire:click="setActiveTab('recipes')" class="py-4 px-1 border-b-2 font-medium text-sm {{ $activeTab === 'recipes' ? 'border-orange-500 text-orange-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
            Công thức
        </button>
        <button wire:click="setActiveTab('collections')" class="py-4 px-1 border-b-2 font-medium text-sm {{ $activeTab === 'collections' ? 'border-orange-500 text-orange-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
            Bộ sưu tập
        </button>
        <button wire:click="setActiveTab('favorites')" class="py-4 px-1 border-b-2 font-medium text-sm {{ $activeTab === 'favorites' ? 'border-orange-500 text-orange-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
            Yêu thích
        </button>
        @if($isEditing)
            <button wire:click="setActiveTab('settings')" class="py-4 px-1 border-b-2 font-medium text-sm {{ $activeTab === 'settings' ? 'border-orange-500 text-orange-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                Cài đặt
            </button>
        @endif
    </nav>
</div> 