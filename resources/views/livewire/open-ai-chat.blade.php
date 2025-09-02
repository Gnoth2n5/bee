@use('Illuminate\Support\Facades\Storage')

<div id="chat-container" class="max-w-4xl mx-auto">
    <!-- Chat Header -->
    <div class="bg-white rounded-t-lg border border-gray-200 px-6 py-4">
        <div class="flex items-center justify-between">
            <div class="flex items-center">
                <div class="w-10 h-10 bg-gradient-to-r from-orange-500 to-orange-600 rounded-full flex items-center justify-center">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                    </svg>
                </div>
                <div class="ml-3">
                    <div class="flex items-center space-x-2">
                        <h3 class="text-lg font-semibold text-gray-900">AI Cooking Assistant</h3>
                        @if(auth()->user()?->isVip())
                            <span class="inline-flex items-center px-2 py-1 text-xs font-medium bg-gradient-to-r from-yellow-400 to-yellow-600 text-white rounded-full">
                                <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                </svg>
                                VIP
                            </span>
                        @endif
                    </div>
                    <p class="text-sm text-gray-500">
                        @if(auth()->user()?->isVip())
                            Trợ lý AI cao cấp với gợi ý cá nhân hóa
                        @else
                            Sẵn sàng hỗ trợ bạn nấu ăn
                        @endif
                    </p>
                </div>
            </div>
            
            <!-- Chat Actions -->
            <div class="flex items-center space-x-2">
                @if($hasConversation)
                    <button wire:click="toggleHistory" 
                            class="p-2 text-gray-400 hover:text-gray-600 transition-colors rounded-lg hover:bg-gray-100">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </button>
                    <button wire:click="clearConversation" 
                            wire:confirm="Bạn có chắc muốn xóa toàn bộ lịch sử trò chuyện?"
                            class="p-2 text-gray-400 hover:text-red-600 transition-colors rounded-lg hover:bg-red-50">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                        </svg>
                    </button>
                @endif
            </div>
        </div>
        
        <!-- Conversation Stats -->
        @if($hasConversation && $showHistory)
            <div class="mt-4 p-3 bg-gray-50 rounded-lg">
                <div class="flex items-center justify-between text-sm text-gray-600">
                    <span>Tổng tin nhắn: {{ $conversationStats['total_messages'] }}</span>
                    <span>Bạn: {{ $conversationStats['user_messages'] }} | AI: {{ $conversationStats['ai_messages'] }}</span>
                </div>
            </div>
        @endif
    </div>

    <!-- Category Tabs -->
    <div class="bg-white border-l border-r border-gray-200 px-6 py-3">
        <div class="flex space-x-1 overflow-x-auto">
            <button wire:click="$set('selectedCategory', 'general')" 
                    class="px-4 py-2 text-sm font-medium rounded-lg transition-colors whitespace-nowrap {{ $selectedCategory === 'general' ? 'bg-orange-100 text-orange-700' : 'text-gray-600 hover:text-gray-900 hover:bg-gray-100' }}">
                <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                </svg>
                Chung
            </button>
            <button wire:click="$set('selectedCategory', 'ingredients')" 
                    class="px-4 py-2 text-sm font-medium rounded-lg transition-colors whitespace-nowrap {{ $selectedCategory === 'ingredients' ? 'bg-orange-100 text-orange-700' : 'text-gray-600 hover:text-gray-900 hover:bg-gray-100' }}">
                <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                </svg>
                Nguyên liệu
            </button>
            <button wire:click="$set('selectedCategory', 'tips')" 
                    class="px-4 py-2 text-sm font-medium rounded-lg transition-colors whitespace-nowrap {{ $selectedCategory === 'tips' ? 'bg-orange-100 text-orange-700' : 'text-gray-600 hover:text-gray-900 hover:bg-gray-100' }}">
                <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/>
                </svg>
                Mẹo hay
            </button>
            <button wire:click="$set('selectedCategory', 'nutrition')" 
                    class="px-4 py-2 text-sm font-medium rounded-lg transition-colors whitespace-nowrap {{ $selectedCategory === 'nutrition' ? 'bg-orange-100 text-orange-700' : 'text-gray-600 hover:text-gray-900 hover:bg-gray-100' }}">
                <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                </svg>
                Dinh dưỡng
            </button>
        </div>
    </div>

    <!-- Ingredients Section (shown when ingredients category is selected) -->
    @if($selectedCategory === 'ingredients')
        <div class="bg-white border-l border-r border-gray-200 px-6 py-4">
            <h4 class="text-sm font-medium text-gray-900 mb-3">Nguyên liệu có sẵn:</h4>
            
            <!-- Add Ingredient -->
            <div class="flex gap-2 mb-3">
                <input type="text" 
                       wire:model="newIngredient"
                       wire:keydown.enter="addIngredient"
                       placeholder="Thêm nguyên liệu..."
                       class="flex-1 px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-transparent text-sm">
                <button wire:click="addIngredient" 
                        class="px-4 py-2 bg-orange-600 text-white rounded-lg hover:bg-orange-700 transition-colors text-sm">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                    </svg>
                </button>
            </div>

            <!-- Ingredients List -->
            @if(count($ingredients) > 0)
                <div class="flex flex-wrap gap-2 mb-3">
                    @foreach($ingredients as $index => $ingredient)
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm bg-orange-100 text-orange-800">
                            {{ $ingredient }}
                            <button wire:click="removeIngredient({{ $index }})" 
                                    class="ml-2 hover:text-orange-600">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                            </button>
                        </span>
                    @endforeach
                </div>
                <button wire:click="getRecipeSuggestions" 
                        class="w-full px-4 py-2 bg-orange-600 text-white rounded-lg hover:bg-orange-700 transition-colors text-sm font-medium">
                    Gợi ý món ăn từ nguyên liệu này
                </button>
            @else
                <p class="text-sm text-gray-500 italic">Chưa có nguyên liệu nào. Hãy thêm nguyên liệu để nhận gợi ý!</p>
            @endif
        </div>
    @endif

    <!-- Quick Suggestions -->
    <div class="bg-white border-l border-r border-gray-200 px-6 py-4">
        <h4 class="text-sm font-medium text-gray-900 mb-3">Gợi ý nhanh:</h4>
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
            @foreach($quickSuggestions[$selectedCategory] as $suggestion)
                <button wire:click="selectQuickSuggestion('{{ $suggestion }}')" 
                        class="text-left px-3 py-2 text-sm text-gray-700 bg-gray-50 rounded-lg hover:bg-orange-50 hover:text-orange-700 transition-colors">
                    {{ $suggestion }}
                </button>
            @endforeach
        </div>
    </div>

    <!-- Chat Messages -->
    <div class="bg-white border-l border-r border-gray-200">
        <div id="chat-messages" class="h-96 overflow-y-auto p-6 space-y-4">
            @if(count($conversation) === 0)
                <!-- Welcome Message -->
                <div class="text-center py-8">
                    <div class="w-16 h-16 bg-orange-50 rounded-full flex items-center justify-center mx-auto mb-4">
                        <svg class="w-8 h-8 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                        </svg>
                    </div>
                    <h3 class="text-lg font-medium text-gray-900 mb-2">Xin chào! Tôi là trợ lý AI nấu ăn</h3>
                    <p class="text-gray-600">Hãy hỏi tôi bất cứ điều gì về nấu ăn, công thức món ăn, hoặc mẹo hay trong bếp!</p>
                </div>
            @else
                @foreach($conversation as $index => $message)
                    <div class="flex {{ $message['role'] === 'user' ? 'justify-end' : 'justify-start' }} animate-fade-in-up" style="animation-delay: {{ $index * 0.1 }}s">
                        <div class="flex max-w-xs lg:max-w-md {{ $message['role'] === 'user' ? 'flex-row-reverse' : 'flex-row' }}">
                            <div class="flex-shrink-0">
                                <div class="w-8 h-8 rounded-full {{ $message['role'] === 'user' ? 'bg-gray-300 ml-3' : 'bg-orange-500 mr-3' }} flex items-center justify-center">
                                    @if($message['role'] === 'user')
                                        <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                        </svg>
                                    @else
                                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                                        </svg>
                                    @endif
                                </div>
                            </div>
                            <div>
                                <div class="px-4 py-2 rounded-lg {{ $message['role'] === 'user' ? 'bg-orange-600 text-white' : ($message['is_error'] ?? false ? 'bg-red-50 text-red-800 border border-red-200' : 'bg-gray-100 text-gray-900') }}">
                                    @if($message['role'] === 'assistant' && isset($message['content_html']))
                                        <div class="text-sm markdown-content">
                                            {!! $message['content_html'] !!}
                                        </div>
                                    @else
                                        <p class="text-sm whitespace-pre-wrap">{{ $message['content'] }}</p>
                                    @endif
                                </div>
                                
                                <!-- Recipe Cards for AI messages -->
                                @if($message['role'] === 'assistant' && isset($message['recipes']) && !empty($message['recipes']))
                                    <div class="mt-3 space-y-3">
                                        @foreach($message['recipes'] as $recipe)
                                            <div class="bg-white border border-gray-200 rounded-lg shadow-sm hover:shadow-md transition-shadow {{ isset($recipe['is_vip_recommendation']) ? 'border-yellow-300 bg-gradient-to-br from-yellow-50 to-white' : '' }}">
                                                @if(isset($recipe['is_vip_recommendation']))
                                                    <div class="bg-gradient-to-r from-yellow-400 to-yellow-600 text-white px-3 py-1 rounded-t-lg">
                                                        <div class="flex items-center justify-between text-xs font-medium">
                                                            <span class="flex items-center">
                                                                <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                                                </svg>
                                                                Gợi ý VIP cá nhân hóa
                                                            </span>
                                                            @if(isset($recipe['vip_bonus']) && $recipe['vip_bonus'] > 0)
                                                                <span>+{{ round($recipe['vip_bonus'] * 100) }}% phù hợp</span>
                                                            @endif
                                                        </div>
                                                    </div>
                                                @endif
                                                <div class="p-4">
                                                    <div class="flex items-start space-x-3">
                                                        @if(!empty($recipe['featured_image']))
                                                            <img src="{{ Storage::url($recipe['featured_image']) }}" 
                                                                 alt="{{ $recipe['title'] }}"
                                                                 class="w-16 h-16 rounded-lg object-cover flex-shrink-0"
                                                                 onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                                                            <div class="w-16 h-16 bg-gray-200 rounded-lg flex items-center justify-center flex-shrink-0 hidden">
                                                                <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                                                                </svg>
                                                            </div>
                                                        @else
                                                            <div class="w-16 h-16 bg-gray-200 rounded-lg flex items-center justify-center flex-shrink-0">
                                                                <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                                                                </svg>
                                                            </div>
                                                        @endif
                                                        
                                                        <div class="flex-1 min-w-0">
                                                            <h4 class="text-sm font-medium text-gray-900 truncate">
                                                                {{ $recipe['title'] }}
                                                            </h4>
                                                            @if(!empty($recipe['summary']))
                                                                <p class="text-xs text-gray-600 mt-1 line-clamp-2">
                                                                    {{ $recipe['summary'] }}
                                                                </p>
                                                            @endif
                                                            
                                                            <div class="flex items-center space-x-4 mt-2 text-xs text-gray-500">
                                                                @if(!empty($recipe['cooking_time']))
                                                                    <span class="flex items-center">
                                                                        <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                                                        </svg>
                                                                        {{ $recipe['cooking_time'] }} phút
                                                                    </span>
                                                                @endif
                                                                
                                                                @if(!empty($recipe['difficulty_level']))
                                                                    <span class="flex items-center">
                                                                        <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                                                                        </svg>
                                                                        {{ ucfirst($recipe['difficulty_level']) }}
                                                                    </span>
                                                                @endif
                                                                
                                                                @if(isset($recipe['similarity']))
                                                                    <span class="text-orange-600 font-medium">
                                                                        {{ round($recipe['similarity'] * 100) }}% phù hợp
                                                                    </span>
                                                                @endif
                                                            </div>
                                                            
                                                            <!-- VIP Nutritional Info -->
                                                            @if(isset($recipe['nutritional_info']))
                                                                <div class="mt-3 p-2 bg-yellow-50 rounded-lg border border-yellow-200">
                                                                    <h5 class="text-xs font-semibold text-yellow-800 mb-1">Thông tin dinh dưỡng (VIP)</h5>
                                                                    <div class="grid grid-cols-3 gap-1 text-xs text-yellow-700">
                                                                        <span>{{ $recipe['nutritional_info']['estimated_calories'] }} kcal</span>
                                                                        <span>Protein: {{ $recipe['nutritional_info']['protein'] }}</span>
                                                                        <span>Carbs: {{ $recipe['nutritional_info']['carbs'] }}</span>
                                                                    </div>
                                                                </div>
                                                            @endif

                                                            <!-- VIP Cooking Tips -->
                                                            @if(isset($recipe['cooking_tips']) && !empty($recipe['cooking_tips']))
                                                                <div class="mt-3 p-2 bg-blue-50 rounded-lg border border-blue-200">
                                                                    <h5 class="text-xs font-semibold text-blue-800 mb-1">Mẹo nấu ăn nâng cao (VIP)</h5>
                                                                    <ul class="text-xs text-blue-700 space-y-1">
                                                                        @foreach(array_slice($recipe['cooking_tips'], 0, 2) as $tip)
                                                                            <li class="flex items-start">
                                                                                <svg class="w-3 h-3 mr-1 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                                                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                                                                </svg>
                                                                                {{ $tip }}
                                                                            </li>
                                                                        @endforeach
                                                                    </ul>
                                                                </div>
                                                            @endif
                                                            
                                                            <div class="mt-2">
                                                                <a href="{{ route('recipes.show', $recipe['slug']) }}" 
                                                                   target="_blank"
                                                                   class="inline-flex items-center px-3 py-1 text-xs font-medium text-orange-700 bg-orange-100 rounded-full hover:bg-orange-200 transition-colors">
                                                                    Xem công thức
                                                                    <svg class="w-3 h-3 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                                                                    </svg>
                                                                </a>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                    
                                    <!-- VIP Upgrade Prompt for non-VIP users -->
                                    @if(!auth()->user()?->isVip() && count($message['recipes']) > 0)
                                        <div class="mt-3 p-4 bg-gradient-to-r from-yellow-50 to-orange-50 border border-yellow-200 rounded-lg">
                                            <div class="flex items-start space-x-3">
                                                <div class="flex-shrink-0">
                                                    <svg class="w-6 h-6 text-yellow-600" fill="currentColor" viewBox="0 0 20 20">
                                                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                                    </svg>
                                                </div>
                                                <div class="flex-1">
                                                    <h4 class="text-sm font-semibold text-gray-900">Nâng cấp lên VIP để có trải nghiệm tốt hơn!</h4>
                                                    <p class="text-xs text-gray-600 mt-1">
                                                        Thành viên VIP sẽ nhận được gợi ý cá nhân hóa, thông tin dinh dưỡng chi tiết, mẹo nấu ăn nâng cao và nhiều công thức phù hợp hơn.
                                                    </p>
                                                    <div class="mt-2">
                                                        <a href="{{ route('subscriptions.packages') }}" 
                                                           class="inline-flex items-center px-3 py-1 text-xs font-medium text-yellow-700 bg-yellow-100 rounded-full hover:bg-yellow-200 transition-colors">
                                                            Xem gói VIP
                                                            <svg class="w-3 h-3 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/>
                                                            </svg>
                                                        </a>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                @endif
                                <p class="text-xs text-gray-500 mt-1 {{ $message['role'] === 'user' ? 'text-right' : 'text-left' }}">
                                    {{ $message['timestamp'] }}
                                </p>
                            </div>
                        </div>
                    </div>
                @endforeach
            @endif

            <!-- Loading indicator -->
            @if($isLoading)
                <div class="flex justify-start">
                    <div class="flex max-w-xs lg:max-w-md flex-row">
                        <div class="flex-shrink-0">
                            <div class="w-8 h-8 rounded-full bg-orange-500 mr-3 flex items-center justify-center">
                                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                                </svg>
                            </div>
                        </div>
                        <div>
                            <div class="px-4 py-2 bg-gray-100 rounded-lg">
                                <div class="flex items-center space-x-2">
                                    <div class="flex space-x-1">
                                        <div class="w-2 h-2 bg-gray-400 rounded-full animate-bounce"></div>
                                        <div class="w-2 h-2 bg-gray-400 rounded-full animate-bounce" style="animation-delay: 0.1s"></div>
                                        <div class="w-2 h-2 bg-gray-400 rounded-full animate-bounce" style="animation-delay: 0.2s"></div>
                                    </div>
                                    <span class="text-sm text-gray-600">AI đang suy nghĩ...</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>

    <!-- Message Input -->
    <div class="bg-white rounded-b-lg border border-gray-200 p-6">
        <form wire:submit.prevent="sendMessage" class="flex space-x-4">
            <div class="flex-1">
                <input type="text" 
                       id="message-input"
                       wire:model="message"
                       wire:keydown.enter="sendMessage"
                       placeholder="Hỏi tôi về nấu ăn, công thức món ăn, mẹo hay..."
                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-transparent"
                       {{ $isLoading ? 'disabled' : '' }}>
            </div>
            <button type="submit" 
                    wire:loading.attr="disabled"
                    wire:loading.class="opacity-50 cursor-not-allowed"
                    class="px-6 py-3 bg-orange-600 text-white rounded-lg hover:bg-orange-700 transition-colors disabled:opacity-50 disabled:cursor-not-allowed flex items-center">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/>
                </svg>
            </button>
        </form>
    </div>
