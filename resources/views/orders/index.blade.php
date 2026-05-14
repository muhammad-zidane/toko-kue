<!DOCTYPE html>
<html lang="id">
<head>
    <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}" />
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Jagoan Kue - Riwayat Pesanan</title>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700;800&family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        :root { --pink: #F0507A; --brown-dark: #2C1810; --cream: #FFF8EE; --cream-dark: #F5EDD8; --white: #FFFFFF; --gray: #6B7280; --text-dark: #1A1A1A; }
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

        .page { max-width: 1100px; margin: 0 auto; padding: 32px 24px 60px; }
        .page-title { font-family: 'Playfair Display', serif; font-size: 28px; font-weight: 800; margin-bottom: 8px; }
        .page-subtitle { font-size: 14px; color: var(--gray); margin-bottom: 22px; line-height: 1.6; }
        .top-row { display: flex; align-items: flex-end; justify-content: space-between; gap: 14px; flex-wrap: wrap; margin-bottom: 18px; }
        .btn-primary { background: var(--pink); color: white; padding: 10px 16px; border-radius: 10px; font-size: 13px; font-weight: 700; display: inline-block; }

        .alert-success { background: #ECFDF5; border: 1px solid #A7F3D0; color: #065F46; padding: 12px 14px; border-radius: 12px; font-size: 13px; margin: 14px 0 0; }
        .alert-error { background: #FFF1F2; border: 1px solid #FECDD3; color: #9F1239; padding: 12px 14px; border-radius: 12px; font-size: 13px; margin: 14px 0 0; }

        .grid { display: grid; grid-template-columns: 1fr 1fr; gap: 18px; margin-top: 20px; }
        .card { background: var(--white); border-radius: 16px; border: 1px solid #EDE0D4; padding: 20px; }
        .card-top { display:flex; align-items:flex-start; justify-content:space-between; gap:12px; }
        .card-left { display:flex; align-items:flex-start; gap:12px; min-width: 0; }
        .thumb { width: 56px; height: 56px; border-radius: 12px; background: var(--cream-dark); overflow: hidden; flex-shrink: 0; border: 1px solid #EDE0D4; }
        .thumb img { width: 100%; height: 100%; object-fit: cover; display: block; }
        .left-info { min-width: 0; }
        .meta { font-size: 12px; color: var(--gray); font-weight: 700; letter-spacing: 0.6px; text-transform: uppercase; }
        .code { margin-top: 6px; font-size: 16px; font-weight: 800; }
        .total { margin-top: 10px; font-size: 13px; color: var(--gray); }
        .total strong { color: var(--brown-dark); }
        .badges { width: 120px; display: flex; flex-direction: column; gap: 8px; align-items: center; text-align: center; flex-shrink: 0; }
        .badge { display: inline-block; padding: 5px 12px; border-radius: 999px; font-size: 12px; font-weight: 700; background: #F3F4F6; color: #111827; }
        .badge-warn { background: #FEF3C7; color: #92400E; }
        .badge-success { background: #DCFCE7; color: #166534; }
        .actions { margin-top: 14px; display: flex; flex-wrap: wrap; gap: 10px; }
        .btn-outline { border: 1.5px solid #D1C0B8; color: var(--brown-dark); padding: 10px 14px; border-radius: 10px; font-size: 13px; font-weight: 700; display: inline-block; }

        .empty { grid-column: 1 / -1; text-align: center; padding: 28px; }

        @media (max-width: 860px) { .grid { grid-template-columns: 1fr; } .navbar-links { display: none; } .footer-inner { grid-template-columns: 1fr 1fr; } }
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
    <div class="top-row">
        <div>
            <h1 class="page-title">Riwayat Pesanan</h1>
            <p class="page-subtitle">Lihat status pesanan dan lanjutkan pembayaran bila diperlukan.</p>
        </div>
        <a href="/products" class="btn-primary">+ Belanja Lagi</a>
    </div>

    @if (session('success'))
        <div class="alert-success">{{ session('success') }}</div>
    @endif

    @if ($errors->any())
        <div class="alert-error">{{ $errors->first() }}</div>
    @endif

    <div class="grid">
        @forelse ($orders as $order)
            @php
                $paymentStatus = $order->payment->status ?? 'unpaid';
                $status = $order->status ?? 'pending';
                $firstItem = $order->orderItems->first();
                $thumbPath = $firstItem?->product?->image ? asset('storage/' . $firstItem->product->image) : 'https://images.unsplash.com/photo-1563729784474-d77dbb933a9e?w=200&q=80';
            @endphp

            <div class="card">
                <div class="card-top">
                    <div class="card-left">
                        <div class="thumb">
                            <img src="{{ $thumbPath }}" alt="Produk">
                        </div>
                        <div class="left-info">
                            <p class="meta">{{ $order->created_at?->format('d M Y, H:i') }} WIB</p>
                            <p class="code">{{ $order->order_code }}</p>
                            <p class="total">Total: <strong>Rp {{ number_format((int) $order->total_price, 0, ',', '.') }}</strong></p>
                        </div>
                    </div>
                    <div class="badges">
                        <span class="badge">{{ ucfirst($status) }}</span>
                        <span class="badge {{ $paymentStatus === 'paid' ? 'badge-success' : 'badge-warn' }}">{{ ucfirst($paymentStatus) }}</span>
                    </div>
                </div>

                <div class="actions">
                    <a class="btn-outline" href="{{ route('orders.show', $order) }}">Detail</a>
                    <a class="btn-primary" href="{{ route('pesanan.status', $order) }}"><i class="fa-solid fa-location-dot" style="margin-right:5px;"></i>Lacak</a>

                    @if ($status === 'completed' && $paymentStatus === 'paid')
                        <a class="btn-outline" href="{{ route('orders.reviews.index', $order) }}">Ulasan</a>
                    @endif

                    @if ($status === 'pending' && $paymentStatus === 'unpaid')
                        <a class="btn-primary" href="{{ route('orders.payment', $order) }}">Bayar Sekarang</a>
                    @endif
                </div>
            </div>
        @empty
            <div class="card empty">
                <p style="font-size:18px; font-weight:800;">Belum ada pesanan</p>
                <p style="font-size:13px; color:var(--gray); margin-top:8px; line-height:1.6;">Yuk mulai belanja kue favoritmu.</p>
                <a href="/products" class="btn-primary" style="margin-top:14px;">Lihat Katalog</a>
            </div>
        @endforelse
    </div>

    <div style="margin-top:18px;">
        {{ $orders->links() }}
    </div>
</div>
@include('partials.footer')
</body>
</html>
