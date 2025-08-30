<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\DietaryRule;
use App\Models\DiseaseCondition;

class DietaryRuleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $rules = [
            // Tiểu đường
            [
                'disease_name' => 'Tiểu đường',
                'rules' => [
                    [
                        'name' => 'Hạn chế đường',
                        'description' => 'Giảm thiểu lượng đường trong chế độ ăn',
                        'ingredients' => ['đường', 'bánh kẹo', 'nước ngọt', 'mật ong'],
                        'cooking_restrictions' => ['nêm đường', 'thêm đường'],
                        'portion_limits' => ['Đường: tối đa 25g/ngày'],
                        'substitutions' => [
                            ['from' => 'đường', 'to' => 'stevia'],
                            ['from' => 'mật ong', 'to' => 'quả chà là']
                        ],
                        'priority' => 5
                    ],
                    [
                        'name' => 'Giảm carbohydrate tinh chế',
                        'description' => 'Thay thế carbohydrate tinh chế bằng ngũ cốc nguyên hạt',
                        'ingredients' => ['gạo trắng', 'bánh mì trắng', 'mì trắng'],
                        'cooking_restrictions' => [],
                        'portion_limits' => ['Gạo trắng: tối đa 1/2 chén/bữa'],
                        'substitutions' => [
                            ['from' => 'gạo trắng', 'to' => 'gạo lứt'],
                            ['from' => 'bánh mì trắng', 'to' => 'bánh mì nguyên cám']
                        ],
                        'priority' => 4
                    ]
                ]
            ],
            // Cao huyết áp
            [
                'disease_name' => 'Cao huyết áp',
                'rules' => [
                    [
                        'name' => 'Giảm muối',
                        'description' => 'Hạn chế lượng muối trong chế độ ăn',
                        'ingredients' => ['muối', 'mắm', 'dưa cà', 'thực phẩm chế biến'],
                        'cooking_restrictions' => ['nêm muối nhiều', 'thêm mắm'],
                        'portion_limits' => ['Muối: tối đa 2g/ngày'],
                        'substitutions' => [
                            ['from' => 'muối', 'to' => 'gia vị thảo mộc'],
                            ['from' => 'mắm', 'to' => 'nước mắm ít muối']
                        ],
                        'priority' => 5
                    ],
                    [
                        'name' => 'Tăng kali',
                        'description' => 'Bổ sung thực phẩm giàu kali',
                        'ingredients' => ['chuối', 'khoai tây', 'rau xanh'],
                        'cooking_restrictions' => [],
                        'portion_limits' => [],
                        'substitutions' => [],
                        'priority' => 3
                    ]
                ]
            ],
            // Gout
            [
                'disease_name' => 'Gout',
                'rules' => [
                    [
                        'name' => 'Giảm purin',
                        'description' => 'Hạn chế thực phẩm giàu purin',
                        'ingredients' => ['thịt đỏ', 'hải sản', 'nội tạng', 'nấm'],
                        'cooking_restrictions' => ['nấu thịt đỏ', 'nấu hải sản'],
                        'portion_limits' => ['Thịt đỏ: tối đa 100g/tuần'],
                        'substitutions' => [
                            ['from' => 'thịt đỏ', 'to' => 'thịt trắng'],
                            ['from' => 'hải sản', 'to' => 'cá nước ngọt']
                        ],
                        'priority' => 5
                    ],
                    [
                        'name' => 'Tăng nước',
                        'description' => 'Uống nhiều nước để thải axit uric',
                        'ingredients' => [],
                        'cooking_restrictions' => [],
                        'portion_limits' => ['Nước: ít nhất 2L/ngày'],
                        'substitutions' => [],
                        'priority' => 4
                    ]
                ]
            ],
            // Bệnh tim mạch
            [
                'disease_name' => 'Bệnh tim mạch',
                'rules' => [
                    [
                        'name' => 'Giảm chất béo bão hòa',
                        'description' => 'Hạn chế chất béo có hại cho tim',
                        'ingredients' => ['mỡ động vật', 'bơ', 'phô mai béo'],
                        'cooking_restrictions' => ['chiên nhiều dầu', 'nấu với mỡ'],
                        'portion_limits' => ['Chất béo bão hòa: tối đa 13g/ngày'],
                        'substitutions' => [
                            ['from' => 'mỡ động vật', 'to' => 'dầu olive'],
                            ['from' => 'bơ', 'to' => 'dầu thực vật']
                        ],
                        'priority' => 5
                    ],
                    [
                        'name' => 'Tăng omega-3',
                        'description' => 'Bổ sung axit béo omega-3',
                        'ingredients' => ['cá béo', 'hạt chia', 'hạt lanh'],
                        'cooking_restrictions' => [],
                        'portion_limits' => [],
                        'substitutions' => [],
                        'priority' => 4
                    ]
                ]
            ]
        ];

        foreach ($rules as $diseaseRule) {
            $disease = DiseaseCondition::where('name', $diseaseRule['disease_name'])->first();

            if ($disease) {
                foreach ($diseaseRule['rules'] as $rule) {
                    DietaryRule::create([
                        'disease_condition_id' => $disease->id,
                        'name' => $rule['name'],
                        'description' => $rule['description'],
                        'ingredients' => $rule['ingredients'],
                        'cooking_restrictions' => $rule['cooking_restrictions'],
                        'portion_limits' => $rule['portion_limits'],
                        'substitutions' => $rule['substitutions'],
                        'priority' => $rule['priority'],
                        'is_active' => true
                    ]);
                }
            }
        }
    }
}
