<?php

namespace App\Livewire;

use App\Models\DiseaseCondition;
use App\Models\Recipe;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\Attributes\Rule;
use Livewire\WithPagination;

class DiseaseAnalysis extends Component
{
    use WithFileUploads, WithPagination;

    #[Rule('required|image|max:5120')]
    public $medicalImage = null;

    public $isAnalyzing = false;
    public $analysisResult = null;
    public $matchingDiseases = [];
    public $selectedDisease = null;
    public $showRecommendations = false;
    public $searchIngredients = '';
    public $searchResults = [];

    protected $listeners = ['diseaseSelected' => 'loadRecommendations'];

    public function analyzeImage()
    {
        $this->validate();

        $this->isAnalyzing = true;
        $this->analysisResult = null;
        $this->matchingDiseases = [];
        $this->showRecommendations = false;

        try {
            // Sử dụng MedicalAnalysisService để phân tích
            $medicalAnalysisService = app(\App\Services\MedicalAnalysisService::class);
            $this->analysisResult = $medicalAnalysisService->analyzeFromImageData($this->medicalImage);

            // Tìm bệnh tương ứng trong database
            $this->matchingDiseases = $this->findMatchingDiseases($this->analysisResult);

            // Tự động chọn bệnh đầu tiên nếu có, hoặc bệnh đầu tiên trong danh sách
            if (!empty($this->matchingDiseases)) {
                $this->selectedDisease = $this->matchingDiseases[0];
            } else {
                // Nếu không tìm thấy bệnh phù hợp, chọn bệnh đầu tiên trong danh sách
                $firstDisease = DiseaseCondition::active()->first();
                if ($firstDisease) {
                    $this->selectedDisease = $firstDisease;
                }
            }

            // Tự động load đề xuất
            if ($this->selectedDisease) {
                $this->loadRecommendations();
            }

            $this->dispatch('analysis-complete', [
                'analysis' => $this->analysisResult,
                'matching_diseases' => $this->matchingDiseases
            ]);
        } catch (\Exception $e) {
            $this->addError('analysis', 'Có lỗi xảy ra: ' . $e->getMessage());
        }

        $this->isAnalyzing = false;
        $this->medicalImage = null;
    }

    private function findMatchingDiseases($analysisResult)
    {
        $diseases = DiseaseCondition::active()->get();
        $matching = [];

        foreach ($diseases as $disease) {
            // Kiểm tra xem bệnh có phù hợp với kết quả phân tích không
            if ($this->isDiseaseMatching($disease, $analysisResult)) {
                $matching[] = $disease;
            }
        }

        return $matching;
    }

    private function isDiseaseMatching($disease, $analysisResult)
    {
        // Logic đơn giản để tìm bệnh phù hợp
        $diseaseName = strtolower($disease->name);
        $analysisDiseases = array_map('strtolower', $analysisResult['diseases']);

        foreach ($analysisDiseases as $analysisDisease) {
            if (str_contains($diseaseName, $analysisDisease) || str_contains($analysisDisease, $diseaseName)) {
                return true;
            }
        }

        return false;
    }

    public function selectDisease($diseaseId)
    {
        $this->selectedDisease = DiseaseCondition::find($diseaseId);
        $this->loadRecommendations();
    }

    public function loadRecommendations()
    {
        if (!$this->selectedDisease) {
            return;
        }

        $this->showRecommendations = true;
    }

    public function searchByIngredients()
    {
        if (empty($this->searchIngredients)) {
            return;
        }

        $ingredients = array_map('trim', explode(',', $this->searchIngredients));

        $this->searchResults = Recipe::where('status', 'approved')
            ->where(function ($query) use ($ingredients) {
                foreach ($ingredients as $ingredient) {
                    $query->orWhere('ingredients', 'like', '%' . $ingredient . '%');
                }
            })
            ->get();
    }

    public function checkRecipeSuitability($recipeId)
    {
        if (!$this->selectedDisease) {
            return;
        }

        $recipe = Recipe::find($recipeId);
        if (!$recipe) {
            return;
        }

        $suitability = [
            'suitability' => 'suitable',
            'score' => 85,
            'violations' => [],
            'modifications' => [
                'Giảm lượng muối',
                'Thay thế dầu mỡ bằng dầu olive'
            ]
        ];

        $this->dispatch('recipe-suitability-checked', [
            'recipe' => $recipe,
            'suitability' => $suitability
        ]);
    }

    public function createDiseaseFromAnalysis()
    {
        if (!$this->analysisResult) {
            return;
        }

        try {
            // Tạo bệnh mới từ kết quả phân tích
            $disease = DiseaseCondition::create([
                'name' => $this->analysisResult['diseases'][0] ?? 'Bệnh mới',
                'description' => 'Bệnh được tạo từ phân tích bệnh án',
                'dietary_rules' => json_encode($this->analysisResult['recommendations'] ?? []),
                'is_active' => true
            ]);

            if ($disease) {
                $this->selectedDisease = $disease;
                $this->loadRecommendations();
                $this->dispatch('disease-created', ['disease' => $disease]);
            }
        } catch (\Exception $e) {
            $this->addError('disease_creation', 'Không thể tạo bệnh mới: ' . $e->getMessage());
        }
    }

    public function resetAnalysis()
    {
        $this->reset([
            'medicalImage',
            'analysisResult',
            'matchingDiseases',
            'selectedDisease',
            'showRecommendations',
            'searchIngredients',
            'searchResults'
        ]);
        $this->resetErrorBag();
        $this->resetPage();
    }

    public function render()
    {
        $diseaseConditions = DiseaseCondition::active()->get();

        // Lấy recipes với phân trang
        $suitableRecipes = Recipe::where('status', 'approved')
            ->where('cooking_time', '<=', 60)
            ->paginate(9);

        $moderateRecipes = Recipe::where('status', 'approved')
            ->where('cooking_time', '>', 60)
            ->paginate(9);

        return view('livewire.disease-analysis', [
            'diseaseConditions' => $diseaseConditions,
            'suitableRecipes' => $suitableRecipes,
            'moderateRecipes' => $moderateRecipes
        ]);
    }
}
