# Thiết Kế Database BeeFood - Cấu Trúc Tối Ưu

## Phân Tích Yêu Cầu

### Tính Năng Chính:

-   Hệ thống phân quyền (User, Manager, Admin)
-   Quản lý công thức nấu ăn với approval workflow
-   Tìm kiếm và lọc nâng cao
-   Hệ thống đánh giá sao
-   Chế độ ăn đặc biệt
-   Thực đơn tuần
-   Bài viết và nội dung
-   Analytics và thống kê
-   AI integration
-   Multilingual support

### Nguyên Tắc Thiết Kế:

1. **Normalization**: Tránh data redundancy
2. **Scalability**: Hỗ trợ tăng trưởng dữ liệu
3. **Flexibility**: Dễ dàng thêm tính năng mới
4. **Performance**: Tối ưu query và indexing
5. **Maintainability**: Dễ bảo trì và mở rộng

## Cấu Trúc Database

### 1. Core Tables

#### Users & Authentication

```sql
-- Users table (extend Laravel's default)
CREATE TABLE users (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) UNIQUE NOT NULL,
    email_verified_at TIMESTAMP NULL,
    password VARCHAR(255) NOT NULL,
    avatar VARCHAR(255) NULL,
    bio TEXT NULL,
    preferences JSON NULL, -- User preferences, dietary restrictions
    status ENUM('active', 'inactive', 'banned') DEFAULT 'active',
    email_verification_token VARCHAR(100) NULL,
    password_reset_token VARCHAR(100) NULL,
    last_login_at TIMESTAMP NULL,
    login_count INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    INDEX idx_email (email),
    INDEX idx_status (status),
    INDEX idx_created_at (created_at)
);

-- User profiles for extended information
CREATE TABLE user_profiles (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id BIGINT UNSIGNED NOT NULL,
    phone VARCHAR(20) NULL,
    address TEXT NULL,
    city VARCHAR(100) NULL,
    country VARCHAR(100) DEFAULT 'Vietnam',
    timezone VARCHAR(50) DEFAULT 'Asia/Ho_Chi_Minh',
    language VARCHAR(10) DEFAULT 'vi',
    dietary_preferences JSON NULL, -- Vegan, gluten-free, etc.
    allergies JSON NULL, -- Food allergies
    health_conditions JSON NULL, -- Medical conditions
    cooking_experience ENUM('beginner', 'intermediate', 'advanced') DEFAULT 'beginner',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_user_id (user_id)
);
```

#### Roles & Permissions (Spatie Laravel Permission)

```sql
-- Roles table
CREATE TABLE roles (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) UNIQUE NOT NULL,
    guard_name VARCHAR(255) DEFAULT 'web',
    display_name VARCHAR(255) NULL,
    description TEXT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Permissions table
CREATE TABLE permissions (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) UNIQUE NOT NULL,
    guard_name VARCHAR(255) DEFAULT 'web',
    display_name VARCHAR(255) NULL,
    description TEXT NULL,
    module VARCHAR(100) NULL, -- Group permissions by module
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    INDEX idx_module (module)
);

-- Role-Permission pivot
CREATE TABLE role_has_permissions (
    permission_id BIGINT UNSIGNED NOT NULL,
    role_id BIGINT UNSIGNED NOT NULL,

    PRIMARY KEY (permission_id, role_id),
    FOREIGN KEY (permission_id) REFERENCES permissions(id) ON DELETE CASCADE,
    FOREIGN KEY (role_id) REFERENCES roles(id) ON DELETE CASCADE
);

-- User-Role pivot
CREATE TABLE model_has_roles (
    role_id BIGINT UNSIGNED NOT NULL,
    model_type VARCHAR(255) NOT NULL,
    model_id BIGINT UNSIGNED NOT NULL,

    PRIMARY KEY (role_id, model_type, model_id),
    FOREIGN KEY (role_id) REFERENCES roles(id) ON DELETE CASCADE,
    INDEX idx_model (model_type, model_id)
);
```

### 2. Content Management

#### Categories & Taxonomies

