<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class PaymentInvoice extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'subscription_id',
        'invoice_number',
        'transaction_id',
        'amount',
        'currency',
        'payment_method',
        'status',
        'payment_gateway',
        'payment_details',
        'invoice_details',
        'paid_at',
        'expires_at',
        'notes'
    ];

    protected $casts = [
        'payment_details' => 'array',
        'invoice_details' => 'array',
        'paid_at' => 'datetime',
        'expires_at' => 'datetime',
        'amount' => 'decimal:2'
    ];

    /**
     * Tạo số hóa đơn tự động
     */
    public static function generateInvoiceNumber()
    {
        $prefix = 'INV';
        $date = now()->format('Ymd');
        $random = strtoupper(Str::random(4));

        return "{$prefix}{$date}{$random}";
    }

    /**
     * Relationship với User
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relationship với Subscription
     */
    public function subscription()
    {
        return $this->belongsTo(UserSubscription::class, 'subscription_id');
    }

    /**
     * Kiểm tra hóa đơn đã hết hạn chưa
     */
    public function isExpired()
    {
        return $this->expires_at && now()->isAfter($this->expires_at);
    }

    /**
     * Kiểm tra hóa đơn đã thanh toán chưa
     */
    public function isPaid()
    {
        return $this->status === 'completed' && $this->paid_at;
    }

    /**
     * Đánh dấu hóa đơn đã thanh toán
     */
    public function markAsPaid($paymentDetails = null)
    {
        $this->update([
            'status' => 'completed',
            'paid_at' => now(),
            'payment_details' => $paymentDetails
        ]);
    }

    /**
     * Lấy thông tin hóa đơn dạng array
     */
    public function getInvoiceData()
    {
        return [
            'invoice_number' => $this->invoice_number,
            'transaction_id' => $this->transaction_id,
            'amount' => $this->amount,
            'currency' => $this->currency,
            'payment_method' => $this->payment_method,
            'status' => $this->status,
            'created_at' => $this->created_at->format('d/m/Y H:i:s'),
            'paid_at' => $this->paid_at ? $this->paid_at->format('d/m/Y H:i:s') : null,
            'expires_at' => $this->expires_at ? $this->expires_at->format('d/m/Y H:i:s') : null,
            'user' => [
                'name' => $this->user->name,
                'email' => $this->user->email
            ],
            'subscription' => $this->subscription ? [
                'type' => $this->subscription->subscription_type,
                'duration' => $this->subscription->end_date->diffInDays($this->subscription->start_date) . ' ngày'
            ] : null
        ];
    }
}
