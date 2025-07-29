# Tính năng Tìm kiếm Công thức bằng Ảnh với Gemini AI

## 🎯 Tổng quan

Tính năng mới cho phép người dùng upload ảnh món ăn và sử dụng AI để tự động tìm kiếm công thức nấu ăn phù hợp. Sử dụng Google Gemini AI để phân tích ảnh và trả về các từ khóa tìm kiếm chính xác.

## 🚀 Tính năng chính

### ✨ Tìm kiếm thông minh

-   **Upload ảnh**: Click icon camera bên cạnh ô tìm kiếm
-   **Phân tích AI**: Gemini AI phân tích ảnh và nhận diện món ăn
-   **Từ khóa tự động**: Trả về danh sách từ khóa tìm kiếm phù hợp
-   **Tìm kiếm ngay**: Tự động tìm kiếm với từ khóa tốt nhất

### 🎨 Giao diện đẹp

-   **Preview ảnh**: Hiển thị ảnh đã chọn với thông tin file
-   **Loading state**: Hiển thị trạng thái đang phân tích
-   **Kết quả rõ ràng**: Hiển thị từ khóa và thông báo kết quả
-   **Responsive**: Hoạt động tốt trên mọi thiết bị

### 🔒 Bảo mật và hiệu suất

-   **Validate file**: Kiểm tra định dạng và kích thước
-   **Error handling**: Xử lý lỗi an toàn
-   **Rate limiting**: Theo giới hạn của Gemini API
-   **Logging**: Ghi log để debug

## 📋 Cài đặt

### 1. Lấy API Key Gemini

```bash
# Truy cập Google AI Studio
https://makersuite.google.com/app/apikey

# Tạo API key mới và sao chép
```

### 2. Cấu hình môi trường

```env
# Thêm vào file .env
GEMINI_API_KEY=your_gemini_api_key_here
```

### 3. Kiểm tra cài đặt

```bash
# Chạy test script
php test_gemini_image_search.php
```

## 🎮 Cách sử dụng

### Bước 1: Truy cập trang chủ

```
http://localhost:8000
```

### Bước 2: Upload ảnh

-   Click vào icon camera (📷) bên cạnh ô tìm kiếm
-   Chọn ảnh món ăn từ máy tính
-   Hỗ trợ: JPG, PNG, GIF, WebP (tối đa 5MB)

### Bước 3: Phân tích ảnh

-   Click nút "Phân tích ảnh"
-   Chờ AI xử lý (có loading indicator)
-   Xem kết quả phân tích

### Bước 4: Tìm kiếm

-   Hệ thống tự động tìm kiếm với từ khóa tốt nhất
-   Xem danh sách công thức phù hợp
-   Có thể click vào các từ khóa khác để tìm kiếm

## 🛠️ Cấu trúc code

### Services

```
app/Services/GeminiService.php
├── analyzeFoodImage() - Phân tích chi tiết ảnh món ăn
└── searchRecipesByImage() - Tìm kiếm công thức từ ảnh
```

### Livewire Components

```
app/Livewire/SearchWithImage.php
├── Upload và validate ảnh
├── Gọi Gemini API
├── Hiển thị kết quả
└── Tương tác với HomePage

app/Livewire/HomePage.php
├── Lắng nghe sự kiện search
└── Cập nhật danh sách công thức
```

### Views

```
resources/views/livewire/search-with-image.blade.php
├── UI upload ảnh
├── Preview ảnh
├── Loading states
└── Kết quả phân tích

resources/views/components/hero-section.blade.php
└── Tích hợp SearchWithImage component
```

## 🔧 Cấu hình

### File upload limits

```php
// app/Livewire/SearchWithImage.php
'searchImage' => 'nullable|image|max:5120', // 5MB max
```

### Gemini API settings

```php
// app/Services/GeminiService.php
'generationConfig' => [
    'temperature' => 0.1,  // Độ chính xác cao
    'topK' => 32,
    'topP' => 1,
    'maxOutputTokens' => 1024,
]
```

## 🎨 Customization

### Thay đổi style

```css
/* resources/css/app.css */
.search-image-upload {
    /* Custom styles */
}

.image-analysis-result {
    /* Custom styles */
}
```

### Thay đổi prompt

```php
// app/Services/GeminiService.php
'text' => 'Đây là ảnh món ăn. Hãy trả về danh sách các từ khóa tìm kiếm...'
```

## 🐛 Troubleshooting

### Lỗi "Không thể phân tích ảnh"

-   ✅ Kiểm tra API key có đúng không
-   ✅ Kiểm tra kết nối internet
-   ✅ Thử lại với ảnh khác
-   ✅ Xem log trong `storage/logs/laravel.log`

### Lỗi "File quá lớn"

-   ✅ Nén ảnh xuống dưới 5MB
-   ✅ Chuyển đổi sang định dạng JPG
-   ✅ Sử dụng ảnh có độ phân giải thấp hơn

### Lỗi "Không nhận diện được món ăn"

-   ✅ Sử dụng ảnh rõ nét, có món ăn ở trung tâm
-   ✅ Tránh ảnh có nhiều món ăn khác nhau
-   ✅ Đảm bảo ánh sáng đủ sáng
-   ✅ Tránh ảnh có text hoặc logo

## 📊 Monitoring

### Logs

```bash
# Xem log errors
tail -f storage/logs/laravel.log | grep "Gemini"

# Xem log requests
tail -f storage/logs/laravel.log | grep "Image analysis"
```

### Performance

-   Response time: ~2-5 giây
-   File size limit: 5MB
-   Supported formats: JPG, PNG, GIF, WebP

## 🔮 Roadmap

### Tính năng sắp tới

-   [ ] Lưu lịch sử tìm kiếm bằng ảnh
-   [ ] Gợi ý công thức tương tự
-   [ ] Phân tích dinh dưỡng từ ảnh
-   [ ] Nhận diện nguyên liệu
-   [ ] Đánh giá độ khó nấu

### Cải tiến

-   [ ] Cache kết quả phân tích
-   [ ] Batch processing cho nhiều ảnh
-   [ ] Offline mode với model local
-   [ ] Multi-language support

## 📞 Support

Nếu gặp vấn đề, vui lòng:

1. Kiểm tra file `README_GEMINI_SETUP.md`
2. Chạy `php test_gemini_image_search.php`
3. Xem log trong `storage/logs/laravel.log`
4. Tạo issue với thông tin chi tiết

---

**🎉 Chúc bạn sử dụng tính năng tìm kiếm bằng ảnh một cách hiệu quả!**
