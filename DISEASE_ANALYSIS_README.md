# 🏥 Hệ thống Phân tích Bệnh án & Đề xuất Món ăn

## 📋 Tổng quan

Hệ thống này cho phép người dùng tải lên hình ảnh bệnh án hoặc kết quả xét nghiệm y tế để nhận đề xuất món ăn phù hợp với tình trạng sức khỏe. Hệ thống sử dụng AI để phân tích hình ảnh và đưa ra các gợi ý dinh dưỡng phù hợp.

## 🚀 Tính năng chính

### 1. Phân tích hình ảnh bệnh án

-   Tải lên hình ảnh bệnh án, kết quả xét nghiệm
-   AI phân tích và xác định bệnh, triệu chứng
-   Tự động tìm kiếm bệnh tương ứng trong database

### 2. Đề xuất món ăn

-   Món ăn phù hợp (suitable)
-   Món ăn cần điều chỉnh (moderate)
-   Thực phẩm nên tránh và nên ăn
-   Gợi ý thay thế nguyên liệu

### 3. Tìm kiếm theo nguyên liệu

-   Tìm món ăn dựa trên nguyên liệu được khuyến nghị
-   Kiểm tra tính phù hợp của món ăn với bệnh

## 🏗️ Cấu trúc hệ thống

### Models

-   `DiseaseCondition`: Quản lý thông tin bệnh
-   `DietaryRule`: Quy tắc ăn kiêng cho từng bệnh
-   `Recipe`: Món ăn (đã có sẵn)

### Services

-   `DiseaseAnalysisService`: Phân tích hình ảnh bệnh án
-   `DietaryRecommendationService`: Đề xuất món ăn

### Controllers

-   `DiseaseAnalysisController`: API endpoints

### Livewire Components

-   `DiseaseAnalysis`: Giao diện người dùng

## 📊 Database Schema

### Bảng `disease_conditions`

```sql
- id (primary key)
- name (tên bệnh)
- slug (URL friendly)
- description (mô tả)
- symptoms (triệu chứng - JSON)
- restricted_foods (thực phẩm cần tránh - JSON)
- recommended_foods (thực phẩm nên ăn - JSON)
- nutritional_requirements (yêu cầu dinh dưỡng - JSON)
- cooking_methods (phương pháp nấu - JSON)
- meal_timing (thời gian ăn - JSON)
- severity_level (mức độ nghiêm trọng 1-5)
- is_active (trạng thái hoạt động)
```

### Bảng `dietary_rules`

```sql
- id (primary key)
- disease_condition_id (foreign key)
- name (tên quy tắc)
- description (mô tả)
- food_categories (danh mục thực phẩm - JSON)
- ingredients (nguyên liệu - JSON)
- cooking_restrictions (hạn chế nấu - JSON)
- portion_limits (giới hạn khẩu phần - JSON)
- substitutions (thay thế - JSON)
- priority (độ ưu tiên)
- is_active (trạng thái hoạt động)
```

### Bảng `recipe_disease_conditions` (Pivot)

```sql
- recipe_id (foreign key)
- disease_condition_id (foreign key)
- suitability (suitable/moderate/unsuitable)
- notes (ghi chú)
- modifications (điều chỉnh - JSON)
```

## 🔧 Cài đặt và sử dụng

### 1. Chạy migration

```bash
php artisan migrate
```

### 2. Seed dữ liệu mẫu

```bash
php artisan db:seed --class=DiseaseConditionSeeder
php artisan db:seed --class=DietaryRuleSeeder
```

### 3. Truy cập trang web

```
http://your-domain/disease-analysis
```

## 📝 API Endpoints

### Phân tích hình ảnh

```http
POST /api/disease-analysis/analyze-image
Content-Type: multipart/form-data

medical_image: [file]
```

### Lấy đề xuất món ăn

```http
POST /api/disease-analysis/recommendations
Content-Type: application/json

{
    "disease_id": 1,
    "limit": 10
}
```

### Tìm kiếm theo nguyên liệu

