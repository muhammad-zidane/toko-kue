<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class OrderController extends Controller
{
    public function index()
    {
        $orders = Order::with('orderItems.product', 'payment')
            ->where('user_id', auth()->id())
            ->latest()
            ->paginate(10);
        return view('orders.index', compact('orders'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'shipping_address' => 'required|string',
            'notes'            => 'nullable|string',
            'items'            => 'required|array',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity'   => 'required|integer|min:1',
            'items.*.note'       => 'nullable|string',
        ]);

        $totalPrice = 0;
        // Validasi stok terlebih dahulu
        foreach ($request->items as $item) {
            $product = Product::findOrFail($item['product_id']);
            if ($product->stock < $item['quantity']) {
                return back()->withErrors(['stock' => "Stok {$product->name} tidak mencukupi. Tersisa {$product->stock}."]);
            }
            $totalPrice += $product->price * $item['quantity'];
        }

        $order = Order::create([
            'user_id'          => auth()->id(),
            'order_code'       => 'ORD-' . strtoupper(Str::random(8)),
            'status'           => 'pending',
            'shipping_address' => $request->shipping_address,
            'total_price'      => $totalPrice,
            'notes'            => $request->notes,
        ]);

        foreach ($request->items as $item) {
            $product = Product::findOrFail($item['product_id']);

            OrderItem::create([
                'order_id'   => $order->id,
                'product_id' => $product->id,
                'quantity'   => $item['quantity'],
                'price'      => $product->price,
                'note'       => $item['note'] ?? null,
            ]);

            // Kurangi stok produk
            $product->decrement('stock', $item['quantity']);
        }

        $paymentMethod = $request->payment_method ?? 'transfer_bank';
        $isCod = $paymentMethod === 'cod';

        Payment::create([
            'order_id'       => $order->id,
            'payment_method' => $paymentMethod,
            'status'         => $isCod ? 'paid' : 'unpaid',
            'amount'         => $totalPrice,
            'paid_at'        => $isCod ? now() : null,
        ]);

        // COD: langsung konfirmasi pesanan
        if ($isCod) {
            $order->update(['status' => 'processing']);
        }

        // Bersihkan cart setelah order berhasil
        session()->forget('cart');

        // COD langsung ke halaman sukses, lainnya ke halaman pembayaran
        if ($isCod) {
            return redirect()->route('orders.success', $order)->with('success', 'Pesanan COD berhasil dikonfirmasi!');
        }

        return redirect()->route('orders.payment', $order)->with('success', 'Pesanan berhasil dibuat!');
    }

    public function show(Order $order)
    {
        // Ownership check
        if ($order->user_id !== auth()->id()) {
            abort(403);
        }

        $order->load('orderItems.product', 'payment', 'productReviews.product', 'productReviews.images');
        return view('orders.show', compact('order'));
    }

    /**
     * Upload bukti pembayaran dan update status.
     */
    public function uploadProof(Request $request, Order $order)
    {
        if ($order->user_id !== auth()->id()) {
            abort(403);
        }

        $request->validate([
            'proof_image' => 'required|image|mimes:jpg,jpeg,png,webp,heic,heif|max:5120',
        ]);

        $path = $request->file('proof_image')->store('payment_proofs', 'public');

        $payment = $order->payment;
        if (!$payment) {
            $payment = Payment::create([
                'order_id'       => $order->id,
                'payment_method' => 'transfer_bank',
                'status'         => 'unpaid',
                'amount'         => $order->total_price,
            ]);
        }

        $payment->update([
            'status'      => 'paid',
            'proof_image' => $path,
            'paid_at'     => now(),
        ]);

        $order->update(['status' => 'processing']);

        return redirect()->route('orders.success', $order)->with('success', 'Bukti pembayaran berhasil diupload!');
    }
}