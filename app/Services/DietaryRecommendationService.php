<?php

namespace App\Services;

use App\Models\Recipe;
use App\Models\DiseaseCondition;
use App\Models\Category;
use App\Models\Tag;
use Illuminate\Support\Facades\DB;

class DietaryRecommendationService
{
    /**
     * Create a new class instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Đề xuất món ăn dựa trên bệnh
     */
    public function getRecommendations($diseaseCondition, $limit = 10)
    {
        $recommendations = [
            'suitable' => [],
            'moderate' => [],
            'categories' => [],
            'tags' => [],
            'restrictions' => [],
            'substitutions' => []
        ];

        if (!$diseaseCondition) {
            return $recommendations;
        }

        // Lấy các món ăn phù hợp
        $recommendations['suitable'] = $this->getSuitableRecipes($diseaseCondition, $limit);

        // Lấy các món ăn ở mức độ vừa phải
        $recommendations['moderate'] = $this->getModerateRecipes($diseaseCondition, $limit);

        // Lấy danh mục phù hợp
        $recommendations['categories'] = $this->getRecommendedCategories($diseaseCondition);

        // Lấy tags phù hợp
        $recommendations['tags'] = $this->getRecommendedTags($diseaseCondition);

        // Lấy các hạn chế
        $recommendations['restrictions'] = $diseaseCondition->getRestrictions();

        // Lấy các thay thế
        $recommendations['substitutions'] = $this->getSubstitutions($diseaseCondition);

        return $recommendations;
    }

    /**
     * Lấy các món ăn phù hợp
     */
    private function getSuitableRecipes($diseaseCondition, $limit)
    {
        return $diseaseCondition->suitableRecipes()
            ->where('status', 'approved')
            ->orderBy('average_rating', 'desc')
            ->limit($limit)
            ->get();
    }

    /**
     * Lấy các món ăn ở mức độ vừa phải
     */
    private function getModerateRecipes($diseaseCondition, $limit)
    {
        return $diseaseCondition->moderateRecipes()
            ->where('status', 'approved')
            ->orderBy('average_rating', 'desc')
            ->limit($limit)
            ->get();
    }

    /**
     * Lấy danh mục phù hợp
     */
    private function getRecommendedCategories($diseaseCondition)
    {
        $recommendedFoods = $diseaseCondition->getRecommendations();

        return Category::where(function ($query) use ($recommendedFoods) {
            foreach ($recommendedFoods as $food) {
                $query->orWhere('name', 'like', '%' . $food . '%')
                    ->orWhere('description', 'like', '%' . $food . '%');
            }
        })->active()->get();
    }

    /**
     * Lấy tags phù hợp
     */
    private function getRecommendedTags($diseaseCondition)
    {
        $recommendedFoods = $diseaseCondition->getRecommendations();

        return Tag::where(function ($query) use ($recommendedFoods) {
            foreach ($recommendedFoods as $food) {
                $query->orWhere('name', 'like', '%' . $food . '%')
                    ->orWhere('description', 'like', '%' . $food . '%');
            }
        })->get();
    }

    /**
     * Lấy các thay thế thực phẩm
     */
    private function getSubstitutions($diseaseCondition)
    {
        $substitutions = [];

        foreach ($diseaseCondition->dietaryRules as $rule) {
            $substitutions = array_merge($substitutions, $rule->getSubstitutions());
        }

        return array_unique($substitutions);
    }

    /**
     * Tìm kiếm món ăn dựa trên nguyên liệu được khuyến nghị
     */
    public function searchRecipesByIngredients($ingredients, $limit = 10)
    {
        $recipes = Recipe::where('status', 'approved')
            ->where(function ($query) use ($ingredients) {
                foreach ($ingredients as $ingredient) {
                    $query->orWhereJsonContains('ingredients', [
                        'name' => $ingredient
                    ]);
                }
            })
            ->orderBy('average_rating', 'desc')
            ->limit($limit)
            ->get();

        return $recipes;
    }

