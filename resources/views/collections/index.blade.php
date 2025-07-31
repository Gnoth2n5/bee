@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gray-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Header -->
        <div class="mb-8">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">Bộ sưu tập của tôi</h1>
                    <p class="text-gray-600 mt-2">Quản lý các bộ sưu tập công thức của bạn</p>
                </div>
                <a href="{{ route('collections.create') }}" 
                   class="inline-flex items-center px-4 py-2 bg-orange-600 hover:bg-orange-700 text-white text-sm font-medium rounded-lg transition-colors">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                    </svg>
                    Tạo bộ sưu tập mới
                </a>
            </div>
        </div>

        <!-- Collections Grid -->
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
                                <a href="{{ route('collections.edit', $collection) }}" 
                                   class="w-8 h-8 flex items-center justify-center bg-white rounded-full shadow-sm hover:bg-gray-50 transition-colors">
                                    <svg class="w-4 h-4 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                    </svg>
                                </a>
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
                                    {{ $collection->recipe_count }} công thức
                                </span>
                            </div>

                            <div class="mt-3 text-xs text-gray-400">
                                Tạo lúc: {{ $collection->created_at->format('d/m/Y H:i') }}
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Pagination -->
            @if($collections->hasPages())
                <div class="mt-8">
                    {{ $collections->links() }}
                </div>
            @endif
        @else
            <div class="text-center py-12">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                </svg>
                <h3 class="mt-2 text-sm font-medium text-gray-900">Chưa có bộ sưu tập nào</h3>
                <p class="mt-1 text-sm text-gray-500">Bắt đầu tạo bộ sưu tập đầu tiên của bạn!</p>
                <div class="mt-6">
                    <a href="{{ route('collections.create') }}" 
                       class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-orange-600 hover:bg-orange-700">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                        </svg>
                        Tạo bộ sưu tập đầu tiên
                    </a>
                </div>
            </div>
        @endif
    </div>
</div>
@endsection 