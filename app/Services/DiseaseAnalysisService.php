<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\UploadedFile;
use App\Models\DiseaseCondition;

class DiseaseAnalysisService
{
    protected $apiKey;
    protected $baseUrl = 'https://generativelanguage.googleapis.com/v1beta/models/gemini-2.0-flash:generateContent';

    public function __construct()
    {
        $this->apiKey = config('services.gemini.api_key');
    }

    /**
     * Phân tích hình ảnh bệnh án và trả về thông tin bệnh
     */
    public function analyzeMedicalImage(UploadedFile $image)
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
                                'text' => 'Đây là hình ảnh bệnh án hoặc kết quả xét nghiệm y tế. Hãy phân tích và trả về thông tin theo format JSON sau: {"diseases": ["tên bệnh 1", "tên bệnh 2"], "symptoms": ["triệu chứng 1", "triệu chứng 2"], "severity": "mild/moderate/severe", "dietary_restrictions": ["hạn chế 1", "hạn chế 2"], "recommended_foods": ["thực phẩm 1", "thực phẩm 2"], "notes": "Ghi chú bổ sung"}'
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
                    'maxOutputTokens' => 4096,
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
                        'diseases' => ['Bệnh chưa xác định'],
                        'symptoms' => [],
                        'severity' => 'moderate',
                        'dietary_restrictions' => [],
                        'recommended_foods' => [],
                        'notes' => $text
                    ]
                ];
            }

            $errorData = $response->json();
            $errorMessage = 'Không thể phân tích hình ảnh bệnh án. Vui lòng thử lại.';

            if (isset($errorData['error']['code']) && $errorData['error']['code'] == 429) {
                $errorMessage = 'API đã hết quota. Vui lòng thử lại sau hoặc liên hệ admin để nâng cấp.';
            } elseif (isset($errorData['error']['code']) && $errorData['error']['code'] == 400 && strpos($errorData['error']['message'], 'expired') !== false) {
                $errorMessage = 'API key đã hết hạn. Vui lòng liên hệ admin để cập nhật.';
            }

            return [
                'success' => false,
                'error' => $errorMessage
            ];
        } catch (\Exception $e) {
            Log::error('Disease analysis error', [
                'message' => $e->getMessage(),
                'file' => $image->getClientOriginalName()
            ]);

            return [
                'success' => false,
                'error' => 'Có lỗi xảy ra khi phân tích hình ảnh: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Tìm kiếm bệnh trong database dựa trên kết quả phân tích
     */
    public function findMatchingDiseases($analysisData)
    {
        $diseases = $analysisData['diseases'] ?? [];
        $symptoms = $analysisData['symptoms'] ?? [];

        $matchingDiseases = [];

        foreach ($diseases as $diseaseName) {
            $disease = DiseaseCondition::where('name', 'like', '%' . $diseaseName . '%')
                ->orWhere('slug', 'like', '%' . strtolower($diseaseName) . '%')
                ->active()
                ->first();

            if ($disease) {
                $matchingDiseases[] = $disease;
            }
        }

        // Nếu không tìm thấy bệnh cụ thể, tìm theo triệu chứng
        if (empty($matchingDiseases) && !empty($symptoms)) {
            $matchingDiseases = DiseaseCondition::where(function ($query) use ($symptoms) {
                foreach ($symptoms as $symptom) {
                    $query->orWhereJsonContains('symptoms', $symptom);
                }
            })->active()->get()->toArray();
        }

        return $matchingDiseases;
    }

    /**
     * Tạo bệnh mới từ kết quả phân tích
     */
    public function createDiseaseFromAnalysis($analysisData)
    {
        $diseases = $analysisData['diseases'] ?? [];
        $symptoms = $analysisData['symptoms'] ?? [];
        $severity = $analysisData['severity'] ?? 'moderate';
        $dietaryRestrictions = $analysisData['dietary_restrictions'] ?? [];
        $recommendedFoods = $analysisData['recommended_foods'] ?? [];
        $notes = $analysisData['notes'] ?? '';

        if (empty($diseases)) {
            return null;
        }

        $diseaseName = $diseases[0]; // Lấy bệnh đầu tiên
        $severityLevel = $this->mapSeverityToLevel($severity);

        $disease = DiseaseCondition::create([
            'name' => $diseaseName,
            'description' => $notes,
            'symptoms' => $symptoms,
            'restricted_foods' => $dietaryRestrictions,
            'recommended_foods' => $recommendedFoods,
            'severity_level' => $severityLevel,
            'is_active' => true
        ]);

        return $disease;
    }

    /**
     * Map severity string to numeric level
     */
    private function mapSeverityToLevel($severity)
    {
        return match (strtolower($severity)) {
            'mild' => 1,
            'moderate' => 3,
            'severe' => 5,
            default => 3
        };
    }
}
