<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    /**
     * Tampilkan daftar semua pesanan untuk admin (paginated 10 per halaman).
     */
    public function index()
    {
        $orders = Order::with(['user', 'payment', 'orderItems'])->latest()->paginate(10);

        return view('admin.orders', compact('orders'));
    }

    /**
     * Tampilkan detail satu pesanan beserta item dan data pembayaran.
     */
    public function show(Order $order)
    {
        $order->load(['user', 'orderItems.product', 'orderItems.customizations.option', 'payment']);

        return view('admin.order-detail', compact('order'));
    }

    public function downloadProof(Order $order)
    {
        $order->load('payment');

        if (!$order->payment?->proof_image) {
            abort(404, 'Bukti pembayaran tidak ditemukan.');
        }

        $path = $order->payment->proof_image;

        if (!\Illuminate\Support\Facades\Storage::disk('public')->exists($path)) {
            abort(404, 'File bukti pembayaran tidak ditemukan.');
        }

        $extension = pathinfo($path, PATHINFO_EXTENSION);
        $filename  = 'bukti-' . $order->order_code . '.' . $extension;

        return \Illuminate\Support\Facades\Storage::disk('public')->download($path, $filename);
    }

    /**
     * Perbarui status pesanan. Jika status menjadi 'completed',
     * pembayaran terkait juga otomatis ditandai 'paid'.
     */
    public function updateStatus(Order $order, string $status)
    {
        $validStatuses = ['pending', 'processing', 'shipped', 'completed', 'cancelled'];

        if (!in_array($status, $validStatuses)) {
            return back()->withErrors(['status' => 'Status tidak valid.']);
        }

        $order->load(['user', 'payment']);
        $order->update(['status' => $status]);

        try {
            \Illuminate\Support\Facades\Mail::to($order->user->email)
                ->queue(new \App\Mail\OrderStatusUpdatedMail($order));
        } catch (\Throwable) {}

        if ($status === 'completed' && $order->payment) {
            $order->payment->update(['status' => 'paid', 'paid_at' => now()]);
        }

        return back()->with('success', 'Status pesanan berhasil diperbarui!');
    }

    public function confirmPayment(Request $request, Order $order)
    {
        $payment = $order->payment;
        if (!$payment) {
            return back()->withErrors(['error' => 'Pembayaran tidak ditemukan.']);
        }

        $payment->update(['status' => 'paid', 'paid_at' => now()]);

        $isFullyPaid = (float) $payment->amount >= (float) $order->total_price;
        $order->update([
            'status'         => 'processing',
            'paid_amount'    => $payment->amount,
            'payment_status' => $isFullyPaid ? 'paid' : 'dp',
        ]);

        return back()->with('success', 'Pembayaran dikonfirmasi.');
    }

    public function rejectPayment(Request $request, Order $order)
    {
        $request->validate(['reason' => 'nullable|string|max:500']);
        $order->payment?->update(['status' => 'failed']);
        $order->update([
            'status'         => 'pending',
            'payment_status' => 'unpaid',
            'paid_amount'    => 0,
            'notes'          => $request->reason ? '[Pembayaran Ditolak] ' . $request->reason : $order->notes,
        ]);
        return back()->with('success', 'Pembayaran ditolak.');
    }
}
