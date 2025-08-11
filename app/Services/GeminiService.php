<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\UploadedFile;

class GeminiService
{
    protected $apiKey;
    protected $baseUrl = 'https://generativelanguage.googleapis.com/v1beta/models/gemini-2.0-flash:generateContent';

    public function __construct()
    {
        $this->apiKey = config('services.gemini.api_key');
    }

    /**
     * Phân tích ảnh món ăn và trả về thông tin
     */
    public function analyzeFoodImage(UploadedFile $image)
    {
        try {
            // Đọc và encode ảnh thành base64
            $imageData = base64_encode(file_get_contents($image->getRealPath()));
            $mimeType = $image->getMimeType();

            $payload = [
                'contents' => [
                    [
                        'parts' => [
                            [
                                'text' => 'Đây là ảnh món ăn. Hãy phân tích và trả về thông tin theo format JSON sau: {"name": "Tên món ăn", "description": "Mô tả ngắn", "ingredients": ["nguyên liệu 1", "nguyên liệu 2"], "cooking_method": "Phương pháp nấu chính", "difficulty": "easy/medium/hard", "cooking_time": "Thời gian nấu ước tính", "cuisine": "Loại ẩm thực"}'
                            ],
                            [
                                'inline_data' => [
                                    'mime_type' => $mimeType,
                                    'data' => $imageData
                                ]
                            ]
                        ]
                    ]
                ],
                'generationConfig' => [
                    'temperature' => 0.1,
                    'topK' => 32,
                    'topP' => 1,
                    'maxOutputTokens' => 2048,
                ]
            ];

            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
            ])->post($this->baseUrl . '?key=' . $this->apiKey, $payload);

            if ($response->successful()) {
                $data = $response->json();
                $text = $data['candidates'][0]['content']['parts'][0]['text'] ?? '';

                // Cố gắng parse JSON từ response
                $jsonStart = strpos($text, '{');
                $jsonEnd = strrpos($text, '}');

                if ($jsonStart !== false && $jsonEnd !== false) {
                    $jsonString = substr($text, $jsonStart, $jsonEnd - $jsonStart + 1);
                    $result = json_decode($jsonString, true);

                    if ($result) {
                        return [
                            'success' => true,
                            'data' => $result
                        ];
                    }
                }

                // Nếu không parse được JSON, trả về text thô
                return [
                    'success' => true,
                    'data' => [
                        'name' => 'Món ăn được nhận diện',
                        'description' => $text,
                        'ingredients' => [],
                        'cooking_method' => '',
                        'difficulty' => 'medium',
                        'cooking_time' => '30 phút',
                        'cuisine' => 'Việt Nam'
                    ]
                ];
            }

            $errorData = $response->json();
            $errorMessage = 'Không thể phân tích ảnh. Vui lòng thử lại.';
            
            if (isset($errorData['error']['code']) && $errorData['error']['code'] == 429) {
                $errorMessage = 'API đã hết quota. Vui lòng thử lại sau hoặc liên hệ admin để nâng cấp.';
            } elseif (isset($errorData['error']['code']) && $errorData['error']['code'] == 400 && strpos($errorData['error']['message'], 'expired') !== false) {
                $errorMessage = 'API key đã hết hạn. Vui lòng liên hệ admin để cập nhật.';
            } elseif (isset($errorData['error']['message'])) {
                $errorMessage = $errorData['error']['message'];
            }
            
            Log::error('Gemini API error', [
                'status' => $response->status(),
                'response' => $response->json()
            ]);

            return [
                'success' => false,
                'error' => $errorMessage
            ];

        } catch (\Exception $e) {
            Log::error('Gemini service error', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return [
                'success' => false,
                'error' => 'Có lỗi xảy ra khi xử lý ảnh.'
            ];
        }
    }

    /**
     * Tìm kiếm công thức dựa trên ảnh
     */
    public function searchRecipesByImage(UploadedFile $image)
    {
        try {
            $imageData = base64_encode(file_get_contents($image->getRealPath()));
            $mimeType = $image->getMimeType();

            $payload = [
                'contents' => [
                    [
                        'parts' => [
                            [
                                'text' => 'Đây là ảnh món ăn. Hãy trả về danh sách các từ khóa tìm kiếm phù hợp để tìm công thức nấu ăn này. Trả về dưới dạng JSON array: ["từ khóa 1", "từ khóa 2", "từ khóa 3"]'
                            ],
                            [
                                'inline_data' => [
                                    'mime_type' => $mimeType,
                                    'data' => $imageData
                                ]
                            ]
                        ]
                    ]
                ],
                'generationConfig' => [
                    'temperature' => 0.1,
                    'topK' => 32,
                    'topP' => 1,
                    'maxOutputTokens' => 1024,
                ]
            ];

            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
            ])->post($this->baseUrl . '?key=' . $this->apiKey, $payload);

            if ($response->successful()) {
                $data = $response->json();
                $text = $data['candidates'][0]['content']['parts'][0]['text'] ?? '';

                // Parse JSON array từ response
                $jsonStart = strpos($text, '[');
                $jsonEnd = strrpos($text, ']');

                if ($jsonStart !== false && $jsonEnd !== false) {
                    $jsonString = substr($text, $jsonStart, $jsonEnd - $jsonStart + 1);
                    $keywords = json_decode($jsonString, true);

                    if ($keywords && is_array($keywords)) {
                        return [
                            'success' => true,
                            'keywords' => $keywords
                        ];
                    }
                }

                return [
                    'success' => true,
                    'keywords' => ['món ăn', 'công thức']
                ];
            }

            $errorData = $response->json();
            $errorMessage = 'Không thể phân tích ảnh để tìm kiếm.';

                        if (isset($errorData['error']['code']) && $errorData['error']['code'] == 429) {
                $errorMessage = 'API đã hết quota. Vui lòng thử lại sau hoặc liên hệ admin để nâng cấp.';
            } elseif (isset($errorData['error']['code']) && $errorData['error']['code'] == 400 && strpos($errorData['error']['message'], 'expired') !== false) {
                $errorMessage = 'API key đã hết hạn. Vui lòng liên hệ admin để cập nhật.';
            } elseif (isset($errorData['error']['message'])) {
                $errorMessage = $errorData['error']['message'];
            }
            
            return [
                'success' => false,
                'error' => $errorMessage
            ];

        } catch (\Exception $e) {
            Log::error('Gemini search error', [
                'message' => $e->getMessage()
            ]);

            return [
                'success' => false,
                'error' => 'Có lỗi xảy ra khi tìm kiếm.'
            ];
        }
    }
}