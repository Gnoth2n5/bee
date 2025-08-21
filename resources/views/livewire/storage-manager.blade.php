<div class="p-6 bg-white rounded-lg shadow-md">
    <h2 class="text-2xl font-bold mb-4 text-gray-800">
        <i class="fas fa-database mr-2"></i>
        Quản lý LocalStorage với SweetAlert
    </h2>
    
    <p class="text-gray-600 mb-6">
        Quản lý dữ liệu localStorage với thông báo SweetAlert đẹp mắt
    </p>

    <!-- Location Information Section -->
    <div class="mb-6 p-4 bg-blue-50 rounded-lg border border-blue-200">
        <h3 class="text-lg font-semibold text-blue-800 mb-3">
            <i class="fas fa-map-marker-alt mr-2"></i>
            Thông tin vị trí trong localStorage
        </h3>
        
        <div id="location-info" class="text-sm">
            <!-- Location info will be populated by JavaScript -->
        </div>
        
        <div class="mt-3 space-x-2">
            <button onclick="checkLocation()" class="px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600 transition-colors">
                <i class="fas fa-search mr-1"></i>
                Kiểm tra vị trí
            </button>
            <button onclick="clearLocation()" class="px-4 py-2 bg-red-500 text-white rounded hover:bg-red-600 transition-colors">
                <i class="fas fa-trash mr-1"></i>
                Xóa vị trí
            </button>
        </div>
    </div>

    <!-- LocalStorage Management Section -->
    <div class="mb-6">
        <h3 class="text-lg font-semibold text-gray-800 mb-3">
            <i class="fas fa-cogs mr-2"></i>
            Quản lý localStorage
        </h3>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Key:</label>
                <input type="text" id="demo-key" placeholder="Nhập key..." class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Value:</label>
                <input type="text" id="demo-value" placeholder="Nhập value..." class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>
        </div>
        
        <div class="space-x-2">
            <button onclick="saveDemoData()" class="px-4 py-2 bg-green-500 text-white rounded hover:bg-green-600 transition-colors">
                <i class="fas fa-save mr-1"></i>
                Lưu dữ liệu
            </button>
            <button onclick="loadLocalStorageData()" class="px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600 transition-colors">
                <i class="fas fa-download mr-1"></i>
                Tải dữ liệu
            </button>
            <button onclick="clearAllLocalStorage()" class="px-4 py-2 bg-red-500 text-white rounded hover:bg-red-600 transition-colors">
                <i class="fas fa-trash-alt mr-1"></i>
                Xóa tất cả
            </button>
            <button onclick="exportLocalStorage()" class="px-4 py-2 bg-purple-500 text-white rounded hover:bg-purple-600 transition-colors">
                <i class="fas fa-file-export mr-1"></i>
                Xuất dữ liệu
            </button>
        </div>
    </div>

    <!-- LocalStorage Data Display -->
    <div class="mt-6">
        <h3 class="text-lg font-semibold text-gray-800 mb-3">
            <i class="fas fa-list mr-2"></i>
            Dữ liệu localStorage hiện tại
        </h3>
        <div id="localStorage-data" class="bg-gray-50 p-4 rounded-lg border border-gray-200 max-h-96 overflow-y-auto">
            <!-- Data will be populated by JavaScript -->
        </div>
    </div>

    <!-- Developer Tools Section -->
    <div class="mt-6 p-4 bg-yellow-50 rounded-lg border border-yellow-200">
        <h3 class="text-lg font-semibold text-yellow-800 mb-3">
            <i class="fas fa-tools mr-2"></i>
            Developer Tools
        </h3>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <h4 class="font-medium text-yellow-700 mb-2">Kiểm tra trong Developer Tools:</h4>
                <div class="text-sm text-yellow-600 space-y-1">
                    <p>1. Mở Developer Tools (F12)</p>
                    <p>2. Vào tab "Application" (Chrome) hoặc "Storage" (Firefox)</p>
                    <p>3. Chọn "Local Storage" → "http://localhost:8000"</p>
                    <p>4. Tìm key "user_location"</p>
                </div>
            </div>
            
            <div>
                <h4 class="font-medium text-yellow-700 mb-2">Console Commands:</h4>
                <div class="text-sm text-yellow-600 space-y-1">
                    <p><code>LocationManager.getLocation()</code> - Lấy vị trí</p>
                    <p><code>LocationManager.hasValidLocation()</code> - Kiểm tra có vị trí không</p>
                    <p><code>LocationManager.removeLocation()</code> - Xóa vị trí</p>
                    <p><code>localStorage.getItem('user_location')</code> - Lấy raw data</p>
                </div>
            </div>
        </div>
        
        <div class="mt-4 p-3 bg-white rounded border">
            <h4 class="font-medium text-gray-700 mb-2">Console Output:</h4>
            <div id="console-output" class="text-xs bg-gray-900 text-green-400 p-3 rounded font-mono h-32 overflow-y-auto">
                <!-- Console output will be shown here -->
            </div>
        </div>
    </div>
