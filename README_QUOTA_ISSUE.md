# 🔥 Vấn đề Quota API Gemini

## ❌ Lỗi hiện tại:

API key `AIzaSyBxDDfR6EVVidKdRz8rgBmdiEPkNKF9YNM` đã hết quota (giới hạn sử dụng).

## 📊 Chi tiết lỗi:

-   **Mã lỗi**: 429 (RESOURCE_EXHAUSTED)
-   **Nguyên nhân**: Vượt quá giới hạn sử dụng miễn phí
-   **Giới hạn**:
    -   Số request/ngày
    -   Số request/phút
    -   Số token input/phút

## 🛠️ Giải pháp:

### 1. **Tạo API Key mới** (Khuyến nghị)

1. Truy cập: https://makersuite.google.com/app/apikey
2. Tạo API key mới
3. Cập nhật trong file `.env`:
    ```
    GEMINI_API_KEY=your_new_api_key_here
    ```

### 2. **Nâng cấp tài khoản** (Cho production)

1. Truy cập: https://ai.google.dev/pricing
2. Chọn plan phù hợp
3. Có quota cao hơn và ổn định hơn

### 3. **Sử dụng API Key khác**

-   Tạo nhiều API key để luân phiên sử dụng
-   Mỗi key có quota riêng

## 🔧 Cách cập nhật API Key:

### Bước 1: Lấy API Key mới

```bash
# Truy cập Google AI Studio
# Tạo API key mới
```

### Bước 2: Cập nhật .env

```bash
# Mở file .env
# Thay đổi dòng:
GEMINI_API_KEY=your_new_api_key_here
```

### Bước 3: Clear cache

```bash
php artisan config:clear
```

### Bước 4: Test

```bash
php test_gemini_api.php
```

## 📋 Kiểm tra trạng thái:

-   **Test API**: `php test_gemini_api.php`
-   **Test Image Search**: Upload ảnh trên trang chủ
-   **Logs**: `storage/logs/laravel.log`

## ⚠️ Lưu ý:

-   API key miễn phí có giới hạn nghiêm ngặt
-   Nên sử dụng API key riêng cho mỗi dự án
-   Backup API key quan trọng
-   Monitor quota usage thường xuyên

## 🎯 Kết quả mong đợi:

Sau khi cập nhật API key mới, tính năng tìm kiếm bằng ảnh sẽ hoạt động bình thường.
