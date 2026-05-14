<!DOCTYPE html>
<html lang="id">
<head>
    <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}" />
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Jagoan Kue - Form Pemesanan</title>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700;800&family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        :root { --pink: #F0507A; --brown-dark: #2C1810; --brown-mid: #5C3D2E; --cream: #FFF8EE; --cream-dark: #F0E6D3; --white: #FFFFFF; --gray: #6B7280; --text-dark: #1A1A1A; }
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
        .breadcrumb { max-width: 1100px; margin: 0 auto; padding: 20px 24px 0; font-size: 14px; color: var(--gray); }
        .stepper-wrap { max-width: 1100px; margin: 0 auto; padding: 28px 24px; }
        .stepper { display: flex; align-items: center; justify-content: center; }
        .step { display: flex; flex-direction: column; align-items: center; gap: 8px; }
        .step-circle { width: 52px; height: 52px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 18px; font-weight: 700; border: 2px solid var(--pink); color: var(--pink); background: var(--white); }
        .step-circle.active { background: var(--pink); color: white; }
        .step-circle.done { background: var(--pink); color: white; }
        .step-label { font-size: 13px; font-weight: 500; }
        .step-line { flex: 1; height: 2px; background: #E5C5CF; margin-bottom: 24px; max-width: 120px; }
        .step-line.done { background: var(--pink); }
        .checkout-layout { max-width: 1100px; margin: 0 auto; padding: 0 24px 60px; display: grid; grid-template-columns: 1fr 380px; gap: 24px; align-items: start; }
        .card { background: var(--white); border-radius: 16px; padding: 24px; margin-bottom: 16px; border: 1px solid #EDE0D4; }
        .card-title { display: flex; align-items: center; gap: 10px; margin-bottom: 20px; }
        .card-num { width: 28px; height: 28px; border-radius: 50%; background: var(--pink); color: white; font-size: 13px; font-weight: 700; display: flex; align-items: center; justify-content: center; flex-shrink: 0; }
        .card-title span { font-size: 15px; font-weight: 700; color: var(--brown-dark); }
        .produk-item { display: flex; align-items: center; gap: 14px; background: var(--cream); border-radius: 10px; padding: 12px; margin-bottom: 12px; }
        .produk-item img { width: 64px; height: 64px; object-fit: cover; border-radius: 8px; }
        .produk-item h4 { font-size: 15px; font-weight: 700; }
        .produk-item p { font-size: 14px; font-weight: 600; color: var(--brown-dark); margin-top: 4px; }
        .produk-item small { font-size: 12px; color: var(--gray); }
        .field-label { font-size: 13px; font-weight: 500; margin-bottom: 6px; display: block; }
        .field-input { width: 100%; background: var(--brown-dark); color: white; border: none; border-radius: 8px; padding: 10px 14px; font-size: 13px; font-family: 'Plus Jakarta Sans', sans-serif; outline: none; margin-bottom: 14px; }
        .field-input::placeholder { color: rgba(255,255,255,0.5); }
        .field-textarea { width: 100%; background: var(--brown-dark); color: white; border: none; border-radius: 8px; padding: 10px 14px; font-size: 13px; font-family: 'Plus Jakarta Sans', sans-serif; outline: none; resize: none; margin-bottom: 14px; }
        .field-textarea::placeholder { color: rgba(255,255,255,0.5); }
        .two-col { display: grid; grid-template-columns: 1fr 1fr; gap: 12px; }
        .payment-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 12px; }
        .payment-option { display: flex; align-items: center; gap: 10px; border: 1.5px solid #D1C0B8; border-radius: 10px; padding: 12px; cursor: pointer; transition: all 0.2s; }
        .payment-option:has(input:checked) { border-color: var(--brown-dark); background: var(--cream-dark); }
        .payment-option input { accent-color: var(--brown-dark); }
        .payment-logo { width: 36px; height: 36px; border-radius: 6px; display: flex; align-items: center; justify-content: center; font-size: 10px; font-weight: 800; color: white; flex-shrink: 0; }
        .payment-info p { font-size: 13px; font-weight: 600; }
        .payment-info small { font-size: 11px; color: var(--gray); }
        .summary-card { background: var(--white); border-radius: 16px; padding: 24px; border: 1px solid #EDE0D4; position: sticky; top: 90px; }
        .summary-title { font-size: 16px; font-weight: 700; margin-bottom: 16px; }
        .summary-item { display: flex; align-items: center; gap: 12px; padding-bottom: 12px; border-bottom: 1px solid #F0E8E0; margin-bottom: 12px; }
        .summary-item img { width: 52px; height: 52px; object-fit: cover; border-radius: 8px; }
        .summary-item-info { flex: 1; }
        .summary-item-info p { font-size: 13px; font-weight: 600; }
        .summary-item-info small { font-size: 12px; color: var(--gray); }
        .summary-item-price { font-size: 13px; font-weight: 600; }
        .summary-row { display: flex; justify-content: space-between; font-size: 13px; margin-bottom: 10px; }
        .summary-row span:first-child { color: var(--gray); }
        .summary-row span:last-child { font-weight: 600; }
        .summary-total { display: flex; justify-content: space-between; font-size: 14px; font-weight: 700; padding-top: 12px; border-top: 1.5px solid #EDE0D4; margin-bottom: 16px; }
        .summary-total span:last-child { color: var(--pink); font-size: 16px; }
        .btn-lanjut { width: 100%; background: var(--brown-dark); color: white; border: none; border-radius: 10px; padding: 14px; font-size: 14px; font-weight: 700; cursor: pointer; font-family: 'Plus Jakarta Sans', sans-serif; }
        .btn-lanjut:hover { opacity: 0.85; }
        .alert-error { background: #FEE2E2; border: 1px solid #FECACA; border-radius: 10px; padding: 12px 16px; font-size: 13px; color: #B91C1C; margin-bottom: 16px; }
        @media (max-width: 768px) { .navbar-links { display: none; } .checkout-layout { grid-template-columns: 1fr; } .footer-inner { grid-template-columns: 1fr 1fr; } }
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
@php
    // Support both single product and multi-product (from cart)
    $items = [];
    if (isset($cartItems) && is_array($cartItems)) {
        $items = $cartItems;
    } elseif (isset($product)) {
        $items = [['product' => $product, 'quantity' => 1]];
    }
    $subtotal = collect($items)->sum(fn($i) => $i['product']->price * $i['quantity']);
@endphp

@include('partials.navbar')

<div class="breadcrumb"><a href="/" style="color:var(--gray);">Beranda</a> / <a href="/products" style="color:var(--gray);">Katalog</a> / <span style="color:var(--text-dark);font-weight:600;">Form Pemesanan</span></div>

<div class="stepper-wrap"><div class="stepper">
    <div class="step"><div class="step-circle done">✓</div><span class="step-label">Pilih Kue</span></div>
    <div class="step-line done"></div>
    <div class="step"><div class="step-circle active">2</div><span class="step-label">Detail Pesanan</span></div>
    <div class="step-line"></div>
    <div class="step"><div class="step-circle">3</div><span class="step-label">Pembayaran</span></div>
    <div class="step-line"></div>
    <div class="step"><div class="step-circle">4</div><span class="step-label">Konfirmasi</span></div>
</div></div>

<form action="/orders" method="POST">
@csrf

@if($errors->any())<div style="max-width:1100px;margin:0 auto;padding:0 24px;"><div class="alert-error">@foreach($errors->all() as $error)<p>{{ $error }}</p>@endforeach</div></div>@endif

<div class="checkout-layout">
    <div>
        {{-- 1. Produk --}}
        <div class="card">
            <div class="card-title"><div class="card-num">1</div><span>Produk yang Dipesan</span></div>
            @foreach($items as $idx => $item)
            <div class="produk-item">
                <img src="{{ $item['product']->image ? asset('storage/' . $item['product']->image) : 'https://images.unsplash.com/photo-1563729784474-d77dbb933a9e?w=200&q=80' }}" alt="">
                <div>
                    <h4>{{ $item['product']->name }}</h4>
                    <p>Rp {{ number_format($item['product']->price, 0, ',', '.') }}</p>
                    <small>Jumlah: {{ $item['quantity'] }}</small>
                </div>
            </div>
            <input type="hidden" name="items[{{ $idx }}][product_id]" value="{{ $item['product']->id }}">
            <input type="hidden" name="items[{{ $idx }}][quantity]" value="{{ $item['quantity'] }}">
            <input type="hidden" name="items[{{ $idx }}][note]" value="{{ $item['note'] ?? '' }}">
            @endforeach
        </div>

        {{-- 2. Data Pemesan --}}
        <div class="card">
            <div class="card-title"><div class="card-num">2</div><span>Data Pemesan</span></div>
            <div class="two-col">
                <div><label class="field-label">Nama Lengkap</label><input type="text" name="name" class="field-input" value="{{ auth()->user()->name ?? '' }}"></div>
                <div><label class="field-label">No. Telepon</label><input type="text" name="phone" class="field-input" placeholder="08123456789"></div>
            </div>
            <label class="field-label">Alamat Lengkap</label>
            <textarea name="shipping_address" class="field-textarea" rows="3" placeholder="Jl. Imam Bonjol ...." required></textarea>
            <label class="field-label">Catatan (opsional)</label>
            <textarea name="notes" class="field-textarea" rows="3" placeholder="Catatan khusus..."></textarea>
        </div>

        {{-- 3. Metode Pembayaran --}}
        <div class="card">
            <div class="card-title"><div class="card-num">3</div><span>Metode Pembayaran</span></div>
            <div class="payment-grid">
                <label class="payment-option"><input type="radio" name="payment_method" value="transfer_bank" checked><div class="payment-logo" style="background:#006CB0;"><i class="fas fa-university"></i></div><div class="payment-info"><p>Transfer Bank</p><small>BCA / BNI / dll</small></div></label>
                <label class="payment-option"><input type="radio" name="payment_method" value="ewallet"><div class="payment-logo" style="background:#00B14F;"><i class="fas fa-wallet"></i></div><div class="payment-info"><p>E-wallet</p><small>GoPay / OVO / dll</small></div></label>
                <label class="payment-option"><input type="radio" name="payment_method" value="qris"><div class="payment-logo" style="background:#7C3AED;"><i class="fas fa-qrcode"></i></div><div class="payment-info"><p>QRIS</p><small>Scan & bayar</small></div></label>
                <label class="payment-option"><input type="radio" name="payment_method" value="cod"><div class="payment-logo" style="background:#6B7280;"><i class="fas fa-motorcycle"></i></div><div class="payment-info"><p>COD</p><small>Bayar di tempat</small></div></label>
            </div>
        </div>
    </div>

    {{-- RINGKASAN --}}
    <div>
        <div class="summary-card">
            <p class="summary-title">Ringkasan Pesanan</p>
            @foreach($items as $item)
            <div class="summary-item">
                <img src="{{ $item['product']->image ? asset('storage/' . $item['product']->image) : 'https://images.unsplash.com/photo-1563729784474-d77dbb933a9e?w=200&q=80' }}" alt="">
                <div class="summary-item-info"><p>{{ $item['product']->name }}</p><small>{{ $item['quantity'] }}x</small></div>
                <span class="summary-item-price">Rp {{ number_format($item['product']->price * $item['quantity'], 0, ',', '.') }}</span>
            </div>
            @endforeach
            <div class="summary-row"><span>Subtotal</span><span>Rp {{ number_format($subtotal, 0, ',', '.') }}</span></div>
            <div class="summary-total"><span>Total</span><span>Rp {{ number_format($subtotal, 0, ',', '.') }}</span></div>
            <button type="submit" class="btn-lanjut">Lanjutkan Ke Pembayaran →</button>
        </div>
    </div>
</div>
</form>
@include('partials.footer')
</body>
</html>
