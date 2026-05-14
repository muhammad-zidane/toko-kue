<!DOCTYPE html>
<html lang="id">
<head>
    <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}" />
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Jagoan Kue - Detail Pesanan {{ $order->order_code }}</title>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700;800&family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        :root { --pink: #F0507A; --brown-dark: #2C1810; --cream: #FFF8EE; --cream-dark: #F5EDD8; --white: #FFFFFF; --gray: #6B7280; --text-dark: #1A1A1A; --green: #22C55E; }
        body { font-family: 'Plus Jakarta Sans', sans-serif; color: var(--text-dark); background: var(--cream); }
        a { text-decoration: none; }
        .navbar { background-color: var(--brown-dark); padding: 16px 24px; position: sticky; top: 0; z-index: 100; }
        .navbar-inner { max-width: 1100px; margin: 0 auto; display: flex; align-items: center; justify-content: space-between; }
        .navbar-logo { font-family: 'Playfair Display', serif; font-size: 24px; font-weight: 800; color: var(--pink); }
        .navbar-links { display: flex; gap: 32px; list-style: none; }
        .navbar-links a { color: white; font-size: 14px; font-weight: 500; opacity: 0.9; }
        .navbar-actions { display: flex; align-items: center; gap: 12px; }
        .btn-cart { background-color: var(--pink); color: white; padding: 8px 18px; border-radius: 8px; font-size: 14px; font-weight: 600; }
        .btn-login { border: 1.5px solid white; color: white; padding: 8px 18px; border-radius: 8px; font-size: 14px; font-weight: 600; }

        .page { max-width: 900px; margin: 0 auto; padding: 32px 24px 60px; }
        .page-title { font-family: 'Playfair Display', serif; font-size: 24px; font-weight: 700; margin-bottom: 8px; }
        .page-subtitle { font-size: 14px; color: var(--gray); margin-bottom: 24px; }

        .detail-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 20px; }
        .card { background: var(--white); border-radius: 16px; border: 1px solid #EDE0D4; padding: 24px; margin-bottom: 20px; }
        .card-label { font-size: 13px; font-weight: 700; color: var(--pink); margin-bottom: 16px; letter-spacing: 0.5px; }

        .order-item { display: flex; align-items: center; gap: 12px; padding: 12px; background: var(--cream); border-radius: 10px; margin-bottom: 12px; }
        .order-item img { width: 52px; height: 52px; object-fit: cover; border-radius: 8px; flex-shrink: 0; }
        .order-item-info { flex: 1; }
        .order-item-info p { font-size: 14px; font-weight: 700; }
        .order-item-info small { font-size: 12px; color: var(--gray); }
        .item-note { margin-top: 6px; font-size: 12px; color: var(--brown-dark); line-height: 1.5; background: #FFF4E6; border-radius: 6px; padding: 6px 8px; }
        .order-item-price { font-size: 14px; font-weight: 600; }

        .info-row { display: flex; justify-content: space-between; align-items: flex-start; padding: 10px 0; border-bottom: 1px solid #F0E8E0; font-size: 13px; }
        .info-row:last-child { border-bottom: none; }
        .info-row span:first-child { color: var(--gray); }
        .info-row span:last-child { font-weight: 600; text-align: right; max-width: 60%; }

        .badge { display: inline-block; padding: 4px 12px; border-radius: 20px; font-size: 12px; font-weight: 600; }
        .badge-pending { background: #FEF3C7; color: #D97706; }
        .badge-processing { background: #DBEAFE; color: #2563EB; }
        .badge-completed { background: #DCFCE7; color: #16A34A; }
        .badge-cancelled { background: #FEE2E2; color: #DC2626; }
        .badge-unpaid { background: #FEF3C7; color: #D97706; }
        .badge-paid { background: #DCFCE7; color: #16A34A; }

        .price-total { display: flex; justify-content: space-between; align-items: center; padding: 12px 16px; background: var(--cream); border-radius: 10px; margin-top: 12px; }
        .price-total span:first-child { font-size: 14px; font-weight: 600; }
        .price-total span:last-child { font-size: 18px; font-weight: 800; }
        .review-box { margin-top: 14px; border: 1px solid #EDE0D4; border-radius: 10px; padding: 12px; }
        .review-stars { color: #F59E0B; font-size: 18px; margin-bottom: 6px; }
        .review-comment { font-size: 13px; line-height: 1.6; margin-bottom: 10px; }
        .review-images { display: flex; gap: 8px; flex-wrap: wrap; }
        .review-images img { width: 70px; height: 70px; object-fit: cover; border-radius: 8px; border: 1px solid #EDE0D4; }

        .btn-back-page { display: inline-block; background: var(--brown-dark); color: white; padding: 12px 24px; border-radius: 10px; font-size: 14px; font-weight: 700; }

        @media (max-width: 768px) { .navbar-links { display: none; } .detail-grid { grid-template-columns: 1fr; } .footer-inner { grid-template-columns: 1fr 1fr; } }
        .footer { background-color: var(--brown-dark); color: white; padding: 56px 24px; }
        .footer-inner { max-width: 1100px; margin: 0 auto; display: grid; grid-template-columns: 2fr 1fr 1fr 1.5fr; gap: 40px; }
        .footer-logo { font-family: 'Playfair Display', serif; font-size: 22px; font-weight: 800; color: var(--pink); margin-bottom: 8px; }
        .footer-desc { font-size: 13px; opacity: 0.6; margin-bottom: 20px; line-height: 1.6; }
        .footer-socials { display: flex; gap: 16px; font-size: 18px; }
        .footer-socials a { opacity: 0.6; transition: opacity 0.2s; }
        .footer-socials a:hover { opacity: 1; }
        .footer-heading { font-size: 14px; font-weight: 700; margin-bottom: 16px; }
        .footer-links { list-style: none; display: flex; flex-direction: column; gap: 10px; }
        .footer-links a { color: white; font-size: 13px; opacity: 0.6; transition: opacity 0.2s; }
        .footer-links a:hover { opacity: 1; }
        .footer-contact { list-style: none; display: flex; flex-direction: column; gap: 10px; }
        .footer-contact li { font-size: 13px; opacity: 0.6; line-height: 1.5; }
    </style>
    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @endif
</head>
<body>
@include('partials.navbar')

<div class="page">
    <h1 class="page-title">Detail Pesanan</h1>
    <p class="page-subtitle">Kode: {{ $order->order_code }} · {{ $order->created_at->format('d M Y, H:i') }}</p>

    <div class="detail-grid">
        <div class="card">
            <p class="card-label">PRODUK DIPESAN</p>
            @foreach($order->orderItems as $item)
            <div class="order-item">
                <img src="{{ $item->product && $item->product->image ? asset('storage/' . $item->product->image) : 'https://images.unsplash.com/photo-1563729784474-d77dbb933a9e?w=200&q=80' }}" alt="{{ $item->product->name ?? 'Produk' }}">
                <div class="order-item-info">
                    <p>{{ $item->product->name ?? 'Produk dihapus' }}</p>
                    <small>{{ $item->quantity }}x · Rp {{ number_format($item->price, 0, ',', '.') }}</small>
                    @if(!empty($item->note))
                        <p class="item-note">Catatan: {{ $item->note }}</p>
                    @endif
                </div>
                <span class="order-item-price">Rp {{ number_format($item->price * $item->quantity, 0, ',', '.') }}</span>
            </div>
            @endforeach

            <div class="price-total">
                <span>Total</span>
                <span>Rp {{ number_format($order->total_price, 0, ',', '.') }}</span>
            </div>

            @php
                $reviewByProduct = $order->productReviews->keyBy('product_id');
            @endphp

            @foreach($order->orderItems as $item)
                @php
                    $review = $item->product ? ($reviewByProduct[$item->product->id] ?? null) : null;
                @endphp
                @if($review)
                    <div class="review-box">
                        <p style="font-size:13px;font-weight:700;margin-bottom:6px;">Ulasan: {{ $item->product->name }}</p>
                        <div class="review-stars">{{ str_repeat('★', (int) $review->rating) }}{{ str_repeat('☆', 5 - (int) $review->rating) }}</div>
                        <p class="review-comment">{{ $review->comment }}</p>
                        @if($review->images->isNotEmpty())
                            <div class="review-images">
                                @foreach($review->images as $image)
                                    <img src="{{ asset('storage/' . $image->path) }}" alt="Gambar ulasan">
                                @endforeach
                            </div>
                        @endif
                    </div>
                @endif
            @endforeach
        </div>

        <div>
            <div class="card">
                <p class="card-label">INFO PESANAN</p>
                <div class="info-row"><span>Status</span><span><span class="badge badge-{{ $order->status }}">{{ ucfirst($order->status) }}</span></span></div>
                <div class="info-row"><span>Pembayaran</span><span><span class="badge badge-{{ $order->payment->status ?? 'unpaid' }}">{{ ucfirst($order->payment->status ?? 'unpaid') }}</span></span></div>
                <div class="info-row"><span>Metode</span><span>{{ ['transfer_bank'=>'Transfer Bank','ewallet'=>'E-Wallet','qris'=>'QRIS','cod'=>'COD'][$order->payment->payment_method ?? ''] ?? ucfirst($order->payment->payment_method ?? '-') }}</span></div>
                <div class="info-row"><span>Alamat</span><span>{{ $order->shipping_address }}</span></div>
                @if($order->notes)
                <div class="info-row"><span>Catatan</span><span>{{ $order->notes }}</span></div>
                @endif
            </div>

            @if($order->status === 'pending' && $order->payment && $order->payment->status === 'unpaid')
            <a href="{{ route('orders.payment', $order) }}" class="btn-back-page" style="width:100%;text-align:center;display:block;margin-bottom:20px;background:var(--pink);">Bayar Sekarang</a>
            @endif
        </div>
    </div>

    <a href="{{ route('orders.index') }}" class="btn-back-page">← Kembali ke Daftar Pesanan</a>
</div>
@include('partials.footer')
</body>
</html>
