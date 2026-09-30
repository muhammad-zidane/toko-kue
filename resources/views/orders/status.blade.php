@extends('layouts.main')

@section('title')Jagoan Kue - Status Pesanan {{ $order->order_code }}@endsection

@section('content')
<div class="bg-cream min-h-screen py-8 px-6">
    <div class="max-w-[1140px] mx-auto">
        <h1 class="font-heading text-3xl font-bold text-brown-dark mb-1">Status Pesanan</h1>
        <p class="text-sm text-text-secondary mb-6">{{ $order->order_code }} &middot; {{ $order->created_at->translatedFormat('d F Y, H:i') }}</p>

        {{-- Status Timeline --}}
        <div class="bg-white rounded-2xl border border-cream-border p-6 mb-6 shadow-sm">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b border-cream-border pb-4 mb-6">
                <div>
                    <h2 class="font-heading text-xl font-bold text-brown-dark">Lacak Pesanan</h2>
                    <p class="text-xs text-text-secondary mt-0.5">Terakhir diperbarui: {{ $order->updated_at->translatedFormat('d F Y, H:i') }}</p>
                </div>
                @php
                    $statusLabels = [
                        'pending'    => 'Menunggu Konfirmasi',
                        'processing' => 'Diproses',
                        'shipped'    => 'Dikirim',
                        'completed'  => 'Selesai',
                        'cancelled'  => 'Dibatalkan',
                    ];
                @endphp
                <span class="inline-flex items-center gap-1.5 px-3 py-1 text-xs font-bold rounded-full badge-{{ $order->status === 'cancelled' ? 'red' : ($order->status === 'completed' ? 'green' : ($order->status === 'pending' ? 'gold' : 'blue')) }}">
                    {{ $statusLabels[$order->status] ?? ucfirst($order->status) }}
                </span>
            </div>

            @if($order->status === 'cancelled')
                {{-- Cancelled state: simple notice --}}
                <div class="flex items-center justify-center gap-0 py-4 max-w-md mx-auto flex-wrap">
                    @php $steps = [
                        ['pending',    'fa-clock',        'Pesanan\nMasuk'],
                        ['cancelled',  'fa-times-circle', 'Dibatalkan'],
                    ]; @endphp
                    @foreach($steps as $idx => [$key, $icon, $label])
                        <div class="flex flex-col items-center">
                            <div class="w-10 h-10 rounded-full flex items-center justify-center text-base bg-red-100 text-red-600 border border-red-200">
                                <i class="fa-solid fa-{{ $icon === 'fa-clock' ? 'clock' : 'xmark' }}"></i>
                            </div>
                            <div class="text-xs font-semibold mt-2 text-center text-red-700">{!! nl2br(e(str_replace('\n', "\n", $label))) !!}</div>
                        </div>
                        @if($idx === 0)
                            <div class="flex-1 min-w-[60px] h-0.5 bg-red-200 mb-6"></div>
                        @endif
                    @endforeach
                </div>
                <div class="bg-red-50 border border-red-200 text-red-700 rounded-xl p-4 text-xs font-semibold flex items-center gap-2 mt-4 justify-center">
                    <i class="fa-solid fa-circle-exclamation text-sm shrink-0"></i>
                    <p>Pesanan ini telah dibatalkan. Hubungi kami jika ada pertanyaan.</p>
                </div>
            @else
                @php
                    $steps = [
                        ['pending',    'fa-clock',          'Menunggu\nKonfirmasi'],
                        ['processing', 'fa-gear',           'Sedang\nDiproses'],
                        ['shipped',    'fa-truck',          'Dalam\nPengiriman'],
                        ['completed',  'fa-circle-check',  'Selesai'],
                    ];
                    $statusOrder = ['pending' => 0, 'processing' => 1, 'shipped' => 2, 'completed' => 3];
                    $currentIdx  = $statusOrder[$order->status] ?? 0;
                @endphp
                <div class="flex items-center justify-center gap-0 py-4 max-w-2xl mx-auto flex-wrap">
                    @foreach($steps as $i => [$key, $icon, $label])
                        @php
                            $stepIdx = $statusOrder[$key];
                            $isDone = $stepIdx < $currentIdx;
                            $isActive = $stepIdx === $currentIdx;
                        @endphp
                        <div class="flex flex-col items-center">
                            <div class="w-10 h-10 rounded-full flex items-center justify-center text-sm font-bold {{ $isDone ? 'bg-green-500 text-white' : ($isActive ? 'bg-primary text-white shadow-gold' : 'bg-cream-dark text-brown-light') }}">
                                <i class="fa-solid {{ $isDone ? 'fa-check' : 'fa-' . substr($icon, 3) }}"></i>
                            </div>
                            <div class="text-xs font-semibold mt-2 text-center {{ $isDone ? 'text-green-700 font-bold' : ($isActive ? 'text-primary font-bold' : 'text-text-muted') }}">{!! nl2br(e(str_replace('\n', "\n", $label))) !!}</div>
                        </div>
                        @if($i < count($steps) - 1)
                            <div class="flex-1 min-w-[30px] sm:min-w-[60px] h-0.5 mb-6 {{ $stepIdx < $currentIdx ? 'bg-green-500' : 'bg-cream-border' }}"></div>
                        @endif
                    @endforeach
                </div>
            @endif
        </div>

        {{-- Detail Grid --}}
        <div class="grid grid-cols-1 lg:grid-cols-[1.3fr_1fr] gap-8 items-start mb-8">
            {{-- Produk --}}
            <div class="bg-white rounded-2xl border border-cream-border p-6 shadow-sm">
                <p class="font-heading text-lg font-bold text-brown-dark mb-4 pb-2 border-b border-cream-border">Produk Dipesan</p>
                <div class="divide-y divide-cream-border mb-4">
                @foreach($order->orderItems as $item)
                    <div class="flex items-start gap-4 py-4 first:pt-0">
                        <img src="{{ $item->product && $item->product->image ? asset('storage/' . $item->product->image) : 'https://images.unsplash.com/photo-1563729784474-d77dbb933a9e?w=200&q=80' }}"
                             alt="{{ $item->product->name ?? 'Produk' }}" class="w-16 h-16 rounded-xl object-cover shrink-0">
                        <div class="flex-1 min-w-0">
                            <p class="font-semibold text-sm text-brown-dark truncate">{{ $item->product->name ?? 'Produk dihapus' }}</p>
                            <small class="text-xs text-text-secondary mt-0.5 block">{{ $item->quantity }}x &middot; Rp {{ number_format($item->price, 0, ',', '.') }}</small>
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

                <div class="flex justify-between items-center pt-4 border-t border-cream-border font-bold text-brown-dark">
                    <span>Total Pembayaran</span>
                    <span class="text-xl text-primary font-extrabold">Rp {{ number_format($order->total_price, 0, ',', '.') }}</span>
                </div>
            </div>

            {{-- Info Pesanan --}}
            <div class="space-y-4">
                <div class="bg-white rounded-2xl border border-cream-border p-6 shadow-sm">
                    <p class="font-heading text-lg font-bold text-brown-dark mb-4 pb-2 border-b border-cream-border">Info Pesanan</p>
                    <div class="space-y-3.5 text-sm">
                        <div class="flex justify-between py-1 border-b border-cream-border/30">
                            <span class="text-text-secondary">Nomor Pesanan</span>
                            <span class="font-semibold text-brown-dark">{{ $order->order_code }}</span>
                        </div>
                        <div class="flex justify-between py-1 border-b border-cream-border/30">
                            <span class="text-text-secondary">Tanggal Pesan</span>
                            <span class="font-semibold text-brown-dark">{{ $order->created_at->translatedFormat('d M Y, H:i') }}</span>
                        </div>
                        <div class="flex justify-between py-1 border-b border-cream-border/30 items-center">
                            <span class="text-text-secondary">Status</span>
                            <span class="font-semibold">
                                <span class="inline-block px-2.5 py-0.5 text-xs font-bold rounded-full badge-{{ $order->status === 'cancelled' ? 'red' : ($order->status === 'completed' ? 'green' : ($order->status === 'pending' ? 'gold' : 'blue')) }}">
                                    {{ $statusLabels[$order->status] ?? ucfirst($order->status) }}
                                </span>
                            </span>
                        </div>
                        <div class="flex justify-between py-1 border-b border-cream-border/30 items-center">
                            <span class="text-text-secondary">Pembayaran</span>
                            <span class="font-semibold">
                                <span class="inline-block px-2.5 py-0.5 text-xs font-bold rounded-full badge-{{ ($order->payment->status ?? 'unpaid') === 'paid' ? 'green' : 'gold' }}">
                                    {{ $order->payment?->status_label ?? 'Belum Bayar' }}
                                </span>
                            </span>
                        </div>
                        <div class="flex justify-between py-1 border-b border-cream-border/30">
                            <span class="text-text-secondary">Metode</span>
                            <span class="font-semibold text-brown-dark">
                                {{ ['transfer_bank' => 'Transfer Bank', 'ewallet' => 'E-Wallet', 'qris' => 'QRIS', 'cod' => 'COD'][$order->payment->payment_method ?? ''] ?? ucfirst($order->payment->payment_method ?? '-') }}
                            </span>
                        </div>
                        <div class="flex justify-between py-1 border-b border-cream-border/30 flex-col gap-1">
                            <span class="text-text-secondary">Alamat Kirim</span>
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

                @if($order->payment_status === 'dp' && $order->paid_amount > 0 && $order->payment?->status !== 'paid')
                    <a href="{{ route('orders.payment', $order) }}" class="btn-primary w-full py-3.5 text-sm font-bold justify-center">Bayar Sisa Rp {{ number_format($order->total_price - $order->paid_amount, 0, ',', '.') }}</a>
                @endif

                @if($order->status === 'pending' && $order->payment && $order->payment->status === 'unpaid' && $order->payment->payment_method !== 'cod')
                    <a href="{{ route('orders.payment', $order) }}" class="btn-primary w-full py-3.5 text-sm font-bold justify-center">
                        <i class="fa-solid fa-credit-card mr-2"></i> Bayar Sekarang
                    </a>
                @endif
            </div>
        </div>

        <div class="flex flex-wrap gap-4 items-center justify-between mt-6">
            <a href="{{ route('orders.index') }}" class="btn-ghost py-2.5 px-6 text-xs">
                <i class="fa-solid fa-arrow-left mr-1.5"></i> Daftar Pesanan
            </a>
            <a href="{{ route('orders.show', $order) }}" class="btn-secondary py-2.5 px-6 text-xs">
                <i class="fa-solid fa-receipt mr-1.5"></i> Detail Lengkap
            </a>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="{{ asset('js/app.js') }}" defer></script>
@endpush
