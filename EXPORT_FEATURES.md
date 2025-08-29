# Hướng dẫn sử dụng chức năng Export

## Tổng quan

Hệ thống đã được tích hợp chức năng xuất dữ liệu công thức nấu ăn ra nhiều định dạng khác nhau:

-   **Excel (.xlsx)**: Định dạng bảng tính với định dạng đẹp
-   **CSV (.csv)**: Định dạng văn bản đơn giản
-   **ZIP với template**: File nén chứa nhiều định dạng khác nhau
-   **PDF (.pdf)**: Định dạng tài liệu in ấn

## Các định dạng Export

### 1. Excel Export (.xlsx)

**Tính năng:**

-   Xuất toàn bộ thông tin công thức
-   Định dạng bảng đẹp với màu sắc
-   Phân loại theo trạng thái (đã duyệt, chờ duyệt, bị từ chối)
-   Tự động điều chỉnh độ rộng cột
-   Wrap text cho nội dung dài

**Dữ liệu bao gồm:**

-   Thông tin cơ bản (ID, tên, mô tả, tóm tắt)
-   Thông tin nấu nướng (thời gian, độ khó, khẩu phần, calories)
-   Nguyên liệu (được format đẹp)
-   Hướng dẫn nấu (từng bước)
-   Thông tin bổ sung (mẹo, ghi chú)
-   Thông tin phân loại (danh mục, tags)
-   Thống kê (lượt xem, yêu thích, đánh giá)
-   Thông tin tác giả và ngày tạo

### 2. CSV Export (.csv)

**Tính năng:**

-   Định dạng văn bản đơn giản
-   Tương thích với nhiều ứng dụng
-   Dễ dàng import vào các hệ thống khác

**Dữ liệu:** Tương tự Excel nhưng ở định dạng CSV

### 3. ZIP Export với Template

**Tính năng:**

-   Tạo file ZIP chứa nhiều định dạng
-   Hỗ trợ 3 loại template:
    -   **Default**: File text đơn giản
    -   **HTML**: Template HTML đẹp mắt
    -   **Markdown**: Định dạng Markdown

**Cấu trúc ZIP:**

```
recipes_YYYY-MM-DD_HH-mm-ss.zip
├── README.txt              # Hướng dẫn sử dụng
├── recipes.json            # Dữ liệu JSON tổng hợp
├── recipes.csv             # Dữ liệu CSV
├── statistics.txt          # Thống kê chi tiết
└── recipes/                # Thư mục chứa từng công thức
    ├── Ten_Cong_Thuc_1/
    │   ├── info.txt        # Thông tin chính
    │   ├── ingredients.txt # Danh sách nguyên liệu
    │   ├── instructions.txt # Hướng dẫn nấu
    │   ├── recipe.json     # Dữ liệu JSON
    │   ├── recipe.html     # Template HTML (nếu chọn)
    │   └── recipe.md       # Template Markdown (nếu chọn)
    └── Ten_Cong_Thuc_2/
        └── ...
```

### 4. PDF Export (.pdf)

**Tính năng:**

-   Định dạng tài liệu in ấn chuyên nghiệp
-   Layout đẹp với màu sắc và typography
-   Tự động phân trang
-   Phù hợp để in ấn hoặc chia sẻ

## Cách sử dụng

### 1. Export tất cả công thức

1. Truy cập trang **Công thức nấu ăn** (`/recipes`)
2. Sử dụng bộ lọc để lọc công thức (tùy chọn)
3. Click nút **"Xuất dữ liệu"** ở góc phải
4. Chọn định dạng mong muốn:
    - **Excel**: Tải ngay file .xlsx
    - **CSV**: Tải ngay file .csv
    - **ZIP với template**: Mở modal chọn template
    - **PDF**: Tải ngay file .pdf

### 2. Export công thức của tôi

1. Truy cập trang **Công thức của tôi** (`/my-recipes`)
2. Click nút **"Xuất công thức"** ở góc phải
3. Chọn định dạng:
    - **Excel**: Xuất công thức của bạn
    - **ZIP với template**: Xuất với template tùy chỉnh

### 3. Chọn template cho ZIP

Khi chọn **ZIP với template**:

1. Modal sẽ hiện ra với 3 lựa chọn:
    - **Template mặc định**: File text đơn giản
    - **Template HTML**: Giao diện web đẹp mắt
    - **Template Markdown**: Định dạng Markdown
2. Chọn template mong muốn
3. Click **"Xuất ZIP"** để tải file

## Bộ lọc và Export

Khi sử dụng bộ lọc trên trang công thức, các filter sẽ được áp dụng vào file export:

-   **Danh mục**: Chỉ export công thức trong danh mục đã chọn
-   **Độ khó**: Chỉ export công thức có độ khó tương ứng
-   **Tìm kiếm**: Chỉ export công thức khớp với từ khóa
-   **Trạng thái**: Chỉ export công thức có trạng thái tương ứng

## Lưu ý kỹ thuật

### Yêu cầu hệ thống

-   **PHP**: >= 8.1
-   **Laravel**: >= 10.0
-   **Maatwebsite/Excel**: Đã cài đặt
-   **DomPDF**: Đã cài đặt
-   **ZipArchive**: Extension PHP

### Cấu hình

-   File config: `config/excel.php` (tự động tạo)
-   File config: `config/dompdf.php` (tự động tạo)
-   Thư mục temp: `storage/app/temp/` (tự động tạo)

### Bảo mật

-   Chỉ user đã đăng nhập mới có thể export
-   Export công thức cá nhân chỉ xuất công thức của user đó
-   Các file tạm được xóa sau khi download

## Troubleshooting

### Lỗi thường gặp

1. **Lỗi "Class not found"**

    - Chạy: `composer dump-autoload`
    - Kiểm tra namespace trong các file Export

2. **Lỗi "Permission denied"**

    - Kiểm tra quyền ghi thư mục `storage/app/temp/`
    - Chạy: `chmod -R 755 storage/`

3. **Lỗi "Memory limit exceeded"**

    - Tăng memory_limit trong php.ini
    - Hoặc giảm số lượng công thức export

4. **File ZIP bị lỗi**
    - Kiểm tra extension ZipArchive
    - Chạy: `php -m | grep zip`

### Tối ưu hiệu suất

-   Sử dụng pagination cho danh sách lớn
-   Chỉ export dữ liệu cần thiết
-   Sử dụng queue cho export lớn (tùy chọn)

## Tùy chỉnh

### Thêm định dạng mới

1. Tạo class Export mới trong `app/Exports/`
2. Implement các interface cần thiết
3. Thêm method trong Controller
4. Thêm route mới
5. Cập nhật UI

### Tùy chỉnh template

1. Sửa method `generateHtmlTemplate()` trong `RecipesZipExport`
2. Hoặc tạo template riêng trong `resources/views/exports/`
3. Cập nhật logic trong Export class

### Tùy chỉnh style

1. Sửa CSS trong các method generate
2. Hoặc tạo file CSS riêng
3. Cập nhật config DomPDF nếu cần

