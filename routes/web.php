<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RecipeController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\TagController;
use App\Http\Controllers\RatingController;

use App\Http\Controllers\CollectionController;
use App\Livewire\HomePage;
use App\Livewire\Recipes\RecipeDetail;

Route::get('/', HomePage::class)->name('home');

Route::view('dashboard', 'dashboard')
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::get('profile', App\Livewire\Profile\ProfilePage::class)
    ->middleware(['auth'])
    ->name('profile');

// Recipe routes
Route::get('/recipes', App\Livewire\Recipes\RecipeList::class)->name('recipes.index');
Route::get('/recipes/{recipe}', RecipeDetail::class)->name('recipes.show');

// Protected routes
Route::middleware(['auth'])->group(function () {
    // Recipe management
    Route::get('/recipes/create', [RecipeController::class, 'create'])->name('recipes.create');
    Route::post('/recipes', [RecipeController::class, 'store'])->name('recipes.store');
    Route::get('/recipes/{recipe:slug}/edit', [RecipeController::class, 'edit'])->name('recipes.edit');
    Route::put('/recipes/{recipe:slug}', [RecipeController::class, 'update'])->name('recipes.update');
    Route::delete('/recipes/{recipe:slug}', [RecipeController::class, 'destroy'])->name('recipes.destroy');

    // User recipes
    Route::get('/my-recipes', [RecipeController::class, 'myRecipes'])->name('recipes.my');

    // Ratings
    Route::post('/recipes/{recipe}/rate', [RatingController::class, 'store'])->name('recipes.rate');
    Route::put('/recipes/{recipe}/rate', [RatingController::class, 'update'])->name('recipes.rate.update');
    Route::delete('/recipes/{recipe}/rate', [RatingController::class, 'destroy'])->name('recipes.rate.destroy');

    // Favorites
    Route::get('/favorites', App\Livewire\Favorites\FavoritesPage::class)->name('favorites.index');

    // Collections
    Route::get('/collections/{collection}', \App\Livewire\Collections\CollectionDetail::class)->name('collections.show');
    Route::resource('collections', CollectionController::class)->except(['show']);
    Route::post('/collections/{collection}/recipes/{recipe}', [CollectionController::class, 'addRecipe'])->name('collections.add-recipe');
    Route::delete('/collections/{collection}/recipes/{recipe}', [CollectionController::class, 'removeRecipe'])->name('collections.remove-recipe');
});

// Admin routes
Route::middleware(['auth', 'role:admin|manager'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/recipes/pending', [RecipeController::class, 'pending'])->name('recipes.pending');
    Route::post('/recipes/{recipe}/approve', [RecipeController::class, 'approve'])->name('recipes.approve');
    Route::post('/recipes/{recipe}/reject', [RecipeController::class, 'reject'])->name('recipes.reject');
    
    Route::resource('categories', CategoryController::class);
    Route::resource('tags', TagController::class);
});

require __DIR__.'/auth.php';
