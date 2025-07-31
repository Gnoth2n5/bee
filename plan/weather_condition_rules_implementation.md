# Hệ Thống Quy Tắc Điều Kiện Thời Tiết - BeeFood

## Tổng Quan

Hệ thống quy tắc điều kiện thời tiết mới cho phép đề xuất món ăn dựa trên nhiệt độ và độ ẩm **không gắn với thành phố cụ thể**. Điều này tạo ra sự linh hoạt và chính xác hơn trong việc đề xuất món ăn phù hợp với điều kiện thời tiết thực tế tại bất kỳ địa điểm nào.

## Cấu Trúc Hệ Thống

### 1. Database Schema

#### Bảng `weather_condition_rules`
```sql
CREATE TABLE weather_condition_rules (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,                    -- Tên quy tắc
    description TEXT NULL,                         -- Mô tả quy tắc
    temperature_min DECIMAL(5,2) NULL,             -- Nhiệt độ tối thiểu
    temperature_max DECIMAL(5,2) NULL,             -- Nhiệt độ tối đa
    humidity_min INT NULL,                         -- Độ ẩm tối thiểu
    humidity_max INT NULL,                         -- Độ ẩm tối đa
    suggested_categories JSON NULL,                -- ID các category phù hợp
    suggested_tags JSON NULL,                      -- ID các tag phù hợp
    suggestion_reason TEXT NOT NULL,               -- Lý do đề xuất
    is_active BOOLEAN DEFAULT TRUE,                -- Trạng thái hoạt động
    priority INT DEFAULT 1,                        -- Độ ưu tiên
    seasonal_rules JSON NULL,                      -- Quy tắc theo mùa
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);
```

**Lưu ý**: Không có trường `city_code` - hệ thống này hoàn toàn độc lập với thành phố.

### 2. Models

#### WeatherConditionRule Model
- **Relationships**: Categories, Tags
- **Scopes**: Active, ForTemperature, ForHumidity, OrderByPriority
- **Methods**: 
  - `matchesConditions()`: Kiểm tra điều kiện phù hợp
  - `getMatchingRecipes()`: Lấy recipes phù hợp
  - `getTemperatureRangeDescription()`: Mô tả khoảng nhiệt độ
  - `getHumidityRangeDescription()`: Mô tả khoảng độ ẩm

### 3. Services

#### WeatherConditionRuleService
- **getSuggestionsByConditions()**: Lấy đề xuất dựa trên điều kiện (không có thành phố)
- **findMatchingRules()**: Tìm quy tắc phù hợp
- **createPredefinedRules()**: Tạo quy tắc mặc định
- **getSuggestionReason()**: Lấy lý do đề xuất
- **getStats()**: Thống kê hệ thống

## Quy Tắc Mặc Định

### 1. Nhiệt Độ Cao (>= 30°C)
- **Categories**: Salad, Đồ uống, Tráng miệng, Món mát
- **Tags**: mát, nhẹ, giải nhiệt, tươi
- **Lý do**: Nhiệt độ cao trên 30°C - phù hợp với các món ăn mát, nhẹ để giải nhiệt

### 2. Nhiệt Độ Cao Độ Ẩm Cao (24-30°C, >70%)
- **Categories**: Súp, Salad, Món mát, Đồ uống
- **Tags**: mát, nhẹ, giải nhiệt, tươi, súp
- **Lý do**: Nhiệt độ cao (24-30°C) và độ ẩm cao (>70%) - gợi ý các món nhẹ như súp và salad để giải nhiệt

### 3. Nhiệt Độ Cao Độ Ẩm Thấp (24-30°C, <70%)
- **Categories**: Canh, Súp, Đồ uống, Món nước, Cháo
- **Tags**: nước, canh, súp, cháo, dễ tiêu
- **Lý do**: Nhiệt độ cao (24-30°C) và độ ẩm thấp (<70%) - gợi ý các món nước và món chế biến nhanh

### 4. Nhiệt Độ Mát Mẻ (15-24°C)
- **Categories**: Món chính, Món nóng, Thịt, Hải sản
- **Tags**: cân bằng, đa dạng, dinh dưỡng
- **Lý do**: Thời tiết mát mẻ (15-24°C) - gợi ý các món ăn đa dạng, cân bằng dinh dưỡng

### 5. Nhiệt Độ Lạnh (< 15°C)
- **Categories**: Lẩu, Cháo, Súp nóng, Món nóng, Thịt
- **Tags**: nóng, ấm, dinh dưỡng, lẩu, cháo
- **Lý do**: Thời tiết lạnh (dưới 15°C) - phù hợp với các món ăn nóng, giàu dinh dưỡng để giữ ấm

