<?php

namespace App\Http\Controllers;

use App\Models\CustomizationOption;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\OrderItemCustomization;
use App\Models\Product;
use App\Models\Payment;
use App\Models\User;
use App\Models\Voucher;
use App\Models\ShippingZone;
use App\Notifications\NewOrderNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
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

        $request->validate([
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
        ], [
            'shipping_zone_id.required_if' => 'Zona pengiriman wajib dipilih jika metode pengiriman adalah diantar.',
            'shipping_zone_id.exists'      => 'Zona pengiriman tidak valid.',
        ]);

        $parsedCustomizations = [];
        foreach ($request->items as $idx => $item) {
            $parsed = [];
            if (!empty($item['customizations'])) {
                $decoded = json_decode($item['customizations'], true);
                if (is_array($decoded)) {
                    $parsed = $decoded;
                }
            }
            $parsedCustomizations[$idx] = $parsed;
        }

        $optionIds  = collect($parsedCustomizations)->flatten()->filter()->unique()->values()->all();
        $optionsMap = CustomizationOption::whereIn('id', $optionIds)->get()->keyBy('id');

        $shippingZone = $request->delivery_method === 'delivery' && $request->shipping_zone_id
            ? ShippingZone::find($request->shipping_zone_id)
            : null;

        try {
            ['order' => $order, 'isCod' => $isCod] = DB::transaction(
                function () use ($request, $parsedCustomizations, $optionsMap, $shippingZone) {

                    // lockForUpdate prevents stock race conditions
                    $products = Product::whereIn('id', array_column($request->items, 'product_id'))
                        ->lockForUpdate()
                        ->get()
                        ->keyBy('id');

                    $subtotal = 0;
                    foreach ($request->items as $idx => $item) {
                        $product = $products->get($item['product_id']);
                        if ($product->stock < $item['quantity']) {
                            throw \Illuminate\Validation\ValidationException::withMessages([
                                'stock' => "Stok {$product->name} tidak mencukupi. Tersisa {$product->stock}.",
                            ]);
                        }
                        $extraTotal = collect($parsedCustomizations[$idx])
                            ->sum(fn($id) => $optionsMap->get($id)?->extra_price ?? 0);
                        $subtotal += ($product->price + $extraTotal) * $item['quantity'];
                    }

                    $shippingCost = $shippingZone?->cost ?? 0;

                    $discountAmount = 0;
                    $voucherCode    = null;
                    if ($request->voucher_code) {
                        $voucher = Voucher::where('code', strtoupper($request->voucher_code))
                            ->lockForUpdate()
                            ->first();
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

                    $dpMinAmount  = config('app.dp_min_amount', 200000);
                    $dpPercentage = config('app.dp_percentage', 50);
                    $useDp        = $request->boolean('use_dp') && $totalPrice >= $dpMinAmount;
                    $dpAmount     = $useDp ? round($totalPrice * $dpPercentage / 100) : 0;

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
                        'payment_status'   => $useDp ? 'dp' : 'unpaid',
                        'dp_amount'        => $dpAmount,
                        'paid_amount'      => 0,
                    ]);

                    foreach ($request->items as $idx => $item) {
                        $product    = $products->get($item['product_id']);
                        $extraTotal = collect($parsedCustomizations[$idx])
                            ->sum(fn($id) => $optionsMap->get($id)?->extra_price ?? 0);

                        $orderItem = OrderItem::create([
                            'order_id'   => $order->id,
                            'product_id' => $product->id,
                            'quantity'   => $item['quantity'],
                            'price'      => $product->price + $extraTotal,
                            'note'       => $item['note'] ?? null,
                        ]);

                        foreach ($parsedCustomizations[$idx] as $optionId) {
                            $opt = $optionsMap->get($optionId);
                            if ($opt) {
                                OrderItemCustomization::create([
                                    'order_item_id'           => $orderItem->id,
                                    'customization_option_id' => $opt->id,
                                    'extra_price'             => $opt->extra_price,
                                ]);
                            }
                        }

                        $product->decrement('stock', $item['quantity']);
                    }

                    $paymentMethod = $request->payment_method ?? 'transfer_bank';
                    $isCod         = $paymentMethod === 'cod';
                    $amountDue     = $useDp ? $dpAmount : $totalPrice;

                    Payment::create([
                        'order_id'       => $order->id,
                        'payment_method' => $paymentMethod,
                        'status'         => $isCod ? 'paid' : 'unpaid',
                        'amount'         => $amountDue,
                        'paid_at'        => $isCod ? now() : null,
                    ]);

                    if ($isCod) {
                        $order->update([
                            'status'         => 'processing',
                            'payment_status' => 'paid',
                            'paid_amount'    => $totalPrice,
                        ]);
                    }

                    return ['order' => $order, 'isCod' => $isCod];
                }
            );
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
        $order->load('orderItems.product', 'payment', 'productReviews.product', 'productReviews.images');

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