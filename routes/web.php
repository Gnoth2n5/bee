<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\App;
use App\Http\Controllers\RecipeController;
use App\Models\Recipe;
use App\Http\Controllers\CategoryController;
use App\Services\VietQrService;
use App\Http\Controllers\TagController;
use App\Http\Controllers\WeeklyMealPlanController;
use App\Http\Controllers\RatingController;

use App\Http\Controllers\CollectionController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\RestaurantController;
use App\Http\Controllers\AdminLogoutController;
use App\Http\Controllers\VietQrController;
use App\Http\Controllers\SubscriptionController;
use App\Http\Controllers\RestaurantAdController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\IngredientSubstituteController;
use App\Http\Controllers\VietnamProvinceController;
use App\Http\Controllers\Admin\PaymentController;

use App\Livewire\MealPlans\WeeklyMealPlanPage;
use App\Livewire\HomePage;
use App\Livewire\Recipes\RecipeList;
use App\Livewire\AdvancedSearch;
use App\Livewire\WeatherRecipeSuggestions;
use App\Livewire\Restaurants\RestaurantMap;
use App\Livewire\Favorites\FavoritesPage;
use App\Livewire\Collections\CollectionDetail;
use App\Livewire\Admin\ModerationTest;
use App\Livewire\Admin\ScheduledPosts;
use App\Livewire\Admin\PendingPosts;
use App\Livewire\StorageManager;
use App\Livewire\Profile\ProfilePage;
use App\Livewire\Recipes\RecipeDetail;

Route::get('/', HomePage::class)->name('home');

// Post routes
Route::get('/posts', [PostController::class, 'index'])->name('posts.index');
Route::get('/posts/{slug}', [PostController::class, 'show'])->name('posts.show');

Route::view('dashboard', 'dashboard')
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::get('profile', ProfilePage::class)
    ->middleware(['auth'])
    ->name('profile');

// Recipe routes
Route::get('/recipes', RecipeList::class)->name('recipes.index');
Route::get('/recipes/{recipe}', RecipeDetail::class)->name('recipes.show');

// Advanced Search route
Route::get('/search', AdvancedSearch::class)->name('search.advanced');

// Weather-based recipe suggestions
Route::get('/weather-suggestions', WeatherRecipeSuggestions::class)->name('weather.suggestions');

// Weekly Meal Plan routes
Route::middleware(['auth'])->group(function () {
    Route::get('/meal-plans', [WeeklyMealPlanController::class, 'index'])->name('meal-plans.index');
    Route::get('/meal-plans/create', [WeeklyMealPlanController::class, 'create'])->name('meal-plans.create');
    Route::post('/meal-plans', [WeeklyMealPlanController::class, 'store'])->name('meal-plans.store');
    Route::get('/meal-plans/{mealPlan}', [WeeklyMealPlanController::class, 'show'])->name('meal-plans.show');
    Route::get('/meal-plans/{mealPlan}/json', [WeeklyMealPlanController::class, 'showJson'])->name('meal-plans.show-json');
    Route::get('/meal-plans/{mealPlan}/edit', [WeeklyMealPlanController::class, 'edit'])->name('meal-plans.edit');
    Route::put('/meal-plans/{mealPlan}', [WeeklyMealPlanController::class, 'update'])->name('meal-plans.update');
    Route::delete('/meal-plans/{mealPlan}', [WeeklyMealPlanController::class, 'destroy'])->name('meal-plans.destroy');

    // Export routes
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

    // API routes for JSON responses
    Route::get('/meal-plans/{mealPlan}/json', [WeeklyMealPlanController::class, 'showJson'])->name('meal-plans.show-json');
});

// Weekly Meal Plan Livewire Component
Route::get('/weekly-meal-plan', \App\Livewire\MealPlans\WeeklyMealPlanPage::class)->name('weekly-meal-plan');

// Weekly Meals Display
Route::get('/weekly-meals/{mealPlan}', [WeeklyMealPlanController::class, 'showWeeklyMeals'])->name('weekly-meals.show');

