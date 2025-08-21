<?php

namespace App\Http\Controllers;

use App\Models\PaymentInvoice;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class InvoiceController extends Controller
{
    /**
     * Hiển thị danh sách hóa đơn của user
     */
    public function index()
    {
        $user = Auth::user();
        $invoices = $user->paymentInvoices()
            ->with(['subscription'])
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('invoices.index', compact('invoices'));
    }

    /**
     * Hiển thị chi tiết hóa đơn
     */
    public function show($id)
    {
        $user = Auth::user();
        $invoice = $user->paymentInvoices()
            ->with(['subscription'])
            ->findOrFail($id);

        return view('invoices.show', compact('invoice'));
    }

    /**
     * Tải xuống hóa đơn dạng PDF (có thể implement sau)
     */
    public function download($id)
    {
        $user = Auth::user();
        $invoice = $user->paymentInvoices()
            ->with(['subscription'])
            ->findOrFail($id);

        // TODO: Implement PDF generation
        return response()->json([
            'success' => false,
            'message' => 'Tính năng tải xuống PDF sẽ được phát triển sau'
        ]);
    }

    /**
     * API: Lấy danh sách hóa đơn
     */
    public function apiIndex()
    {
        $user = Auth::user();
        $invoices = $user->paymentInvoices()
            ->with(['subscription'])
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($invoice) {
                return [
                    'id' => $invoice->id,
                    'invoice_number' => $invoice->invoice_number,
                    'transaction_id' => $invoice->transaction_id,
                    'amount' => $invoice->amount,
                    'currency' => $invoice->currency,
                    'status' => $invoice->status,
                    'payment_method' => $invoice->payment_method,
                    'created_at' => $invoice->created_at->format('d/m/Y H:i:s'),
                    'paid_at' => $invoice->paid_at ? $invoice->paid_at->format('d/m/Y H:i:s') : null,
                    'subscription' => $invoice->subscription ? [
                        'type' => $invoice->subscription->subscription_type,
                        'status' => $invoice->subscription->status
                    ] : null
                ];
            });

        return response()->json([
            'success' => true,
            'invoices' => $invoices
        ]);
    }

    /**
     * API: Lấy chi tiết hóa đơn
     */
    public function apiShow($id)
    {
        $user = Auth::user();
        $invoice = $user->paymentInvoices()
            ->with(['subscription'])
            ->findOrFail($id);

        return response()->json([
            'success' => true,
            'invoice' => $invoice->getInvoiceData()
        ]);
    }
}
