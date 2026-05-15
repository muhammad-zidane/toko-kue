<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\Payment;
use App\Models\User;
use App\Models\Voucher;
use App\Models\ShippingZone;
use App\Notifications\NewOrderNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class OrderController extends Controller
{
    /**
     * Tampilkan daftar pesanan milik pengguna yang sedang login (paginated 10).
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $orders = Order::with('orderItems.product', 'payment')
            ->where('user_id', auth()->id())
            ->latest()
            ->paginate(10);

        return view('orders.index', compact('orders'));
    }

    public function singleProductCheckout(Product $product)
    {
        return view('orders.create', compact('product'));
    }

    public function payment(Order $order)
    {
        $this->authorizeOwner($order);
        $order->load('orderItems.product', 'payment');

        return view('orders.payment', compact('order'));
    }

    public function success(Order $order)
    {
        $this->authorizeOwner($order);
        $order->load('orderItems.product', 'payment');

        return view('orders.success', compact('order'));
    }

    /**
     * Buat pesanan baru dari form checkout.
     *
     * Memvalidasi stok, membuat record Order, OrderItem, dan Payment.
     * Stok produk dikurangi otomatis. COD langsung dikonfirmasi;
     * metode lain diarahkan ke halaman upload bukti bayar.
     *
     * @param  Request $request  Input: shipping_address, notes, items[]{product_id, quantity}, payment_method
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        $leadDays = config('app.lead_time_days', 2);

        $request->validate([
            'delivery_method'    => 'required|in:pickup,delivery',
            'shipping_address'   => 'required_if:delivery_method,delivery|string',
            'delivery_date'      => ['required', 'date', 'after_or_equal:' . now()->addDays($leadDays)->format('Y-m-d')],
            'delivery_slot'      => 'nullable|string',
            'notes'              => 'nullable|string|max:300',
            'items'              => 'required|array',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity'   => 'required|integer|min:1',
            'items.*.note'       => 'nullable|string',
            'voucher_code'       => 'nullable|string',
        ]);

        $products = Product::findMany(array_column($request->items, 'product_id'))->keyBy('id');

        $subtotal = 0;
        foreach ($request->items as $item) {
            $product = $products->get($item['product_id']);
            if ($product->stock < $item['quantity']) {
                return back()->withErrors(['stock' => "Stok {$product->name} tidak mencukupi. Tersisa {$product->stock}."]);
            }
            $subtotal += $product->price * $item['quantity'];
        }

        // Shipping cost
        $shippingCost = 0;
        if ($request->delivery_method === 'delivery' && $request->shipping_zone_id) {
            $zone = ShippingZone::find($request->shipping_zone_id);
            $shippingCost = $zone ? $zone->cost : 0;
        }

        // Voucher
        $discountAmount = 0;
        $voucherCode    = null;
        if ($request->voucher_code) {
            $voucher = Voucher::where('code', strtoupper($request->voucher_code))->first();
            if ($voucher && $voucher->isValid($subtotal)) {
                $discountAmount = $voucher->calculateDiscount($subtotal);
                $voucherCode    = $voucher->code;
                $voucher->increment('used_count');
            }
        }

        $totalPrice = max(0, $subtotal + $shippingCost - $discountAmount);

        $shippingAddress = $request->delivery_method === 'pickup'
            ? 'Ambil di Toko'
            : $request->shipping_address;

        $order = Order::create([
            'user_id'          => auth()->id(),
            'order_code'       => 'ORD-' . strtoupper(Str::random(8)),
            'status'           => 'pending',
            'shipping_address' => $shippingAddress,
            'total_price'      => $totalPrice,
            'notes'            => $request->notes,
            'delivery_method'  => $request->delivery_method,
            'delivery_date'    => $request->delivery_date,
            'delivery_slot'    => $request->delivery_slot,
            'shipping_cost'    => $shippingCost,
            'voucher_code'     => $voucherCode,
            'discount_amount'  => $discountAmount,
        ]);

        foreach ($request->items as $item) {
            $product = $products->get($item['product_id']);

            OrderItem::create([
                'order_id'   => $order->id,
                'product_id' => $product->id,
                'quantity'   => $item['quantity'],
                'price'      => $product->price,
                'note'       => $item['note'] ?? null,
            ]);

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

        if ($isCod) {
            $order->update(['status' => 'processing']);
        }

        session()->forget('cart');

        try {
            $order->load('user', 'orderItems.product', 'payment');
            \Illuminate\Support\Facades\Mail::to(auth()->user()->email)
                ->queue(new \App\Mail\OrderConfirmationMail($order));

            // Notify all admins
            User::where('role', 'admin')->each(function ($admin) use ($order) {
                $admin->notify(new NewOrderNotification($order));
            });
        } catch (\Throwable) {}

        if ($isCod) {
            return redirect()->route('orders.success', $order)->with('success', 'Pesanan COD berhasil dikonfirmasi!');
        }

        return redirect()->route('orders.payment', $order)->with('success', 'Pesanan berhasil dibuat!');
    }

    public function showStatus(Order $order)
    {
        $this->authorizeOwner($order);
        $order->load('orderItems.product', 'payment');

        return view('orders.status', compact('order'));
    }

    /**
     * Tampilkan detail satu pesanan milik pengguna.
     * Akses ditolak (403) jika pesanan bukan milik pengguna yang login.
     *
     * @param  Order $order  Pesanan yang akan ditampilkan
     * @return \Illuminate\View\View
     */
    public function show(Order $order)
    {
        $this->authorizeOwner($order);
        $order->load('orderItems.product', 'payment', 'productReviews.product', 'productReviews.images');

        return view('orders.show', compact('order'));
    }

    public function uploadProof(Request $request, Order $order)
    {
        $this->authorizeOwner($order);

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

    public function invoice(Order $order)
    {
        if ($order->user_id !== auth()->id() && !auth()->user()?->role === 'admin') {
            abort(403);
        }

        $order->load('user', 'orderItems.product', 'payment');

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('pdf.invoice', compact('order'));

        return $pdf->download('invoice-' . $order->order_code . '.pdf');
    }

    private function authorizeOwner(Order $order): void
    {
        if ($order->user_id !== auth()->id()) {
            abort(403);
        }
    }
}