// Recipe search API for meal plans
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

// Restaurant routes
Route::get('/restaurants', [RestaurantController::class, 'index'])->name('restaurants.index');
Route::get('/restaurants/map', RestaurantMap::class)->name('restaurants.map');

// Ingredient Substitute API
Route::post('/api/ingredient-substitute', [IngredientSubstituteController::class, 'getSubstitutes'])->name('api.ingredient.substitute');


// Vietnam Provinces API
Route::prefix('api/vietnam-provinces')->name('api.vietnam-provinces.')->group(function () {
    Route::get('/', [VietnamProvinceController::class, 'index'])->name('index');
    Route::get('/stats', [VietnamProvinceController::class, 'stats'])->name('stats');
    Route::get('/health', [VietnamProvinceController::class, 'health'])->name('health');
    Route::get('/search', [VietnamProvinceController::class, 'search'])->name('search');
    Route::get('/region/{region}', [VietnamProvinceController::class, 'provincesByRegion'])->name('provinces-by-region');
    Route::get('/communes-with-coordinates', [VietnamProvinceController::class, 'communesWithCoordinates'])->name('communes-with-coordinates');
    Route::get('/{code}', [VietnamProvinceController::class, 'show'])->name('show');
    Route::get('/{provinceCode}/districts', [VietnamProvinceController::class, 'districts'])->name('districts');
    Route::get('/districts/{districtCode}/wards', [VietnamProvinceController::class, 'wards'])->name('wards');
    Route::delete('/cache', [VietnamProvinceController::class, 'clearCache'])->name('clear-cache');
});

// PayOS API routes
Route::prefix('api/payos')->name('api.payos.')->group(function () {
    Route::get('/banks', function () {
        $vietqrService = new VietQrService();
        $banks = $vietqrService->getBanks();
        return response()->json($banks);
    })->name('banks');

    Route::post('/generate', function (Request $request) {
        $vietqrService = new VietQrService();
        $qrData = $vietqrService->generateQrCode($request->all());
        return response()->json($qrData);
    })->name('generate');
});

// Restaurant ads API routes
Route::prefix('api/restaurant-ads')->name('api.restaurant-ads.')->group(function () {
    Route::get('/active', [RestaurantAdController::class, 'getActiveAds'])->name('active');
    Route::post('/{ad}/increment-views', [RestaurantAdController::class, 'incrementViews'])->name('increment-views');
    Route::post('/{ad}/increment-clicks', [RestaurantAdController::class, 'incrementClicks'])->name('increment-clicks');
});

