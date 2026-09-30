@extends('layouts.main')

@section('title', 'Jagoan Kue - Riwayat Pesanan')

@section('content')
<div class="bg-cream min-h-screen py-8 px-6">
    <div class="max-w-[1140px] mx-auto">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
            <div>
                <h1 class="font-heading text-3xl font-bold text-brown-dark mb-1">Riwayat Pesanan</h1>
                <p class="text-sm text-text-secondary">Lihat status pesanan dan lanjutkan pembayaran bila diperlukan.</p>
            </div>
            <a href="/products" class="btn-primary shrink-0">+ Belanja Lagi</a>
        </div>

        @if (session('success'))
            <div class="bg-green-50 border border-green-200 text-green-700 rounded-xl p-4 text-xs font-semibold mb-6">
                {{ session('success') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="bg-red-50 border border-red-200 text-red-700 rounded-xl p-4 text-xs font-semibold mb-6">
                {{ $errors->first() }}
            </div>
        @endif

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            @forelse ($orders as $order)
                @php
                    $paymentStatus = $order->payment?->status ?? 'unpaid';
                    $status = $order->status ?? 'pending';
                    $firstItem = $order->orderItems->first();
                    $thumbPath = $firstItem?->product?->image ? asset('storage/' . $firstItem->product->image) : 'https://images.unsplash.com/photo-1563729784474-d77dbb933a9e?w=200&q=80';
                @endphp

                <div class="bg-white rounded-2xl border border-cream-border p-6 shadow-sm flex flex-col justify-between">
                    <div>
                        <div class="flex items-start gap-4 mb-4">
                            <img src="{{ $thumbPath }}" alt="Produk" class="w-16 h-16 rounded-xl object-cover shrink-0">
                            <div class="flex-1 min-w-0">
                                <p class="text-[11px] text-text-muted font-medium mb-0.5">{{ $order->created_at?->format('d M Y, H:i') }} WIB</p>
                                <p class="font-mono text-sm font-bold text-brown-dark truncate mb-1">{{ $order->order_code }}</p>
                                <p class="text-sm text-text-secondary">Total: <strong class="text-primary font-bold">Rp {{ number_format((int) $order->total_price, 0, ',', '.') }}</strong></p>
                                <p class="text-[10px] text-text-muted mt-0.5">Ongkir: {{ $order->shipping_cost > 0 ? 'Rp ' . number_format((int)$order->shipping_cost, 0, ',', '.') : 'Gratis' }}</p>
                            </div>
                        </div>

                        <div class="flex flex-wrap gap-2 mb-4">
                            <span class="inline-block px-2.5 py-0.5 text-xs font-bold rounded-full badge-{{ $status === 'completed' ? 'green' : ($status === 'cancelled' ? 'red' : 'blue') }}">
                                {{ ucfirst($status) }}
                            </span>
                            <span class="inline-block px-2.5 py-0.5 text-xs font-bold rounded-full badge-{{ $paymentStatus === 'paid' ? 'green' : 'gold' }}">
                                {{ $order->payment?->status_label ?? 'Belum Bayar' }}
                            </span>
                        </div>

                        @if($paymentStatus === 'dp')
                        @php $sisaBayar = $order->total_price - $order->paid_amount; @endphp
                        <div class="bg-amber-50 border border-amber-200 rounded-xl p-3 text-xs text-amber-800 font-semibold mb-4 flex items-center gap-1.5">
                            <i class="fas fa-exclamation-circle text-sm shrink-0"></i>
                            <span>Sisa pembayaran: Rp {{ number_format($sisaBayar, 0, ',', '.') }}</span>
                        </div>
                        @endif
                    </div>

                    <div class="flex flex-wrap gap-2 items-center border-t border-cream-border pt-4 mt-2">
                        <a class="btn-ghost py-2 px-4 text-xs" href="{{ route('orders.show', $order) }}">Detail</a>
                        <a class="btn-primary py-2 px-4 text-xs flex items-center gap-1.5" href="{{ route('orders.status', $order) }}">
                            <i class="fa-solid fa-location-dot"></i> Lacak
                        </a>

                        @if ($status === 'completed' && $paymentStatus === 'paid')
                            <a class="btn-secondary py-2 px-4 text-xs" href="{{ route('orders.reviews.index', $order) }}">Ulasan</a>
                        @endif

                        @if ($status === 'pending' && $paymentStatus === 'unpaid' && !($order->payment && $order->payment->proof_image))
                            <a class="btn-primary py-2 px-4 text-xs" href="{{ route('orders.payment', $order) }}">Bayar Sekarang</a>
                        @endif

                        @if ($paymentStatus === 'dp')
                            <a class="btn-primary py-2 px-4 text-xs bg-amber-500 hover:bg-amber-600 border-0" href="{{ route('orders.payment', $order) }}">Bayar Sisa</a>
                        @endif
                    </div>
                </div>
            @empty
                <div class="col-span-full text-center py-20 bg-white rounded-2xl border border-cream-border p-6 flex flex-col items-center justify-center shadow-sm">
                    <p class="text-lg font-bold text-brown-dark mb-1">Belum ada pesanan</p>
                    <p class="text-xs text-text-secondary mb-4">Yuk mulai belanja kue favoritmu.</p>
                    <a href="/products" class="btn-primary">Lihat Katalog</a>
                </div>
            @endforelse
        </div>

        <div class="mt-6 flex justify-center">
            {{ $orders->links() }}
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="{{ asset('js/app.js') }}" defer></script>
@endpush
