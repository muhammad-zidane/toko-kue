<?php

namespace App\Models;

use Database\Factories\PaymentFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    /** @use HasFactory<PaymentFactory> */
    use HasFactory;

    protected $fillable = [
        'order_id', 'payment_method', 'status',
        'amount', 'proof_image', 'paid_at'
    ];

    protected $casts = [
        'paid_at' => 'datetime',
    ];

    /**
     * Pesanan yang terkait dengan pembayaran ini.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function order()
    {
        return $this->belongsTo(Order::class);
    }
}