// Protected routes
Route::middleware(['auth'])->group(function () {
    // Weekly Meal Plan page - REMOVED DUPLICATE ROUTE

    // Weekly Meal Plan API routes
    Route::prefix('meal-plans')->name('meal-plans.')->group(function () {
        Route::post('/', [WeeklyMealPlanController::class, 'store'])->name('store');
        Route::get('/current', [WeeklyMealPlanController::class, 'current'])->name('current');
        Route::get('/suggestions', [WeeklyMealPlanController::class, 'generateSuggestions'])->name('suggestions');
        Route::get('/personalized-suggestions', [WeeklyMealPlanController::class, 'getPersonalizedSuggestions'])->name('personalized-suggestions');

        Route::prefix('{mealPlan}')->group(function () {
            Route::get('/', [WeeklyMealPlanController::class, 'show'])->name('show');
            Route::put('/', [WeeklyMealPlanController::class, 'update'])->name('update');
            Route::delete('/', [WeeklyMealPlanController::class, 'destroy'])->name('destroy');
            Route::post('/meals', [WeeklyMealPlanController::class, 'addMeal'])->name('add-meal');
            Route::delete('/meals', [WeeklyMealPlanController::class, 'removeMeal'])->name('remove-meal');
            Route::get('/shopping-list', [WeeklyMealPlanController::class, 'generateShoppingList'])->name('shopping-list');
            Route::post('/duplicate', [WeeklyMealPlanController::class, 'duplicateForNextWeek'])->name('duplicate');
            Route::get('/statistics', [WeeklyMealPlanController::class, 'getStatistics'])->name('statistics');
        });
    });

    // Recipe management
    Route::get('/recipes/create', [RecipeController::class, 'create'])->name('recipes.create');
    Route::post('/recipes', [RecipeController::class, 'store'])->name('recipes.store');
    Route::get('/recipes/{recipe:slug}/edit', [RecipeController::class, 'edit'])->name('recipes.edit');
    Route::put('/recipes/{recipe:slug}', [RecipeController::class, 'update'])->name('recipes.update');
    Route::delete('/recipes/{recipe:slug}', [RecipeController::class, 'destroy'])->name('recipes.destroy');

    // User recipes
    Route::get('/my-recipes', [RecipeController::class, 'myRecipes'])->name('recipes.my');

    // Export routes
    Route::get('/recipes/export/excel', [RecipeController::class, 'exportExcel'])->name('recipes.export.excel');
    Route::get('/recipes/export/csv', [RecipeController::class, 'exportCsv'])->name('recipes.export.csv');
    Route::get('/recipes/export/zip', [RecipeController::class, 'exportZip'])->name('recipes.export.zip');
    Route::get('/recipes/export/pdf', [RecipeController::class, 'exportPdf'])->name('recipes.export.pdf');
    Route::get('/my-recipes/export/excel', [RecipeController::class, 'exportMyRecipesExcel'])->name('recipes.my.export.excel');
    Route::get('/my-recipes/export/zip', [RecipeController::class, 'exportMyRecipesZip'])->name('recipes.my.export.zip');

    // Ratings
    Route::post('/recipes/{recipe}/rate', [RatingController::class, 'store'])->name('recipes.rate');
    Route::put('/recipes/{recipe}/rate', [RatingController::class, 'update'])->name('recipes.rate.update');
    Route::delete('/recipes/{recipe}/rate', [RatingController::class, 'destroy'])->name('recipes.rate.destroy');

    // Favorites
    Route::get('/favorites', FavoritesPage::class)->name('favorites.index');

    // Collections
    Route::get('/collections/{collection}', CollectionDetail::class)->name('collections.show');
    Route::resource('collections', CollectionController::class)->except(['show']);
    Route::post('/collections/{collection}/recipes/{recipe}', [CollectionController::class, 'addRecipe'])->name('collections.add-recipe');
    Route::delete('/collections/{collection}/recipes/{recipe}', [CollectionController::class, 'removeRecipe'])->name('collections.remove-recipe');



    // Restaurant favorites and ratings
    Route::post('/restaurants/favorites', [RestaurantController::class, 'addToFavorites'])->name('restaurants.favorites.add');
    Route::delete('/restaurants/favorites', [RestaurantController::class, 'removeFromFavorites'])->name('restaurants.favorites.remove');
    Route::get('/restaurants/favorites', [RestaurantController::class, 'getFavorites'])->name('restaurants.favorites.index');
    Route::post('/restaurants/rate', [RestaurantController::class, 'rate'])->name('restaurants.rate');

    // Subscription routes
    Route::get('/subscriptions', [SubscriptionController::class, 'index'])->name('subscriptions.index');
    Route::get('/subscriptions/packages', [SubscriptionController::class, 'showPackages'])->name('subscriptions.packages');
    Route::post('/subscriptions/purchase', [SubscriptionController::class, 'purchase'])->name('subscriptions.purchase');
    Route::post('/subscriptions/verify-payment', [SubscriptionController::class, 'verifyPayment'])->name('subscriptions.verify-payment');
    Route::post('/subscriptions/cancel', [SubscriptionController::class, 'cancel'])->name('subscriptions.cancel');

    // Restaurant ads routes
    Route::resource('restaurant-ads', RestaurantAdController::class);
    Route::post('/restaurant-ads/{ad}/verify-payment', [RestaurantAdController::class, 'verifyPayment'])->name('restaurant-ads.verify-payment');
    Route::post('/restaurant-ads/{ad}/increment-views', [RestaurantAdController::class, 'incrementViews'])->name('restaurant-ads.increment-views');
    Route::post('/restaurant-ads/{ad}/increment-clicks', [RestaurantAdController::class, 'incrementClicks'])->name('restaurant-ads.increment-clicks');

    // VIP features routes
    Route::middleware(['vip'])->group(function () {
        Route::get('/vip/map-search', function () {
            return view('vip.map-search');
        })->name('vip.map-search');

        Route::get('/vip/advanced-features', function () {
            return view('vip.advanced-features');
        })->name('vip.advanced-features');
    });

    // Test route để kiểm tra VIP
    Route::get('/test-vip', function () {
        $user = auth()->user();
        return response()->json([
            'user_id' => $user->id,
            'user_name' => $user->name,
            'is_vip' => $user->isVip(),
            'active_subscription' => $user->activeSubscription(),
            'subscriptions' => $user->subscriptions()->get()
        ]);
    })->name('test.vip');

    // Test route để kiểm tra QR code
    Route::get('/test-qr-display', function () {
        return view('test.qr-display');
    })->name('test.qr-display');

    // Test QR generation
    Route::get('/test-vietqr', function () {
        try {
            $vietqrService = new \App\Services\VietQrService();
            $result = $vietqrService->generateVietQrCode([
                'amount' => 1000,
                'orderCode' => 'TEST_' . time()
            ]);

            return response()->json($result);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
        }
    });

    Route::get('/test-qr', function () {
        $vietqrService = new \App\Services\VietQrService();
        $qrData = $vietqrService->generateQrCode([
            'transaction_id' => 'TEST_' . time(),
            'amount' => 199000,
            'message' => 'Test thanh toan - 199000 VND',
        ]);

        return view('test.qr-test', [
            'qrCode' => $qrData['qr_code'] ?? null,
            'qrText' => $qrData['qr_text'] ?? null,
            'amount' => $qrData['amount'] ?? 199000,
            'transactionId' => $qrData['transaction_id'] ?? null,
            'message' => $qrData['message'] ?? null,
            'isDemo' => $qrData['is_demo'] ?? false,
        ]);
    })->name('test.qr');

    // Test simple QR generation (no auth required)
    Route::get('/api/test-simple-qr', function () {
        try {
            $amount = 1000;
            $transactionId = 'TEST_' . time();

            // Tạo chuỗi VIETQR đơn giản
            $vietqrString = "000201010212" .
                "38580010A00000072701290006970422" .
                "0112VQRQADWLF2921" .
                "5204597053037045408" .
                sprintf("%08d", $amount) .
                "5802VN62" .
                sprintf("%02d", strlen($transactionId)) . $transactionId .
                "6304";

            // Sử dụng Google Charts API để tạo QR code
            $qrImageUrl = 'https://chart.googleapis.com/chart?chs=300x300&cht=qr&chl=' . urlencode($vietqrString);

            return response()->json([
                'success' => true,
                'qr_code' => $qrImageUrl,
                'amount' => $amount,
                'transaction_id' => $transactionId,
                'vietqr_string' => $vietqrString,
                'message' => 'QR code đã được tạo thành công'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
        }
    })->middleware('web');

    // Test debug route
    Route::get('/debug-qr', function () {
        return response()->json([
            'status' => 'ok',
            'message' => 'Debug route working',
            'time' => now()
        ]);
    });

    // Test PayOS API trực tiếp
    Route::get('/test-payos', function () {
        $vietqrService = new \App\Services\VietQrService();
        $qrData = $vietqrService->generateQrCode([
            'amount' => 1000,
            'message' => 'Test PayOS API',
            'transaction_id' => 'PAYOS_TEST_' . time(),
        ]);

        return response()->json([
            'success' => $qrData['success'],
            'data' => $qrData,
            'config' => [
                'client_id' => config('vietqr.api.client_id'),
                'api_key' => substr(config('vietqr.api.api_key'), 0, 10) . '...',
                'checksum_key' => substr(config('vietqr.api.checksum_key'), 0, 10) . '...',
                'use_demo_mode' => config('vietqr.api.use_demo_mode')
            ]
        ]);
    })->name('test.payos');

    // Test PayOS API đơn giản (không cần auth)
    Route::get('/api/test-payos', function () {
        $vietqrService = new \App\Services\VietQrService();
        $qrData = $vietqrService->generateQrCode([
            'amount' => 1000,
            'message' => 'Test PayOS API',
            'transaction_id' => 'PAYOS_TEST_' . time(),
        ]);

        return response()->json([
            'success' => $qrData['success'],
            'data' => $qrData,
            'config' => [
                'client_id' => config('vietqr.api.client_id'),
                'api_key' => substr(config('vietqr.api.api_key'), 0, 10) . '...',
                'checksum_key' => substr(config('vietqr.api.checksum_key'), 0, 10) . '...',
                'use_demo_mode' => config('vietqr.api.use_demo_mode')
            ]
        ]);
    });

    // Test PayOS API với subscription (không cần auth)
    Route::get('/api/test-payos-subscription', function () {
        $vietqrService = new \App\Services\VietQrService();
        $qrData = $vietqrService->generateQrCode([
            'amount' => 199000,
            'message' => 'Thanh toán gói VIP',
            'transaction_id' => 'VIP_TEST_' . time(),
        ]);

        return response()->json([
            'success' => $qrData['success'],
            'data' => $qrData,
            'payment_info' => [
                'amount' => 199000,
                'package' => 'VIP',
                'duration' => '30 ngày'
            ]
        ]);
    });

    // Test PayOS API đơn giản
    Route::get('/test-payos-simple', function () {
        $vietqrService = new \App\Services\VietQrService();
        $qrData = $vietqrService->generateQrCode([
            'amount' => 1000,
            'message' => 'Test PayOS',
            'transaction_id' => 'TEST_' . time(),
        ]);

        return response()->json([
            'success' => $qrData['success'],
            'data' => $qrData,
            'config' => [
                'use_demo_mode' => config('vietqr.api.use_demo_mode'),
                'client_id' => config('vietqr.api.client_id'),
            ]
        ]);
    });

    // Invoice routes
    Route::get('/invoices', [InvoiceController::class, 'index'])->name('invoices.index');
    Route::get('/invoices/{id}', [InvoiceController::class, 'show'])->name('invoices.show');
    Route::get('/invoices/{id}/download', [InvoiceController::class, 'download'])->name('invoices.download');

    // Invoice API routes
    Route::get('/api/invoices', [InvoiceController::class, 'apiIndex'])->name('api.invoices.index');
    Route::get('/api/invoices/{id}', [InvoiceController::class, 'apiShow'])->name('api.invoices.show');
});

// Admin routes
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/recipes/pending', [RecipeController::class, 'pending'])->name('recipes.pending');
    Route::post('/recipes/{recipe}/approve', [RecipeController::class, 'approve'])->name('recipes.approve');
    Route::post('/recipes/{recipe}/reject', [RecipeController::class, 'reject'])->name('recipes.reject');
    Route::get('/moderation-test', ModerationTest::class)->name('moderation.test');
    Route::get('/scheduled-posts', ScheduledPosts::class)->name('scheduled-posts');
    Route::get('/pending-posts', PendingPosts::class)->name('pending-posts');

    // Payment management routes
    Route::get('/payments', [PaymentController::class, 'index'])->name('payments.index');
    Route::get('/payments/{id}', [PaymentController::class, 'show'])->name('payments.show');
    Route::post('/payments/{id}/approve', [PaymentController::class, 'approve'])->name('payments.approve');
    Route::post('/payments/{id}/reject', [PaymentController::class, 'reject'])->name('payments.reject');
    Route::get('/payments/statistics', [PaymentController::class, 'statistics'])->name('payments.statistics');
});

