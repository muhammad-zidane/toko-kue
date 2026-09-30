@extends('admin.layout')
@section('title', 'Detail Pesanan')
@section('page-title', 'Detail Pesanan #' . $order->order_code)
@section('page-subtitle', 'Dibuat pada ' . $order->created_at->format('d M Y, H:i'))

@section('content')
<div class="mb-4">
    <a href="{{ route('admin.orders.index') }}" class="text-xs font-bold text-brown-dark hover:text-primary transition-all inline-flex items-center gap-1 no-underline">← Kembali ke Pesanan</a>
</div>

<div class="grid grid-cols-1 lg:grid-cols-[2fr_1.2fr] gap-6">
    {{-- KIRI --}}
    <div>
        {{-- DAFTAR ITEM --}}
        <div class="bg-white rounded-2xl border border-cream-border p-6 shadow-sm mb-6">
            <div class="font-heading text-lg font-bold text-brown-dark mb-4 pb-2 border-b border-cream-border/50">Produk Dipesan ({{ $order->orderItems->count() }} item)</div>
            @foreach($order->orderItems as $item)
            <div class="flex gap-4 py-4 border-b border-cream-border/40 last:border-b-0 last:pb-0 items-start">
                <div class="w-14 h-14 bg-cream rounded-xl border border-cream-border flex items-center justify-center text-xl overflow-hidden flex-shrink-0">
                    @if($item->product->image)
                        <img src="{{ asset('storage/' . $item->product->image) }}" alt="{{ $item->product->name }}" class="w-full h-full object-cover">
                    @else
                        <span>🧁</span>
                    @endif
                </div>
                <div>
                    <div class="font-bold text-brown-dark text-sm">{{ $item->product->name }}</div>
                    <div class="text-xs text-text-secondary mt-0.5">{{ $item->quantity }} x Rp {{ number_format($item->price, 0, ',', '.') }}</div>
                    @if($item->customizations->isNotEmpty())
                    <div class="text-xs text-purple-700 font-semibold mt-1.5 flex items-center gap-1.5">
                        <i class="fas fa-paint-brush text-[10px]"></i>
                        {{ $item->customizations->map(fn($c) => $c->option?->name)->filter()->join(', ') }}
                    </div>
                    @endif
                    @if(!empty($item->note))
                    <div class="text-xs text-brown-mid bg-cream/60 px-3 py-2 rounded-lg mt-2 border border-cream-border/50">Catatan: {{ $item->note }}</div>
                    @endif
                </div>
                <div class="font-bold text-brown-dark text-sm ml-auto">Rp {{ number_format($item->price * $item->quantity, 0, ',', '.') }}</div>
            </div>
            @endforeach
        </div>

        {{-- CATATAN --}}
        @if($order->notes)
        <div class="bg-white rounded-2xl border border-cream-border p-6 shadow-sm mb-6">
            <div class="font-heading text-lg font-bold text-brown-dark mb-4 pb-2 border-b border-cream-border/50">Catatan Toko</div>
            <p class="text-sm text-text-secondary leading-relaxed">{{ $order->notes }}</p>
        </div>
        @endif
    </div>

    {{-- KANAN --}}
    <div>
        {{-- INFO PESANAN --}}
        <div class="bg-white rounded-2xl border border-cream-border p-6 shadow-sm mb-6">
            <div class="font-heading text-lg font-bold text-brown-dark mb-4 pb-2 border-b border-cream-border/50">Informasi Pesanan</div>
            <div class="flex justify-between items-center py-2.5 border-b border-cream-border/40 last:border-b-0 last:pb-0">
                <span class="text-xs text-text-secondary font-medium">Kode Pesanan</span>
                <span class="text-xs font-bold text-brown-dark">{{ $order->order_code }}</span>
            </div>
            <div class="flex justify-between items-center py-2.5 border-b border-cream-border/40 last:border-b-0 last:pb-0">
                <span class="text-xs text-text-secondary font-medium">Status</span>
                @php
                    $statusClass = match($order->status) {
                        'completed' => 'bg-green-50 text-green-700',
                        'processing' => 'bg-blue-50 text-blue-700',
                        'cancelled' => 'bg-red-50 text-red-700',
                        default => 'bg-amber-50 text-amber-700',
                    };
                @endphp
                <span class="inline-flex items-center justify-center px-2.5 py-0.5 rounded-full text-xs font-bold {{ $statusClass }}">{{ ucfirst($order->status) }}</span>
            </div>
            <div class="flex justify-between items-center py-2.5 border-b border-cream-border/40 last:border-b-0 last:pb-0">
                <span class="text-xs text-text-secondary font-medium">Total</span>
                <span class="text-xs font-bold text-primary">Rp {{ number_format($order->total_price, 0, ',', '.') }}</span>
            </div>
            <div class="flex justify-between items-center py-2.5 border-b border-cream-border/40 last:border-b-0 last:pb-0">
                <span class="text-xs text-text-secondary font-medium">Alamat Pengiriman</span>
                <span class="text-xs font-bold text-brown-dark max-w-[200px] text-right">{{ $order->shipping_address }}</span>
            </div>

            {{-- UBAH STATUS --}}
            <div class="font-heading text-sm font-bold text-brown-dark mt-6 mb-3">Ubah Status</div>
            <div class="flex flex-wrap gap-2">
                @if($order->status !== 'processing')
                <form method="POST" action="{{ route('admin.orders.status', [$order, 'processing']) }}" class="m-0">
                    @csrf @method('PATCH')
                    <button type="submit" class="text-xs px-4 py-2 rounded-full border border-blue-200 bg-blue-50 text-blue-700 hover:bg-blue-600 hover:text-white hover:border-blue-600 transition-all cursor-pointer font-semibold">Proses</button>
                </form>
                @endif
                @if($order->status !== 'completed')
                <form method="POST" action="{{ route('admin.orders.status', [$order, 'completed']) }}" class="m-0">
                    @csrf @method('PATCH')
                    <button type="submit" class="text-xs px-4 py-2 rounded-full border border-green-200 bg-green-50 text-green-700 hover:bg-green-600 hover:text-white hover:border-green-600 transition-all cursor-pointer font-semibold">Selesai</button>
                </form>
                @endif
                @if($order->status !== 'cancelled')
                <form method="POST" action="{{ route('admin.orders.status', [$order, 'cancelled']) }}" class="m-0">
                    @csrf @method('PATCH')
                    <button type="submit" class="text-xs px-4 py-2 rounded-full border border-red-200 bg-red-50 text-red-700 hover:bg-red-600 hover:text-white hover:border-red-600 transition-all cursor-pointer font-semibold">Batal</button>
                </form>
                @endif
            </div>
        </div>

        {{-- INFO PELANGGAN --}}
        <div class="bg-white rounded-2xl border border-cream-border p-6 shadow-sm mb-6">
            <div class="font-heading text-lg font-bold text-brown-dark mb-4 pb-2 border-b border-cream-border/50">Pelanggan</div>
            <div class="flex items-center gap-3">
                <div class="w-11 h-11 rounded-full bg-blue-500 text-white font-bold text-sm flex items-center justify-center">
                    {{ strtoupper(substr($order->user->name ?? '-', 0, 2)) }}
                </div>
                <div>
                    <div class="text-sm font-bold text-brown-dark">{{ $order->user->name ?? '-' }}</div>
                    <div class="text-xs text-text-secondary mt-0.5">{{ $order->user->email ?? '-' }}</div>
                </div>
            </div>
        </div>

        {{-- INFO PEMBAYARAN --}}
        <div class="bg-white rounded-2xl border border-cream-border p-6 shadow-sm mb-6">
            <div class="font-heading text-lg font-bold text-brown-dark mb-4 pb-2 border-b border-cream-border/50">Pembayaran</div>
            @if($order->payment)
            <div class="flex justify-between items-center py-2.5 border-b border-cream-border/40 last:border-b-0 last:pb-0">
                <span class="text-xs text-text-secondary font-medium">Metode</span>
                <span class="text-xs font-bold text-brown-dark">{{ ['transfer_bank'=>'Transfer Bank','ewallet'=>'E-Wallet','qris'=>'QRIS','cod'=>'COD'][$order->payment->payment_method ?? ''] ?? strtoupper($order->payment->payment_method ?? '-') }}</span>
            </div>
            <div class="flex justify-between items-center py-2.5 border-b border-cream-border/40 last:border-b-0 last:pb-0">
                <span class="text-xs text-text-secondary font-medium">Status</span>
                @php
                    $payStatusClass = match($order->payment->status) {
                        'paid' => 'bg-green-50 text-green-700',
                        'failed' => 'bg-red-50 text-red-700',
                        default => 'bg-amber-50 text-amber-700',
                    };
                @endphp
                <span class="inline-flex items-center justify-center px-2.5 py-0.5 rounded-full text-xs font-bold {{ $payStatusClass }}">{{ $order->payment?->status_label ?? 'Belum Bayar' }}</span>
            </div>
            <div class="flex justify-between items-center py-2.5 border-b border-cream-border/40 last:border-b-0 last:pb-0">
                <span class="text-xs text-text-secondary font-medium">Jumlah</span>
                <span class="text-xs font-bold text-brown-dark">Rp {{ number_format($order->payment->amount, 0, ',', '.') }}</span>
            </div>
            @if($order->dp_amount > 0)
            <div class="flex justify-between items-center py-2.5 border-b border-cream-border/40">
                <span class="text-xs text-text-secondary font-medium">Total Terkonfirmasi</span>
                <span class="text-xs font-bold text-brown-dark">Rp {{ number_format($order->paid_amount, 0, ',', '.') }}</span>
            </div>
            <div class="flex justify-between items-center py-2.5 border-b border-cream-border/40">
                <span class="text-xs text-text-secondary font-medium">Sisa Tagihan</span>
                <span class="text-xs font-bold text-brown-dark">Rp {{ number_format(max(0, $order->total_price - $order->paid_amount), 0, ',', '.') }}</span>
            </div>
            @endif
            @if($order->payment->paid_at)
            <div class="flex justify-between items-center py-2.5 border-b border-cream-border/40 last:border-b-0 last:pb-0">
                <span class="text-xs text-text-secondary font-medium">Dibayar Pada</span>
                <span class="text-xs font-bold text-brown-dark">{{ $order->payment->paid_at->format('d M Y, H:i') ?? '-' }}</span>
            </div>
            @endif
            @if($order->payment->proof_image)
            <div class="mt-4">
                <p class="text-xs font-bold text-text-secondary mb-2">Bukti Pembayaran:</p>
                <img src="{{ asset('storage/' . $order->payment->proof_image) }}"
                     alt="Bukti Pembayaran"
                     class="max-w-[100px] [&.expanded]:max-w-full rounded-lg border border-cream-border transition-all duration-300"
                     onclick="this.classList.toggle('expanded')"
                     style="cursor:zoom-in;">
                <a href="{{ route('admin.orders.downloadProof', $order) }}" class="inline-flex items-center gap-1.5 text-xs px-4 py-2 mt-3 bg-primary text-white font-bold rounded-full shadow-gold hover:bg-primary-hover hover:-translate-y-0.5 transition-all cursor-pointer border-0 no-underline">
                    <i class="fas fa-download"></i> Unduh Bukti
                </a>
            </div>
            @if($order->payment->status === 'unpaid')
            <div class="mt-4 flex flex-wrap gap-2">
                <form method="POST" action="{{ route('admin.orders.confirmPayment', $order) }}" class="m-0">
                    @csrf
                    <button type="submit" class="text-xs px-4 py-2 rounded-full border border-green-200 bg-green-50 text-green-700 hover:bg-green-600 hover:text-white hover:border-green-600 transition-all cursor-pointer font-semibold inline-flex items-center gap-1" onclick="return confirm('Konfirmasi pembayaran ini?')">
                        <i class="fas fa-check"></i> Konfirmasi Pembayaran
                    </button>
                </form>
                <form method="POST" action="{{ route('admin.orders.rejectPayment', $order) }}" class="m-0 w-full mt-2">
                    @csrf
                    <input type="text" name="reason" placeholder="Alasan penolakan (opsional)" class="w-full border border-cream-border rounded-xl px-3 py-2 text-xs text-text-primary bg-white outline-none transition-all focus:border-primary mb-2">
                    <button type="submit" class="text-xs px-4 py-2 rounded-full border border-red-200 bg-red-50 text-red-700 hover:bg-red-600 hover:text-white hover:border-red-600 transition-all cursor-pointer font-semibold inline-flex items-center gap-1" onclick="return confirm('Tolak pembayaran ini?')">
                        <i class="fas fa-times"></i> Tolak Pembayaran
                    </button>
                </form>
            </div>
            @endif
            @endif
            @else
            <p class="text-xs text-text-secondary mt-2">Belum ada data pembayaran.</p>
            @endif
        </div>
    </div>
</div>
@endsection
