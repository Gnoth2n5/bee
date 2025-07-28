@extends('layouts.app')

@section('title', $post->title . ' - BeeFood')

@section('meta')
    <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate">
    <meta http-equiv="Pragma" content="no-cache">
    <meta http-equiv="Expires" content="0">
@endsection

@section('content')
<div class="bg-gray-50 min-h-screen">
    <!-- Hero Section -->
    <div class="relative bg-white">
        @if($post->featured_image)
            <div class="absolute inset-0 bg-gradient-to-br from-orange-500 to-red-600">
                <img src="{{ Storage::url($post->featured_image) }}" 
                     alt="{{ $post->title }}" 
                     class="w-full h-full object-cover opacity-75">
            </div>
        @else
            <div class="absolute inset-0 bg-gradient-to-br from-orange-500 to-red-600"></div>
        @endif
        
        <div class="relative max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
            <div class="text-center text-white">
                <!-- Breadcrumb -->
                <nav class="flex justify-center mb-6" aria-label="Breadcrumb">
                    <ol class="inline-flex items-center space-x-1 md:space-x-3">
                        <li class="inline-flex items-center">
                            <a href="{{ route('home') }}" class="inline-flex items-center text-sm font-medium text-orange-200 hover:text-white">
                                <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z"></path>
                                </svg>
                                Trang chủ
                            </a>
                        </li>
                        <li>
                            <div class="flex items-center">
                                <svg class="w-6 h-6 text-orange-200" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
                                </svg>
                                <a href="{{ route('posts.index') }}" class="ml-1 text-sm font-medium text-orange-200 hover:text-white md:ml-2">
                                    Bài viết
                                </a>
                            </div>
                        </li>
                        <li aria-current="page">
                            <div class="flex items-center">
                                <svg class="w-6 h-6 text-orange-200" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
                                </svg>
                                <span class="ml-1 text-sm font-medium text-white md:ml-2">{{ $post->title }}</span>
                            </div>
                        </li>
                    </ol>
                </nav>

                <!-- Meta info -->
                <div class="flex items-center justify-center text-sm text-orange-200 mb-6">
                    <div class="flex items-center">
                        <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"></path>
                        </svg>
                        {{ $post->user->name }}
                    </div>
                    <span class="mx-3">•</span>
                    <div class="flex items-center">
                        <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"></path>
                        </svg>
                        {{ $post->published_at->format('d/m/Y H:i') }}
                    </div>
                    <span class="mx-3">•</span>
                    <div class="flex items-center">
                        <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M10 12a2 2 0 100-4 2 2 0 000 4z"></path>
                            <path fill-rule="evenodd" d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z" clip-rule="evenodd"></path>
                        </svg>
                        {{ number_format($post->view_count) }} lượt xem
                    </div>
                </div>

                <!-- Tiêu đề -->
                <h1 class="text-4xl md:text-5xl font-bold mb-6 leading-tight">
                    {{ $post->title }}
                </h1>

                <!-- Tóm tắt -->
                @if($post->excerpt)
                    <p class="text-xl text-orange-100 max-w-3xl mx-auto leading-relaxed">
                        {{ $post->excerpt }}
                    </p>
                @endif
            </div>
        </div>
    </div>

    <!-- Content -->
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-12">
            <!-- Main content -->
            <div class="lg:col-span-2">
                <article class="bg-white rounded-lg shadow-lg p-8">
                    <!-- Nội dung bài viết -->
                    <div class="prose prose-lg max-w-none">
                        {!! $post->content !!}
                    </div>

                    <!-- Footer bài viết -->
                    <div class="mt-8 pt-8 border-t border-gray-200">
                        <livewire:posts.post-actions :post="$post" />
                    </div>
                </article>
            </div>

            <!-- Sidebar -->
            <div class="lg:col-span-1">
                <!-- Bài viết liên quan -->
                @if($relatedPosts->count() > 0)
                    <div class="bg-white rounded-lg shadow-lg p-6 mb-8">
                        <h3 class="text-xl font-bold text-gray-900 mb-6 flex items-center">
                            <svg class="w-5 h-5 mr-2 text-orange-500" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M3 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1z" clip-rule="evenodd"></path>
                            </svg>
                            Bài viết liên quan
                        </h3>
                        
                        <div class="space-y-4">
                            @foreach($relatedPosts as $relatedPost)
                                <article class="group">
                                    <a href="{{ route('posts.show', $relatedPost->slug) }}" class="block">
                                        <div class="flex space-x-4">
                                            <!-- Ảnh thumbnail -->
                                            <div class="flex-shrink-0">
                                                @if($relatedPost->featured_image)
                                                    <img src="{{ Storage::url($relatedPost->featured_image) }}" 
                                                         alt="{{ $relatedPost->title }}" 
                                                         class="w-16 h-16 object-cover rounded-lg">
                                                @else
                                                    <div class="w-16 h-16 bg-gradient-to-br from-orange-400 to-red-500 rounded-lg flex items-center justify-center">
                                                        <svg class="w-6 h-6 text-white" fill="currentColor" viewBox="0 0 20 20">
                                                            <path fill-rule="evenodd" d="M4 3a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V5a2 2 0 00-2-2H4zm12 12H4l4-8 3 6 2-4 3 6z" clip-rule="evenodd"></path>
                                                        </svg>
                                                    </div>
                                                @endif
                                            </div>
                                            
                                            <!-- Nội dung -->
                                            <div class="flex-1 min-w-0">
                                                <h4 class="text-sm font-semibold text-gray-900 group-hover:text-orange-600 transition-colors duration-200 line-clamp-2">
                                                    {{ $relatedPost->title }}
                                                </h4>
                                                
                                                <div class="flex items-center text-xs text-gray-500 mt-1">
                                                    <span>{{ $relatedPost->published_at->diffForHumans() }}</span>
                                                </div>
                                            </div>
                                        </div>
                                    </a>
                                </article>
                            @endforeach
                        </div>
                    </div>
                @endif

                <!-- Thống kê -->
                <div class="bg-white rounded-lg shadow-lg p-6">
                    <h3 class="text-xl font-bold text-gray-900 mb-6 flex items-center">
                        <svg class="w-5 h-5 mr-2 text-orange-500" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M3 3a1 1 0 000 2v8a2 2 0 002 2h2.586l-1.293 1.293a1 1 0 101.414 1.414L10 15.414l2.293 2.293a1 1 0 001.414-1.414L12.414 15H15a2 2 0 002-2V5a1 1 0 100-2H3zm11.707 4.707a1 1 0 00-1.414-1.414L10 9.586 8.707 8.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                        </svg>
                        Thống kê
                    </h3>
                    
                    <div class="space-y-4">
                        <div class="flex items-center justify-between">
                            <span class="text-gray-600">Lượt xem</span>
                            <span class="font-semibold text-gray-900">{{ number_format($post->view_count) }}</span>
                        </div>
                        
                        <div class="flex items-center justify-between">
                            <span class="text-gray-600">Ngày xuất bản</span>
                            <span class="font-semibold text-gray-900">{{ $post->published_at->format('d/m/Y') }}</span>
                        </div>
                        
                        <div class="flex items-center justify-between">
                            <span class="text-gray-600">Tác giả</span>
                            <span class="font-semibold text-gray-900">{{ $post->user->name }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.line-clamp-2 {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

.prose {
    color: #374151;
}

.prose h1, .prose h2, .prose h3, .prose h4, .prose h5, .prose h6 {
    color: #111827;
    font-weight: 700;
    margin-top: 2rem;
    margin-bottom: 1rem;
}

.prose h1 {
    font-size: 2.25rem;
}

.prose h2 {
    font-size: 1.875rem;
}

.prose h3 {
    font-size: 1.5rem;
}

.prose p {
    margin-bottom: 1.5rem;
    line-height: 1.75;
}

.prose ul, .prose ol {
    margin-bottom: 1.5rem;
    padding-left: 1.5rem;
}

.prose li {
    margin-bottom: 0.5rem;
}

.prose blockquote {
    border-left: 4px solid #f59e0b;
    padding-left: 1rem;
    margin: 2rem 0;
    font-style: italic;
    color: #6b7280;
}

.prose img {
    border-radius: 0.5rem;
    margin: 2rem 0;
}

.prose a {
    color: #f59e0b;
    text-decoration: underline;
}

.prose a:hover {
    color: #d97706;
}
</style>
@endsection 