@props(['activeTab', 'isEditing'])

<div class="border-t border-orange-200/50 dark:border-orange-800/50 bg-gradient-to-r from-orange-50/30 to-red-50/30 dark:from-orange-900/10 dark:to-red-900/10">
    <nav class="flex space-x-8 px-6" aria-label="Tabs">
        <button wire:click="setActiveTab('recipes')" class="group py-4 px-1 border-b-2 font-medium text-sm transition-all duration-300 {{ $activeTab === 'recipes' ? 'border-orange-500 text-orange-600 dark:text-orange-400' : 'border-transparent text-gray-500 dark:text-gray-400 hover:text-orange-600 dark:hover:text-orange-400 hover:border-orange-300' }}">
            <div class="flex items-center space-x-2">
                <x-heroicon-o-book-open class="w-4 h-4 {{ $activeTab === 'recipes' ? 'text-orange-500' : 'group-hover:text-orange-500' }} transition-colors duration-300" />
                <span>Công thức</span>
            </div>
        </button>
        <button wire:click="setActiveTab('collections')" class="group py-4 px-1 border-b-2 font-medium text-sm transition-all duration-300 {{ $activeTab === 'collections' ? 'border-orange-500 text-orange-600 dark:text-orange-400' : 'border-transparent text-gray-500 dark:text-gray-400 hover:text-orange-600 dark:hover:text-orange-400 hover:border-orange-300' }}">
            <div class="flex items-center space-x-2">
                <x-heroicon-o-folder class="w-4 h-4 {{ $activeTab === 'collections' ? 'text-orange-500' : 'group-hover:text-orange-500' }} transition-colors duration-300" />
                <span>Bộ sưu tập</span>
            </div>
        </button>
        <button wire:click="setActiveTab('favorites')" class="group py-4 px-1 border-b-2 font-medium text-sm transition-all duration-300 {{ $activeTab === 'favorites' ? 'border-orange-500 text-orange-600 dark:text-orange-400' : 'border-transparent text-gray-500 dark:text-gray-400 hover:text-orange-600 dark:hover:text-orange-400 hover:border-orange-300' }}">
            <div class="flex items-center space-x-2">
                <x-heroicon-o-heart class="w-4 h-4 {{ $activeTab === 'favorites' ? 'text-red-500' : 'group-hover:text-red-500' }} transition-colors duration-300" />
                <span>Yêu thích</span>
            </div>
        </button>
        <button wire:click="setActiveTab('settings')" class="group py-4 px-1 border-b-2 font-medium text-sm transition-all duration-300 {{ $activeTab === 'settings' ? 'border-orange-500 text-orange-600 dark:text-orange-400' : 'border-transparent text-gray-500 dark:text-gray-400 hover:text-orange-600 dark:hover:text-orange-400 hover:border-orange-300' }}">
            <div class="flex items-center space-x-2">
                <x-heroicon-o-cog-6-tooth class="w-4 h-4 {{ $activeTab === 'settings' ? 'text-orange-500' : 'group-hover:text-orange-500' }} transition-colors duration-300" />
                <span>Cài đặt</span>
            </div>
        </button>
    </nav>
</div> 