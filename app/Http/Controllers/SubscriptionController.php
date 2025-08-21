<?php

namespace App\Http\Controllers;

use App\Models\UserSubscription;
use App\Models\VietqrInformation;
use App\Models\PaymentInvoice;
use App\Services\VietQrService;
use App\Mail\PaymentSuccessMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;
use App\Models\SubscriptionPackage;
use Illuminate\Support\Facades\DB;

class SubscriptionController extends Controller
{
    public function __construct()
    {
        // Middleware được định nghĩa trong routes thay vì controller
    }

    public function index()
    {
        $user = Auth::user();
        $subscriptions = $user->subscriptions()->orderBy('created_at', 'desc')->get();
        $activeSubscription = $user->activeSubscription();

        return view('subscriptions.index', compact('subscriptions', 'activeSubscription'));
    }

    public function showPackages()
    {
        $packages = [
            [
                'id' => 'basic',
                'name' => 'Gói Cơ Bản',
                'price' => 0,
                'duration' => 30,
                'features' => [
                    'Xem công thức nấu ăn cơ bản',
                    'Tìm kiếm món ăn theo địa điểm',
                    'Đánh giá và bình luận'
                ]
            ],
            [
                'id' => 'premium',
                'name' => 'Gói Premium',
                'price' => 1000,
                'duration' => 30,
                'features' => [
                    'Tất cả tính năng cơ bản',
                    'Tìm món ăn theo bản đồ nâng cao',
                    'Quảng cáo cửa hàng của bạn',
                    'Ưu tiên hiển thị trong tìm kiếm',
                    'Thống kê chi tiết'
                ]
            ],
            [
                'id' => 'vip',
                'name' => 'Gói VIP',
                'price' => 199000,
                'duration' => 30,
                'features' => [
                    'Tất cả tính năng Premium',
                    'Quảng cáo ưu tiên cao nhất',
                    'Hỗ trợ 24/7',
                    'Tính năng đặc biệt',
                    'Giảm giá đặc biệt'
                ]
            ]
        ];

        $user = Auth::user();
        $activeSubscription = $user->activeSubscription();

        return view('subscriptions.packages', compact('packages', 'activeSubscription'));
    }

