# Hướng dẫn sử dụng Location Storage

## Vấn đề đã được giải quyết

Trước đây, ứng dụng chỉ lưu vị trí vào **session** (server-side), khiến dữ liệu bị mất khi refresh trang hoặc chuyển trang. Bây giờ ứng dụng đã được cập nhật để lưu vị trí vào **localStorage** (client-side).

## Các thay đổi đã thực hiện

### 1. Tạo LocationManager Utility

-   File: `resources/js/app.js`
-   Cung cấp các hàm tiện ích để quản lý vị trí trong localStorage:
    -   `LocationManager.saveLocation(latitude, longitude)` - Lưu vị trí
    -   `LocationManager.getLocation()` - Lấy vị trí đã lưu
    -   `LocationManager.removeLocation()` - Xóa vị trí
    -   `LocationManager.hasValidLocation()` - Kiểm tra có vị trí hợp lệ không
    -   `LocationManager.getCurrentLocation()` - Lấy vị trí hiện tại từ trình duyệt

### 2. Cập nhật các Component

Các component sau đã được cập nhật để sử dụng localStorage:

-   `resources/views/livewire/profile/profile-page.blade.php`
-   `resources/views/livewire/weather-slideshow-simple.blade.php`
-   `resources/views/livewire/weather-recipe-slideshow.blade.php`
-   `resources/views/livewire/weather-recipe-suggestions.blade.php`

### 3. Tạo Storage Manager

-   File: `resources/views/livewire/storage-manager.blade.php`
-   Route: `/storage-manager`
-   Cung cấp giao diện để:
    -   Xem thông tin vị trí đã lưu
    -   Kiểm tra localStorage
    -   Xóa dữ liệu
    -   Xuất dữ liệu

## Cách hoạt động

### 1. Khi lấy vị trí lần đầu

```javascript
// Khi người dùng cho phép lấy vị trí
LocationManager.getCurrentLocation()
    .then((result) => {
        // Vị trí được lưu tự động vào localStorage
        // Và gửi về Livewire component
        @this.setUserLocation(result.latitude, result.longitude);
    });
```

### 2. Khi component được load

```javascript
// Kiểm tra localStorage trước
const savedLocation = LocationManager.getLocation();
if (savedLocation) {
    // Sử dụng vị trí đã lưu nếu còn hợp lệ (trong vòng 1 giờ)
    @this.setUserLocation(savedLocation.latitude, savedLocation.longitude);
} else {
    // Yêu cầu lấy vị trí mới
    showLocationModal();
}
```

### 3. Cấu trúc dữ liệu trong localStorage

```json
{
    "user_location": {
        "latitude": 10.762622,
        "longitude": 106.660172,
        "timestamp": 1703123456789
    }
}
```

## Cách kiểm tra

### 1. Sử dụng Storage Manager

Truy cập `/storage-manager` để:

-   Xem thông tin vị trí đã lưu
-   Kiểm tra tuổi dữ liệu
-   Xóa hoặc xuất dữ liệu

### 2. Sử dụng Developer Tools

1. Mở Developer Tools (F12)
2. Vào tab "Application" (Chrome) hoặc "Storage" (Firefox)
3. Chọn "Local Storage" → domain của bạn
4. Tìm key `user_location`

### 3. Sử dụng Console

```javascript
// Kiểm tra có vị trí không
LocationManager.hasValidLocation();

// Lấy thông tin vị trí
LocationManager.getLocation();

// Xem raw data
localStorage.getItem("user_location");

// Xóa vị trí
LocationManager.removeLocation();
```

## Lợi ích

1. **Không mất dữ liệu khi refresh**: Vị trí được lưu trong localStorage
2. **Tự động sử dụng lại**: Component tự động sử dụng vị trí đã lưu
3. **Hết hạn tự động**: Dữ liệu hết hạn sau 1 giờ
4. **Dễ debug**: Có Storage Manager để kiểm tra
5. **Tương thích tốt**: Vẫn lưu vào session để dùng ở server-side

## Lưu ý

-   Dữ liệu vị trí có thời hạn 1 giờ
-   Nếu dữ liệu hết hạn, sẽ tự động yêu cầu lấy vị trí mới
-   Vị trí được lưu cả trong localStorage và session
-   Có thể xóa dữ liệu thủ công qua Storage Manager

## Troubleshooting

### Vị trí không được lưu

1. Kiểm tra console có lỗi không
2. Kiểm tra quyền truy cập vị trí của trình duyệt
3. Sử dụng Storage Manager để debug

### Vị trí không được sử dụng

1. Kiểm tra localStorage có dữ liệu không
2. Kiểm tra timestamp có hợp lệ không
3. Refresh trang để component load lại

### Xóa dữ liệu

1. Sử dụng Storage Manager
2. Hoặc chạy `LocationManager.removeLocation()` trong console
3. Hoặc xóa trực tiếp trong Developer Tools

