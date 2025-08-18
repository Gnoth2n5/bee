# Tích hợp Google Maps/Places API cho tìm kiếm nhà hàng

## Tổng quan

Dự án đã được tích hợp Google Maps/Places API để cung cấp tính năng tìm kiếm và hiển thị nhà hàng trên bản đồ. Hệ thống bao gồm:

-   **Google Maps Service**: Xử lý các API calls đến Google Maps và Places API
-   **Restaurant Service**: Logic nghiệp vụ cho nhà hàng
-   **Livewire Components**: Giao diện tương tác với bản đồ
-   **Database Models**: Lưu trữ thông tin nhà hàng và đánh giá
-   **API Endpoints**: RESTful API cho frontend và mobile apps

## Cấu hình

### 1. API Keys

Đã cấu hình sẵn API key trong `config/services.php`:

```php
'google_maps' => [
    'api_key' => env('GOOGLE_MAPS_API_KEY', 'AIzaSyAmMy0OV-imvLmAjGyIUlIsPtmYVto8F-4'),
    'places_api_key' => env('GOOGLE_PLACES_API_KEY', 'AIzaSyAmMy0OV-imvLmAjGyIUlIsPtmYVto8F-4'),
],
```

### 2. Environment Variables

Thêm vào file `.env`:

```env
GOOGLE_MAPS_API_KEY=AIzaSyAmMy0OV-imvLmAjGyIUlIsPtmYVto8F-4
GOOGLE_PLACES_API_KEY=AIzaSyAmMy0OV-imvLmAjGyIUlIsPtmYVto8F-4
```

## Cài đặt

### 1. Chạy Migrations

```bash
php artisan migrate
```

### 2. Chạy Seeders

```bash
php artisan db:seed --class=RestaurantSeeder
```

### 3. Cấu hình Cache

Đảm bảo cache driver được cấu hình đúng trong `.env`:

```env
CACHE_DRIVER=redis
# hoặc
CACHE_DRIVER=file
```

## Cấu trúc Database

### Bảng `restaurants`

```sql
CREATE TABLE restaurants (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    place_id VARCHAR(255) UNIQUE NOT NULL,
    name VARCHAR(255) NOT NULL,
    formatted_address TEXT NOT NULL,
    latitude DECIMAL(10,8) NOT NULL,
    longitude DECIMAL(11,8) NOT NULL,
    rating DECIMAL(2,1) NULL,
    user_ratings_total INT DEFAULT 0,
    formatted_phone_number VARCHAR(255) NULL,
    website VARCHAR(255) NULL,
    price_level INT NULL,
    types JSON NULL,
    opening_hours JSON NULL,
    photos JSON NULL,
    reviews JSON NULL,
    is_active BOOLEAN DEFAULT TRUE,
    last_updated TIMESTAMP NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    INDEX idx_location (latitude, longitude),
    INDEX idx_rating (rating),
    INDEX idx_active (is_active)
);
```

### Bảng `restaurant_ratings`

```sql
CREATE TABLE restaurant_ratings (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id BIGINT UNSIGNED NOT NULL,
    restaurant_id BIGINT UNSIGNED NOT NULL,
    rating INT NOT NULL,
    comment TEXT NULL,
    is_verified BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    UNIQUE KEY unique_user_restaurant (user_id, restaurant_id),
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (restaurant_id) REFERENCES restaurants(id) ON DELETE CASCADE
);
```

### Bảng `restaurant_favorites`

```sql
CREATE TABLE restaurant_favorites (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id BIGINT UNSIGNED NOT NULL,
    restaurant_id BIGINT UNSIGNED NOT NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    UNIQUE KEY unique_user_restaurant (user_id, restaurant_id),
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (restaurant_id) REFERENCES restaurants(id) ON DELETE CASCADE
);
```

## Services

### GoogleMapsService

Xử lý tất cả API calls đến Google Maps và Places API:

```php
// Tìm kiếm nhà hàng gần đây
$restaurants = $googleMapsService->searchNearbyRestaurants($lat, $lng, $radius);

// Tìm kiếm theo từ khóa
$restaurants = $googleMapsService->searchRestaurantsByKeyword($keyword, $lat, $lng);

// Lấy chi tiết nhà hàng
$restaurant = $googleMapsService->getRestaurantDetails($placeId);

// Geocoding
$coordinates = $googleMapsService->geocodeAddress($address);
```

### RestaurantService

Logic nghiệp vụ cho nhà hàng:

```php
// Tìm kiếm với filters
$restaurants = $restaurantService->searchNearbyRestaurants($lat, $lng, $radius, $filters);

// Quản lý favorites
$restaurantService->addToFavorites($userId, $restaurantId);
$restaurantService->removeFromFavorites($userId, $restaurantId);

// Đánh giá
$restaurantService->rateRestaurant($userId, $restaurantId, $rating, $comment);
```

## Livewire Components

### RestaurantMap

Component chính hiển thị bản đồ và danh sách nhà hàng:

