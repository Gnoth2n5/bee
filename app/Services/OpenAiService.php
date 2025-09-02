<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Exception;


class OpenAiService
{
    protected $apiKey;
    protected $baseUrl = 'https://openrouter.ai/api/v1';
    protected static $circuitBreakerKey = 'openai_service_circuit_breaker';
    protected static $failureThreshold = 5; // Number of failures before opening circuit
    protected static $recoveryTimeout = 300; // 5 minutes
    protected $geminiService;

    public function __construct(GeminiService $geminiService)
    {
        $this->apiKey = env('OPEN_AI_API_KEY');
        $this->geminiService = $geminiService;
    }

    /**
     * Check if circuit breaker is open (service is down)
     */
    protected function isCircuitBreakerOpen(): bool
    {
        $failureData = Cache::get(self::$circuitBreakerKey);

        if (!$failureData) {
            return false;
        }

        // If we've hit the failure threshold and haven't passed recovery timeout
        if (
            $failureData['count'] >= self::$failureThreshold &&
            time() - $failureData['last_failure'] < self::$recoveryTimeout
        ) {
            return true;
        }

        // Reset if recovery timeout has passed
        if (time() - $failureData['last_failure'] >= self::$recoveryTimeout) {
            cache()->forget(self::$circuitBreakerKey);
        }

        return false;
    }

    /**
     * Record a failure in the circuit breaker
     */
    protected function recordFailure(): void
    {
        $failureData = Cache::get(self::$circuitBreakerKey, ['count' => 0, 'last_failure' => time()]);
        $failureData['count']++;
        $failureData['last_failure'] = time();

        Cache::put(self::$circuitBreakerKey, $failureData, self::$recoveryTimeout * 2);
    }

    /**
     * Record a success in the circuit breaker (reset failures)
     */
    protected function recordSuccess(): void
    {
        Cache::forget(self::$circuitBreakerKey);
    }

    /**
     * Test OpenAI API connection
     */
    public function testConnection()
    {
        try {
            if (!$this->apiKey) {
                return [
                    'success' => false,
                    'error' => 'OpenAI API key chưa được cấu hình.'
                ];
            }

            $payload = [
                'model' => 'gpt-3.5-turbo',
                'messages' => [
                    [
                        'role' => 'user',
                        'content' => 'Xin chào! Hãy trả lời "API hoạt động tốt!"'
                    ]
                ],
                'max_tokens' => 50,
                'temperature' => 0.7,
            ];

            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->apiKey,
                'Content-Type' => 'application/json',
            ])->withOptions([
                'verify' => env('APP_ENV') === 'production' ? true : false,
                'timeout' => 10,
            ])->post($this->baseUrl . '/chat/completions', $payload);

            if ($response->successful()) {
                $data = $response->json();
                $messageContent = $data['choices'][0]['message']['content'] ?? '';

                return [
                    'success' => true,
                    'message' => 'API hoạt động tốt!',
                    'response' => $messageContent
                ];
            }

