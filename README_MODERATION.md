# Hệ thống Kiểm duyệt Tự động Công thức

## Tổng quan

Hệ thống kiểm duyệt tự động được thiết kế để tự động phê duyệt, từ chối hoặc đánh dấu các công thức nấu ăn dựa trên các quy tắc được định nghĩa trước. Hệ thống chạy tự động mỗi giờ và cũng có thể được chạy thủ công.

## Tính năng chính

### 1. Quản lý Quy tắc Kiểm duyệt

-   **Tạo quy tắc mới**: Định nghĩa từ khóa cấm và hành động tương ứng
-   **Chỉnh sửa quy tắc**: Cập nhật từ khóa, hành động, độ ưu tiên
-   **Bật/Tắt quy tắc**: Kích hoạt hoặc vô hiệu hóa quy tắc
-   **Độ ưu tiên**: Quy tắc có độ ưu tiên cao hơn sẽ được áp dụng trước

### 2. Các loại Hành động

-   **Reject (Từ chối)**: Tự động từ chối công thức vi phạm
-   **Flag (Đánh dấu)**: Đánh dấu để kiểm tra thủ công
-   **Auto Approve (Tự động phê duyệt)**: Vẫn phê duyệt dù có vi phạm

### 3. Kiểm tra Nội dung

Hệ thống kiểm tra các trường sau:

-   Tiêu đề (title)
-   Mô tả (description)
-   Tóm tắt (summary)
-   Nguyên liệu (ingredients)
-   Hướng dẫn (instructions)
-   Mẹo (tips)
-   Ghi chú (notes)

## Cài đặt và Sử dụng

### 1. Chạy Migration

```bash
php artisan migrate
```

### 2. Chạy Seeder (Tạo dữ liệu mẫu)

```bash
php artisan db:seed --class=ModerationRuleSeeder
```

### 3. Chạy kiểm duyệt thủ công

```bash
# Chạy kiểm duyệt tất cả công thức đang chờ
php artisan recipes:auto-moderate

# Chạy thử nghiệm (không thay đổi dữ liệu)
php artisan recipes:auto-moderate --dry-run
```

### 4. Lên lịch tự động

Hệ thống đã được cấu hình để chạy tự động mỗi giờ. Để đảm bảo hoạt động, cần thiết lập cron job:

```bash
# Thêm vào crontab
* * * * * cd /path/to/your/project && php artisan schedule:run >> /dev/null 2>&1
```

## Quản lý qua Admin Panel

### 1. Truy cập Quy tắc Kiểm duyệt

-   Đăng nhập vào Admin Panel
-   Vào menu "Quản lý hệ thống" > "Quy tắc kiểm duyệt"

### 2. Tạo Quy tắc Mới

1. Click "Tạo quy tắc mới"
2. Điền thông tin:
    - **Tên quy tắc**: Mô tả ngắn gọn
    - **Từ khóa cấm**: Danh sách từ khóa, phân cách bằng dấu phẩy
    - **Hành động**: Chọn reject/flag/auto_approve
    - **Mô tả**: Giải thích chi tiết
    - **Các trường kiểm tra**: Chọn trường cần kiểm tra
    - **Độ ưu tiên**: Số từ 1-10 (càng cao càng ưu tiên)
    - **Kích hoạt**: Bật/tắt quy tắc

### 3. Test Kiểm duyệt

-   Trong trang quản lý công thức, chọn công thức cần test
-   Click "Test kiểm duyệt" để xem kết quả
-   Hệ thống sẽ hiển thị các quy tắc bị vi phạm (nếu có)

### 4. Kiểm duyệt Hàng loạt

-   Chọn nhiều công thức trong danh sách
-   Click "Kiểm duyệt tự động đã chọn"
-   Hệ thống sẽ áp dụng quy tắc cho tất cả công thức đã chọn

## Quy tắc Mẫu

Hệ thống đã được cấu hình sẵn các quy tắc mẫu:

1. **Từ ngữ không phù hợp** (Priority: 10)

    - Từ chối các từ ngữ tục tĩu
    - Hành động: Reject

2. **Spam và quảng cáo** (Priority: 9)

    - Từ chối nội dung spam, quảng cáo
    - Hành động: Reject

3. **Nội dung chính trị nhạy cảm** (Priority: 8)

    - Đánh dấu nội dung chính trị
    - Hành động: Flag

