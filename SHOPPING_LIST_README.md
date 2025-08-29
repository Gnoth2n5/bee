# Shopping List Feature - BeeFood

## Tổng quan

Shopping List là tính năng cho phép người dùng tạo và quản lý danh sách mua sắm từ các recipe và meal plan. Tính năng này giúp người dùng:

-   Tạo shopping list từ recipe hoặc meal plan
-   Quản lý danh sách mua sắm theo danh mục
-   Check/uncheck các items
-   Tích hợp với hệ thống recipe và meal planning

## Cấu trúc Database

### Bảng `shopping_lists`

-   `id` - Primary key
-   `user_id` - Foreign key đến users
-   `name` - Tên shopping list
-   `description` - Mô tả
-   `is_active` - Trạng thái hoạt động
-   `is_shared` - Có thể chia sẻ
-   `completed_at` - Thời gian hoàn thành
-   `timestamps` - Created/updated timestamps

### Bảng `shopping_list_items`

-   `id` - Primary key
-   `shopping_list_id` - Foreign key đến shopping_lists
-   `ingredient_name` - Tên nguyên liệu
-   `amount` - Số lượng
-   `unit` - Đơn vị
-   `notes` - Ghi chú
-   `is_checked` - Đã check chưa
-   `sort_order` - Thứ tự sắp xếp
-   `category` - Danh mục (Rau củ, Thịt cá, etc.)
-   `recipe_id` - Foreign key đến recipes (optional)
-   `weekly_meal_plan_id` - Foreign key đến weekly_meal_plans (optional)
-   `timestamps` - Created/updated timestamps

## Models

### ShoppingList

-   Relationships với User và ShoppingListItem
-   Methods: `isCompleted()`, `markAsCompleted()`, `markAsActive()`
-   Scopes: `active()`, `completed()`, `incomplete()`, `byUser()`
-   Attributes: `total_items_count`, `checked_items_count`, `unchecked_items_count`, `completion_percentage`

### ShoppingListItem

-   Relationships với ShoppingList, Recipe, WeeklyMealPlan
-   Methods: `toggleChecked()`, `markAsChecked()`, `markAsUnchecked()`
-   Scopes: `checked()`, `unchecked()`, `byCategory()`, `byRecipe()`, `byMealPlan()`
-   Attributes: `formatted_amount`, `display_name`

## Services

### ShoppingListService

Các methods chính:

-   `createShoppingList()` - Tạo shopping list mới
-   `generateFromMealPlan()` - Tạo từ meal plan
-   `generateFromRecipe()` - Tạo từ recipe
-   `addItemToShoppingList()` - Thêm item
-   `aggregateIngredients()` - Gộp ingredients tương tự
-   `categorizeIngredient()` - Phân loại ingredient
-   `toggleItemChecked()` - Toggle trạng thái check
-   `clearCheckedItems()` - Xóa items đã check
-   `markShoppingListCompleted()` - Đánh dấu hoàn thành

## Livewire Components

### ShoppingListManager

Component chính để quản lý shopping list:

-   Hiển thị danh sách shopping lists
-   Tạo shopping list mới
-   Thêm items
-   Check/uncheck items
-   Xóa items
-   Tạo từ meal plan/recipe

### AddToShoppingList

Component nhỏ để thêm recipe vào shopping list:

-   Chọn shopping list có sẵn
-   Tạo shopping list mới
-   Thêm ingredients từ recipe

## API Endpoints

### Shopping Lists

-   `GET /api/shopping-lists` - Lấy danh sách
-   `POST /api/shopping-lists` - Tạo mới
-   `GET /api/shopping-lists/{id}` - Chi tiết
-   `PUT /api/shopping-lists/{id}` - Cập nhật
-   `DELETE /api/shopping-lists/{id}` - Xóa

### Items

-   `POST /api/shopping-lists/{id}/items` - Thêm item
-   `PUT /api/shopping-lists/{id}/items/{item}` - Cập nhật item
-   `DELETE /api/shopping-lists/{id}/items/{item}` - Xóa item
-   `PATCH /api/shopping-lists/{id}/items/{item}/toggle` - Toggle check

