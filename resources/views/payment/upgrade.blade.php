@extends('layouts.app')

@section('title', 'Nâng cấp VIP')

@section('content')
<!-- Hero Section -->
<section class="relative min-h-screen bg-gradient-to-br from-slate-900 via-slate-800 to-slate-900 overflow-hidden">
    <!-- Background Decorations -->
    <div class="absolute inset-0 overflow-hidden pointer-events-none">
        <div class="absolute top-20 left-10 w-32 h-32 bg-gradient-to-r from-yellow-400/20 to-orange-500/20 rounded-full blur-3xl animate-pulse"></div>
        <div class="absolute bottom-20 right-20 w-40 h-40 bg-gradient-to-r from-orange-500/20 to-red-500/20 rounded-full blur-3xl animate-bounce" style="animation-delay: 1s"></div>
        <div class="absolute top-1/2 left-1/2 w-24 h-24 bg-gradient-to-r from-purple-500/20 to-pink-500/20 rounded-full blur-2xl animate-ping" style="animation-delay: 2s"></div>
    </div>

    <div class="relative z-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-20 pb-16">
        <!-- Header -->
        <div class="text-center mb-16">
            <div class="inline-flex items-center px-4 py-2 rounded-full bg-gradient-to-r from-yellow-100/20 to-orange-100/20 border border-yellow-400/30 mb-6 backdrop-blur-sm">
                <svg class="w-5 h-5 mr-2 text-yellow-400 animate-spin" style="animation-duration: 3s" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                </svg>
                <span class="text-sm font-semibold text-yellow-400">Thành viên VIP</span>
            </div>
            
            <h1 class="text-4xl md:text-6xl lg:text-7xl font-black text-white mb-6">
                Nâng cấp lên 
                <span class="bg-gradient-to-r from-yellow-400 to-orange-500 bg-clip-text text-transparent">
                    VIP
                </span>
            </h1>
            
            <p class="text-xl text-gray-300 max-w-3xl mx-auto leading-relaxed mb-8">
                Trở thành thành viên VIP để truy cập tính năng AI Chat và nhiều tính năng độc quyền khác
            </p>

            <!-- Price Badge -->
            <div class="inline-flex items-center bg-gradient-to-r from-yellow-400 to-orange-500 rounded-full px-8 py-4 shadow-2xl">
                <span class="text-3xl font-black text-white mr-3">{{ number_format($pricing['vip']['price']) }}đ</span>
                <span class="text-white/90 font-medium">{{ $pricing['vip']['duration'] }}</span>
            </div>
        </div>
    </div>
</section>

