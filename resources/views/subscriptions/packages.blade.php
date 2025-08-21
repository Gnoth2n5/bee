<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Gói Dịch Vụ') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    @if($activeSubscription)
                        <div class="mb-6 p-4 bg-green-100 border border-green-400 text-green-700 rounded">
                            <h3 class="font-bold">Gói hiện tại: {{ ucfirst($activeSubscription->subscription_type) }}</h3>
                            <p>Hết hạn: {{ $activeSubscription->end_date->format('d/m/Y') }}</p>
                            <p>Còn lại: {{ $activeSubscription->getRemainingDays() }} ngày</p>
                        </div>
                    @endif

                    {{-- <!-- VietQR Test Button (dev only) -->
                    @if(app()->environment('local') || request()->has('debug'))
                    <div class="mb-6 p-4 bg-blue-100 border border-blue-400 rounded">
                        <h3 class="font-bold text-blue-800 mb-2">VietQR Test & Debug</h3>
                        <div class="space-x-2">
                            <button onclick="testVietQR()" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
                                Test VietQR API
                            </button>
                            <button onclick="checkAuthStatus()" class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700">
                                Check Auth
                            </button>
                        </div>
                    </div>
                    @endif --}}

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        @foreach($packages as $package)
                            <div class="border rounded-lg p-6 {{ $package['id'] === 'vip' ? 'border-yellow-400 bg-yellow-50' : 'border-gray-200' }}">
                                <div class="text-center">
                                    <h3 class="text-xl font-bold mb-2">{{ $package['name'] }}</h3>
                                    <div class="text-3xl font-bold mb-4">
                                        @if($package['price'] == 0)
                                            Miễn phí
                                        @else
                                            {{ number_format($package['price']) }} VNĐ
                                        @endif
                                    </div>
                                    <p class="text-gray-600 mb-4">{{ $package['duration'] }} ngày</p>
                                </div>

                                <ul class="space-y-2 mb-6">
                                    @foreach($package['features'] as $feature)
                                        <li class="flex items-center">
                                            <svg class="w-4 h-4 text-green-500 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                            </svg>
                                            {{ $feature }}
                                        </li>
                                    @endforeach
                                </ul>

                                <div class="text-center">
                                    <button onclick="purchasePackage('{{ $package['id'] }}')" 
                                    class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-6 rounded-lg transition duration-300">
                                        Mua ngay
                                    </button>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal thanh toán -->
    <div id="paymentModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden z-50">
        <div class="flex items-center justify-center min-h-screen p-4">
            <div class="bg-white rounded-lg max-w-lg w-full mx-auto">
                <div class="p-6">
                    <h3 class="text-lg font-bold mb-4 text-center">Thanh toán</h3>
                    <div id="qrCode" class="mb-4"></div>
                    <div class="text-center mb-4">
                        <p class="font-bold">Số tiền: <span id="amount"></span> VNĐ</p>
                        <p class="text-sm text-gray-600">Mã giao dịch: <span id="transactionId"></span></p>
                    </div>
                    <div class="text-center mb-4">
                        <p class="text-sm text-gray-600 mb-2">Hướng dẫn thanh toán:</p>
                        <ol class="text-sm text-gray-600 text-left list-decimal list-inside space-y-1">
                            <li>Mở ứng dụng ngân hàng trên điện thoại</li>
                            <li>Chọn tính năng quét mã QR</li>
                            <li>Quét mã QR bên trên</li>
                            <li>Kiểm tra thông tin và xác nhận thanh toán</li>
                            <li>Thanh toán sẽ được xử lý tự động</li>
                        </ol>
                    </div>
                    <div class="flex space-x-2">
                        <button onclick="closePaymentModal()" class="flex-1 bg-gray-500 text-white py-2 px-4 rounded">Đóng</button>
                        <button onclick="checkPaymentStatus()" class="flex-1 bg-blue-600 text-white py-2 px-4 rounded">Kiểm tra trạng thái</button>
                        <button onclick="checkVipStatus()" class="flex-1 bg-green-600 text-white py-2 px-4 rounded">Kiểm tra VIP</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // QR Code Generator chuẩn với 3 góc vuông
        class QRCodeGenerator {
            constructor() {
                this.cellSize = 4;
                this.margin = 20;
            }

            // Tạo QR code chuẩn từ text
            generateQR(text) {
                const size = 25; // QR code 25x25
                const modules = this.generateQRMatrix(text, size);
                return this.createSVG(modules, size);
            }

            // Tạo QR matrix chuẩn
            generateQRMatrix(text, size) {
                const modules = [];
                
                // Khởi tạo matrix
                for (let row = 0; row < size; row++) {
                    modules[row] = [];
                    for (let col = 0; col < size; col++) {
                        modules[row][col] = false;
                    }
                }

                // Tạo 3 góc vuông finder patterns
                this.addFinderPattern(modules, 0, 0); // Góc trái trên
                this.addFinderPattern(modules, 0, size - 7); // Góc phải trên
                this.addFinderPattern(modules, size - 7, 0); // Góc trái dưới

                // Thêm timing patterns (đường kẻ)
                for (let i = 8; i < size - 8; i++) {
                    modules[6][i] = i % 2 === 0;
                    modules[i][6] = i % 2 === 0;
                }

                // Thêm data từ text
                this.addDataPattern(modules, text, size);

                return modules;
            }

            // Thêm finder pattern (góc vuông 7x7)
            addFinderPattern(modules, row, col) {
                for (let r = -1; r <= 7; r++) {
                    if (row + r < 0 || row + r >= modules.length) continue;
                    for (let c = -1; c <= 7; c++) {
                        if (col + c < 0 || col + c >= modules[0].length) continue;
                        
                        if ((0 <= r && r <= 6 && (c === 0 || c === 6)) ||
                            (0 <= c && c <= 6 && (r === 0 || r === 6)) ||
                            (2 <= r && r <= 4 && 2 <= c && c <= 4)) {
                            modules[row + r][col + c] = true;
                        } else {
                            modules[row + r][col + c] = false;
                        }
                    }
                }
            }

            // Thêm data pattern từ text
            addDataPattern(modules, text, size) {
                let dataIndex = 0;
                const textBytes = new TextEncoder().encode(text);
                
                for (let row = 0; row < size; row++) {
                    for (let col = 0; col < size; col++) {
                        // Bỏ qua finder patterns và timing patterns
                        if (this.isReservedArea(row, col, size)) continue;
                        
                        if (dataIndex < textBytes.length * 8) {
                            const byteIndex = Math.floor(dataIndex / 8);
                            const bitIndex = dataIndex % 8;
                            const bit = (textBytes[byteIndex] >> (7 - bitIndex)) & 1;
                            modules[row][col] = bit === 1;
                            dataIndex++;
                        } else {
                            // Pattern ngẫu nhiên cho phần còn lại
                            modules[row][col] = (row + col + dataIndex) % 2 === 0;
                            dataIndex++;
                        }
                    }
                }
            }

            // Kiểm tra vùng reserved
            isReservedArea(row, col, size) {
                // Finder patterns
                if ((row < 9 && col < 9) || 
                    (row < 9 && col >= size - 8) || 
                    (row >= size - 8 && col < 9)) {
                    return true;
                }
                
                // Timing patterns
                if (row === 6 || col === 6) {
                    return true;
                }
                
                return false;
            }

            // Tạo SVG từ QR matrix
            createSVG(modules, size) {
                const totalSize = size * this.cellSize + 2 * this.margin;
                
                let svg = `<svg width="${totalSize}" height="${totalSize}" xmlns="http://www.w3.org/2000/svg">`;
                svg += `<rect width="${totalSize}" height="${totalSize}" fill="white"/>`;
                
                for (let row = 0; row < size; row++) {
                    for (let col = 0; col < size; col++) {
                        if (modules[row][col]) {
                            const x = col * this.cellSize + this.margin;
                            const y = row * this.cellSize + this.margin;
                            svg += `<rect x="${x}" y="${y}" width="${this.cellSize}" height="${this.cellSize}" fill="black"/>`;
                        }
                    }
                }
                
                // Thêm logo V đỏ ở giữa
                const centerX = totalSize / 2;
                const centerY = totalSize / 2;
                const logoSize = this.cellSize * 2;
                
                svg += `<circle cx="${centerX}" cy="${centerY}" r="${logoSize}" fill="red"/>`;
                svg += `<text x="${centerX}" y="${centerY + logoSize/3}" text-anchor="middle" font-family="Arial" font-size="${logoSize}" font-weight="bold" fill="white">V</text>`;
                
                svg += '</svg>';
                return svg;
            }
        }



        let currentTransactionId = '';
        const qrGenerator = new QRCodeGenerator();

        async function purchasePackage(packageId) {
            // Kiểm tra xem VietQRPayment đã sẵn sàng chưa
            if (typeof window.VietQRPayment === 'undefined') {
                alert('VietQR Payment service chưa sẵn sàng. Vui lòng tải lại trang.');
                return;
            }

            try {
                // Lấy thông tin gói từ PHP data (hoặc API)
                const packageData = getPackageData(packageId);
                if (!packageData) {
                    alert('Không tìm thấy thông tin gói dịch vụ');
                    return;
                }

                console.log('Processing package:', packageData);

                // Sử dụng VietQR Payment Service
                const result = await window.VietQRPayment.processVipPayment(packageId, packageData.price);
                
                if (result.success) {
                    console.log('VietQR Payment successful:', result);
                    // Modal đã được hiển thị bởi VietQRPayment.processVipPayment
                } else {
                    throw new Error(result.error || 'Lỗi không xác định');
                }
            } catch (error) {
                console.error('Purchase error:', error);
                alert('Có lỗi xảy ra khi tạo QR code: ' + error.message);
            }
        }

        // Helper function để lấy thông tin gói
        function getPackageData(packageId) {
            const packages = {
                'basic': { price: 0, name: 'Basic' },
                'premium': { price: 10000, name: 'Premium' },
                'vip': { price: 99000, name: 'VIP' }
            };
            return packages[packageId] || null;
        }

        // Debug function để check auth status
        async function checkAuthStatus() {
            try {
                console.log('Checking authentication status...');
                
                // Test API endpoint trực tiếp
                const response = await fetch('/api/vietqr/user-id', {
                    method: 'GET',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'X-Requested-With': 'XMLHttpRequest',
                    },
                    credentials: 'same-origin'
                });

                console.log('Response status:', response.status);
                console.log('Response headers:', [...response.headers.entries()]);

                if (response.ok) {
                    const data = await response.json();
                    console.log('Auth check success:', data);
                    alert('✅ Auth OK!\nUser ID: ' + data.user_id + '\nMemo: ' + data.memo);
                } else {
                    const errorText = await response.text();
                    console.log('Auth check failed:', errorText);
                    alert('❌ Auth Failed!\nStatus: ' + response.status + '\nResponse: ' + errorText);
                }
            } catch (error) {
                console.error('Auth check error:', error);
                alert('❌ Auth Check Error:\n' + error.message);
            }
        }

        // Test function cho VietQR
        async function testVietQR() {
            console.log('Testing VietQR integration...');
            
            if (typeof window.VietQRPayment === 'undefined') {
                alert('VietQR Payment service chưa được load. Vui lòng kiểm tra console để debug.');
                return;
            }

            try {
                // Test lấy user ID
                console.log('Step 1: Testing getUserId()');
                const userResult = await window.VietQRPayment.getUserId();
                console.log('User ID result:', userResult);

                // Test tạo QR
                console.log('Step 2: Testing QR generation');
                const qrResult = await window.VietQRPayment.generatePaymentQR({
                    amount: 10000,
                    message: 'Test VietQR BeeFood'
                });
                console.log('QR generation result:', qrResult);

                if (qrResult.success) {
                    alert('✅ VietQR Test thành công!\n\nKiểm tra console để xem chi tiết.\nQR code sẽ được hiển thị trong modal.');
                    window.VietQRPayment.showPaymentModal(qrResult, 'TEST');
                } else {
                    alert('❌ VietQR Test thất bại:\n' + qrResult.message);
                }
            } catch (error) {
                console.error('VietQR Test Error:', error);
                alert('❌ VietQR Test lỗi:\n' + error.message);
            }
        }

                    function showPaymentModal(qrCode, amount, transactionId, isDemo = false, payosInfo = null) {
                console.log('QR Code received:', qrCode ? 'Yes' : 'No');
                console.log('QR Code length:', qrCode ? qrCode.length : 0);
                console.log('QR Code starts with:', qrCode ? qrCode.substring(0, 50) : 'None');
                console.log('PayOS Info:', payosInfo);
                
                // Xóa thông báo demo cũ nếu có
                const existingDemoNotice = document.querySelector('.bg-yellow-100');
                if (existingDemoNotice) {
                    existingDemoNotice.remove();
                }
                
                if (qrCode) {
                    let qrHtml = '';
                    
                                    // Xử lý QR code từ PayOS API hoặc tạo bằng JS
                if (qrCode && (qrCode.startsWith('data:image') || qrCode.startsWith('http'))) {
                    // QR code từ PayOS API (image URL hoặc base64)
                    qrHtml = createPayOSStyleInterface(qrCode, amount, transactionId, payosInfo);
                } else if (qrCode) {
                    // Tạo QR code bằng JavaScript từ chuỗi
                    try {
                        const qrSvg = qrGenerator.generateQR(qrCode);
                        qrHtml = createPayOSStyleInterface(qrSvg, amount, transactionId, payosInfo);
                    } catch (error) {
                        console.error('QR generation error:', error);
                        qrHtml = '<p class="text-red-500">Không thể tạo QR code</p>';
                    }
                } else {
                    qrHtml = '<p class="text-red-500">Không có dữ liệu QR code</p>';
                }
                    
                    document.getElementById('qrCode').innerHTML = qrHtml;
                } else {
                    document.getElementById('qrCode').innerHTML = '<p class="text-red-500">Không thể tạo QR code</p>';
                }
            document.getElementById('amount').textContent = amount.toLocaleString();
            document.getElementById('transactionId').textContent = transactionId;
            currentTransactionId = transactionId;
            
            // Hiển thị thông báo demo mode nếu có
            if (isDemo) {
                const demoNotice = document.createElement('div');
                demoNotice.className = 'bg-yellow-100 border border-yellow-400 text-yellow-700 px-4 py-3 rounded mb-4';
                demoNotice.innerHTML = `
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-yellow-400" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                            </svg>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm">
                                <strong>Chế độ Demo:</strong> VIETQR API hiện không khả dụng. QR code này chỉ để demo. 
                                Bạn có thể test thanh toán bằng cách nhấn "Kiểm tra trạng thái" sau 2 phút.
                            </p>
                        </div>
                    </div>
                `;
                document.getElementById('qrCode').parentNode.insertBefore(demoNotice, document.getElementById('qrCode'));
            }
            
            document.getElementById('paymentModal').classList.remove('hidden');
        }

        function createPayOSStyleInterface(qrCode, amount, transactionId, payosInfo, paymentUrl = null) {
            const defaultPaymentUrl = paymentUrl || 'https://pay.payos.vn/web/1cc1a2b904054810b8290d966bf0db1d';
            
            // Xác định loại QR code
            const isImageUrl = qrCode && (qrCode.startsWith('http') || qrCode.startsWith('data:image'));
            const isSvg = qrCode && qrCode.includes('<svg');
            
            return `
                <div class="max-w-md mx-auto bg-white rounded-lg shadow-lg overflow-hidden">
                    <!-- Payment Link Section -->
                    <div class="bg-gray-50 p-4 border-b">
                        <h3 class="text-sm font-medium text-gray-700 mb-2">Link thanh toán của bạn:</h3>
                        <div class="flex items-center space-x-2">
                            <input type="text" value="${defaultPaymentUrl}" readonly 
                                   class="flex-1 bg-gray-200 text-gray-800 text-xs px-3 py-2 rounded border-0">
                            <button onclick="copyToClipboard('${defaultPaymentUrl}')" 
                                    class="text-gray-500 hover:text-gray-700">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                                </svg>
                            </button>
                            <button onclick="window.open('${defaultPaymentUrl}', '_blank')" 
                                    class="bg-green-500 hover:bg-green-600 text-white text-xs px-3 py-2 rounded font-medium">
                                Mở link
                            </button>
                        </div>
                    </div>

                    <!-- Payment Method Tabs -->
                    <div class="flex border-b">
                        <button class="flex-1 bg-white text-gray-800 px-4 py-3 text-sm font-medium border-b-2 border-blue-500 flex items-center justify-center space-x-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V6a1 1 0 00-1-1H5a1 1 0 00-1 1v1a1 1 0 001 1zm12 0h2a1 1 0 001-1V6a1 1 0 00-1-1h-2a1 1 0 00-1 1v1a1 1 0 001 1zM5 20h2a1 1 0 001-1v-1a1 1 0 00-1-1H5a1 1 0 00-1 1v1a1 1 0 001 1z"></path>
                            </svg>
                            <span>Quét mã QR</span>
                        </button>
                        <button class="flex-1 bg-gray-100 text-gray-500 px-4 py-3 text-sm font-medium flex items-center justify-center space-x-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                            </svg>
                            <span>Chuyển khoản</span>
                        </button>
                    </div>

                    <!-- QR Code Display -->
                    <div class="p-6 text-center">
                        <div class="bg-white p-4 rounded-lg border inline-block">
                            ${isImageUrl ? `<img src="${qrCode}" alt="QR Code" class="w-64 h-64 mx-auto" onerror="this.style.display='none'; this.nextElementSibling.style.display='block';">` : ''}
                            ${isSvg ? `<div class="w-64 h-64 mx-auto">${qrCode}</div>` : ''}
                            ${!isImageUrl && !isSvg ? `<div class="w-64 h-64 mx-auto flex items-center justify-center text-gray-500">QR Code không khả dụng</div>` : ''}
                        </div>
                    </div>

                    <!-- Payment Partner Logos -->
                    <div class="px-6 pb-4">
                        <div class="flex items-center justify-center space-x-4">
                            <div class="flex items-center space-x-1">
                                <span class="text-red-500 font-bold text-sm">VIETQR™</span>
                                <svg class="w-4 h-4 text-red-500" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/>
                                </svg>
                            </div>
                            <div class="flex items-center space-x-1">
                                <span class="text-pink-500 font-bold text-sm">MoMo</span>
                            </div>
                            <div class="flex items-center space-x-1">
                                <span class="text-blue-500 font-bold text-sm">ZaloPay</span>
                            </div>
                        </div>
                    </div>

                    <!-- Instruction Message -->
                    <div class="bg-gray-50 px-6 py-3 text-center">
                        <p class="text-sm text-gray-600">Sau khi thanh toán thành công, vui lòng đợi trong giây lát</p>
                    </div>

                    <!-- VIETQR Payment Info -->
                    ${payosInfo ? `
                        <div class="p-4 bg-blue-50 border-t">
                            <h4 class="font-semibold text-blue-800 mb-2">VIETQR PAYMENT:</h4>
                            <div class="text-sm space-y-1">
                                <p><strong>Bank:</strong> ${payosInfo.bank}</p>
                                <p><strong>Account:</strong> ${payosInfo.account || payosInfo.account_number}</p>
                                <p><strong>Name:</strong> ${payosInfo.name || payosInfo.account_name}</p>
                                <p class="font-bold text-red-600"><strong>Amount:</strong> ${(payosInfo.amount || amount).toLocaleString()} VND</p>
                                <p class="text-gray-600"><strong>Content:</strong> ${payosInfo.content}</p>
                            </div>
                            <div class="mt-3">
                                <a href="#" class="text-blue-600 underline text-sm">Scan with banking app</a>
                            </div>
                            <div class="mt-2 text-xs text-gray-500">
                                Demo Mode - For Testing Only
                            </div>
                        </div>
                    ` : ''}
                </div>
            `;
        }

        function copyToClipboard(text) {
            navigator.clipboard.writeText(text).then(function() {
                // Hiển thị thông báo copy thành công
                const notification = document.createElement('div');
                notification.className = 'fixed top-4 right-4 bg-green-500 text-white px-4 py-2 rounded shadow-lg z-50';
                notification.textContent = 'Đã copy link thanh toán!';
                document.body.appendChild(notification);
                
                setTimeout(() => {
                    notification.remove();
                }, 2000);
            });
        }

        function closePaymentModal() {
            document.getElementById('paymentModal').classList.add('hidden');
        }

        function checkPaymentStatus() {
            fetch('{{ route("subscriptions.verify-payment") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    transaction_id: currentTransactionId
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Kiểm tra trạng thái VIP sau khi thanh toán thành công
                    checkVipStatus();
                } else {
                    alert('Thanh toán chưa hoàn tất. Vui lòng thử lại sau hoặc liên hệ hỗ trợ.');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Có lỗi xảy ra khi kiểm tra trạng thái thanh toán');
            });
        }

        function checkVipStatus() {
            fetch('{{ route("check.vip.status") }}', {
                method: 'GET',
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    if (data.is_vip) {
                        alert('🎉 Thanh toán thành công! Gói VIP đã được kích hoạt.\n\nBạn có thể sử dụng các tính năng VIP ngay bây giờ!');
                        location.reload();
                    } else {
                        alert('Thanh toán thành công nhưng VIP chưa được kích hoạt. Vui lòng liên hệ hỗ trợ.');
                    }
                } else {
                    alert('Có lỗi khi kiểm tra trạng thái VIP: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Có lỗi xảy ra khi kiểm tra trạng thái VIP');
            });
        }
    </script>
</x-app-layout>
