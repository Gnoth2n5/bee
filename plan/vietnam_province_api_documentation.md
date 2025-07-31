# API Tỉnh Thành Việt Nam - BeeFood

## Tổng Quan

Hệ thống sử dụng API OpenAPI Vietnam (`https://provinces.open-api.vn/api/`) để lấy dữ liệu tỉnh thành, quận/huyện, xã/phường của Việt Nam. Dữ liệu được cache trong 24 giờ để tối ưu hiệu suất.

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
        },
        // ...
    ],
    "message": "Lấy danh sách tỉnh thành thành công",
    "total": 63
}
```

### 2. Lấy Thông Tin Chi Tiết Tỉnh

**GET** `/api/vietnam-provinces/{code}`

**Parameters:**
- `code` (integer): Mã tỉnh thành

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
- `provinceCode` (integer): Mã tỉnh thành

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
        },
        // ...
    ],
    "message": "Lấy danh sách quận/huyện thành công",
    "total": 30
}
```

### 4. Lấy Danh Sách Xã/Phường

**GET** `/api/vietnam-provinces/districts/{districtCode}/wards`

**Parameters:**
- `districtCode` (integer): Mã quận/huyện

**Response:**
```json
{
    "success": true,
    "data": [
        {
            "name": "Phường Phúc Xá",
            "code": 1,
            "codename": "phuong_phuc_xa",
            "latitude": 21.0333,
            "longitude": 105.8167
        },
        // ...
    ],
    "message": "Lấy danh sách xã/phường thành công",
    "total": 13
}
```

### 5. Tìm Kiếm Tỉnh Thành

**GET** `/api/vietnam-provinces/search?name={searchTerm}`

**Parameters:**
- `name` (string): Từ khóa tìm kiếm (tối thiểu 2 ký tự)

**Response:**
```json
{
    "success": true,
    "data": [
        {
            "name": "Thành phố Hà Nội",
            "code": 1,
            "codename": "thanh_pho_ha_noi"
        },
        // ...
    ],
    "message": "Tìm kiếm tỉnh thành thành công",
    "total": 9,
    "search_term": "Hà"
}
```

### 6. Lấy Thống Kê

**GET** `/api/vietnam-provinces/stats`

**Response:**
```json
{
    "success": true,
    "data": {
        "total_provinces": 63,
        "last_updated": "2025-07-31 15:50:09",
        "api_status": "online",
        "cache_status": "cached"
    },
    "message": "Lấy thống kê tỉnh thành thành công"
}
```

### 7. Kiểm Tra Trạng Thái API

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

### 8. Xóa Cache

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

- **Cache Time**: 24 giờ (86400 giây)
- **Cache Keys**:
  - `vietnam_provinces`: Danh sách tất cả tỉnh
  - `vietnam_province_{code}`: Thông tin tỉnh cụ thể
  - `vietnam_districts_{provinceCode}`: Quận/huyện của tỉnh
  - `vietnam_wards_{districtCode}`: Xã/phường của quận/huyện

## Error Handling

### HTTP Status Codes
- `200`: Thành công
- `404`: Không tìm thấy dữ liệu
- `500`: Lỗi server

### Error Response Format
```json
{
    "success": false,
    "message": "Mô tả lỗi"
}
```

## Performance

- **Response Time**: ~300ms cho lần gọi đầu tiên
- **Cache Hit**: ~10ms cho các lần gọi tiếp theo
- **Memory Usage**: ~2MB cho toàn bộ dữ liệu tỉnh thành

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
2. Kiểm tra trạng thái API: `php artisan vietnam:test-api --endpoint=connection`
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
fetch('/api/vietnam-provinces/')
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            console.log('Tỉnh thành:', data.data);
        }
    });

// Tìm kiếm tỉnh
fetch('/api/vietnam-provinces/search?name=Hà')
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            console.log('Kết quả tìm kiếm:', data.data);
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