4. **Từ khóa nhạy cảm** (Priority: 7)

    - Từ chối nội dung liên quan chất cấm
    - Hành động: Reject

5. **Nội dung bạo lực** (Priority: 6)

    - Từ chối nội dung bạo lực
    - Hành động: Reject

6. **Từ khóa tình dục** (Priority: 5)

    - Đánh dấu nội dung tình dục
    - Hành động: Flag

7. **Nội dung phân biệt đối xử** (Priority: 4)

    - Từ chối nội dung phân biệt đối xử
    - Hành động: Reject

8. **Từ khóa y tế nhạy cảm** (Priority: 3)

    - Đánh dấu nội dung y tế
    - Hành động: Flag

9. **Nội dung tôn giáo nhạy cảm** (Priority: 2)

    - Đánh dấu nội dung tôn giáo
    - Hành động: Flag

10. **Từ khóa thương hiệu** (Priority: 1)
    - Ghi nhận thương hiệu nhưng vẫn phê duyệt
    - Hành động: Auto Approve

## Dashboard Widgets

### 1. ModerationStatsWidget

Hiển thị thống kê tổng quan:

-   Tổng số quy tắc kiểm duyệt
-   Số công thức chờ duyệt
-   Số công thức bị từ chối
-   Số công thức đánh dấu

### 2. ModerationRulesWidget

Hiển thị danh sách 5 quy tắc gần đây nhất với thông tin chi tiết.

## Log và Monitoring

### 1. Log Files

Hệ thống ghi log vào:

-   `storage/logs/laravel.log`

### 2. Các loại Log

-   **Auto approval**: Khi công thức được tự động phê duyệt
-   **Auto rejection**: Khi công thức bị tự động từ chối
-   **Flagging**: Khi công thức được đánh dấu
-   **Errors**: Khi có lỗi xảy ra trong quá trình kiểm duyệt

### 3. Monitoring

-   Kiểm tra log định kỳ để đảm bảo hệ thống hoạt động
-   Theo dõi tỷ lệ phê duyệt/từ chối để điều chỉnh quy tắc
-   Cập nhật từ khóa cấm khi cần thiết

## Tùy chỉnh và Mở rộng

### 1. Thêm Quy tắc Mới

-   Tạo quy tắc qua Admin Panel
-   Hoặc thêm trực tiếp vào database
-   Có thể import/export quy tắc qua CSV

### 2. Tùy chỉnh Logic

-   Chỉnh sửa `ModerationService` để thay đổi logic kiểm tra
-   Thêm các loại hành động mới
-   Tùy chỉnh cách xử lý vi phạm

### 3. Tích hợp AI

-   Có thể tích hợp AI để phân tích nội dung phức tạp hơn
-   Sử dụng sentiment analysis
-   Phát hiện spam nâng cao

## Troubleshooting

### 1. Lỗi thường gặp

-   **Command không chạy**: Kiểm tra quyền thực thi
-   **Không có kết quả**: Kiểm tra có công thức pending không
-   **Quy tắc không hoạt động**: Kiểm tra trạng thái is_active

### 2. Debug

```bash
# Chạy với verbose
php artisan recipes:auto-moderate -v

# Kiểm tra log
tail -f storage/logs/laravel.log
```

### 3. Reset

```bash
# Xóa tất cả quy tắc
php artisan tinker
>>> App\Models\ModerationRule::truncate();

# Chạy lại seeder
php artisan db:seed --class=ModerationRuleSeeder
```

## Bảo mật

### 1. Quyền truy cập

-   Chỉ admin và manager mới có thể quản lý quy tắc
-   User thường chỉ có thể xem kết quả kiểm duyệt

### 2. Validation

-   Tất cả input đều được validate
-   SQL injection protection
-   XSS protection

### 3. Audit Trail

-   Ghi log tất cả thay đổi quy tắc
-   Track người tạo/chỉnh sửa quy tắc
-   Backup định kỳ

## Kết luận

Hệ thống kiểm duyệt tự động giúp:

-   Giảm tải công việc cho admin
-   Đảm bảo chất lượng nội dung
-   Phát hiện và ngăn chặn nội dung không phù hợp
-   Tăng hiệu quả quản lý website

Để đạt hiệu quả tốt nhất, cần:

-   Định kỳ cập nhật từ khóa cấm
-   Theo dõi và điều chỉnh quy tắc
-   Đào tạo admin về cách sử dụng
-   Backup dữ liệu định kỳ