// Admin logout route
Route::post('/admin/logout', [AdminLogoutController::class, 'logout'])->name('admin.logout');

// Filament admin logout route
Route::post('/admin/logout', [AdminLogoutController::class, 'logout'])->name('filament.admin.auth.logout');

// Filament user logout route
Route::post('/user/logout', [AdminLogoutController::class, 'logout'])->name('filament.user.auth.logout');

// Storage Manager route (for debugging)
Route::get('/storage-manager', StorageManager::class)->name('storage.manager');

// Test route để kiểm tra admin access
Route::get('/test-admin', function () {
    $user = auth()->user();
    if (!$user) {
        return response()->json(['error' => 'Not logged in']);
    }

    return response()->json([
        'user_id' => $user->id,
        'email' => $user->email,
        'is_admin' => $user->is_admin,
        'status' => $user->status,
        'can_access_admin' => $user->is_admin ? 'YES' : 'NO'
    ]);
})->middleware('auth')->name('test.admin');

// Test route để kiểm tra admin middleware
Route::get('/test-admin-middleware', function () {
    return response()->json(['success' => 'Admin middleware passed!']);
})->middleware(['auth', 'admin'])->name('test.admin.middleware');

// Force login as admin for testing
Route::get('/force-admin-login', function () {
    $admin = \App\Models\User::where('email', 'admin@beefood.com')->first();
    if ($admin) {
        auth()->login($admin);
        return response()->json([
            'success' => 'Logged in as admin',
            'user' => [
                'id' => $admin->id,
                'email' => $admin->email,
                'is_admin' => $admin->is_admin
            ]
        ]);
    }
    return response()->json(['error' => 'Admin user not found']);
})->name('force.admin.login');

