<div class="max-w-4xl mx-auto p-6">
    <div class="bg-white rounded-lg shadow-lg dark:bg-gray-800">
        <!-- Header -->
        <div class="p-6 border-b border-gray-200 dark:border-gray-700">
            <h1 class="text-3xl font-bold text-gray-900 dark:text-white mb-2">
                Demo LocalStorage với SweetAlert
            </h1>
            <p class="text-gray-600 dark:text-gray-400">
                Trang demo hiển thị cách sử dụng localStorage với thông báo SweetAlert đẹp mắt
            </p>
        </div>

        <!-- Demo Section -->
        <div class="p-6">
            <!-- Theme Toggle -->
            <div class="mb-8 p-4 bg-gradient-to-r from-blue-50 to-purple-50 rounded-lg dark:from-gray-700 dark:to-gray-600">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">
                    Chuyển đổi Theme
                </h3>
                <div class="flex items-center space-x-4">
                    <span class="text-sm text-gray-600 dark:text-gray-400">
                        Theme hiện tại: <span class="font-medium">{{ $currentTheme }}</span>
                    </span>
                    <button 
                        wire:click="toggleTheme"
                        class="px-4 py-2 bg-gradient-to-r from-blue-500 to-purple-600 text-white rounded-lg hover:from-blue-600 hover:to-purple-700 transition-all duration-200 transform hover:scale-105"
                    >
                        <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"></path>
                        </svg>
                        Chuyển Theme
                    </button>
                </div>
            </div>

            <!-- Data Input Form -->
            <div class="mb-8 p-6 bg-gray-50 rounded-lg dark:bg-gray-700">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">
                    Thêm dữ liệu demo
                </h3>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Key
                        </label>
                        <input 
                            type="text" 
                            wire:model="demoKey"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-600 dark:border-gray-500 dark:text-white"
                            placeholder="Nhập key demo..."
                        >
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Value
                        </label>
                        <input 
                            type="text" 
                            wire:model="demoValue"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-600 dark:border-gray-500 dark:text-white"
                            placeholder="Nhập value demo..."
                        >
                    </div>
                </div>
                
                <div class="flex space-x-4">
                    <button 
                        wire:click="saveDemoData"
                        class="px-6 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 focus:ring-2 focus:ring-green-500 transition-colors duration-200"
                    >
                        <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                        Lưu dữ liệu
                    </button>
                    
                    <button 
                        wire:click="clearDemoData"
                        class="px-6 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 focus:ring-2 focus:ring-red-500 transition-colors duration-200"
                    >
                        <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                        </svg>
                        Xóa tất cả
                    </button>
                </div>
            </div>

            <!-- Demo Examples -->
            <div class="mb-8">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">
                    Ví dụ sử dụng
                </h3>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Example 1 -->
                    <div class="p-4 border border-gray-200 rounded-lg dark:border-gray-600">
                        <h4 class="font-medium text-gray-900 dark:text-white mb-2">Lưu thông tin người dùng</h4>
                        <p class="text-sm text-gray-600 dark:text-gray-400 mb-3">
                            Lưu thông tin người dùng vào localStorage với thông báo SweetAlert
                        </p>
                        <button 
                            onclick="saveUserInfo()"
                            class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 text-sm"
                        >
                            Demo lưu thông tin
                        </button>
                    </div>

                    <!-- Example 2 -->
                    <div class="p-4 border border-gray-200 rounded-lg dark:border-gray-600">
                        <h4 class="font-medium text-gray-900 dark:text-white mb-2">Lưu cài đặt</h4>
                        <p class="text-sm text-gray-600 dark:text-gray-400 mb-3">
                            Lưu cài đặt ứng dụng với xác nhận SweetAlert
                        </p>
                        <button 
                            onclick="saveSettings()"
                            class="px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 text-sm"
                        >
                            Demo lưu cài đặt
                        </button>
                    </div>

                    <!-- Example 3 -->
                    <div class="p-4 border border-gray-200 rounded-lg dark:border-gray-600">
                        <h4 class="font-medium text-gray-900 dark:text-white mb-2">Xóa dữ liệu</h4>
                        <p class="text-sm text-gray-600 dark:text-gray-400 mb-3">
                            Xóa dữ liệu với xác nhận SweetAlert
                        </p>
                        <button 
                            onclick="deleteData()"
                            class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 text-sm"
                        >
                            Demo xóa dữ liệu
                        </button>
                    </div>

                    <!-- Example 4 -->
                    <div class="p-4 border border-gray-200 rounded-lg dark:border-gray-600">
                        <h4 class="font-medium text-gray-900 dark:text-white mb-2">Đọc dữ liệu</h4>
                        <p class="text-sm text-gray-600 dark:text-gray-400 mb-3">
                            Đọc dữ liệu từ localStorage với thông báo
                        </p>
                        <button 
                            onclick="readData()"
                            class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 text-sm"
                        >
                            Demo đọc dữ liệu
                        </button>
                    </div>
                </div>
            </div>

            <!-- Current Data Display -->
            <div class="p-4 bg-gray-50 rounded-lg dark:bg-gray-700">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">
                    Dữ liệu hiện tại trong localStorage
                </h3>
                <div id="currentData" class="text-sm text-gray-600 dark:text-gray-400">
                    <!-- Dữ liệu sẽ được hiển thị ở đây -->
                </div>
            </div>
        </div>
    </div>

    <script>
        // Hàm demo lưu thông tin người dùng
        function saveUserInfo() {
            const userInfo = {
                name: 'Nguyễn Văn A',
                email: 'nguyenvana@example.com',
                preferences: {
                    language: 'vi',
                    notifications: true
                }
            };
            
            // Sử dụng SweetAlertStorage để có thông báo
            SweetAlertStorage.setItem('userInfo', JSON.stringify(userInfo));
            updateCurrentData();
        }

        // Hàm demo lưu cài đặt
        function saveSettings() {
            const settings = {
                theme: 'dark',
                fontSize: 'medium',
                autoSave: true,
                lastUpdated: new Date().toISOString()
            };
            
            // Sử dụng SweetAlertStorage để có thông báo
            SweetAlertStorage.setItem('appSettings', JSON.stringify(settings));
            updateCurrentData();
        }

        // Hàm demo xóa dữ liệu
        function deleteData() {
            SweetAlertStorage.confirmRemove('userInfo', function() {
                updateCurrentData();
            });
        }

        // Hàm demo đọc dữ liệu
        function readData() {
            const userInfo = localStorage.getItem('userInfo');
            const settings = localStorage.getItem('appSettings');
            
            if (userInfo || settings) {
                Swal.fire({
                    title: 'Dữ liệu đã đọc',
                    html: `
                        <div class="text-left">
                            <p><strong>User Info:</strong> ${userInfo || 'Không có'}</p>
                            <p><strong>Settings:</strong> ${settings || 'Không có'}</p>
                        </div>
                    `,
                    icon: 'info',
                    confirmButtonText: 'OK'
                });
            } else {
                Swal.fire({
                    title: 'Không có dữ liệu',
                    text: 'Chưa có dữ liệu nào được lưu',
                    icon: 'warning',
                    confirmButtonText: 'OK'
                });
            }
        }

        // Hàm cập nhật hiển thị dữ liệu hiện tại
        function updateCurrentData() {
            const container = document.getElementById('currentData');
            let html = '<div class="space-y-2">';
            
            for (let i = 0; i < localStorage.length; i++) {
                const key = localStorage.key(i);
                const value = localStorage.getItem(key);
                
                html += `
                    <div class="p-2 bg-white rounded border dark:bg-gray-600 dark:border-gray-500">
                        <strong>${key}:</strong> ${value}
                    </div>
                `;
            }
            
            if (localStorage.length === 0) {
                html += '<p class="text-gray-500">Chưa có dữ liệu nào</p>';
            }
            
            html += '</div>';
            container.innerHTML = html;
        }

        // Lắng nghe sự kiện từ Livewire
        Livewire.on('saveToLocalStorage', (data) => {
            // Sử dụng SweetAlertStorage để có thông báo
            SweetAlertStorage.setItem(data.key, data.value);
            updateCurrentData();
        });

        Livewire.on('clearLocalStorage', () => {
            SweetAlertStorage.confirmClearAll(function() {
                updateCurrentData();
            });
        });

        Livewire.on('showSweetAlert', (data) => {
            Swal.fire({
                title: data.title,
                text: data.message,
                icon: data.type,
                confirmButtonText: 'OK'
            });
        });

        // Cập nhật dữ liệu khi trang load
        document.addEventListener('DOMContentLoaded', function() {
            updateCurrentData();
        });
    </script>
</div> 