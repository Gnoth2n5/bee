# Hướng dẫn sử dụng LocalStorage với SweetAlert

## Tổng quan

Hệ thống này sử dụng **localStorage gốc** để lưu trữ dữ liệu và **SweetAlert** chỉ để hiển thị thông báo đẹp mắt khi thao tác với localStorage.

## Cách hoạt động

### 1. LocalStorage gốc

-   ✅ Sử dụng `localStorage.setItem()`, `localStorage.getItem()`, `localStorage.removeItem()` như bình thường
-   ✅ Dữ liệu được lưu trữ trong localStorage của trình duyệt
-   ✅ Không thay đổi cách hoạt động của localStorage

### 2. SweetAlert cho thông báo

-   ✅ Hiển thị thông báo đẹp mắt khi lưu/xóa dữ liệu
-   ✅ Xác nhận trước khi xóa dữ liệu quan trọng
-   ✅ Toast notifications không gây gián đoạn

## Cách sử dụng

### 1. Sử dụng localStorage trực tiếp (khuyến nghị)

```javascript
// Lưu dữ liệu - sử dụng localStorage gốc
localStorage.setItem("key", "value");

// Đọc dữ liệu - sử dụng localStorage gốc
const value = localStorage.getItem("key");

// Xóa dữ liệu - sử dụng localStorage gốc
localStorage.removeItem("key");
```

### 2. Sử dụng SweetAlertStorage cho thông báo

```javascript
// Lưu dữ liệu với thông báo SweetAlert
SweetAlertStorage.setItem("key", "value");

// Đọc dữ liệu (có thể có thông báo tùy chọn)
const value = SweetAlertStorage.getItem("key", true); // true = hiển thị thông báo

// Xóa dữ liệu với thông báo SweetAlert
SweetAlertStorage.removeItem("key");

// Xóa với xác nhận
SweetAlertStorage.confirmRemove("key", function () {
    console.log("Đã xóa thành công");
});

// Xóa tất cả với xác nhận
SweetAlertStorage.confirmClearAll(function () {
    console.log("Đã xóa tất cả");
});
```

### 3. Sử dụng trong Livewire

```php
// Trong Livewire Component
public function saveData()
{
    $this->dispatch('saveToLocalStorage', [
        'key' => 'user_preferences',
        'value' => json_encode($preferences)
    ]);
}

public function deleteData()
{
    $this->dispatch('deleteFromStorage', ['key' => 'user_preferences']);
}
```

### 4. Sử dụng SweetAlert trực tiếp

```javascript
// Thông báo đơn giản
Swal.fire({
    title: "Thành công!",
    text: "Dữ liệu đã được lưu",
    icon: "success",
    confirmButtonText: "OK",
});

// Thông báo với xác nhận
Swal.fire({
    title: "Xác nhận xóa",
    text: "Bạn có chắc muốn xóa?",
    icon: "warning",
    showCancelButton: true,
    confirmButtonColor: "#d33",
    cancelButtonColor: "#3085d6",
    confirmButtonText: "Xóa",
    cancelButtonText: "Hủy",
}).then((result) => {
    if (result.isConfirmed) {
        // Thực hiện xóa
        localStorage.removeItem("key");
    }
});
```

## Các vị trí đã được cập nhật

### 1. File JavaScript chính

-   `resources/js/app.js`: Thêm SweetAlert và hệ thống SweetAlertStorage

### 2. CSS tùy chỉnh

-   `resources/css/app.css`: Thêm styles cho SweetAlert phù hợp với Flowbite

### 3. Components Livewire

-   `app/Livewire/StorageManager.php`: Component quản lý localStorage
-   `app/Livewire/LocalStorageDemo.php`: Component demo

### 4. Views

-   `resources/views/livewire/storage-manager.blade.php`: Giao diện quản lý
-   `resources/views/livewire/local-storage-demo.blade.php`: Giao diện demo

### 5. Routes

-   `/storage-manager`: Trang quản lý localStorage
-   `/localstorage-demo`: Trang demo

## Tính năng nổi bật

### 1. Toast Notifications

-   Thông báo nhỏ ở góc màn hình
-   Tự động biến mất sau 1-2 giây
-   Hỗ trợ dark mode

### 2. Confirmation Dialogs

-   Xác nhận trước khi xóa dữ liệu
-   Giao diện đẹp với nút xác nhận/hủy
-   Tùy chỉnh màu sắc

### 3. Error Handling

-   Xử lý lỗi khi localStorage không khả dụng
-   Thông báo lỗi thân thiện
-   Fallback mechanisms

### 4. Dark Mode Support

-   Tự động thích ứng với theme
-   Màu sắc phù hợp với dark mode
-   CSS variables cho dễ tùy chỉnh

## Cách test

### 1. Truy cập trang demo

```
http://localhost:8000/localstorage-demo
```

### 2. Truy cập trang quản lý

```
http://localhost:8000/storage-manager
```

### 3. Test các tính năng

-   Lưu dữ liệu mới (sẽ có thông báo SweetAlert)
-   Đọc dữ liệu hiện có
-   Xóa dữ liệu với xác nhận SweetAlert
-   Chuyển đổi theme
-   Xuất dữ liệu ra file JSON

## Lưu ý quan trọng

### 1. Tương thích

-   **localStorage gốc vẫn hoạt động bình thường**
-   SweetAlert chỉ là lớp thông báo
-   Tương thích với tất cả trình duyệt hiện đại

### 2. Performance

-   SweetAlert chỉ hiển thị khi cần thiết
-   Không ảnh hưởng đến hiệu suất localStorage
-   Lazy loading cho các tính năng nâng cao

### 3. Security

-   Không lưu trữ dữ liệu nhạy cảm
-   Validation dữ liệu đầu vào
-   Sanitize output

## Troubleshooting

### 1. SweetAlert không hiển thị

-   Kiểm tra console để xem lỗi
-   Đảm bảo đã build assets: `npm run build`
-   Kiểm tra import SweetAlert trong app.js

### 2. localStorage không hoạt động

-   Kiểm tra quyền truy cập localStorage
-   Thử trong chế độ ẩn danh
-   Kiểm tra cài đặt trình duyệt

### 3. Dark mode không hoạt động

-   Kiểm tra CSS variables
-   Đảm bảo class `dark` được thêm vào HTML
-   Kiểm tra Tailwind config

## Kết luận

Hệ thống localStorage với SweetAlert đã được triển khai hoàn chỉnh, cung cấp:

-   **localStorage gốc** để lưu trữ dữ liệu
-   **SweetAlert** để hiển thị thông báo đẹp mắt
-   Giao diện thống nhất với Flowbite
-   Hỗ trợ dark mode
-   Error handling tốt
-   Documentation đầy đủ

**Lưu ý**: localStorage vẫn hoạt động như bình thường, SweetAlert chỉ được sử dụng để hiển thị thông báo đẹp mắt khi thao tác với dữ liệu.
