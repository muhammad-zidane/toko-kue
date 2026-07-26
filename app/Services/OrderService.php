<?php

namespace App\Services;

use App\Models\CustomizationOption;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\OrderItemCustomization;
use App\Models\Payment;
use App\Models\Product;
use App\Models\ShippingZone;
use App\Models\User;
use App\Models\Voucher;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class OrderService
{
    /**
     * Proses pembuatan pesanan baru (termasuk validasi stok, diskon voucher, DP, dan pembuatan payment).
     *
     * @param User $user
     * @param array $data
     * @return array{order: Order, isCod: bool}
     */
    public function placeOrder(User $user, array $data): array
    {
        $parsedCustomizations = [];
        foreach ($data['items'] as $idx => $item) {
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

        $shippingZone = ($data['delivery_method'] ?? 'pickup') === 'delivery' && !empty($data['shipping_zone_id'])
            ? ShippingZone::find($data['shipping_zone_id'])
            : null;

        return DB::transaction(function () use ($user, $data, $parsedCustomizations, $optionsMap, $shippingZone) {
            // Lock products to prevent race conditions on stock
            $products = Product::whereIn('id', array_column($data['items'], 'product_id'))
                ->lockForUpdate()
                ->get()
                ->keyBy('id');

            $subtotal = 0;
            foreach ($data['items'] as $idx => $item) {
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
            if (!empty($data['voucher_code'])) {
                $voucher = Voucher::where('code', strtoupper($data['voucher_code']))
                    ->lockForUpdate()
                    ->first();
                if ($voucher && $voucher->isValid($subtotal)) {
                    $discountAmount = $voucher->calculateDiscount($subtotal);
                    $voucherCode    = $voucher->code;
                    $voucher->increment('used_count');
                }
            }

            $totalPrice = max(0, $subtotal + $shippingCost - $discountAmount);

            $shippingAddress = ($data['delivery_method'] ?? 'pickup') === 'pickup'
                ? 'Ambil di Toko'
                : ($data['shipping_address'] ?? '');

            $dpMinAmount  = config('app.dp_min_amount', 200000);
            $dpPercentage = config('app.dp_percentage', 50);
            $useDp        = !empty($data['use_dp']) && $totalPrice >= $dpMinAmount;
            $dpAmount     = $useDp ? round($totalPrice * $dpPercentage / 100) : 0;

            $order = Order::create([
                'user_id'          => $user->id,
                'order_code'       => 'ORD-' . strtoupper(Str::random(8)),
                'status'           => 'pending',
                'shipping_address' => $shippingAddress,
                'total_price'      => $totalPrice,
                'notes'            => $data['notes'] ?? null,
                'delivery_method'  => $data['delivery_method'] ?? 'pickup',
                'delivery_date'    => $data['delivery_date'] ?? null,
                'delivery_slot'    => $data['delivery_slot'] ?? null,
                'shipping_cost'    => $shippingCost,
                'voucher_code'     => $voucherCode,
                'discount_amount'  => $discountAmount,
                'payment_status'   => $useDp ? 'dp' : 'unpaid',
                'dp_amount'        => $dpAmount,
                'paid_amount'      => 0,
            ]);

            foreach ($data['items'] as $idx => $item) {
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

            $paymentMethod = $data['payment_method'] ?? 'transfer_bank';
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
        });
    }
}