            return [
                'success' => false,
                'error' => 'API test failed: ' . $response->status() . ' - ' . $response->body()
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'error' => 'API test error: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Send a chat message to OpenAI and get response
     */
    public function sendMessage(string $message, array $conversationHistory = [])
    {
        try {
            // Check circuit breaker first
            if ($this->isCircuitBreakerOpen()) {
                Log::warning('OpenAI service circuit breaker is open');
                return [
                    'success' => false,
                    'error' => 'Dịch vụ AI hiện tại không khả dụng do quá nhiều lỗi. Vui lòng thử lại sau 5 phút.'
                ];
            }

            // Memory optimization: Set memory limit for this operation
            if (function_exists('ini_set')) {
                ini_set('memory_limit', '256M');
            }

            if (!$this->apiKey) {
                return [
                    'success' => false,
                    'error' => 'OpenAI API key chưa được cấu hình. Vui lòng kiểm tra cài đặt.'
                ];
            }

            // Prepare system message for recipe assistance
            $systemMessage = [
                'role' => 'system',
                'content' => 'Bạn là trợ lý AI chuyên về nấu ăn và công thức món ăn. Hãy trả lời bằng tiếng Việt một cách thân thiện và hữu ích. Khi được hỏi về công thức, hãy đưa ra hướng dẫn chi tiết bao gồm nguyên liệu, cách làm, thời gian nấu và mẹo hay. Luôn đảm bảo thông tin chính xác và an toàn thực phẩm. Hãy trả lời một cách ngắn gọn và dễ hiểu. QUAN TRỌNG: Trả lời trực tiếp và ngắn gọn, KHÔNG sử dụng cấu trúc reasoning, KHÔNG giải thích quá trình suy nghĩ, KHÔNG sử dụng các trường reasoning. Chỉ đưa ra câu trả lời trực tiếp trong trường content.'
            ];

            // Build messages array
            $messages = [$systemMessage];

            // Add conversation history (limit to last 5 messages to save memory - reduced from 8)
            $limitedHistory = array_slice($conversationHistory, -5);
            foreach ($limitedHistory as $msg) {
                $messages[] = [
                    'role' => $msg['role'],
                    'content' => mb_substr($msg['content'], 0, 1000) // Limit message length to 1000 chars
                ];
            }

            // Add current message (with length limit)
            $messages[] = [
                'role' => 'user',
                'content' => mb_substr($message, 0, 2000) // Limit to 2000 chars
            ];


            $payload = [
                'model' => 'deepseek/deepseek-chat-v3-0324:free',
                'messages' => $messages,
                'max_tokens' => 1000,
                'temperature' => 0.7,
                'top_p' => 0.9,
                'stream' => false,
            ];

            // Implement retry logic with exponential backoff
            $maxRetries = 3;
            $baseDelay = 1; // seconds

            for ($attempt = 1; $attempt <= $maxRetries; $attempt++) {
                try {
                    $timeout = min(15 + ($attempt * 5), 30); // Progressive timeout: 20s, 25s, 30s
                    Log::info('OpenAI API request', [
                        'attempt' => $attempt,
                        'payload' => $payload,
                        'timeout' => $timeout
                    ]);

                    $response = Http::withHeaders([
                        'Authorization' => 'Bearer ' . $this->apiKey,
                        'Content-Type' => 'application/json',
                    ])->withOptions([
                        'verify' => env('APP_ENV') === 'production' ? true : false,
                        'timeout' => $timeout,
                        'connect_timeout' => 10, // Separate connection timeout
                    ])->timeout($timeout)->post($this->baseUrl . '/chat/completions', $payload);


                    // If successful, break out of retry loop
                    if ($response->successful()) {
                        break;
                    }

                    // If not the last attempt and it's a timeout/server error, retry
                    if ($attempt < $maxRetries && ($response->status() >= 500 || $response->status() === 408)) {
                        $delay = $baseDelay * pow(2, $attempt - 1); // Exponential backoff
                        Log::warning("OpenAI API attempt {$attempt} failed, retrying in {$delay}s", [
                            'status' => $response->status(),
                            'attempt' => $attempt
                        ]);
                        sleep($delay);
                        continue;
                    }
                } catch (\Illuminate\Http\Client\ConnectionException $e) {
                    Log::warning("OpenAI connection error on attempt {$attempt}", [
                        'message' => $e->getMessage(),
                        'attempt' => $attempt
                    ]);

                    // If not the last attempt, retry with exponential backoff
                    if ($attempt < $maxRetries) {
                        $delay = $baseDelay * pow(2, $attempt - 1);
                        sleep($delay);
                        continue;
                    }

                    // Re-throw if it's the last attempt
                    throw $e;
                }
            }

            if ($response->successful()) {
                $data = $response->json();
                Log::info('OpenAI API data', [
                    'data' => $data
                ]);

                // Extract message content - try content first, then reasoning field
                $messageContent = $data['choices'][0]['message']['content'] ?? '';

                // If content is empty, try to get from reasoning field (for models like deepseek-r1)
                if (empty($messageContent) && isset($data['choices'][0]['message']['reasoning'])) {
                    $messageContent = $data['choices'][0]['message']['reasoning'];
                }

                // Fallback message if both fields are empty
                if (empty($messageContent)) {
                    $messageContent = 'Xin lỗi, tôi không thể trả lời câu hỏi này.';
                }

                return [
                    'success' => true,
                    'message' => $messageContent,
                    'usage' => $data['usage'] ?? []
                ];
            }

            $errorData = $response->json();
            $errorMessage = $this->getErrorMessage($errorData);

            Log::error('OpenRouter API error', [
                'status' => $response->status(),
                'response' => $errorData
            ]);

            return [
                'success' => false,
                'error' => $errorMessage
            ];
        } catch (\Illuminate\Http\Client\ConnectionException $e) {
            Log::error('OpenRouter connection error', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            // Check if it's a timeout error
            if (str_contains($e->getMessage(), 'timeout') || str_contains($e->getMessage(), 'timed out')) {
                return [
                    'success' => false,
                    'error' => 'Kết nối với AI bị timeout. Hệ thống đang quá tải, vui lòng thử lại sau ít phút.'
                ];
            }

            return [
                'success' => false,
                'error' => 'Không thể kết nối với dịch vụ AI. Vui lòng kiểm tra kết nối mạng và thử lại.'
            ];
        } catch (Exception $e) {
            Log::error('OpenRouter service error', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return [
                'success' => false,
                'error' => 'Có lỗi xảy ra khi kết nối với AI. Vui lòng thử lại sau.'
            ];
        }
    }

    /**
     * Get recipe suggestions based on ingredients or prompt using site's recipe database
     */
    public function getRecipeSuggestions($prompt, $user = null)
    {
        try {
            Log::info('OpenRouter recipe suggestions', [
                'prompt' => $prompt,
                'user_id' => $user?->id,
                'is_vip' => $user?->isVip() ?? false
            ]);

            // Check if user has VIP access for enhanced features
            if ($user && $user->isVip()) {
                return $this->getVipRecipeSuggestions($prompt, $user);
            }

            return $this->recommendFromSite($prompt);
        } catch (Exception $e) {
            Log::error('OpenRouter recipe suggestions error', [
                'message' => $e->getMessage(),
                'prompt' => $prompt
            ]);

            return [
                'success' => false,
                'error' => 'Không thể tạo gợi ý công thức. Vui lòng thử lại.'
            ];
        }
    }

    /**
     * Enhanced recipe suggestions for VIP users
     */
    public function getVipRecipeSuggestions(string $prompt, $user): array
    {
        try {
            // Step 1: Get user preferences and dietary restrictions
            $userPreferences = $this->getUserPreferences($user);

            // Step 2: Enhanced embedding with user context, fallback if fails
            /** @var EmbeddingService $embeddingService */
            $embeddingService = app(EmbeddingService::class);
            $enhancedPrompt = $this->enhancePromptWithUserContext($prompt, $userPreferences);
            $queryVector = $embeddingService->embed($enhancedPrompt);

            if (!is_array($queryVector) || empty($queryVector)) {
                Log::warning('VIP embedding failed, falling back to regular recommendations', [
                    'prompt' => $prompt,
                    'user_id' => $user->id
                ]);
                return $this->recommendFromSite($prompt);
            }

            // Step 3: Enhanced search with more results for VIP
            $candidates = \App\Models\Recipe::query()
                ->where('status', 'approved')
                ->whereNotNull('embedding')
                ->select(['id', 'title', 'summary', 'slug', 'embedding', 'featured_image', 'cooking_time', 'difficulty', 'ingredients'])
                ->get();

            // If no recipes have embeddings, fall back to regular recommendations
            if ($candidates->isEmpty()) {
                Log::info('No VIP recipes with embeddings found, falling back to regular recommendations', ['prompt' => $prompt, 'user_id' => $user->id]);
                return $this->recommendFromSite($prompt);
            }

            $scored = [];
            foreach ($candidates as $recipe) {
                $vec = is_array($recipe->embedding) ? $recipe->embedding : [];
                if (empty($vec)) {
                    continue;
                }

                $score = $this->cosineSimilarity($queryVector, $vec);

                // Apply VIP bonus scoring based on user preferences
                $bonusScore = $this->calculateVipBonusScore($recipe, $userPreferences);
                $finalScore = $score + $bonusScore;

                if ($finalScore > 0) {
                    $scored[] = [
                        'id' => $recipe->id,
                        'title' => $recipe->title,
                        'summary' => $recipe->summary,
                        'slug' => $recipe->slug,
                        'featured_image' => $recipe->featured_image,
                        'cooking_time' => $recipe->cooking_time,
                        'difficulty_level' => $recipe->difficulty,
                        'ingredients' => $recipe->ingredients,
                        'similarity' => round($score, 6),
                        'vip_bonus' => round($bonusScore, 6),
                        'final_score' => round($finalScore, 6),
                    ];
                }
            }

            if (empty($scored)) {
                Log::info('No recipes found in VIP search, falling back to regular recommendations', ['prompt' => $prompt]);
                return $this->recommendFromSite($prompt);
            }

            usort($scored, function ($a, $b) {
                return $b['final_score'] <=> $a['final_score'];
            });

            // VIP users get more results (up to 8 vs 5 for regular users)
            $top = array_slice($scored, 0, 8);

            // Step 4: Enhanced AI response for VIP with nutritional info
            $systemMessage = [
                'role' => 'system',
                'content' => 'Bạn là trợ lý ẩm thực VIP cao cấp. Dựa trên danh sách công thức và sở thích cá nhân, hãy đưa ra lời khuyên chuyên nghiệp. Trả lời bằng tiếng Việt chi tiết, thân thiện và nhiệt tình. Tập trung giới thiệu những món phù hợp mà không cần giải thích tại sao loại bỏ các món khác.'
            ];

            $contextJson = json_encode($top, JSON_UNESCAPED_UNICODE);
            $preferencesJson = json_encode($userPreferences, JSON_UNESCAPED_UNICODE);

            $userMessage = [
                'role' => 'user',
                'content' => "Người dùng hỏi: {$prompt}\n\nDanh sách công thức VIP: {$contextJson}\n\nSở thích cá nhân: {$preferencesJson}\n\nHãy giới thiệu những món ăn tuyệt vời nhất từ danh sách, kèm theo:\n- Lý do đặc biệt phù hợp\n- Giá trị dinh dưỡng nổi bật\n- Mẹo nấu ăn chuyên nghiệp\n- Cách nâng cấp món ăn"
            ];

            $response = $this->sendMessageWithMessages([$systemMessage, $userMessage]);
            if (($response['success'] ?? false) === true) {
                $suggestedRecipes = $this->extractSuggestedRecipes($response['message'], $top);

                if (!empty($suggestedRecipes)) {
                    // Add VIP-specific data to recipes
                    foreach ($suggestedRecipes as &$recipe) {
                        $recipe['is_vip_recommendation'] = true;
                        $recipe['nutritional_info'] = $this->generateNutritionalInfo($recipe);
                        $recipe['cooking_tips'] = $this->generateCookingTips($recipe);
                    }

                    $response['recipes'] = $suggestedRecipes;
                    $response['vip_features'] = [
                        'enhanced_scoring' => true,
                        'personalized_recommendations' => true,
                        'nutritional_analysis' => true,
                        'advanced_tips' => true
                    ];
                }
            }

            return $response;
        } catch (Exception $e) {
            Log::error('VIP recommendation flow failed', [
                'message' => $e->getMessage(),
                'query' => $prompt,
                'user_id' => $user->id
            ]);

            // Fallback to regular recommendations
            return $this->recommendFromSite($prompt);
        }
    }

    /**
     * Recommend recipes from site's current database using semantic similarity.
     * Flow: 1) Embed query 2) Vector similarity against Recipe.embeddings 3) Ask LLM to personalize.
     */
    public function recommendFromSite(string $prompt): array
    {
        try {
            // Step 1: Try to embed the query, fallback to keyword search if fails
            /** @var EmbeddingService $embeddingService */
            $embeddingService = app(EmbeddingService::class);
            $queryVector = $embeddingService->embed($prompt);
            if (!is_array($queryVector) || empty($queryVector)) {
                Log::warning('Embedding failed, falling back to keyword search', ['prompt' => $prompt]);
                return $this->keywordBasedRecommendation($prompt);
            }

            // Step 2: Search DB by vector similarity (cosine)
            $candidates = \App\Models\Recipe::query()
                ->where('status', 'approved')
                ->whereNotNull('embedding')
                ->select(['id', 'title', 'summary', 'slug', 'embedding', 'featured_image', 'cooking_time', 'difficulty'])
                ->get();

            // If no recipes have embeddings, fall back to keyword search
            if ($candidates->isEmpty()) {
                Log::info('No recipes with embeddings found, falling back to keyword search', ['prompt' => $prompt]);
                return $this->keywordBasedRecommendation($prompt);
            }

            $scored = [];
            foreach ($candidates as $recipe) {
                $vec = is_array($recipe->embedding) ? $recipe->embedding : [];
                if (empty($vec)) {
                    continue;
                }
                $score = $this->cosineSimilarity($queryVector, $vec);
                if ($score > 0) {
                    $scored[] = [
                        'id' => $recipe->id,
                        'title' => $recipe->title,
                        'summary' => $recipe->summary,
                        'slug' => $recipe->slug,
                        'featured_image' => $recipe->featured_image,
                        'cooking_time' => $recipe->cooking_time,
                        'difficulty_level' => $recipe->difficulty,
                        'similarity' => round($score, 6),
                    ];
                }
            }

            if (empty($scored)) {
                Log::info('No recipes found with embedding search, falling back to AI chat', ['prompt' => $prompt]);
                return $this->sendMessage($prompt);
            }

            usort($scored, function ($a, $b) {
                return $b['similarity'] <=> $a['similarity'];
            });
            $top = array_slice($scored, 0, max(3, 5));

            // Step 3: Send to LLM for personalized recommendation
            $systemMessage = [
                'role' => 'system',
                'content' => 'Bạn là trợ lý ẩm thực thân thiện. Dựa trên danh sách công thức có sẵn, hãy đề xuất những món phù hợp nhất với yêu cầu của người dùng. Trả lời bằng tiếng Việt một cách tự nhiên và nhiệt tình. Chỉ giới thiệu các món ăn có trong danh sách mà không cần giải thích tại sao không chọn món khác.'
            ];

            $contextJson = json_encode($top, JSON_UNESCAPED_UNICODE);
            $userMessage = [
                'role' => 'user',
                'content' => "Người dùng hỏi: {$prompt}\n\nDanh sách công thức có sẵn: {$contextJson}\n\nHãy giới thiệu những món ăn phù hợp nhất từ danh sách trên một cách tự nhiên và hấp dẫn. Tập trung vào những điểm hay của từng món."
            ];

            $response = $this->sendMessageWithMessages([$systemMessage, $userMessage]);
            if (($response['success'] ?? false) === true) {
                // Parse the AI response to extract only the suggested recipes
                $suggestedRecipes = $this->extractSuggestedRecipes($response['message'], $top);

                // Attach only the suggested recipes, not all candidates
                if (!empty($suggestedRecipes)) {
                    $response['recipes'] = $suggestedRecipes;
                }
            }
            return $response;
        } catch (Exception $e) {
            Log::error('Recommendation flow failed', [
                'message' => $e->getMessage(),
                'query' => $prompt,
            ]);
            return [
                'success' => false,
                'error' => 'Không thể tạo gợi ý dựa trên dữ liệu trang. Vui lòng thử lại.'
            ];
        }
    }

    /**
     * Get cooking tips and tricks
     */
    public function getCookingTips(string $dishType = '')
    {
        try {
            $message = $dishType
                ? "Hãy cho tôi một số mẹo nấu ăn hay cho món {$dishType}."
                : "Hãy cho tôi một số mẹo nấu ăn hay để cải thiện kỹ năng nấu nướng.";

            return $this->sendMessage($message);
        } catch (Exception $e) {
            Log::error('OpenRouter cooking tips error', [
                'message' => $e->getMessage(),
                'dish_type' => $dishType
            ]);

            return [
                'success' => false,
                'error' => 'Không thể lấy mẹo nấu ăn. Vui lòng thử lại.'
            ];
        }
    }

    /**
     * Analyze recipe and provide feedback
     */
    public function analyzeRecipe(string $recipeContent)
    {
        try {
            $message = "Hãy phân tích công thức này và đưa ra nhận xét về nguyên liệu, cách làm, và gợi ý cải tiến nếu có:\n\n{$recipeContent}";

            return $this->sendMessage($message);
        } catch (Exception $e) {
            Log::error('OpenRouter recipe analysis error', [
                'message' => $e->getMessage()
            ]);

            return [
                'success' => false,
                'error' => 'Không thể phân tích công thức. Vui lòng thử lại.'
            ];
        }
    }

    /**
     * Get nutritional information
     */
    public function getNutritionalInfo(string $dish)
    {
        try {
            $message = "Hãy cung cấp thông tin dinh dưỡng chi tiết cho món {$dish}, bao gồm calories, protein, carbs, fat và các vitamin, khoáng chất chính.";

            return $this->sendMessage($message);
        } catch (Exception $e) {
            Log::error('OpenRouter nutritional info error', [
                'message' => $e->getMessage(),
                'dish' => $dish
            ]);

            return [
                'success' => false,
                'error' => 'Không thể lấy thông tin dinh dưỡng. Vui lòng thử lại.'
            ];
        }
    }

    /**
     * Parse error messages from exceptions
     */
    private function parseErrorMessage(string $errorMessage): string
    {
        if (str_contains($errorMessage, 'insufficient_quota') || str_contains($errorMessage, 'quota_exceeded')) {
            return 'API đã hết quota. Vui lòng thử lại sau hoặc liên hệ admin để nâng cấp.';
        }

        if (str_contains($errorMessage, 'invalid_api_key')) {
            return 'API key không hợp lệ. Vui lòng liên hệ admin.';
        }

        if (str_contains($errorMessage, 'rate_limit_exceeded')) {
            return 'Đã vượt quá giới hạn request. Vui lòng thử lại sau ít phút.';
        }

        if (str_contains($errorMessage, 'model_not_found')) {
            return 'Model AI không khả dụng. Vui lòng thử lại sau.';
        }

        return 'Có lỗi xảy ra khi kết nối với AI. Vui lòng thử lại sau.';
    }

    /**
     * Handle API error messages (legacy method for backward compatibility)
     */
    private function getErrorMessage($errorData): string
    {
        if (isset($errorData['error']['code'])) {
            switch ($errorData['error']['code']) {
                case 'insufficient_quota':
                case 'quota_exceeded':
                    return 'API đã hết quota. Vui lòng thử lại sau hoặc liên hệ admin để nâng cấp.';

                case 'invalid_api_key':
                    return 'API key không hợp lệ. Vui lòng liên hệ admin.';

                case 'rate_limit_exceeded':
                    return 'Đã vượt quá giới hạn request. Vui lòng thử lại sau ít phút.';

                case 'model_not_found':
                    return 'Model AI không khả dụng. Vui lòng thử lại sau.';

                case 400:
                    // Handle specific 400 errors
                    if (isset($errorData['error']['message'])) {
                        if (str_contains($errorData['error']['message'], 'Expected object, received boolean')) {
                            return 'Lỗi cấu hình API. Vui lòng liên hệ admin để kiểm tra cài đặt.';
                        }
                        if (str_contains($errorData['error']['message'], 'validation')) {
                            return 'Dữ liệu gửi đến AI không hợp lệ. Vui lòng thử lại.';
                        }
                    }
                    return 'Yêu cầu không hợp lệ. Vui lòng kiểm tra thông tin và thử lại.';

                default:
                    return $errorData['error']['message'] ?? 'Có lỗi xảy ra với dịch vụ AI.';
            }
        }

        // Handle cases where error structure is different
        if (isset($errorData['error']['message'])) {
            $message = $errorData['error']['message'];

            // Check for specific error patterns
            if (str_contains($message, 'Expected object, received boolean')) {
                return 'Lỗi cấu hình API. Vui lòng liên hệ admin để kiểm tra cài đặt.';
            }

            if (str_contains($message, 'validation')) {
                return 'Dữ liệu gửi đến AI không hợp lệ. Vui lòng thử lại.';
            }

            return $message;
        }

        return 'Không thể kết nối với dịch vụ AI. Vui lòng thử lại.';
    }

    /**
     * Check if API key is configured
     */
    public function isConfigured(): bool
    {
        return !empty($this->apiKey);
    }

    /**
     * Extract suggested recipes from AI response by matching recipe titles
     */
    private function extractSuggestedRecipes(string $aiResponse, array $candidates): array
    {
        $suggestedRecipes = [];

        // Convert AI response to lowercase for better matching
        $responseLower = strtolower($aiResponse);

        Log::info('Extracting suggested recipes', [
            'ai_response_length' => strlen($aiResponse),
            'candidates_count' => count($candidates),
            'response_preview' => substr($aiResponse, 0, 200) . '...'
        ]);

        // First, try exact title matching
        foreach ($candidates as $recipe) {
            $titleLower = strtolower($recipe['title']);

            // Check if the recipe title appears in the AI response
            if (str_contains($responseLower, $titleLower)) {
                $suggestedRecipes[] = $recipe;
                Log::info('Exact match found', ['recipe_title' => $recipe['title']]);
            }
        }

        // If no exact matches found, try partial matching
        if (empty($suggestedRecipes)) {
            Log::info('No exact matches found, trying partial matching');
            foreach ($candidates as $recipe) {
                $titleWords = explode(' ', strtolower($recipe['title']));
                $matchCount = 0;

                foreach ($titleWords as $word) {
                    if (strlen($word) > 2 && str_contains($responseLower, $word)) {
                        $matchCount++;
                    }
                }

                // If more than 50% of words match, consider it suggested
                if ($matchCount > 0 && ($matchCount / count($titleWords)) > 0.5) {
                    $suggestedRecipes[] = $recipe;
                    Log::info('Partial match found', [
                        'recipe_title' => $recipe['title'],
                        'match_count' => $matchCount,
                        'total_words' => count($titleWords),
                        'match_ratio' => $matchCount / count($titleWords)
                    ]);
                }
            }
        }

        // If still no matches, check if the AI response contains recipe-related keywords
        if (empty($suggestedRecipes)) {
            Log::info('No title matches found, checking for recipe-related keywords');
            $recipeKeywords = ['công thức', 'món ăn', 'nấu', 'chế biến', 'gợi ý', 'đề xuất'];
            $hasRecipeKeywords = false;

            foreach ($recipeKeywords as $keyword) {
                if (str_contains($responseLower, $keyword)) {
                    $hasRecipeKeywords = true;
                    break;
                }
            }

            // If AI mentions recipes but no specific titles, take top 3 candidates
            if ($hasRecipeKeywords) {
                $suggestedRecipes = array_slice($candidates, 0, 3);
                Log::info('Taking top candidates based on recipe keywords', [
                    'suggested_titles' => array_column($suggestedRecipes, 'title')
                ]);
            }
        }

        Log::info('Recipe extraction complete', [
            'suggested_count' => count($suggestedRecipes),
            'suggested_titles' => array_column($suggestedRecipes, 'title')
        ]);

        // Limit to maximum 5 recipes
        return array_slice($suggestedRecipes, 0, 5);
    }

    /**
     * Send chat with full messages array, reusing circuit breaker and retry logic.
     */
    private function sendMessageWithMessages(array $messages)
    {
        try {
            if ($this->isCircuitBreakerOpen()) {
                Log::warning('OpenRouter service circuit breaker is open');
                return [
                    'success' => false,
                    'error' => 'Dịch vụ AI hiện tại không khả dụng do quá nhiều lỗi. Vui lòng thử lại sau 5 phút.'
                ];
            }

            if (function_exists('ini_set')) {
                ini_set('memory_limit', '256M');
            }

            if (!$this->apiKey) {
                return [
                    'success' => false,
                    'error' => 'OpenRouter API key chưa được cấu hình. Vui lòng kiểm tra cài đặt.'
                ];
            }

            $payload = [
                'model' => 'deepseek/deepseek-chat-v3-0324:free',
                'messages' => $messages,
                'max_tokens' => 300,
                'temperature' => 0.5,
                'top_p' => 0.5,
                'stream' => false,
            ];

            $maxRetries = 3;
            $baseDelay = 1;

            for ($attempt = 1; $attempt <= $maxRetries; $attempt++) {
                try {
                    $timeout = min(15 + ($attempt * 5), 30);
                    Log::info('OpenRouter API request (custom messages)', [
                        'attempt' => $attempt,
                        'payload' => $payload,
                        'timeout' => $timeout
                    ]);

                    $response = Http::withHeaders([
                        'Authorization' => 'Bearer ' . $this->apiKey,
                        'Content-Type' => 'application/json',
                        'HTTP-Referer' => request()->getSchemeAndHttpHost() ?? 'http://localhost',
                        'X-Title' => 'Bee Recipe Assistant',
                    ])->withOptions([
                        'verify' => env('APP_ENV') === 'production' ? true : false,
                        'timeout' => $timeout,
                        'connect_timeout' => 10,
                    ])->timeout($timeout)->post($this->baseUrl . '/chat/completions', $payload);

                    if ($response->successful()) {
                        break;
                    }

                    if ($attempt < $maxRetries && ($response->status() >= 500 || $response->status() === 408)) {
                        $delay = $baseDelay * pow(2, $attempt - 1);
                        Log::warning("OpenRouter API attempt {$attempt} failed, retrying in {$delay}s", [
                            'status' => $response->status(),
                            'attempt' => $attempt
                        ]);
                        sleep($delay);
                        continue;
                    }
                } catch (\Illuminate\Http\Client\ConnectionException $e) {
                    Log::warning("OpenRouter connection error on attempt {$attempt}", [
                        'message' => $e->getMessage(),
                        'attempt' => $attempt
                    ]);

                    if ($attempt < $maxRetries) {
                        $delay = $baseDelay * pow(2, $attempt - 1);
                        sleep($delay);
                        continue;
                    }

                    throw $e;
                }
            }

            if ($response->successful()) {
                $data = $response->json();
                Log::info('OpenRouter API data (custom messages)', [
                    'data' => $data
                ]);

                $messageContent = $data['choices'][0]['message']['content'] ?? '';
                if (empty($messageContent) && isset($data['choices'][0]['message']['reasoning'])) {
                    $messageContent = $data['choices'][0]['message']['reasoning'];
                }
                if (empty($messageContent)) {
                    $messageContent = 'Xin lỗi, tôi không thể trả lời câu hỏi này.';
                }

                return [
                    'success' => true,
                    'message' => $messageContent,
                    'usage' => $data['usage'] ?? []
                ];
            }

            $errorData = $response->json();
            $errorMessage = $this->getErrorMessage($errorData);

            Log::error('OpenRouter API error (custom messages)', [
                'status' => $response->status(),
                'response' => $errorData
            ]);

            // Fallback to Gemini when OpenRouter fails
            Log::info('Falling back to Gemini due to OpenRouter error');
            return $this->fallbackToGemini($messages);
        } catch (\Illuminate\Http\Client\ConnectionException $e) {
            Log::error('OpenRouter connection error (custom messages)', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            if (str_contains($e->getMessage(), 'timeout') || str_contains($e->getMessage(), 'timed out')) {
                Log::info('OpenRouter timeout, falling back to Gemini');
                return $this->fallbackToGemini($messages);
            }

            Log::info('OpenRouter connection error, falling back to Gemini');
            return $this->fallbackToGemini($messages);
        } catch (Exception $e) {
            Log::error('OpenRouter service error (custom messages)', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            Log::info('OpenRouter service error, falling back to Gemini');
            return $this->fallbackToGemini($messages);
        }
    }

    /**
     * Fallback to Gemini when OpenRouter fails
     */
    private function fallbackToGemini(array $messages)
    {
        try {
            Log::info('Attempting to use Gemini as fallback', [
                'messages_count' => count($messages)
            ]);

            $result = $this->geminiService->chat($messages);

            if ($result['success']) {
                Log::info('Gemini fallback successful');
                return [
                    'success' => true,
                    'message' => $result['message'],
                    'provider' => 'gemini'
                ];
            } else {
                Log::error('Gemini fallback failed', [
                    'error' => $result['error'] ?? 'Unknown error'
                ]);
                return [
                    'success' => false,
                    'error' => 'Cả OpenRouter và Gemini đều không khả dụng. Vui lòng thử lại sau.'
                ];
            }
        } catch (Exception $e) {
            Log::error('Gemini fallback exception', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return [
                'success' => false,
                'error' => 'Dịch vụ AI tạm thời không khả dụng. Vui lòng thử lại sau.'
            ];
        }
    }

    /**
     * Compute cosine similarity between two vectors.
     */
    private function cosineSimilarity(array $a, array $b): float
    {
        if (empty($a) || empty($b)) {
            return 0.0;
        }
        $len = min(count($a), count($b));
        if ($len === 0) {
            return 0.0;
        }
        $dot = 0.0;
        $normA = 0.0;
        $normB = 0.0;
        for ($i = 0; $i < $len; $i++) {
            $va = (float) $a[$i];
            $vb = (float) $b[$i];
            $dot += $va * $vb;
            $normA += $va * $va;
            $normB += $vb * $vb;
        }
        if ($normA <= 0.0 || $normB <= 0.0) {
            return 0.0;
        }
        return $dot / (sqrt($normA) * sqrt($normB));
    }

    /**
     * Fallback keyword-based recommendation when embedding fails
     */
    private function keywordBasedRecommendation(string $prompt): array
    {
        try {
            Log::info('Using keyword-based recommendation fallback', ['prompt' => $prompt]);

            // Extract keywords from prompt
            $keywords = $this->extractKeywords($prompt);

            // Search recipes using fulltext or LIKE queries
            $query = \App\Models\Recipe::query()
                ->where('status', 'approved')
                ->select(['id', 'title', 'summary', 'slug', 'featured_image', 'cooking_time', 'difficulty']);

            // Apply keyword filters
            foreach ($keywords as $keyword) {
                $query->where(function ($q) use ($keyword) {
                    $q->where('title', 'LIKE', "%{$keyword}%")
                        ->orWhere('summary', 'LIKE', "%{$keyword}%")
                        ->orWhere('description', 'LIKE', "%{$keyword}%");
                });
            }

            $candidates = $query->limit(10)->get();

            if ($candidates->isEmpty()) {
                // If no keyword matches, get random popular recipes
                $candidates = \App\Models\Recipe::query()
                    ->where('status', 'approved')
                    ->orderBy('view_count', 'desc')
                    ->limit(5)
                    ->get();
            }

            $recipes = $candidates->map(function ($recipe) {
                return [
                    'id' => $recipe->id,
                    'title' => $recipe->title,
                    'summary' => $recipe->summary,
                    'slug' => $recipe->slug,
                    'featured_image' => $recipe->featured_image,
                    'cooking_time' => $recipe->cooking_time,
                    'difficulty_level' => $recipe->difficulty,
                    'similarity' => 0.8, // Mock similarity score
                ];
            })->toArray();

            // Generate AI response with found recipes
            if (!empty($recipes)) {
                $systemMessage = [
                    'role' => 'system',
                    'content' => 'Bạn là trợ lý ẩm thực thân thiện và nhiệt tình. Hãy giới thiệu các món ăn ngon từ danh sách một cách hấp dẫn bằng tiếng Việt. Tập trung vào điểm đặc biệt của từng món mà không cần so sánh hay loại trừ.'
                ];

                $contextJson = json_encode($recipes, JSON_UNESCAPED_UNICODE);
                $userMessage = [
                    'role' => 'user',
                    'content' => "Người dùng hỏi: {$prompt}\n\nDanh sách món ăn ngon: {$contextJson}\n\nHãy giới thiệu những món ăn tuyệt vời từ danh sách một cách hấp dẫn và nhiệt tình nhất!"
                ];

                $response = $this->sendMessageWithMessages([$systemMessage, $userMessage]);
                if (($response['success'] ?? false) === true) {
                    $response['recipes'] = array_slice($recipes, 0, 5);
                    $response['search_method'] = 'keyword_based';
                }
                return $response;
            }

            // If no recipes found, just return regular chat
            return $this->sendMessage($prompt);
        } catch (Exception $e) {
            Log::error('Keyword-based recommendation failed', [
                'message' => $e->getMessage(),
                'prompt' => $prompt
            ]);

            // Ultimate fallback: regular chat
            return $this->sendMessage($prompt);
        }
    }

    /**
     * Extract keywords from user prompt
     */
    private function extractKeywords(string $prompt): array
    {
        // Convert to lowercase and remove punctuation
        $cleaned = strtolower(preg_replace('/[^\p{L}\p{N}\s]/u', ' ', $prompt));

        // Split into words
        $words = array_filter(explode(' ', $cleaned));

        // Remove common Vietnamese stop words
        $stopWords = ['và', 'của', 'với', 'trong', 'trên', 'dưới', 'từ', 'đến', 'cho', 'về', 'như', 'khi', 'nào', 'làm', 'thế', 'sao', 'gì', 'ai', 'đâu', 'nào', 'bao', 'nhiều', 'ít', 'hơn', 'nhất', 'rất', 'quá', 'tôi', 'bạn', 'chúng', 'ta', 'họ', 'nó'];

        $keywords = array_filter($words, function ($word) use ($stopWords) {
            return !in_array($word, $stopWords) && strlen($word) > 2;
        });

        // Return unique keywords
        return array_unique(array_values($keywords));
    }

    /**
     * Get user preferences for VIP recommendations
     */
    private function getUserPreferences($user): array
    {
        $preferences = [
            'dietary_preferences' => [],
            'allergies' => [],
            'health_conditions' => [],
            'cooking_experience' => 'beginner',
            'favorite_cuisines' => [],
            'cooking_time_preference' => 'any'
        ];

        // Get from user profile if exists
        if ($user->profile) {
            $profile = $user->profile;
            $preferences['dietary_preferences'] = $profile->dietary_preferences ?? [];
            $preferences['allergies'] = $profile->allergies ?? [];
            $preferences['health_conditions'] = $profile->health_conditions ?? [];
            $preferences['cooking_experience'] = $profile->cooking_experience ?? 'beginner';
        }

        // Get from user's recipe interactions (favorites, ratings)
        $favoriteRecipes = $user->favoriteRecipes()->with('categories', 'tags')->limit(10)->get();
        if ($favoriteRecipes->isNotEmpty()) {
            $cuisineTypes = [];
            foreach ($favoriteRecipes as $recipe) {
                foreach ($recipe->categories as $category) {
                    $cuisineTypes[] = $category->name;
                }
            }
            $preferences['favorite_cuisines'] = array_unique($cuisineTypes);
        }

        return $preferences;
    }

    /**
     * Enhance prompt with user context for better VIP recommendations
     */
    private function enhancePromptWithUserContext(string $prompt, array $preferences): string
    {
        $contextParts = [$prompt];

        if (!empty($preferences['dietary_preferences'])) {
            $contextParts[] = "Ăn kiêng: " . implode(', ', $preferences['dietary_preferences']);
        }

        if (!empty($preferences['allergies'])) {
            $contextParts[] = "Dị ứng: " . implode(', ', $preferences['allergies']);
        }

        if (!empty($preferences['health_conditions'])) {
            $contextParts[] = "Tình trạng sức khỏe: " . implode(', ', $preferences['health_conditions']);
        }

        if (!empty($preferences['favorite_cuisines'])) {
            $contextParts[] = "Ẩm thực yêu thích: " . implode(', ', $preferences['favorite_cuisines']);
        }

        $contextParts[] = "Kinh nghiệm nấu ăn: " . $preferences['cooking_experience'];

        return implode('. ', $contextParts);
    }

    /**
     * Calculate VIP bonus score based on user preferences
     */
    private function calculateVipBonusScore($recipe, array $preferences): float
    {
        $bonusScore = 0.0;

        // Bonus for dietary preferences match
        if (!empty($preferences['dietary_preferences']) && !empty($recipe->ingredients)) {
            $ingredients = is_array($recipe->ingredients) ? $recipe->ingredients : json_decode($recipe->ingredients, true);
            if (is_array($ingredients)) {
                $vegetarianIngredients = ['rau', 'củ', 'quả', 'đậu', 'nấm'];
                $hasVegetarian = false;
                foreach ($ingredients as $ingredient) {
                    $ingredientName = is_array($ingredient) ? ($ingredient['name'] ?? '') : (string)$ingredient;
                    foreach ($vegetarianIngredients as $vegIngredient) {
                        if (str_contains(strtolower($ingredientName), $vegIngredient)) {
                            $hasVegetarian = true;
                            break 2;
                        }
                    }
                }
                if ($hasVegetarian && in_array('vegetarian', $preferences['dietary_preferences'])) {
                    $bonusScore += 0.2;
                }
            }
        }

        // Bonus for cooking experience match
        if ($preferences['cooking_experience'] === 'beginner' && $recipe->difficulty === 'easy') {
            $bonusScore += 0.15;
        } elseif ($preferences['cooking_experience'] === 'advanced' && in_array($recipe->difficulty, ['hard', 'expert'])) {
            $bonusScore += 0.15;
        }

        // Bonus for cooking time preference
        if (!empty($recipe->cooking_time)) {
            if ($preferences['cooking_time_preference'] === 'quick' && $recipe->cooking_time <= 30) {
                $bonusScore += 0.1;
            } elseif ($preferences['cooking_time_preference'] === 'elaborate' && $recipe->cooking_time > 60) {
                $bonusScore += 0.1;
            }
        }

        return $bonusScore;
    }

    /**
     * Generate nutritional information for VIP users
     */
    private function generateNutritionalInfo($recipe): array
    {
        // This would typically connect to a nutrition API or database
        // For now, return sample data
        return [
            'estimated_calories' => rand(200, 800),
            'protein' => rand(10, 40) . 'g',
            'carbs' => rand(20, 80) . 'g',
            'fat' => rand(5, 30) . 'g',
            'fiber' => rand(2, 15) . 'g',
            'notes' => 'Thông tin dinh dưỡng ước tính dành cho thành viên VIP'
        ];
    }

    /**
     * Generate advanced cooking tips for VIP users
     */
    private function generateCookingTips($recipe): array
    {
        $tips = [
            'Mẹo chuẩn bị: Chuẩn bị tất cả nguyên liệu trước khi bắt đầu nấu',
            'Mẹo nấu ăn: Kiểm soát nhiệt độ đều để món ăn chín đều',
            'Mẹo trình bày: Trang trí đẹp mắt để món ăn hấp dẫn hơn'
        ];

        // Add difficulty-specific tips
        if ($recipe['difficulty_level'] === 'easy') {
            $tips[] = 'Mẹo cho người mới: Đọc kỹ công thức trước khi bắt đầu';
        } elseif ($recipe['difficulty_level'] === 'hard') {
            $tips[] = 'Mẹo nâng cao: Chú ý timing và nhiệt độ để có kết quả tốt nhất';
        }

        return $tips;
    }

    /**
     * Get available models (for future expansion)
     */
    public function getAvailableModels(): array
    {
        return [
            'gpt-3.5-turbo' => 'GPT-3.5 Turbo',
            'gpt-4' => 'GPT-4',
            'gpt-4-turbo' => 'GPT-4 Turbo'
        ];
    }
}