```sql
-- Categories table (hierarchical)
CREATE TABLE categories (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    slug VARCHAR(255) UNIQUE NOT NULL,
    description TEXT NULL,
    image VARCHAR(255) NULL,
    icon VARCHAR(100) NULL,
    color VARCHAR(7) NULL, -- Hex color
    parent_id BIGINT UNSIGNED NULL,
    level INT DEFAULT 0,
    sort_order INT DEFAULT 0,
    is_active BOOLEAN DEFAULT TRUE,
    created_by BIGINT UNSIGNED NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    FOREIGN KEY (parent_id) REFERENCES categories(id) ON DELETE SET NULL,
    FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE SET NULL,
    INDEX idx_slug (slug),
    INDEX idx_parent_id (parent_id),
    INDEX idx_level (level),
    INDEX idx_sort_order (sort_order),
    INDEX idx_is_active (is_active)
);

-- Tags table for flexible tagging
CREATE TABLE tags (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    slug VARCHAR(100) UNIQUE NOT NULL,
    description TEXT NULL,
    color VARCHAR(7) NULL,
    usage_count INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    INDEX idx_slug (slug),
    INDEX idx_usage_count (usage_count)
);
```

#### Recipes & Content

```sql
-- Recipes table
CREATE TABLE recipes (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id BIGINT UNSIGNED NOT NULL,
    title VARCHAR(255) NOT NULL,
    slug VARCHAR(255) UNIQUE NOT NULL,
    description TEXT NULL,
    summary VARCHAR(500) NULL,

    -- Cooking details
    cooking_time INT NULL, -- in minutes
    preparation_time INT NULL, -- in minutes
    total_time INT NULL, -- calculated field
    difficulty ENUM('easy', 'medium', 'hard') DEFAULT 'medium',
    servings INT DEFAULT 1,
    calories_per_serving INT NULL,

    -- Content
    ingredients JSON NOT NULL, -- Structured ingredients data
    instructions JSON NOT NULL, -- Step-by-step instructions
    tips TEXT NULL,
    notes TEXT NULL,

    -- Media
    featured_image VARCHAR(255) NULL,
    video_url VARCHAR(500) NULL,

    -- Status & workflow
    status ENUM('draft', 'pending', 'approved', 'rejected') DEFAULT 'draft',
    approved_by BIGINT UNSIGNED NULL,
    approved_at TIMESTAMP NULL,
    rejection_reason TEXT NULL,

    -- SEO & metadata
    meta_title VARCHAR(255) NULL,
    meta_description TEXT NULL,
    meta_keywords VARCHAR(500) NULL,

    -- Statistics
    view_count INT DEFAULT 0,
    favorite_count INT DEFAULT 0,
    rating_count INT DEFAULT 0,
    average_rating DECIMAL(3,2) DEFAULT 0.00,

    -- Timestamps
    published_at TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (approved_by) REFERENCES users(id) ON DELETE SET NULL,
    INDEX idx_slug (slug),
    INDEX idx_user_id (user_id),
    INDEX idx_status (status),
    INDEX idx_approved_by (approved_by),
    INDEX idx_published_at (published_at),
    INDEX idx_created_at (created_at),
    INDEX idx_average_rating (average_rating),
    INDEX idx_view_count (view_count),
    FULLTEXT idx_search (title, description, summary)
);

-- Recipe images (multiple images per recipe)
CREATE TABLE recipe_images (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    recipe_id BIGINT UNSIGNED NOT NULL,
    image_path VARCHAR(255) NOT NULL,
    alt_text VARCHAR(255) NULL,
    caption VARCHAR(500) NULL,
    is_primary BOOLEAN DEFAULT FALSE,
    sort_order INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    FOREIGN KEY (recipe_id) REFERENCES recipes(id) ON DELETE CASCADE,
    INDEX idx_recipe_id (recipe_id),
    INDEX idx_is_primary (is_primary),
    INDEX idx_sort_order (sort_order)
);

-- Recipe categories (many-to-many)
CREATE TABLE recipe_categories (
    recipe_id BIGINT UNSIGNED NOT NULL,
    category_id BIGINT UNSIGNED NOT NULL,

    PRIMARY KEY (recipe_id, category_id),
    FOREIGN KEY (recipe_id) REFERENCES recipes(id) ON DELETE CASCADE,
    FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE CASCADE
);

-- Recipe tags (many-to-many)
CREATE TABLE recipe_tags (
    recipe_id BIGINT UNSIGNED NOT NULL,
    tag_id BIGINT UNSIGNED NOT NULL,

    PRIMARY KEY (recipe_id, tag_id),
    FOREIGN KEY (recipe_id) REFERENCES recipes(id) ON DELETE CASCADE,
    FOREIGN KEY (tag_id) REFERENCES tags(id) ON DELETE CASCADE
);
```

### 3. User Interactions

#### Ratings & Reviews

