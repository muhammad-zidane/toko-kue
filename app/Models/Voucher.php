<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Voucher extends Model
{
    use HasFactory;
    protected $fillable = [
        'code', 'type', 'value', 'usage_limit', 'used_count',
        'min_purchase', 'is_active', 'expires_at',
    ];

    protected $casts = [
        'expires_at' => 'datetime',
    ];

    public function isValid(float $amount): bool
    {
        if (!$this->is_active) return false;
        if ($this->expires_at && $this->expires_at->isPast()) return false;
        if ($this->usage_limit !== null && $this->used_count >= $this->usage_limit) return false;
        if ($amount < $this->min_purchase) return false;
        return true;
    }

    public function calculateDiscount(float $amount): float
    {
        if ($this->type === 'percent') {
            return min($amount, round($amount * $this->value / 100, 2));
        }
        return min($amount, $this->value);
    }
}
