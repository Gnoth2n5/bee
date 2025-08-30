<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Services\DiseaseAnalysisService;
use App\Services\DietaryRecommendationService;
use App\Models\DiseaseCondition;
use App\Models\Recipe;
use Illuminate\Support\Facades\Validator;

class DiseaseAnalysisController extends Controller
{
    protected $diseaseAnalysisService;
    protected $dietaryRecommendationService;

    public function __construct(
        DiseaseAnalysisService $diseaseAnalysisService,
        DietaryRecommendationService $dietaryRecommendationService
    ) {
        $this->diseaseAnalysisService = $diseaseAnalysisService;
        $this->dietaryRecommendationService = $dietaryRecommendationService;
    }

    /**
     * Hiển thị trang phân tích bệnh án
     */
    public function index()
    {
        $diseaseConditions = DiseaseCondition::active()->get();
        return view('disease-analysis.index', compact('diseaseConditions'));
    }

    /**
     * Phân tích hình ảnh bệnh án
     */
    public function analyzeImage(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'medical_image' => 'required|image|max:5120', // 5MB max
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'error' => 'Hình ảnh không hợp lệ. Vui lòng chọn file hình ảnh dưới 5MB.'
            ], 422);
        }

        try {
            $result = $this->diseaseAnalysisService->analyzeMedicalImage($request->file('medical_image'));

            if ($result['success']) {
                // Tìm kiếm bệnh trong database
                $matchingDiseases = $this->diseaseAnalysisService->findMatchingDiseases($result['data']);

                return response()->json([
                    'success' => true,
                    'analysis' => $result['data'],
                    'matching_diseases' => $matchingDiseases,
                    'message' => 'Phân tích thành công!'
                ]);
            }

            return response()->json([
                'success' => false,
                'error' => $result['error']
            ], 500);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'Có lỗi xảy ra: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Lấy đề xuất món ăn dựa trên bệnh
     */
    public function getRecommendations(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'disease_id' => 'required|exists:disease_conditions,id',
            'limit' => 'nullable|integer|min:1|max:50'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'error' => 'Dữ liệu không hợp lệ.'
            ], 422);
        }

        try {
            $diseaseCondition = DiseaseCondition::findOrFail($request->disease_id);
            $limit = $request->input('limit', 10);

            $recommendations = $this->dietaryRecommendationService->getRecommendations($diseaseCondition, $limit);

            return response()->json([
                'success' => true,
                'disease' => $diseaseCondition,
                'recommendations' => $recommendations,
                'message' => 'Đã tìm thấy ' . count($recommendations['suitable']) . ' món ăn phù hợp.'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'Có lỗi xảy ra: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Tìm kiếm món ăn theo nguyên liệu
     */
    public function searchByIngredients(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'ingredients' => 'required|array|min:1',
            'ingredients.*' => 'string|max:255',
            'limit' => 'nullable|integer|min:1|max:50'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'error' => 'Dữ liệu không hợp lệ.'
            ], 422);
        }

        try {
            $ingredients = $request->input('ingredients');
            $limit = $request->input('limit', 10);

            $recipes = $this->dietaryRecommendationService->searchRecipesByIngredients($ingredients, $limit);

            return response()->json([
                'success' => true,
                'recipes' => $recipes,
                'message' => 'Đã tìm thấy ' . count($recipes) . ' món ăn.'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'Có lỗi xảy ra: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Kiểm tra tính phù hợp của món ăn với bệnh
     */
    public function checkRecipeSuitability(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'recipe_id' => 'required|exists:recipes,id',
            'disease_id' => 'required|exists:disease_conditions,id'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'error' => 'Dữ liệu không hợp lệ.'
            ], 422);
        }

        try {
            $recipe = Recipe::findOrFail($request->recipe_id);
            $diseaseCondition = DiseaseCondition::findOrFail($request->disease_id);

            $suitability = $this->dietaryRecommendationService->checkRecipeSuitability($recipe, $diseaseCondition);

            return response()->json([
                'success' => true,
                'recipe' => $recipe,
                'disease' => $diseaseCondition,
                'suitability' => $suitability
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'Có lỗi xảy ra: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Lấy danh sách bệnh
     */
    public function getDiseases(): JsonResponse
    {
        try {
            $diseases = DiseaseCondition::active()->get();

            return response()->json([
                'success' => true,
                'diseases' => $diseases
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'Có lỗi xảy ra: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Tạo bệnh mới từ kết quả phân tích
     */
    public function createDisease(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'analysis_data' => 'required|array',
            'analysis_data.diseases' => 'required|array|min:1',
            'analysis_data.diseases.*' => 'string|max:255'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'error' => 'Dữ liệu không hợp lệ.'
            ], 422);
        }

        try {
            $disease = $this->diseaseAnalysisService->createDiseaseFromAnalysis($request->input('analysis_data'));

            if ($disease) {
                return response()->json([
                    'success' => true,
                    'disease' => $disease,
                    'message' => 'Đã tạo bệnh mới thành công.'
                ]);
            }

            return response()->json([
                'success' => false,
                'error' => 'Không thể tạo bệnh mới.'
            ], 400);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'Có lỗi xảy ra: ' . $e->getMessage()
            ], 500);
        }
    }
}
