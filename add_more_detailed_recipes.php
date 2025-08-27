<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "Thêm công thức chi tiết cho các món còn lại...\n";

$jsonPath = storage_path('app/recipes_data.json');
$recipes = json_decode(file_get_contents($jsonPath), true);

// Danh sách công thức chi tiết cho các món còn lại
$moreDetailedRecipes = [
    // Sashimi còn lại
    'Ebi Sashimi' => [
        'ingredients' => [
            ['name' => 'Tôm tươi', 'amount' => '300g', 'unit' => 'g'],
            ['name' => 'Wasabi', 'amount' => 'vừa đủ', 'unit' => ''],
            ['name' => 'Nước tương', 'amount' => 'vừa đủ', 'unit' => ''],
            ['name' => 'Gừng ngâm', 'amount' => 'vừa đủ', 'unit' => ''],
            ['name' => 'Lá shiso', 'amount' => 'vừa đủ', 'unit' => '']
        ],
        'instructions' => [
            ['step' => 1, 'instruction' => 'Làm sạch tôm, bóc vỏ'],
            ['step' => 2, 'instruction' => 'Thái tôm thành lát mỏng'],
            ['step' => 3, 'instruction' => 'Bày tôm lên đĩa lạnh'],
            ['step' => 4, 'instruction' => 'Trang trí với lá shiso'],
            ['step' => 5, 'instruction' => 'Thêm gừng ngâm'],
            ['step' => 6, 'instruction' => 'Dùng với wasabi và nước tương']
        ]
    ],
    'Hamachi Sashimi' => [
        'ingredients' => [
            ['name' => 'Cá hamachi tươi', 'amount' => '300g', 'unit' => 'g'],
            ['name' => 'Wasabi', 'amount' => 'vừa đủ', 'unit' => ''],
            ['name' => 'Nước tương', 'amount' => 'vừa đủ', 'unit' => ''],
            ['name' => 'Gừng ngâm', 'amount' => 'vừa đủ', 'unit' => ''],
            ['name' => 'Củ cải trắng', 'amount' => '1', 'unit' => 'củ']
        ],
        'instructions' => [
            ['step' => 1, 'instruction' => 'Rửa sạch cá hamachi, lau khô'],
            ['step' => 2, 'instruction' => 'Thái cá thành lát mỏng 3-5mm'],
            ['step' => 3, 'instruction' => 'Bày cá lên đĩa lạnh'],
            ['step' => 4, 'instruction' => 'Thái củ cải thành sợi mỏng'],
            ['step' => 5, 'instruction' => 'Trang trí với củ cải'],
            ['step' => 6, 'instruction' => 'Dùng với wasabi, nước tương và gừng']
        ]
    ],
    'Maguro Sashimi' => [
        'ingredients' => [
            ['name' => 'Cá ngừ maguro tươi', 'amount' => '300g', 'unit' => 'g'],
            ['name' => 'Wasabi', 'amount' => 'vừa đủ', 'unit' => ''],
            ['name' => 'Nước tương', 'amount' => 'vừa đủ', 'unit' => ''],
            ['name' => 'Gừng ngâm', 'amount' => 'vừa đủ', 'unit' => ''],
            ['name' => 'Lá shiso', 'amount' => 'vừa đủ', 'unit' => '']
        ],
        'instructions' => [
            ['step' => 1, 'instruction' => 'Rửa sạch cá maguro, lau khô'],
            ['step' => 2, 'instruction' => 'Thái cá thành lát mỏng 3-5mm'],
            ['step' => 3, 'instruction' => 'Bày cá lên đĩa lạnh'],
            ['step' => 4, 'instruction' => 'Trang trí với lá shiso'],
            ['step' => 5, 'instruction' => 'Thêm gừng ngâm'],
            ['step' => 6, 'instruction' => 'Dùng với wasabi và nước tương']
        ]
    ],
    'Sake Sashimi' => [
        'ingredients' => [
            ['name' => 'Cá hồi sake tươi', 'amount' => '300g', 'unit' => 'g'],
            ['name' => 'Wasabi', 'amount' => 'vừa đủ', 'unit' => ''],
            ['name' => 'Nước tương', 'amount' => 'vừa đủ', 'unit' => ''],
            ['name' => 'Gừng ngâm', 'amount' => 'vừa đủ', 'unit' => ''],
            ['name' => 'Lá shiso', 'amount' => 'vừa đủ', 'unit' => '']
        ],
        'instructions' => [
            ['step' => 1, 'instruction' => 'Rửa sạch cá sake, lau khô'],
            ['step' => 2, 'instruction' => 'Thái cá thành lát mỏng 3-5mm'],
            ['step' => 3, 'instruction' => 'Bày cá lên đĩa lạnh'],
            ['step' => 4, 'instruction' => 'Trang trí với lá shiso'],
            ['step' => 5, 'instruction' => 'Thêm gừng ngâm'],
            ['step' => 6, 'instruction' => 'Dùng với wasabi và nước tương']
        ]
    ],
    // Nigiri còn lại
    'Maguro Nigiri' => [
        'ingredients' => [
            ['name' => 'Gạo sushi', 'amount' => '2', 'unit' => 'chén'],
            ['name' => 'Cá ngừ maguro', 'amount' => '200g', 'unit' => 'g'],
            ['name' => 'Giấm sushi', 'amount' => '3', 'unit' => 'thìa canh'],
            ['name' => 'Muối', 'amount' => '1/2', 'unit' => 'thìa cà phê'],
            ['name' => 'Đường', 'amount' => '1', 'unit' => 'thìa canh'],
            ['name' => 'Đường', 'amount' => '1', 'unit' => 'thìa canh']
        ],
        'instructions' => [
            ['step' => 1, 'instruction' => 'Nấu gạo sushi, trộn với giấm, muối, đường'],
            ['step' => 2, 'instruction' => 'Thái cá maguro thành miếng mỏng vừa ăn'],
            ['step' => 3, 'instruction' => 'Vo gạo thành viên nhỏ, ấn hơi dẹt'],
            ['step' => 4, 'instruction' => 'Phết wasabi lên gạo'],
            ['step' => 5, 'instruction' => 'Đặt cá maguro lên trên, ấn nhẹ'],
            ['step' => 6, 'instruction' => 'Thưởng thức với nước tương']
        ]
    ],
    'Sake Nigiri' => [
        'ingredients' => [
            ['name' => 'Gạo sushi', 'amount' => '2', 'unit' => 'chén'],
            ['name' => 'Cá hồi sake', 'amount' => '200g', 'unit' => 'g'],
            ['name' => 'Giấm sushi', 'amount' => '3', 'unit' => 'thìa canh'],
            ['name' => 'Muối', 'amount' => '1/2', 'unit' => 'thìa cà phê'],
            ['name' => 'Đường', 'amount' => '1', 'unit' => 'thìa canh'],
            ['name' => 'Đường', 'amount' => '1', 'unit' => 'thìa canh']
        ],
        'instructions' => [
            ['step' => 1, 'instruction' => 'Nấu gạo sushi, trộn với giấm, muối, đường'],
            ['step' => 2, 'instruction' => 'Thái cá sake thành miếng mỏng vừa ăn'],
            ['step' => 3, 'instruction' => 'Vo gạo thành viên nhỏ, ấn hơi dẹt'],
            ['step' => 4, 'instruction' => 'Phết wasabi lên gạo'],
            ['step' => 5, 'instruction' => 'Đặt cá sake lên trên, ấn nhẹ'],
            ['step' => 6, 'instruction' => 'Thưởng thức với nước tương']
        ]
    ],
    'Hamachi Nigiri' => [
        'ingredients' => [
            ['name' => 'Gạo sushi', 'amount' => '2', 'unit' => 'chén'],
            ['name' => 'Cá hamachi', 'amount' => '200g', 'unit' => 'g'],
            ['name' => 'Giấm sushi', 'amount' => '3', 'unit' => 'thìa canh'],
            ['name' => 'Muối', 'amount' => '1/2', 'unit' => 'thìa cà phê'],
            ['name' => 'Đường', 'amount' => '1', 'unit' => 'thìa canh'],
            ['name' => 'Đường', 'amount' => '1', 'unit' => 'thìa canh']
        ],
        'instructions' => [
            ['step' => 1, 'instruction' => 'Nấu gạo sushi, trộn với giấm, muối, đường'],
            ['step' => 2, 'instruction' => 'Thái cá hamachi thành miếng mỏng vừa ăn'],
            ['step' => 3, 'instruction' => 'Vo gạo thành viên nhỏ, ấn hơi dẹt'],
            ['step' => 4, 'instruction' => 'Phết wasabi lên gạo'],
            ['step' => 5, 'instruction' => 'Đặt cá hamachi lên trên, ấn nhẹ'],
            ['step' => 6, 'instruction' => 'Thưởng thức với nước tương']
        ]
    ],
    'Unagi Nigiri' => [
        'ingredients' => [
            ['name' => 'Gạo sushi', 'amount' => '2', 'unit' => 'chén'],
            ['name' => 'Lươn nướng', 'amount' => '200g', 'unit' => 'g'],
            ['name' => 'Giấm sushi', 'amount' => '3', 'unit' => 'thìa canh'],
            ['name' => 'Muối', 'amount' => '1/2', 'unit' => 'thìa cà phê'],
            ['name' => 'Đường', 'amount' => '1', 'unit' => 'thìa canh'],
            ['name' => 'Sốt unagi', 'amount' => 'vừa đủ', 'unit' => '']
        ],
        'instructions' => [
            ['step' => 1, 'instruction' => 'Nấu gạo sushi, trộn với giấm, muối, đường'],
            ['step' => 2, 'instruction' => 'Thái lươn nướng thành miếng mỏng vừa ăn'],
            ['step' => 3, 'instruction' => 'Vo gạo thành viên nhỏ, ấn hơi dẹt'],
            ['step' => 4, 'instruction' => 'Phết sốt unagi lên gạo'],
            ['step' => 5, 'instruction' => 'Đặt lươn lên trên, ấn nhẹ'],
            ['step' => 6, 'instruction' => 'Thưởng thức với sốt unagi']
        ]
    ],
    'Tamago Nigiri' => [
        'ingredients' => [
            ['name' => 'Gạo sushi', 'amount' => '2', 'unit' => 'chén'],
            ['name' => 'Trứng gà', 'amount' => '3', 'unit' => 'quả'],
            ['name' => 'Giấm sushi', 'amount' => '3', 'unit' => 'thìa canh'],
            ['name' => 'Muối', 'amount' => '1/2', 'unit' => 'thìa cà phê'],
            ['name' => 'Đường', 'amount' => '1', 'unit' => 'thìa canh'],
            ['name' => 'Nước tương', 'amount' => '1', 'unit' => 'thìa canh']
        ],
        'instructions' => [
            ['step' => 1, 'instruction' => 'Nấu gạo sushi, trộn với giấm, muối, đường'],
            ['step' => 2, 'instruction' => 'Đánh trứng với nước tương, đường'],
            ['step' => 3, 'instruction' => 'Rán trứng thành lớp mỏng'],
            ['step' => 4, 'instruction' => 'Vo gạo thành viên nhỏ, ấn hơi dẹt'],
            ['step' => 5, 'instruction' => 'Đặt trứng lên trên, ấn nhẹ'],
            ['step' => 6, 'instruction' => 'Thưởng thức với nước tương']
        ]
    ],
    // Sushi Rolls còn lại
    'Spider Roll' => [
        'ingredients' => [
            ['name' => 'Gạo sushi', 'amount' => '2', 'unit' => 'chén'],
            ['name' => 'Rong biển nori', 'amount' => '3', 'unit' => 'lá'],
            ['name' => 'Cua bể', 'amount' => '300g', 'unit' => 'g'],
            ['name' => 'Bơ', 'amount' => '1', 'unit' => 'quả'],
            ['name' => 'Dưa leo', 'amount' => '1', 'unit' => 'quả'],
            ['name' => 'Mayonnaise', 'amount' => '2', 'unit' => 'thìa canh']
        ],
        'instructions' => [
            ['step' => 1, 'instruction' => 'Nấu gạo sushi, trộn với giấm'],
            ['step' => 2, 'instruction' => 'Luộc cua, bóc thịt'],
            ['step' => 3, 'instruction' => 'Đặt rong biển lên mành tre'],
            ['step' => 4, 'instruction' => 'Phết gạo lên rong biển'],
            ['step' => 5, 'instruction' => 'Xếp thịt cua, bơ, dưa leo'],
            ['step' => 6, 'instruction' => 'Cuộn chặt, cắt thành miếng']
        ]
    ],
    'Philadelphia Roll' => [
        'ingredients' => [
            ['name' => 'Gạo sushi', 'amount' => '2', 'unit' => 'chén'],
            ['name' => 'Rong biển nori', 'amount' => '3', 'unit' => 'lá'],
            ['name' => 'Cá hồi', 'amount' => '200g', 'unit' => 'g'],
            ['name' => 'Phô mai cream cheese', 'amount' => '100g', 'unit' => 'g'],
            ['name' => 'Bơ', 'amount' => '1', 'unit' => 'quả'],
            ['name' => 'Dưa leo', 'amount' => '1', 'unit' => 'quả']
        ],
        'instructions' => [
            ['step' => 1, 'instruction' => 'Nấu gạo sushi, trộn với giấm'],
            ['step' => 2, 'instruction' => 'Thái cá hồi thành lát mỏng'],
            ['step' => 3, 'instruction' => 'Đặt rong biển lên mành tre'],
            ['step' => 4, 'instruction' => 'Phết gạo lên rong biển'],
            ['step' => 5, 'instruction' => 'Xếp cá hồi, cream cheese, bơ, dưa leo'],
            ['step' => 6, 'instruction' => 'Cuộn chặt, cắt thành miếng']
        ]
    ],
    'Rainbow Roll' => [
        'ingredients' => [
            ['name' => 'Gạo sushi', 'amount' => '2', 'unit' => 'chén'],
            ['name' => 'Rong biển nori', 'amount' => '3', 'unit' => 'lá'],
            ['name' => 'Cá hồi', 'amount' => '100g', 'unit' => 'g'],
            ['name' => 'Cá ngừ', 'amount' => '100g', 'unit' => 'g'],
            ['name' => 'Cá hamachi', 'amount' => '100g', 'unit' => 'g'],
            ['name' => 'Bơ', 'amount' => '1', 'unit' => 'quả']
        ],
        'instructions' => [
            ['step' => 1, 'instruction' => 'Nấu gạo sushi, trộn với giấm'],
            ['step' => 2, 'instruction' => 'Thái các loại cá thành lát mỏng'],
            ['step' => 3, 'instruction' => 'Đặt rong biển lên mành tre'],
            ['step' => 4, 'instruction' => 'Phết gạo lên rong biển'],
            ['step' => 5, 'instruction' => 'Xếp bơ và các loại cá'],
            ['step' => 6, 'instruction' => 'Cuộn chặt, cắt thành miếng']
        ]
    ],
    // Món nướng còn lại
    'Thịt Bò Nướng Lá Lốt' => [
        'ingredients' => [
            ['name' => 'Thịt bò', 'amount' => '500g', 'unit' => 'g'],
            ['name' => 'Lá lốt', 'amount' => 'vừa đủ', 'unit' => ''],
            ['name' => 'Sả', 'amount' => '3', 'unit' => 'cây'],
            ['name' => 'Nghệ', 'amount' => '1', 'unit' => 'củ'],
            ['name' => 'Nước mắm', 'amount' => '3', 'unit' => 'thìa canh'],
            ['name' => 'Đường', 'amount' => '1', 'unit' => 'thìa canh']
        ],
        'instructions' => [
            ['step' => 1, 'instruction' => 'Thái thịt bò thành miếng mỏng'],
            ['step' => 2, 'instruction' => 'Ướp thịt với nước mắm, đường, sả, nghệ'],
            ['step' => 3, 'instruction' => 'Bọc thịt trong lá lốt'],
            ['step' => 4, 'instruction' => 'Nướng trên than hoa'],
            ['step' => 5, 'instruction' => 'Nướng đến khi thịt chín vàng'],
            ['step' => 6, 'instruction' => 'Thưởng thức với nước mắm pha']
        ]
    ],
    'Tôm Nướng Muối Ớt' => [
        'ingredients' => [
            ['name' => 'Tôm sú', 'amount' => '500g', 'unit' => 'g'],
            ['name' => 'Muối', 'amount' => '2', 'unit' => 'thìa canh'],
            ['name' => 'Ớt hiểm', 'amount' => '5', 'unit' => 'quả'],
            ['name' => 'Sả', 'amount' => '3', 'unit' => 'cây'],
            ['name' => 'Tỏi', 'amount' => '5', 'unit' => 'tép'],
            ['name' => 'Dầu ăn', 'amount' => '2', 'unit' => 'thìa canh']
        ],
        'instructions' => [
            ['step' => 1, 'instruction' => 'Làm sạch tôm, để nguyên vỏ'],
            ['step' => 2, 'instruction' => 'Băm nhỏ ớt, sả, tỏi'],
            ['step' => 3, 'instruction' => 'Trộn muối với ớt, sả, tỏi'],
            ['step' => 4, 'instruction' => 'Ướp tôm với hỗn hợp muối ớt'],
            ['step' => 5, 'instruction' => 'Nướng tôm trên than hoa'],
            ['step' => 6, 'instruction' => 'Nướng đến khi tôm chín đỏ']
        ]
    ],
    // Canh còn lại
    'Canh Rau Muống' => [
        'ingredients' => [
            ['name' => 'Rau muống', 'amount' => '500g', 'unit' => 'g'],
            ['name' => 'Thịt heo bằm', 'amount' => '200g', 'unit' => 'g'],
            ['name' => 'Hành lá', 'amount' => '3', 'unit' => 'cây'],
            ['name' => 'Nước mắm', 'amount' => '2', 'unit' => 'thìa canh'],
            ['name' => 'Muối', 'amount' => '1/2', 'unit' => 'thìa cà phê'],
            ['name' => 'Tiêu', 'amount' => '1/4', 'unit' => 'thìa cà phê']
        ],
        'instructions' => [
            ['step' => 1, 'instruction' => 'Nhặt rau muống, rửa sạch'],
            ['step' => 2, 'instruction' => 'Nấu nước sôi'],
            ['step' => 3, 'instruction' => 'Thêm thịt bằm vào nấu'],
            ['step' => 4, 'instruction' => 'Thêm rau muống vào nấu'],
            ['step' => 5, 'instruction' => 'Nấu đến khi rau mềm'],
            ['step' => 6, 'instruction' => 'Thêm hành lá, nêm gia vị']
        ]
    ],
    'Canh Mướp Đắng Nhồi Thịt' => [
        'ingredients' => [
            ['name' => 'Mướp đắng', 'amount' => '3', 'unit' => 'quả'],
            ['name' => 'Thịt heo xay', 'amount' => '300g', 'unit' => 'g'],
            ['name' => 'Hành lá', 'amount' => '3', 'unit' => 'cây'],
            ['name' => 'Nấm mèo', 'amount' => '50g', 'unit' => 'g'],
            ['name' => 'Nước mắm', 'amount' => '2', 'unit' => 'thìa canh'],
            ['name' => 'Muối', 'amount' => '1/2', 'unit' => 'thìa cà phê']
        ],
        'instructions' => [
            ['step' => 1, 'instruction' => 'Cắt mướp đắng thành khúc, bỏ ruột'],
            ['step' => 2, 'instruction' => 'Trộn thịt với nấm mèo, hành lá'],
            ['step' => 3, 'instruction' => 'Nhồi thịt vào mướp đắng'],
            ['step' => 4, 'instruction' => 'Nấu nước sôi'],
            ['step' => 5, 'instruction' => 'Thêm mướp đắng nhồi thịt vào nấu'],
            ['step' => 6, 'instruction' => 'Nấu đến khi mướp mềm, nêm gia vị']
        ]
    ],
    'Canh Chua Tôm' => [
        'ingredients' => [
            ['name' => 'Tôm sú', 'amount' => '400g', 'unit' => 'g'],
            ['name' => 'Dứa', 'amount' => '200g', 'unit' => 'g'],
            ['name' => 'Cà chua', 'amount' => '3', 'unit' => 'quả'],
            ['name' => 'Đậu bắp', 'amount' => '100g', 'unit' => 'g'],
            ['name' => 'Bạc hà', 'amount' => 'vừa đủ', 'unit' => ''],
            ['name' => 'Nước mắm', 'amount' => '2', 'unit' => 'thìa canh']
        ],
        'instructions' => [
            ['step' => 1, 'instruction' => 'Làm sạch tôm, bóc vỏ'],
            ['step' => 2, 'instruction' => 'Nấu nước dùng với đầu tôm'],
            ['step' => 3, 'instruction' => 'Thêm tôm vào nấu'],
            ['step' => 4, 'instruction' => 'Thêm dứa, cà chua, đậu bắp'],
            ['step' => 5, 'instruction' => 'Nấu đến khi rau mềm'],
            ['step' => 6, 'instruction' => 'Thêm bạc hà, nước mắm, thưởng thức']
        ]
    ],
    // Món xào còn lại
    'Bắp Cải Xào Thịt Bằm' => [
        'ingredients' => [
            ['name' => 'Bắp cải', 'amount' => '500g', 'unit' => 'g'],
            ['name' => 'Thịt heo bằm', 'amount' => '200g', 'unit' => 'g'],
            ['name' => 'Hành tây', 'amount' => '1', 'unit' => 'củ'],
            ['name' => 'Dầu ăn', 'amount' => '2', 'unit' => 'thìa canh'],
            ['name' => 'Nước mắm', 'amount' => '1', 'unit' => 'thìa canh'],
            ['name' => 'Muối', 'amount' => '1/2', 'unit' => 'thìa cà phê']
        ],
        'instructions' => [
            ['step' => 1, 'instruction' => 'Thái bắp cải thành sợi'],
            ['step' => 2, 'instruction' => 'Thái hành tây thành sợi'],
            ['step' => 3, 'instruction' => 'Xào thịt bằm với hành tây'],
            ['step' => 4, 'instruction' => 'Thêm bắp cải vào xào'],
            ['step' => 5, 'instruction' => 'Xào đến khi bắp cải mềm'],
            ['step' => 6, 'instruction' => 'Thêm nước mắm, nêm gia vị']
        ]
    ],
    'Cải Thảo Xào Tôm' => [
        'ingredients' => [
            ['name' => 'Cải thảo', 'amount' => '500g', 'unit' => 'g'],
            ['name' => 'Tôm sú', 'amount' => '300g', 'unit' => 'g'],
            ['name' => 'Tỏi', 'amount' => '3', 'unit' => 'tép'],
            ['name' => 'Dầu ăn', 'amount' => '2', 'unit' => 'thìa canh'],
            ['name' => 'Nước mắm', 'amount' => '1', 'unit' => 'thìa canh'],
            ['name' => 'Muối', 'amount' => '1/2', 'unit' => 'thìa cà phê']
        ],
        'instructions' => [
            ['step' => 1, 'instruction' => 'Thái cải thảo thành sợi'],
            ['step' => 2, 'instruction' => 'Làm sạch tôm, bóc vỏ'],
            ['step' => 3, 'instruction' => 'Phi thơm tỏi trong dầu'],
            ['step' => 4, 'instruction' => 'Xào tôm với tỏi'],
            ['step' => 5, 'instruction' => 'Thêm cải thảo vào xào'],
            ['step' => 6, 'instruction' => 'Xào đến khi cải mềm, nêm gia vị']
        ]
    ]
];

$updatedCount = 0;
foreach ($recipes as &$recipe) {
    $title = $recipe['title'];
    
    // Kiểm tra xem có công thức chi tiết không
    if (isset($moreDetailedRecipes[$title])) {
        $recipe['ingredients'] = $moreDetailedRecipes[$title]['ingredients'];
        $recipe['instructions'] = $moreDetailedRecipes[$title]['instructions'];
        $updatedCount++;
        echo "Đã cập nhật công thức chi tiết: {$title}\n";
    }
}

file_put_contents($jsonPath, json_encode($recipes, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

echo "\nHoàn thành! Đã cập nhật {$updatedCount} món ăn với công thức chi tiết.\n";