```sql
-- Ratings table
CREATE TABLE ratings (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id BIGINT UNSIGNED NOT NULL,
    recipe_id BIGINT UNSIGNED NOT NULL,
    rating TINYINT NOT NULL CHECK (rating >= 1 AND rating <= 5),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (recipe_id) REFERENCES recipes(id) ON DELETE CASCADE,
    UNIQUE KEY unique_user_recipe (user_id, recipe_id),
    INDEX idx_recipe_id (recipe_id),
    INDEX idx_rating (rating),
    INDEX idx_created_at (created_at)
);
```

#### Favorites & Collections

```sql
-- User favorites
CREATE TABLE favorites (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id BIGINT UNSIGNED NOT NULL,
    recipe_id BIGINT UNSIGNED NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (recipe_id) REFERENCES recipes(id) ON DELETE CASCADE,
    UNIQUE KEY unique_user_recipe (user_id, recipe_id),
    INDEX idx_user_id (user_id),
    INDEX idx_recipe_id (recipe_id)
);

-- Collections (user-created recipe collections)
CREATE TABLE collections (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id BIGINT UNSIGNED NOT NULL,
    name VARCHAR(255) NOT NULL,
    description TEXT NULL,
    is_public BOOLEAN DEFAULT FALSE,
    cover_image VARCHAR(255) NULL,
    recipe_count INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_user_id (user_id),
    INDEX idx_is_public (is_public)
);

-- Collection recipes (many-to-many)
CREATE TABLE collection_recipes (
    collection_id BIGINT UNSIGNED NOT NULL,
    recipe_id BIGINT UNSIGNED NOT NULL,
    added_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    PRIMARY KEY (collection_id, recipe_id),
    FOREIGN KEY (collection_id) REFERENCES collections(id) ON DELETE CASCADE,
    FOREIGN KEY (recipe_id) REFERENCES recipes(id) ON DELETE CASCADE
);
```

### 4. Dietary & Health System

#### Dietary Restrictions

```sql
-- Dietary restrictions
CREATE TABLE dietary_restrictions (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    slug VARCHAR(100) UNIQUE NOT NULL,
    description TEXT NULL,
    icon VARCHAR(100) NULL,
    color VARCHAR(7) NULL,
    is_active BOOLEAN DEFAULT TRUE,
    sort_order INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    INDEX idx_slug (slug),
    INDEX idx_is_active (is_active),
    INDEX idx_sort_order (sort_order)
);

-- Recipe dietary restrictions (many-to-many)
CREATE TABLE recipe_dietary_restrictions (
    recipe_id BIGINT UNSIGNED NOT NULL,
    dietary_restriction_id BIGINT UNSIGNED NOT NULL,

    PRIMARY KEY (recipe_id, dietary_restriction_id),
    FOREIGN KEY (recipe_id) REFERENCES recipes(id) ON DELETE CASCADE,
    FOREIGN KEY (dietary_restriction_id) REFERENCES dietary_restrictions(id) ON DELETE CASCADE
);
```

### 5. Content Management

#### Articles & Blog Posts

```sql
-- Articles table
CREATE TABLE articles (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id BIGINT UNSIGNED NOT NULL,
    title VARCHAR(255) NOT NULL,
    slug VARCHAR(255) UNIQUE NOT NULL,
    content LONGTEXT NOT NULL,
    excerpt TEXT NULL,
    featured_image VARCHAR(255) NULL,

    -- Status
    status ENUM('draft', 'published', 'archived') DEFAULT 'draft',
    published_at TIMESTAMP NULL,

    -- SEO
    meta_title VARCHAR(255) NULL,
    meta_description TEXT NULL,
    meta_keywords VARCHAR(500) NULL,

    -- Statistics
    view_count INT DEFAULT 0,

    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_slug (slug),
    INDEX idx_status (status),
    INDEX idx_published_at (published_at),
    FULLTEXT idx_search (title, content, excerpt)
);

-- Article categories
CREATE TABLE article_categories (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    slug VARCHAR(100) UNIQUE NOT NULL,
    description TEXT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Article category pivot
CREATE TABLE article_category (
    article_id BIGINT UNSIGNED NOT NULL,
    category_id BIGINT UNSIGNED NOT NULL,

    PRIMARY KEY (article_id, category_id),
    FOREIGN KEY (article_id) REFERENCES articles(id) ON DELETE CASCADE,
    FOREIGN KEY (category_id) REFERENCES article_categories(id) ON DELETE CASCADE
);
```

#### Weekly Menus

