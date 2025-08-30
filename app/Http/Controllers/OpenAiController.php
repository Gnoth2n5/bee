<?php

namespace App\Http\Controllers;

use App\Services\OpenAiService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;

class OpenAiController extends Controller
{
    public function __construct(
        private OpenAiService $openAiService
    ) {
    }

    /**
     * Display the AI chat interface
     */
    public function index(): View
    {
        // Check if DeepSeek is configured
        if (!$this->openAiService->isConfigured()) {
            abort(503, 'Dịch vụ AI chưa được cấu hình. Vui lòng liên hệ admin.');
        }

        return view('openai.index');
    }

    /**
     * Send a message to DeepSeek and get response
     */
    public function sendMessage(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'message' => 'required|string|max:2000',
            'conversation_history' => 'array|max:20'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'error' => 'Tin nhắn không hợp lệ.'
            ], 422);
        }

        // Check if OpenAI is configured
        if (!$this->openAiService->isConfigured()) {
            return response()->json([
                'success' => false,
                'error' => 'Dịch vụ AI chưa được cấu hình.'
            ], 503);
        }

        $message = $request->input('message');
        $conversationHistory = $request->input('conversation_history', []);

        // Limit conversation history to prevent token overflow
        $conversationHistory = array_slice($conversationHistory, -10);

        $result = $this->openAiService->sendMessage($message, $conversationHistory);

        if ($result['success']) {
            // Store conversation in session for this user
            $this->storeConversationInSession($message, $result['message']);

            return response()->json([
                'success' => true,
                'message' => $result['message'],
                'usage' => $result['usage'] ?? []
            ]);
        }

        return response()->json([
            'success' => false,
            'error' => $result['error']
        ], 500);
    }

    /**
     * Get recipe suggestions based on ingredients
     */
    public function getRecipeSuggestions(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'ingredients' => 'required|array|min:1|max:20',
            'ingredients.*' => 'string|max:100'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'error' => 'Danh sách nguyên liệu không hợp lệ.'
            ], 422);
        }

        if (!$this->openAiService->isConfigured()) {
            return response()->json([
                'success' => false,
                'error' => 'Dịch vụ AI chưa được cấu hình.'
            ], 503);
        }

        $ingredients = $request->input('ingredients');
        $result = $this->openAiService->getRecipeSuggestions($ingredients);

        if ($result['success']) {
            return response()->json([
                'success' => true,
                'suggestions' => $result['message']
            ]);
        }

        return response()->json([
            'success' => false,
            'error' => $result['error']
        ], 500);
    }

    /**
     * Get cooking tips
     */
    public function getCookingTips(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'dish_type' => 'nullable|string|max:200'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'error' => 'Loại món ăn không hợp lệ.'
            ], 422);
        }

        if (!$this->openAiService->isConfigured()) {
            return response()->json([
                'success' => false,
                'error' => 'Dịch vụ AI chưa được cấu hình.'
            ], 503);
        }

        $dishType = $request->input('dish_type', '');
        $result = $this->openAiService->getCookingTips($dishType);

        if ($result['success']) {
            return response()->json([
                'success' => true,
                'tips' => $result['message']
            ]);
        }

        return response()->json([
            'success' => false,
            'error' => $result['error']
        ], 500);
    }

    /**
     * Analyze a recipe
     */
    public function analyzeRecipe(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'recipe_content' => 'required|string|max:5000'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'error' => 'Nội dung công thức không hợp lệ.'
            ], 422);
        }

        if (!$this->openAiService->isConfigured()) {
            return response()->json([
                'success' => false,
                'error' => 'Dịch vụ AI chưa được cấu hình.'
            ], 503);
        }

        $recipeContent = $request->input('recipe_content');
        $result = $this->openAiService->analyzeRecipe($recipeContent);

        if ($result['success']) {
            return response()->json([
                'success' => true,
                'analysis' => $result['message']
            ]);
        }

        return response()->json([
            'success' => false,
            'error' => $result['error']
        ], 500);
    }

    /**
     * Get nutritional information
     */
    public function getNutritionalInfo(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'dish' => 'required|string|max:200'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'error' => 'Tên món ăn không hợp lệ.'
            ], 422);
        }

        if (!$this->openAiService->isConfigured()) {
            return response()->json([
                'success' => false,
                'error' => 'Dịch vụ AI chưa được cấu hình.'
            ], 503);
        }

        $dish = $request->input('dish');
        $result = $this->openAiService->getNutritionalInfo($dish);

        if ($result['success']) {
            return response()->json([
                'success' => true,
                'nutritional_info' => $result['message']
            ]);
        }

        return response()->json([
            'success' => false,
            'error' => $result['error']
        ], 500);
    }

    /**
     * Get conversation history from session
     */
    public function getConversationHistory(): JsonResponse
    {
        $history = Session::get('openai_conversation', []);
        
        return response()->json([
            'success' => true,
            'history' => $history
        ]);
    }

    /**
     * Clear conversation history
     */
    public function clearConversationHistory(): JsonResponse
    {
        Session::forget('openai_conversation');
        
        return response()->json([
            'success' => true,
            'message' => 'Lịch sử trò chuyện đã được xóa.'
        ]);
    }

    /**
     * Store conversation in session
     */
    private function storeConversationInSession(string $userMessage, string $aiResponse): void
    {
        $conversation = Session::get('openai_conversation', []);
        
        // Add user message
        $conversation[] = [
            'role' => 'user',
            'content' => $userMessage,
            'timestamp' => now()->toISOString()
        ];
        
        // Add AI response
        $conversation[] = [
            'role' => 'assistant',
            'content' => $aiResponse,
            'timestamp' => now()->toISOString()
        ];
        
        // Keep only last 20 messages to prevent session from growing too large
        $conversation = array_slice($conversation, -20);
        
        Session::put('openai_conversation', $conversation);
    }

    /**
     * Get quick recipe suggestions for the homepage
     */
    public function getQuickSuggestions(): JsonResponse
    {
        if (!$this->openAiService->isConfigured()) {
            return response()->json([
                'success' => false,
                'error' => 'Dịch vụ AI chưa được cấu hình.'
            ], 503);
        }

        $suggestions = [
            "Món ăn nhanh cho bữa sáng",
            "Công thức món chay healthy", 
            "Cách nấu cơm ngon",
            "Món súp dinh dưỡng",
            "Bánh ngọt đơn giản",
            "Món ăn vặt dễ làm"
        ];

        return response()->json([
            'success' => true,
            'suggestions' => $suggestions
        ]);
    }
}