// Force login as tungnnpn00006@gmail.com for testing
Route::get('/force-tung-login', function () {
    $admin = \App\Models\User::where('email', 'tungnnpn00006@gmail.com')->first();
    if ($admin) {
        auth()->login($admin);
        return response()->json([
            'success' => 'Logged in as Tùng',
            'user' => [
                'id' => $admin->id,
                'email' => $admin->email,
                'is_admin' => $admin->is_admin
            ]
        ]);
    }
    return response()->json(['error' => 'Tùng user not found']);
})->name('force.tung.login');

// Force login as qaatsa80@gmail.com for testing
Route::get('/force-qaatsa-login', function () {
    $admin = \App\Models\User::where('email', 'qaatsa80@gmail.com')->first();
    if ($admin) {
        auth()->login($admin);
        return response()->json([
            'success' => 'Logged in as Tùng Nguyễn',
            'user' => [
                'id' => $admin->id,
                'email' => $admin->email,
                'is_admin' => $admin->is_admin
            ]
        ]);
    }
    return response()->json(['error' => 'qaatsa80 user not found']);
})->name('force.qaatsa.login');

// Test auto verification (admin only)
Route::get('/test-auto-verify', function () {
    $minutes = request('minutes', 5);
    \Artisan::call('payments:auto-verify', ['--minutes' => $minutes]);
    return response()->json([
        'success' => true,
        'message' => 'Auto verification completed',
        'output' => \Artisan::output()
    ]);
})->middleware(['auth', 'admin'])->name('test.auto.verify');

