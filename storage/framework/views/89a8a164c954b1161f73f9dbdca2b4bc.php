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
                    <h3 class="text-lg font-semibold text-gray-900">AI Cooking Assistant</h3>
                    <p class="text-sm text-gray-500">Sẵn sàng hỗ trợ bạn nấu ăn</p>
                </div>
            </div>
            
            <!-- Chat Actions -->
            <div class="flex items-center space-x-2">
                <!--[if BLOCK]><![endif]--><?php if($hasConversation): ?>
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
                <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
            </div>
        </div>
        
        <!-- Conversation Stats -->
        <!--[if BLOCK]><![endif]--><?php if($hasConversation && $showHistory): ?>
            <div class="mt-4 p-3 bg-gray-50 rounded-lg">
                <div class="flex items-center justify-between text-sm text-gray-600">
                    <span>Tổng tin nhắn: <?php echo e($conversationStats['total_messages']); ?></span>
                    <span>Bạn: <?php echo e($conversationStats['user_messages']); ?> | AI: <?php echo e($conversationStats['ai_messages']); ?></span>
                </div>
            </div>
        <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
    </div>

    <!-- Category Tabs -->
    <div class="bg-white border-l border-r border-gray-200 px-6 py-3">
        <div class="flex space-x-1 overflow-x-auto">
            <button wire:click="$set('selectedCategory', 'general')" 
                    class="px-4 py-2 text-sm font-medium rounded-lg transition-colors whitespace-nowrap <?php echo e($selectedCategory === 'general' ? 'bg-orange-100 text-orange-700' : 'text-gray-600 hover:text-gray-900 hover:bg-gray-100'); ?>">
                <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                </svg>
                Chung
            </button>
            <button wire:click="$set('selectedCategory', 'ingredients')" 
                    class="px-4 py-2 text-sm font-medium rounded-lg transition-colors whitespace-nowrap <?php echo e($selectedCategory === 'ingredients' ? 'bg-orange-100 text-orange-700' : 'text-gray-600 hover:text-gray-900 hover:bg-gray-100'); ?>">
                <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                </svg>
                Nguyên liệu
            </button>
            <button wire:click="$set('selectedCategory', 'tips')" 
                    class="px-4 py-2 text-sm font-medium rounded-lg transition-colors whitespace-nowrap <?php echo e($selectedCategory === 'tips' ? 'bg-orange-100 text-orange-700' : 'text-gray-600 hover:text-gray-900 hover:bg-gray-100'); ?>">
                <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/>
                </svg>
                Mẹo hay
            </button>
            <button wire:click="$set('selectedCategory', 'nutrition')" 
                    class="px-4 py-2 text-sm font-medium rounded-lg transition-colors whitespace-nowrap <?php echo e($selectedCategory === 'nutrition' ? 'bg-orange-100 text-orange-700' : 'text-gray-600 hover:text-gray-900 hover:bg-gray-100'); ?>">
                <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                </svg>
                Dinh dưỡng
            </button>
        </div>
    </div>

    <!-- Ingredients Section (shown when ingredients category is selected) -->
    <!--[if BLOCK]><![endif]--><?php if($selectedCategory === 'ingredients'): ?>
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
            <!--[if BLOCK]><![endif]--><?php if(count($ingredients) > 0): ?>
                <div class="flex flex-wrap gap-2 mb-3">
                    <!--[if BLOCK]><![endif]--><?php $__currentLoopData = $ingredients; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $ingredient): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm bg-orange-100 text-orange-800">
                            <?php echo e($ingredient); ?>

                            <button wire:click="removeIngredient(<?php echo e($index); ?>)" 
                                    class="ml-2 hover:text-orange-600">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                            </button>
                        </span>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><!--[if ENDBLOCK]><![endif]-->
                </div>
                <button wire:click="getRecipeSuggestions" 
                        class="w-full px-4 py-2 bg-orange-600 text-white rounded-lg hover:bg-orange-700 transition-colors text-sm font-medium">
                    Gợi ý món ăn từ nguyên liệu này
                </button>
            <?php else: ?>
                <p class="text-sm text-gray-500 italic">Chưa có nguyên liệu nào. Hãy thêm nguyên liệu để nhận gợi ý!</p>
            <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
        </div>
    <?php endif; ?><!--[if ENDBLOCK]><![endif]-->

    <!-- Quick Suggestions -->
    <div class="bg-white border-l border-r border-gray-200 px-6 py-4">
        <h4 class="text-sm font-medium text-gray-900 mb-3">Gợi ý nhanh:</h4>
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
            <!--[if BLOCK]><![endif]--><?php $__currentLoopData = $quickSuggestions[$selectedCategory]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $suggestion): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <button wire:click="selectQuickSuggestion('<?php echo e($suggestion); ?>')" 
                        class="text-left px-3 py-2 text-sm text-gray-700 bg-gray-50 rounded-lg hover:bg-orange-50 hover:text-orange-700 transition-colors">
                    <?php echo e($suggestion); ?>

                </button>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><!--[if ENDBLOCK]><![endif]-->
        </div>
    </div>

    <!-- Chat Messages -->
    <div class="bg-white border-l border-r border-gray-200">
        <div id="chat-messages" class="h-96 overflow-y-auto p-6 space-y-4">
            <!--[if BLOCK]><![endif]--><?php if(count($conversation) === 0): ?>
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
            <?php else: ?>
                <!--[if BLOCK]><![endif]--><?php $__currentLoopData = $conversation; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $message): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div class="flex <?php echo e($message['role'] === 'user' ? 'justify-end' : 'justify-start'); ?>">
                        <div class="flex max-w-xs lg:max-w-md <?php echo e($message['role'] === 'user' ? 'flex-row-reverse' : 'flex-row'); ?>">
                            <div class="flex-shrink-0">
                                <div class="w-8 h-8 rounded-full <?php echo e($message['role'] === 'user' ? 'bg-gray-300 ml-3' : 'bg-orange-500 mr-3'); ?> flex items-center justify-center">
                                    <!--[if BLOCK]><![endif]--><?php if($message['role'] === 'user'): ?>
                                        <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                        </svg>
                                    <?php else: ?>
                                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                                        </svg>
                                    <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                                </div>
                            </div>
                            <div>
                                <div class="px-4 py-2 rounded-lg <?php echo e($message['role'] === 'user' ? 'bg-orange-600 text-white' : ($message['is_error'] ?? false ? 'bg-red-50 text-red-800 border border-red-200' : 'bg-gray-100 text-gray-900')); ?>">
                                    <!--[if BLOCK]><![endif]--><?php if($message['role'] === 'assistant' && isset($message['content_html'])): ?>
                                        <div class="text-sm markdown-content">
                                            <?php echo $message['content_html']; ?>

                                        </div>
                                    <?php else: ?>
                                        <p class="text-sm whitespace-pre-wrap"><?php echo e($message['content']); ?></p>
                                    <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                                </div>
                                <p class="text-xs text-gray-500 mt-1 <?php echo e($message['role'] === 'user' ? 'text-right' : 'text-left'); ?>">
                                    <?php echo e($message['timestamp']); ?>

                                </p>
                            </div>
                        </div>
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><!--[if ENDBLOCK]><![endif]-->
            <?php endif; ?><!--[if ENDBLOCK]><![endif]-->

            <!-- Loading indicator -->
            <!--[if BLOCK]><![endif]--><?php if($isLoading): ?>
                <div class="flex justify-start">
                    <div class="flex items-center space-x-2 px-4 py-2 bg-gray-100 rounded-lg">
                        <div class="flex space-x-1">
                            <div class="w-2 h-2 bg-gray-400 rounded-full animate-bounce"></div>
                            <div class="w-2 h-2 bg-gray-400 rounded-full animate-bounce" style="animation-delay: 0.1s"></div>
                            <div class="w-2 h-2 bg-gray-400 rounded-full animate-bounce" style="animation-delay: 0.2s"></div>
                        </div>
                        <span class="text-sm text-gray-600">AI đang suy nghĩ...</span>
                    </div>
                </div>
            <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
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
                       <?php echo e($isLoading ? 'disabled' : ''); ?>>
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

<?php $__env->startPush('scripts'); ?>
<style>
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
});
</script>
<?php $__env->stopPush(); ?>
<?php /**PATH D:\DuAn1\test\bee\resources\views/livewire/open-ai-chat.blade.php ENDPATH**/ ?>