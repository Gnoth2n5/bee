<?php

namespace Database\Seeders;

use App\Models\ModerationRule;
use App\Models\User;
use Illuminate\Database\Seeder;

class ModerationRuleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $admin = User::whereHas('roles', function ($query) {
            $query->where('name', 'admin');
        })->first();

        if (!$admin) {
            $admin = User::first();
        }
        
        if (!$admin) {
            $this->command->error('Không tìm thấy user nào để gán làm người tạo quy tắc.');
            return;
        }

        $rules = [
            [
                'name' => 'Từ ngữ không phù hợp',
                'keywords' => 'đm, địt, đụ, lồn, cặc, buồi, đít, đéo, mẹ kiếp, đcm, đcmđ, đcmđm, đcmđmm, đcmđmmm',
                'action' => 'reject',
                'description' => 'Từ chối các công thức có chứa từ ngữ tục tĩu, không phù hợp',
                'priority' => 10,
                'fields_to_check' => ['title', 'description', 'summary', 'ingredients', 'instructions', 'tips', 'notes'],
                'is_active' => true,
            ],
            [
                'name' => 'Spam và quảng cáo',
                'keywords' => 'mua ngay, giảm giá, khuyến mãi, liên hệ, hotline, website, www, http, https, .com, .vn, 090, 091, 092, 093, 094, 095, 096, 097, 098, 099',
                'action' => 'reject',
                'description' => 'Từ chối các công thức có nội dung spam hoặc quảng cáo',
                'priority' => 9,
                'fields_to_check' => ['title', 'description', 'summary', 'tips', 'notes'],
                'is_active' => true,
            ],
            [
                'name' => 'Nội dung chính trị nhạy cảm',
                'keywords' => 'đảng, chính trị, chính phủ, nhà nước, lãnh đạo, thủ tướng, chủ tịch, bộ trưởng',
                'action' => 'flag',
                'description' => 'Đánh dấu để kiểm tra thủ công các nội dung liên quan đến chính trị',
                'priority' => 8,
                'fields_to_check' => ['title', 'description', 'summary', 'tips', 'notes'],
                'is_active' => true,
            ],
            [
                'name' => 'Từ khóa nhạy cảm',
                'keywords' => 'ma túy, heroin, cocaine, thuốc lắc, cần sa, rượu, bia, thuốc lá, cờ bạc, cá độ',
                'action' => 'reject',
                'description' => 'Từ chối các công thức có nội dung liên quan đến chất cấm hoặc hoạt động bất hợp pháp',
                'priority' => 7,
                'fields_to_check' => ['title', 'description', 'summary', 'ingredients', 'instructions', 'tips', 'notes'],
                'is_active' => true,
            ],
            [
                'name' => 'Nội dung bạo lực',
                'keywords' => 'giết, chết, máu, bạo lực, đánh nhau, đâm, chém, súng, dao, vũ khí',
                'action' => 'reject',
                'description' => 'Từ chối các công thức có nội dung bạo lực hoặc không phù hợp',
                'priority' => 6,
                'fields_to_check' => ['title', 'description', 'summary', 'instructions', 'tips', 'notes'],
                'is_active' => true,
            ],
            [
                'name' => 'Từ khóa tình dục',
                'keywords' => 'sex, tình dục, làm tình, quan hệ, yêu, hôn, ôm, vuốt ve, kích thích',
                'action' => 'flag',
                'description' => 'Đánh dấu để kiểm tra thủ công các nội dung liên quan đến tình dục',
                'priority' => 5,
                'fields_to_check' => ['title', 'description', 'summary', 'tips', 'notes'],
                'is_active' => true,
            ],
            [
                'name' => 'Nội dung phân biệt đối xử',
                'keywords' => 'da đen, da trắng, mọi rợ, man rợ, dân tộc, chủng tộc, phân biệt, kỳ thị',
                'action' => 'reject',
                'description' => 'Từ chối các công thức có nội dung phân biệt đối xử hoặc kỳ thị',
                'priority' => 4,
                'fields_to_check' => ['title', 'description', 'summary', 'tips', 'notes'],
                'is_active' => true,
            ],
            [
                'name' => 'Từ khóa y tế nhạy cảm',
                'keywords' => 'thuốc, bệnh, điều trị, chữa bệnh, bác sĩ, bệnh viện, phòng khám, triệu chứng, chẩn đoán',
                'action' => 'flag',
                'description' => 'Đánh dấu để kiểm tra thủ công các nội dung liên quan đến y tế',
                'priority' => 3,
                'fields_to_check' => ['title', 'description', 'summary', 'ingredients', 'instructions', 'tips', 'notes'],
                'is_active' => true,
            ],
            [
                'name' => 'Nội dung tôn giáo nhạy cảm',
                'keywords' => 'phật, chúa, allah, thánh, thần, cúng, lễ, cầu nguyện, tôn giáo, tín ngưỡng',
                'action' => 'flag',
                'description' => 'Đánh dấu để kiểm tra thủ công các nội dung liên quan đến tôn giáo',
                'priority' => 2,
                'fields_to_check' => ['title', 'description', 'summary', 'tips', 'notes'],
                'is_active' => true,
            ],
            [
                'name' => 'Từ khóa thương hiệu',
                'keywords' => 'coca cola, pepsi, heineken, tiger, carlsberg, marlboro, vinataba, sabeco, habeco',
                'action' => 'auto_approve',
                'description' => 'Cho phép các công thức có đề cập đến thương hiệu nhưng vẫn ghi nhận',
                'priority' => 1,
                'fields_to_check' => ['title', 'description', 'summary', 'ingredients', 'instructions', 'tips', 'notes'],
                'is_active' => true,
            ],
        ];

        foreach ($rules as $ruleData) {
            ModerationRule::updateOrCreate(
                ['name' => $ruleData['name']],
                array_merge($ruleData, ['created_by' => $admin->id])
            );
        }

        $this->command->info('Đã tạo ' . count($rules) . ' quy tắc kiểm duyệt mẫu.');
    }
}