</div>

@push('scripts')
<style>
@keyframes fade-in-up {
    from {
        opacity: 0;
        transform: translateY(10px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.animate-fade-in-up {
    animation: fade-in-up 0.3s ease-out forwards;
}

.markdown-content h1, .markdown-content h2, .markdown-content h3, .markdown-content h4, .markdown-content h5, .markdown-content h6 {
    font-weight: bold;
    margin-top: 1rem;
    margin-bottom: 0.5rem;
}
.markdown-content h1 { font-size: 1.25rem; }
.markdown-content h2 { font-size: 1.125rem; }
.markdown-content h3 { font-size: 1rem; }
.markdown-content p {
    margin-bottom: 0.75rem;
}
.markdown-content ul, .markdown-content ol {
    margin-bottom: 0.75rem;
    padding-left: 1.5rem;
}
.markdown-content ul {
    list-style-type: disc;
}
.markdown-content ol {
    list-style-type: decimal;
}
.markdown-content li {
    margin-bottom: 0.25rem;
}
.markdown-content strong {
    font-weight: bold;
}
.markdown-content em {
    font-style: italic;
}
.markdown-content code {
    background-color: #f3f4f6;
    padding: 0.125rem 0.25rem;
    border-radius: 0.25rem;
    font-family: ui-monospace, SFMono-Regular, "SF Mono", Consolas, "Liberation Mono", Menlo, monospace;
    font-size: 0.875em;
}
.markdown-content pre {
    background-color: #1f2937;
    color: #f9fafb;
    padding: 0.75rem;
    border-radius: 0.5rem;
    overflow-x: auto;
    margin-bottom: 0.75rem;
}
.markdown-content pre code {
    background-color: transparent;
    padding: 0;
    color: inherit;
}
.markdown-content blockquote {
    border-left: 4px solid #d1d5db;
    padding-left: 1rem;
    margin-bottom: 0.75rem;
    font-style: italic;
    color: #6b7280;
}
</style>

<script>
document.addEventListener('livewire:init', () => {
    Livewire.on('scroll-to-bottom', () => {
        setTimeout(() => {
            const chatMessages = document.getElementById('chat-messages');
            if (chatMessages) {
                chatMessages.scrollTop = chatMessages.scrollHeight;
            }
        }, 100);
    });
});

// Auto-scroll to bottom when new messages are added
document.addEventListener('DOMContentLoaded', function() {
    const chatMessages = document.getElementById('chat-messages');
    if (chatMessages) {
        const observer = new MutationObserver(() => {
            chatMessages.scrollTop = chatMessages.scrollHeight;
        });
        observer.observe(chatMessages, { childList: true, subtree: true });
    }
    
    // Add enter key support for message input
    const messageInput = document.querySelector('textarea[wire\\:model="message"]');
    if (messageInput) {
        messageInput.addEventListener('keydown', function(e) {
            if (e.key === 'Enter' && !e.shiftKey) {
                e.preventDefault();
                const form = messageInput.closest('form');
                if (form) {
                    form.dispatchEvent(new Event('submit', { bubbles: true, cancelable: true }));
                }
            }
        });
    }
});
</script>
@endpush
