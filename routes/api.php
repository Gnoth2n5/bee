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