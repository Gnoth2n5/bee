<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class IngredientSubstituteService
{
    protected $geminiApiKey;
    protected $spoonacularApiKey;
    protected $geminiBaseUrl = 'https://generativelanguage.googleapis.com/v1beta/models/gemini-2.0-flash:generateContent';
    protected $spoonacularBaseUrl = 'https://api.spoonacular.com/food/ingredients';

    /**
     * Thời gian cache (1 ngày)
     */
    protected $cacheTime = 24 * 60 * 60; // 1 day in seconds

    public function __construct()
    {
        $this->geminiApiKey = config('services.gemini.api_key') ?? env('GEMINI_API_KEY');
        $this->spoonacularApiKey = env('SPOONACULAR_API_KEY');
    }

    /**
     * Dịch nguyên liệu từ tiếng Việt sang tiếng Anh bằng Gemini API
     *
     * @param string $ingredientVi
     * @return array
     */
    public function translateToEnglish(string $ingredientVi): array
    {
        try {
            // Kiểm tra cache trước
            $cacheKey = "ingredient_translate_vi_en_" . md5(strtolower(trim($ingredientVi)));
            $cached = Cache::get($cacheKey);

            if ($cached) {
                Log::info('Cache hit for Vietnamese to English translation', ['ingredient' => $ingredientVi]);
                return [
                    'success' => true,
                    'translation' => $cached,
                    'from_cache' => true
                ];
            }

            Log::info('Making Gemini API call for Vietnamese to English translation', ['ingredient' => $ingredientVi]);

            $payload = [
                'contents' => [
                    [
                        'parts' => [
                            [
                                'text' => "Dịch tên nguyên liệu từ tiếng Việt sang tiếng Anh (chỉ trả về từ tiếng Anh, không giải thích thêm): \"{$ingredientVi}\""
                            ]
                        ]
                    ]
                ],
                'generationConfig' => [
                    'temperature' => 0.1,
                    'topK' => 1,
                    'topP' => 1,
                    'maxOutputTokens' => 50,
                ]
            ];

            $response = Http::timeout(30)
                ->withOptions([
                    'verify' => config('app.env') === 'production'
                ])
                ->withHeaders([
                    'Content-Type' => 'application/json',
                ])->post($this->geminiBaseUrl . '?key=' . $this->geminiApiKey, $payload);

            if ($response->successful()) {
                $data = $response->json();
                $translation = trim($data['candidates'][0]['content']['parts'][0]['text'] ?? '');

                if (!empty($translation)) {
                    // Loại bỏ dấu ngoặc kép nếu có
                    $translation = trim($translation, '"\'');

                    // Cache kết quả
                    Cache::put($cacheKey, $translation, $this->cacheTime);

                    return [
                        'success' => true,
                        'translation' => $translation,
                        'from_cache' => false
                    ];
                }
            }

            $errorData = $response->json();
            Log::error('Gemini translation API error', [
                'status' => $response->status(),
                'response' => $errorData,
                'ingredient' => $ingredientVi
            ]);

            return [
                'success' => false,
                'error' => $this->getGeminiErrorMessage($errorData)
            ];
        } catch (\Exception $e) {
            Log::error('Exception in translateToEnglish', [
                'message' => $e->getMessage(),
                'ingredient' => $ingredientVi
            ]);

            return [
                'success' => false,
                'error' => 'Có lỗi xảy ra khi dịch nguyên liệu.'
            ];
        }
    }

    /**
     * Dịch kết quả từ tiếng Anh sang tiếng Việt bằng Gemini API
     *
     * @param array $substitutes
     * @return array
     */
    public function translateToVietnamese(array $substitutes): array
    {
        try {
            $translatedSubstitutes = [];

            foreach ($substitutes as $substitute) {
                $name = $substitute['name'] ?? '';
                $description = $substitute['description'] ?? '';

                if (empty($name)) {
                    continue;
                }

                // Cache key cho mỗi substitute
                $cacheKey = "ingredient_translate_en_vi_" . md5(strtolower($name));
                $cached = Cache::get($cacheKey);

                if ($cached) {
                    $translatedSubstitutes[] = [
                        'name' => $cached['name'],
                        'description' => $cached['description'],
                        'original_name' => $name
                    ];
                    continue;
                }

                // Tạo text để dịch
                $textToTranslate = "Tên: {$name}";
                if (!empty($description)) {
                    $textToTranslate .= "\nMô tả: {$description}";
                }

                $payload = [
                    'contents' => [
                        [
                            'parts' => [
                                [
                                    'text' => "Dịch thông tin nguyên liệu sau từ tiếng Anh sang tiếng Việt (giữ nguyên format):\n{$textToTranslate}"
                                ]
                            ]
                        ]
                    ],
                    'generationConfig' => [
                        'temperature' => 0.1,
                        'topK' => 1,
                        'topP' => 1,
                        'maxOutputTokens' => 200,
                    ]
                ];

                $response = Http::timeout(30)
                    ->withOptions([
                        'verify' => config('app.env') === 'production'
                    ])
                    ->withHeaders([
                        'Content-Type' => 'application/json',
                    ])->post($this->geminiBaseUrl . '?key=' . $this->geminiApiKey, $payload);

                if ($response->successful()) {
                    $data = $response->json();
                    $translation = trim($data['candidates'][0]['content']['parts'][0]['text'] ?? '');

                    // Parse translation
                    $translatedName = $name; // fallback
                    $translatedDescription = $description; // fallback

                    if (preg_match('/Tên:\s*(.+?)(?:\n|$)/u', $translation, $nameMatches)) {
                        $translatedName = trim($nameMatches[1]);
                    }

                    if (preg_match('/Mô tả:\s*(.+?)(?:\n|$)/u', $translation, $descMatches)) {
                        $translatedDescription = trim($descMatches[1]);
                    }

                    $translatedData = [
                        'name' => $translatedName,
                        'description' => $translatedDescription
                    ];

                    // Cache kết quả
                    Cache::put($cacheKey, $translatedData, $this->cacheTime);

                    $translatedSubstitutes[] = [
                        'name' => $translatedName,
                        'description' => $translatedDescription,
                        'original_name' => $name
                    ];
                } else {
                    // Nếu dịch thất bại, giữ nguyên bản gốc
                    $translatedSubstitutes[] = [
                        'name' => $name,
                        'description' => $description,
                        'original_name' => $name
                    ];
                }

                // Thêm delay nhỏ giữa các request để tránh rate limit
                usleep(100000); // 0.1 second
            }

            return [
                'success' => true,
                'substitutes' => $translatedSubstitutes
            ];
        } catch (\Exception $e) {
            Log::error('Exception in translateToVietnamese', [
                'message' => $e->getMessage()
            ]);

            return [
                'success' => false,
                'error' => 'Có lỗi xảy ra khi dịch kết quả.'
            ];
        }
    }

    /**
     * Lấy danh sách nguyên liệu thay thế
     *
     * @param string $ingredientVi
     * @return array
     */
    public function getSubstitutes(string $ingredientVi): array
    {
        try {
            // Kiểm tra cache chính cho kết quả cuối cùng
            $mainCacheKey = "ingredient_substitute_" . md5(strtolower(trim($ingredientVi))) . "_vi";
            $cached = Cache::get($mainCacheKey);

            if ($cached) {
                Log::info('Cache hit for ingredient substitutes', ['ingredient' => $ingredientVi]);
                return [
                    'success' => true,
                    'substitutes' => $cached,
                    'from_cache' => true
                ];
            }

            Log::info('Processing new ingredient substitute request', ['ingredient' => $ingredientVi]);

            // Bước 1: Dịch nguyên liệu sang tiếng Anh
            $translationResult = $this->translateToEnglish($ingredientVi);

            if (!$translationResult['success']) {
                return $translationResult;
            }

            $ingredientEn = $translationResult['translation'];
            Log::info('Translated ingredient', ['vi' => $ingredientVi, 'en' => $ingredientEn]);

            // Bước 2: Gọi Spoonacular API
            $spoonacularResult = $this->getSpoonacularSubstitutes($ingredientEn);

            if (!$spoonacularResult['success']) {
                return $spoonacularResult;
            }

            // Bước 3: Dịch kết quả sang tiếng Việt
            $translatedResult = $this->translateToVietnamese($spoonacularResult['substitutes']);

            if (!$translatedResult['success']) {
                return $translatedResult;
            }

            // Cache kết quả cuối cùng
            Cache::put($mainCacheKey, $translatedResult['substitutes'], $this->cacheTime);

            return [
                'success' => true,
                'substitutes' => $translatedResult['substitutes'],
                'from_cache' => false,
                'english_translation' => $ingredientEn
            ];
        } catch (\Exception $e) {
            Log::error('Exception in getSubstitutes', [
                'message' => $e->getMessage(),
                'ingredient' => $ingredientVi
            ]);

            return [
                'success' => false,
                'error' => 'Có lỗi xảy ra khi tìm nguyên liệu thay thế.'
            ];
        }
    }

    /**
     * Gọi Spoonacular API để lấy ingredient substitutes
     *
     * @param string $ingredientEn
     * @return array
     */
    protected function getSpoonacularSubstitutes(string $ingredientEn): array
    {
        try {
            // Kiểm tra cache cho kết quả Spoonacular
            $cacheKey = "spoonacular_substitute_" . md5(strtolower(trim($ingredientEn)));
            $cached = Cache::get($cacheKey);

            if ($cached) {
                Log::info('Cache hit for Spoonacular substitutes', ['ingredient' => $ingredientEn]);
                return [
                    'success' => true,
                    'substitutes' => $cached,
                    'from_cache' => true
                ];
            }

            Log::info('Making Spoonacular API call', ['ingredient' => $ingredientEn]);

            $url = $this->spoonacularBaseUrl . '/substitutes';
            $response = Http::timeout(30)
                ->withOptions([
                    'verify' => config('app.env') === 'production'
                ])
                ->get($url, [
                    'ingredientName' => $ingredientEn,
                    'apiKey' => $this->spoonacularApiKey
                ]);

            if ($response->successful()) {
                $data = $response->json();
                $substitutes = [];

                if (isset($data['substitutes']) && is_array($data['substitutes']) && !empty($data['substitutes'])) {
                    foreach ($data['substitutes'] as $substitute) {
                        $substitutes[] = [
                            'name' => $substitute,
                            'description' => ''
                        ];
                    }
                }

                if (empty($substitutes)) {
                    return [
                        'success' => false,
                        'error' => 'Không tìm thấy nguyên liệu thay thế cho "' . $ingredientEn . '".'
                    ];
                }

                // Cache kết quả Spoonacular
                Cache::put($cacheKey, $substitutes, $this->cacheTime);

                return [
                    'success' => true,
                    'substitutes' => $substitutes,
                    'from_cache' => false
                ];
            }

            $errorData = $response->json();
            Log::error('Spoonacular API error', [
                'status' => $response->status(),
                'response' => $errorData,
                'ingredient' => $ingredientEn
            ]);

            $errorMessage = 'Không thể lấy thông tin từ Spoonacular.';
            if (isset($errorData['code']) && $errorData['code'] == 402) {
                $errorMessage = 'API đã hết quota. Vui lòng thử lại sau.';
            } elseif (isset($errorData['message'])) {
                $errorMessage = $errorData['message'];
            }

            return [
                'success' => false,
                'error' => $errorMessage
            ];
        } catch (\Exception $e) {
            Log::error('Exception in getSpoonacularSubstitutes', [
                'message' => $e->getMessage(),
                'ingredient' => $ingredientEn
            ]);

            return [
                'success' => false,
                'error' => 'Có lỗi xảy ra khi gọi API Spoonacular.'
            ];
        }
    }

    /**
     * Xử lý error message từ Gemini API
     *
     * @param array|null $errorData
     * @return string
     */
    protected function getGeminiErrorMessage(?array $errorData): string
    {
        if (!$errorData) {
            return 'Không thể kết nối đến dịch vụ dịch thuật.';
        }

        if (isset($errorData['error']['code'])) {
            switch ($errorData['error']['code']) {
                case 429:
                    return 'API dịch thuật đã hết quota. Vui lòng thử lại sau.';
                case 400:
                    if (strpos($errorData['error']['message'], 'expired') !== false) {
                        return 'API key đã hết hạn. Vui lòng liên hệ admin.';
                    }
                    break;
            }
        }

        if (isset($errorData['error']['message'])) {
            return 'Lỗi dịch thuật: ' . $errorData['error']['message'];
        }

        return 'Có lỗi xảy ra khi dịch nguyên liệu.';
    }

    /**
     * Xóa cache cho một nguyên liệu cụ thể
     *
     * @param string $ingredientVi
     * @return bool
     */
    public function clearCache(string $ingredientVi): bool
    {
        $keys = [
            "ingredient_substitute_" . md5(strtolower(trim($ingredientVi))) . "_vi",
            "ingredient_translate_vi_en_" . md5(strtolower(trim($ingredientVi))),
        ];

        foreach ($keys as $key) {
            Cache::forget($key);
        }

        return true;
    }

    /**
     * Lấy thông tin cache status
     *
     * @param string $ingredientVi
     * @return array
     */
    public function getCacheInfo(string $ingredientVi): array
    {
        $mainCacheKey = "ingredient_substitute_" . md5(strtolower(trim($ingredientVi))) . "_vi";
        $translationCacheKey = "ingredient_translate_vi_en_" . md5(strtolower(trim($ingredientVi)));

        return [
            'has_main_cache' => Cache::has($mainCacheKey),
            'has_translation_cache' => Cache::has($translationCacheKey),
            'cache_ttl' => $this->cacheTime
        ];
    }
}