<!-- Features Section -->
<section class="py-20 bg-gradient-to-br from-white via-gray-50 to-blue-50 dark:from-slate-900 dark:via-slate-800 dark:to-slate-900 relative overflow-hidden">
    <!-- Background Decorations -->
    <div class="absolute inset-0 overflow-hidden pointer-events-none">
        <div class="absolute top-20 left-10 w-32 h-32 bg-gradient-to-r from-emerald-200 to-green-200 dark:from-emerald-800/30 dark:to-green-800/30 rounded-full blur-3xl opacity-30 animate-pulse"></div>
        <div class="absolute bottom-20 right-20 w-40 h-40 bg-gradient-to-r from-purple-200 to-indigo-200 dark:from-purple-800/30 dark:to-indigo-800/30 rounded-full blur-3xl opacity-20 animate-bounce" style="animation-delay: 1s"></div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
        <div class="text-center mb-16">
            <h2 class="text-3xl md:text-4xl lg:text-5xl font-black text-gray-800 dark:text-white mb-6">
                Quyền Lợi 
                <span class="bg-gradient-to-r from-yellow-500 to-orange-600 bg-clip-text text-transparent">
                    VIP
                </span>
            </h2>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8 mb-16">
            <!-- AI Chat Bot -->
            <div class="group bg-white dark:bg-slate-800 rounded-2xl p-8 shadow-xl hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-2 border border-gray-100 dark:border-slate-700">
                <div class="w-16 h-16 bg-gradient-to-r from-blue-500 to-purple-600 rounded-2xl flex items-center justify-center mb-6 group-hover:scale-110 transition-transform duration-300">
                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                    </svg>
                </div>
                <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-3">🤖 AI Chat Bot</h3>
                <p class="text-gray-600 dark:text-gray-300">Trò chuyện với AI để nhận gợi ý công thức nấu ăn thông minh</p>
            </div>

            <!-- Meal Plan -->
            <div class="group bg-white dark:bg-slate-800 rounded-2xl p-8 shadow-xl hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-2 border border-gray-100 dark:border-slate-700">
                <div class="w-16 h-16 bg-gradient-to-r from-green-500 to-teal-600 rounded-2xl flex items-center justify-center mb-6 group-hover:scale-110 transition-transform duration-300">
                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                </div>
                <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-3">📋 Meal Plan Cá Nhân Hóa</h3>
                <p class="text-gray-600 dark:text-gray-300">Lập kế hoạch bữa ăn thông minh dựa trên sở thích cá nhân</p>
            </div>

            <!-- Shopping List -->
            <div class="group bg-white dark:bg-slate-800 rounded-2xl p-8 shadow-xl hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-2 border border-gray-100 dark:border-slate-700">
                <div class="w-16 h-16 bg-gradient-to-r from-orange-500 to-red-600 rounded-2xl flex items-center justify-center mb-6 group-hover:scale-110 transition-transform duration-300">
                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4m0 0L7 13m0 0l-2.5 5M7 13l2.5 5m6-5v5a2 2 0 01-2 2H9a2 2 0 01-2-2v-5m6 0V9a2 2 0 00-2-2H9a2 2 0 00-2 2v4.01"/>
                    </svg>
                </div>
                <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-3">🛒 Shopping List Thông Minh</h3>
                <p class="text-gray-600 dark:text-gray-300">Tạo danh sách mua sắm tự động từ công thức nấu ăn</p>
            </div>

            <!-- Recipe Management -->
            <div class="group bg-white dark:bg-slate-800 rounded-2xl p-8 shadow-xl hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-2 border border-gray-100 dark:border-slate-700">
                <div class="w-16 h-16 bg-gradient-to-r from-purple-500 to-pink-600 rounded-2xl flex items-center justify-center mb-6 group-hover:scale-110 transition-transform duration-300">
                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                    </svg>
                </div>
                <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-3">📝 Quản Lý Bài Viết Nâng Cao</h3>
                <p class="text-gray-600 dark:text-gray-300">Công cụ viết và quản lý bài viết chuyên nghiệp</p>
            </div>

            <!-- Priority Support -->
            <div class="group bg-white dark:bg-slate-800 rounded-2xl p-8 shadow-xl hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-2 border border-gray-100 dark:border-slate-700">
                <div class="w-16 h-16 bg-gradient-to-r from-yellow-500 to-orange-600 rounded-2xl flex items-center justify-center mb-6 group-hover:scale-110 transition-transform duration-300">
                    <svg class="w-8 h-8 text-white" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                    </svg>
                </div>
                <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-3">⭐ Ưu Tiên Hỗ Trợ</h3>
                <p class="text-gray-600 dark:text-gray-300">Được ưu tiên hỗ trợ khi gặp vấn đề</p>
            </div>

            <!-- Exclusive Content -->
            <div class="group bg-white dark:bg-slate-800 rounded-2xl p-8 shadow-xl hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-2 border border-gray-100 dark:border-slate-700">
                <div class="w-16 h-16 bg-gradient-to-r from-indigo-500 to-blue-600 rounded-2xl flex items-center justify-center mb-6 group-hover:scale-110 transition-transform duration-300">
                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                    </svg>
                </div>
                <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-3">🔒 Nội Dung Độc Quyền</h3>
                <p class="text-gray-600 dark:text-gray-300">Truy cập các tính năng và nội dung chỉ dành riêng cho VIP</p>
            </div>
        </div>
    </div>
</section>