```php
// Sử dụng trong Blade
<livewire:restaurants.restaurant-map />
```

Tính năng:

-   Hiển thị Google Maps với markers
-   Tìm kiếm theo từ khóa
-   Bộ lọc (rating, giá, giờ mở cửa)
-   Lấy vị trí hiện tại
-   Modal chi tiết nhà hàng
-   Thêm/xóa favorites
-   Đánh giá nhà hàng

## API Endpoints

### Public APIs

```http
# Tìm kiếm nhà hàng gần đây
GET /api/restaurants/search/nearby?latitude=21.0368&longitude=105.8342&radius=5000

# Tìm kiếm theo từ khóa
GET /api/restaurants/search/keyword?keyword=phở&latitude=21.0368&longitude=105.8342

# Chi tiết nhà hàng
GET /api/restaurants/{placeId}

# Đánh giá nhà hàng
GET /api/restaurants/{restaurantId}/ratings

# Nhà hàng phổ biến
GET /api/restaurants/popular

# Nhà hàng mới
GET /api/restaurants/recent

# Geocoding
POST /api/restaurants/geocode
Content-Type: application/json
{
    "address": "Hà Nội, Việt Nam"
}
```

### Protected APIs (Yêu cầu authentication)

```http
# Thêm vào favorites
POST /api/restaurants/favorites
Content-Type: application/json
{
    "restaurant_id": 1
}

# Xóa khỏi favorites
DELETE /api/restaurants/favorites
Content-Type: application/json
{
    "restaurant_id": 1
}

# Lấy danh sách favorites
GET /api/restaurants/favorites

# Đánh giá nhà hàng
POST /api/restaurants/rate
Content-Type: application/json
{
    "restaurant_id": 1,
    "rating": 5,
    "comment": "Nhà hàng rất ngon!"
}
```

## Routes

### Web Routes

```php
// Trang chính
Route::get('/restaurants', [RestaurantController::class, 'index'])->name('restaurants.index');

// Livewire component
Route::get('/restaurants/map', App\Livewire\Restaurants\RestaurantMap::class)->name('restaurants.map');

// Protected routes
Route::middleware(['auth'])->group(function () {
    Route::post('/restaurants/favorites', [RestaurantController::class, 'addToFavorites']);
    Route::delete('/restaurants/favorites', [RestaurantController::class, 'removeFromFavorites']);
    Route::get('/restaurants/favorites', [RestaurantController::class, 'getFavorites']);
    Route::post('/restaurants/rate', [RestaurantController::class, 'rate']);
});
```

### API Routes

```php
Route::prefix('restaurants')->name('restaurants.')->group(function () {
    Route::get('/search/nearby', [RestaurantController::class, 'searchNearby']);
    Route::get('/search/keyword', [RestaurantController::class, 'searchByKeyword']);
    Route::get('/{placeId}', [RestaurantController::class, 'show']);
    Route::get('/{restaurantId}/ratings', [RestaurantController::class, 'getRatings']);
    Route::get('/popular', [RestaurantController::class, 'getPopular']);
    Route::get('/recent', [RestaurantController::class, 'getRecent']);
    Route::post('/geocode', [RestaurantController::class, 'geocode']);
});

Route::middleware('auth:sanctum')->prefix('restaurants')->name('restaurants.')->group(function () {
    Route::post('/favorites', [RestaurantController::class, 'addToFavorites']);
    Route::delete('/favorites', [RestaurantController::class, 'removeFromFavorites']);
    Route::get('/favorites', [RestaurantController::class, 'getFavorites']);
    Route::post('/rate', [RestaurantController::class, 'rate']);
});
```

## Models

### Restaurant

```php
class Restaurant extends Model
{
    protected $fillable = [
        'place_id', 'name', 'formatted_address', 'latitude', 'longitude',
        'rating', 'user_ratings_total', 'formatted_phone_number', 'website',
        'price_level', 'types', 'opening_hours', 'photos', 'reviews',
        'is_active', 'last_updated'
    ];

    protected $casts = [
        'latitude' => 'float',
        'longitude' => 'float',
        'rating' => 'float',
        'types' => 'array',
        'opening_hours' => 'array',
        'photos' => 'array',
        'reviews' => 'array',
        'is_active' => 'boolean',
        'last_updated' => 'datetime'
    ];

    // Relationships
    public function recipes(): BelongsToMany
    public function favoritedBy(): BelongsToMany
    public function ratings(): HasMany

    // Scopes
    public function scopeNearby($query, $latitude, $longitude, $radius = 5)
    public function scopeWithRating($query, $minRating = 0)
    public function scopeByType($query, $type)
    public function scopeOpenNow($query)

    // Accessors
    public function getFirstPhotoAttribute()
    public function getAverageRatingAttribute()
    public function getTotalRatingsAttribute()
    public function getPriceLevelTextAttribute()
    public function getOpeningHoursTextAttribute()
    public function getShortAddressAttribute()
}
```

