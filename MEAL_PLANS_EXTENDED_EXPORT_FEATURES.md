# Tính năng Xuất dữ liệu Kế hoạch Bữa ăn - Phiên bản Mở rộng

## Tổng quan

Hệ thống đã được mở rộng với **7 định dạng export** khác nhau cho kế hoạch bữa ăn, cung cấp tính linh hoạt cao cho người dùng trong việc xuất và chia sẻ dữ liệu.

## Các định dạng Export có sẵn

### 1. Excel Export (.xlsx) - Định dạng Bảng tính

-   **Tính năng**: Xuất kế hoạch bữa ăn ra định dạng Excel với định dạng đẹp
-   **Dữ liệu bao gồm**: Thông tin chi tiết từng bữa ăn, công thức, nguyên liệu, hướng dẫn nấu
-   **Ưu điểm**: Dễ chỉnh sửa, phân tích dữ liệu, tương thích với các ứng dụng văn phòng

### 2. CSV Export (.csv) - Định dạng Văn bản

-   **Tính năng**: Xuất dữ liệu ra định dạng CSV đơn giản
-   **Ưu điểm**: Tương thích với nhiều ứng dụng, dễ import vào hệ thống khác
-   **Dữ liệu**: Tương tự Excel nhưng ở định dạng văn bản thuần túy

### 3. PDF Export (.pdf) - Định dạng Tài liệu

-   **Tính năng**: Xuất ra định dạng PDF đẹp mắt, phù hợp để in ấn
-   **Đặc điểm**: Layout chuyên nghiệp với CSS styling, tự động phân trang
-   **Phù hợp**: Chia sẻ, lưu trữ, in ấn

### 4. ZIP Export (.zip) - Định dạng Nén

-   **Tính năng**: Xuất ra file ZIP chứa nhiều định dạng khác nhau
-   **Cấu trúc**: Bao gồm JSON, CSV, thống kê, danh sách mua sắm, file từng ngày
-   **Ưu điểm**: Tất cả dữ liệu trong một file, dễ chia sẻ

### 5. XML Export (.xml) - Định dạng Cấu trúc

-   **Tính năng**: Xuất dữ liệu ra định dạng XML có cấu trúc
-   **Đặc điểm**: Dữ liệu được tổ chức theo cấu trúc phân cấp rõ ràng
-   **Phù hợp**: Tích hợp với hệ thống khác, xử lý dữ liệu tự động
-   **Cấu trúc XML**:

```xml
<?xml version="1.0" encoding="UTF-8"?>
<meal_plan>
    <id>1</id>
    <name>Kế hoạch tuần 1</name>
    <week_start>2024-01-01</week_start>
    <statistics>
        <total_meals>21</total_meals>
        <total_calories>15000</total_calories>
    </statistics>
    <weekly_meals>
        <day key="monday" label="Thứ 2">
            <meals>
                <meal_type type="breakfast">
                    <recipes>...</recipes>
                </meal_type>
            </meals>
        </day>
    </weekly_meals>
</meal_plan>
```

### 6. Markdown Export (.md) - Định dạng Văn bản có định dạng

-   **Tính năng**: Xuất ra định dạng Markdown với emoji và bảng
-   **Đặc điểm**: Dễ đọc, có thể chuyển đổi thành HTML, PDF
-   **Phù hợp**: Tài liệu kỹ thuật, chia sẻ trên GitHub, blog
-   **Ví dụ nội dung**:

```markdown
# Kế hoạch Bữa ăn: Tuần 1

**Tuần:** 01/01/2024 - 07/01/2024
**Trạng thái:** Hoạt động

## 📊 Thống kê Tổng quan

| Chỉ số                | Giá trị |
| --------------------- | ------- |
| Tổng số bữa ăn        | 21      |
| Số công thức duy nhất | 15      |
| % Hoàn thành          | 85%     |

## 🍽️ Kế hoạch Bữa ăn Theo Ngày

### Thứ 2 - 01/01/2024

#### Bữa sáng

**Phở bò**

> Món phở truyền thống Việt Nam

**Thông tin:**

-   🕒 Thời gian nấu: 45 phút
-   ⭐ Độ khó: 3/5
-   👥 Khẩu phần: 4 người
-   🔥 Calories: 450 kcal
```

### 7. JSON Export (.json) - Định dạng Dữ liệu Cấu trúc

-   **Tính năng**: Xuất dữ liệu ra định dạng JSON với nhiều biến thể
-   **Các định dạng JSON**:
    -   **Detailed**: Dữ liệu chi tiết đầy đủ
    -   **Summary**: Tóm tắt thông tin cơ bản
    -   **Calendar**: Cấu trúc theo lịch
    -   **Nutrition**: Tập trung vào thông tin dinh dưỡng
