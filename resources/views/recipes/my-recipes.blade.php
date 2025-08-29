@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gray-50">
    <!-- Header Section -->
    <div class="bg-white border-b border-gray-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">Công thức của tôi</h1>
                    <p class="text-gray-600 mt-2">Quản lý công thức và bộ sưu tập của bạn</p>
                </div>
                <div class="flex space-x-3">
                    <!-- Export Dropdown -->
                    <div class="relative" x-data="{ open: false }">
                        <button @click="open = !open" 
                                class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg transition-colors flex items-center">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                            Xuất công thức
                        </button>
                        
                        <div x-show="open" @click.away="open = false" 
                             x-transition:enter="transition ease-out duration-100"
                             x-transition:enter-start="transform opacity-0 scale-95"
                             x-transition:enter-end="transform opacity-100 scale-100"
                             x-transition:leave="transition ease-in duration-75"
                             x-transition:leave-start="transform opacity-100 scale-100"
                             x-transition:leave-end="transform opacity-0 scale-95"
                             class="absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg z-50 border border-gray-200">
                            <div class="py-1">
                                <a href="{{ route('recipes.my.export.excel') }}" 
                                   class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                    <svg class="w-4 h-4 mr-2 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                    </svg>
                                    Excel (.xlsx)
                                </a>
                                <a href="{{ route('recipes.my.export.zip') }}" 
                                   class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                    <svg class="w-4 h-4 mr-2 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                                    </svg>
                                    ZIP với template
                                </a>
                            </div>
                        </div>
                    </div>

                    <a href="{{ route('recipes.create') }}" 
                       class="inline-flex items-center px-4 py-2 bg-orange-600 hover:bg-orange-700 text-white text-sm font-medium rounded-lg transition-colors">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                        </svg>
                        Tạo công thức mới
                    </a>
                </div>
            </div>

            <!-- Stats -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mt-8">
                <div class="bg-gradient-to-r from-orange-500 to-red-500 rounded-lg p-6 text-white">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                            </svg>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium opacity-90">Công thức</p>
                            <p class="text-2xl font-bold">{{ $stats['recipes_count'] }}</p>
                        </div>
                    </div>
                </div>

                <div class="bg-gradient-to-r from-blue-500 to-indigo-500 rounded-lg p-6 text-white">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                            </svg>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium opacity-90">Bộ sưu tập</p>
                            <p class="text-2xl font-bold">{{ $stats['collections_count'] }}</p>
                        </div>
                    </div>
                </div>

                <div class="bg-gradient-to-r from-red-500 to-pink-500 rounded-lg p-6 text-white">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
                            </svg>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium opacity-90">Yêu thích</p>
                            <p class="text-2xl font-bold">{{ $stats['favorites_count'] }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Content Section -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Tabs -->
        <div class="border-b border-gray-200 mb-8">
            <nav class="-mb-px flex space-x-8">
                <button class="border-b-2 border-orange-500 text-orange-600 py-2 px-1 text-sm font-medium" id="recipes-tab">
                    Công thức của tôi
                </button>
                <button class="border-b-2 border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 py-2 px-1 text-sm font-medium" id="collections-tab">
                    Bộ sưu tập
                </button>
            </nav>
        </div>

        <!-- Recipes Tab Content -->
        <div id="recipes-content" class="tab-content">
            @if($recipes->count() > 0)
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach($recipes as $recipe)
                        <div class="bg-white rounded-lg shadow-sm hover:shadow-md transition-all duration-300 overflow-hidden">
                            <div class="aspect-video bg-gray-200 relative overflow-hidden">
                                @if($recipe->featured_image)
                                    <img src="{{ Storage::url($recipe->featured_image) }}" 
                                         alt="{{ $recipe->title }}" 
                                         class="w-full h-full object-cover" />
                                @else
                                    <div class="w-full h-full flex items-center justify-center bg-gradient-to-br from-orange-100 to-red-100">
                                        <svg class="w-12 h-12 text-orange-300" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h6"/>
                                            <circle cx="12" cy="12" r="10"/>
                                        </svg>
                                    </div>
                                @endif
                                
                                <!-- Status Badge -->
                                <div class="absolute top-2 left-2">
                                    @if($recipe->status === 'approved')
                                        <span class="px-2 py-1 text-xs font-medium text-white bg-green-500 rounded-full">Đã duyệt</span>
                                    @elseif($recipe->status === 'pending')
                                        <span class="px-2 py-1 text-xs font-medium text-white bg-yellow-500 rounded-full">Chờ duyệt</span>
                                    @else
                                        <span class="px-2 py-1 text-xs font-medium text-white bg-red-500 rounded-full">Từ chối</span>
                                    @endif
                                </div>

                                <!-- Actions -->
                                <div class="absolute top-2 right-2 flex space-x-2">
                                    <a href="{{ route('recipes.edit', $recipe) }}" 
                                       class="w-8 h-8 flex items-center justify-center bg-white rounded-full shadow-sm hover:bg-gray-50 transition-colors">
                                        <svg class="w-4 h-4 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                        </svg>
                                    </a>
                                    <a href="{{ route('recipes.show', $recipe) }}" 
                                       class="w-8 h-8 flex items-center justify-center bg-white rounded-full shadow-sm hover:bg-gray-50 transition-colors">
                                        <svg class="w-4 h-4 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                        </svg>
                                    </a>
                                </div>
                            </div>

                            <div class="p-4">
                                <h3 class="font-semibold text-gray-900 mb-2 line-clamp-2">
                                    <a href="{{ route('recipes.show', $recipe) }}" class="hover:text-orange-600 transition-colors">
                                        {{ $recipe->title }}
                                    </a>
                                </h3>

                                <p class="text-sm text-gray-600 mb-3 line-clamp-2">{{ $recipe->summary }}</p>

                                <div class="flex items-center justify-between text-sm text-gray-500">
                                    <span class="flex items-center">
                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                        </svg>
                                        {{ $recipe->cooking_time }} phút
                                    </span>
                                    <span class="flex items-center">
                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                                        </svg>
                                        {{ $recipe->servings }} người
                                    </span>
                                </div>

                                <div class="mt-3 text-xs text-gray-400">
                                    Tạo lúc: {{ $recipe->created_at->format('d/m/Y H:i') }}
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- Pagination -->
                @if($recipes->hasPages())
                    <div class="mt-8">
                        {{ $recipes->links() }}
                    </div>
                @endif
            @else
                <div class="text-center py-12">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                    </svg>
                    <h3 class="mt-2 text-sm font-medium text-gray-900">Chưa có công thức nào</h3>
                    <p class="mt-1 text-sm text-gray-500">Bắt đầu tạo công thức đầu tiên của bạn!</p>
                    <div class="mt-6">
                        <a href="{{ route('recipes.create') }}" 
                           class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-orange-600 hover:bg-orange-700">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                            </svg>
                            Tạo công thức đầu tiên
                        </a>
                    </div>
                </div>
            @endif
        </div>

        <!-- Collections Tab Content -->
        <div id="collections-content" class="tab-content hidden">
            @if($collections->count() > 0)
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach($collections as $collection)
                        <div class="bg-white rounded-lg shadow-sm hover:shadow-md transition-all duration-300 overflow-hidden">
                            <div class="aspect-video bg-gray-200 relative overflow-hidden">
                                @if($collection->cover_image)
                                    <img src="{{ Storage::url($collection->cover_image) }}" 
                                         alt="{{ $collection->name }}" 
                                         class="w-full h-full object-cover" />
                                @else
                                    <div class="w-full h-full flex items-center justify-center bg-gradient-to-br from-blue-100 to-indigo-100">
                                        <svg class="w-12 h-12 text-blue-300" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                                        </svg>
                                    </div>
                                @endif

                                <!-- Privacy Badge -->
                                <div class="absolute top-2 left-2">
                                    @if($collection->is_public)
                                        <span class="px-2 py-1 text-xs font-medium text-white bg-green-500 rounded-full">Công khai</span>
                                    @else
                                        <span class="px-2 py-1 text-xs font-medium text-white bg-gray-500 rounded-full">Riêng tư</span>
                                    @endif
                                </div>

                                <!-- Actions -->
                                <div class="absolute top-2 right-2 flex space-x-2">
                                    <a href="{{ route('collections.show', $collection) }}" 
                                       class="w-8 h-8 flex items-center justify-center bg-white rounded-full shadow-sm hover:bg-gray-50 transition-colors">
                                        <svg class="w-4 h-4 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                        </svg>
                                    </a>
                                </div>
                            </div>

                            <div class="p-4">
                                <h3 class="font-semibold text-gray-900 mb-2 line-clamp-2">
                                    <a href="{{ route('collections.show', $collection) }}" class="hover:text-blue-600 transition-colors">
                                        {{ $collection->name }}
                                    </a>
                                </h3>

                                <p class="text-sm text-gray-600 mb-3 line-clamp-2">{{ $collection->description }}</p>

                                <div class="flex items-center justify-between text-sm text-gray-500">
                                    <span class="flex items-center">
                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                                        </svg>
                                        {{ $collection->recipes_count }} công thức
                                    </span>
                                </div>

                                <div class="mt-3 text-xs text-gray-400">
                                    Tạo lúc: {{ $collection->created_at->format('d/m/Y H:i') }}
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-12">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                    </svg>
                    <h3 class="mt-2 text-sm font-medium text-gray-900">Chưa có bộ sưu tập nào</h3>
                    <p class="mt-1 text-sm text-gray-500">Tạo bộ sưu tập để tổ chức công thức yêu thích!</p>
                </div>
            @endif
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const recipesTab = document.getElementById('recipes-tab');
    const collectionsTab = document.getElementById('collections-tab');
    const recipesContent = document.getElementById('recipes-content');
    const collectionsContent = document.getElementById('collections-content');

    function showTab(tabName) {
        // Hide all content
        recipesContent.classList.add('hidden');
        collectionsContent.classList.add('hidden');

        // Remove active class from all tabs
        recipesTab.classList.remove('border-orange-500', 'text-orange-600');
        recipesTab.classList.add('border-transparent', 'text-gray-500');
        collectionsTab.classList.remove('border-orange-500', 'text-orange-600');
        collectionsTab.classList.add('border-transparent', 'text-gray-500');

        // Show selected content and activate tab
        if (tabName === 'recipes') {
            recipesContent.classList.remove('hidden');
            recipesTab.classList.remove('border-transparent', 'text-gray-500');
            recipesTab.classList.add('border-orange-500', 'text-orange-600');
        } else if (tabName === 'collections') {
            collectionsContent.classList.remove('hidden');
            collectionsTab.classList.remove('border-transparent', 'text-gray-500');
            collectionsTab.classList.add('border-orange-500', 'text-orange-600');
        }
    }

    recipesTab.addEventListener('click', () => showTab('recipes'));
    collectionsTab.addEventListener('click', () => showTab('collections'));
});
</script>
@endsection 