<!-- Payment Section -->
<section class="py-20 bg-gradient-to-br from-slate-900 via-slate-800 to-slate-900 relative overflow-hidden">
    <!-- Background Decorations -->
    <div class="absolute inset-0 overflow-hidden pointer-events-none">
        <div class="absolute top-20 left-10 w-32 h-32 bg-gradient-to-r from-green-400/20 to-teal-500/20 rounded-full blur-3xl animate-pulse"></div>
        <div class="absolute bottom-20 right-20 w-40 h-40 bg-gradient-to-r from-blue-500/20 to-purple-500/20 rounded-full blur-3xl animate-bounce" style="animation-delay: 1s"></div>
    </div>

    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
        <div class="text-center mb-12">
            <h2 class="text-3xl md:text-4xl font-black text-white mb-4">
                Hướng Dẫn 
                <span class="bg-gradient-to-r from-green-400 to-blue-500 bg-clip-text text-transparent">
                    Thanh Toán
                </span>
            </h2>
            <p class="text-gray-300">Chỉ cần 2.000đ để trở thành thành viên VIP</p>
        </div>

        <!-- Payment Card -->
        <div class="bg-white/10 backdrop-blur-lg rounded-3xl p-8 border border-white/20 shadow-2xl">
            <!-- Alert -->
            <div class="bg-yellow-500/20 border border-yellow-500/30 text-yellow-200 rounded-2xl p-4 mb-8 text-center">
                <span class="font-medium">Bạn chưa là VIP. Quét QR hoặc chuyển khoản để nâng cấp ngay!</span>
            </div>
            
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 items-center">
                <!-- QR Code Section -->
                <div class="text-center">
                    <div class="bg-white rounded-2xl p-6 shadow-xl inline-block">
                        <img src="https://qr.sepay.vn/img?bank=MBBank&acc=0975821009&template=compact&amount={{ $paymentData['amount'] }}&des={{ $paymentData['description'] }}"
                            class="w-80 h-80 mx-auto" alt="QR Code thanh toán" />
                    </div>
                    <p class="text-gray-300 mt-4 font-medium">Quét QR để thanh toán nhanh</p>
                </div>
                
                <!-- Bank Information Section -->
                <div class="space-y-6">
                    <h3 class="text-2xl font-bold text-white mb-6">Thông tin chuyển khoản</h3>
                    
                    <div class="space-y-4">
                        <div class="flex justify-between items-center bg-white/5 rounded-xl p-4 border border-white/10">
                            <span class="text-gray-300">Ngân hàng:</span>
                            <span class="font-bold text-white">MBBank</span>
                        </div>
                        <div class="flex justify-between items-center bg-white/5 rounded-xl p-4 border border-white/10">
                            <span class="text-gray-300">Số tài khoản:</span>
                            <span class="font-mono font-bold text-white">0975821009</span>
                        </div>
                        <div class="flex justify-between items-center bg-white/5 rounded-xl p-4 border border-white/10">
                            <span class="text-gray-300">Chủ tài khoản:</span>
                            <span class="font-bold text-white">PHAM MINH THONG</span>
                        </div>
                        <div class="flex justify-between items-center bg-gradient-to-r from-green-500/20 to-teal-500/20 rounded-xl p-4 border border-green-500/30">
                            <span class="text-gray-300">Số tiền:</span>
                            <span class="font-bold text-green-400 text-xl">{{ number_format($paymentData['amount']) }}đ</span>
                        </div>
                        <div class="flex justify-between items-center bg-gradient-to-r from-blue-500/20 to-purple-500/20 rounded-xl p-4 border border-blue-500/30">
                            <span class="text-gray-300">Nội dung:</span>
                            <span class="font-mono font-bold text-blue-400">{{ $paymentData['description'] }}</span>
                        </div>
                    </div>

                    <div class="bg-blue-500/10 border border-blue-500/20 rounded-xl p-4">
                        <p class="text-blue-200 text-sm">
                            💡 Sau khi chuyển khoản, hệ thống sẽ tự động kích hoạt VIP trong vòng 1-5 phút
                        </p>
                    </div>
                </div>
            </div>

            <!-- Instructions -->
            <div class="mt-12 bg-white/5 rounded-2xl p-8 border border-white/10">
                <h4 class="text-xl font-bold text-white mb-6 text-center">📋 Hướng dẫn thanh toán</h4>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="space-y-3">
                        <h5 class="font-semibold text-green-400">Cách 1: Quét QR Code</h5>
                        <ol class="list-decimal list-inside space-y-2 text-sm text-gray-300">
                            <li>Mở ứng dụng ngân hàng hoặc ví điện tử</li>
                            <li>Chọn tính năng quét QR</li>
                            <li>Quét mã QR bên trái</li>
                            <li>Xác nhận thanh toán</li>
                        </ol>
                    </div>
                    
                    <div class="space-y-3">
                        <h5 class="font-semibold text-blue-400">Cách 2: Chuyển khoản thủ công</h5>
                        <ol class="list-decimal list-inside space-y-2 text-sm text-gray-300">
                            <li>Đăng nhập ứng dụng ngân hàng</li>
                            <li>Nhập thông tin chuyển khoản bên trên</li>
                            <li>Đảm bảo nội dung: <strong class="text-blue-400">{{ $paymentData['description'] }}</strong></li>
                            <li>Xác nhận chuyển khoản</li>
                        </ol>
                    </div>
                </div>
            </div>

            <!-- Warning -->
            <div class="mt-8 bg-red-500/10 border border-red-500/20 rounded-xl p-6">
                <div class="flex items-start space-x-3">
                    <svg class="w-6 h-6 text-red-400 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                    </svg>
                    <div>
                        <div class="font-medium text-red-400 mb-2">⚠️ Lưu ý quan trọng</div>
                        <p class="text-red-200 text-sm">
                            Vui lòng chuyển khoản đúng số tiền và nội dung để hệ thống tự động xử lý. 
                            Nếu có vấn đề, vui lòng liên hệ hỗ trợ.
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Back to Home -->
        <div class="text-center mt-12">
            <a href="{{ route('home') }}" class="inline-flex items-center px-8 py-4 bg-white/10 hover:bg-white/20 text-white rounded-2xl transition-all duration-300 backdrop-blur-sm border border-white/20 hover:border-white/30 transform hover:scale-105">
                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Quay lại trang chủ
            </a>
        </div>
    </div>
