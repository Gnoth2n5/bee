<?php

namespace App\Livewire;

use App\Services\OpenAiService;
use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use League\CommonMark\CommonMarkConverter;

#[Layout('layouts.app')]
class OpenAiChat extends Component
{
    public $message = '';
    public $conversation = [];
    public $isLoading = false;
    public $selectedCategory = 'general';
    public $ingredients = [];
    public $newIngredient = '';
    public $showHistory = false;

    // Quick action suggestions
    public $quickSuggestions = [
        'general' => [
            'Gợi ý món ăn cho hôm nay',
            'Cách nấu phở bò ngon',
            'Mẹo bảo quản thực phẩm tươi',
            'Món ăn vặt dễ làm'
        ],
        'ingredients' => [
            'Món ngon từ thịt gà',
            'Chế biến rau củ quả',
            'Công thức từ trứng',
            'Món chay healthy'
        ],
        'tips' => [
            'Mẹo nấu cơm ngon',
            'Cách ướp thịt đúng cách',
            'Làm sao để giữ rau xanh',
            'Bí quyết nấu nước dùng trong'
        ],
        'nutrition' => [
            'Thông tin dinh dưỡng phở',
            'Calories trong các món phổ biến',
            'Thực phẩm giàu protein',
            'Món ăn tốt cho sức khỏe'
        ]
    ];

    protected $openAiService;
    protected $markdownConverter;

    public function boot(OpenAiService $openAiService)
    {
        $this->openAiService = $openAiService;
        $this->markdownConverter = new CommonMarkConverter([
            'html_input' => 'strip',
            'allow_unsafe_links' => false,
        ]);
    }

    public function mount()
    {
        // Check if user is VIP, redirect if not
        if (!Auth::check() || !Auth::user()->isVip()) {
            return redirect()->route('vip.upgrade')->with('error', 'Tính năng AI Chat chỉ dành cho thành viên VIP. Vui lòng nâng cấp tài khoản.');
        }

        $this->loadConversationFromSession();

        // Ensure string properties are properly initialized
        $this->message = is_string($this->message) ? $this->message : '';
        $this->newIngredient = is_string($this->newIngredient) ? $this->newIngredient : '';
    }

    public function sendMessage()
    {
        // Ensure message is always a string
        Log::info('sendMessage called', [
            'message' => $this->message,
            'message_type' => gettype($this->message),
            'is_empty' => empty(trim($this->message ?? ''))
        ]);

        if (is_array($this->message)) {

            $this->message = '';
            return;
        }

        if (empty(trim($this->message ?? ''))) {
            return;
        }

        if (!$this->openAiService->isConfigured()) {
            session()->flash('error', 'Dịch vụ AI chưa được cấu hình. Vui lòng liên hệ admin.');
            return;
        }

        $userMessage = trim($this->message);

        // Add user message to conversation
        $this->conversation[] = [
            'role' => 'user',
            'content' => $userMessage,
            'timestamp' => now()->format('H:i'),
            'avatar' => Auth::user()?->avatar ?? '/images/default-avatar.png'
        ];

        // Clear message input
        $this->message = '';

        // Save conversation to session immediately
        $this->storeConversationInSession();

        // Dispatch scroll to show user message immediately
        $this->dispatch('scroll-to-bottom');

        // Trigger AI response fetch on the client side
        $this->dispatch('start-ai-response', userMessage: $userMessage);
    }

