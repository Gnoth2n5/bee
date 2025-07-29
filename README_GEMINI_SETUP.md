# Cấu hình Gemini AI cho tính năng phân tích ảnh

## Tổng quan

Tính năng phân tích ảnh món ăn sử dụng Google Gemini AI để nhận diện và tìm kiếm công thức nấu ăn dựa trên ảnh.

## Cài đặt

### 1. Lấy API Key Gemini

1. Truy cập [Google AI Studio](https://makersuite.google.com/app/apikey)
2. Đăng nhập bằng tài khoản Google
3. Tạo API key mới
4. Sao chép API key

### 2. Cấu hình trong file .env

Thêm dòng sau vào file `.env`:

```env
GEMINI_API_KEY=your_gemini_api_key_here
```

### 3. Cấu hình đã được thêm vào config/services.php

```php
'gemini' => [
    'api_key' => env('GEMINI_API_KEY'),
],
```

## Tính năng

### Phân tích ảnh món ăn

-   Upload ảnh món ăn
-   AI sẽ phân tích và trả về các từ khóa tìm kiếm
-   Tự động tìm kiếm công thức với từ khóa phù hợp nhất

### Giới hạn

-   Kích thước file tối đa: 5MB
-   Định dạng hỗ trợ: JPG, PNG, GIF, WebP
-   Số lượng request: Theo giới hạn của Gemini API

## Sử dụng

1. Truy cập trang chủ
2. Click vào icon camera bên cạnh ô tìm kiếm
3. Chọn ảnh món ăn
4. Click "Phân tích ảnh"
5. Hệ thống sẽ tự động tìm kiếm công thức phù hợp

## Troubleshooting

### Lỗi "Không thể phân tích ảnh"

-   Kiểm tra API key có đúng không
-   Kiểm tra kết nối internet
-   Thử lại với ảnh khác

### Lỗi "File quá lớn"

-   Nén ảnh xuống dưới 5MB
-   Chuyển đổi sang định dạng JPG

### Lỗi "Không nhận diện được món ăn"

-   Sử dụng ảnh rõ nét, có món ăn ở trung tâm
-   Tránh ảnh có nhiều món ăn khác nhau
-   Đảm bảo ánh sáng đủ sáng

## API Endpoints

### Gemini Vision API

-   URL: `https://generativelanguage.googleapis.com/v1beta/models/gemini-pro-vision:generateContent`
-   Method: POST
-   Content-Type: application/json

### Request Format

```json
{
    "contents": [
        {
            "parts": [
                {
                    "text": "Đây là ảnh món ăn. Hãy trả về danh sách các từ khóa tìm kiếm..."
                },
                {
                    "inline_data": {
                        "mime_type": "image/jpeg",
                        "data": "base64_encoded_image_data"
                    }
                }
            ]
        }
    ]
}
```

## Bảo mật

-   API key được lưu trong file .env (không commit lên git)
-   Validate file upload (kích thước, định dạng)
-   Xử lý lỗi an toàn, không expose thông tin nhạy cảm