</div>

<script>
    // Hàm cập nhật thông tin vị trí
    function updateLocationInfo() {
        const locationInfo = document.getElementById('location-info');
        const locationData = LocationManager.getLocation();
        
        if (locationData) {
            const date = new Date(locationData.timestamp);
            const ageMinutes = Math.round((Date.now() - locationData.timestamp) / 1000 / 60);
            const isValid = ageMinutes < 60;
            
            locationInfo.innerHTML = `
                <div class="grid grid-cols-2 gap-4 text-sm">
                    <div>
                        <p><strong>Latitude:</strong> ${locationData.latitude}</p>
                        <p><strong>Longitude:</strong> ${locationData.longitude}</p>
                    </div>
                    <div>
                        <p><strong>Lưu lúc:</strong> ${date.toLocaleString('vi-VN')}</p>
                        <p><strong>Tuổi:</strong> <span class="${isValid ? 'text-green-600' : 'text-red-600'}">${ageMinutes} phút</span></p>
                    </div>
                </div>
                <div class="mt-2">
                    <span class="px-2 py-1 rounded text-xs ${isValid ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'}">
                        ${isValid ? 'Còn hiệu lực' : 'Đã hết hạn'}
                    </span>
                </div>
            `;
            
            logToConsole(`Found location: ${locationData.latitude}, ${locationData.longitude} (${ageMinutes} minutes old)`);
        } else {
            locationInfo.innerHTML = `
                <p class="text-gray-500 italic">Chưa có thông tin vị trí nào được lưu</p>
            `;
            logToConsole('No location data found in localStorage');
        }
    }

    // Hàm log vào console output
    function logToConsole(message) {
        const consoleOutput = document.getElementById('console-output');
        const timestamp = new Date().toLocaleTimeString('vi-VN');
        const logEntry = `[${timestamp}] ${message}\n`;
        
        consoleOutput.textContent += logEntry;
        consoleOutput.scrollTop = consoleOutput.scrollHeight;
        
        // Cũng log vào browser console
        console.log(message);
    }

    // Hàm kiểm tra thông tin vị trí
    function checkLocation() {
        const locationData = LocationManager.getLocation();
        if (locationData) {
            const date = new Date(locationData.timestamp);
            logToConsole(`Location check: ${locationData.latitude}, ${locationData.longitude} saved at ${date.toLocaleString('vi-VN')}`);
            
            Swal.fire({
                title: 'Thông tin vị trí',
                html: `
                    <div class="text-left">
                        <p><strong>Latitude:</strong> ${locationData.latitude}</p>
                        <p><strong>Longitude:</strong> ${locationData.longitude}</p>
                        <p><strong>Thời gian lưu:</strong> ${date.toLocaleString('vi-VN')}</p>
                        <p><strong>Tuổi dữ liệu:</strong> ${Math.round((Date.now() - locationData.timestamp) / 1000 / 60)} phút</p>
                    </div>
                `,
                icon: 'info',
                confirmButtonText: 'OK'
            });
        } else {
            logToConsole('No location data found');
            Swal.fire({
                title: 'Không có dữ liệu vị trí',
                text: 'Chưa có vị trí nào được lưu trong localStorage',
                icon: 'warning',
                confirmButtonText: 'OK'
            });
        }
    }

    // Hàm xóa vị trí
    function clearLocation() {
        Swal.fire({
            title: 'Xác nhận xóa',
            text: 'Bạn có chắc muốn xóa thông tin vị trí khỏi localStorage?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Xóa',
            cancelButtonText: 'Hủy'
        }).then((result) => {
            if (result.isConfirmed) {
                LocationManager.removeLocation();
                updateLocationInfo();
                logToConsole('Location data removed from localStorage');
                Swal.fire(
                    'Đã xóa!',
                    'Thông tin vị trí đã được xóa khỏi localStorage.',
                    'success'
                );
            }
        });
    }

    // Hàm tải dữ liệu từ localStorage
    function loadLocalStorageData() {
        const dataContainer = document.getElementById('localStorage-data');
        let html = '<div class="space-y-2">';
        let itemCount = 0;
        
        for (let i = 0; i < localStorage.length; i++) {
            const key = localStorage.key(i);
            const value = localStorage.getItem(key);
            itemCount++;
            
            try {
                const parsedValue = JSON.parse(value);
                html += `
                    <div class="p-3 bg-white rounded border">
                        <div class="flex justify-between items-start">
                            <div class="flex-1">
                                <p class="font-semibold text-blue-600">${key}</p>
                                <pre class="text-xs text-gray-600 mt-1 overflow-x-auto">${JSON.stringify(parsedValue, null, 2)}</pre>
                            </div>
                            <button onclick="deleteItem('${key}')" class="ml-2 px-2 py-1 bg-red-500 text-white rounded text-xs hover:bg-red-600">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </div>
                `;
            } catch (e) {
                html += `
                    <div class="p-3 bg-white rounded border">
                        <div class="flex justify-between items-start">
                            <div class="flex-1">
                                <p class="font-semibold text-blue-600">${key}</p>
                                <p class="text-sm text-gray-600 mt-1">${value}</p>
                            </div>
                            <button onclick="deleteItem('${key}')" class="ml-2 px-2 py-1 bg-red-500 text-white rounded text-xs hover:bg-red-600">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </div>
                `;
            }
        }
        
        html += '</div>';
        dataContainer.innerHTML = html;
        
        logToConsole(`Loaded ${itemCount} items from localStorage`);
        
        Swal.fire({
            title: 'Thành công!',
            text: 'Đã tải dữ liệu localStorage',
            icon: 'success',
            timer: 1500,
            showConfirmButton: false
        });
    }

    // Hàm xóa item từ localStorage với SweetAlert
    function deleteItem(key) {
        Swal.fire({
            title: 'Xác nhận xóa',
            text: `Bạn có chắc muốn xóa "${key}"?`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Xóa',
            cancelButtonText: 'Hủy'
        }).then((result) => {
            if (result.isConfirmed) {
                localStorage.removeItem(key);
                loadLocalStorageData();
                updateLocationInfo();
                logToConsole(`Deleted item: ${key}`);
                Swal.fire(
                    'Đã xóa!',
                    `Item "${key}" đã được xóa.`,
                    'success'
                );
            }
        });
    }

    // Hàm xóa tất cả localStorage với SweetAlert
    function clearAllLocalStorage() {
        Swal.fire({
            title: 'Xác nhận xóa tất cả',
            text: 'Bạn có chắc muốn xóa tất cả dữ liệu localStorage?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Xóa tất cả',
            cancelButtonText: 'Hủy'
        }).then((result) => {
            if (result.isConfirmed) {
                const itemCount = localStorage.length;
                localStorage.clear();
                loadLocalStorageData();
                updateLocationInfo();
                logToConsole(`Cleared all localStorage (${itemCount} items removed)`);
                Swal.fire(
                    'Đã xóa!',
                    'Tất cả dữ liệu localStorage đã được xóa.',
                    'success'
                );
            }
        });
    }

    // Hàm xuất dữ liệu localStorage
    function exportLocalStorage() {
        const data = {};
        for (let i = 0; i < localStorage.length; i++) {
            const key = localStorage.key(i);
            data[key] = localStorage.getItem(key);
        }
        
        const blob = new Blob([JSON.stringify(data, null, 2)], { type: 'application/json' });
        const url = URL.createObjectURL(blob);
        const link = document.createElement('a');
        link.href = url;
        link.download = 'localStorage-backup.json';
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
        URL.revokeObjectURL(url);
        
        logToConsole(`Exported ${Object.keys(data).length} items to localStorage-backup.json`);
        
        Swal.fire({
            title: 'Thành công!',
            text: 'Dữ liệu localStorage đã được xuất ra file JSON',
            icon: 'success',
            timer: 2000,
            showConfirmButton: false
        });
    }

    // Hàm lưu dữ liệu demo
    function saveDemoData() {
        const key = document.getElementById('demo-key').value.trim();
        const value = document.getElementById('demo-value').value.trim();
        
        if (!key) {
            Swal.fire({
                title: 'Lỗi',
                text: 'Vui lòng nhập key cho dữ liệu demo',
                icon: 'error',
                confirmButtonText: 'OK'
            });
            return;
        }
        
        localStorage.setItem(key, value);
        document.getElementById('demo-key').value = '';
        document.getElementById('demo-value').value = '';
        loadLocalStorageData();
        
        logToConsole(`Saved demo data: ${key} = ${value}`);
        
        Swal.fire({
            title: 'Thành công!',
            text: 'Dữ liệu đã được lưu vào localStorage',
            icon: 'success',
            timer: 1500,
            showConfirmButton: false
        });
    }

    // Khởi tạo khi trang load
    document.addEventListener('DOMContentLoaded', function() {
        logToConsole('Storage Manager initialized');
        updateLocationInfo();
        loadLocalStorageData();
    });
</script> 