-   **Phù hợp**: API, tích hợp ứng dụng, xử lý dữ liệu
-   **Ví dụ cấu trúc**:

```json
{
  "metadata": {
    "export_type": "detailed_meal_plan",
    "exported_at": "2024-01-01T10:00:00Z",
    "version": "1.0"
  },
  "meal_plan": {
    "id": 1,
    "name": "Kế hoạch tuần 1",
    "week_start": "2024-01-01T00:00:00Z",
    "is_active": true
  },
  "statistics": {
    "total_meals": 21,
    "unique_recipes": 15,
    "completion_rate": 85,
    "total_calories": 15000
  },
  "weekly_schedule": [
    {
      "date": "2024-01-01T00:00:00Z",
      "day_label": "Thứ 2",
      "meals": {
        "breakfast": {
          "label": "Bữa sáng",
          "recipes": [...]
        }
      }
    }
  ]
}
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

### Xuất JSON với định dạng tùy chỉnh

```php
// Xuất định dạng calendar
GET /meal-plans/{mealPlan}/export/json?format=calendar

// Xuất định dạng nutrition
GET /meal-plans/{mealPlan}/export/json?format=nutrition

// Xuất định dạng summary
GET /meal-plans/{mealPlan}/export/json?format=summary
```

## Routes API

### Xuất một kế hoạch cụ thể

```
GET /meal-plans/{mealPlan}/export          # Excel
GET /meal-plans/{mealPlan}/export/csv      # CSV
GET /meal-plans/{mealPlan}/export/pdf      # PDF
GET /meal-plans/{mealPlan}/export/zip      # ZIP
GET /meal-plans/{mealPlan}/export/xml      # XML
GET /meal-plans/{mealPlan}/export/markdown # Markdown
GET /meal-plans/{mealPlan}/export/json     # JSON (detailed)
GET /meal-plans/{mealPlan}/export/json?format=calendar   # JSON calendar
GET /meal-plans/{mealPlan}/export/json?format=nutrition  # JSON nutrition
GET /meal-plans/{mealPlan}/export/json?format=summary    # JSON summary
```

### Xuất tất cả kế hoạch

```
GET /meal-plans/export/all                 # Excel
GET /meal-plans/export/all/csv             # CSV
GET /meal-plans/export/all/pdf             # PDF
GET /meal-plans/export/all/zip             # ZIP
GET /meal-plans/export/all/xml             # XML
GET /meal-plans/export/all/markdown        # Markdown
GET /meal-plans/export/all/json            # JSON
```

## Tính năng đặc biệt

### 1. Định dạng JSON linh hoạt

-   **Detailed**: Dữ liệu đầy đủ với metadata
-   **Summary**: Tóm tắt thông tin cơ bản
-   **Calendar**: Cấu trúc theo ngày và bữa ăn
-   **Nutrition**: Tập trung vào calories và dinh dưỡng

### 2. Markdown với emoji

-   Sử dụng emoji để làm cho tài liệu sinh động
-   Bảng được format đẹp
-   Dễ đọc và chia sẻ

### 3. XML có cấu trúc

-   Dữ liệu được tổ chức theo cấu trúc phân cấp
-   Thuộc tính và phần tử được định nghĩa rõ ràng
-   Phù hợp cho tích hợp hệ thống

### 4. Tương thích đa nền tảng

-   Tất cả định dạng đều hỗ trợ Unicode (tiếng Việt)
-   Tương thích với các ứng dụng phổ biến
-   File size được tối ưu hóa

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

### Xuất Markdown cho tài liệu

```php
$markdownExport = new MealPlansMarkdownExport(null, $mealPlan);
$content = $markdownExport->export();
// Lưu vào file .md hoặc hiển thị
```

### Xuất JSON cho API

```php
$jsonExport = new MealPlansJsonExport(null, $mealPlan, 'calendar');
$jsonData = $jsonExport->export();
// Trả về cho frontend hoặc API consumer
```

### Xuất XML cho tích hợp

```php
$xmlExport = new MealPlansXmlExport(null, $mealPlan);
$xmlContent = $xmlExport->export();
// Gửi đến hệ thống khác
```

## Kết luận

Với 7 định dạng export khác nhau, hệ thống cung cấp tính linh hoạt cao cho người dùng:

-   **Excel/CSV**: Cho phân tích dữ liệu và chỉnh sửa
-   **PDF**: Cho in ấn và chia sẻ chính thức
-   **ZIP**: Cho backup và chia sẻ đầy đủ
-   **XML**: Cho tích hợp hệ thống
-   **Markdown**: Cho tài liệu kỹ thuật
-   **JSON**: Cho API và xử lý dữ liệu

Mỗi định dạng có ưu điểm riêng, phù hợp với nhu cầu sử dụng khác nhau của người dùng.
