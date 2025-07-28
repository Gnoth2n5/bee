# Chức năng Đăng nhập Google OAuth - BeeFood

## Tổng quan

Dự án BeeFood đã được tích hợp chức năng đăng nhập bằng Google OAuth 2.0, cho phép người dùng đăng nhập và đăng ký tài khoản thông qua tài khoản Google của họ.

## Tính năng đã được thêm

### 1. Database Migration

-   **File**: `database/migrations/2025_07_28_093012_add_google_id_to_users_table.php`
-   **Thêm cột**:
    -   `google_id`: Lưu ID duy nhất của user từ Google
    -   `google_token`: Lưu access token từ Google
    -   `google_refresh_token`: Lưu refresh token từ Google

### 2. Model User

-   **File**: `app/Models/User.php`
-   **Cập nhật**: Thêm các trường Google vào `$fillable` array

### 3. Google Controller

-   **File**: `app/Http/Controllers/Auth/GoogleController.php`
-   **Chức năng**:
    -   `redirectToGoogle()`: Chuyển hướng đến Google OAuth
    -   `handleGoogleCallback()`: Xử lý callback từ Google
    -   `logout()`: Đăng xuất và revoke Google token

### 4. Routes

-   **File**: `routes/auth.php`
-   **Routes đã thêm**:
    -   `GET /auth/google` → `google.redirect`
    -   `GET /auth/google/callback` → `google.callback`
    -   `POST /auth/google/logout` → `google.logout`

### 5. Configuration

-   **File**: `config/services.php`
-   **Thêm cấu hình Google OAuth**

### 6. UI Components

-   **Files**:
    -   `resources/views/livewire/auth/login.blade.php`
    -   `resources/views/livewire/auth/register.blade.php`
-   **Thêm nút đăng nhập/đăng ký Google với thiết kế đẹp**

### 7. Middleware

-   **File**: `app/Http/Middleware/GoogleLogoutMiddleware.php`
-   **Chức năng**: Tự động revoke Google token khi logout

## Cách hoạt động

### Đăng nhập/Đăng ký

1. User click nút "Đăng nhập bằng Google"
2. Chuyển hướng đến Google OAuth consent screen
3. User đăng nhập Google và cấp quyền
4. Google callback về ứng dụng với authorization code
5. Ứng dụng trao đổi code lấy access token
6. Lấy thông tin user từ Google API
7. Tạo hoặc cập nhật user trong database
8. Đăng nhập user vào hệ thống

### Xử lý User

-   **User mới**: Tạo tài khoản mới với thông tin từ Google
-   **User đã tồn tại**: Cập nhật thông tin Google và đăng nhập
-   **Email đã tồn tại**: Liên kết tài khoản hiện tại với Google

### Đăng xuất

1. Revoke Google access token
2. Xóa token trong database
3. Đăng xuất khỏi hệ thống
4. Clear session

## Dependencies đã cài đặt

```json
{
    "laravel/socialite": "^5.23",
    "google/apiclient": "^2.18"
}
```

## Cấu hình cần thiết

### 1. Google Cloud Console

-   Tạo OAuth 2.0 Client ID
-   Cấu hình Authorized Redirect URIs
-   Kích hoạt Google+ API

### 2. Environment Variables

```env
GOOGLE_CLIENT_ID=your_google_client_id
GOOGLE_CLIENT_SECRET=your_google_client_secret
GOOGLE_REDIRECT_URI=http://localhost:8000/auth/google/callback
```

## Bảo mật

### Token Management

-   Access token được lưu trong database
-   Refresh token được lưu để gia hạn access token
-   Token được revoke khi logout

### Error Handling

-   Log lỗi chi tiết
-   Fallback về đăng nhập thông thường
-   User-friendly error messages

### Data Privacy

-   Chỉ lấy thông tin cần thiết từ Google
-   Không lưu trữ thông tin nhạy cảm
-   Tuân thủ GDPR và các quy định bảo mật

## Testing

### Manual Testing

1. Truy cập `/login` hoặc `/register`
2. Click nút Google
3. Đăng nhập Google
4. Kiểm tra user được tạo/cập nhật
5. Test logout và token revocation

### Automated Testing

-   Unit tests cho GoogleController
-   Feature tests cho OAuth flow
-   Integration tests với Google API

## Troubleshooting

### Common Issues

1. **redirect_uri_mismatch**: Kiểm tra URI trong Google Console
2. **invalid_client**: Kiểm tra Client ID/Secret
3. **User không được tạo**: Kiểm tra database permissions
4. **Token không được revoke**: Kiểm tra Google API permissions

### Debug

-   Kiểm tra logs: `storage/logs/laravel.log`
-   Kiểm tra network requests
-   Verify Google Console configuration

## Future Enhancements

### Planned Features

-   [ ] Refresh token tự động
-   [ ] Google Calendar integration
-   [ ] Google Drive integration cho recipe images
-   [ ] Multi-provider OAuth (Facebook, GitHub)

### Performance Optimizations

-   [ ] Cache Google user info
-   [ ] Batch token refresh
-   [ ] Async token revocation

## Support

Nếu gặp vấn đề, vui lòng:

1. Kiểm tra file `GOOGLE_OAUTH_SETUP.md`
2. Xem logs trong `storage/logs/laravel.log`
3. Verify Google Console configuration
4. Contact development team
