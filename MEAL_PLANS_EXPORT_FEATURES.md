# Tính năng Xuất dữ liệu Kế hoạch Bữa ăn (Meal Plans)

## Tổng quan

Hệ thống đã được mở rộng với các tính năng xuất dữ liệu kế hoạch bữa ăn ra nhiều định dạng khác nhau, tương tự như tính năng export công thức nấu ăn.

## Các định dạng Export có sẵn

### 1. Excel Export (.xlsx)

-   **Tính năng**: Xuất kế hoạch bữa ăn ra định dạng Excel với định dạng đẹp
-   **Dữ liệu bao gồm**:
    -   Thông tin chi tiết từng bữa ăn theo ngày và loại bữa
    -   Thông tin công thức (tên, mô tả, calories, thời gian nấu, độ khó)
    -   Nguyên liệu và hướng dẫn nấu
    -   Thống kê tổng quan

### 2. CSV Export (.csv)

-   **Tính năng**: Xuất dữ liệu ra định dạng CSV đơn giản
-   **Ưu điểm**: Tương thích với nhiều ứng dụng, dễ import vào hệ thống khác
-   **Dữ liệu**: Tương tự Excel nhưng ở định dạng văn bản

### 3. PDF Export (.pdf)

-   **Tính năng**: Xuất ra định dạng PDF đẹp mắt, phù hợp để in ấn
-   **Đặc điểm**:
    -   Layout chuyên nghiệp với CSS styling
    -   Tự động phân trang
    -   Bao gồm thống kê và danh sách mua sắm
    -   Phù hợp để chia sẻ và lưu trữ

### 4. ZIP Export (.zip)

-   **Tính năng**: Xuất ra file ZIP chứa nhiều định dạng khác nhau
-   **Cấu trúc ZIP**:
    ```
    ke-hoach-bua-an-YYYY-MM-DD.zip
    ├── README.txt              # Hướng dẫn sử dụng
    ├── meal-plan.json          # Dữ liệu JSON đầy đủ
    ├── meal-plan.csv           # Dữ liệu CSV
    ├── statistics.txt          # Thống kê chi tiết
    ├── shopping-list.txt       # Danh sách mua sắm
    └── daily-meals/            # Thư mục chứa từng ngày
        ├── Thu-2-YYYY-MM-DD.txt
        ├── Thu-3-YYYY-MM-DD.txt
        └── ...
    ```

## Cách sử dụng

### Xuất một kế hoạch bữa ăn cụ thể

1. **Từ trang chi tiết kế hoạch**:

    - Vào trang chi tiết kế hoạch bữa ăn
    - Nhấn nút "Xuất dữ liệu" (dropdown)
    - Chọn định dạng mong muốn

2. **Từ trang danh sách kế hoạch**:
    - Trong bảng danh sách, nhấn nút export bên cạnh kế hoạch
    - Chọn định dạng từ dropdown menu

### Xuất tất cả kế hoạch bữa ăn

1. **Từ trang danh sách kế hoạch**:
    - Nhấn nút "Xuất dữ liệu" ở header
    - Chọn định dạng mong muốn

## Routes API

### Xuất một kế hoạch cụ thể

```
GET /meal-plans/{mealPlan}/export          # Excel
GET /meal-plans/{mealPlan}/export/csv      # CSV
GET /meal-plans/{mealPlan}/export/pdf      # PDF
GET /meal-plans/{mealPlan}/export/zip      # ZIP
```

### Xuất tất cả kế hoạch

```
GET /meal-plans/export/all                 # Excel
GET /meal-plans/export/all/csv             # CSV
GET /meal-plans/export/all/pdf             # PDF
GET /meal-plans/export/all/zip             # ZIP
```

## Dữ liệu được xuất

### Thông tin cơ bản kế hoạch

-   Tên kế hoạch
-   Tuần bắt đầu và kết thúc
-   Trạng thái (hoạt động/không hoạt động)
-   Tổng calories và chi phí
-   Thống kê hoàn thành

### Thông tin từng bữa ăn

-   Ngày và thứ trong tuần
-   Loại bữa ăn (sáng, trưa, tối, phụ)
-   Tên công thức
-   Mô tả và thông tin dinh dưỡng
-   Thời gian nấu và độ khó
-   Nguyên liệu và hướng dẫn nấu

### Thông tin bổ sung

-   Danh sách mua sắm tổng hợp
-   Thống kê chi tiết
-   Tính năng sử dụng (AI, thời tiết, v.v.)

## Tính năng đặc biệt

### 1. Thống kê tự động

-   Tổng số bữa ăn trong tuần
-   Số công thức duy nhất
-   % hoàn thành kế hoạch
-   Tổng calories và chi phí

### 2. Danh sách mua sắm thông minh

-   Tự động tổng hợp nguyên liệu từ tất cả công thức
-   Gộp các nguyên liệu trùng lặp
-   Hiển thị số lượng cần thiết
-   Ghi chú công thức sử dụng

### 3. Template tùy chỉnh (ZIP)

-   Hỗ trợ nhiều template khác nhau
-   Cấu trúc thư mục có tổ chức
-   File README hướng dẫn sử dụng

## Lưu ý kỹ thuật

### Bảo mật

-   Chỉ người dùng sở hữu kế hoạch mới có thể xuất
-   Kiểm tra quyền truy cập trước khi xuất
-   File tạm được xóa sau khi download

### Hiệu suất

-   Sử dụng queue cho các file lớn
-   Cache thống kê để tăng tốc độ
-   Tối ưu hóa query database

### Tương thích

-   Hỗ trợ Unicode cho tiếng Việt
-   Tương thích với các ứng dụng phổ biến
-   Định dạng file chuẩn

## Ví dụ sử dụng

### Xuất PDF cho in ấn

```php
// Trong controller
public function exportMealPlanPdf(WeeklyMealPlan $mealPlan)
{
    $pdfExport = new MealPlansPdfExport(null, $mealPlan);
    $pdfContent = $pdfExport->export();

    return response($pdfContent)
        ->header('Content-Type', 'application/pdf')
        ->header('Content-Disposition', 'attachment; filename="meal-plan.pdf"');
}
```

### Xuất ZIP với template

```php
// Trong controller
public function exportMealPlanZip(WeeklyMealPlan $mealPlan, Request $request)
{
    $template = $request->get('template', 'default');
    $zipExport = new MealPlansZipExport(null, $mealPlan, $template);
    $zipPath = $zipExport->export();

    return response()->download($zipPath, 'meal-plan.zip')->deleteFileAfterSend();
}
```

## Kết luận

Tính năng export meal plans đã được triển khai đầy đủ với 4 định dạng chính, cung cấp tính linh hoạt cao cho người dùng trong việc xuất và chia sẻ dữ liệu kế hoạch bữa ăn của họ.
