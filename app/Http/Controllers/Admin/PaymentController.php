<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PaymentInvoice;
use App\Models\UserSubscription;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class PaymentController extends Controller
{
    public function __construct()
    {
        // Middleware được định nghĩa trong routes
    }

    /**
     * Hiển thị danh sách thanh toán chờ kiểm duyệt
     */
    public function index()
    {
        $pendingPayments = PaymentInvoice::with(['user', 'subscription'])
            ->where('status', 'pending')
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        $completedPayments = PaymentInvoice::with(['user', 'subscription'])
            ->where('status', 'paid')
            ->orderBy('updated_at', 'desc')
            ->paginate(20);

        $totalPending = PaymentInvoice::where('status', 'pending')->count();
        $totalCompleted = PaymentInvoice::where('status', 'paid')->count();
        $totalAmount = PaymentInvoice::where('status', 'paid')->sum('amount');

        return view('admin.payments.index', compact(
            'pendingPayments',
            'completedPayments',
            'totalPending',
            'totalCompleted',
            'totalAmount'
        ));
    }

    /**
     * Hiển thị chi tiết thanh toán
     */
    public function show($id)
    {
        $payment = PaymentInvoice::with(['user', 'subscription'])->findOrFail($id);

        return view('admin.payments.show', compact('payment'));
    }

    /**
     * Xác nhận thanh toán thành công
     */
    public function approve(Request $request, $id)
    {
        $payment = PaymentInvoice::with(['user', 'subscription'])->findOrFail($id);

        if ($payment->status !== 'pending') {
            return response()->json([
                'success' => false,
                'message' => 'Thanh toán này đã được xử lý'
            ], 400);
        }

        try {
            // Cập nhật trạng thái hóa đơn
            $payment->markAsPaid([
                'verified_at' => now(),
                'method' => 'admin_manual_approval',
                'admin_id' => Auth::id(),
                'notes' => $request->input('notes', 'Được phê duyệt bởi admin')
            ]);

            // Kích hoạt gói dịch vụ
            if ($payment->subscription) {
                $payment->subscription->update([
                    'status' => 'active',
                    'payment_details' => json_encode([
                        'verified_at' => now(),
                        'method' => 'admin_manual_approval',
                        'admin_id' => Auth::id(),
                        'notes' => $request->input('notes', 'Được phê duyệt bởi admin')
                    ])
                ]);
            }

            Log::info('Payment approved by admin', [
                'payment_id' => $payment->id,
                'admin_id' => Auth::id(),
                'amount' => $payment->amount
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Thanh toán đã được phê duyệt thành công'
            ]);

        } catch (\Exception $e) {
            Log::error('Error approving payment', [
                'payment_id' => $payment->id,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra khi phê duyệt thanh toán'
            ], 500);
        }
    }

    /**
     * Từ chối thanh toán
     */
    public function reject(Request $request, $id)
    {
        $payment = PaymentInvoice::with(['user', 'subscription'])->findOrFail($id);

        if ($payment->status !== 'pending') {
            return response()->json([
                'success' => false,
                'message' => 'Thanh toán này đã được xử lý'
            ], 400);
        }

        try {
            // Cập nhật trạng thái hóa đơn
            $payment->update([
                'status' => 'rejected',
                'payment_details' => json_encode([
                    'rejected_at' => now(),
                    'method' => 'admin_manual_rejection',
                    'admin_id' => Auth::id(),
                    'notes' => $request->input('notes', 'Bị từ chối bởi admin')
                ])
            ]);

            // Cập nhật trạng thái subscription
            if ($payment->subscription) {
                $payment->subscription->update([
                    'status' => 'cancelled',
                    'payment_details' => json_encode([
                        'rejected_at' => now(),
                        'method' => 'admin_manual_rejection',
                        'admin_id' => Auth::id(),
                        'notes' => $request->input('notes', 'Bị từ chối bởi admin')
                    ])
                ]);
            }

            Log::info('Payment rejected by admin', [
                'payment_id' => $payment->id,
                'admin_id' => Auth::id(),
                'amount' => $payment->amount
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Thanh toán đã bị từ chối'
            ]);

        } catch (\Exception $e) {
            Log::error('Error rejecting payment', [
                'payment_id' => $payment->id,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra khi từ chối thanh toán'
            ], 500);
        }
    }

    /**
     * Thống kê thanh toán
     */
    public function statistics()
    {
        $today = now()->startOfDay();
        $thisMonth = now()->startOfMonth();
        $thisYear = now()->startOfYear();

        $stats = [
            'today' => [
                'count' => PaymentInvoice::where('status', 'paid')
                    ->whereDate('updated_at', $today)
                    ->count(),
                'amount' => PaymentInvoice::where('status', 'paid')
                    ->whereDate('updated_at', $today)
                    ->sum('amount')
            ],
            'this_month' => [
                'count' => PaymentInvoice::where('status', 'paid')
                    ->whereDate('updated_at', '>=', $thisMonth)
                    ->count(),
                'amount' => PaymentInvoice::where('status', 'paid')
                    ->whereDate('updated_at', '>=', $thisMonth)
                    ->sum('amount')
            ],
            'this_year' => [
                'count' => PaymentInvoice::where('status', 'paid')
                    ->whereDate('updated_at', '>=', $thisYear)
                    ->count(),
                'amount' => PaymentInvoice::where('status', 'paid')
                    ->whereDate('updated_at', '>=', $thisYear)
                    ->sum('amount')
            ],
            'pending' => PaymentInvoice::where('status', 'pending')->count(),
            'total_paid' => PaymentInvoice::where('status', 'paid')->sum('amount')
        ];

        return response()->json($stats);
    }
}
