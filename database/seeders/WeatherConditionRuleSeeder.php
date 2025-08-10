<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\WeatherConditionRule;

class WeatherConditionRuleSeeder extends Seeder
{
    public function run(): void
    {
        $rules = [
            [
                'name' => 'Ấm ẩm – Canh/Súp/Cháo',
                'description' => 'Nhiệt độ 22–28°C, độ ẩm cao, hợp món nước ấm.',
                'temperature_min' => 22,
                'temperature_max' => 28,
                'humidity_min' => 80,
                'humidity_max' => null,
                'suggested_categories' => [],
                'suggested_tags' => [],
                'suggestion_reason' => 'Ẩm cao dễ ngán, món nước ấm dễ tiêu',
                'is_active' => true,
                'priority' => 80,
            ],
            [
                'name' => 'Nóng ẩm – Món mát, ít dầu',
                'description' => '≥30°C và độ ẩm ≥70%.',
                'temperature_min' => 30,
                'temperature_max' => null,
                'humidity_min' => 70,
                'humidity_max' => null,
                'suggested_categories' => [],
                'suggested_tags' => [],
                'suggestion_reason' => 'Giải nhiệt, thanh vị',
                'is_active' => true,
                'priority' => 70,
            ],
            [
                'name' => 'Nóng khô – Món có nước/đồ hấp',
                'description' => '≥30°C, độ ẩm ≤50%.',
                'temperature_min' => 30,
                'temperature_max' => null,
                'humidity_min' => null,
                'humidity_max' => 50,
                'suggested_categories' => [],
                'suggested_tags' => [],
                'suggestion_reason' => 'Bù nước, dễ ăn',
                'is_active' => true,
                'priority' => 65,
            ],
            [
                'name' => 'Mát vừa – Nướng/áp chảo nhẹ',
                'description' => '22–28°C, ẩm 40–70%.',
                'temperature_min' => 22,
                'temperature_max' => 28,
                'humidity_min' => 40,
                'humidity_max' => 70,
                'suggested_categories' => [],
                'suggested_tags' => [],
                'suggestion_reason' => 'Đậm đà vừa phải',
                'is_active' => true,
                'priority' => 50,
            ],
            [
                'name' => 'Se lạnh – Lẩu/Súp sệt/Cháo',
                'description' => '16–22°C.',
                'temperature_min' => 16,
                'temperature_max' => 22,
                'humidity_min' => null,
                'humidity_max' => null,
                'suggested_categories' => [],
                'suggested_tags' => [],
                'suggestion_reason' => 'Ấm người, dễ tiêu',
                'is_active' => true,
                'priority' => 60,
            ],
            [
                'name' => 'Lạnh – Món hầm/cay nóng',
                'description' => '≤15°C.',
                'temperature_min' => null,
                'temperature_max' => 15,
                'humidity_min' => null,
                'humidity_max' => null,
                'suggested_categories' => [],
                'suggested_tags' => [],
                'suggestion_reason' => 'Sinh nhiệt, giàu năng lượng',
                'is_active' => true,
                'priority' => 90,
            ],
            [
                'name' => 'Ẩm rất cao – Ưu tiên nóng ấm, gừng/tiêu',
                'description' => 'Độ ẩm ≥85%.',
                'temperature_min' => null,
                'temperature_max' => null,
                'humidity_min' => 85,
                'humidity_max' => null,
                'suggested_categories' => [],
                'suggested_tags' => [],
                'suggestion_reason' => 'Giảm cảm giác ẩm nặng',
                'is_active' => true,
                'priority' => 85,
            ],
            [
                'name' => 'Khô hanh – Bổ sung nước',
                'description' => 'Độ ẩm ≤40%.',
                'temperature_min' => null,
                'temperature_max' => null,
                'humidity_min' => null,
                'humidity_max' => 40,
                'suggested_categories' => [],
                'suggested_tags' => [],
                'suggestion_reason' => 'Bù ẩm, dễ chịu',
                'is_active' => true,
                'priority' => 55,
            ],
            [
                'name' => 'Ví dụ bạn yêu cầu – Canh/Súp khi ≥22°C & ≥80%',
                'description' => 'Nhiệt độ ≥22°C và độ ẩm ≥80% → món nước ấm.',
                'temperature_min' => 22,
                'temperature_max' => null,
                'humidity_min' => 80,
                'humidity_max' => null,
                'suggested_categories' => [],
                'suggested_tags' => [],
                'suggestion_reason' => 'Ăn canh/súp/cháo cho dễ chịu',
                'is_active' => true,
                'priority' => 95,
            ],
        ];

        foreach ($rules as $rule) {
            WeatherConditionRule::updateOrCreate(
                ['name' => $rule['name']],
                $rule
            );
        }
    }
}