### RestaurantRating

```php
class RestaurantRating extends Model
{
    protected $fillable = [
        'user_id', 'restaurant_id', 'rating', 'comment', 'is_verified'
    ];

    protected $casts = [
        'rating' => 'integer',
        'is_verified' => 'boolean'
    ];

    // Relationships
    public function user(): BelongsTo
    public function restaurant(): BelongsTo

    // Scopes
    public function scopeVerified($query)
    public function scopeWithRating($query, $rating)
    public function scopeByUser($query, $userId)
    public function scopeByRestaurant($query, $restaurantId)

    // Static methods
    public static function getAverageRatingForRestaurant($restaurantId)
    public static function getRatingCountForRestaurant($restaurantId)
    public static function hasUserRated($userId, $restaurantId)
    public static function getUserRating($userId, $restaurantId)

    // Accessors
    public function getRatingStarsAttribute()
    public function getRatingTextAttribute()
}
```

## Tính năng chính

### 1. Tìm kiếm nhà hàng

-   **Tìm kiếm gần đây**: Dựa trên tọa độ và bán kính
-   **Tìm kiếm theo từ khóa**: Tên nhà hàng, địa chỉ, loại ẩm thực
-   **Geocoding**: Chuyển đổi địa chỉ thành tọa độ
-   **Bộ lọc**: Rating, giá cả, giờ mở cửa, loại nhà hàng

### 2. Hiển thị bản đồ

-   **Google Maps**: Bản đồ tương tác với markers
-   **Info Windows**: Thông tin nhà hàng khi click marker
-   **Custom Markers**: Icons tùy chỉnh cho nhà hàng
-   **User Location**: Hiển thị vị trí người dùng

### 3. Quản lý favorites

-   **Thêm/xóa favorites**: Chỉ dành cho user đã đăng nhập
-   **Danh sách favorites**: Xem tất cả nhà hàng yêu thích
-   **Sync với database**: Lưu trữ lâu dài

### 4. Đánh giá và reviews

-   **Rating system**: 1-5 sao
-   **Comments**: Viết đánh giá chi tiết
-   **User verification**: Xác minh đánh giá
-   **Aggregate ratings**: Tính trung bình và tổng số đánh giá

### 5. Cache và Performance

-   **API caching**: Cache kết quả Google Places API
-   **Database caching**: Cache queries phức tạp
-   **Image optimization**: Tối ưu hóa ảnh nhà hàng
-   **Lazy loading**: Tải dữ liệu theo nhu cầu

## Bảo mật

### 1. API Key Protection

-   API keys được lưu trong environment variables
-   Không expose keys trong client-side code
-   Rate limiting cho API calls

### 2. Input Validation

-   Validate tất cả input parameters
-   Sanitize user inputs
-   Prevent SQL injection

### 3. Authentication

-   Protected routes yêu cầu authentication
-   CSRF protection cho forms
-   Session management

## Monitoring và Logging

### 1. Error Logging

```php
Log::error('Google Places API exception', [
    'message' => $e->getMessage(),
    'place_id' => $placeId
]);
```

### 2. Performance Monitoring

-   Track API response times
-   Monitor cache hit rates
-   Log slow queries

### 3. Usage Analytics

-   Track popular searches
-   Monitor user interactions
-   Analyze favorite patterns

## Troubleshooting

### 1. API Quota Exceeded

```bash
# Check API usage
php artisan tinker
>>> app(App\Services\GoogleMapsService::class)->checkApiQuota();
```

### 2. Cache Issues

```bash
# Clear cache
php artisan cache:clear

# Clear specific cache keys
php artisan tinker
>>> Cache::forget('nearby_restaurants_21.0368_105.8342_5000_restaurant');
```

### 3. Database Issues

```bash
# Check database connection
php artisan tinker
>>> DB::connection()->getPdo();

# Reset migrations
php artisan migrate:fresh --seed
```

## Tương lai

### 1. Tính năng mở rộng

-   **Đặt bàn online**: Tích hợp với hệ thống đặt bàn
-   **Delivery tracking**: Theo dõi giao hàng
-   **Menu integration**: Hiển thị menu nhà hàng
-   **Payment integration**: Thanh toán online

### 2. Performance Optimization

-   **CDN**: Sử dụng CDN cho ảnh
-   **Database indexing**: Tối ưu hóa queries
-   **Caching strategy**: Redis clustering
-   **API optimization**: GraphQL implementation

### 3. Mobile App

-   **React Native**: Mobile app với cùng API
-   **Push notifications**: Thông báo khuyến mãi
-   **Offline support**: Cache dữ liệu offline
-   **Location services**: Background location tracking

## Liên hệ

Nếu có vấn đề hoặc cần hỗ trợ, vui lòng liên hệ:

-   **Email**: support@beerecipe.com
-   **Documentation**: https://docs.beerecipe.com
-   **GitHub**: https://github.com/beerecipe/restaurant-api
