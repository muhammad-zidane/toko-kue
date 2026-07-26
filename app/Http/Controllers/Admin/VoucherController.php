<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Voucher;
use Illuminate\Http\Request;

class VoucherController extends Controller
{
    public function index()
    {
        $vouchers = Voucher::latest()->paginate(20);
        return view('admin.vouchers', compact('vouchers'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'code'        => 'required|string|unique:vouchers,code',
            'type'        => 'required|in:percent,fixed',
            'value'       => 'required|numeric|min:0',
            'usage_limit' => 'nullable|integer|min:1',
            'min_purchase'=> 'nullable|numeric|min:0',
            'expires_at'  => 'nullable|date',
        ]);

        Voucher::create([
            'code'         => strtoupper($request->code),
            'type'         => $request->type,
            'value'        => $request->value,
            'usage_limit'  => $request->usage_limit,
            'min_purchase' => $request->min_purchase ?? 0,
            'is_active'    => true,
            'expires_at'   => $request->expires_at,
        ]);

        return back()->with('success', 'Voucher berhasil ditambahkan.');
    }

    public function update(Request $request, Voucher $voucher)
    {
        $request->validate([
            'type'        => 'required|in:percent,fixed',
            'value'       => 'required|numeric|min:0',
            'usage_limit' => 'nullable|integer|min:1',
            'min_purchase'=> 'nullable|numeric|min:0',
            'expires_at'  => 'nullable|date',
            'is_active'   => 'nullable|boolean',
        ]);

        $voucher->update([
            'type'         => $request->type,
            'value'        => $request->value,
            'usage_limit'  => $request->usage_limit,
            'min_purchase' => $request->min_purchase ?? 0,
            'is_active'    => $request->boolean('is_active'),
            'expires_at'   => $request->expires_at,
        ]);

        return back()->with('success', 'Voucher berhasil diperbarui.');
    }

    public function destroy(Voucher $voucher)
    {
        $voucher->delete();
        return back()->with('success', 'Voucher berhasil dihapus.');
    }
}
