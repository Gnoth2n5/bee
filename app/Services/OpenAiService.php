<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Exception;
use Illuminate\Support\Facades\Cache;


class OpenAiService
{
    protected $apiKey;
    protected $baseUrl = 'https://openrouter.ai/api/v1';
    protected static $circuitBreakerKey = 'openai_service_circuit_breaker';
    protected static $failureThreshold = 5; // Number of failures before opening circuit
    protected static $recoveryTimeout = 300; // 5 minutes

    public function __construct()
    {
        $this->apiKey = env('OPEN_AI_API_KEY');
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
     * Send a chat message to OpenAI and get response
     */
    public function sendMessage(string $message, array $conversationHistory = [])
    {
        try {
            // Check circuit breaker first
            if ($this->isCircuitBreakerOpen()) {
                Log::warning('OpenRouter service circuit breaker is open');
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
                    'error' => 'OpenRouter API key chưa được cấu hình. Vui lòng kiểm tra cài đặt.'
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
                'temperature' => 0.5, // Re-enable temperature for better response variety
                'top_p' => 0.5,
                'stream' => false, // Ensure we get a complete response

            ];

            // Implement retry logic with exponential backoff
            $maxRetries = 3;
            $baseDelay = 1; // seconds

            for ($attempt = 1; $attempt <= $maxRetries; $attempt++) {
                try {
                    $timeout = min(15 + ($attempt * 5), 30); // Progressive timeout: 20s, 25s, 30s
                    Log::info('OpenRouter API request', [
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
                        'connect_timeout' => 10, // Separate connection timeout
                    ])->timeout($timeout)->post($this->baseUrl . '/chat/completions', $payload);


                    // If successful, break out of retry loop
                    if ($response->successful()) {
                        break;
                    }

                    // If not the last attempt and it's a timeout/server error, retry
                    if ($attempt < $maxRetries && ($response->status() >= 500 || $response->status() === 408)) {
                        $delay = $baseDelay * pow(2, $attempt - 1); // Exponential backoff
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
                Log::info('OpenRouter API data', [
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
     * Get recipe suggestions based on ingredients
     */
    public function getRecipeSuggestions(array $ingredients)
    {
        try {
            $ingredientsList = implode(', ', $ingredients);
            $message = "Tôi có những nguyên liệu sau: {$ingredientsList}. Hãy gợi ý cho tôi 3 món ăn có thể làm từ những nguyên liệu này và cách làm chi tiết.";

            return $this->sendMessage($message);
        } catch (Exception $e) {
            Log::error('OpenRouter recipe suggestions error', [
                'message' => $e->getMessage(),
                'ingredients' => $ingredients
            ]);

            return [
                'success' => false,
                'error' => 'Không thể tạo gợi ý công thức. Vui lòng thử lại.'
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
