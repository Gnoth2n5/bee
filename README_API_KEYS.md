# 🔑 Hướng dẫn tạo và quản lý Gemini API Keys

## 📋 Tổng quan

Dự án Bee Recipe sử dụng Google Gemini AI để phân tích ảnh món ăn. Do giới hạn quota miễn phí, bạn cần tạo nhiều API keys để đảm bảo tính năng hoạt động liên tục.

## 🎯 Giới hạn quota miễn phí

-   **15 requests/phút** cho mỗi tài khoản Google
-   **1000 requests/ngày** cho mỗi tài khoản Google
-   **Giải pháp**: Tạo nhiều tài khoản Google để có nhiều quota

## 🚀 Các script có sẵn

### 1. `auto_generate_key.php` - Tạo API Key mới

```bash
php auto_generate_key.php
```

**Chức năng:**

-   Hướng dẫn chi tiết tạo tài khoản Google mới
-   Hướng dẫn tạo API Key từ Google AI Studio
-   Tự động test và cập nhật API Key
-   Khởi động server nếu thành công

### 2. `find_working_key.php` - Tìm API Key hoạt động

```bash
php find_working_key.php
```

**Chức năng:**

-   Test tất cả API Keys có sẵn
-   Tìm key có quota còn lại
-   Tự động cập nhật key hoạt động vào .env
-   Khởi động server nếu tìm thấy

### 3. `auto_create_keys.php` - Tạo nhiều API Keys

```bash
php auto_create_keys.php
```

**Chức năng:**

-   Hướng dẫn tạo API Keys từ nhiều tài khoản
-   Lưu trữ keys vào file JSON
-   Tạo script tự động chuyển đổi keys
-   Quản lý rotation keys

### 4. `create_gemini_key.php` - Tạo key đơn giản

```bash
php create_gemini_key.php
```

**Chức năng:**

-   Hướng dẫn nhanh tạo API Key
-   Test và cập nhật key
-   Phù hợp cho người mới

## 📝 Hướng dẫn tạo API Key thủ công

### Bước 1: Tạo tài khoản Google mới

1. Mở trình duyệt ẩn danh (Ctrl+Shift+N)
2. Truy cập: https://accounts.google.com/signup
3. Tạo tài khoản với email mới
4. Xác minh email và số điện thoại

### Bước 2: Tạo API Key

1. Đăng nhập vào tài khoản Google mới
2. Truy cập: https://aistudio.google.com/app/apikey
3. Click "Create API Key"
4. Chọn "Create API Key in new project"
5. Đặt tên project: "Bee Recipe AI - Key X"
6. Click "Create"
7. Copy API Key (bắt đầu bằng AIzaSy...)

### Bước 3: Test và sử dụng

1. Chạy script: `php auto_generate_key.php`
2. Paste API Key vào
3. Script sẽ tự động test và cập nhật
4. Khởi động server nếu thành công

## 🔄 Hệ thống rotation keys

### Tự động chuyển đổi keys

Khi API Key hiện tại hết quota, hệ thống sẽ tự động chuyển sang key tiếp theo:

```php
// Sử dụng script rotation
php rotate_api_key.php
```

### Quản lý keys

-   **File lưu trữ**: `api_keys.json`
-   **File key hiện tại**: `current_key.txt`
-   **File keys hoạt động**: `working_keys.json`

## 💡 Tips quan trọng

### Tránh hết quota

1. **Tạo nhiều tài khoản Google** (5-10 tài khoản)
2. **Sử dụng email thật** để tránh bị khóa
3. **Cache kết quả phân tích** để giảm requests
4. **Nâng cấp lên plan trả phí** nếu cần

### Bảo mật

1. **Không chia sẻ API Keys** công khai
2. **Sử dụng .env** để lưu trữ keys
3. **Xóa keys cũ** khi không dùng
4. **Backup keys** vào file riêng

### Tối ưu hiệu suất

1. **Giảm kích thước ảnh** trước khi upload
2. **Sử dụng format ảnh phù hợp** (JPEG, PNG)
3. **Giới hạn số lượng requests** mỗi phút
4. **Implement retry logic** khi gặp lỗi

## 🚨 Xử lý lỗi thường gặp

### Lỗi 429 - Quota exceeded

```
API đã hết quota. Vui lòng thử lại sau hoặc liên hệ admin để nâng cấp.
```

**Giải pháp:**

-   Chạy `php find_working_key.php` để tìm key khác
-   Tạo tài khoản Google mới
-   Chờ 24h để reset quota

### Lỗi 400 - API key expired

```
API key đã hết hạn. Vui lòng liên hệ admin để cập nhật.
```

**Giải pháp:**

-   Tạo API Key mới
-   Cập nhật vào .env
-   Clear config cache

### Lỗi 403 - Invalid API key

```
API Key không hợp lệ
```

**Giải pháp:**

-   Kiểm tra lại API Key
-   Đảm bảo key bắt đầu bằng "AIzaSy"
-   Tạo key mới nếu cần

## 📊 Monitoring và Logs

### Theo dõi sử dụng

-   **File logs**: `storage/logs/laravel.log`
-   **API responses**: Debugbar (development)
-   **Quota usage**: Google AI Studio dashboard

### Metrics quan trọng

-   Số lượng requests/phút
-   Số lượng requests/ngày
-   Tỷ lệ lỗi 429
-   Thời gian response

## 🎯 Kết luận

Với hệ thống quản lý API Keys này, bạn có thể:

1. **Tự động tạo** API Keys mới
2. **Tự động tìm** key hoạt động
3. **Tự động chuyển đổi** khi hết quota
4. **Quản lý** nhiều keys cùng lúc

**Lưu ý**: Luôn có sẵn ít nhất 3-5 API Keys để đảm bảo tính năng hoạt động liên tục.
