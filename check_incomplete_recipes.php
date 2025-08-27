<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "Kiểm tra chi tiết những món còn thiếu công thức...\n";

$jsonPath = storage_path('app/recipes_data.json');
$recipes = json_decode(file_get_contents($jsonPath), true);

$incompleteRecipes = [];
$recipesWithDefaultRecipe = [];

foreach ($recipes as $recipe) {
    $title = $recipe['title'];
    $hasIngredients = !empty($recipe['ingredients']);
    $hasInstructions = !empty($recipe['instructions']);
    
    if (!$hasIngredients || !$hasInstructions) {
        $incompleteRecipes[] = [
            'title' => $title,
            'id' => $recipe['id'],
            'missing_ingredients' => !$hasIngredients,
            'missing_instructions' => !$hasInstructions,
            'ingredients_count' => $hasIngredients ? count($recipe['ingredients']) : 0,
            'instructions_count' => $hasInstructions ? count($recipe['instructions']) : 0
        ];
    } else {
        // Kiểm tra xem có phải công thức mặc định không
        if (isset($recipe['ingredients'][0]['name']) && $recipe['ingredients'][0]['name'] === 'Nguyên liệu chính') {
            $recipesWithDefaultRecipe[] = $title;
        }
    }
}

echo "=== MÓN ĂN CHƯA HOÀN CHỈNH ===\n";
if (empty($incompleteRecipes)) {
    echo "✅ Tất cả món ăn đã có công thức!\n";
} else {
    echo "❌ Có " . count($incompleteRecipes) . " món chưa hoàn chỉnh:\n\n";
    foreach ($incompleteRecipes as $recipe) {
        echo "📝 {$recipe['title']} (ID: {$recipe['id']})\n";
        if ($recipe['missing_ingredients']) {
            echo "   ❌ Thiếu nguyên liệu\n";
        }
        if ($recipe['missing_instructions']) {
            echo "   ❌ Thiếu hướng dẫn\n";
        }
        if ($recipe['ingredients_count'] > 0) {
            echo "   📊 Có {$recipe['ingredients_count']} nguyên liệu\n";
        }
        if ($recipe['instructions_count'] > 0) {
            echo "   📊 Có {$recipe['instructions_count']} bước\n";
        }
        echo "\n";
    }
}

echo "=== MÓN CÓ CÔNG THỨC MẶC ĐỊNH ===\n";
if (empty($recipesWithDefaultRecipe)) {
    echo "✅ Không có món nào dùng công thức mặc định!\n";
} else {
    echo "📝 Có " . count($recipesWithDefaultRecipe) . " món dùng công thức mặc định:\n";
    foreach ($recipesWithDefaultRecipe as $title) {
        echo "- {$title}\n";
    }
}

echo "\n=== KIỂM TRA CHI TIẾT 10 MÓN ĐẦU TIÊN ===\n";
for ($i = 0; $i < min(10, count($recipes)); $i++) {
    $recipe = $recipes[$i];
    echo "🍳 {$recipe['title']}:\n";
    echo "   📦 Ingredients: " . (empty($recipe['ingredients']) ? '❌ Không có' : '✅ ' . count($recipe['ingredients']) . ' items') . "\n";
    echo "   📋 Instructions: " . (empty($recipe['instructions']) ? '❌ Không có' : '✅ ' . count($recipe['instructions']) . ' steps') . "\n";
    
    if (!empty($recipe['ingredients'])) {
        echo "   📝 Ingredients sample: ";
        $firstIngredient = $recipe['ingredients'][0];
        if (isset($firstIngredient['name'])) {
            echo $firstIngredient['name'];
            if (isset($firstIngredient['amount'])) {
                echo " ({$firstIngredient['amount']}";
                if (isset($firstIngredient['unit'])) {
                    echo " {$firstIngredient['unit']}";
                }
                echo ")";
            }
        } else {
            echo "Invalid format";
        }
        echo "\n";
    }
    
    if (!empty($recipe['instructions'])) {
        echo "   📝 Instructions sample: ";
        $firstInstruction = $recipe['instructions'][0];
        if (isset($firstInstruction['instruction'])) {
            echo substr($firstInstruction['instruction'], 0, 50) . "...";
        } else {
            echo "Invalid format";
        }
        echo "\n";
    }
    echo "\n";
}

echo "✅ Hoàn thành kiểm tra!\n";
