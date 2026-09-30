@extends('layouts.main')

@section('title', 'Jagoan Kue - Pesanan Berhasil')

@section('content')
<div class="bg-cream min-h-screen py-8 px-6">
    <div class="max-w-[1140px] mx-auto">
        <div class="text-center max-w-xl mx-auto mb-10">
            <div class="w-20 h-20 rounded-full bg-green-100 text-green-600 text-4xl flex items-center justify-center mx-auto mb-4">✓</div>
            <h1 class="font-heading text-3xl font-bold text-brown-dark mb-2">Pesanan Berhasil Ditempatkan!</h1>
            <p class="text-sm text-text-secondary mb-4">Terima kasih! Pesananmu sedang kami proses.</p>
            <div class="inline-block font-mono bg-cream-dark px-4 py-2 rounded-xl text-brown-dark font-bold">No. Pesanan: {{ $order->order_code }}</div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-[1.3fr_1fr] gap-8 items-start mb-8">
            <div class="bg-white rounded-2xl border border-cream-border p-6 shadow-sm">
                <p class="font-heading text-lg font-bold text-brown-dark mb-4 pb-2 border-b border-cream-border">Detail Pesanan</p>
                <div class="divide-y divide-cream-border mb-4">
                @foreach($order->orderItems as $item)
                <div class="flex items-start gap-4 py-4 first:pt-0">
                    <img src="{{ $item->product && $item->product->image ? asset('storage/' . $item->product->image) : 'https://images.unsplash.com/photo-1563729784474-d77dbb933a9e?w=200&q=80' }}" alt="" class="w-16 h-16 rounded-xl object-cover shrink-0">
                    <div class="flex-1 min-w-0">
                        <p class="font-semibold text-sm text-brown-dark truncate">{{ $item->product->name ?? 'Produk' }}</p>
                        <small class="text-xs text-text-secondary mt-0.5 block">{{ $item->quantity }}x</small>
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

                @php
                    $subtotalItems  = $order->orderItems->sum(fn($i) => $i->price * $i->quantity);
                    $isTransferBank = ($order->payment->payment_method ?? '') === 'transfer_bank';
                    $uniqueCode     = $isTransferBank ? 1000 : 0;
                @endphp
                
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
                    @if($isTransferBank)
                    <div class="flex justify-between items-center">
                        <span>Biaya Layanan (Kode Unik)</span>
                        <span class="font-semibold text-brown-dark">Rp {{ number_format($uniqueCode, 0, ',', '.') }}</span>
                    </div>
                    @endif
                </div>

                <div class="flex justify-between items-center pt-4 border-t border-cream-border font-bold text-brown-dark">
                    <span>Total Harga</span>
                    <span class="text-xl text-primary font-extrabold">Rp {{ number_format($order->total_price + $uniqueCode, 0, ',', '.') }}</span>
                </div>
            </div>

            <div class="space-y-4">
                <div class="bg-white rounded-2xl border border-cream-border p-6 shadow-sm">
                    <p class="font-heading text-lg font-bold text-brown-dark mb-4 pb-2 border-b border-cream-border">INFO PENGIRIMAN</p>
                    <div class="space-y-3 text-sm">
                        <div class="flex justify-between py-1 border-b border-cream-border/30">
                            <span class="text-text-secondary">Penerima</span>
                            <span class="font-semibold text-brown-dark">{{ auth()->user()->name }}</span>
                        </div>
                        <div class="flex justify-between items-start py-1 border-b border-cream-border/30 gap-4">
                            <span class="text-text-secondary shrink-0">Alamat</span>
                            <span class="font-semibold text-brown-dark leading-relaxed text-right">{{ $order->shipping_address }}</span>
                        </div>
                        <div class="flex justify-between py-1 border-b border-cream-border/30">
                            <span class="text-text-secondary">Pembayaran</span>
                            <span class="font-semibold text-brown-dark">{{ ['transfer_bank'=>'Transfer Bank','ewallet'=>'E-Wallet','qris'=>'QRIS','cod'=>'COD'][$order->payment->payment_method ?? ''] ?? ucfirst($order->payment->payment_method ?? '-') }}</span>
                        </div>
                        @php
                            $pStatus = $order->payment->status ?? 'unpaid';
                        @endphp
                        <div class="flex justify-between py-1 border-b border-cream-border/30 items-center">
                            <span class="text-text-secondary">Status Bayar</span>
                            <span class="font-semibold">
                                <span class="inline-block px-2.5 py-0.5 text-xs font-bold rounded-full badge-{{ $pStatus === 'paid' ? 'green' : 'gold' }}">
                                    {{ $order->payment?->status_label ?? 'Belum Bayar' }}
                                </span>
                            </span>
                        </div>
                        @if($order->notes)
                        <div class="flex justify-between py-1 flex-col gap-1">
                            <span class="text-text-secondary">Catatan</span>
                            <span class="font-semibold text-brown-dark leading-relaxed">{{ $order->notes }}</span>
                        </div>
                        @endif
                    </div>
                </div>

                <div class="bg-white rounded-2xl border border-cream-border p-6 shadow-sm">
                    <p class="font-heading text-lg font-bold text-brown-dark mb-4 pb-2 border-b border-cream-border">STATUS PESANAN</p>
                    <div class="relative pl-6 border-l border-cream-border ml-3 space-y-6 text-sm">
                        {{-- Step 1: Pesanan Diterima (done) --}}
                        <div class="relative">
                            <div class="absolute -left-[33px] top-0 w-5 h-5 rounded-full bg-green-500 text-white flex items-center justify-center text-[10px] font-bold"><i class="fas fa-check"></i></div>
                            <p class="font-semibold text-brown-dark">Pesanan Diterima</p>
                            <small class="text-xs text-text-muted block mt-0.5">{{ $order->created_at->format('d M Y, H:i') }}</small>
                        </div>
                        {{-- Step 2: Sedang dipersiapkan (active) --}}
                        <div class="relative">
                            <div class="absolute -left-[33px] top-0 w-5 h-5 rounded-full bg-primary text-white flex items-center justify-center text-[8px] shadow-gold"><i class="fas fa-circle"></i></div>
                            <p class="font-semibold text-brown-dark">Sedang Dipersiapkan</p>
                            <small class="text-xs text-text-muted block mt-0.5">Kue sedang dibuat oleh tim kami</small>
                        </div>
                        {{-- Step 3: Dalam pengiriman (pending) --}}
                        <div class="relative">
                            <div class="absolute -left-[31px] top-0 w-4.5 h-4.5 rounded-full bg-cream-dark border border-cream-border"></div>
                            <p class="font-semibold text-text-muted">Dalam Pengiriman</p>
                        </div>
                        {{-- Step 4: Pesanan diterima (pending) --}}
                        <div class="relative">
                            <div class="absolute -left-[33px] top-0 w-5 h-5 rounded-full bg-cream-dark border border-cream-border"></div>
                            <p class="font-semibold text-text-muted">Pesanan Diterima</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-[#25D366]/10 border border-[#25D366]/20 rounded-2xl p-6 flex flex-col sm:flex-row items-center justify-between gap-4 mb-8">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 rounded-full bg-[#25D366] text-white flex items-center justify-center text-xl shrink-0"><i class="fab fa-whatsapp"></i></div>
                <p class="text-sm font-medium text-brown-dark">Ada pertanyaan tentang pesananmu? Tim kami siap membantu via WhatsApp.</p>
            </div>
            <a href="https://wa.me/6282283203385" target="_blank" class="inline-flex items-center justify-center px-6 py-2.5 bg-[#25D366] hover:bg-[#20ba59] text-white font-bold text-sm rounded-full transition-all shrink-0">Chat WhatsApp</a>
        </div>

        <div class="flex flex-wrap gap-4 items-center justify-center mt-6">
            <a href="/orders" class="btn-primary">Lihat Riwayat Pesanan</a>
            <a href="{{ route('orders.invoice', $order) }}" target="_blank" class="btn-secondary">Unduh Bukti Pesanan</a>
            <a href="/" class="btn-ghost">Kembali ke Beranda</a>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="{{ asset('js/app.js') }}" defer></script>
@endpush
