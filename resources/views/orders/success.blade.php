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
        .page { max-width: 960px; margin: 0 auto; padding: 40px 24px 60px; }
        .success-header { background: var(--white); border-radius: 20px; padding: 48px 32px; text-align: center; margin-bottom: 24px; border: 1px solid #EDE0D4; }
        .success-icon { width: 72px; height: 72px; border-radius: 50%; border: 3px solid var(--teal); display: flex; align-items: center; justify-content: center; margin: 0 auto 20px; font-size: 28px; color: var(--teal); animation: popIn 0.5s ease; }
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
        .info-row { display: flex; justify-content: space-between; align-items: flex-start; padding: 10px 0; border-bottom: 1px solid #F0E8E0; font-size: 13px; }
        .info-row:last-child { border-bottom: none; }
        .info-row span:first-child { color: var(--gray); flex-shrink: 0; }
        .info-row span:last-child { font-weight: 600; text-align: right; max-width: 60%; }
        .info-row .highlight { color: var(--pink); }
        .badge-status { display: inline-block; padding: 4px 12px; border-radius: 20px; font-size: 12px; font-weight: 600; }
        .badge-pending { background: #FEF3C7; color: #D97706; }
        .badge-paid { background: #DCFCE7; color: #16A34A; }
        .status-list { display: flex; flex-direction: column; }
        .status-item { display: flex; align-items: flex-start; gap: 12px; position: relative; padding-bottom: 20px; }
        .status-item:last-child { padding-bottom: 0; }
        .status-item::before { content: ''; position: absolute; left: 10px; top: 24px; bottom: 0; width: 2px; background: #E5D5C5; }
        .status-item:last-child::before { display: none; }
        .status-item.done::before { background: var(--teal); }
        .status-item.active::before { background: linear-gradient(to bottom, var(--pink), #E5D5C5); }
        .status-dot { width: 22px; height: 22px; border-radius: 50%; border: 2px solid #E5D5C5; background: var(--white); display: flex; align-items: center; justify-content: center; flex-shrink: 0; font-size: 11px; z-index: 1; }
        .status-dot.done { background: var(--teal); border-color: var(--teal); color: white; }
        .status-dot.active { background: var(--pink); border-color: var(--pink); color: white; font-size: 8px; }
        .status-content p { font-size: 13px; font-weight: 600; }
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

            <div class="price-row"><span>Subtotal</span><span>Rp {{ number_format($order->total_price, 0, ',', '.') }}</span></div>
            <div class="price-total"><span>Total</span><span>Rp {{ number_format($order->total_price, 0, ',', '.') }}</span></div>
        </div>

        <div>
            <div class="card" style="margin-bottom: 20px;">
                <p class="card-label">INFO PENGIRIMAN</p>
                <div class="info-row"><span>Penerima</span><span>{{ auth()->user()->name }}</span></div>
                <div class="info-row"><span>Alamat</span><span>{{ $order->shipping_address }}</span></div>
                <div class="info-row"><span>Pembayaran</span><span>{{ ['transfer_bank'=>'Transfer Bank','ewallet'=>'E-Wallet','qris'=>'QRIS','cod'=>'COD'][$order->payment->payment_method ?? ''] ?? ucfirst($order->payment->payment_method ?? '-') }}</span></div>
                <div class="info-row"><span>Status bayar</span><span><span class="badge-status badge-{{ $order->payment->status ?? 'pending' }}">{{ ucfirst($order->payment->status ?? 'unpaid') }}</span></span></div>
                @if($order->notes)
                <div class="info-row"><span>Catatan</span><span>{{ $order->notes }}</span></div>
                @endif
            </div>

            <div class="card">
                <p class="card-label">STATUS PESANAN</p>
                <div class="status-list">
                    <div class="status-item done">
                        <div class="status-dot done">✓</div>
                        <div class="status-content"><p>Pesanan Diterima</p><small>{{ $order->created_at->format('d M Y, H:i') }}</small></div>
                    </div>
                    <div class="status-item active">
                        <div class="status-dot active">●</div>
                        <div class="status-content"><p>Sedang dipersiapkan</p><small>Kue sedang dibuat oleh tim kami</small></div>
                    </div>
                    <div class="status-item">
                        <div class="status-dot"></div>
                        <div class="status-content"><p style="color: var(--gray);">Dalam pengiriman</p></div>
                    </div>
                    <div class="status-item">
                        <div class="status-dot"></div>
                        <div class="status-content"><p style="color: var(--gray);">Pesanan diterima</p></div>
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