```http
POST /api/disease-analysis/search-ingredients
Content-Type: application/json

{
    "ingredients": ["rau xanh", "cá"],
    "limit": 10
}
```

### Kiểm tra tính phù hợp

```http
POST /api/disease-analysis/check-suitability
Content-Type: application/json

{
    "recipe_id": 1,
    "disease_id": 1
}
```

## 🎯 Các bệnh được hỗ trợ

1. **Tiểu đường**

    - Hạn chế: đường, bánh kẹo, gạo trắng
    - Khuyến nghị: rau xanh, cá, gạo lứt

2. **Cao huyết áp**

    - Hạn chế: muối, mắm, thực phẩm chế biến
    - Khuyến nghị: rau xanh, chuối, cá

3. **Gout**

    - Hạn chế: thịt đỏ, hải sản, nội tạng
    - Khuyến nghị: rau xanh, sữa ít béo, trái cây

4. **Bệnh tim mạch**

    - Hạn chế: chất béo bão hòa, cholesterol
    - Khuyến nghị: cá béo, rau xanh, ngũ cốc nguyên hạt

5. **Bệnh thận**

    - Hạn chế: muối, kali cao, phốt pho cao
    - Khuyến nghị: rau xanh ít kali, protein thực vật

6. **Bệnh gan**
    - Hạn chế: rượu bia, thực phẩm nhiều chất béo
    - Khuyến nghị: rau xanh, trái cây, protein nạc

## 🔄 Quy trình hoạt động

1. **Upload hình ảnh**: Người dùng tải lên hình ảnh bệnh án
2. **AI phân tích**: Gemini AI phân tích và trả về thông tin bệnh
3. **Tìm kiếm bệnh**: Hệ thống tìm bệnh tương ứng trong database
4. **Đề xuất món ăn**: Dựa trên quy tắc ăn kiêng, đề xuất món phù hợp
5. **Kiểm tra phù hợp**: Kiểm tra tính phù hợp của món ăn với bệnh

## 📁 File JSON dữ liệu

File `storage/app/dietary_rules.json` chứa dữ liệu quy tắc ăn kiêng chi tiết cho từng bệnh, bao gồm:

-   Thực phẩm cần tránh
-   Thực phẩm nên ăn
-   Phương pháp nấu phù hợp
-   Gợi ý thay thế nguyên liệu
-   Giới hạn khẩu phần

## 🛠️ Tùy chỉnh

### Thêm bệnh mới

1. Thêm dữ liệu vào `DiseaseConditionSeeder`
2. Thêm quy tắc vào `DietaryRuleSeeder`
3. Cập nhật file JSON `dietary_rules.json`
4. Chạy seeder

### Tùy chỉnh AI prompt

Chỉnh sửa prompt trong `DiseaseAnalysisService::analyzeMedicalImage()`

### Thêm quy tắc ăn kiêng

Thêm vào model `DietaryRule` và cập nhật logic trong `DietaryRecommendationService`

## 🔒 Bảo mật

-   Validation cho file upload (chỉ cho phép hình ảnh, tối đa 5MB)
-   Sanitize input từ AI response
-   Kiểm tra quyền truy cập cho các API endpoints

## 📈 Hiệu suất

-   Cache kết quả phân tích AI
-   Index database cho các trường tìm kiếm
-   Lazy loading cho relationships
-   Pagination cho danh sách món ăn

## 🐛 Troubleshooting

### Lỗi AI không phân tích được

-   Kiểm tra API key Gemini
-   Kiểm tra quota API
-   Kiểm tra format hình ảnh

### Không tìm thấy bệnh tương ứng

-   Kiểm tra dữ liệu trong database
-   Cập nhật logic matching trong `findMatchingDiseases()`

### Đề xuất không chính xác

-   Kiểm tra quy tắc ăn kiêng
-   Cập nhật logic trong `checkRecipeSuitability()`

## 📞 Hỗ trợ

Nếu gặp vấn đề, vui lòng:

1. Kiểm tra logs trong `storage/logs/`
2. Kiểm tra cấu hình trong `.env`
3. Chạy `php artisan config:clear` và `php artisan cache:clear`
