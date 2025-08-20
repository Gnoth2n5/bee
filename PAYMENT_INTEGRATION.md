# Tích hợp Thanh toán VietQR vào Dự án Bee

## Tổng quan

Dự án Bee đã được tích hợp module thanh toán VietQR từ dự án `mr4-lc.vietqr-main` để hỗ trợ các chức năng:

1. **Gói VIP cho người dùng**: Mua gói Premium/VIP để sử dụng tính năng nâng cao
2. **Tìm món ăn theo bản đồ**: Tính năng tìm kiếm nâng cao cho VIP
3. **Quảng cáo cửa hàng**: Cho phép chủ nhà hàng quảng cáo cửa hàng của mình

## Cấu trúc Database

### Bảng mới được tạo:

1. **vietqr_banks**: Lưu thông tin ngân hàng
2. **vietqr_service_codes**: Lưu mã dịch vụ
3. **vietqr_informations**: Lưu thông tin tài khoản thanh toán
4. **user_subscriptions**: Quản lý gói VIP của người dùng
5. **restaurant_ads**: Quản lý quảng cáo cửa hàng

## Cài đặt

### 1. Chạy Migration

```bash
php artisan migrate
```

### 2. Chạy Seeder

```bash
php artisan db:seed --class=VietqrSeeder
```

### 3. Cài đặt Dependencies

Thêm vào `composer.json`:

```json
{
    "require": {
        "simplesoftwareio/simple-qrcode": "^4.2",
        "khanamiryan/qrcode-detector-decoder": "^1.0"
    }
}
```

Chạy:

```bash
composer install
```

## Cấu hình

### 1. File Config

File `config/vietqr.php` đã được tạo với cấu hình mặc định:

```php
return [
    'logo' => 'logo.png',
    'default' => [
        'transaction_currency' => '704',
        'country_code' => 'VN',
    ],
    'validation' => [
        'account_id' => 'required|exists:vietqr_informations,id',
        'transaction_amount' => 'required|numeric|min:1000',
        'message' => 'nullable|string|max:100',
        'transaction_id' => 'nullable|string|max:50',
    ],
];
```

### 2. Cập nhật tài khoản thanh toán

Vào database và cập nhật thông tin tài khoản thật trong bảng `vietqr_informations`.

## Sử dụng

### 1. Gói Dịch Vụ

#### Các gói có sẵn:

-   **Basic**: Miễn phí - Tính năng cơ bản
-   **Premium**: 99,000 VNĐ/tháng - Tính năng nâng cao
-   **VIP**: 199,000 VNĐ/tháng - Tính năng đặc biệt

#### Truy cập trang gói dịch vụ:

```
/subscriptions/packages
```

### 2. API VietQR

#### Tạo QR Code thanh toán:

```http
POST /api/vietqr
Content-Type: application/json

{
    "account_id": 1,
    "transaction_amount": 99000,
    "message": "Thanh toan goi premium",
    "transaction_id": "TXN_123456"
}
```

#### Mã hóa QR Code:

```http
POST /api/vietqr/encode
```

#### Giải mã QR Code:

```http
POST /api/vietqr/decode
{
    "data": "QR_CODE_STRING"
}
```

#### Đọc QR Code từ hình ảnh:

```http
POST /api/vietqr/detect
Content-Type: multipart/form-data

image: [file]
```

### 3. Quảng cáo Cửa hàng

#### Tạo quảng cáo:

```http
POST /restaurant-ads
Content-Type: multipart/form-data

restaurant_id: 1
title: "Quảng cáo nhà hàng"
description: "Mô tả quảng cáo"
image: [file]
start_date: "2024-01-01"
end_date: "2024-01-31"
amount: 50000
payment_method: "vietqr"
```

#### Lấy quảng cáo đang hoạt động:

```http
GET /api/restaurant-ads/active
```

## Middleware

### CheckVipAccess

Middleware để kiểm tra quyền truy cập VIP:

```php
Route::middleware(['auth', 'vip'])->group(function () {
    // Routes chỉ dành cho VIP
});
```

Đăng ký middleware trong `app/Http/Kernel.php`:

```php
protected $routeMiddleware = [
    // ...
    'vip' => \App\Http\Middleware\CheckVipAccess::class,
];
```

## Model Relationships

### User Model

```php
// Kiểm tra VIP
$user->isVip();
$user->isPremium();

// Lấy subscription hiện tại
$user->activeSubscription();

// Lấy quảng cáo
$user->restaurantAds();
$user->activeRestaurantAds();
```

### UserSubscription Model

```php
// Kiểm tra trạng thái
$subscription->isActive();
$subscription->isVip();
$subscription->isPremium();

// Lấy số ngày còn lại
$subscription->getRemainingDays();
```

### RestaurantAd Model

```php
// Kiểm tra trạng thái
$ad->isActive();

// Thống kê
$ad->incrementViews();
$ad->incrementClicks();
$ad->getCTR();
```

## Tính năng VIP

### 1. Tìm món ăn theo bản đồ nâng cao

-   Bản đồ tương tác
-   Tìm kiếm theo khoảng cách
-   Lọc theo đánh giá, giá cả

### 2. Quảng cáo cửa hàng

-   Hiển thị ưu tiên trong kết quả tìm kiếm
-   Banner quảng cáo
-   Thống kê lượt xem, lượt click

### 3. Tính năng đặc biệt

-   Hỗ trợ 24/7
-   Giảm giá đặc biệt
-   Tính năng beta

## Webhook (Tùy chọn)

Để tự động xác minh thanh toán, bạn có thể tích hợp webhook từ VietQR:

```php
// Trong VietQrController
public function webhook(Request $request)
{
    // Xử lý webhook từ VietQR
    // Cập nhật trạng thái thanh toán
}
```

## Bảo mật

1. **Validation**: Tất cả input đều được validate
2. **Authorization**: Kiểm tra quyền sở hữu
3. **CSRF Protection**: Bảo vệ khỏi CSRF attack
4. **Rate Limiting**: Giới hạn số request

## Troubleshooting

### Lỗi thường gặp:

1. **QR Code không hiển thị**: Kiểm tra package `simplesoftwareio/simple-qrcode`
2. **Không đọc được QR**: Kiểm tra package `khanamiryan/qrcode-detector-decoder`
3. **Thanh toán không xác minh**: Kiểm tra webhook hoặc manual verification

### Debug:

```php
// Kiểm tra tài khoản VietQR
$account = VietqrInformation::where('status', 1)->first();
dd($account);

// Kiểm tra subscription
$user = Auth::user();
dd($user->activeSubscription());
```

## Tương lai

-   Tích hợp thêm các cổng thanh toán khác (Stripe, PayPal)
-   Hệ thống affiliate
-   Gói doanh nghiệp
-   API cho mobile app
