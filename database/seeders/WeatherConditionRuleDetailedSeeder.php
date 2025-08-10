<?php

namespace Database\Seeders;

use App\Models\WeatherConditionRule;
use App\Models\Category;
use App\Models\Tag;
use Illuminate\Database\Seeder;

class WeatherConditionRuleDetailedSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = Category::all();
        $tags = Tag::all();

        if ($categories->isEmpty() || $tags->isEmpty()) {
            $this->command->error('Cần có categories và tags trước khi chạy seeder này!');
            return;
        }

        // Xóa các quy tắc cũ nếu có
        WeatherConditionRule::truncate();

        $rules = [
            // === NHIỆT ĐỘ CAO (>= 30°C) ===
            [
                'name' => 'Nhiệt độ rất cao (>= 35°C)',
                'description' => 'Thời tiết nóng bức, cần món ăn mát lạnh để giải nhiệt',
                'temperature_min' => 35,
                'temperature_max' => null,
                'humidity_min' => null,
                'humidity_max' => null,
                'suggested_categories' => $this->getCategoryIds(['Salad', 'Đồ uống', 'Tráng miệng', 'Món mát']),
                'suggested_tags' => $this->getTagIds(['mát', 'lạnh', 'giải nhiệt', 'tươi', 'nhẹ']),
                'suggestion_reason' => 'Nhiệt độ rất cao trên 35°C - cần các món ăn mát lạnh, nhẹ để giải nhiệt và bổ sung nước',
                'priority' => 10,
                'is_active' => true
            ],
            [
                'name' => 'Nhiệt độ cao (30-35°C)',
                'description' => 'Thời tiết nóng, ưu tiên món ăn mát và dễ tiêu',
                'temperature_min' => 30,
                'temperature_max' => 35,
                'humidity_min' => null,
                'humidity_max' => null,
                'suggested_categories' => $this->getCategoryIds(['Salad', 'Món mát', 'Đồ uống', 'Tráng miệng']),
                'suggested_tags' => $this->getTagIds(['mát', 'nhẹ', 'dễ tiêu', 'tươi']),
                'suggestion_reason' => 'Nhiệt độ cao 30-35°C - phù hợp với các món ăn mát, nhẹ và dễ tiêu hóa',
                'priority' => 8,
                'is_active' => true
            ],

            // === NHIỆT ĐỘ CAO + ĐỘ ẨM CAO ===
            [
                'name' => 'Nóng ẩm cao (28-32°C, >80%)',
                'description' => 'Nhiệt độ cao kết hợp độ ẩm cao, cần món ăn mát và giải nhiệt',
                'temperature_min' => 28,
                'temperature_max' => 32,
                'humidity_min' => 80,
                'humidity_max' => null,
                'suggested_categories' => $this->getCategoryIds(['Súp', 'Salad', 'Món mát', 'Đồ uống']),
                'suggested_tags' => $this->getTagIds(['mát', 'giải nhiệt', 'tươi', 'nhẹ', 'súp']),
                'suggestion_reason' => 'Nhiệt độ cao (28-32°C) và độ ẩm cao (>80%) - gợi ý các món súp mát và salad để giải nhiệt',
                'priority' => 9,
                'is_active' => true
            ],
            [
                'name' => 'Nóng ẩm vừa (25-30°C, 60-80%)',
                'description' => 'Nhiệt độ cao với độ ẩm vừa phải',
                'temperature_min' => 25,
                'temperature_max' => 30,
                'humidity_min' => 60,
                'humidity_max' => 80,
                'suggested_categories' => $this->getCategoryIds(['Món mát', 'Salad', 'Đồ uống', 'Tráng miệng']),
                'suggested_tags' => $this->getTagIds(['mát', 'cân bằng', 'tươi', 'nhẹ']),
                'suggestion_reason' => 'Nhiệt độ cao (25-30°C) với độ ẩm vừa phải (60-80%) - phù hợp với các món ăn mát và cân bằng',
                'priority' => 7,
                'is_active' => true
            ],

            // === NHIỆT ĐỘ CAO + ĐỘ ẨM THẤP ===
            [
                'name' => 'Nóng khô (25-30°C, <50%)',
                'description' => 'Nhiệt độ cao với độ ẩm thấp, cần bổ sung nước',
                'temperature_min' => 25,
                'temperature_max' => 30,
                'humidity_min' => null,
                'humidity_max' => 50,
                'suggested_categories' => $this->getCategoryIds(['Canh', 'Súp', 'Đồ uống', 'Món nước', 'Cháo']),
                'suggested_tags' => $this->getTagIds(['nước', 'canh', 'súp', 'cháo', 'dễ tiêu']),
                'suggestion_reason' => 'Nhiệt độ cao (25-30°C) và độ ẩm thấp (<50%) - cần các món có nhiều nước để bổ sung độ ẩm',
                'priority' => 8,
                'is_active' => true
            ],

            // === NHIỆT ĐỘ MÁT ===
            [
                'name' => 'Mát mẻ dễ chịu (20-25°C)',
                'description' => 'Thời tiết mát mẻ, phù hợp với nhiều loại món ăn',
                'temperature_min' => 20,
                'temperature_max' => 25,
                'humidity_min' => null,
                'humidity_max' => null,
                'suggested_categories' => $this->getCategoryIds(['Món chính', 'Món nóng', 'Thịt', 'Hải sản']),
                'suggested_tags' => $this->getTagIds(['cân bằng', 'đa dạng', 'dinh dưỡng', 'ngon']),
                'suggestion_reason' => 'Thời tiết mát mẻ 20-25°C - phù hợp với các món ăn đa dạng, cân bằng dinh dưỡng',
                'priority' => 5,
                'is_active' => true
            ],
            [
                'name' => 'Mát hơi lạnh (15-20°C)',
                'description' => 'Nhiệt độ mát hơi lạnh, ưu tiên món ăn ấm',
                'temperature_min' => 15,
                'temperature_max' => 20,
                'humidity_min' => null,
                'humidity_max' => null,
                'suggested_categories' => $this->getCategoryIds(['Món nóng', 'Thịt', 'Hải sản', 'Món chính']),
                'suggested_tags' => $this->getTagIds(['ấm', 'dinh dưỡng', 'ngon', 'cân bằng']),
                'suggestion_reason' => 'Nhiệt độ mát hơi lạnh 15-20°C - phù hợp với các món ăn ấm và giàu dinh dưỡng',
                'priority' => 6,
                'is_active' => true
            ],

            // === NHIỆT ĐỘ LẠNH ===
            [
                'name' => 'Lạnh vừa (10-15°C)',
                'description' => 'Thời tiết lạnh vừa, cần món ăn ấm',
                'temperature_min' => 10,
                'temperature_max' => 15,
                'humidity_min' => null,
                'humidity_max' => null,
                'suggested_categories' => $this->getCategoryIds(['Lẩu', 'Súp nóng', 'Món nóng', 'Thịt']),
                'suggested_tags' => $this->getTagIds(['nóng', 'ấm', 'dinh dưỡng', 'lẩu']),
                'suggestion_reason' => 'Thời tiết lạnh vừa 10-15°C - phù hợp với các món ăn nóng, ấm để giữ nhiệt',
                'priority' => 7,
                'is_active' => true
            ],
            [
                'name' => 'Lạnh nhiều (5-10°C)',
                'description' => 'Thời tiết lạnh nhiều, cần món ăn nóng và giàu dinh dưỡng',
                'temperature_min' => 5,
                'temperature_max' => 10,
                'humidity_min' => null,
                'humidity_max' => null,
                'suggested_categories' => $this->getCategoryIds(['Lẩu', 'Cháo', 'Súp nóng', 'Món nóng']),
                'suggested_tags' => $this->getTagIds(['nóng', 'ấm', 'dinh dưỡng', 'lẩu', 'cháo']),
                'suggestion_reason' => 'Thời tiết lạnh nhiều 5-10°C - cần các món ăn nóng, giàu dinh dưỡng để giữ ấm cơ thể',
                'priority' => 8,
                'is_active' => true
            ],
            [
                'name' => 'Rất lạnh (< 5°C)',
                'description' => 'Thời tiết rất lạnh, cần món ăn nóng và bổ dưỡng',
                'temperature_min' => null,
                'temperature_max' => 5,
                'humidity_min' => null,
                'humidity_max' => null,
                'suggested_categories' => $this->getCategoryIds(['Lẩu', 'Cháo', 'Súp nóng', 'Món nóng', 'Thịt']),
                'suggested_tags' => $this->getTagIds(['nóng', 'ấm', 'dinh dưỡng', 'lẩu', 'cháo', 'bổ dưỡng']),
                'suggestion_reason' => 'Thời tiết rất lạnh dưới 5°C - cần các món ăn nóng, bổ dưỡng để tăng cường sức khỏe',
                'priority' => 10,
                'is_active' => true
            ],

            // === ĐỘ ẨM CAO ===
            [
                'name' => 'Độ ẩm rất cao (>90%)',
                'description' => 'Độ ẩm rất cao, cần món ăn khô và cay',
                'temperature_min' => null,
                'temperature_max' => null,
                'humidity_min' => 90,
                'humidity_max' => null,
                'suggested_categories' => $this->getCategoryIds(['Món khô', 'Món cay', 'Nướng', 'Chiên']),
                'suggested_tags' => $this->getTagIds(['khô', 'cay', 'nướng', 'chiên', 'đậm đà']),
                'suggestion_reason' => 'Độ ẩm rất cao (>90%) - gợi ý các món ăn khô, cay để cân bằng và tăng cảm giác ngon miệng',
                'priority' => 6,
                'is_active' => true
            ],
            [
                'name' => 'Độ ẩm cao (70-90%)',
                'description' => 'Độ ẩm cao, phù hợp với món ăn đa dạng',
                'temperature_min' => null,
                'temperature_max' => null,
                'humidity_min' => 70,
                'humidity_max' => 90,
                'suggested_categories' => $this->getCategoryIds(['Món khô', 'Món cay', 'Nướng', 'Món chính']),
                'suggested_tags' => $this->getTagIds(['khô', 'cay', 'nướng', 'đậm đà']),
                'suggestion_reason' => 'Độ ẩm cao (70-90%) - phù hợp với các món ăn khô, cay để cân bằng',
                'priority' => 5,
                'is_active' => true
            ],

            // === ĐỘ ẨM THẤP ===
            [
                'name' => 'Độ ẩm thấp (<30%)',
                'description' => 'Độ ẩm thấp, cần bổ sung nước qua món ăn',
                'temperature_min' => null,
                'temperature_max' => null,
                'humidity_min' => null,
                'humidity_max' => 30,
                'suggested_categories' => $this->getCategoryIds(['Canh', 'Súp', 'Đồ uống', 'Món mát']),
                'suggested_tags' => $this->getTagIds(['nước', 'canh', 'súp', 'mát', 'dễ tiêu']),
                'suggestion_reason' => 'Độ ẩm thấp (<30%) - cần các món ăn có nhiều nước để bổ sung độ ẩm cho cơ thể',
                'priority' => 6,
                'is_active' => true
            ],
            [
                'name' => 'Độ ẩm vừa thấp (30-50%)',
                'description' => 'Độ ẩm vừa thấp, phù hợp với món ăn cân bằng',
                'temperature_min' => null,
                'temperature_max' => null,
                'humidity_min' => 30,
                'humidity_max' => 50,
                'suggested_categories' => $this->getCategoryIds(['Canh', 'Súp', 'Món chính', 'Đồ uống']),
                'suggested_tags' => $this->getTagIds(['nước', 'canh', 'súp', 'cân bằng']),
                'suggestion_reason' => 'Độ ẩm vừa thấp (30-50%) - phù hợp với các món ăn có nước vừa phải',
                'priority' => 4,
                'is_active' => true
            ],

            // === KẾT HỢP ĐẶC BIỆT ===
            [
                'name' => 'Mưa lạnh (10-15°C, >80%)',
                'description' => 'Thời tiết mưa lạnh, cần món ăn ấm và bổ dưỡng',
                'temperature_min' => 10,
                'temperature_max' => 15,
                'humidity_min' => 80,
                'humidity_max' => null,
                'suggested_categories' => $this->getCategoryIds(['Lẩu', 'Cháo', 'Súp nóng', 'Món nóng']),
                'suggested_tags' => $this->getTagIds(['nóng', 'ấm', 'dinh dưỡng', 'lẩu', 'cháo']),
                'suggestion_reason' => 'Thời tiết mưa lạnh (10-15°C, >80%) - cần các món ăn nóng, ấm để chống lạnh và tăng sức đề kháng',
                'priority' => 9,
                'is_active' => true
            ],
            [
                'name' => 'Nắng nóng khô (30-35°C, <40%)',
                'description' => 'Nắng nóng với độ ẩm thấp, cần món ăn mát và bổ sung nước',
                'temperature_min' => 30,
                'temperature_max' => 35,
                'humidity_min' => null,
                'humidity_max' => 40,
                'suggested_categories' => $this->getCategoryIds(['Canh', 'Súp', 'Đồ uống', 'Món mát', 'Tráng miệng']),
                'suggested_tags' => $this->getTagIds(['mát', 'nước', 'giải nhiệt', 'dễ tiêu', 'tươi']),
                'suggestion_reason' => 'Nắng nóng khô (30-35°C, <40%) - cần các món ăn mát, có nhiều nước để giải nhiệt và bổ sung độ ẩm',
                'priority' => 9,
                'is_active' => true
            ],

            // === QUY TẮC THEO MÙA ===
            [
                'name' => 'Mùa hè nóng bức',
                'description' => 'Quy tắc cho mùa hè nóng bức',
                'temperature_min' => 28,
                'temperature_max' => null,
                'humidity_min' => null,
                'humidity_max' => null,
                'suggested_categories' => $this->getCategoryIds(['Salad', 'Món mát', 'Đồ uống', 'Tráng miệng']),
                'suggested_tags' => $this->getTagIds(['mát', 'giải nhiệt', 'tươi', 'nhẹ', 'mùa hè']),
                'suggestion_reason' => 'Mùa hè nóng bức - ưu tiên các món ăn mát, giải nhiệt và tươi ngon',
                'priority' => 7,
                'is_active' => true,
                'seasonal_rules' => ['summer' => true]
            ],
            [
                'name' => 'Mùa đông lạnh giá',
                'description' => 'Quy tắc cho mùa đông lạnh giá',
                'temperature_min' => null,
                'temperature_max' => 15,
                'humidity_min' => null,
                'humidity_max' => null,
                'suggested_categories' => $this->getCategoryIds(['Lẩu', 'Cháo', 'Súp nóng', 'Món nóng']),
                'suggested_tags' => $this->getTagIds(['nóng', 'ấm', 'dinh dưỡng', 'lẩu', 'cháo', 'mùa đông']),
                'suggestion_reason' => 'Mùa đông lạnh giá - cần các món ăn nóng, ấm và giàu dinh dưỡng để giữ ấm',
                'priority' => 8,
                'is_active' => true,
                'seasonal_rules' => ['winter' => true]
            ],
            [
                'name' => 'Mùa mưa ẩm ướt',
                'description' => 'Quy tắc cho mùa mưa ẩm ướt',
                'temperature_min' => 20,
                'temperature_max' => 28,
                'humidity_min' => 70,
                'humidity_max' => null,
                'suggested_categories' => $this->getCategoryIds(['Món khô', 'Món cay', 'Nướng', 'Món nóng']),
                'suggested_tags' => $this->getTagIds(['khô', 'cay', 'nướng', 'ấm', 'mùa mưa']),
                'suggestion_reason' => 'Mùa mưa ẩm ướt - phù hợp với các món ăn khô, cay để cân bằng độ ẩm',
                'priority' => 6,
                'is_active' => true,
                'seasonal_rules' => ['rainy' => true]
            ]
        ];

        foreach ($rules as $ruleData) {
            WeatherConditionRule::create($ruleData);
        }

        $this->command->info('Đã tạo thành công ' . count($rules) . ' quy tắc điều kiện thời tiết:');
        $this->command->info('- 3 quy tắc cho nhiệt độ cao');
        $this->command->info('- 4 quy tắc cho nhiệt độ cao + độ ẩm');
        $this->command->info('- 2 quy tắc cho nhiệt độ mát');
        $this->command->info('- 3 quy tắc cho nhiệt độ lạnh');
        $this->command->info('- 2 quy tắc cho độ ẩm cao');
        $this->command->info('- 2 quy tắc cho độ ẩm thấp');
        $this->command->info('- 2 quy tắc kết hợp đặc biệt');
        $this->command->info('- 3 quy tắc theo mùa');
    }

    /**
     * Get category IDs by names.
     */
    protected function getCategoryIds($categoryNames)
    {
        return Category::whereIn('name', $categoryNames)
            ->pluck('id')
            ->toArray();
    }

    /**
     * Get tag IDs by names.
     */
    protected function getTagIds($tagNames)
    {
        return Tag::whereIn('name', $tagNames)
            ->pluck('id')
            ->toArray();
    }
} 