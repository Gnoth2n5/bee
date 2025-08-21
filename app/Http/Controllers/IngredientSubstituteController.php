<?php

namespace App\Http\Controllers;

use App\Services\IngredientSubstituteService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class IngredientSubstituteController extends Controller
{
    protected $ingredientSubstituteService;

    public function __construct(IngredientSubstituteService $ingredientSubstituteService)
    {
        $this->ingredientSubstituteService = $ingredientSubstituteService;
    }

    /**
     * Get ingredient substitutes
     */
    public function getSubstitutes(Request $request): JsonResponse
    {
        try {
            // Validate request
            $validator = Validator::make($request->all(), [
                'ingredient' => 'required|string|min:1|max:100'
            ], [
                'ingredient.required' => 'Vui lòng nhập tên nguyên liệu.',
                'ingredient.min' => 'Tên nguyên liệu quá ngắn.',
                'ingredient.max' => 'Tên nguyên liệu quá dài.'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'error' => $validator->errors()->first()
                ], 422);
            }

            $ingredient = trim($request->input('ingredient'));

            Log::info('API request for ingredient substitutes', [
                'ingredient' => $ingredient,
                'user_ip' => $request->ip()
            ]);

            // Call service to get substitutes
            $result = $this->ingredientSubstituteService->getSubstitutes($ingredient);

            if ($result['success'] && !empty($result['substitutes'])) {
                Log::info('API ingredient substitutes found successfully', [
                    'ingredient' => $ingredient,
                    'substitutes_count' => count($result['substitutes']),
                    'from_cache' => $result['from_cache'] ?? false
                ]);

                return response()->json([
                    'success' => true,
                    'ingredient' => $ingredient,
                    'substitutes' => $result['substitutes'],
                    'message' => 'Tìm thấy ' . count($result['substitutes']) . ' nguyên liệu có thể thay thế cho "' . $ingredient . '".',
                    'from_cache' => $result['from_cache'] ?? false
                ]);
            } else {
                $error = $result['error'] ?? 'Không tìm thấy nguyên liệu thay thế cho "' . $ingredient . '".';

                Log::warning('API failed to find ingredient substitutes', [
                    'ingredient' => $ingredient,
                    'error' => $error
                ]);

                return response()->json([
                    'success' => false,
                    'error' => $error
                ], 404);
            }
        } catch (\Exception $e) {
            Log::error('Exception in IngredientSubstituteController', [
                'ingredient' => $request->input('ingredient'),
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'error' => 'Có lỗi xảy ra. Vui lòng thử lại sau.'
            ], 500);
        }
    }
}