    #[On('fetch-ai-response')]
    public function fetchAiResponse($userMessage): void
    {
        try {
            // Send to OpenAI - use recipe suggestions method for better results
            $result = $this->openAiService->getRecipeSuggestions($userMessage, Auth::user());

            if ($result['success']) {
                Log::info('OpenRouter API result', [
                    'result' => $result
                ]);

                $conversationEntry = [
                    'role' => 'assistant',
                    'content' => $result['message'],
                    'content_html' => $this->parseMarkdown($result['message']),
                    'timestamp' => now()->format('H:i'),
                    'avatar' => '/images/ai-avatar.png'
                ];

                // Add recipe data if available
                if (isset($result['recipes']) && !empty($result['recipes'])) {
                    $conversationEntry['recipes'] = $result['recipes'];
                    Log::info('Recipe data added to conversation', [
                        'recipe_count' => count($result['recipes']),
                        'recipe_titles' => array_column($result['recipes'], 'title')
                    ]);
                } else {
                    Log::info('No recipe data in AI response');
                }

                $this->conversation[] = $conversationEntry;

                $this->storeConversationInSession();
                $this->dispatch('scroll-to-bottom');
            } else {
                $errorMessage = 'Xin lỗi, đã có lỗi xảy ra: ' . $result['error'];
                $this->conversation[] = [
                    'role' => 'assistant',
                    'content' => $errorMessage,
                    'content_html' => $this->parseMarkdown($errorMessage),
                    'timestamp' => now()->format('H:i'),
                    'avatar' => '/images/ai-avatar.png',
                    'is_error' => true
                ];

                $this->storeConversationInSession();
            }
        } catch (\Exception $e) {
            Log::error('Error in fetchAiResponse', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            $errorMessage = 'Xin lỗi, đã có lỗi kết nối. Vui lòng thử lại sau.';
            $this->conversation[] = [
                'role' => 'assistant',
                'content' => $errorMessage,
                'content_html' => $this->parseMarkdown($errorMessage),
                'timestamp' => now()->format('H:i'),
                'avatar' => '/images/ai-avatar.png',
                'is_error' => true
            ];

            $this->storeConversationInSession();
        } finally {
            $this->isLoading = false;
        }
    }

    public function addIngredient()
    {
        // Ensure newIngredient is always a string
        if (is_array($this->newIngredient)) {
            $this->newIngredient = '';
            return;
        }

        if (empty(trim($this->newIngredient))) {
            return;
        }

        $ingredient = trim($this->newIngredient);

        if (!in_array($ingredient, $this->ingredients)) {
            $this->ingredients[] = $ingredient;
        }

        $this->newIngredient = '';
    }

    public function removeIngredient($index)
    {
        unset($this->ingredients[$index]);
        $this->ingredients = array_values($this->ingredients);
    }

    public function getRecipeSuggestions()
    {
        if (empty($this->ingredients)) {
            session()->flash('error', 'Vui lòng thêm ít nhất 1 nguyên liệu.');
            return;
        }

        $ingredientsList = implode(', ', $this->ingredients);
        $this->message = "Tôi có những nguyên liệu sau: {$ingredientsList}. Hãy gợi ý cho tôi các món ăn có thể làm từ những nguyên liệu này.";
        $this->sendMessage();
    }

    public function selectQuickSuggestion($suggestion)
    {
        $this->message = $suggestion;
        $this->sendMessage();
    }

    public function clearConversation()
    {
        $this->conversation = [];
        Session::forget('openai_conversation');
        session()->flash('success', 'Đã xóa lịch sử trò chuyện.');
    }

    public function toggleHistory()
    {
        $this->showHistory = !$this->showHistory;
    }

    #[On('openai-quick-message')]
    public function handleQuickMessage($message)
    {
        $this->message = $message;
        $this->sendMessage();
    }

    private function loadConversationFromSession()
    {
        $sessionConversation = Session::get('openai_conversation', []);

        $this->conversation = collect($sessionConversation)->map(function ($msg) {
            $conversationMsg = [
                'role' => $msg['role'],
                'content' => $msg['content'],
                'content_html' => $msg['role'] === 'assistant' ? $this->parseMarkdown($msg['content']) : null,
                'timestamp' => isset($msg['timestamp']) ?
                    \Carbon\Carbon::parse($msg['timestamp'])->format('H:i') :
                    now()->format('H:i'),
                'avatar' => $msg['role'] === 'user' ?
                    (Auth::user()?->avatar ?? '/images/default-avatar.png') :
                    '/images/ai-avatar.png',
                'is_error' => $msg['is_error'] ?? false
            ];

            // Restore recipe data if available
            if (isset($msg['recipes'])) {
                $conversationMsg['recipes'] = $msg['recipes'];
            }

            return $conversationMsg;
        })->toArray();
    }

    private function storeConversationInSession()
    {
        $sessionData = collect($this->conversation)->map(function ($msg) {
            $sessionMsg = [
                'role' => $msg['role'],
                'content' => $msg['content'],
                'timestamp' => now()->toISOString(),
                'is_error' => $msg['is_error'] ?? false
            ];

            // Include recipe data if available
            if (isset($msg['recipes'])) {
                $sessionMsg['recipes'] = $msg['recipes'];
            }

            return $sessionMsg;
        })->slice(-20)->values()->toArray(); // Keep last 20 messages

        Session::put('openai_conversation', $sessionData);
    }

    public function getConversationStatsProperty()
    {
        return [
            'total_messages' => count($this->conversation),
            'user_messages' => collect($this->conversation)->where('role', 'user')->count(),
            'ai_messages' => collect($this->conversation)->where('role', 'assistant')->count(),
        ];
    }

    /**
     * Parse markdown content to HTML
     */
    private function parseMarkdown(string $content): string
    {
        try {
            return $this->markdownConverter->convert($content)->getContent();
        } catch (\Exception $e) {
            Log::warning('Failed to parse markdown content', [
                'error' => $e->getMessage(),
                'content' => substr($content, 0, 100) . '...'
            ]);
            // Fallback to plain text with basic line breaks
            return nl2br(e($content));
        }
    }

    public function testConnection()
    {
        Log::info('Test connection method called');
        session()->flash('success', 'Test connection successful!');
    }

    public function render()
    {
        return view('livewire.open-ai-chat', [
            'conversationStats' => $this->conversationStats,
            'hasConversation' => count($this->conversation) > 0
        ]);
    }
}
