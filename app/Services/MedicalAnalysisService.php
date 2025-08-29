<?php

namespace App\Services;

class MedicalAnalysisService
{
    /**
     * Phân tích các chỉ số xét nghiệm và trả về bệnh tương ứng
     */
    public function analyzeLabResults($labResults)
    {
        $diseases = [];
        $symptoms = [];
        $recommendations = [];
        $detailedLabResults = [];

        // Phân tích từng chỉ số
        foreach ($labResults as $test => $value) {
            $analysis = $this->analyzeSingleTest($test, $value);

            if ($analysis) {
                $diseases = array_merge($diseases, $analysis['diseases']);
                $symptoms = array_merge($symptoms, $analysis['symptoms']);
                $recommendations = array_merge($recommendations, $analysis['recommendations']);
            }

            // Tạo thông tin chi tiết cho mỗi chỉ số
            $detailedLabResults[$test] = $this->getDetailedLabInfo($test, $value);
        }

        // Loại bỏ trùng lặp
        $diseases = array_unique($diseases);
        $symptoms = array_unique($symptoms);
        $recommendations = array_unique($recommendations);

        return [
            'diseases' => $diseases,
            'symptoms' => $symptoms,
            'recommendations' => $recommendations,
            'lab_results' => $detailedLabResults
        ];
    }

    /**
     * Lấy thông tin chi tiết cho một chỉ số xét nghiệm
     */
    private function getDetailedLabInfo($test, $value)
    {
        $rules = $this->getMedicalRules();

        if (!isset($rules[$test])) {
            return null;
        }

        $rule = $rules[$test];
        $normalRange = $rule['normal_range'];
        $unit = $rule['unit'];

        // Xác định trạng thái
        $status = 'Bình thường';
        $statusColor = 'green';

        if (count($normalRange) == 2) {
            if ($value < $normalRange[0]) {
                $status = 'Giảm';
                $statusColor = 'blue';
            } elseif ($value > $normalRange[1]) {
                $status = 'Tăng';
                $statusColor = 'red';
            }
        } else {
            if ($value > $normalRange[0]) {
                $status = 'Tăng';
                $statusColor = 'red';
            }
        }

        return [
            'value' => $value,
            'unit' => $unit,
            'normal_range' => $normalRange,
            'normal_text' => $this->formatNormalRange($normalRange),
            'status' => $status,
            'status_color' => $statusColor
        ];
    }

    /**
     * Format khoảng bình thường thành text
     */
    private function formatNormalRange($range)
    {
        if (count($range) == 2) {
            return $range[0] . ' - ' . $range[1];
        } else {
            return '< ' . $range[0];
        }
    }

    /**
     * Phân tích một chỉ số xét nghiệm cụ thể
     */
    private function analyzeSingleTest($test, $value)
    {
        $rules = $this->getMedicalRules();

        if (!isset($rules[$test])) {
            return null;
        }

        $rule = $rules[$test];
        $result = [
            'diseases' => [],
            'symptoms' => [],
            'recommendations' => []
        ];

        // Kiểm tra từng điều kiện
        foreach ($rule['conditions'] as $condition) {
            if ($this->checkCondition($value, $condition)) {
                $result['diseases'] = array_merge($result['diseases'], $condition['diseases']);
                $result['symptoms'] = array_merge($result['symptoms'], $condition['symptoms']);
                $result['recommendations'] = array_merge($result['recommendations'], $condition['recommendations']);
            }
        }

        return $result;
    }

    /**
     * Kiểm tra điều kiện
     */
    private function checkCondition($value, $condition)
    {
        $operator = $condition['operator'];
        $threshold = $condition['threshold'];

        switch ($operator) {
            case '>':
                return $value > $threshold;
            case '>=':
                return $value >= $threshold;
            case '<':
                return $value < $threshold;
            case '<=':
                return $value <= $threshold;
            case '==':
                return $value == $threshold;
            case '!=':
                return $value != $threshold;
            case 'between':
                return $value >= $threshold[0] && $value <= $threshold[1];
            case 'not_between':
                return $value < $threshold[0] || $value > $threshold[1];
            default:
                return false;
        }
    }

