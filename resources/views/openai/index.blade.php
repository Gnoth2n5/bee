@extends('layouts.app')

@section('title', 'AI Cooking Assistant - BeeFood')

@section('content')
<div class="min-h-screen bg-gray-50">
    <!-- Header Section -->
    <div class="bg-gradient-to-r from-orange-500 to-orange-600 text-white">
        <div class="max-w-10xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
            <div class="text-center">
                <div class="flex justify-center mb-6">
                    <div class="w-20 h-20 bg-white rounded-full flex items-center justify-center">
                        <svg class="w-12 h-12 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                        </svg>
                    </div>
                </div>
                <h1 class="text-4xl font-bold mb-4">AI Cooking Assistant</h1>
                <p class="text-xl text-orange-100 max-w-3xl mx-auto">
                    Trợ lý nấu ăn AI thông minh của BeeFood - sẵn sàng giúp bạn tìm kiếm công thức, mẹo nấu ăn và gợi ý món ngon!
                </p>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="max-w-10xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        @livewire('open-ai-chat')
    </div>

    <!-- Features Section -->
    <div class="bg-white border-t border-gray-200 mt-12">
        <div class="max-w-10xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
            <div class="text-center mb-12">
                <h2 class="text-3xl font-bold text-gray-900 mb-4">Trợ lý AI có thể giúp bạn</h2>
                <p class="text-lg text-gray-600 max-w-3xl mx-auto">
                    Với công nghệ AI tiên tiến, chúng tôi mang đến cho bạn trải nghiệm nấu ăn thông minh và tiện lợi
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
                <!-- Feature 1 -->
                <div class="text-center">
                    <div class="w-16 h-16 bg-orange-50 rounded-full flex items-center justify-center mx-auto mb-4">
                        <svg class="w-8 h-8 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C20.832 18.477 19.246 18 17.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-900 mb-2">Gợi ý công thức</h3>
                    <p class="text-gray-600">Tìm món ăn phù hợp với nguyên liệu bạn có sẵn</p>
                </div>

                <!-- Feature 2 -->
                <div class="text-center">
                    <div class="w-16 h-16 bg-orange-50 rounded-full flex items-center justify-center mx-auto mb-4">
                        <svg class="w-8 h-8 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/>
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-900 mb-2">Mẹo nấu ăn</h3>
                    <p class="text-gray-600">Học những bí quyết nấu nướng từ AI thông minh</p>
                </div>

                <!-- Feature 3 -->
                <div class="text-center">
                    <div class="w-16 h-16 bg-orange-50 rounded-full flex items-center justify-center mx-auto mb-4">
                        <svg class="w-8 h-8 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-900 mb-2">Thông tin dinh dưỡng</h3>
                    <p class="text-gray-600">Tìm hiểu giá trị dinh dưỡng của các món ăn</p>
                </div>

                <!-- Feature 4 -->
                <div class="text-center">
                    <div class="w-16 h-16 bg-orange-50 rounded-full flex items-center justify-center mx-auto mb-4">
                        <svg class="w-8 h-8 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-900 mb-2">Tư vấn 24/7</h3>
                    <p class="text-gray-600">Hỏi đáp mọi lúc về nấu ăn và ẩm thực</p>
                </div>
            </div>
        </div>
    </div>

    <!-- CTA Section -->
    <div class="bg-orange-50">
        <div class="max-w-10xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
            <div class="text-center">
                <h2 class="text-3xl font-bold text-gray-900 mb-4">Bắt đầu hành trình nấu ăn cùng AI</h2>
                <p class="text-lg text-gray-600 mb-8 max-w-2xl mx-auto">
                    Đừng ngần ngại hỏi bất cứ điều gì về nấu ăn. AI của chúng tôi luôn sẵn sàng hỗ trợ bạn!
                </p>
                <div class="flex flex-col sm:flex-row gap-4 justify-center">
                    <button onclick="document.querySelector('#message-input').focus()" 
                            class="inline-flex items-center px-6 py-3 border border-transparent text-base font-medium rounded-md text-white bg-orange-600 hover:bg-orange-700 transition-colors">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                        </svg>
                        Bắt đầu trò chuyện
                    </button>
                    <a href="{{ route('recipes.index') }}" 
                       class="inline-flex items-center px-6 py-3 border border-orange-600 text-base font-medium rounded-md text-orange-600 bg-white hover:bg-orange-50 transition-colors">
                        Xem công thức
                        <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                        </svg>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Scroll to chat section when clicking start chat button
    const startChatBtn = document.querySelector('[onclick*="message-input"]');
    if (startChatBtn) {
        startChatBtn.addEventListener('click', function() {
            setTimeout(() => {
                const chatSection = document.querySelector('#chat-container');
                if (chatSection) {
                    chatSection.scrollIntoView({ behavior: 'smooth' });
                }
            }, 100);
        });
    }
});
</script>
@endpush
@endsection
