<?php

namespace App\Http\Controllers;

use App\Models\Voucher;
use Illuminate\Http\Request;

class VoucherController extends Controller
{
    public function apply(Request $request)
    {
        $request->validate([
            'code'   => 'required|string',
            'amount' => 'required|numeric|min:0',
        ]);

        $voucher = Voucher::where('code', strtoupper($request->code))->first();

        if (!$voucher || !$voucher->isValid((float) $request->amount)) {
            $message = match(true) {
                !$voucher                                  => 'Kode voucher tidak ditemukan.',
                !$voucher->is_active                       => 'Voucher sudah tidak aktif.',
                $voucher->expires_at?->isPast()            => 'Voucher sudah kadaluarsa.',
                $voucher->usage_limit !== null
                    && $voucher->used_count >= $voucher->usage_limit => 'Batas penggunaan voucher telah tercapai.',
                (float) $request->amount < $voucher->min_purchase   => 'Minimum pembelian Rp ' . number_format($voucher->min_purchase, 0, ',', '.'),
                default                                    => 'Voucher tidak valid.',
            };

            return response()->json(['valid' => false, 'message' => $message]);
        }

        $discount = $voucher->calculateDiscount((float) $request->amount);

        return response()->json([
            'valid'    => true,
            'discount' => $discount,
            'message'  => 'Voucher berhasil diterapkan.',
        ]);
    }
}
