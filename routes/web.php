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
use App\Http\Controllers\Admin\PaymentController as AdminPaymentController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\DiseaseAnalysisController;

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
use App\Livewire\DiseaseAnalysis;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Artisan;

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

// Weekly Meal Plan Livewire Component
Route::get('/weekly-meal-plan', \App\Livewire\MealPlans\WeeklyMealPlanPage::class)->name('weekly-meal-plan');


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

    // VIP payment routes
    Route::get('/vip/upgrade', [PaymentController::class, 'upgrade'])->name('vip.upgrade');
    Route::get('/vip/payment-history', [PaymentController::class, 'history'])->name('vip.payment-history');

    // VIP features routes - Moved to routes/vip.php

    // Test route để kiểm tra VIP
    Route::get('/test-vip', function () {
        /** @var \App\Models\User $user */
        $user = Auth::user();
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
    Route::get('/payments', [AdminPaymentController::class, 'index'])->name('payments.index');
    Route::get('/payments/{id}', [AdminPaymentController::class, 'show'])->name('payments.show');
    Route::post('/payments/{id}/approve', [AdminPaymentController::class, 'approve'])->name('payments.approve');
    Route::post('/payments/{id}/reject', [AdminPaymentController::class, 'reject'])->name('payments.reject');
    Route::get('/payments/statistics', [AdminPaymentController::class, 'statistics'])->name('payments.statistics');
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
    $user = Auth::user();
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
        Auth::login($admin);
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


// Test auto verification (admin only)
Route::get('/test-auto-verify', function () {
    $minutes = request('minutes', 5);
    Artisan::call('payments:auto-verify', ['--minutes' => $minutes]);
    return response()->json([
        'success' => true,
        'message' => 'Auto verification completed',
        'output' => Artisan::output()
    ]);
})->middleware(['auth', 'admin'])->name('test.auto.verify');

// VIP Payment webhook - excluded from CSRF protection (like old project)
Route::post('/webhooks/payment', [PaymentController::class, 'webhook'])
    ->name('webhooks.payment')
    ->withoutMiddleware(['auth', 'web']);

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
    /** @var \App\Models\User $user */
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

// API endpoint để check VIP status và payment success
Route::get('/api/check-vip-status', function () {
    /** @var \App\Models\User $user */
    $user = Auth::user();
    if (!$user) {
        return response()->json(['success' => false, 'message' => 'Chưa đăng nhập']);
    }

    $isVip = $user->isVip();

    // Check payment success from cache
    $paymentSuccess = Cache::get("vip_payment_success_{$user->id}");

    // Clear the cache after reading to show notification only once
    if ($paymentSuccess) {
        Cache::forget("vip_payment_success_{$user->id}");
    }

    return response()->json([
        'success' => true,
        'is_vip' => $isVip,
        'user_id' => $user->id,
        'payment_success' => $paymentSuccess,
        'profile' => $user->profile ? [
            'isVipAccount' => $user->profile->isVipAccount,
            'vip_expires_at' => $user->profile->vip_expires_at,
            'vip_plan' => $user->profile->vip_plan
        ] : null
    ]);
})->middleware('auth')->name('api.check.vip.status');


// Test VIP middleware protection
Route::get('/test-vip-middleware', function () {
    /** @var \App\Models\User $user */
    $user = Auth::user();
    return response()->json([
        'success' => true,
        'message' => 'VIP middleware test passed',
        'user_id' => $user->id,
        'is_vip' => $user->isVip(),
        'timestamp' => now()
    ]);
})->middleware(['auth', 'vip'])->name('test.vip.middleware');

// Test VIP Payment webhook (for development only)
Route::get('/test-vip-payment/{userId}', function ($userId) {
    $user = \App\Models\User::find($userId);
    if (!$user) {
        return response()->json(['error' => 'User not found'], 404);
    }

    // Simulate webhook payload
    $payload = [
        'transferAmount' => 2000,
        'description' => "DH{$userId}",
        'gateway' => 'test',
        'transactionDate' => now()->toISOString(),
        'accountNumber' => '0975821009',
        'code' => 'TEST_' . time(),
        'content' => 'Test payment',
        'transferType' => 'in',
        'accumulated' => 2000,
        'subAccount' => '',
        'referenceCode' => 'TEST_REF_' . time()
    ];

    try {
        $paymentService = new \App\Services\PaymentService();
        $payment = $paymentService->createFromWebhook($payload);

        return response()->json([
            'success' => true,
            'message' => 'Test payment processed successfully',
            'payment_id' => $payment->id,
            'user_vip_status' => $user->fresh()->isVip()
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'error' => $e->getMessage()
        ], 500);
    }
})->name('test.vip.payment');

require __DIR__ . '/auth.php';