// Webhook cho PayOS
Route::post('/webhook/payos', function (Request $request) {
    Log::info('PayOS Webhook received', $request->all());

    // Xử lý webhook từ PayOS
    $data = $request->all();

    // Kiểm tra signature nếu cần
    // $signature = $request->header('x-signature');

    if (isset($data['orderCode']) && isset($data['status'])) {
        $transactionId = $data['orderCode'];
        $status = $data['status'];
        $amount = $data['amount'] ?? 0;

        // Tìm subscription theo transaction_id
        $subscription = \App\Models\UserSubscription::where('transaction_id', $transactionId)
            ->where('status', 'pending')
            ->first();

        if ($subscription && $status === 'success') {
            // Cập nhật trạng thái subscription
            $subscription->update([
                'status' => 'active',
                'payment_details' => json_encode([
                    'verified_at' => now(),
                    'method' => 'payos_webhook',
                    'webhook_data' => $data
                ])
            ]);

            // Cập nhật hóa đơn
            $invoice = \App\Models\PaymentInvoice::where('transaction_id', $transactionId)->first();
            if ($invoice) {
                $invoice->markAsPaid([
                    'verified_at' => now(),
                    'method' => 'payos_webhook',
                    'webhook_data' => $data
                ]);
            }

            Log::info('Payment verified via PayOS webhook', [
                'transaction_id' => $transactionId,
                'user_id' => $subscription->user_id,
                'amount' => $amount
            ]);

            return response()->json(['success' => true]);
        }
    }

    return response()->json(['success' => false, 'message' => 'Invalid webhook data']);
})->name('webhook.payos');

