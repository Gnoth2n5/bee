@echo off
echo ===========================================
echo    SETUP GOOGLE OAUTH FOR BEEFOOD
echo ===========================================
echo.

echo 🔍 Kiểm tra cấu hình hiện tại...
php artisan google:check-config

echo.
echo ===========================================
echo    HƯỚNG DẪN CẤU HÌNH GOOGLE OAUTH
echo ===========================================
echo.

echo 📝 Bước 1: Truy cập Google Cloud Console
echo https://console.cloud.google.com/
echo.

echo 📝 Bước 2: Tạo OAuth 2.0 Client ID
echo - Vào "APIs & Services" ^> "Credentials"
echo - Click "Create Credentials" ^> "OAuth 2.0 Client IDs"
echo - Chọn "Web application"
echo - Đặt tên: "BeeFood Web Client"
echo.

echo 📝 Bước 3: Cấu hình Authorized Redirect URIs
echo Thêm chính xác URL này:
echo http://127.0.0.1:8000/auth/google/callback
echo.

echo 📝 Bước 4: Copy thông tin credentials
echo - Copy Client ID và Client Secret
echo - Thêm vào file .env
echo.

echo 📝 Bước 5: Cập nhật file .env
echo Thêm các dòng sau vào file .env:
echo.
echo GOOGLE_CLIENT_ID=your_client_id_here
echo GOOGLE_CLIENT_SECRET=your_client_secret_here
echo APP_URL=http://127.0.0.1:8000
echo.

echo 📝 Bước 6: Clear cache và test
echo php artisan config:clear
echo php artisan cache:clear
echo php artisan google:check-config
echo.

echo ⚠️ Lưu ý quan trọng:
echo - Đảm bảo redirect URI khớp chính xác
echo - Không có dấu / thừa ở cuối URL
echo - Copy chính xác Client ID và Client Secret
echo.

echo 🚀 Sau khi cấu hình xong, chạy:
echo php artisan serve
echo.

echo 🌐 Truy cập: http://127.0.0.1:8000/login
echo.

pause 