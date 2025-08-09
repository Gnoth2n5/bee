# API Tỉnh Thành Việt Nam - BeeFood

## Tổng Quan

Hệ thống sử dụng dữ liệu **34 tỉnh thành mới** theo Nghị quyết sắp xếp hành chính 2025 của Quốc hội. Dữ liệu quận/huyện, xã/phường được lấy từ [OpenAPI Vietnam](https://provinces.open-api.vn/api/). Tọa độ địa lý chính xác được cung cấp cho tất cả tỉnh thành. Dữ liệu được cache trong 24 giờ để tối ưu hiệu suất.

## Dữ Liệu Tỉnh Thành Mới 2025

Theo Nghị quyết sắp xếp hành chính 2025, Việt Nam có **34 đơn vị hành chính cấp tỉnh**:

-   **28 Tỉnh**
-   **6 Thành phố trực thuộc Trung ương**

### Các tỉnh thành mới sau sáp nhập:

-   **Tuyên Quang** (Tuyên Quang + Hà Giang)
-   **Lào Cai** (Lào Cai + Yên Bái)
-   **Thái Nguyên** (Thái Nguyên + Bắc Kạn)
-   **Phú Thọ** (Phú Thọ + Vĩnh Phúc + Hòa Bình)
-   **Bắc Ninh** (Bắc Ninh + Bắc Giang)
-   **Hưng Yên** (Hưng Yên + Thái Bình)
-   **TP. Hải Phòng** (TP. Hải Phòng + Hải Dương)
-   **Ninh Bình** (Ninh Bình + Nam Định + Hà Nam)
-   **Quảng Trị** (Quảng Bình + Quảng Trị)
-   **TP. Đà Nẵng** (TP. Đà Nẵng + Quảng Nam)
-   **Quảng Ngãi** (Quảng Ngãi + Kon Tum)
-   **Gia Lai** (Gia Lai + Bình Định)
-   **Khánh Hòa** (Khánh Hòa + Ninh Thuận)
-   **Lâm Đồng** (Lâm Đồng + Bình Thuận + Đắk Nông)
-   **Đắk Lắk** (Đắk Lắk + Phú Yên)
-   **TP. Hồ Chí Minh** (TP.HCM + Bình Dương + Bà Rịa Vũng Tàu)
-   **Đồng Nai** (Đồng Nai + Bình Phước)
-   **Tây Ninh** (Tây Ninh + Long An)
-   **TP. Cần Thơ** (TP. Cần Thơ + Sóc Trăng + Hậu Giang)
-   **Vĩnh Long** (Vĩnh Long + Bến Tre + Trà Vinh)
-   **Đồng Tháp** (Đồng Tháp + Tiền Giang)
-   **Cà Mau** (Cà Mau + Bạc Liêu)
-   **An Giang** (An Giang + Kiên Giang)

## Base URL

```
https://provinces.open-api.vn/api/
```

## Endpoints

### 1. Lấy Danh Sách Tất Cả Tỉnh Thành

**GET** `/api/vietnam-provinces/`

**Response:**

```json
{
    "success": true,
    "data": [
        {
            "name": "Thành phố Hà Nội",
            "code": 1,
            "codename": "thanh_pho_ha_noi",
            "latitude": 21.0278,
            "longitude": 105.8342
        }
        // ...
    ],
    "message": "Lấy danh sách tỉnh thành thành công",
    "total": 34
}
```

### 2. Lấy Thông Tin Chi Tiết Tỉnh

**GET** `/api/vietnam-provinces/{code}`

**Parameters:**

-   `code` (integer): Mã tỉnh thành

**Response:**

```json
{
    "success": true,
    "data": {
        "name": "Thành phố Hà Nội",
        "code": 1,
        "codename": "thanh_pho_ha_noi",
        "latitude": 21.0278,
        "longitude": 105.8342
    },
    "message": "Lấy thông tin tỉnh thành thành công"
}
```

### 3. Lấy Danh Sách Quận/Huyện

**GET** `/api/vietnam-provinces/{provinceCode}/districts`

**Parameters:**

-   `provinceCode` (integer): Mã tỉnh thành

**Response:**

```json
{
    "success": true,
    "data": [
        {
            "name": "Quận Ba Đình",
            "code": 1,
            "codename": "quan_ba_dinh",
            "latitude": 21.0333,
            "longitude": 105.8167
        }
        // ...
    ],
    "message": "Lấy danh sách quận/huyện thành công",
    "total": 30
}
```

### 4. Lấy Danh Sách Xã/Phường Của Tỉnh

**GET** `/api/vietnam-provinces/{provinceCode}/districts`

**Parameters:**

-   `provinceCode` (integer): Mã tỉnh thành

**Response:**

```json
{
    "success": true,
    "data": [
        {
            "name": "Phường Phúc Xá",
            "code": "01001",
            "codename": "phuong_phuc_xa",
            "latitude": 21.0333,
            "longitude": 105.8167
        }
        // ...
    ],
    "message": "Lấy danh sách xã/phường thành công",
    "total": 30
}
```

### 5. Lấy Tất Cả Xã/Phường Với Tọa Độ

**GET** `/api/vietnam-provinces/communes-with-coordinates`

**Response:**

```json
{
    "success": true,
    "data": [
        {
            "name": "Phường Phúc Xá",
            "code": "01001",
            "codename": "phuong_phuc_xa",
            "latitude": 21.0333,
            "longitude": 105.8167,
            "province": {
                "name": "Thành phố Hà Nội",
                "code": "01"
            }
        }
        // ...
    ],
    "message": "Lấy danh sách xã/phường với tọa độ thành công",
    "total": 3970
}
```

### 6. Tìm Kiếm Tỉnh Thành

**GET** `/api/vietnam-provinces/search?name={searchTerm}`

**Parameters:**

-   `name` (string): Từ khóa tìm kiếm (tối thiểu 2 ký tự)

**Response:**

```json
{
    "success": true,
    "data": [
        {
            "name": "Thành phố Hà Nội",
            "code": 1,
            "codename": "thanh_pho_ha_noi"
        }
        // ...
    ],
    "message": "Tìm kiếm tỉnh thành thành công",
    "total": 9,
    "search_term": "Hà"
}
```

### 7. Lấy Thống Kê

**GET** `/api/vietnam-provinces/stats`

**Response:**

```json
{
    "success": true,
    "data": {
        "total_provinces": 34,
        "last_updated": "2025-07-31 15:50:09",
        "api_status": "online",
        "cache_status": "cached"
    },
    "message": "Lấy thống kê tỉnh thành thành công"
}
```

### 8. Kiểm Tra Trạng Thái API

**GET** `/api/vietnam-provinces/health`

**Response:**

```json
{
    "success": true,
    "data": {
        "status": "online",
        "timestamp": "2025-07-31T08:50:09.000000Z",
        "message": "API hoạt động bình thường"
    },
    "message": "Kiểm tra trạng thái API thành công"
}
```

### 9. Xóa Cache

**DELETE** `/api/vietnam-provinces/cache`

**Response:**

```json
{
    "success": true,
    "message": "Đã xóa cache tỉnh thành thành công"
}
```

## Sử Dụng Trong Code

### Service Class

```php
use App\Services\VietnamProvinceService;

class YourController extends Controller
{
    protected $provinceService;

    public function __construct(VietnamProvinceService $provinceService)
    {
        $this->provinceService = $provinceService;
    }

    public function getProvinces()
    {
        $provinces = $this->provinceService->getAllProvinces();
        return response()->json($provinces);
    }

    public function getProvinceByCode($code)
    {
        $province = $this->provinceService->getProvinceByCode($code);
        return response()->json($province);
    }

    public function searchProvinces($name)
    {
        $provinces = $this->provinceService->searchProvincesByName($name);
        return response()->json($provinces);
    }
}
```

### Artisan Commands

#### Đồng Bộ Dữ Liệu

```bash
# Đồng bộ dữ liệu tỉnh thành
php artisan vietnam:sync-provinces

# Force đồng bộ (ghi đè dữ liệu cũ)
php artisan vietnam:sync-provinces --force

# Test kết nối API
php artisan vietnam:sync-provinces --test
```

#### Test API

```bash
# Test tất cả endpoints
php artisan vietnam:test-api

# Test endpoint cụ thể
php artisan vietnam:test-api --endpoint=provinces
php artisan vietnam:test-api --endpoint=province --code=01
php artisan vietnam:test-api --endpoint=districts --code=01
php artisan vietnam:test-api --endpoint=wards --code=001
php artisan vietnam:test-api --endpoint=search
php artisan vietnam:test-api --endpoint=stats
```

## Cấu Trúc Dữ Liệu

### Tỉnh Thành

```json
{
    "name": "Tên tỉnh thành",
    "code": "Mã số",
    "codename": "Tên mã",
    "latitude": "Vĩ độ",
    "longitude": "Kinh độ"
}
```

### Quận/Huyện

```json
{
    "name": "Tên quận/huyện",
    "code": "Mã số",
    "codename": "Tên mã",
    "latitude": "Vĩ độ",
    "longitude": "Kinh độ"
}
```

### Xã/Phường

```json
{
    "name": "Tên xã/phường",
    "code": "Mã số",
    "codename": "Tên mã",
    "latitude": "Vĩ độ",
    "longitude": "Kinh độ"
}
```

## Cache Strategy

-   **Cache Time**: 24 giờ (86400 giây)
-   **Cache Keys**:
    -   `vietnam_provinces`: Danh sách tất cả tỉnh
    -   `vietnam_province_{code}`: Thông tin tỉnh cụ thể
    -   `vietnam_districts_{provinceCode}`: Xã/phường của tỉnh
    -   `vietnam_communes_with_coordinates`: Tất cả xã/phường với tọa độ

## Error Handling

### HTTP Status Codes

-   `200`: Thành công
-   `404`: Không tìm thấy dữ liệu
-   `500`: Lỗi server

### Error Response Format

```json
{
    "success": false,
    "message": "Mô tả lỗi"
}
```

## Performance

-   **Response Time**: ~300ms cho lần gọi đầu tiên
-   **Cache Hit**: ~10ms cho các lần gọi tiếp theo
-   **Memory Usage**: ~2MB cho toàn bộ dữ liệu tỉnh thành

## Monitoring

### Health Check

```bash
curl -X GET "http://your-domain.com/api/vietnam-provinces/health"
```

### Stats Check

```bash
curl -X GET "http://your-domain.com/api/vietnam-provinces/stats"
```

## Troubleshooting

### API Không Hoạt Động

1. Kiểm tra kết nối internet
2. Kiểm tra trạng thái Cas AddressKit API: `php artisan vietnam:test-api --endpoint=connection`
3. Xóa cache: `php artisan vietnam:test-api --endpoint=clear-cache`

### Dữ Liệu Không Cập Nhật

1. Chạy đồng bộ: `php artisan vietnam:sync-provinces --force`
2. Xóa cache: `DELETE /api/vietnam-provinces/cache`

### Lỗi Cache

1. Xóa cache: `php artisan cache:clear`
2. Kiểm tra cấu hình cache trong `config/cache.php`

## Best Practices

1. **Sử dụng Cache**: Luôn sử dụng cache để tối ưu hiệu suất
2. **Error Handling**: Luôn xử lý lỗi khi gọi API
3. **Validation**: Validate dữ liệu trước khi sử dụng
4. **Monitoring**: Theo dõi trạng thái API định kỳ
5. **Backup**: Backup dữ liệu tỉnh thành trong database

## Ví Dụ Sử Dụng

### Frontend (JavaScript)

```javascript
// Lấy danh sách tỉnh
fetch("/api/vietnam-provinces/")
    .then((response) => response.json())
    .then((data) => {
        if (data.success) {
            console.log("Tỉnh thành:", data.data);
        }
    });

// Lấy xã/phường với tọa độ
fetch("/api/vietnam-provinces/communes-with-coordinates")
    .then((response) => response.json())
    .then((data) => {
        if (data.success) {
            console.log("Xã/phường với tọa độ:", data.data);
        }
    });

// Tìm kiếm tỉnh
fetch("/api/vietnam-provinces/search?name=Hà")
    .then((response) => response.json())
    .then((data) => {
        if (data.success) {
            console.log("Kết quả tìm kiếm:", data.data);
        }
    });
```

### Backend (PHP)

```php
// Trong controller
public function getProvinces()
{
    $provinces = $this->provinceService->getAllProvinces();
    return view('provinces.index', compact('provinces'));
}

// Trong view
@foreach($provinces as $province)
    <option value="{{ $province['code'] }}">{{ $province['name'] }}</option>
@endforeach
```
