# 🚀 Hướng dẫn nhanh - Tính năng Tìm kiếm bằng Ảnh

## ✅ Đã hoàn thành

-   ✅ Database đã được tạo và có dữ liệu mẫu
-   ✅ Server đang chạy tại: http://localhost:8000
-   ✅ Tính năng tìm kiếm bằng ảnh đã được tích hợp

## 🔧 Cấu hình API Key (Bắt buộc)

### Bước 1: Lấy API Key Gemini

1. Truy cập: https://makersuite.google.com/app/apikey
2. Đăng nhập bằng Google
3. Tạo API key mới
4. Sao chép API key

### Bước 2: Thêm vào file .env

```env
GEMINI_API_KEY=your_actual_api_key_here
```

### Bước 3: Kiểm tra cấu hình

```bash
php test_gemini_image_search.php
```

## 🎮 Test tính năng

### Truy cập trang chủ

```
http://localhost:8000
```

### Sử dụng tính năng tìm kiếm bằng ảnh

1. **Tìm icon camera** 📷 bên cạnh ô tìm kiếm lớn
2. **Click vào icon** để mở file picker
3. **Chọn ảnh món ăn** (JPG, PNG, GIF, WebP, tối đa 5MB)
4. **Click "Phân tích ảnh"** để AI xử lý
5. **Xem kết quả** và danh sách từ khóa
6. **Hệ thống tự động tìm kiếm** với từ khóa tốt nhất

## 📸 Ảnh test gợi ý

-   Ảnh phở bò
-   Ảnh bánh mì
-   Ảnh cơm tấm
-   Ảnh bún chả
-   Ảnh gỏi cuốn

## 🔍 Tính năng đã có

-   ✅ Upload ảnh với preview
-   ✅ Validate file (kích thước, định dạng)
-   ✅ Loading state khi phân tích
-   ✅ Hiển thị kết quả với từ khóa
-   ✅ Tự động tìm kiếm công thức
-   ✅ Giao diện responsive
-   ✅ Error handling

## 🐛 Nếu gặp lỗi

### Lỗi "Chưa cấu hình API key"

-   Kiểm tra file `.env` có `GEMINI_API_KEY=...`
-   Restart server: `php artisan serve`

### Lỗi "Không thể phân tích ảnh"

-   Kiểm tra kết nối internet
-   Thử ảnh khác
-   Xem log: `tail -f storage/logs/laravel.log`

### Lỗi "File quá lớn"

-   Nén ảnh xuống dưới 5MB
-   Chuyển sang định dạng JPG

## 📱 Tài khoản test

```
Admin: admin@beefood.com / password
User: user1@beefood.com / password
```

## 🎯 Kết quả mong đợi

-   AI sẽ phân tích ảnh và trả về từ khóa như: "phở bò", "bánh mì", "cơm tấm"
-   Hệ thống tự động tìm kiếm công thức phù hợp
-   Hiển thị danh sách công thức có liên quan

---

**🎉 Chúc bạn test tính năng thành công!**
