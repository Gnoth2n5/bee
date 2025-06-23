# Kế Hoạch Phân Quyền Chi Tiết - BeeFood

## Phân Tích Cookpad Việt Nam

Dựa trên [Cookpad Việt Nam](https://cookpad.com/vn), tôi đã phân tích các tính năng chính:

### Tính Năng Chính của Cookpad:

-   **Tìm kiếm công thức** với từ khóa phổ biến
-   **Cooksnap** - chia sẻ ảnh món ăn đã nấu
-   **Premium features** - thực đơn premium, top món được xem nhiều
-   **Chuyên mục** - phân loại theo mùa, chế độ ăn, độ tuổi
-   **Cộng đồng** - tương tác giữa người dùng
-   **Mobile app** - ứng dụng di động

## Hệ Thống Phân Quyền BeeFood

### 1. User (Người dùng thường)

**Quyền cơ bản:**

-   Đăng ký/đăng nhập
-   Xem công thức công khai
-   Tìm kiếm và lọc công thức
-   Đánh giá sao (1-5)
-   Lưu công thức yêu thích
-   Tạo bộ sưu tập cá nhân
-   Đăng tải công thức cá nhân
-   Chỉnh sửa/xóa công thức của mình
-   Cập nhật hồ sơ cá nhân
-   Xem thực đơn tuần
-   Xuất PDF thực đơn

**Hạn chế:**

-   Không thể quản lý công thức của người khác
-   Không thể quản lý danh mục
-   Không thể xem thống kê admin

### 2. Manager (Quản lý nội dung)

**Quyền mở rộng từ User:**

-   Quản lý danh mục công thức
-   Duyệt công thức mới
-   Quản lý chế độ ăn đặc biệt
-   Tạo và quản lý bài viết nổi bật
-   Quản lý thực đơn tuần
-   Xem thống kê cơ bản
-   Quản lý báo cáo vi phạm
-   Gắn thẻ chế độ ăn cho công thức
-   Quản lý từ khóa tìm kiếm phổ biến

**Hạn chế:**

-   Không thể quản lý người dùng
-   Không thể thay đổi cấu hình hệ thống
-   Không thể xem thống kê tài chính

### 3. Admin (Quản trị viên)

**Quyền toàn bộ:**

-   Tất cả quyền của Manager
-   Quản lý người dùng (thăng cấp, hạ cấp, khóa)
-   Quản lý roles và permissions
-   Cấu hình hệ thống
-   Xem thống kê chi tiết và báo cáo
-   Quản lý AI settings
-   Backup và restore database
-   Quản lý SEO và marketing
-   Quản lý thanh toán và premium features
-   Quản lý thông báo hệ thống

## Phân Chia Chức Năng Chi Tiết

### Phase 1: MVP (8 tuần)

#### Tuần 1-2: Setup & Authentication

**Backend Setup:**

-   [ ] **Cài đặt Laravel 11 + Spatie Permission**
    -   Tạo project mới
    -   Cấu hình database (MySQL)
    -   Setup Redis cho cache
    -   Cài đặt Spatie Laravel Permission
    -   Tạo roles: admin, manager, user

**Database Migration:**

```sql
-- Users table (extend existing)
users (id, name, email, password, avatar, bio, preferences, email_verified_at, created_at, updated_at)

-- Roles table (Spatie)
roles (id, name, guard_name, created_at, updated_at)
permissions (id, name, guard_name, created_at, updated_at)
role_has_permissions (permission_id, role_id)
model_has_roles (role_id, model_type, model_id)
model_has_permissions (permission_id, model_type, model_id)

-- Categories table
categories (id, name, slug, description, image, parent_id, created_by, status, created_at, updated_at)

-- Recipes table
recipes (id, user_id, title, slug, description, ingredients, instructions, cooking_time, difficulty, servings, image, status, approved_by, approved_at, created_at, updated_at)

-- Recipe Categories (pivot)
recipe_category (recipe_id, category_id)

-- Ratings table
ratings (id, user_id, recipe_id, rating, created_at, updated_at)

-- Favorites table
favorites (id, user_id, recipe_id, created_at)

-- Recipe Images table
recipe_images (id, recipe_id, image_path, is_primary, order, created_at)

-- Reports table
reports (id, user_id, recipe_id, reason, status, handled_by, handled_at, created_at, updated_at)
```

**Permissions Setup:**

```php
// User Permissions
'view-recipes'
'create-recipes'
'edit-own-recipes'
'delete-own-recipes'
'rate-recipes'
'favorite-recipes'
'view-own-profile'
'edit-own-profile'

// Manager Permissions
'approve-recipes'
'reject-recipes'
'manage-categories'
'manage-dietary-restrictions'
'create-articles'
'manage-weekly-menus'
'view-basic-statistics'
'handle-reports'

// Admin Permissions
'manage-users'
'manage-roles'
'manage-system-settings'
'view-all-statistics'
'manage-ai-settings'
'manage-seo'
'manage-payments'
'backup-database'
```

#### Tuần 3-4: Recipe CRUD với Phân Quyền

**Recipe Management:**

-   [ ] **Recipe Model với Authorization**

    -   Recipe policy cho từng role
    -   Middleware kiểm tra quyền
    -   Soft delete cho recipes

-   [ ] **Recipe Creation Form (Livewire)**

    -   Multi-step form với validation
    -   Rich text editor (TinyMCE/CKEditor)
    -   Dynamic ingredients input
    -   Image upload (multiple)
    -   Category selection
    -   Draft/Publish status

-   [ ] **Recipe Approval System**
    -   Manager approval workflow
    -   Email notifications
    -   Approval history tracking
    -   Auto-publish for trusted users

**Test Cases:**

```php
// Test Recipe Creation
public function test_user_can_create_recipe()
public function test_user_cannot_edit_others_recipe()
public function test_manager_can_approve_recipe()
public function test_admin_can_manage_all_recipes()
public function test_recipe_requires_approval_for_new_users()
```

#### Tuần 5-6: Search & Filter với Phân Quyền

**Search System:**

-   [ ] **Advanced Search với Permissions**

    -   Search by title, ingredients, author
    -   Filter by approval status (admin/manager)
    -   Filter by user role
    -   Search suggestions

-   [ ] **Category Management**
    -   Manager CRUD categories
    -   Category hierarchy
    -   Category permissions

**Test Cases:**

```php
// Test Search Permissions
public function test_user_can_search_approved_recipes()
public function test_manager_can_search_pending_recipes()
public function test_admin_can_search_all_recipes()
public function test_category_filter_works_correctly()
```

#### Tuần 7-8: Rating & Favorites với Moderation

**Rating System:**

-   [ ] **Star Rating với Moderation**

    -   5-star rating system
    -   Rating validation (prevent spam)
    -   Manager can moderate ratings
    -   Rating analytics

-   [ ] **Favorite System**
    -   Like/Unlike functionality
    -   Favorite collections
    -   Share collections

**Test Cases:**

```php
// Test Rating System
public function test_user_can_rate_recipe_once()
public function test_user_cannot_rate_own_recipe()
public function test_manager_can_moderate_ratings()
public function test_rating_average_calculation()
```

### Phase 2: Advanced Features (8 tuần)

#### Tuần 9-10: Dietary Restrictions & Moderation

**Dietary System:**

-   [ ] **Dietary Management (Manager)**

    -   CRUD dietary restrictions
    -   Assign tags to recipes
    -   Dietary validation
    -   Icon và color management

-   [ ] **Content Moderation**
    -   Report system
    -   Manager approval queue
    -   Auto-flagging system
    -   Moderation dashboard

**Test Cases:**

```php
// Test Dietary System
public function test_manager_can_create_dietary_restriction()
public function test_user_can_filter_by_dietary()
public function test_dietary_validation_works()
public function test_report_system_functionality()
```

#### Tuần 11-12: Weekly Menu & Articles

**Content Management:**

-   [ ] **Weekly Menu (Manager)**

    -   Menu builder interface
    -   Auto-suggestions
    -   PDF export
    -   Email distribution

-   [ ] **Article Management (Manager)**
    -   Rich text editor
    -   Article categories
    -   Featured articles
    -   SEO optimization

**Test Cases:**

```php
// Test Content Management
public function test_manager_can_create_weekly_menu()
public function test_user_can_view_weekly_menu()
public function test_pdf_export_works()
public function test_article_publishing_workflow()
```

#### Tuần 13-14: Multilingual & Localization

**Internationalization:**

-   [ ] **Language Management (Admin)**
    -   Language settings
    -   Translation management
    -   Content translation
    -   SEO meta translation

**Test Cases:**

```php
// Test Multilingual
public function test_language_switcher_works()
public function test_content_translation()
public function test_seo_meta_translation()
```

#### Tuần 15-16: Real-time Notifications

**Notification System:**

-   [ ] **Role-based Notifications**
    -   User: likes, follows, recipe approval
    -   Manager: new recipes, reports, system alerts
    -   Admin: system issues, user management

**Test Cases:**

```php
// Test Notifications
public function test_user_receives_recipe_approval_notification()
public function test_manager_receives_new_recipe_notification()
public function test_admin_receives_system_alerts()
```

### Phase 3: AI Integration (8 tuần)

#### Tuần 17-18: AI Recommendations với Phân Quyền

**Recommendation Engine:**

-   [ ] **Role-based Recommendations**
    -   User: personalized recipes
    -   Manager: trending content, moderation suggestions
    -   Admin: system optimization suggestions

**Test Cases:**

```php
// Test AI Recommendations
public function test_user_gets_personalized_recommendations()
public function test_manager_gets_moderation_suggestions()
public function test_ai_recommendation_accuracy()
```

#### Tuần 19-20: Content Moderation AI

**AI Moderation:**

-   [ ] **Automated Moderation**
    -   Text content analysis
    -   Image moderation
    -   Spam detection
    -   Manager review queue

**Test Cases:**

```php
// Test AI Moderation
public function test_ai_detects_inappropriate_content()
public function test_ai_spam_detection()
public function test_moderation_queue_workflow()
```

#### Tuần 21-22: Health Profiles & Medical

**Health System:**

-   [ ] **Health Profile Management**
    -   User health profiles
    -   Allergy warnings
    -   Medical condition considerations
    -   Privacy controls

**Test Cases:**

```php
// Test Health System
public function test_user_can_create_health_profile()
public function test_allergy_warnings_display()
public function test_health_data_privacy()
```

#### Tuần 23-24: AI Content Generation

**Content AI:**

-   [ ] **Automated Content**
    -   Recipe descriptions
    -   SEO optimization
    -   Social media content
    -   Manager approval workflow

**Test Cases:**

```php
// Test AI Content Generation
public function test_ai_generates_recipe_description()
public function test_seo_optimization_works()
public function test_content_approval_workflow()
```

### Phase 4: Optimization & Analytics (8 tuần)

#### Tuần 25-26: Performance & Caching

**Performance Optimization:**

-   [ ] **Role-based Caching**
    -   User content caching
    -   Manager dashboard caching
    -   Admin analytics caching

**Test Cases:**

```php
// Test Performance
public function test_caching_improves_performance()
public function test_role_based_cache_invalidation()
public function test_database_query_optimization()
```

#### Tuần 27-28: SEO & Marketing

**SEO Implementation:**

-   [ ] **Role-based SEO**
    -   User content SEO
    -   Manager content optimization
    -   Admin SEO management

**Test Cases:**

```php
// Test SEO
public function test_recipe_seo_optimization()
public function test_sitemap_generation()
public function test_meta_tags_optimization()
```

#### Tuần 29-30: Mobile App & PWA

**Mobile Development:**

-   [ ] **Role-based Mobile Features**
    -   User mobile experience
    -   Manager mobile dashboard
    -   Admin mobile analytics

**Test Cases:**

```php
// Test Mobile
public function test_mobile_responsive_design()
public function test_pwa_functionality()
public function test_mobile_notifications()
```

#### Tuần 31-32: Analytics & Monitoring

**Analytics System:**

-   [ ] **Role-based Analytics**
    -   User: personal statistics
    -   Manager: content performance
    -   Admin: system-wide analytics

**Test Cases:**

```php
// Test Analytics
public function test_user_analytics_tracking()
public function test_manager_content_analytics()
public function test_admin_system_analytics()
```

## Middleware & Policies

### Middleware Classes

```php
// app/Http/Middleware/CheckRole.php
class CheckRole
{
    public function handle($request, Closure $next, $role)
    {
        if (!auth()->user()->hasRole($role)) {
            abort(403, 'Unauthorized action.');
        }
        return $next($request);
    }
}

// app/Http/Middleware/CheckPermission.php
class CheckPermission
{
    public function handle($request, Closure $next, $permission)
    {
        if (!auth()->user()->hasPermissionTo($permission)) {
            abort(403, 'Unauthorized action.');
        }
        return $next($request);
    }
}
```

### Policy Classes

```php
// app/Policies/RecipePolicy.php
class RecipePolicy
{
    public function viewAny(User $user)
    {
        return true;
    }

    public function view(User $user, Recipe $recipe)
    {
        return $recipe->status === 'approved' ||
               $user->hasRole(['admin', 'manager']) ||
               $recipe->user_id === $user->id;
    }

    public function create(User $user)
    {
        return true;
    }

    public function update(User $user, Recipe $recipe)
    {
        return $user->hasRole(['admin', 'manager']) ||
               $recipe->user_id === $user->id;
    }

    public function delete(User $user, Recipe $recipe)
    {
        return $user->hasRole(['admin', 'manager']) ||
               $recipe->user_id === $user->id;
    }

    public function approve(User $user, Recipe $recipe)
    {
        return $user->hasRole(['admin', 'manager']);
    }
}
```

## Routes với Phân Quyền

```php
// routes/web.php
Route::middleware(['auth'])->group(function () {
    // User routes
    Route::get('/recipes', [RecipeController::class, 'index'])->name('recipes.index');
    Route::get('/recipes/create', [RecipeController::class, 'create'])->name('recipes.create');
    Route::post('/recipes', [RecipeController::class, 'store'])->name('recipes.store');

    // Manager routes
    Route::middleware(['role:manager'])->group(function () {
        Route::get('/admin/recipes/pending', [AdminRecipeController::class, 'pending'])->name('admin.recipes.pending');
        Route::post('/admin/recipes/{recipe}/approve', [AdminRecipeController::class, 'approve'])->name('admin.recipes.approve');
        Route::resource('/admin/categories', AdminCategoryController::class);
    });

    // Admin routes
    Route::middleware(['role:admin'])->group(function () {
        Route::resource('/admin/users', AdminUserController::class);
        Route::resource('/admin/roles', AdminRoleController::class);
        Route::get('/admin/analytics', [AdminAnalyticsController::class, 'index'])->name('admin.analytics');
    });
});
```

## Test Suite Structure

```
tests/
├── Feature/
│   ├── Auth/
│   │   ├── UserRegistrationTest.php
│   │   ├── UserLoginTest.php
│   │   └── RoleAssignmentTest.php
│   ├── Recipe/
│   │   ├── RecipeCreationTest.php
│   │   ├── RecipeApprovalTest.php
│   │   └── RecipePermissionTest.php
│   ├── Search/
│   │   ├── SearchFunctionalityTest.php
│   │   └── FilterPermissionTest.php
│   ├── Rating/
│   │   ├── RatingSystemTest.php
│   │   └── RatingModerationTest.php
│   └── Admin/
│       ├── UserManagementTest.php
│       ├── ContentModerationTest.php
│       └── AnalyticsTest.php
└── Unit/
    ├── RecipePolicyTest.php
    ├── RolePermissionTest.php
    └── SearchServiceTest.php
```

## Kết Luận

Hệ thống phân quyền BeeFood được thiết kế để đảm bảo:

-   **Bảo mật**: Mỗi role chỉ có quyền truy cập phù hợp
-   **Khả năng mở rộng**: Dễ dàng thêm roles và permissions mới
-   **Kiểm thử**: Test cases đầy đủ cho từng chức năng
-   **Thực tế**: Phù hợp với nhu cầu quản lý nội dung thực tế

Mỗi phase đều có deliverables cụ thể và test cases tương ứng, đảm bảo chất lượng và độ tin cậy của hệ thống.

---

_Cập nhật lần cuối: [Ngày hiện tại]_
_Phiên bản: 1.0_
