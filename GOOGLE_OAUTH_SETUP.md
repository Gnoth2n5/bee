# Hướng dẫn cấu hình Google OAuth cho BeeFood

## 🔧 Fix lỗi "Missing required parameter: redirect_uri"

### Bước 1: Kiểm tra cấu hình hiện tại

Chạy lệnh sau để kiểm tra:

```bash
php artisan google:check-config
```

### Bước 2: Tạo Google OAuth 2.0 Client

1. **Truy cập Google Cloud Console**:

    - Vào [Google Cloud Console](https://console.cloud.google.com/)
    - Đăng nhập bằng tài khoản Google

2. **Tạo hoặc chọn project**:

    - Tạo project mới hoặc chọn project hiện có
    - Đảm bảo project đã được chọn

3. **Kích hoạt Google+ API**:

    - Vào "APIs & Services" > "Library"
    - Tìm "Google+ API" hoặc "Google Identity"
    - Click "Enable"

4. **Tạo OAuth 2.0 credentials**:
    - Vào "APIs & Services" > "Credentials"
    - Click "Create Credentials" > "OAuth 2.0 Client IDs"
    - Chọn "Web application"
    - Đặt tên cho client (ví dụ: "BeeFood Web Client")

### Bước 3: Cấu hình Authorized Redirect URIs

**QUAN TRỌNG**: Thêm chính xác các URI sau vào "Authorized redirect URIs":

```
http://127.0.0.1:8000/auth/google/callback
http://localhost:8000/auth/google/callback
```

**Lưu ý**:

-   Không thêm dấu `/` ở cuối
-   Đảm bảo protocol (http/https) khớp chính xác
-   Trong production, sử dụng domain thực tế

### Bước 4: Cập nhật file .env

Thêm các biến môi trường sau vào file `.env`:

```env
# Google OAuth Configuration
GOOGLE_CLIENT_ID=your_google_client_id_here
GOOGLE_CLIENT_SECRET=your_google_client_secret_here

# App URL (đảm bảo không có dấu / ở cuối)
APP_URL=http://127.0.0.1:8000
```

**Lưu ý**:

-   Copy chính xác Client ID và Client Secret từ Google Console
-   Đảm bảo APP_URL không có dấu `/` ở cuối
-   Không có khoảng trắng xung quanh dấu `=`

### Bước 5: Clear cache và kiểm tra

```bash
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan google:check-config
```

### Bước 6: Test chức năng

1. **Khởi động server**:

    ```bash
    php artisan serve
    ```

2. **Truy cập trang đăng nhập**:

    - Vào `http://127.0.0.1:8000/login`
    - Click nút "Đăng nhập bằng Google"

3. **Kiểm tra logs nếu có lỗi**:
    ```bash
    tail -f storage/logs/laravel.log
    ```

## 🚨 Troubleshooting

### Lỗi "redirect_uri_mismatch"

**Nguyên nhân**: Redirect URI trong Google Console không khớp với URI thực tế

**Cách fix**:

1. Kiểm tra lại URI trong Google Console
2. Đảm bảo protocol (http/https) khớp chính xác
3. Không có dấu `/` thừa ở cuối
4. Clear cache: `php artisan config:clear`

### Lỗi "invalid_client"

**Nguyên nhân**: Client ID hoặc Client Secret không đúng

**Cách fix**:

1. Copy lại chính xác từ Google Console
2. Kiểm tra không có khoảng trắng thừa
3. Đảm bảo project đã được chọn đúng

### Lỗi "Missing required parameter: redirect_uri"

**Nguyên nhân**: Cấu hình redirect URI không đúng

**Cách fix**:

1. Kiểm tra file `config/services.php`
2. Đảm bảo APP_URL được cấu hình đúng
3. Clear cache và restart server

### User không được tạo

**Nguyên nhân**: Lỗi database hoặc migration

**Cách fix**:

1. Chạy migration: `php artisan migrate`
2. Kiểm tra quyền ghi database
3. Xem logs: `storage/logs/laravel.log`

## 📋 Checklist

-   [ ] Google+ API đã được kích hoạt
-   [ ] OAuth 2.0 Client ID đã được tạo
-   [ ] Redirect URIs đã được cấu hình đúng
-   [ ] GOOGLE_CLIENT_ID đã được thêm vào .env
-   [ ] GOOGLE_CLIENT_SECRET đã được thêm vào .env
-   [ ] APP_URL đã được cấu hình đúng
-   [ ] Cache đã được clear
-   [ ] Server đã được restart
-   [ ] Test đăng nhập thành công

## 🔍 Debug Commands

```bash
# Kiểm tra cấu hình
php artisan google:check-config

# Xem routes
php artisan route:list | findstr google

# Clear cache
php artisan config:clear
php artisan cache:clear

# Xem logs
tail -f storage/logs/laravel.log
```

## 📞 Support

Nếu vẫn gặp vấn đề:

1. Kiểm tra logs trong `storage/logs/laravel.log`
2. Chạy `php artisan google:check-config`
3. Verify Google Console configuration
4. Contact development team
