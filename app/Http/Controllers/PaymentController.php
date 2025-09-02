<?php

namespace App\Http\Controllers;

use App\Services\PaymentService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class PaymentController extends Controller
{
    protected $paymentService;

    public function __construct(PaymentService $paymentService)
    {
        $this->paymentService = $paymentService;
    }

    /**
     * Show VIP upgrade page
     */
    public function upgrade()
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        // If user is already VIP, redirect to profile
        if ($user->isVip()) {
            return redirect()->route('profile')->with('success', 'Bạn đã là thành viên VIP!');
        }

        $pricing = $this->paymentService->getVipPricing();
        $paymentData = $this->paymentService->generatePaymentData($user);

        return view('payment.upgrade', compact('user', 'pricing', 'paymentData'));
    }

    /**
     * Handle payment webhook (like old project)
     */
    public function webhook(Request $request)
    {
        try {
            Log::info('Payment webhook received', ['payload' => $request->all()]);
            $payment = $this->paymentService->createFromWebhook($request->all());
            return response()->json(['ok' => true, 'payment_id' => $payment->id]);
        } catch (\Exception $e) {
            Log::error('Payment webhook error', ['error' => $e->getMessage()]);
            return response()->json(['ok' => false, 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * Show payment history
     */
    public function history()
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        $payments = $user->payments()
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('payment.history', compact('payments'));
    }
}
