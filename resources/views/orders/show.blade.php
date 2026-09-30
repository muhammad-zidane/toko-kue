@extends('layouts.main')

@section('title')Jagoan Kue - Detail Pesanan {{ $order->order_code }}@endsection

@section('content')
<div class="bg-cream min-h-screen py-8 px-6">
    <div class="max-w-[1140px] mx-auto">
        <h1 class="font-heading text-3xl font-bold text-brown-dark mb-1">Detail Pesanan</h1>
        <p class="text-sm text-text-secondary mb-6">Kode: {{ $order->order_code }} · {{ $order->created_at->format('d M Y, H:i') }}</p>

        <div class="grid grid-cols-1 lg:grid-cols-[1.3fr_1fr] gap-8 items-start mb-8">
            <div class="bg-white rounded-2xl border border-cream-border p-6 shadow-sm">
                <p class="font-heading text-lg font-bold text-brown-dark mb-4 pb-2 border-b border-cream-border">PRODUK DIPESAN</p>
                <div class="divide-y divide-cream-border mb-4">
                @foreach($order->orderItems as $item)
                <div class="flex items-start gap-4 py-4 first:pt-0">
                    <img src="{{ $item->product && $item->product->image ? asset('storage/' . $item->product->image) : 'https://images.unsplash.com/photo-1563729784474-d77dbb933a9e?w=200&q=80' }}" alt="{{ $item->product->name ?? 'Produk' }}" class="w-16 h-16 rounded-xl object-cover shrink-0">
                    <div class="flex-1 min-w-0">
                        <p class="font-semibold text-sm text-brown-dark truncate">{{ $item->product->name ?? 'Produk dihapus' }}</p>
                        <small class="text-xs text-text-secondary mt-0.5 block">{{ $item->quantity }}x · Rp {{ number_format($item->price, 0, ',', '.') }}</small>
                        @if($item->customizations->isNotEmpty())
                            <p class="text-xs text-purple-700 font-semibold bg-purple-50 border border-purple-100 rounded-lg p-2 flex items-center gap-1.5 mt-1.5">
                                <i class="fas fa-paint-brush"></i>
                                {{ $item->customizations->map(fn($c) => $c->option?->name)->filter()->join(', ') }}
                            </p>
                        @endif
                        @if(!empty($item->note))
                            <p class="text-xs text-text-muted mt-1 bg-cream-warm/50 border border-cream-border rounded-lg p-2 flex items-start gap-1">
                                <i class="fa-solid fa-note-sticky text-[10px] mt-1 text-primary shrink-0"></i>
                                <span>{{ $item->note }}</span>
                            </p>
                        @endif
                    </div>
                    <span class="font-bold text-sm text-primary shrink-0 ml-auto">Rp {{ number_format($item->price * $item->quantity, 0, ',', '.') }}</span>
                </div>
                @endforeach
                </div>

                @php $subtotalItems = $order->orderItems->sum(fn($i) => $i->price * $i->quantity); @endphp
                <div class="space-y-2 mb-4 border-t border-cream-border pt-4 text-xs text-text-secondary">
                    <div class="flex justify-between items-center">
                        <span>Subtotal Produk</span>
                        <span class="font-semibold text-brown-dark">Rp {{ number_format($subtotalItems, 0, ',', '.') }}</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span>Ongkos Kirim</span>
                        <span class="font-semibold text-brown-dark">{{ $order->shipping_cost > 0 ? 'Rp ' . number_format($order->shipping_cost, 0, ',', '.') : 'Gratis' }}</span>
                    </div>
                    @if($order->discount_amount > 0)
                    <div class="flex justify-between items-center text-green-700 font-semibold">
                        <span>Diskon Voucher</span>
                        <span>-Rp {{ number_format($order->discount_amount, 0, ',', '.') }}</span>
                    </div>
                    @endif
                </div>

                <div class="flex justify-between items-center pt-4 border-t border-cream-border font-bold text-brown-dark">
                    <span>Total Harga</span>
                    <span class="text-xl text-primary font-extrabold">Rp {{ number_format($order->total_price, 0, ',', '.') }}</span>
                </div>

                @php
                    $reviewByProduct = $order->productReviews->keyBy('product_id');
                @endphp

                @foreach($order->orderItems as $item)
                    @php
                        $review = $item->product ? ($reviewByProduct[$item->product->id] ?? null) : null;
                    @endphp
                    @if($review)
                        <div class="mt-6 bg-cream-warm border border-cream-border rounded-2xl p-5">
                            <p class="text-sm font-bold text-brown-dark mb-1.5">Ulasan: {{ $item->product->name }}</p>
                            <div class="text-amber-400 text-xs mb-2">
                                {{ str_repeat('★', (int) $review->rating) }}{{ str_repeat('☆', 5 - (int) $review->rating) }}
                            </div>
                            <p class="text-xs text-text-secondary leading-relaxed mb-3">{{ $review->comment }}</p>
                            @if($review->images->isNotEmpty())
                                <div class="flex gap-2 flex-wrap">
                                    @foreach($review->images as $image)
                                        <img src="{{ asset('storage/' . $image->path) }}" alt="Gambar ulasan" class="w-16 h-16 rounded-xl object-cover border border-cream-border">
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    @endif
                @endforeach
            </div>

            <div class="space-y-4">
                <div class="bg-white rounded-2xl border border-cream-border p-6 shadow-sm">
                    <p class="font-heading text-lg font-bold text-brown-dark mb-4 pb-2 border-b border-cream-border">INFO PESANAN</p>
                    <div class="space-y-3.5 text-sm">
                        <div class="flex justify-between py-1 border-b border-cream-border/30 items-center">
                            <span class="text-text-secondary">Status</span>
                            <span>
                                <span class="inline-block px-2.5 py-0.5 text-xs font-bold rounded-full badge-{{ $order->status === 'cancelled' ? 'red' : ($order->status === 'completed' ? 'green' : ($order->status === 'pending' ? 'gold' : 'blue')) }}">
                                    {{ ucfirst($order->status) }}
                                </span>
                            </span>
                        </div>
                        @php
                            $payStatus = $order->payment_status ?? $order->payment?->status ?? 'unpaid';
                            $payLabel  = $payStatus === 'dp' ? 'DP 50%' : ($order->payment?->status_label ?? 'Belum Bayar');
                        @endphp
                        <div class="flex justify-between py-1 border-b border-cream-border/30 items-center">
                            <span class="text-text-secondary">Pembayaran</span>
                            <span>
                                <span class="inline-block px-2.5 py-0.5 text-xs font-bold rounded-full badge-{{ $payStatus === 'paid' ? 'green' : 'gold' }}">
                                    {{ $payLabel }}
                                </span>
                            </span>
                        </div>
                        @if(($order->payment_status ?? '') === 'dp')
                        <div class="flex justify-between py-1 border-b border-cream-border/30">
                            <span class="text-text-secondary">DP Dibayar</span>
                            <span class="font-semibold text-brown-dark">Rp {{ number_format($order->paid_amount, 0, ',', '.') }}</span>
                        </div>
                        <div class="flex justify-between py-1 border-b border-cream-border/30">
                            <span class="text-text-secondary">Sisa Pembayaran</span>
                            <span class="font-bold text-red-650">Rp {{ number_format($order->total_price - $order->paid_amount, 0, ',', '.') }}</span>
                        </div>
                        @endif
                        <div class="flex justify-between py-1 border-b border-cream-border/30">
                            <span class="text-text-secondary">Metode</span>
                            <span class="font-semibold text-brown-dark">{{ ['transfer_bank' => 'Transfer Bank', 'ewallet' => 'E-Wallet', 'qris' => 'QRIS', 'cod' => 'COD'][$order->payment->payment_method ?? ''] ?? ucfirst($order->payment->payment_method ?? '-') }}</span>
                        </div>
                        <div class="flex justify-between py-1 border-b border-cream-border/30 flex-col gap-1">
                            <span class="text-text-secondary">Alamat</span>
                            <span class="font-semibold text-brown-dark leading-relaxed">{{ $order->shipping_address }}</span>
                        </div>
                        @if($order->notes)
                        <div class="flex justify-between py-1 flex-col gap-1">
                            <span class="text-text-secondary">Catatan</span>
                            <span class="font-semibold text-brown-dark leading-relaxed">{{ $order->notes }}</span>
                        </div>
                        @endif
                    </div>
                </div>

                @if($order->status === 'pending' && $order->payment && $order->payment->status === 'unpaid' && $order->payment->payment_method !== 'cod' && !$order->payment->proof_image)
                <a href="{{ route('orders.payment', $order) }}" class="btn-primary w-full py-3 text-center block text-sm font-bold justify-center">Bayar Sekarang</a>
                @endif

                @if($order->payment_status === 'dp' && $order->paid_amount > 0 && $order->payment?->status !== 'paid')
                <a href="{{ route('orders.payment', $order) }}" class="btn-primary w-full py-3 text-center block text-sm font-bold justify-center">Bayar Sisa Rp {{ number_format($order->total_price - $order->paid_amount, 0, ',', '.') }}</a>
                @endif

                @if(in_array($order->status, ['processing', 'completed']))
                <a href="{{ route('orders.invoice', $order) }}" target="_blank" class="btn-ghost w-full py-3 text-center block text-sm font-semibold justify-center">
                    <i class="fas fa-file-pdf mr-2"></i> Unduh Invoice (PDF)
                </a>
                @endif
            </div>
        </div>

        <div class="mt-6">
            <a href="{{ route('orders.index') }}" class="btn-ghost py-2.5 px-6 text-xs">
                <i class="fa-solid fa-arrow-left mr-1.5"></i> Kembali ke Daftar Pesanan
            </a>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="{{ asset('js/app.js') }}" defer></script>
@endpush
