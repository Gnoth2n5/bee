<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\DiseaseCondition;

class DiseaseConditionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $diseases = [
            [
                'name' => 'Tiểu đường',
                'description' => 'Bệnh tiểu đường là tình trạng cơ thể không sản xuất đủ insulin hoặc không sử dụng insulin hiệu quả, dẫn đến lượng đường trong máu cao.',
                'symptoms' => ['Khát nước thường xuyên', 'Đi tiểu nhiều', 'Mệt mỏi', 'Sụt cân không rõ nguyên nhân'],
                'restricted_foods' => ['Đường', 'Bánh kẹo', 'Nước ngọt', 'Gạo trắng', 'Bánh mì trắng', 'Khoai tây', 'Trái cây ngọt'],
                'recommended_foods' => ['Rau xanh', 'Cá', 'Thịt nạc', 'Gạo lứt', 'Yến mạch', 'Đậu', 'Hạt chia'],
                'nutritional_requirements' => ['Giảm carbohydrate', 'Tăng protein', 'Tăng chất xơ'],
                'cooking_methods' => ['Hấp', 'Luộc', 'Nướng', 'Xào ít dầu'],
                'meal_timing' => ['Ăn đều đặn 3 bữa', 'Không bỏ bữa', 'Ăn nhẹ giữa các bữa'],
                'severity_level' => 3
            ],
            [
                'name' => 'Cao huyết áp',
                'description' => 'Cao huyết áp là tình trạng áp lực máu trong động mạch cao hơn bình thường, có thể gây ra các biến chứng tim mạch.',
                'symptoms' => ['Đau đầu', 'Chóng mặt', 'Mệt mỏi', 'Khó thở', 'Đau ngực'],
                'restricted_foods' => ['Muối', 'Mắm', 'Dưa cà', 'Thực phẩm chế biến', 'Thịt đỏ', 'Rượu bia'],
                'recommended_foods' => ['Rau xanh', 'Chuối', 'Cá', 'Sữa ít béo', 'Yến mạch', 'Hạt hạnh nhân'],
                'nutritional_requirements' => ['Giảm natri', 'Tăng kali', 'Tăng canxi', 'Giảm chất béo'],
                'cooking_methods' => ['Hấp', 'Luộc', 'Nướng', 'Xào ít dầu'],
                'meal_timing' => ['Ăn đều đặn', 'Không ăn quá no', 'Ăn chậm'],
                'severity_level' => 4
            ],
            [
                'name' => 'Gout',
                'description' => 'Gout là bệnh viêm khớp do tích tụ axit uric trong máu, gây đau đớn và sưng tấy ở các khớp.',
                'symptoms' => ['Đau khớp dữ dội', 'Sưng đỏ khớp', 'Cứng khớp', 'Sốt nhẹ'],
                'restricted_foods' => ['Thịt đỏ', 'Hải sản', 'Nội tạng', 'Bia rượu', 'Nấm', 'Măng tây'],
                'recommended_foods' => ['Rau xanh', 'Sữa ít béo', 'Trái cây', 'Ngũ cốc', 'Đậu', 'Cá nước ngọt'],
                'nutritional_requirements' => ['Giảm purin', 'Tăng nước', 'Tăng vitamin C'],
                'cooking_methods' => ['Hấp', 'Luộc', 'Nướng'],
                'meal_timing' => ['Ăn đều đặn', 'Uống nhiều nước', 'Tránh ăn khuya'],
                'severity_level' => 3
            ],
            [
                'name' => 'Bệnh tim mạch',
                'description' => 'Bệnh tim mạch bao gồm các vấn đề về tim và mạch máu, có thể dẫn đến đau tim hoặc đột quỵ.',
                'symptoms' => ['Đau ngực', 'Khó thở', 'Mệt mỏi', 'Chóng mặt', 'Sưng chân'],
                'restricted_foods' => ['Chất béo bão hòa', 'Cholesterol', 'Muối', 'Đường', 'Thực phẩm chế biến'],
                'recommended_foods' => ['Cá béo', 'Rau xanh', 'Trái cây', 'Ngũ cốc nguyên hạt', 'Dầu olive'],
                'nutritional_requirements' => ['Giảm chất béo', 'Tăng omega-3', 'Tăng chất xơ'],
                'cooking_methods' => ['Hấp', 'Luộc', 'Nướng', 'Xào ít dầu'],
                'meal_timing' => ['Ăn đều đặn', 'Không ăn quá no', 'Ăn chậm'],
                'severity_level' => 5
            ],
            [
                'name' => 'Bệnh thận',
                'description' => 'Bệnh thận ảnh hưởng đến khả năng lọc máu và loại bỏ chất thải của thận.',
                'symptoms' => ['Mệt mỏi', 'Sưng chân', 'Thay đổi nước tiểu', 'Buồn nôn', 'Chán ăn'],
                'restricted_foods' => ['Muối', 'Kali cao', 'Phốt pho cao', 'Protein động vật', 'Thực phẩm chế biến'],
                'recommended_foods' => ['Rau xanh ít kali', 'Trái cây ít kali', 'Protein thực vật', 'Ngũ cốc'],
                'nutritional_requirements' => ['Giảm natri', 'Giảm kali', 'Giảm phốt pho', 'Giảm protein'],
                'cooking_methods' => ['Hấp', 'Luộc', 'Nướng'],
                'meal_timing' => ['Ăn đều đặn', 'Uống nước vừa phải'],
                'severity_level' => 4
            ],
            [
                'name' => 'Bệnh gan',
                'description' => 'Bệnh gan ảnh hưởng đến chức năng gan, có thể do viêm gan, xơ gan hoặc các vấn đề khác.',
                'symptoms' => ['Mệt mỏi', 'Vàng da', 'Đau bụng', 'Buồn nôn', 'Chán ăn'],
                'restricted_foods' => ['Rượu bia', 'Thực phẩm nhiều chất béo', 'Thực phẩm chế biến', 'Muối'],
                'recommended_foods' => ['Rau xanh', 'Trái cây', 'Ngũ cốc nguyên hạt', 'Protein nạc', 'Sữa ít béo'],
                'nutritional_requirements' => ['Giảm chất béo', 'Tăng protein', 'Tăng vitamin'],
                'cooking_methods' => ['Hấp', 'Luộc', 'Nướng'],
                'meal_timing' => ['Ăn đều đặn', 'Không ăn quá no'],
                'severity_level' => 4
            ]
        ];

        foreach ($diseases as $disease) {
            DiseaseCondition::create($disease);
        }
    }
}
