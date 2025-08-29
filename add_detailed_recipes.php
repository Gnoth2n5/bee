<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "Thêm công thức chi tiết cho các món còn thiếu...\n";

$jsonPath = storage_path('app/recipes_data.json');
$recipes = json_decode(file_get_contents($jsonPath), true);

// Danh sách công thức chi tiết cho các món còn thiếu
$detailedRecipes = [
    // Chè
    'Chè Bưởi Non Hạt Sen Long Nhãn' => [
        'ingredients' => [
            ['name' => 'Bưởi non', 'amount' => '200g', 'unit' => 'g'],
            ['name' => 'Hạt sen', 'amount' => '100g', 'unit' => 'g'],
            ['name' => 'Long nhãn', 'amount' => '50g', 'unit' => 'g'],
            ['name' => 'Đường phèn', 'amount' => '100g', 'unit' => 'g'],
            ['name' => 'Nước', 'amount' => '1', 'unit' => 'lít'],
            ['name' => 'Lá dứa', 'amount' => '2', 'unit' => 'lá']
        ],
        'instructions' => [
            ['step' => 1, 'instruction' => 'Ngâm hạt sen qua đêm'],
            ['step' => 2, 'instruction' => 'Nấu hạt sen với nước và lá dứa'],
            ['step' => 3, 'instruction' => 'Thái bưởi non thành miếng nhỏ'],
            ['step' => 4, 'instruction' => 'Thêm bưởi non và long nhãn'],
            ['step' => 5, 'instruction' => 'Nấu đến khi bưởi mềm'],
            ['step' => 6, 'instruction' => 'Thêm đường phèn, thưởng thức nóng hoặc lạnh']
        ]
    ],
    'Chè Đậu Đen Hạt Sen Long Nhãn' => [
        'ingredients' => [
            ['name' => 'Đậu đen', 'amount' => '200g', 'unit' => 'g'],
            ['name' => 'Hạt sen', 'amount' => '100g', 'unit' => 'g'],
            ['name' => 'Long nhãn', 'amount' => '50g', 'unit' => 'g'],
            ['name' => 'Đường phèn', 'amount' => '100g', 'unit' => 'g'],
            ['name' => 'Nước', 'amount' => '1.5', 'unit' => 'lít'],
            ['name' => 'Lá dứa', 'amount' => '2', 'unit' => 'lá']
        ],
        'instructions' => [
            ['step' => 1, 'instruction' => 'Ngâm đậu đen và hạt sen qua đêm'],
            ['step' => 2, 'instruction' => 'Nấu đậu đen với nước và lá dứa'],
            ['step' => 3, 'instruction' => 'Thêm hạt sen, nấu đến mềm'],
            ['step' => 4, 'instruction' => 'Thêm long nhãn'],
            ['step' => 5, 'instruction' => 'Nấu thêm 10 phút'],
            ['step' => 6, 'instruction' => 'Thêm đường phèn, thưởng thức']
        ]
    ],
    // Sashimi
    'Tuna Sashimi' => [
        'ingredients' => [
            ['name' => 'Cá ngừ tươi', 'amount' => '300g', 'unit' => 'g'],
            ['name' => 'Wasabi', 'amount' => 'vừa đủ', 'unit' => ''],
            ['name' => 'Nước tương', 'amount' => 'vừa đủ', 'unit' => ''],
            ['name' => 'Gừng ngâm', 'amount' => 'vừa đủ', 'unit' => ''],
            ['name' => 'Củ cải trắng', 'amount' => '1', 'unit' => 'củ']
        ],
        'instructions' => [
            ['step' => 1, 'instruction' => 'Rửa sạch cá ngừ, lau khô'],
            ['step' => 2, 'instruction' => 'Thái cá thành lát mỏng 3-5mm'],
            ['step' => 3, 'instruction' => 'Bày cá lên đĩa lạnh'],
            ['step' => 4, 'instruction' => 'Thái củ cải thành sợi mỏng'],
            ['step' => 5, 'instruction' => 'Trang trí với củ cải'],
            ['step' => 6, 'instruction' => 'Dùng với wasabi, nước tương và gừng']
        ]
    ],
    'Salmon Sashimi' => [
        'ingredients' => [
            ['name' => 'Cá hồi tươi', 'amount' => '300g', 'unit' => 'g'],
            ['name' => 'Wasabi', 'amount' => 'vừa đủ', 'unit' => ''],
            ['name' => 'Nước tương', 'amount' => 'vừa đủ', 'unit' => ''],
            ['name' => 'Gừng ngâm', 'amount' => 'vừa đủ', 'unit' => ''],
            ['name' => 'Lá shiso', 'amount' => 'vừa đủ', 'unit' => '']
        ],
        'instructions' => [
            ['step' => 1, 'instruction' => 'Rửa sạch cá hồi, lau khô'],
            ['step' => 2, 'instruction' => 'Thái cá thành lát mỏng 3-5mm'],
            ['step' => 3, 'instruction' => 'Bày cá lên đĩa lạnh'],
            ['step' => 4, 'instruction' => 'Trang trí với lá shiso'],
            ['step' => 5, 'instruction' => 'Thêm gừng ngâm'],
            ['step' => 6, 'instruction' => 'Dùng với wasabi và nước tương']
        ]
    ],
    // Nigiri
    'Ebi Nigiri' => [
        'ingredients' => [
            ['name' => 'Gạo sushi', 'amount' => '2', 'unit' => 'chén'],
            ['name' => 'Tôm tươi', 'amount' => '200g', 'unit' => 'g'],
            ['name' => 'Giấm sushi', 'amount' => '3', 'unit' => 'thìa canh'],
            ['name' => 'Muối', 'amount' => '1/2', 'unit' => 'thìa cà phê'],
            ['name' => 'Đường', 'amount' => '1', 'unit' => 'thìa canh'],
            ['name' => 'Wasabi', 'amount' => 'vừa đủ', 'unit' => '']
        ],
        'instructions' => [
            ['step' => 1, 'instruction' => 'Nấu gạo sushi, trộn với giấm, muối, đường'],
            ['step' => 2, 'instruction' => 'Luộc tôm, bóc vỏ, giữ đuôi'],
            ['step' => 3, 'instruction' => 'Vo gạo thành viên nhỏ, ấn hơi dẹt'],
            ['step' => 4, 'instruction' => 'Phết wasabi lên gạo'],
            ['step' => 5, 'instruction' => 'Đặt tôm lên trên, ấn nhẹ'],
            ['step' => 6, 'instruction' => 'Thưởng thức với nước tương']
        ]
    ],
    // Sushi Rolls
    'California Roll' => [
        'ingredients' => [
            ['name' => 'Gạo sushi', 'amount' => '2', 'unit' => 'chén'],
            ['name' => 'Rong biển nori', 'amount' => '3', 'unit' => 'lá'],
            ['name' => 'Cua bể', 'amount' => '200g', 'unit' => 'g'],
            ['name' => 'Bơ', 'amount' => '1', 'unit' => 'quả'],
            ['name' => 'Dưa leo', 'amount' => '1', 'unit' => 'quả'],
            ['name' => 'Mayonnaise', 'amount' => '2', 'unit' => 'thìa canh']
        ],
        'instructions' => [
            ['step' => 1, 'instruction' => 'Nấu gạo sushi, trộn với giấm'],
            ['step' => 2, 'instruction' => 'Trộn cua với mayonnaise'],
            ['step' => 3, 'instruction' => 'Đặt rong biển lên mành tre'],
            ['step' => 4, 'instruction' => 'Phết gạo lên rong biển'],
            ['step' => 5, 'instruction' => 'Xếp cua, bơ, dưa leo'],
            ['step' => 6, 'instruction' => 'Cuộn chặt, cắt thành miếng']
        ]
    ],
    'Dragon Roll' => [
        'ingredients' => [
            ['name' => 'Gạo sushi', 'amount' => '2', 'unit' => 'chén'],
            ['name' => 'Rong biển nori', 'amount' => '3', 'unit' => 'lá'],
            ['name' => 'Tôm tempura', 'amount' => '6', 'unit' => 'con'],
            ['name' => 'Cá hồi', 'amount' => '200g', 'unit' => 'g'],
            ['name' => 'Bơ', 'amount' => '1', 'unit' => 'quả'],
            ['name' => 'Sốt unagi', 'amount' => 'vừa đủ', 'unit' => '']
        ],
        'instructions' => [
            ['step' => 1, 'instruction' => 'Nấu gạo sushi, trộn với giấm'],
            ['step' => 2, 'instruction' => 'Làm tempura tôm'],
            ['step' => 3, 'instruction' => 'Đặt rong biển lên mành tre'],
            ['step' => 4, 'instruction' => 'Phết gạo lên rong biển'],
            ['step' => 5, 'instruction' => 'Xếp tôm tempura, bơ, cá hồi'],
            ['step' => 6, 'instruction' => 'Cuộn chặt, phết sốt unagi']
        ]
    ],
    // Món nướng
    'Cá Lóc Nướng Lá Chuối' => [
        'ingredients' => [
            ['name' => 'Cá lóc', 'amount' => '1', 'unit' => 'con'],
            ['name' => 'Lá chuối', 'amount' => 'vừa đủ', 'unit' => ''],
            ['name' => 'Sả', 'amount' => '3', 'unit' => 'cây'],
            ['name' => 'Nghệ', 'amount' => '1', 'unit' => 'củ'],
            ['name' => 'Muối', 'amount' => '1', 'unit' => 'thìa cà phê'],
            ['name' => 'Tiêu', 'amount' => '1/2', 'unit' => 'thìa cà phê']
        ],
        'instructions' => [
            ['step' => 1, 'instruction' => 'Làm sạch cá lóc, để nguyên con'],
            ['step' => 2, 'instruction' => 'Ướp cá với muối, tiêu, sả, nghệ'],
            ['step' => 3, 'instruction' => 'Bọc cá trong lá chuối'],
            ['step' => 4, 'instruction' => 'Nướng trên than hoa'],
            ['step' => 5, 'instruction' => 'Nướng đến khi cá chín vàng'],
            ['step' => 6, 'instruction' => 'Thưởng thức với nước mắm pha']
        ]
    ],
    'Gà Nướng Lá Chanh' => [
        'ingredients' => [
            ['name' => 'Gà ta', 'amount' => '1', 'unit' => 'con'],
            ['name' => 'Lá chanh', 'amount' => 'vừa đủ', 'unit' => ''],
            ['name' => 'Sả', 'amount' => '5', 'unit' => 'cây'],
            ['name' => 'Nghệ', 'amount' => '2', 'unit' => 'củ'],
            ['name' => 'Nước mắm', 'amount' => '3', 'unit' => 'thìa canh'],
            ['name' => 'Đường', 'amount' => '1', 'unit' => 'thìa canh']
        ],
        'instructions' => [
            ['step' => 1, 'instruction' => 'Làm sạch gà, để nguyên con'],
            ['step' => 2, 'instruction' => 'Ướp gà với nước mắm, đường, sả, nghệ'],
            ['step' => 3, 'instruction' => 'Nhét lá chanh vào bụng gà'],
            ['step' => 4, 'instruction' => 'Nướng gà trên than hoa'],
            ['step' => 5, 'instruction' => 'Nướng đến khi gà chín vàng'],
            ['step' => 6, 'instruction' => 'Thưởng thức với muối tiêu chanh']
        ]
    ],
    // Canh
    'Canh Chua Cá Lóc' => [
        'ingredients' => [
            ['name' => 'Cá lóc', 'amount' => '500g', 'unit' => 'g'],
            ['name' => 'Dứa', 'amount' => '200g', 'unit' => 'g'],
            ['name' => 'Cà chua', 'amount' => '3', 'unit' => 'quả'],
            ['name' => 'Đậu bắp', 'amount' => '100g', 'unit' => 'g'],
            ['name' => 'Bạc hà', 'amount' => 'vừa đủ', 'unit' => ''],
            ['name' => 'Nước mắm', 'amount' => '2', 'unit' => 'thìa canh']
        ],
        'instructions' => [
            ['step' => 1, 'instruction' => 'Làm sạch cá lóc, cắt khúc'],
            ['step' => 2, 'instruction' => 'Nấu nước dùng với xương cá'],
            ['step' => 3, 'instruction' => 'Thêm cá vào nấu'],
            ['step' => 4, 'instruction' => 'Thêm dứa, cà chua, đậu bắp'],
            ['step' => 5, 'instruction' => 'Nấu đến khi rau mềm'],
            ['step' => 6, 'instruction' => 'Thêm bạc hà, nước mắm, thưởng thức']
        ]
    ],
    'Canh Bí Đỏ Thịt Bằm' => [
        'ingredients' => [
            ['name' => 'Bí đỏ', 'amount' => '500g', 'unit' => 'g'],
            ['name' => 'Thịt heo bằm', 'amount' => '200g', 'unit' => 'g'],
            ['name' => 'Hành lá', 'amount' => '3', 'unit' => 'cây'],
            ['name' => 'Nước mắm', 'amount' => '2', 'unit' => 'thìa canh'],
            ['name' => 'Muối', 'amount' => '1/2', 'unit' => 'thìa cà phê'],
            ['name' => 'Tiêu', 'amount' => '1/4', 'unit' => 'thìa cà phê']
        ],
        'instructions' => [
            ['step' => 1, 'instruction' => 'Gọt vỏ bí đỏ, cắt miếng vừa ăn'],
            ['step' => 2, 'instruction' => 'Nấu nước sôi'],
            ['step' => 3, 'instruction' => 'Thêm bí đỏ vào nấu'],
            ['step' => 4, 'instruction' => 'Thêm thịt bằm, khuấy đều'],
            ['step' => 5, 'instruction' => 'Nấu đến khi bí mềm'],
            ['step' => 6, 'instruction' => 'Thêm hành lá, nêm gia vị']
        ]
    ],
    // Món xào
    'Rau Muống Xào Tỏi' => [
        'ingredients' => [
            ['name' => 'Rau muống', 'amount' => '500g', 'unit' => 'g'],
            ['name' => 'Tỏi', 'amount' => '5', 'unit' => 'tép'],
            ['name' => 'Dầu ăn', 'amount' => '2', 'unit' => 'thìa canh'],
            ['name' => 'Nước mắm', 'amount' => '1', 'unit' => 'thìa canh'],
            ['name' => 'Muối', 'amount' => '1/2', 'unit' => 'thìa cà phê'],
            ['name' => 'Tiêu', 'amount' => '1/4', 'unit' => 'thìa cà phê']
        ],
        'instructions' => [
            ['step' => 1, 'instruction' => 'Nhặt rau muống, rửa sạch'],
            ['step' => 2, 'instruction' => 'Băm nhỏ tỏi'],
            ['step' => 3, 'instruction' => 'Phi thơm tỏi trong dầu'],
            ['step' => 4, 'instruction' => 'Thêm rau muống vào xào'],
            ['step' => 5, 'instruction' => 'Xào nhanh tay đến khi rau chín'],
            ['step' => 6, 'instruction' => 'Thêm nước mắm, nêm gia vị']
        ]
    ],
    // Bánh
    'Bánh Nậm' => [
        'ingredients' => [
            ['name' => 'Bột gạo', 'amount' => '300g', 'unit' => 'g'],
            ['name' => 'Thịt heo xay', 'amount' => '200g', 'unit' => 'g'],
            ['name' => 'Tôm khô', 'amount' => '50g', 'unit' => 'g'],
            ['name' => 'Lá chuối', 'amount' => 'vừa đủ', 'unit' => ''],
            ['name' => 'Nước mắm', 'amount' => '2', 'unit' => 'thìa canh'],
            ['name' => 'Hành lá', 'amount' => '3', 'unit' => 'cây']
        ],
        'instructions' => [
            ['step' => 1, 'instruction' => 'Pha bột gạo với nước'],
            ['step' => 2, 'instruction' => 'Trộn thịt với tôm khô, hành lá'],
            ['step' => 3, 'instruction' => 'Cắt lá chuối thành hình chữ nhật'],
            ['step' => 4, 'instruction' => 'Đổ bột lên lá chuối'],
            ['step' => 5, 'instruction' => 'Thêm nhân thịt lên trên'],
            ['step' => 6, 'instruction' => 'Hấp 15-20 phút đến chín']
        ]
    ],
    'Bánh Lọc' => [
        'ingredients' => [
            ['name' => 'Bột năng', 'amount' => '300g', 'unit' => 'g'],
            ['name' => 'Tôm tươi', 'amount' => '200g', 'unit' => 'g'],
            ['name' => 'Thịt heo xay', 'amount' => '100g', 'unit' => 'g'],
            ['name' => 'Lá chuối', 'amount' => 'vừa đủ', 'unit' => ''],
            ['name' => 'Nước mắm', 'amount' => '2', 'unit' => 'thìa canh'],
            ['name' => 'Hành phi', 'amount' => '2', 'unit' => 'thìa canh']
        ],
        'instructions' => [
            ['step' => 1, 'instruction' => 'Pha bột năng với nước sôi'],
            ['step' => 2, 'instruction' => 'Trộn tôm và thịt với gia vị'],
            ['step' => 3, 'instruction' => 'Cắt lá chuối thành hình vuông'],
            ['step' => 4, 'instruction' => 'Đặt nhân tôm thịt lên lá'],
            ['step' => 5, 'instruction' => 'Bọc bột năng quanh nhân'],
            ['step' => 6, 'instruction' => 'Hấp 10-15 phút đến chín']
        ]
    ]
];

$updatedCount = 0;
foreach ($recipes as &$recipe) {
    $title = $recipe['title'];
    
    // Kiểm tra xem có công thức chi tiết không
    if (isset($detailedRecipes[$title])) {
        $recipe['ingredients'] = $detailedRecipes[$title]['ingredients'];
        $recipe['instructions'] = $detailedRecipes[$title]['instructions'];
        $updatedCount++;
        echo "Đã cập nhật công thức chi tiết: {$title}\n";
    }
}

file_put_contents($jsonPath, json_encode($recipes, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

echo "\nHoàn thành! Đã cập nhật {$updatedCount} món ăn với công thức chi tiết.\n";