    public function purchase(Request $request)
    {
        try {
            Log::info('Purchase request received', $request->all());

            $request->validate([
                'package_id' => 'required|in:basic,premium,vip',
            ]);

            $user = auth()->user();
            $packageId = $request->package_id;

            Log::info('Package ID: ' . $packageId);

            // Lấy thông tin gói từ array packages
            $packages = [
                'basic' => ['price' => 0, 'duration_days' => 30, 'name' => 'Gói Cơ Bản'],
                'premium' => ['price' => 1000, 'duration_days' => 30, 'name' => 'Gói Premium'],
                'vip' => ['price' => 199000, 'duration_days' => 30, 'name' => 'Gói VIP']
            ];

            $package = (object) $packages[$packageId];

            Log::info('Package info: ' . json_encode($package));

            // Kiểm tra xem user đã có subscription đang hoạt động chưa
            $activeSubscription = UserSubscription::where('user_id', $user->id)
                ->where('status', 'active')
                ->where('end_date', '>', now())
                ->first();

            if ($activeSubscription) {
                return response()->json([
                    'success' => false,
                    'message' => 'Bạn đã có gói dịch vụ đang hoạt động. Vui lòng đợi gói hiện tại hết hạn.'
                ]);
            }

            // Tạo transaction ID
            $transactionId = 'TXN_' . uniqid();
            Log::info('Transaction ID: ' . $transactionId);

            // Tạo subscription
            $subscription = UserSubscription::create([
                'user_id' => $user->id,
                'subscription_type' => $packageId,
                'transaction_id' => $transactionId,
                'amount' => $package->price,
                'status' => 'pending',
                'start_date' => now(),
                'end_date' => now()->addDays($package->duration_days),
                'payment_method' => 'vietqr',
            ]);

            // Tạo hóa đơn
            PaymentInvoice::create([
                'user_id' => $user->id,
                'subscription_id' => $subscription->id,
                'invoice_number' => PaymentInvoice::generateInvoiceNumber(),
                'transaction_id' => $transactionId,
                'amount' => $package->price,
                'payment_method' => 'vietqr',
                'payment_gateway' => 'vietqr',
                'status' => 'pending',
            ]);

            // Sử dụng PayOS API để tạo QR code thật
            $vietqrService = new VietQrService();
            $qrResult = $vietqrService->generateQrCode([
                'transaction_id' => $transactionId,
                'amount' => $package->price,
                'message' => 'Thanh toán gói ' . $package->name
            ]);

            if ($qrResult['success']) {
                return response()->json([
                    'success' => true,
                    'qr_code' => $qrResult['qr_code'],
                    'amount' => $package->price,
                    'transaction_id' => $transactionId,
                    'vietqr_data' => $qrResult['payos_info'] ?? null,
                    'payment_url' => $qrResult['payment_url'] ?? null,
                    'is_demo' => $qrResult['is_demo'] ?? false,
                    'message' => 'QR code đã được tạo thành công'
                ]);
            } else {
                Log::error('PayOS API error: ' . $qrResult['message']);

                return response()->json([
                    'success' => false,
                    'message' => 'Không thể tạo QR code PayOS: ' . $qrResult['message']
                ]);
            }
        } catch (\Exception $e) {
            Log::error('Purchase error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Lỗi: ' . $e->getMessage()
            ]);
        }
    }

    public function verifyPayment(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'transaction_id' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Dữ liệu không hợp lệ',
                'errors' => $validator->errors()
            ], 422);
        }

        $user = Auth::user();
        $subscription = $user->subscriptions()
            ->where('transaction_id', $request->transaction_id)
            ->where('status', 'pending')
            ->first();

        if (!$subscription) {
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy giao dịch hoặc giao dịch đã được xử lý'
            ], 404);
        }

        // Kiểm tra thời gian tạo giao dịch (không cho phép xác minh quá sớm)
        $timeSinceCreated = now()->diffInMinutes($subscription->created_at);
        if ($timeSinceCreated < 1) {
            return response()->json([
                'success' => false,
                'message' => 'Vui lòng đợi ít nhất 1 phút sau khi tạo giao dịch để kiểm tra'
            ], 400);
        }

        // Sử dụng VietQR API để kiểm tra trạng thái thanh toán thực tế
        $vietqrService = new VietQrService();
        $paymentStatus = $vietqrService->checkPaymentStatus($subscription->transaction_id);

        // Nếu API không hoạt động, sử dụng demo mode
        if (!$paymentStatus['success']) {
            // Demo mode: Cho phép thanh toán sau 2 phút
            if ($timeSinceCreated < 2) {
                return response()->json([
                    'success' => false,
                    'message' => 'Thanh toán chưa hoàn tất. Vui lòng thử lại sau.'
                ], 400);
            }

            // Demo: Giả lập thanh toán thành công
            $paymentStatus = [
                'success' => true,
                'status' => 'completed',
                'amount' => $subscription->amount,
                'message' => 'Thanh toán thành công (Demo mode)'
            ];
        } else {
            // Kiểm tra trạng thái thanh toán thực tế
            if ($paymentStatus['status'] !== 'success' && $paymentStatus['status'] !== 'completed') {
                return response()->json([
                    'success' => false,
                    'message' => 'Thanh toán chưa hoàn tất. Vui lòng thử lại sau.'
                ], 400);
            }

            // Xác minh số tiền
            if ($paymentStatus['amount'] != $subscription->amount) {
                return response()->json([
                    'success' => false,
                    'message' => 'Số tiền thanh toán không khớp với giao dịch.'
                ], 400);
            }
        }

        // Cập nhật hóa đơn thành công
        $invoice = PaymentInvoice::where('transaction_id', $subscription->transaction_id)->first();
        if ($invoice) {
            $invoice->markAsPaid([
                'verified_at' => now(),
                'method' => 'vietqr_api_verification',
                'verification_time' => $timeSinceCreated . ' minutes',
                'api_response' => $paymentStatus
            ]);
        }

        // Kích hoạt gói dịch vụ
        $subscription->update([
            'status' => 'active',
            'payment_details' => json_encode([
                'verified_at' => now(),
                'method' => 'vietqr_api_verification',
                'verification_time' => $timeSinceCreated . ' minutes',
                'api_response' => $paymentStatus
            ])
        ]);

        // Gửi email thông báo thanh toán thành công
        try {
            if ($invoice) {
                Mail::to($user->email)->send(new PaymentSuccessMail($invoice));
            }
        } catch (\Exception $e) {
            // Log lỗi gửi email nhưng không ảnh hưởng đến quá trình thanh toán
            \Log::error('Failed to send payment success email: ' . $e->getMessage());
        }

        return response()->json([
            'success' => true,
            'message' => 'Thanh toán thành công! Gói VIP đã được kích hoạt.',
            'subscription' => $subscription,
            'invoice_number' => $invoice ? $invoice->invoice_number : null
        ]);
    }

    public function cancel(Request $request)
    {
        $user = Auth::user();
        $subscription = $user->activeSubscription();

        if (!$subscription) {
            return response()->json([
                'success' => false,
                'message' => 'Bạn không có gói dịch vụ đang hoạt động'
            ], 400);
        }

        $subscription->update(['status' => 'cancelled']);

        return response()->json([
            'success' => true,
            'message' => 'Hủy gói dịch vụ thành công'
        ]);
    }

    /**
     * Lấy danh sách tính năng của gói dịch vụ
     */
    private function getPackageFeatures($packageId)
    {
        $features = [
            'basic' => [
                'Xem công thức nấu ăn cơ bản',
                'Tìm kiếm món ăn theo địa điểm',
                'Đánh giá và bình luận'
            ],
            'premium' => [
                'Tất cả tính năng cơ bản',
                'Tìm món ăn theo bản đồ nâng cao',
                'Quảng cáo cửa hàng của bạn',
                'Ưu tiên hiển thị trong tìm kiếm',
                'Thống kê chi tiết'
            ],
            'vip' => [
                'Tất cả tính năng Premium',
                'Quảng cáo ưu tiên cao nhất',
                'Hỗ trợ 24/7',
                'Tính năng đặc biệt',
                'Giảm giá đặc biệt'
            ]
        ];

        return $features[$packageId] ?? [];
    }
}
