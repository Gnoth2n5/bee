# Cải tiến Admin Filament Recipe

## Tổng quan

Đã chỉnh sửa phần admin Filament recipe để tận dụng RecipeService và xử lý đúng cấu trúc dữ liệu đặc biệt của công thức nấu ăn.

## Những thay đổi chính

### 1. RecipeResource.php

-   **Tận dụng RecipeService**: Sử dụng `RecipeService` cho các thao tác CRUD thay vì thao tác trực tiếp với model
-   **Form cải tiến**:
    -   Thay đổi `ingredients` và `instructions` từ RichEditor sang Repeater để phù hợp với cấu trúc array
    -   Thêm validation và placeholder cho các trường
    -   Cải thiện UX với collapsible sections và cloneable items
-   **Bảng cải tiến**:
    -   Thêm cột danh mục và thời gian nấu
    -   Cải thiện hiển thị với badges và formatting
    -   Thêm các actions: Phê duyệt, Từ chối, Xuất bản
    -   Thêm bulk actions cho quản lý hàng loạt
-   **Filters**: Thêm filters cho trạng thái, độ khó, danh mục và ngày tạo

### 2. CreateRecipe.php

-   **Xử lý dữ liệu**: Thêm logic xử lý `ingredients` và `instructions` trước khi tạo
-   **Tận dụng Service**: Sử dụng `RecipeService::create()` thay vì tạo trực tiếp
-   **Validation**: Đảm bảo dữ liệu đúng format trước khi lưu

### 3. EditRecipe.php

-   **Xử lý dữ liệu**: Thêm logic chuyển đổi dữ liệu từ relationship sang array IDs
-   **Tận dụng Service**: Sử dụng `RecipeService::update()` và `RecipeService::delete()`
-   **Validation**: Đảm bảo dữ liệu đúng format trước khi cập nhật

### 4. ViewRecipe.php (Mới)

-   **Trang xem chi tiết**: Tạo trang view để hiển thị đầy đủ thông tin công thức
-   **Infolist**: Sử dụng Infolist để hiển thị dữ liệu một cách có cấu trúc
-   **Hiển thị dữ liệu**: Hiển thị ingredients và instructions dưới dạng danh sách có cấu trúc

### 5. RecipeService.php

-   **Method mới**: Thêm `prepareRecipeData()` để xử lý dữ liệu trước khi lưu
-   **Xử lý array**: Cải thiện xử lý `ingredients` và `instructions` dạng array
-   **Validation**: Đảm bảo dữ liệu đúng format và loại bỏ các item rỗng

## Cấu trúc dữ liệu được hỗ trợ

### Ingredients (Array)

```php
[
    [
        'name' => 'Thịt bò',
        'amount' => '500',
        'unit' => 'g'
    ],
    [
        'name' => 'Gạo',
        'amount' => '200',
        'unit' => 'g'
    ]
]
```

### Instructions (Array)

```php
[
    [
        'step' => 1,
        'instruction' => 'Nướng gừng và hành tây cho thơm'
    ],
    [
        'step' => 2,
        'instruction' => 'Luộc xương bò với nước lạnh'
    ]
]
```

## Tính năng mới

### Actions

-   **Phê duyệt**: Sử dụng `RecipeService::approve()`
-   **Từ chối**: Sử dụng `RecipeService::reject()` với lý do
-   **Xuất bản**: Chuyển trạng thái sang published
-   **Xem chi tiết**: Trang view với infolist đầy đủ

### Bulk Actions

-   **Phê duyệt hàng loạt**: Phê duyệt nhiều công thức cùng lúc
-   **Từ chối hàng loạt**: Từ chối nhiều công thức với lý do chung
-   **Xuất bản hàng loạt**: Xuất bản nhiều công thức đã được phê duyệt

### Filters

-   **Trạng thái**: Lọc theo trạng thái công thức
-   **Độ khó**: Lọc theo độ khó nấu
-   **Danh mục**: Lọc theo danh mục
-   **Ngày tạo**: Lọc theo khoảng thời gian tạo

## Lợi ích

1. **Tận dụng Service**: Đảm bảo logic nghiệp vụ được thực hiện đúng cách
2. **Cấu trúc dữ liệu**: Hỗ trợ đúng cấu trúc array cho ingredients và instructions
3. **UX tốt hơn**: Giao diện thân thiện với người dùng
4. **Quản lý hiệu quả**: Các tính năng quản lý hàng loạt và filters
5. **Validation**: Đảm bảo dữ liệu đúng format trước khi lưu
6. **Hiển thị chi tiết**: Trang view với thông tin đầy đủ

## Hướng dẫn sử dụng

1. **Tạo công thức mới**: Sử dụng form với Repeater cho ingredients và instructions
2. **Chỉnh sửa**: Form tự động chuyển đổi dữ liệu từ relationship sang array
3. **Phê duyệt**: Sử dụng action "Phê duyệt" hoặc bulk action
4. **Xem chi tiết**: Click vào action "Xem" để xem thông tin đầy đủ
5. **Quản lý hàng loạt**: Chọn nhiều công thức và sử dụng bulk actions
