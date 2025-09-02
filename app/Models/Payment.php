<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'gateway',
        'transaction_date',
        'account_number',
        'code',
        'content',
        'transfer_type',
        'transfer_amount',
        'accumulated',
        'sub_account',
        'reference_code',
        'description',
        'status',
        'raw_payload',
    ];

    protected $casts = [
        'transaction_date' => 'datetime',
        'raw_payload' => 'array',
    ];

    /**
     * Get the user that owns the payment.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