</section>
@endsection

@push('scripts')
<!-- SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Check payment status every 10 seconds
    let paymentCheckInterval;
    let currentUserId = {{ auth()->id() ?? 'null' }};
    
    if (currentUserId) {
        // Start checking payment status
        paymentCheckInterval = setInterval(checkPaymentStatus, 10000);
        
        // Also check immediately
        setTimeout(checkPaymentStatus, 2000);
    }
    
    function checkPaymentStatus() {
        fetch('/api/check-vip-status', {
            method: 'GET',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            credentials: 'same-origin'
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Check if payment was successful
                if (data.payment_success) {
                    clearInterval(paymentCheckInterval);
                    showPaymentSuccessAlert();
                    return;
                }
                
                // Check if user is now VIP
                if (data.is_vip) {
                    clearInterval(paymentCheckInterval);
                    showPaymentSuccessAlert();
                }
            }
        })
        .catch(error => {
            console.log('Payment status check error:', error);
        });
    }
    
    function showPaymentSuccessAlert() {
        Swal.fire({
            title: '🎉 Chúc mừng!',
            text: 'Thanh toán VIP thành công! Tài khoản của bạn đã được nâng cấp.',
            icon: 'success',
            confirmButtonText: 'Tuyệt vời!',
            confirmButtonColor: '#f59e0b',
            background: '#1e293b',
            color: '#ffffff',
            customClass: {
                popup: 'border border-yellow-400/30',
                title: 'text-yellow-400',
                confirmButton: 'bg-gradient-to-r from-yellow-400 to-orange-500 hover:from-yellow-500 hover:to-orange-600'
            },
            showClass: {
                popup: 'animate__animated animate__fadeInDown animate__faster'
            },
            hideClass: {
                popup: 'animate__animated animate__fadeOutUp animate__faster'
            }
        }).then((result) => {
            if (result.isConfirmed) {
                // Reload page to update VIP status
                window.location.reload();
            }
        });
        
        // Auto reload after 3 seconds even if user doesn't click
        setTimeout(() => {
            window.location.reload();
        }, 3000);
    }
    
    // Listen for browser events (if using broadcasting)
    if (typeof Echo !== 'undefined' && currentUserId) {
        Echo.private(`user.${currentUserId}`)
            .listen('.payment.processed', (e) => {
                console.log('Payment processed event received:', e);
                clearInterval(paymentCheckInterval);
                showPaymentSuccessAlert();
            });
            
        Echo.channel('payment-success')
            .listen('.payment.processed', (e) => {
                console.log('Global payment event received:', e);
                if (e.user_id == currentUserId) {
                    clearInterval(paymentCheckInterval);
                    showPaymentSuccessAlert();
                }
            });
    }
    
    // Clean up interval when page is unloaded
    window.addEventListener('beforeunload', function() {
        if (paymentCheckInterval) {
            clearInterval(paymentCheckInterval);
        }
    });
});
</script>

<!-- Animate.css for SweetAlert animations -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"/>
@endpush