### Actions

-   `DELETE /api/shopping-lists/{id}/clear-checked` - Xóa items đã check
-   `PATCH /api/shopping-lists/{id}/mark-completed` - Đánh dấu hoàn thành
-   `GET /api/shopping-lists/{id}/categories` - Lấy theo danh mục

### Generate

-   `POST /api/shopping-lists/generate/meal-plan` - Tạo từ meal plan
-   `POST /api/shopping-lists/generate/recipe` - Tạo từ recipe

## Routes

### Web Routes

-   `GET /shopping-lists` - Trang quản lý shopping list (Livewire)

### API Routes

-   Tất cả API endpoints được bảo vệ bởi `auth:sanctum` middleware

## Tính năng chính

### 1. Tạo Shopping List

-   Tạo thủ công
-   Tạo từ recipe
-   Tạo từ meal plan

### 2. Quản lý Items

-   Thêm/sửa/xóa items
-   Check/uncheck items
-   Phân loại theo danh mục
-   Sắp xếp theo thứ tự

### 3. Aggregation

-   Tự động gộp ingredients tương tự
-   Tính tổng số lượng
-   Phân loại thông minh

### 4. Categories

-   Rau củ
-   Thịt cá
-   Sữa và bơ sữa
-   Ngũ cốc
-   Gia vị
-   Trái cây
-   Trứng
-   Khác

### 5. Integration

-   Tích hợp với Recipe system
-   Tích hợp với Meal Planning
-   Tích hợp với User system

## Cách sử dụng

### 1. Truy cập Shopping List

-   Đăng nhập vào hệ thống
-   Click vào "Shopping List" trong navigation
-   Hoặc truy cập `/shopping-lists`

### 2. Tạo Shopping List mới

-   Click "Tạo mới" trong sidebar
-   Nhập tên và mô tả
-   Click "Tạo"

### 3. Thêm Items

-   Chọn shopping list
-   Nhập tên nguyên liệu, số lượng, đơn vị
-   Chọn danh mục (optional)
-   Click "Thêm"

### 4. Tạo từ Recipe

-   Vào trang chi tiết recipe
-   Sử dụng component "Thêm vào Shopping List"
-   Chọn shopping list có sẵn hoặc tạo mới

### 5. Tạo từ Meal Plan

-   Vào trang quản lý shopping list
-   Chọn "Từ Meal Plan"
-   Chọn meal plan muốn tạo shopping list

### 6. Quản lý Items

-   Check/uncheck items
-   Xóa items không cần
-   Xóa tất cả items đã check
-   Đánh dấu shopping list hoàn thành

## Lưu ý kỹ thuật

### Performance

-   Sử dụng eager loading cho relationships
-   Index trên các cột thường query
-   Pagination cho danh sách lớn

### Security

-   Kiểm tra ownership cho tất cả operations
-   Validation input data
-   CSRF protection cho web routes
-   API authentication

### Scalability

-   Có thể mở rộng thêm features:
    -   Sharing shopping lists
    -   Collaborative shopping
    -   Mobile app integration
    -   Export/import functionality
    -   Barcode scanning
    -   Price tracking

## Troubleshooting

### Lỗi thường gặp

1. **Shopping list không hiển thị**: Kiểm tra user authentication
2. **Không thể thêm items**: Kiểm tra shopping list ownership
3. **Ingredients không được gộp**: Kiểm tra format của recipe ingredients

### Debug

-   Kiểm tra logs trong `storage/logs/laravel.log`
-   Sử dụng Laravel Telescope (nếu có)
-   Kiểm tra database relationships

## Future Enhancements

1. **Sharing**: Chia sẻ shopping list với family/friends
2. **Collaboration**: Nhiều người cùng edit
3. **Mobile App**: Native mobile app
4. **Barcode**: Scan barcode để thêm items
5. **Price Tracking**: Theo dõi giá cả
6. **Export**: Export ra PDF, CSV
7. **Notifications**: Reminder khi shopping
8. **AI Suggestions**: Gợi ý items dựa trên history
