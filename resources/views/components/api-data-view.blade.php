<div class="space-y-4">
    <div class="bg-gray-50 p-4 rounded-lg">
        <h3 class="text-lg font-semibold text-gray-900 mb-2">Dữ liệu API cho: {{ $cityName }}</h3>
        <p class="text-sm text-gray-600 mb-4">Dữ liệu được lấy từ OpenAPI Vietnam</p>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <!-- Thông tin cơ bản -->
        <div class="bg-white p-4 rounded-lg border">
            <h4 class="font-medium text-gray-900 mb-3">Thông tin cơ bản</h4>
            <dl class="space-y-2">
                <div>
                    <dt class="text-sm font-medium text-gray-500">Tên tỉnh/thành:</dt>
                    <dd class="text-sm text-gray-900">{{ $data['name'] ?? 'N/A' }}</dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-gray-500">Mã tỉnh:</dt>
                    <dd class="text-sm text-gray-900">{{ $data['code'] ?? 'N/A' }}</dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-gray-500">Tên mã:</dt>
                    <dd class="text-sm text-gray-900">{{ $data['codename'] ?? 'N/A' }}</dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-gray-500">Phân cấp hành chính:</dt>
                    <dd class="text-sm text-gray-900">{{ $data['administrative_region'] ?? 'N/A' }}</dd>
                </div>
            </dl>
        </div>

        <!-- Tọa độ -->
        <div class="bg-white p-4 rounded-lg border">
            <h4 class="font-medium text-gray-900 mb-3">Tọa độ địa lý</h4>
            <dl class="space-y-2">
                <div>
                    <dt class="text-sm font-medium text-gray-500">Vĩ độ:</dt>
                    <dd class="text-sm text-gray-900">{{ $data['latitude'] ?? 'N/A' }}</dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-gray-500">Kinh độ:</dt>
                    <dd class="text-sm text-gray-900">{{ $data['longitude'] ?? 'N/A' }}</dd>
                </div>
            </dl>
        </div>
    </div>

    <!-- Thống kê -->
    @if(isset($data['districts']) || isset($data['wards']))
    <div class="bg-white p-4 rounded-lg border">
        <h4 class="font-medium text-gray-900 mb-3">Thống kê hành chính</h4>
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            @if(isset($data['districts']))
            <div class="text-center">
                <div class="text-2xl font-bold text-blue-600">{{ count($data['districts']) }}</div>
                <div class="text-sm text-gray-500">Quận/Huyện</div>
            </div>
            @endif
            
            @if(isset($data['wards']))
            <div class="text-center">
                <div class="text-2xl font-bold text-green-600">{{ count($data['wards']) }}</div>
                <div class="text-sm text-gray-500">Xã/Phường</div>
            </div>
            @endif
        </div>
    </div>
    @endif

    <!-- Dữ liệu JSON -->
    <div class="bg-white p-4 rounded-lg border">
        <h4 class="font-medium text-gray-900 mb-3">Dữ liệu JSON</h4>
        <div class="bg-gray-900 text-green-400 p-3 rounded text-xs overflow-x-auto">
            <pre>{{ json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
        </div>
    </div>

    <!-- Thông tin cập nhật -->
    <div class="bg-blue-50 p-4 rounded-lg">
        <div class="flex items-center">
            <svg class="w-5 h-5 text-blue-500 mr-2" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
            </svg>
            <span class="text-sm text-blue-700">
                Dữ liệu này được lấy từ OpenAPI Vietnam và được cache trong hệ thống.
            </span>
        </div>
    </div>
</div> 