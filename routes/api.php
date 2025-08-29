<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\RestaurantController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// VietQR API routes  
Route::middleware('web')->prefix('vietqr')->name('api.vietqr.')->group(function () {
    Route::get('/user-id', function (Request $request) {
        $user = Auth::user();
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'User not authenticated'
            ], 401);
        }

        return response()->json([
            'success' => true,
            'user_id' => $user->id,
            'memo' => 'VIPPAY' . $user->id
        ]);
    })->name('user-id');
});

// Restaurant API routes
Route::prefix('restaurants')->name('restaurants.')->group(function () {
    Route::get('/search/nearby', [RestaurantController::class, 'searchNearby'])->name('search.nearby');
    Route::get('/search/keyword', [RestaurantController::class, 'searchByKeyword'])->name('search.keyword');
    Route::get('/{placeId}', [RestaurantController::class, 'show'])->name('show');
    Route::get('/{restaurantId}/ratings', [RestaurantController::class, 'getRatings'])->name('ratings');
    Route::get('/popular', [RestaurantController::class, 'getPopular'])->name('popular');
    Route::get('/recent', [RestaurantController::class, 'getRecent'])->name('recent');
    Route::post('/geocode', [RestaurantController::class, 'geocode'])->name('geocode');
});

// Protected restaurant routes
Route::middleware('auth:sanctum')->prefix('restaurants')->name('restaurants.')->group(function () {
    Route::post('/favorites', [RestaurantController::class, 'addToFavorites'])->name('favorites.add');
    Route::delete('/favorites', [RestaurantController::class, 'removeFromFavorites'])->name('favorites.remove');
    Route::get('/favorites', [RestaurantController::class, 'getFavorites'])->name('favorites.index');
    Route::post('/rate', [RestaurantController::class, 'rate'])->name('rate');
});

// Shopping List API routes
Route::middleware('auth:sanctum')->prefix('shopping-lists')->name('shopping-lists.')->group(function () {
    Route::get('/', [\App\Http\Controllers\Api\ShoppingListController::class, 'index'])->name('index');
    Route::post('/', [\App\Http\Controllers\Api\ShoppingListController::class, 'store'])->name('store');
    Route::get('/{shoppingList}', [\App\Http\Controllers\Api\ShoppingListController::class, 'show'])->name('show');
    Route::put('/{shoppingList}', [\App\Http\Controllers\Api\ShoppingListController::class, 'update'])->name('update');
    Route::delete('/{shoppingList}', [\App\Http\Controllers\Api\ShoppingListController::class, 'destroy'])->name('destroy');

    // Items
    Route::post('/{shoppingList}/items', [\App\Http\Controllers\Api\ShoppingListController::class, 'addItem'])->name('items.add');
    Route::put('/{shoppingList}/items/{item}', [\App\Http\Controllers\Api\ShoppingListController::class, 'updateItem'])->name('items.update');
    Route::delete('/{shoppingList}/items/{item}', [\App\Http\Controllers\Api\ShoppingListController::class, 'deleteItem'])->name('items.delete');
    Route::patch('/{shoppingList}/items/{item}/toggle', [\App\Http\Controllers\Api\ShoppingListController::class, 'toggleItem'])->name('items.toggle');

    // Actions
    Route::delete('/{shoppingList}/clear-checked', [\App\Http\Controllers\Api\ShoppingListController::class, 'clearChecked'])->name('clear-checked');
    Route::patch('/{shoppingList}/mark-completed', [\App\Http\Controllers\Api\ShoppingListController::class, 'markCompleted'])->name('mark-completed');
    Route::get('/{shoppingList}/categories', [\App\Http\Controllers\Api\ShoppingListController::class, 'getByCategories'])->name('categories');

    // Generate from other sources
    Route::post('/generate/meal-plan', [\App\Http\Controllers\Api\ShoppingListController::class, 'generateFromMealPlan'])->name('generate.meal-plan');
    Route::post('/generate/recipe', [\App\Http\Controllers\Api\ShoppingListController::class, 'generateFromRecipe'])->name('generate.recipe');
});