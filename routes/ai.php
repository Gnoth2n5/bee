<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\OpenAiController;

// OpenAI Chat Assistant
Route::get('/ai-chat', [OpenAiController::class, 'index'])->name('openai.index');

// OpenAI API routes
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