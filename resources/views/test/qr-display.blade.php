<x-app-layout>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h2 class="text-2xl font-bold mb-4">Test QR Code Thanh Toán</h2>
                    
                    <div class="mb-4">
                        <button onclick="testQR()" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                            Tạo QR Code PayOS Style
                        </button>
                        <button onclick="testPayOSSimple()" class="bg-purple-500 hover:bg-purple-700 text-white font-bold py-2 px-4 rounded ml-2">
                            Test PayOS API
                        </button>
                        <button onclick="showPayOSInterface()" class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded ml-2">
                            Hiển thị Giao diện PayOS
                        </button>
                    </div>

                    <div id="qrResult" class="mb-4"></div>
                    <div id="qrInfo" class="mb-4"></div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function testQR() {
            fetch('/subscriptions/purchase', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    package_id: 'vip',
                    payment_method: 'vietqr'
                })
            })
            .then(response => response.json())
            .then(data => {
                console.log('Response:', data);
                
                const qrResult = document.getElementById('qrResult');
                const qrInfo = document.getElementById('qrInfo');
                
                if (data.success) {
                    if (data.qr_code) {
                        qrResult.innerHTML = `
                            <div class="mb-4">
                                <h3 class="font-bold text-lg mb-2">QR Code:</h3>
                                <img src="${data.qr_code}" alt="QR Code" class="mx-auto max-w-xs border">
                            </div>
                        `;
                        
                        qrInfo.innerHTML = `
                            <div class="bg-green-100 p-3 rounded">
                                <p><strong>Số tiền:</strong> ${data.amount.toLocaleString()} VNĐ</p>
                                <p><strong>Mã giao dịch:</strong> ${data.transaction_id}</p>
                                <p><strong>Trạng thái:</strong> ${data.message}</p>
                                ${data.payment_url ? `<p><strong>Link thanh toán:</strong> <a href="${data.payment_url}" target="_blank" class="text-blue-600 hover:underline">${data.payment_url}</a></p>` : ''}
                                ${data.payos_info ? `
                                    <div class="mt-2 p-2 bg-gray-100 rounded">
                                        <p><strong>Ngân hàng:</strong> ${data.payos_info.bank}</p>
                                        <p><strong>Tài khoản:</strong> ${data.payos_info.account_number}</p>
                                        <p><strong>Chủ tài khoản:</strong> ${data.payos_info.account_name}</p>
                                        <p><strong>Nội dung:</strong> ${data.payos_info.content}</p>
                                    </div>
                                ` : ''}
                            </div>
                        `;
                    } else {
                        qrResult.innerHTML = '<p class="text-red-500">Không có QR code trong response</p>';
                        qrInfo.innerHTML = `<pre class="bg-gray-100 p-2 rounded text-xs">${JSON.stringify(data, null, 2)}</pre>`;
                    }
                } else {
                    qrResult.innerHTML = '<p class="text-red-500">Lỗi: ' + data.message + '</p>';
                    qrInfo.innerHTML = `<pre class="bg-red-100 p-2 rounded text-xs">${JSON.stringify(data, null, 2)}</pre>`;
                }
            })
            .catch(error => {
                console.error('Error:', error);
                document.getElementById('qrResult').innerHTML = '<p class="text-red-500">Lỗi kết nối: ' + error.message + '</p>';
            });
        }

        function showPayOSInterface() {
            const qrResult = document.getElementById('qrResult');
            const qrInfo = document.getElementById('qrInfo');

            // Clear previous results
            qrResult.innerHTML = '';
            qrInfo.innerHTML = '';

            // Tạo thông tin VIETQR từ ảnh
            const vietqrData = {
                bank: 'Quan Doi (MB)',
                account: 'VQRQADWLF2921',
                name: 'NGUYEN NGOC TUNG',
                amount: 1000,
                content: 'TXN_glYTpLqWMv'
            };

            // Tạo chuỗi VIETQR theo format chuẩn
            const vietqrString = createVietQrString(vietqrData);
            
            // Tạo QR code từ chuỗi VIETQR
            const qrImageUrl = `https://chart.googleapis.com/chart?chs=300x300&cht=qr&chl=${encodeURIComponent(vietqrString)}`;
            
            // Tạo giao diện PayOS style với QR VIETQR
            qrResult.innerHTML = createPayOSStyleInterface(qrImageUrl, vietqrData.amount, 'TXN_glYTpLqWMv', vietqrData);

            qrInfo.innerHTML = `
                <div class="bg-green-100 p-3 rounded">
                    <h4 class="font-bold mb-2">Thông tin VIETQR:</h4>
                    <p><strong>Ngân hàng:</strong> ${vietqrData.bank}</p>
                    <p><strong>Số tài khoản:</strong> ${vietqrData.account}</p>
                    <p><strong>Chủ tài khoản:</strong> ${vietqrData.name}</p>
                    <p><strong>Số tiền:</strong> ${vietqrData.amount.toLocaleString()} VNĐ</p>
                    <p><strong>Nội dung:</strong> ${vietqrData.content}</p>
                    <div class="mt-2 p-2 bg-yellow-100 rounded">
                        <p class="text-sm"><strong>Lưu ý:</strong> Nhập chính xác số tiền <strong>${vietqrData.amount.toLocaleString()}</strong> khi chuyển khoản</p>
                    </div>
                </div>
            `;
        }

        function createPayOSStyleInterface(qrImageUrl, amount, transactionId, payosInfo, paymentUrl = null) {
            const defaultPaymentUrl = paymentUrl || 'https://pay.payos.vn/web/1cc1a2b904054810b8290d966bf0db1d';
            
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
                            <img src="${qrImageUrl}" alt="QR Code" class="w-64 h-64 mx-auto">
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

                    <!-- Payment Info -->
                    ${payosInfo ? `
                        <div class="p-4 bg-blue-50 border-t">
                            <h4 class="font-semibold text-blue-800 mb-2">Thông tin chuyển khoản:</h4>
                            <div class="text-sm space-y-1">
                                <p><strong>Ngân hàng:</strong> ${payosInfo.bank}</p>
                                <p><strong>Số tài khoản:</strong> ${payosInfo.account || payosInfo.account_number}</p>
                                <p><strong>Chủ tài khoản:</strong> ${payosInfo.name || payosInfo.account_name}</p>
                                <p><strong>Số tiền:</strong> ${(payosInfo.amount || amount).toLocaleString()} VND</p>
                                <p><strong>Nội dung:</strong> ${payosInfo.content}</p>
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

        function testPayOSSimple() {
            fetch('/test-payos-simple', {
                method: 'GET',
                headers: {
                    'Accept': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                console.log('PayOS Simple Test Response:', data);
                
                const qrResult = document.getElementById('qrResult');
                const qrInfo = document.getElementById('qrInfo');
                
                qrResult.innerHTML = `
                    <div class="mb-4">
                        <h3 class="font-bold text-lg mb-2">Test PayOS API:</h3>
                        <pre class="bg-gray-100 p-4 rounded text-xs overflow-auto">${JSON.stringify(data, null, 2)}</pre>
                    </div>
                `;
                
                qrInfo.innerHTML = `
                    <div class="bg-blue-100 p-3 rounded">
                        <h4 class="font-bold mb-2">Thông tin cấu hình:</h4>
                        <p><strong>Demo Mode:</strong> ${data.config.use_demo_mode ? 'Bật' : 'Tắt'}</p>
                        <p><strong>Client ID:</strong> ${data.config.client_id}</p>
                        <p><strong>Success:</strong> ${data.success ? 'Có' : 'Không'}</p>
                        ${data.success && data.data.qr_code ? '<p class="text-green-600 font-bold">✅ Có QR Code!</p>' : ''}
                        ${!data.success ? '<p class="text-red-600 font-bold">❌ Lỗi: ' + (data.data.message || 'Unknown error') + '</p>' : ''}
                    </div>
                `;
            })
            .catch(error => {
                console.error('Error:', error);
                document.getElementById('qrResult').innerHTML = '<p class="text-red-500">Lỗi kết nối: ' + error.message + '</p>';
            });
        }

        function createVietQrString(data) {
            // Format VIETQR theo chuẩn
            const vietqrFormat = {
                bankBin: '970422', // Mã ngân hàng MB
                accountNo: data.account,
                amount: data.amount,
                content: data.content
            };
            
            // Tạo chuỗi VIETQR theo format chuẩn
            return `00020101021238580010A000000727012900069704220112${vietqrFormat.accountNo}52045${vietqrFormat.bankBin}5303704540${vietqrFormat.amount.toString().length}${vietqrFormat.amount}5802VN62${vietqrFormat.content.length}${vietqrFormat.content}6304`;
        }
    </script>
</x-app-layout>
