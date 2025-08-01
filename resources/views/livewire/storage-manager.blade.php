<div class="p-6 bg-white rounded-lg shadow-md dark:bg-gray-800">
    <div class="mb-6">
        <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-2">
            Quản lý LocalStorage với SweetAlert
        </h2>
        <p class="text-gray-600 dark:text-gray-400">
            Quản lý dữ liệu localStorage với thông báo SweetAlert đẹp mắt
        </p>
    </div>

    <!-- Form thêm dữ liệu -->
    <div class="mb-6 p-4 bg-gray-50 rounded-lg dark:bg-gray-700">
        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">
            Thêm dữ liệu mới
        </h3>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
            <div>
                <label for="storageKey" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    Key
                </label>
                <input 
                    type="text" 
                    id="storageKey"
                    wire:model="storageKey"
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-600 dark:border-gray-500 dark:text-white"
                    placeholder="Nhập key..."
                >
            </div>
            
            <div>
                <label for="storageValue" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                    Value
                </label>
                <input 
                    type="text" 
                    id="storageValue"
                    wire:model="storageValue"
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-600 dark:border-gray-500 dark:text-white"
                    placeholder="Nhập value..."
                >
            </div>
        </div>
        
        <button 
            wire:click="saveItem"
            class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 focus:ring-2 focus:ring-blue-500 transition-colors duration-200"
        >
            <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
            </svg>
            Lưu dữ liệu
        </button>
    </div>

    <!-- Danh sách dữ liệu -->
    <div class="mb-6">
        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">
            Dữ liệu hiện tại
        </h3>
        
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                    <tr>
                        <th scope="col" class="px-6 py-3">Key</th>
                        <th scope="col" class="px-6 py-3">Value</th>
                        <th scope="col" class="px-6 py-3">Thao tác</th>
                    </tr>
                </thead>
                <tbody id="storageTableBody">
                    <!-- Dữ liệu sẽ được load bằng JavaScript -->
                </tbody>
            </table>
        </div>
    </div>

    <!-- Các nút thao tác -->
    <div class="flex flex-wrap gap-4">
        <button 
            onclick="loadStorageData()"
            class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 focus:ring-2 focus:ring-green-500 transition-colors duration-200"
        >
            <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path>
            </svg>
            Tải dữ liệu
        </button>
        
        <button 
            onclick="clearAllStorage()"
            class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 focus:ring-2 focus:ring-red-500 transition-colors duration-200"
        >
            <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
            </svg>
            Xóa tất cả
        </button>
        
        <button 
            onclick="exportStorage()"
            class="px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 focus:ring-2 focus:ring-purple-500 transition-colors duration-200"
        >
            <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
            </svg>
            Xuất dữ liệu
        </button>
    </div>

    <script>
        // Hàm tải dữ liệu từ localStorage
        function loadStorageData() {
            const tableBody = document.getElementById('storageTableBody');
            tableBody.innerHTML = '';
            
            for (let i = 0; i < localStorage.length; i++) {
                const key = localStorage.key(i);
                const value = localStorage.getItem(key);
                
                const row = document.createElement('tr');
                row.className = 'bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600';
                
                row.innerHTML = `
                    <td class="px-6 py-4 font-medium text-gray-900 dark:text-white">${key}</td>
                    <td class="px-6 py-4 text-gray-500 dark:text-gray-400">${value}</td>
                    <td class="px-6 py-4">
                        <button 
                            onclick="deleteStorageItem('${key}')"
                            class="text-red-600 hover:text-red-900 dark:text-red-400 dark:hover:text-red-300"
                        >
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                            </svg>
                        </button>
                    </td>
                `;
                
                tableBody.appendChild(row);
            }
        }

        // Hàm xóa item từ localStorage với SweetAlert
        function deleteStorageItem(key) {
            SweetAlertStorage.confirmRemove(key, function() {
                loadStorageData();
            });
        }

        // Hàm xóa tất cả localStorage với SweetAlert
        function clearAllStorage() {
            SweetAlertStorage.confirmClearAll(function() {
                loadStorageData();
            });
        }

        // Hàm xuất dữ liệu localStorage
        function exportStorage() {
            const data = {};
            for (let i = 0; i < localStorage.length; i++) {
                const key = localStorage.key(i);
                data[key] = localStorage.getItem(key);
            }
            
            const dataStr = JSON.stringify(data, null, 2);
            const dataBlob = new Blob([dataStr], {type: 'application/json'});
            const url = URL.createObjectURL(dataBlob);
            
            const link = document.createElement('a');
            link.href = url;
            link.download = 'localStorage-backup.json';
            link.click();
            
            URL.revokeObjectURL(url);
            
            Swal.fire({
                title: 'Xuất thành công!',
                text: 'Dữ liệu localStorage đã được xuất ra file JSON',
                icon: 'success',
                timer: 2000,
                showConfirmButton: false
            });
        }

        // Tải dữ liệu khi trang load
        document.addEventListener('DOMContentLoaded', function() {
            loadStorageData();
        });

        // Lắng nghe sự kiện từ Livewire
        Livewire.on('saveToStorage', (data) => {
            // Sử dụng SweetAlertStorage để có thông báo
            SweetAlertStorage.setItem(data.key, data.value);
            loadStorageData();
        });

        Livewire.on('deleteFromStorage', (data) => {
            // Sử dụng SweetAlertStorage để có thông báo
            SweetAlertStorage.removeItem(data.key);
            loadStorageData();
        });

        Livewire.on('showSweetAlert', (data) => {
            Swal.fire({
                title: data.title,
                text: data.message,
                icon: data.type,
                confirmButtonText: 'OK'
            });
        });
    </script>
</div> 