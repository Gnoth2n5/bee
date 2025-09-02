<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\OpenAiController;
use App\Http\Controllers\WeeklyMealPlanController;
use App\Livewire\DiseaseAnalysis;
use App\Http\Controllers\DiseaseAnalysisController;
use Illuminate\Http\Request;
use App\Models\Recipe;



/*
|--------------------------------------------------------------------------
| VIP Routes
|--------------------------------------------------------------------------
|
| Only routes that actually require VIP access should be defined here.
| These routes are automatically protected with ['auth', 'vip'] middleware.
|
*/

Route::middleware(['auth', 'vip'])->group(function () {

    // Meal Plans - VIP Only
    Route::get('/meal-plans', [WeeklyMealPlanController::class, 'index'])->name('meal-plans.index');
    Route::get('/meal-plans/create', [WeeklyMealPlanController::class, 'create'])->name('meal-plans.create');
    Route::post('/meal-plans', [WeeklyMealPlanController::class, 'store'])->name('meal-plans.store');
    Route::get('/meal-plans/current', [WeeklyMealPlanController::class, 'current'])->name('meal-plans.current');
    Route::get('/meal-plans/suggestions', [WeeklyMealPlanController::class, 'generateSuggestions'])->name('meal-plans.suggestions');
    Route::get('/meal-plans/personalized-suggestions', [WeeklyMealPlanController::class, 'getPersonalizedSuggestions'])->name('meal-plans.personalized-suggestions');

    Route::get('/meal-plans/{mealPlan}', [WeeklyMealPlanController::class, 'show'])->name('meal-plans.show');
    Route::get('/meal-plans/{mealPlan}/edit', [WeeklyMealPlanController::class, 'edit'])->name('meal-plans.edit');
    Route::put('/meal-plans/{mealPlan}', [WeeklyMealPlanController::class, 'update'])->name('meal-plans.update');
    Route::delete('/meal-plans/{mealPlan}', [WeeklyMealPlanController::class, 'destroy'])->name('meal-plans.destroy');
    Route::post('/meal-plans/{mealPlan}/meals', [WeeklyMealPlanController::class, 'addMeal'])->name('meal-plans.add-meal');
    Route::delete('/meal-plans/{mealPlan}/meals', [WeeklyMealPlanController::class, 'removeMeal'])->name('meal-plans.remove-meal');
    Route::get('/meal-plans/{mealPlan}/shopping-list', [WeeklyMealPlanController::class, 'generateShoppingList'])->name('meal-plans.shopping-list');
    Route::post('/meal-plans/{mealPlan}/duplicate', [WeeklyMealPlanController::class, 'duplicateForNextWeek'])->name('meal-plans.duplicate');
    Route::get('/meal-plans/{mealPlan}/statistics', [WeeklyMealPlanController::class, 'getStatistics'])->name('meal-plans.statistics');
    Route::get('/meal-plans/{mealPlan}/json', [WeeklyMealPlanController::class, 'showJson'])->name('meal-plans.show-json');

    // Meal Plan Export routes - VIP Only
    Route::get('/meal-plans/{mealPlan}/export', [WeeklyMealPlanController::class, 'exportMealPlan'])->name('meal-plans.export');
    Route::get('/meal-plans/{mealPlan}/export/csv', [WeeklyMealPlanController::class, 'exportMealPlanCsv'])->name('meal-plans.export-csv');
    Route::get('/meal-plans/{mealPlan}/export/pdf', [WeeklyMealPlanController::class, 'exportMealPlanPdf'])->name('meal-plans.export-pdf');
    Route::get('/meal-plans/{mealPlan}/export/zip', [WeeklyMealPlanController::class, 'exportMealPlanZip'])->name('meal-plans.export-zip');
    Route::get('/meal-plans/{mealPlan}/export/xml', [WeeklyMealPlanController::class, 'exportMealPlanXml'])->name('meal-plans.export-xml');
    Route::get('/meal-plans/{mealPlan}/export/markdown', [WeeklyMealPlanController::class, 'exportMealPlanMarkdown'])->name('meal-plans.export-markdown');
    Route::get('/meal-plans/{mealPlan}/export/json', [WeeklyMealPlanController::class, 'exportMealPlanJson'])->name('meal-plans.export-json');
    Route::get('/meal-plans/export/all', [WeeklyMealPlanController::class, 'exportAllMealPlans'])->name('meal-plans.export-all');
    Route::get('/meal-plans/export/all/csv', [WeeklyMealPlanController::class, 'exportAllMealPlansCsv'])->name('meal-plans.export-all-csv');
    Route::get('/meal-plans/export/all/pdf', [WeeklyMealPlanController::class, 'exportAllMealPlansPdf'])->name('meal-plans.export-all-pdf');
    Route::get('/meal-plans/export/all/zip', [WeeklyMealPlanController::class, 'exportAllMealPlansZip'])->name('meal-plans.export-all-zip');
    Route::get('/meal-plans/export/all/xml', [WeeklyMealPlanController::class, 'exportAllMealPlansXml'])->name('meal-plans.export-all-xml');
    Route::get('/meal-plans/export/all/markdown', [WeeklyMealPlanController::class, 'exportAllMealPlansMarkdown'])->name('meal-plans.export-all-markdown');
    Route::get('/meal-plans/export/all/json', [WeeklyMealPlanController::class, 'exportAllMealPlansJson'])->name('meal-plans.export-all-json');

    // Weekly Meals Display - VIP Only
    Route::get('/weekly-meals/{mealPlan}', [WeeklyMealPlanController::class, 'showWeeklyMeals'])->name('weekly-meals.show');

    // Disease Analysis - VIP Only
    Route::get('/disease-analysis', DiseaseAnalysis::class)->name('disease-analysis.index');
    Route::prefix('api/disease-analysis')->name('api.disease-analysis.')->group(function () {
        Route::post('/analyze-image', [DiseaseAnalysisController::class, 'analyzeImage'])->name('analyze-image');
        Route::post('/recommendations', [DiseaseAnalysisController::class, 'getRecommendations'])->name('recommendations');
        Route::post('/search-ingredients', [DiseaseAnalysisController::class, 'searchByIngredients'])->name('search-ingredients');
        Route::post('/check-suitability', [DiseaseAnalysisController::class, 'checkRecipeSuitability'])->name('check-suitability');
        Route::get('/diseases', [DiseaseAnalysisController::class, 'getDiseases'])->name('diseases');
        Route::post('/create-disease', [DiseaseAnalysisController::class, 'createDisease'])->name('create-disease');
    });

    // Shopping Lists - VIP Only
    Route::get('/shopping-lists', \App\Livewire\ShoppingList\ShoppingListManager::class)->name('shopping-lists.index');
    Route::get('/shopping-lists/dashboard', \App\Livewire\ShoppingList\ShoppingListDashboard::class)->name('shopping-lists.dashboard');

    // Recipe search API for meal plans - VIP Only
    Route::get('/api/recipes/search', function (Request $request) {
        $query = $request->get('q', '');

        $recipes = Recipe::where('status', 'approved')
            ->where(function ($q) use ($query) {
                $q->where('title', 'like', "%{$query}%")
                    ->orWhere('description', 'like', "%{$query}%");
            })
            ->limit(10)
            ->get(['id', 'title', 'description', 'calories_per_serving']);

        return response()->json([
            'success' => true,
            'data' => $recipes
        ]);
    })->name('api.recipes.search');


    // AI Chat Assistant - VIP Only (the only real VIP feature)
    Route::get('/ai-chat', [OpenAiController::class, 'index'])->name('openai.index');

    // AI Chat API Routes - VIP Only  
    Route::prefix('api/openai')->name('api.openai.')->group(function () {
        Route::post('/send-message', [OpenAiController::class, 'sendMessage'])->name('send-message');
        Route::post('/recipe-suggestions', [OpenAiController::class, 'getRecipeSuggestions'])->name('recipe-suggestions');
        Route::post('/cooking-tips', [OpenAiController::class, 'getCookingTips'])->name('cooking-tips');
        Route::post('/analyze-recipe', [OpenAiController::class, 'analyzeRecipe'])->name('analyze-recipe');
        Route::post('/nutritional-info', [OpenAiController::class, 'getNutritionalInfo'])->name('nutritional-info');
        Route::get('/conversation-history', [OpenAiController::class, 'getConversationHistory'])->name('conversation-history');
        Route::delete('/conversation-history', [OpenAiController::class, 'clearConversationHistory'])->name('clear-conversation');
        Route::get('/quick-suggestions', [OpenAiController::class, 'getQuickSuggestions'])->name('quick-suggestions');
    });

    // VIP Map Search - Only existing VIP view
    Route::get('/vip/map-search', function () {
        return view('vip.map-search');
    })->name('vip.map-search');
});
