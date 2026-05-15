<!DOCTYPE html>
<html lang="id">
<head>
    <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}" />
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Jagoan Kue - Pesanan Berhasil</title>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700;800&family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    <style>
        body { background: var(--cream); }
        .page { max-width: 960px; margin: 0 auto; padding: 40px 24px 60px; }
        .success-header { background: var(--white); border-radius: 20px; padding: 48px 32px; text-align: center; margin-bottom: 24px; border: 1px solid #EDE0D4; }
        .success-icon { width: 72px; height: 72px; border-radius: 50%; background: var(--pink); display: flex; align-items: center; justify-content: center; margin: 0 auto 20px; font-size: 28px; color: var(--cream); animation: popIn 0.5s ease; }
        @keyframes popIn { 0% { transform: scale(0); opacity: 0; } 70% { transform: scale(1.1); } 100% { transform: scale(1); opacity: 1; } }
        .success-title { font-family: 'Playfair Display', serif; font-size: 28px; font-weight: 700; margin-bottom: 10px; }
        .success-desc { font-size: 14px; color: var(--gray); line-height: 1.7; max-width: 420px; margin: 0 auto 24px; }
        .order-code-box { display: inline-block; border: 1.5px solid #E5D5C5; border-radius: 10px; padding: 14px 32px; font-size: 14px; }
        .order-code-box span { color: var(--pink); font-weight: 700; }
        .detail-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 20px; }
        .card { background: var(--white); border-radius: 16px; border: 1px solid #EDE0D4; padding: 24px; }
        .card-label { font-size: 13px; font-weight: 700; color: var(--pink); margin-bottom: 16px; letter-spacing: 0.5px; }
        .order-item { display: flex; align-items: center; gap: 12px; padding: 12px; background: var(--cream); border-radius: 10px; margin-bottom: 12px; }
        .order-item img { width: 52px; height: 52px; object-fit: cover; border-radius: 8px; flex-shrink: 0; }
        .order-item-info { flex: 1; }
        .order-item-info p { font-size: 14px; font-weight: 700; }
        .order-item-info small { font-size: 12px; color: var(--gray); }
        .item-note { margin-top: 6px; font-size: 12px; color: var(--brown-dark); line-height: 1.5; background: #FFF4E6; border-radius: 6px; padding: 6px 8px; }
        .order-item-price { font-size: 14px; font-weight: 600; }
        .price-row { display: flex; justify-content: space-between; font-size: 13px; margin-bottom: 8px; }
        .price-row span:first-child { color: var(--gray); }
        .price-row span:last-child { font-weight: 500; }
        .price-total { display: flex; justify-content: space-between; align-items: center; padding: 12px 16px; background: var(--cream); border-radius: 10px; margin-top: 12px; }
        .price-total span:first-child { font-size: 14px; font-weight: 600; }
        .price-total span:last-child { font-size: 18px; font-weight: 800; }
        .info-row { display: flex; justify-content: space-between; align-items: center; padding: 11px 0; border-bottom: 1px solid #F0E8E0; font-size: 13px; gap: 12px; }
        .info-row:last-child { border-bottom: none; }
        .info-row .info-label { color: var(--gray); flex-shrink: 0; }
        .info-row .info-value { font-weight: 600; text-align: right; }
        .info-row .info-value.wrap { max-width: 58%; line-height: 1.5; }
        .badge-status { display: inline-flex; align-items: center; padding: 3px 10px; border-radius: 20px; font-size: 11px; font-weight: 700; white-space: nowrap; line-height: 1.4; }
        .badge-pending { background: #FEF3C7; color: #D97706; }
        .badge-dp      { background: #FFF7ED; color: #C2410C; }
        .badge-paid    { background: #DCFCE7; color: #16A34A; }

        /* Timeline status */
        .status-list { display: flex; flex-direction: column; }
        .status-item { display: flex; align-items: flex-start; gap: 14px; position: relative; }
        .status-item:not(:last-child) { padding-bottom: 24px; }
        .status-track { display: flex; flex-direction: column; align-items: center; flex-shrink: 0; }
        .status-dot { width: 24px; height: 24px; border-radius: 50%; border: 2px solid #D1C0B8; background: var(--white); display: flex; align-items: center; justify-content: center; font-size: 12px; font-weight: 700; flex-shrink: 0; }
        .status-dot.done  { background: #10B981; border-color: #10B981; color: white; }
        .status-dot.active { background: var(--pink); border-color: var(--pink); color: white; }
        .status-line { width: 2px; flex: 1; min-height: 24px; background: #E5D5C5; margin-top: 4px; }
        .status-line.done   { background: #10B981; }
        .status-line.active { background: linear-gradient(to bottom, var(--pink), #E5D5C5); }
        .status-content { padding-top: 2px; }
        .status-content p { font-size: 13px; font-weight: 600; margin-bottom: 2px; }
        .status-content p.muted { color: var(--gray); font-weight: 500; }
        .status-content small { font-size: 12px; color: var(--gray); }
        .wa-banner { background: var(--white); border-radius: 16px; border: 1px solid #EDE0D4; padding: 20px 24px; display: flex; align-items: center; gap: 16px; margin-bottom: 24px; }
        .wa-icon { width: 44px; height: 44px; border-radius: 50%; background: #22C55E; display: flex; align-items: center; justify-content: center; font-size: 22px; flex-shrink: 0; }
        .wa-text { flex: 1; font-size: 13px; color: var(--gray); line-height: 1.6; }
        .btn-wa { background: var(--brown-dark); color: white; padding: 10px 20px; border-radius: 8px; font-size: 13px; font-weight: 700; white-space: nowrap; }
        .action-buttons { display: grid; grid-template-columns: repeat(3, 1fr); gap: 12px; margin-bottom: 40px; }
        .btn-action { padding: 14px; border-radius: 10px; font-size: 14px; font-weight: 600; text-align: center; cursor: pointer; font-family: 'Plus Jakarta Sans', sans-serif; border: none; display: block; }
        .btn-action.pink { background: var(--pink); color: white; }
        .btn-action.outline { background: var(--white); color: var(--text-dark); border: 1.5px solid #D1C0B8; }
        @media (max-width: 768px) {
 .detail-grid { grid-template-columns: 1fr; } .action-buttons { grid-template-columns: 1fr; }
 .wa-banner { flex-direction: column; text-align: center; } }
    </style></head>
<body>
@include('partials.navbar')

<div class="page">
    <div class="success-header">
        <div class="success-icon">✓</div>
        <h1 class="success-title">Pesanan Berhasil Ditempatkan!</h1>
        <p class="success-desc">Terima kasih! Pesananmu sedang kami proses.</p>
        <div class="order-code-box">No. Pesanan: <span>{{ $order->order_code }}</span></div>
    </div>

    <div class="detail-grid">
        <div class="card">
            <p class="card-label">Detail Pesanan</p>
            @foreach($order->orderItems as $item)
            <div class="order-item">
                <img src="{{ $item->product && $item->product->image ? asset('storage/' . $item->product->image) : 'https://images.unsplash.com/photo-1563729784474-d77dbb933a9e?w=200&q=80' }}" alt="">
                <div class="order-item-info">
                    <p>{{ $item->product->name ?? 'Produk' }}</p>
                    <small>{{ $item->quantity }}x</small>
                    @if(!empty($item->note))
                        <p class="item-note">Catatan: {{ $item->note }}</p>
                    @endif
                </div>
                <span class="order-item-price">Rp {{ number_format($item->price * $item->quantity, 0, ',', '.') }}</span>
            </div>
            @endforeach

            @php
                $subtotalItems  = $order->orderItems->sum(fn($i) => $i->price * $i->quantity);
                $isTransferBank = ($order->payment->payment_method ?? '') === 'transfer_bank';
                $uniqueCode     = $isTransferBank ? 1000 : 0;
            @endphp
            <div class="price-row"><span>Subtotal Produk</span><span>Rp {{ number_format($subtotalItems, 0, ',', '.') }}</span></div>
            <div class="price-row"><span>Ongkos Kirim</span><span>{{ $order->shipping_cost > 0 ? 'Rp ' . number_format($order->shipping_cost, 0, ',', '.') : 'Gratis' }}</span></div>
            @if($order->discount_amount > 0)
            <div class="price-row"><span>Diskon Voucher</span><span style="color:#059669;">-Rp {{ number_format($order->discount_amount, 0, ',', '.') }}</span></div>
            @endif
            @if($isTransferBank)
            <div class="price-row"><span>Biaya Layanan (Kode Unik)</span><span>Rp {{ number_format($uniqueCode, 0, ',', '.') }}</span></div>
            @endif
            <div class="price-total"><span>Total Harga</span><span>Rp {{ number_format($order->total_price + $uniqueCode, 0, ',', '.') }}</span></div>
        </div>

        <div>
            <div class="card" style="margin-bottom: 20px;">
                <p class="card-label">INFO PENGIRIMAN</p>
                <div class="info-row">
                    <span class="info-label">Penerima</span>
                    <span class="info-value">{{ auth()->user()->name }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Alamat</span>
                    <span class="info-value wrap">{{ $order->shipping_address }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Pembayaran</span>
                    <span class="info-value">{{ ['transfer_bank'=>'Transfer Bank','ewallet'=>'E-Wallet','qris'=>'QRIS','cod'=>'COD'][$order->payment->payment_method ?? ''] ?? ucfirst($order->payment->payment_method ?? '-') }}</span>
                </div>
                @php
                    $pStatus = $order->payment->status ?? 'unpaid';
                    $pLabel  = match($pStatus) { 'paid' => 'Lunas', 'dp' => 'DP 50%', default => 'Belum Bayar' };
                @endphp
                <div class="info-row">
                    <span class="info-label">Status Bayar</span>
                    <span class="info-value"><span class="badge-status badge-{{ $pStatus }}">{{ $pLabel }}</span></span>
                </div>
                @if($order->notes)
                <div class="info-row">
                    <span class="info-label">Catatan</span>
                    <span class="info-value wrap">{{ $order->notes }}</span>
                </div>
                @endif
            </div>

            <div class="card">
                <p class="card-label">STATUS PESANAN</p>
                <div class="status-list">
                    {{-- Step 1: Pesanan Diterima (done) --}}
                    <div class="status-item">
                        <div class="status-track">
                            <div class="status-dot done">✓</div>
                            <div class="status-line done"></div>
                        </div>
                        <div class="status-content">
                            <p>Pesanan Diterima</p>
                            <small>{{ $order->created_at->format('d M Y, H:i') }}</small>
                        </div>
                    </div>
                    {{-- Step 2: Sedang dipersiapkan (active) --}}
                    <div class="status-item">
                        <div class="status-track">
                            <div class="status-dot active"><i class="fas fa-circle" style="font-size:7px;"></i></div>
                            <div class="status-line active"></div>
                        </div>
                        <div class="status-content">
                            <p>Sedang Dipersiapkan</p>
                            <small>Kue sedang dibuat oleh tim kami</small>
                        </div>
                    </div>
                    {{-- Step 3: Dalam pengiriman (pending) --}}
                    <div class="status-item">
                        <div class="status-track">
                            <div class="status-dot"></div>
                            <div class="status-line"></div>
                        </div>
                        <div class="status-content">
                            <p class="muted">Dalam Pengiriman</p>
                        </div>
                    </div>
                    {{-- Step 4: Pesanan diterima (pending) --}}
                    <div class="status-item">
                        <div class="status-track">
                            <div class="status-dot"></div>
                        </div>
                        <div class="status-content">
                            <p class="muted">Pesanan Diterima</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="wa-banner">
        <div class="wa-icon"><i class="fab fa-whatsapp" style="color:white"></i></div>
        <p class="wa-text">Ada pertanyaan tentang pesananmu? Tim kami siap membantu via WhatsApp.</p>
        <a href="https://wa.me/6282283203385" target="_blank" class="btn-wa">Chat WhatsApp</a>
    </div>

    <div class="action-buttons">
        <a href="/orders" class="btn-action pink">Lihat Riwayat Pesanan</a>
        <button class="btn-action outline" onclick="window.print()">Unduh Bukti Pesanan</button>
        <a href="/" class="btn-action outline">Kembali ke Beranda</a>
    </div>
</div>

@include('partials.footer')
<script src="{{ asset('js/app.js') }}" defer></script>
</body>
</html>