### 6. Độ Ẩm Cao (>80%)
- **Categories**: Món khô, Món cay, Nướng, Chiên
- **Tags**: khô, cay, nướng, chiên
- **Lý do**: Độ ẩm cao (>80%) - gợi ý các món ăn khô, cay để cân bằng

### 7. Độ Ẩm Thấp (<40%)
- **Categories**: Canh, Súp, Đồ uống, Món mát
- **Tags**: nước, canh, súp, mát
- **Lý do**: Độ ẩm thấp (<40%) - gợi ý các món ăn có nước, mát để bổ sung độ ẩm

## Cách Sử Dụng

### 1. Tạo Quy Tắc Mặc Định
```bash
php artisan weather:create-rules
```

### 2. Sử Dụng Trong Code
```php
// Lấy đề xuất dựa trên nhiệt độ và độ ẩm (không cần thành phố)
$suggestions = $weatherConditionRuleService->getSuggestionsByConditions(
    $temperature = 25,
    $humidity = 75,
    $limit = 12
);

// Lấy lý do đề xuất
$reason = $weatherConditionRuleService->getSuggestionReason(25, 75);
```

### 3. Quản Lý Trong Admin Panel
- Truy cập: `/admin/weather-condition-rules`
- Tạo, chỉnh sửa, xóa quy tắc
- Test quy tắc với dữ liệu thực tế
- **Không có tham chiếu đến thành phố**

## Tích Hợp Với Frontend

### 1. Chọn Thành Phố (Để Lấy Dữ Liệu Thời Tiết)
- Người dùng chọn thành phố để lấy thông tin nhiệt độ và độ ẩm
- Hệ thống sử dụng dữ liệu thời tiết của thành phố đó

### 2. Áp Dụng Quy Tắc (Không Phụ Thuộc Thành Phố)
- Dựa trên nhiệt độ và độ ẩm từ thành phố đã chọn
- Áp dụng các quy tắc phù hợp để đề xuất món ăn
- **Kết quả đề xuất không phụ thuộc vào thành phố**

### 3. Nhập Thủ Công
- Người dùng có thể nhập nhiệt độ và độ ẩm thủ công
- Hệ thống áp dụng quy tắc dựa trên giá trị nhập vào
- **Hoàn toàn độc lập với thành phố**

## Lợi Ích

### 1. Linh Hoạt Tuyệt Đối
- Không gắn cố định với thành phố
- Có thể áp dụng cho bất kỳ địa điểm nào
- Dễ dàng tùy chỉnh quy tắc

### 2. Chính Xác
- Dựa trên điều kiện thời tiết thực tế
- Kết hợp nhiệt độ và độ ẩm
- Hỗ trợ quy tắc theo mùa

### 3. Mở Rộng
- Dễ dàng thêm quy tắc mới
- Hỗ trợ nhiều loại điều kiện
- Có thể tích hợp với AI/ML

### 4. Quản Lý
- Giao diện admin thân thiện (tiếng Việt)
- Thống kê chi tiết
- Test và debug dễ dàng

## Tích Hợp Với Hệ Thống Cũ

### 1. Tương Thích Ngược
- Giữ nguyên API cũ cho việc chọn thành phố
- Tự động chuyển đổi sang hệ thống quy tắc mới
- Không ảnh hưởng đến frontend

### 2. Cải Tiến Dần Dần
- Có thể sử dụng song song
- Chuyển đổi từng phần
- Đảm bảo ổn định

## Kế Hoạch Phát Triển

### 1. Ngắn Hạn
- [x] Tạo database schema (không có thành phố)
- [x] Implement models và services
- [x] Tạo admin interface (tiếng Việt)
- [x] Tích hợp với hệ thống cũ

### 2. Trung Hạn
- [ ] Thêm quy tắc theo mùa
- [ ] Tích hợp với AI/ML
- [ ] Thêm analytics
- [ ] Optimize performance

### 3. Dài Hạn
- [ ] Hỗ trợ nhiều loại thời tiết
- [ ] Personalization
- [ ] Machine learning
- [ ] API cho third-party

## Kết Luận

Hệ thống quy tắc điều kiện thời tiết mới cung cấp một giải pháp linh hoạt và chính xác cho việc đề xuất món ăn. **Không gắn với thành phố cụ thể**, hệ thống này có thể áp dụng cho bất kỳ địa điểm nào dựa trên điều kiện thời tiết thực tế. Với khả năng tùy chỉnh cao và dễ quản lý, hệ thống này sẽ cải thiện đáng kể trải nghiệm người dùng và hiệu quả của ứng dụng BeeFood.