    /**
     * Định nghĩa các rules y tế
     */
    private function getMedicalRules()
    {
        return [
            'uric_acid' => [
                'unit' => 'µmol/L',
                'normal_range' => [220, 450],
                'conditions' => [
                    [
                        'operator' => '>',
                        'threshold' => 450,
                        'diseases' => ['Tăng acid uric (Gout)', 'Bệnh thận', 'Rối loạn chuyển hóa'],
                        'symptoms' => ['Nguy cơ bệnh Gout', 'Có thể gây sỏi thận', 'Đau khớp, viêm khớp'],
                        'recommendations' => [
                            'Hạn chế thực phẩm giàu purine',
                            'Giảm thịt đỏ, hải sản',
                            'Tăng cường rau xanh, trái cây',
                            'Uống nhiều nước',
                            'Tránh rượu bia'
                        ]
                    ]
                ]
            ],
            'glucose' => [
                'unit' => 'mmol/L',
                'normal_range' => [3.9, 5.6],
                'conditions' => [
                    [
                        'operator' => '>',
                        'threshold' => 5.6,
                        'diseases' => ['Tiểu đường', 'Rối loạn chuyển hóa glucose'],
                        'symptoms' => ['Khát nước thường xuyên', 'Đi tiểu nhiều', 'Mệt mỏi', 'Sụt cân'],
                        'recommendations' => [
                            'Hạn chế đường và tinh bột',
                            'Ăn nhiều rau xanh',
                            'Tập thể dục đều đặn',
                            'Theo dõi đường huyết thường xuyên'
                        ]
                    ],
                    [
                        'operator' => '<',
                        'threshold' => 3.9,
                        'diseases' => ['Hạ đường huyết'],
                        'symptoms' => ['Chóng mặt', 'Run tay', 'Đổ mồ hôi', 'Đói cồn cào'],
                        'recommendations' => [
                            'Ăn đều bữa',
                            'Mang theo kẹo ngọt',
                            'Không bỏ bữa'
                        ]
                    ]
                ]
            ],
            'cholesterol' => [
                'unit' => 'mmol/L',
                'normal_range' => [0, 5.18],
                'conditions' => [
                    [
                        'operator' => '>',
                        'threshold' => 5.18,
                        'diseases' => ['Tăng cholesterol máu', 'Bệnh tim mạch'],
                        'symptoms' => ['Nguy cơ xơ vữa động mạch', 'Bệnh tim', 'Đột quỵ'],
                        'recommendations' => [
                            'Giảm chất béo bão hòa',
                            'Tăng cường omega-3',
                            'Ăn nhiều rau xanh',
                            'Tập thể dục thường xuyên'
                        ]
                    ]
                ]
            ],
            'triglyceride' => [
                'unit' => 'mmol/L',
                'normal_range' => [0, 1.7],
                'conditions' => [
                    [
                        'operator' => '>',
                        'threshold' => 1.7,
                        'diseases' => ['Tăng triglyceride', 'Bệnh tim mạch'],
                        'symptoms' => ['Nguy cơ viêm tụy', 'Bệnh tim mạch'],
                        'recommendations' => [
                            'Giảm đường và tinh bột',
                            'Hạn chế rượu bia',
                            'Tập thể dục thường xuyên',
                            'Giảm cân nếu thừa cân'
                        ]
                    ]
                ]
            ],
            'creatinine' => [
                'unit' => 'µmol/L',
                'normal_range' => [64, 104],
                'conditions' => [
                    [
                        'operator' => '>',
                        'threshold' => 104,
                        'diseases' => ['Suy thận', 'Bệnh thận'],
                        'symptoms' => ['Mệt mỏi', 'Phù nề', 'Thiếu máu'],
                        'recommendations' => [
                            'Hạn chế protein',
                            'Giảm muối',
                            'Uống nước vừa phải',
                            'Theo dõi chức năng thận'
                        ]
                    ]
                ]
            ],
            'ure' => [
                'unit' => 'mmol/L',
                'normal_range' => [3.2, 7.4],
                'conditions' => [
                    [
                        'operator' => '>',
                        'threshold' => 7.4,
                        'diseases' => ['Suy thận', 'Bệnh thận'],
                        'symptoms' => ['Mệt mỏi', 'Chán ăn', 'Buồn nôn'],
                        'recommendations' => [
                            'Hạn chế protein',
                            'Giảm muối',
                            'Theo dõi chức năng thận'
                        ]
                    ]
                ]
            ],
            'alt' => [
                'unit' => 'U/L',
                'normal_range' => [0, 45],
                'conditions' => [
                    [
                        'operator' => '>',
                        'threshold' => 45,
                        'diseases' => ['Viêm gan', 'Bệnh gan'],
                        'symptoms' => ['Mệt mỏi', 'Chán ăn', 'Vàng da'],
                        'recommendations' => [
                            'Hạn chế rượu bia',
                            'Giảm chất béo',
                            'Ăn nhiều rau xanh',
                            'Khám gan định kỳ'
                        ]
                    ]
                ]
            ],
            'ast' => [
                'unit' => 'U/L',
                'normal_range' => [0, 34],
                'conditions' => [
                    [
                        'operator' => '>',
                        'threshold' => 34,
                        'diseases' => ['Viêm gan', 'Bệnh gan', 'Tổn thương cơ'],
                        'symptoms' => ['Mệt mỏi', 'Đau cơ', 'Vàng da'],
                        'recommendations' => [
                            'Hạn chế rượu bia',
                            'Giảm chất béo',
                            'Nghỉ ngơi đầy đủ',
                            'Khám gan định kỳ'
                        ]
                    ]
                ]
            ]
        ];
    }

    /**
     * Phân tích dữ liệu từ ảnh xét nghiệm (OCR result)
     */
    public function analyzeFromImageData($imageData)
    {
        // Giả lập dữ liệu từ ảnh xét nghiệm
        $labResults = $this->extractLabResults($imageData);

        return $this->analyzeLabResults($labResults);
    }

    /**
     * Trích xuất kết quả xét nghiệm từ dữ liệu ảnh
     */
    private function extractLabResults($imageData)
    {
        // Trong thực tế, đây sẽ là kết quả OCR
        // Hiện tại sử dụng dữ liệu mẫu
        return [
            'uric_acid' => 498.3,
            'glucose' => 5.14,
            'cholesterol' => 3.86,
            'triglyceride' => 0.98,
            'creatinine' => 76.85,
            'ure' => 5.40,
            'alt' => 43.80,
            'ast' => 21.33
        ];
    }
}