```sql
-- Weekly menus
CREATE TABLE weekly_menus (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id BIGINT UNSIGNED NOT NULL,
    name VARCHAR(255) NOT NULL,
    description TEXT NULL,
    week_start_date DATE NOT NULL,
    week_end_date DATE NOT NULL,
    is_public BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_user_id (user_id),
    INDEX idx_week_start_date (week_start_date),
    INDEX idx_is_public (is_public)
);

-- Weekly menu items
CREATE TABLE weekly_menu_items (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    weekly_menu_id BIGINT UNSIGNED NOT NULL,
    recipe_id BIGINT UNSIGNED NOT NULL,
    day_of_week TINYINT NOT NULL CHECK (day_of_week >= 1 AND day_of_week <= 7),
    meal_type ENUM('breakfast', 'lunch', 'dinner', 'snack') NOT NULL,
    sort_order INT DEFAULT 0,

    FOREIGN KEY (weekly_menu_id) REFERENCES weekly_menus(id) ON DELETE CASCADE,
    FOREIGN KEY (recipe_id) REFERENCES recipes(id) ON DELETE CASCADE,
    INDEX idx_weekly_menu_id (weekly_menu_id),
    INDEX idx_day_of_week (day_of_week),
    INDEX idx_meal_type (meal_type)
);
```

### 6. Moderation & Reports

#### Content Moderation

```sql
-- Reports table
CREATE TABLE reports (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id BIGINT UNSIGNED NOT NULL,
    reportable_type VARCHAR(255) NOT NULL, -- Recipe, Comment, User
    reportable_id BIGINT UNSIGNED NOT NULL,
    reason ENUM('inappropriate', 'spam', 'copyright', 'other') NOT NULL,
    description TEXT NULL,
    status ENUM('pending', 'reviewed', 'resolved', 'dismissed') DEFAULT 'pending',
    handled_by BIGINT UNSIGNED NULL,
    handled_at TIMESTAMP NULL,
    resolution_notes TEXT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (handled_by) REFERENCES users(id) ON DELETE SET NULL,
    INDEX idx_reportable (reportable_type, reportable_id),
    INDEX idx_status (status),
    INDEX idx_handled_by (handled_by),
    INDEX idx_created_at (created_at)
);
```

### 7. Analytics & Tracking

#### User Activity Tracking

```sql
-- User activity logs
CREATE TABLE activity_logs (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id BIGINT UNSIGNED NULL,
    action VARCHAR(100) NOT NULL,
    subject_type VARCHAR(255) NULL,
    subject_id BIGINT UNSIGNED NULL,
    properties JSON NULL,
    ip_address VARCHAR(45) NULL,
    user_agent TEXT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL,
    INDEX idx_user_id (user_id),
    INDEX idx_action (action),
    INDEX idx_subject (subject_type, subject_id),
    INDEX idx_created_at (created_at)
);

-- Recipe views tracking
CREATE TABLE recipe_views (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    recipe_id BIGINT UNSIGNED NOT NULL,
    user_id BIGINT UNSIGNED NULL,
    ip_address VARCHAR(45) NOT NULL,
    user_agent TEXT NULL,
    viewed_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    FOREIGN KEY (recipe_id) REFERENCES recipes(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL,
    INDEX idx_recipe_id (recipe_id),
    INDEX idx_user_id (user_id),
    INDEX idx_viewed_at (viewed_at),
    INDEX idx_ip_address (ip_address)
);
```

### 8. Multilingual Support

#### Translations

```sql
-- Translations table
CREATE TABLE translations (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    locale VARCHAR(10) NOT NULL,
    namespace VARCHAR(100) NOT NULL,
    key_name VARCHAR(255) NOT NULL,
    value TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    UNIQUE KEY unique_translation (locale, namespace, key_name),
    INDEX idx_locale (locale),
    INDEX idx_namespace (namespace)
);

-- Content translations
CREATE TABLE content_translations (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    translatable_type VARCHAR(255) NOT NULL,
    translatable_id BIGINT UNSIGNED NOT NULL,
    locale VARCHAR(10) NOT NULL,
    field_name VARCHAR(100) NOT NULL,
    value TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    UNIQUE KEY unique_content_translation (translatable_type, translatable_id, locale, field_name),
    INDEX idx_translatable (translatable_type, translatable_id),
    INDEX idx_locale (locale)
);
```

### 9. System Configuration

#### Settings & Configuration

