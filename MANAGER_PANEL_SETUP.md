# Thiết lập Manager Panel cho BeeFood

## Tổng quan

Đã tạo thành công một panel riêng cho Manager tại đường dẫn `/manager` với đầy đủ phân quyền và tính năng theo yêu cầu.

## Các tính năng đã triển khai

### 1. Manager Panel (`/manager`)

-   **URL**: `https://your-domain.com/manager`
-   **Giao diện**: Logo và tiêu đề riêng "BeeFood Manager"
-   **Màu sắc**: Sử dụng brand colors của BeeFood (cam/orange)
-   **Middleware**: `ManagerAccess` - chỉ cho phép role `manager`

### 2. Quản lý công thức (Recipe Management)

**Đường dẫn**: `/manager/recipes`

**Tính năng chính**:

-   ✅ Xem danh sách công thức với bộ lọc theo trạng thái
-   ✅ **Action duyệt**: Phê duyệt công thức (chờ duyệt → đã duyệt)
-   ✅ **Action từ chối**: Từ chối công thức với lý do bắt buộc
-   ✅ Đặt lịch phê duyệt tự động
-   ✅ Bulk actions: Duyệt/từ chối hàng loạt
-   ✅ Hiển thị badge số lượng công thức chờ duyệt
-   ✅ Tự động refresh mỗi 30 giây

### 3. Quản lý bài viết (Post Management)

**Đường dẫn**: `/manager/posts`

**Tính năng CRUD đầy đủ**:

-   ✅ Tạo bài viết mới
-   ✅ Chỉnh sửa bài viết
-   ✅ Xóa bài viết (soft delete)
-   ✅ Xuất bản bài viết
-   ✅ Lưu trữ bài viết
-   ✅ Bulk actions: Xuất bản/lưu trữ hàng loạt
-   ✅ Rich text editor với đầy đủ formatting

### 4. Dashboard riêng cho Manager

**Đường dẫn**: `/manager`

**Widgets hiển thị**:

-   📊 **Thống kê tổng quan**: Số công thức chờ duyệt, đã duyệt, bài viết
-   📋 **Công thức chờ duyệt**: Bảng với quick actions
-   📄 **Bài viết gần đây**: Theo dõi hoạt động bài viết
-   📈 **Biểu đồ**: Phân bố trạng thái công thức/bài viết

**Quick Actions**:

-   🔗 Liên kết nhanh đến công thức chờ duyệt
-   ➕ Tạo công thức/bài viết mới
-   📊 Quản lý nội dung

## Phân quyền và bảo mật

### Role Manager có quyền:

-   ✅ `recipe.approve` - Duyệt công thức
-   ✅ `recipe.reject` - Từ chối công thức
-   ✅ `article.create` - CRUD bài viết
-   ✅ `article.edit` - Chỉnh sửa bài viết
-   ✅ `article.publish` - Xuất bản bài viết

### Middleware phân quyền:

-   `ManagerAccess`: Chỉ cho phép role `manager` truy cập `/manager`
-   `AdminAccess`: Cập nhật để tự động chuyển hướng Manager về panel riêng
-   Admin vẫn có thể truy cập `/admin` như cũ

## Cấu trúc files đã tạo

### Panel Provider

```
app/Providers/Filament/ManagerPanelProvider.php
```

### Middleware

```
app/Http/Middleware/ManagerAccess.php
```

### Resources cho Manager

```
app/Filament/ManagerResources/
├── RecipeResource.php
├── PostResource.php
├── RecipeResource/Pages/
│   ├── ListRecipes.php
│   ├── CreateRecipe.php
│   ├── ViewRecipe.php
│   └── EditRecipe.php
└── PostResource/Pages/
    ├── ListPosts.php
    ├── CreatePost.php
    ├── ViewPost.php
    └── EditPost.php
```

### Dashboard và Widgets

```
app/Filament/ManagerPages/
└── ManagerDashboard.php

app/Filament/ManagerWidgets/
├── ManagerStatsOverview.php
├── PendingRecipes.php
├── RecentPosts.php
├── RecipeStatsWidget.php
└── PostStatsWidget.php
```

### Views

```
resources/views/filament/manager/pages/
└── dashboard.blade.php
```

## Cách sử dụng

### Đăng nhập Manager

1. Truy cập `https://your-domain.com/manager/login`
2. Sử dụng account có role `manager`
3. Hệ thống tự động chuyển về dashboard Manager

### Duyệt công thức

1. Vào `/manager/recipes`
2. Filter "Chờ phê duyệt"
3. Sử dụng action "Duyệt" hoặc "Từ chối"
4. Có thể duyệt hàng loạt

### Quản lý bài viết

1. Vào `/manager/posts`
2. Tạo bài viết mới với rich editor
3. Xuất bản trực tiếp hoặc lưu nháp
4. Quản lý trạng thái bài viết

## Người dùng test

**Manager Account**:

-   Email: `manager@beefood.com`
-   Password: `password`
-   Role: `manager`

## Routes được tạo

```
GET /manager                    - Dashboard Manager
GET /manager/login             - Đăng nhập Manager
GET /manager/recipes           - Danh sách công thức
GET /manager/recipes/create    - Tạo công thức
GET /manager/posts             - Danh sách bài viết
GET /manager/posts/create      - Tạo bài viết
```

## Lưu ý kỹ thuật

1. **Cache**: Đã clear config/route/view cache
2. **Phân quyền**: Sử dụng Spatie Laravel Permission
3. **UI Components**: Sử dụng Flowbite với Heroicons [[memory:2500974]]
4. **Performance**: Auto-refresh, pagination, polling
5. **UX**: Badge thông báo, quick actions, bulk operations

Hệ thống đã sẵn sàng sử dụng và Manager có thể đăng nhập ngay để bắt đầu quản lý nội dung!