    /**
     * Kiểm tra tính phù hợp của món ăn với bệnh
     */
    public function checkRecipeSuitability(Recipe $recipe, DiseaseCondition $diseaseCondition)
    {
        $violations = [];
        $score = 100; // Điểm ban đầu

        // Kiểm tra các quy tắc ăn kiêng
        foreach ($diseaseCondition->dietaryRules as $rule) {
            $ruleViolations = $rule->checkRecipeViolation($recipe);
            if (!empty($ruleViolations)) {
                $violations = array_merge($violations, $ruleViolations);
                $score -= 20; // Trừ điểm cho mỗi vi phạm
            }
        }

        // Kiểm tra nguyên liệu bị hạn chế
        $restrictedFoods = $diseaseCondition->getRestrictions();
        foreach ($recipe->ingredients as $ingredient) {
            $ingredientName = strtolower($ingredient['name']);
            foreach ($restrictedFoods as $restrictedFood) {
                if (str_contains($ingredientName, strtolower($restrictedFood))) {
                    $violations[] = "Chứa nguyên liệu bị hạn chế: {$ingredient['name']}";
                    $score -= 15;
                }
            }
        }

        // Xác định mức độ phù hợp
        $suitability = match (true) {
            $score >= 80 => 'suitable',
            $score >= 50 => 'moderate',
            default => 'unsuitable'
        };

        return [
            'suitability' => $suitability,
            'score' => max(0, $score),
            'violations' => $violations,
            'modifications' => $this->suggestModifications($recipe, $diseaseCondition)
        ];
    }

    /**
     * Đề xuất điều chỉnh cho món ăn
     */
    private function suggestModifications(Recipe $recipe, DiseaseCondition $diseaseCondition)
    {
        $modifications = [];

        // Đề xuất thay thế nguyên liệu
        $substitutions = $this->getSubstitutions($diseaseCondition);
        foreach ($recipe->ingredients as $ingredient) {
            $ingredientName = strtolower($ingredient['name']);
            foreach ($substitutions as $substitution) {
                if (str_contains($ingredientName, strtolower($substitution['from'] ?? ''))) {
                    $modifications[] = "Thay thế {$ingredient['name']} bằng {$substitution['to']}";
                }
            }
        }

        // Đề xuất điều chỉnh phương pháp nấu
        $cookingMethods = $diseaseCondition->cooking_methods ?? [];
        if (!empty($cookingMethods)) {
            $modifications[] = "Sử dụng phương pháp nấu: " . implode(', ', $cookingMethods);
        }

        return $modifications;
    }

    /**
     * Tạo JSON data cho quy tắc ăn kiêng
     */
    public function generateDietaryRulesJson()
    {
        $rules = [
            'diabetes' => [
                'name' => 'Tiểu đường',
                'restrictions' => ['đường', 'bánh kẹo', 'nước ngọt', 'gạo trắng', 'bánh mì trắng'],
                'recommendations' => ['rau xanh', 'cá', 'thịt nạc', 'gạo lứt', 'yến mạch'],
                'cooking_methods' => ['hấp', 'luộc', 'nướng'],
                'substitutions' => [
                    ['from' => 'đường', 'to' => 'stevia'],
                    ['from' => 'gạo trắng', 'to' => 'gạo lứt'],
                    ['from' => 'bánh mì trắng', 'to' => 'bánh mì nguyên cám']
                ]
            ],
            'hypertension' => [
                'name' => 'Cao huyết áp',
                'restrictions' => ['muối', 'mắm', 'dưa cà', 'thực phẩm chế biến'],
                'recommendations' => ['rau xanh', 'chuối', 'cá', 'sữa ít béo'],
                'cooking_methods' => ['hấp', 'luộc', 'nướng'],
                'substitutions' => [
                    ['from' => 'muối', 'to' => 'gia vị thảo mộc'],
                    ['from' => 'mắm', 'to' => 'nước mắm ít muối']
                ]
            ],
            'gout' => [
                'name' => 'Gout',
                'restrictions' => ['thịt đỏ', 'hải sản', 'nội tạng', 'bia rượu'],
                'recommendations' => ['rau xanh', 'sữa ít béo', 'trái cây', 'ngũ cốc'],
                'cooking_methods' => ['hấp', 'luộc'],
                'substitutions' => [
                    ['from' => 'thịt đỏ', 'to' => 'thịt trắng'],
                    ['from' => 'hải sản', 'to' => 'cá nước ngọt']
                ]
            ]
        ];

        return json_encode($rules, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    }
}