// Kiểm tra trạng thái VIP
Route::get('/check-vip-status', function () {
    $user = Auth::user();
    if (!$user) {
        return response()->json(['success' => false, 'message' => 'Chưa đăng nhập']);
    }

    $activeSubscription = $user->activeSubscription();
    $isVip = $user->isVip();

    return response()->json([
        'success' => true,
        'is_vip' => $isVip,
        'subscription' => $activeSubscription ? [
            'type' => $activeSubscription->subscription_type,
            'status' => $activeSubscription->status,
            'start_date' => $activeSubscription->start_date,
            'end_date' => $activeSubscription->end_date,
            'remaining_days' => $activeSubscription->getRemainingDays()
        ] : null
    ]);
})->middleware('auth')->name('check.vip.status');

// Test OpenAI API
Route::get('/test-openai', function () {
    $openAiService = new \App\Services\OpenAiService();
    $result = $openAiService->testConnection();

    return response()->json($result);
})->name('test.openai');

// Test WeeklyMealPlan creation
Route::get('/test-mealplan', function () {
    try {
        $user = \App\Models\User::first();
        if (!$user) {
            return response()->json(['error' => 'No user found']);
        }

        $service = new \App\Services\WeeklyMealPlanService();

        $mealPlan = $service->createMealPlan($user, 'Test Plan', now()->startOfWeek());

        return response()->json([
            'success' => true,
            'meal_plan' => $mealPlan->toArray()
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString()
        ]);
    }
})->name('test.mealplan');

// Test Livewire component
Route::get('/test-livewire', function () {
    return view('test-livewire');
})->name('test.livewire');

// Test weekly meals generation
Route::get('/test-weekly-meals', function () {
    try {
        $user = \App\Models\User::first();
        if (!$user) {
            return response()->json(['error' => 'No user found']);
        }

        $mealPlanId = request()->get('meal_plan_id');

        if ($mealPlanId) {
            $mealPlan = \App\Models\WeeklyMealPlan::find($mealPlanId);
        } else {
            $mealPlan = \App\Models\WeeklyMealPlan::where('user_id', $user->id)
                ->orderBy('created_at', 'desc')
                ->first();
        }

        if (!$mealPlan) {
            return response()->json(['error' => 'No meal plan found']);
        }

        $service = new \App\Services\WeeklyMealPlanService();
        $weeklyMeals = $service->generateWeeklyMeals($mealPlan);

        return response()->json([
            'success' => true,
            'meal_plan_id' => $mealPlan->id,
            'weekly_meals' => $weeklyMeals,
            'weekly_meals_count' => count($weeklyMeals)
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString()
        ]);
    }
})->name('test.weekly.meals');

require __DIR__ . '/ai.php';

require __DIR__ . '/auth.php';
