<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Payment;
use App\Models\Product;
use App\Models\User;
use App\Notifications\NewOrderNotification;
use App\Services\OrderService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class OrderController extends Controller
{
    public function __construct(
        protected OrderService $orderService
    ) {}

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
        $savedAddresses = auth()->user()->addresses()->latest()->get();
        $dpMinAmount    = config('app.dp_min_amount', 200000);
        $dpPercentage   = config('app.dp_percentage', 50);

        return view('orders.create', compact('product', 'savedAddresses', 'dpMinAmount', 'dpPercentage'));
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

    public function store(Request $request)
    {
        $leadDays = config('app.lead_time_days', 2);

        $validated = $request->validate([
            'delivery_method'              => 'required|in:pickup,delivery',
            'shipping_address'             => 'required_if:delivery_method,delivery|nullable|string',
            'shipping_zone_id'             => 'required_if:delivery_method,delivery|nullable|exists:shipping_zones,id',
            'delivery_date'                => ['required', 'date', 'after_or_equal:' . now()->addDays($leadDays)->format('Y-m-d')],
            'delivery_slot'                => 'nullable|string',
            'notes'                        => 'nullable|string|max:300',
            'items'                        => 'required|array',
            'items.*.product_id'           => 'required|exists:products,id',
            'items.*.quantity'             => 'required|integer|min:1',
            'items.*.note'                 => 'nullable|string|max:300',
            'items.*.customizations'       => 'nullable|string',
            'voucher_code'                 => 'nullable|string',
            'use_dp'                       => 'nullable|boolean',
            'payment_method'               => 'nullable|string',
        ], [
            'shipping_zone_id.required_if' => 'Zona pengiriman wajib dipilih jika metode pengiriman adalah diantar.',
            'shipping_zone_id.exists'      => 'Zona pengiriman tidak valid.',
        ]);

        try {
            ['order' => $order, 'isCod' => $isCod] = $this->orderService->placeOrder(auth()->user(), $validated);
        } catch (\Illuminate\Validation\ValidationException $e) {
            throw $e;
        } catch (\Throwable $e) {
            Log::error('Order creation failed', ['error' => $e->getMessage(), 'user' => auth()->id()]);
            return back()->withErrors(['error' => 'Terjadi kesalahan saat memproses pesanan. Silakan coba lagi.']);
        }

        session()->forget('cart');

        try {
            $order->load('user', 'orderItems.product', 'payment');
            \Illuminate\Support\Facades\Mail::to(auth()->user()->email)
                ->queue(new \App\Mail\OrderConfirmationMail($order));

            User::admins()->each(function ($admin) use ($order) {
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

    public function show(Order $order)
    {
        $this->authorizeOwner($order);
        $order->load('orderItems.product', 'orderItems.customizations.option', 'payment', 'productReviews.product', 'productReviews.images');

        return view('orders.show', compact('order'));
    }

    public function uploadProof(Request $request, Order $order)
    {
        $this->authorizeOwner($order);

        $order->load('payment');
        if ($order->payment?->status === 'paid') {
            return back()->with('error', 'Pembayaran untuk pesanan ini sudah dikonfirmasi.');
        }

        $request->validate([
            'proof_image' => ['required', 'image', 'mimes:jpg,jpeg,png', 'mimetypes:image/jpeg,image/png', 'max:2048'],
        ]);

        if ($order->payment?->proof_image) {
            \Illuminate\Support\Facades\Storage::disk('public')->delete($order->payment->proof_image);
        }

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
            'proof_image' => $path,
            'status'      => 'unpaid',
        ]);

        $message = 'Bukti pembayaran berhasil diunggah. Menunggu verifikasi admin.';

        return redirect()->route('orders.success', $order)->with('success', $message);
    }

    public function invoice(Order $order)
    {
        if ($order->user_id !== auth()->id() && !auth()->user()?->isAdmin()) {
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