```sql
-- System settings
CREATE TABLE settings (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    key_name VARCHAR(255) UNIQUE NOT NULL,
    value TEXT NULL,
    type ENUM('string', 'integer', 'boolean', 'json') DEFAULT 'string',
    description TEXT NULL,
    is_public BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    INDEX idx_key_name (key_name),
    INDEX idx_is_public (is_public)
);

-- Cache table for Laravel
CREATE TABLE cache (
    `key` VARCHAR(255) PRIMARY KEY,
    `value` MEDIUMTEXT NOT NULL,
    expiration INT NOT NULL
);

-- Cache locks table
CREATE TABLE cache_locks (
    `key` VARCHAR(255) PRIMARY KEY,
    `owner` VARCHAR(255) NOT NULL,
    `expiration` INT NOT NULL
);
```

## Indexing Strategy

### Primary Indexes

```sql
-- Composite indexes for common queries
CREATE INDEX idx_recipes_status_published ON recipes(status, published_at);
CREATE INDEX idx_recipes_user_status ON recipes(user_id, status);
CREATE INDEX idx_recipes_rating_view ON recipes(average_rating DESC, view_count DESC);
CREATE INDEX idx_ratings_recipe_rating ON ratings(recipe_id, rating);
CREATE INDEX idx_favorites_user_created ON favorites(user_id, created_at DESC);
```

### Full-Text Search Indexes

```sql
-- Full-text search for recipes
ALTER TABLE recipes ADD FULLTEXT INDEX ft_recipes_search (title, description, summary);

-- Full-text search for articles
ALTER TABLE articles ADD FULLTEXT INDEX ft_articles_search (title, content, excerpt);
```

## Data Integrity Constraints

### Foreign Key Constraints

```sql
-- Ensure referential integrity
ALTER TABLE recipes ADD CONSTRAINT fk_recipes_user
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE;

ALTER TABLE ratings ADD CONSTRAINT fk_ratings_user
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE;

ALTER TABLE ratings ADD CONSTRAINT fk_ratings_recipe
    FOREIGN KEY (recipe_id) REFERENCES recipes(id) ON DELETE CASCADE;
```

### Check Constraints

```sql
-- Rating validation
ALTER TABLE ratings ADD CONSTRAINT chk_rating_range
    CHECK (rating >= 1 AND rating <= 5);

-- Difficulty validation
ALTER TABLE recipes ADD CONSTRAINT chk_difficulty
    CHECK (difficulty IN ('easy', 'medium', 'hard'));

-- Status validation
ALTER TABLE recipes ADD CONSTRAINT chk_recipe_status
    CHECK (status IN ('draft', 'pending', 'approved', 'rejected'));
```

## Performance Optimization

### Partitioning Strategy

```sql
-- Partition activity_logs by date for better performance
ALTER TABLE activity_logs PARTITION BY RANGE (YEAR(created_at)) (
    PARTITION p2024 VALUES LESS THAN (2025),
    PARTITION p2025 VALUES LESS THAN (2026),
    PARTITION p2026 VALUES LESS THAN (2027),
    PARTITION p_future VALUES LESS THAN MAXVALUE
);
```

### Archiving Strategy

```sql
-- Archive old activity logs
CREATE TABLE activity_logs_archive LIKE activity_logs;

-- Archive old recipe views
CREATE TABLE recipe_views_archive LIKE recipe_views;
```

## Backup & Recovery

### Backup Strategy

```sql
-- Create backup tables for critical data
CREATE TABLE users_backup LIKE users;
CREATE TABLE recipes_backup LIKE recipes;
CREATE TABLE ratings_backup LIKE ratings;
```

## Migration Strategy

### Version Control

```sql
-- Database version tracking
CREATE TABLE migrations (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    migration VARCHAR(255) NOT NULL,
    batch INT NOT NULL,
    executed_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
```

## Kết Luận

Database design này đảm bảo:

### **Tính Mở Rộng:**

-   Modular structure cho từng tính năng
-   Flexible tagging system
-   JSON fields cho dynamic data
-   Polymorphic relationships

### **Hiệu Suất:**

-   Strategic indexing
-   Partitioning cho large tables
-   Optimized queries
-   Caching strategy

### **Bảo Mật:**

-   Proper foreign key constraints
-   Data validation
-   Audit trails
-   Access control

### **Bảo Trì:**

-   Clear naming conventions
-   Documentation
-   Version control
-   Backup strategy

Database này có thể hỗ trợ tất cả tính năng hiện tại và tương lai của BeeFood mà không cần major restructuring.

---

_Cập nhật lần cuối: [Ngày hiện tại]_
_Phiên bản: 1.0_
