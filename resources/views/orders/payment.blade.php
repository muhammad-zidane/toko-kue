<!DOCTYPE html>
<html lang="id">
<head>
    <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}" />
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Jagoan Kue - Konfirmasi Pembayaran</title>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700;800&family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
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
        .breadcrumb { max-width: 1100px; margin: 0 auto; padding: 20px 24px 0; font-size: 13px; color: var(--gray); }
        .breadcrumb a { color: var(--gray); } .breadcrumb span { color: var(--text-dark); font-weight: 600; }
        .stepper-wrap { max-width: 1100px; margin: 0 auto; padding: 28px 24px; }
        .stepper { display: flex; align-items: center; justify-content: center; }
        .step { display: flex; flex-direction: column; align-items: center; gap: 8px; }
        .step-circle { width: 52px; height: 52px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 18px; font-weight: 700; border: 2px solid var(--pink); color: var(--pink); background: var(--white); }
        .step-circle.done { background: var(--pink); color: white; }
        .step-circle.active { background: var(--pink); color: white; }
        .step-label { font-size: 13px; font-weight: 500; }
        .step-line { flex: 1; height: 2px; background: #E5C5CF; margin-bottom: 24px; max-width: 120px; }
        .step-line.done { background: var(--pink); }
        .main { max-width: 1100px; margin: 0 auto; padding: 0 24px 60px; display: grid; grid-template-columns: 1fr 380px; gap: 24px; align-items: start; }
        .card { background: var(--white); border-radius: 16px; border: 1px solid #EDE0D4; padding: 24px; margin-bottom: 16px; }
        .timer-label { font-size: 14px; font-weight: 700; color: var(--pink); margin-bottom: 16px; }
        .timer-box { background: var(--cream-dark); border-radius: 12px; padding: 16px 20px; display: flex; align-items: center; justify-content: space-between; margin-bottom: 12px; }
        .timer-text { font-size: 13px; }
        .timer-digits { display: flex; align-items: center; gap: 4px; }
        .timer-unit { text-align: center; }
        .timer-num { font-size: 28px; font-weight: 800; color: var(--pink); font-variant-numeric: tabular-nums; }
        .timer-sep { font-size: 24px; font-weight: 800; color: var(--pink); margin-bottom: 12px; }
        .timer-sub { font-size: 10px; color: var(--gray); }
        .timer-warning { font-size: 12px; color: var(--pink); margin-bottom: 12px; }
        .alert-box { background: #FFF0F3; border: 1px solid #FECDD3; border-radius: 10px; padding: 12px 16px; font-size: 13px; color: #BE123C; line-height: 1.6; }
        .metode-label { font-size: 15px; font-weight: 700; margin-bottom: 12px; }
        .status-badge { display: inline-flex; align-items: center; gap: 6px; background: var(--cream-dark); border-radius: 20px; padding: 4px 12px; font-size: 12px; font-weight: 600; color: var(--brown-dark); margin-bottom: 16px; }
        .status-dot { width: 8px; height: 8px; border-radius: 50%; background: #F59E0B; }
        .bank-header { display: flex; align-items: center; gap: 12px; padding: 14px; background: var(--cream-dark); border-radius: 10px; margin-bottom: 16px; }
        .bank-logo { width: 42px; height: 42px; border-radius: 8px; display: flex; align-items: center; justify-content: center; color: white; font-size: 12px; font-weight: 800; }
        .bank-name { font-size: 14px; font-weight: 700; }
        .bank-desc { font-size: 12px; color: var(--gray); }
        .bank-row { display: flex; justify-content: space-between; align-items: center; padding: 12px 0; border-bottom: 1px solid #F0E8E0; }
        .bank-row:last-child { border-bottom: none; }
        .bank-row-label { font-size: 13px; color: var(--gray); }
        .bank-row-value { font-size: 13px; font-weight: 600; display: flex; align-items: center; gap: 8px; }
        .btn-salin { background: none; border: 1.5px solid #D1C0B8; border-radius: 6px; padding: 3px 10px; font-size: 12px; font-weight: 600; cursor: pointer; font-family: 'Plus Jakarta Sans', sans-serif; }
        .jumlah-box { background: var(--pink); border-radius: 10px; padding: 14px 16px; display: flex; align-items: center; justify-content: space-between; margin: 12px 0; }
        .jumlah-left p { font-size: 13px; font-weight: 700; color: white; }
        .jumlah-left small { font-size: 11px; color: rgba(255,255,255,0.75); }
        .jumlah-right { display: flex; align-items: center; gap: 8px; }
        .jumlah-amount { font-size: 18px; font-weight: 800; color: white; }
        .btn-salin-white { background: white; border: none; border-radius: 6px; padding: 4px 10px; font-size: 12px; font-weight: 700; cursor: pointer; color: var(--pink); font-family: 'Plus Jakarta Sans', sans-serif; }
        .kode-unik-note { font-size: 12px; color: var(--gray); line-height: 1.6; margin-top: 4px; }
        .cara-label { font-size: 13px; font-weight: 700; color: var(--pink); margin-bottom: 12px; }
        .cara-list { list-style: none; display: flex; flex-direction: column; gap: 10px; }
        .cara-item { display: flex; align-items: flex-start; gap: 10px; font-size: 13px; line-height: 1.6; }
        .cara-num { width: 22px; height: 22px; border-radius: 50%; border: 1.5px solid var(--pink); color: var(--pink); font-size: 11px; font-weight: 700; display: flex; align-items: center; justify-content: center; flex-shrink: 0; margin-top: 1px; }
        .upload-label { font-size: 13px; font-weight: 700; color: var(--pink); margin-bottom: 12px; }
        .upload-zone { border: 2px dashed #D1C0B8; border-radius: 12px; padding: 32px; text-align: center; cursor: pointer; margin-bottom: 12px; }
        .upload-zone:hover { border-color: var(--pink); background: #FFF0F3; }
        .upload-icon { font-size: 32px; margin-bottom: 8px; }
        .upload-text { font-size: 13px; margin-bottom: 4px; }
        .upload-sub { font-size: 12px; color: var(--gray); margin-bottom: 12px; }
        .btn-pilih { background: var(--pink); color: white; border: none; border-radius: 8px; padding: 8px 20px; font-size: 13px; font-weight: 600; cursor: pointer; font-family: 'Plus Jakarta Sans', sans-serif; }
        .upload-format { font-size: 11px; color: var(--gray); }
        .upload-info { background: #ECFDF5; border: 1px solid #A7F3D0; border-radius: 10px; padding: 12px 16px; font-size: 12px; color: #065F46; line-height: 1.6; }
        .summary-card { background: var(--white); border-radius: 16px; border: 1px solid #EDE0D4; padding: 24px; position: sticky; top: 90px; }
        .summary-title { font-size: 18px; font-weight: 700; margin-bottom: 20px; }
        .summary-item { display: flex; align-items: center; gap: 12px; padding-bottom: 16px; border-bottom: 1px solid #F0E8E0; margin-bottom: 16px; }
        .summary-item img { width: 52px; height: 52px; object-fit: cover; border-radius: 8px; }
        .summary-item-info { flex: 1; }
        .summary-item-info p { font-size: 13px; font-weight: 600; }
        .summary-item-info small { font-size: 12px; color: var(--gray); }
        .summary-item-price { font-size: 13px; font-weight: 600; }
        .summary-row { display: flex; justify-content: space-between; font-size: 13px; margin-bottom: 8px; }
        .summary-row span:first-child { color: var(--gray); }
        .summary-row span:last-child { font-weight: 600; }
        .summary-total { display: flex; justify-content: space-between; font-size: 15px; font-weight: 700; padding-top: 12px; border-top: 1.5px solid #EDE0D4; margin: 12px 0 20px; }
        .btn-upload-bukti { width: 100%; background: var(--brown-dark); color: white; border: none; border-radius: 10px; padding: 14px; font-size: 14px; font-weight: 700; cursor: pointer; font-family: 'Plus Jakarta Sans', sans-serif; margin-bottom: 10px; }
        .bantuan { text-align: center; font-size: 12px; color: var(--gray); margin-bottom: 16px; }
        .bantuan a { color: var(--green); font-weight: 600; }
        .aman-box { background: var(--cream); border-radius: 10px; padding: 14px; }
        .aman-title { font-size: 12px; font-weight: 700; margin-bottom: 8px; }
        .aman-item { display: flex; align-items: center; gap: 8px; font-size: 12px; margin-bottom: 6px; }
        .aman-check { color: var(--green); font-size: 14px; }
        .footer { background-color: var(--brown-dark); color: white; padding: 56px 24px; }
        .footer-inner { max-width: 1100px; margin: 0 auto; display: grid; grid-template-columns: 2fr 1fr 1fr 1.5fr; gap: 40px; }
        .footer-logo { font-family: 'Playfair Display', serif; font-size: 22px; font-weight: 800; color: var(--pink); margin-bottom: 8px; }
        .footer-desc { font-size: 13px; opacity: 0.6; margin-bottom: 20px; line-height: 1.6; }
        .footer-heading { font-size: 14px; font-weight: 700; margin-bottom: 16px; }
        .footer-links { list-style: none; display: flex; flex-direction: column; gap: 10px; }
        .footer-links a { color: white; font-size: 13px; opacity: 0.6; }
        .footer-contact { list-style: none; display: flex; flex-direction: column; gap: 10px; }
        .footer-contact li { font-size: 13px; opacity: 0.6; line-height: 1.5; }
        @media (max-width: 768px) { .navbar-links { display: none; } .main { grid-template-columns: 1fr; } .footer-inner { grid-template-columns: 1fr 1fr; } .timer-box { flex-direction: column; gap: 12px; } }
    </style>
    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @endif
</head>
<body>
@php
    $paymentMethod = $order->payment->payment_method ?? 'transfer';
    $totalAmount = $order->total_price;
    $uniqueCode = rand(10, 99);
    $totalTransfer = $totalAmount + $uniqueCode;
    $deadline = \Carbon\Carbon::parse($order->created_at)->addHours(2);
    $remainingSeconds = max(0, now()->diffInSeconds($deadline, false));
@endphp

<nav class="navbar"><div class="navbar-inner"><a href="/" class="navbar-logo">Jagoan Kue</a><ul class="navbar-links"><li><a href="/">Beranda</a></li><li><a href="/products">Katalog</a></li><li><a href="/orders">Pemesanan</a></li></ul><div class="navbar-actions"><a href="/cart" class="btn-cart">🛒 Keranjang</a>@auth<a href="/profile" class="btn-login">{{ auth()->user()->name }}</a>@else<a href="/login" class="btn-login">Login</a>@endauth</div></div></nav>

<div class="breadcrumb"><a href="/">Beranda</a> / <a href="/products">Katalog</a> / <span>Konfirmasi Pembayaran</span></div>

<div class="stepper-wrap"><div class="stepper">
    <div class="step"><div class="step-circle done">✓</div><span class="step-label">Pilih Kue</span></div>
    <div class="step-line done"></div>
    <div class="step"><div class="step-circle done">✓</div><span class="step-label">Detail Pesanan</span></div>
    <div class="step-line done"></div>
    <div class="step"><div class="step-circle active">3</div><span class="step-label">Pembayaran</span></div>
    <div class="step-line"></div>
    <div class="step"><div class="step-circle">4</div><span class="step-label">Konfirmasi</span></div>
</div></div>

<form id="upload-form" action="{{ route('orders.uploadProof', $order) }}" method="POST" enctype="multipart/form-data">
@csrf
<div class="main">
    <div>
        {{-- TIMER --}}
        <div class="card">
            <p class="timer-label">Batas Waktu Pembayaran</p>
            <div class="timer-box">
                <p class="timer-text">Selesaikan pembayaran sebelum:</p>
                <div class="timer-digits">
                    <div class="timer-unit"><div class="timer-num" id="timer-jam">00</div><div class="timer-sub">Jam</div></div>
                    <div class="timer-sep">:</div>
                    <div class="timer-unit"><div class="timer-num" id="timer-menit">00</div><div class="timer-sub">Menit</div></div>
                    <div class="timer-sep">:</div>
                    <div class="timer-unit"><div class="timer-num" id="timer-detik">00</div><div class="timer-sub">Detik</div></div>
                </div>
            </div>
            <p class="timer-warning">Pesanan akan otomatis dibatalkan jika tidak dibayar sebelum <strong>{{ $deadline->format('d M Y, H.i') }} WIB</strong></p>
            <div class="alert-box">Segera lakukan pembayaran agar pesanan kue kamu tidak hangus dan stok tetap terjamin.</div>
        </div>

        {{-- METODE PEMBAYARAN --}}
        <div class="card">
            <p class="metode-label">Metode Pembayaran</p>
            <div class="status-badge"><div class="status-dot"></div> Menunggu Pembayaran</div>

            @if(in_array($paymentMethod, ['bca', 'bni']))
            {{-- BANK TRANSFER UI --}}
            <div class="bank-header">
                @if($paymentMethod === 'bca')
                <div class="bank-logo" style="background:#006CB0;">BCA</div>
                <div><p class="bank-name">Bank BCA</p><p class="bank-desc">Transfer Manual</p></div>
                @else
                <div class="bank-logo" style="background:#F26522;">BNI</div>
                <div><p class="bank-name">Bank BNI</p><p class="bank-desc">Transfer Manual</p></div>
                @endif
            </div>

            <div class="bank-row"><span class="bank-row-label">Nama Rekening</span><span class="bank-row-value">Jagoan Kue Official</span></div>
            <div class="bank-row"><span class="bank-row-label">Nomor Rekening</span><span class="bank-row-value">1234 5678 9012 <button type="button" class="btn-salin" onclick="salin('123456789012')">Salin</button></span></div>

            <div class="jumlah-box">
                <div class="jumlah-left"><p>Jumlah Transfer Tepat</p><small>Transfer sesuai nominal untuk verifikasi</small></div>
                <div class="jumlah-right">
                    <span class="jumlah-amount">Rp {{ number_format($totalTransfer, 0, ',', '.') }}</span>
                    <button type="button" class="btn-salin-white" onclick="salin('{{ $totalTransfer }}')">Salin</button>
                </div>
            </div>
            <p class="kode-unik-note">Nominal transfer berbeda {{ $uniqueCode }} rupiah dari total pesanan — ini adalah kode unik untuk verifikasi.</p>

            @elseif($paymentMethod === 'gopay')
            {{-- GOPAY UI --}}
            <div class="bank-header">
                <div class="bank-logo" style="background:#00B14F;">GP</div>
                <div><p class="bank-name">GoPay</p><p class="bank-desc">E-Wallet</p></div>
            </div>

            <div class="jumlah-box">
                <div class="jumlah-left"><p>Jumlah Pembayaran</p><small>Bayar via GoPay</small></div>
                <div class="jumlah-right">
                    <span class="jumlah-amount">Rp {{ number_format($totalAmount, 0, ',', '.') }}</span>
                </div>
            </div>
            @endif
        </div>

        @if(in_array($paymentMethod, ['bca', 'bni']))
        {{-- CARA TRANSFER --}}
        <div class="card">
            <p class="cara-label">CARA MELAKUKAN TRANSFER</p>
            <ul class="cara-list">
                <li class="cara-item"><div class="cara-num">1</div><div>Buka aplikasi mobile banking atau m-banking kamu</div></li>
                <li class="cara-item"><div class="cara-num">2</div><div>Pilih menu Transfer</div></li>
                <li class="cara-item"><div class="cara-num">3</div><div>Masukkan nomor rekening 1234 5678 9012 a.n. Jagoan Kue Official</div></li>
                <li class="cara-item"><div class="cara-num">4</div><div>Masukkan nominal transfer Rp {{ number_format($totalTransfer, 0, ',', '.') }} (termasuk kode unik)</div></li>
                <li class="cara-item"><div class="cara-num">5</div><div>Selesaikan transfer, lalu upload bukti pembayaran di bawah</div></li>
            </ul>
        </div>
        @elseif($paymentMethod === 'gopay')
        <div class="card">
            <p class="cara-label">CARA PEMBAYARAN GOPAY</p>
            <ul class="cara-list">
                <li class="cara-item"><div class="cara-num">1</div><div>Buka aplikasi Gojek atau GoPay di HP kamu</div></li>
                <li class="cara-item"><div class="cara-num">2</div><div>Transfer ke nomor GoPay: <strong>0822-8320-3385</strong> a.n. Jagoan Kue</div></li>
                <li class="cara-item"><div class="cara-num">3</div><div>Masukkan nominal Rp {{ number_format($totalAmount, 0, ',', '.') }}</div></li>
                <li class="cara-item"><div class="cara-num">4</div><div>Selesaikan pembayaran, lalu screenshot dan upload bukti di bawah</div></li>
            </ul>
        </div>
        @endif

        {{-- UPLOAD BUKTI --}}
        <div class="card">
            <p class="upload-label">UPLOAD BUKTI PEMBAYARAN</p>

            @if ($errors->has('proof_image'))
                <div class="alert-box" style="margin-bottom:12px;">
                    {{ $errors->first('proof_image') }}
                </div>
            @endif

            <input
                type="file"
                id="file-input"
                name="proof_image"
                style="display:none"
                accept=".jpg,.jpeg,.png,.webp,.heic,.heif"
                onchange="handleFile(this)"
            >

            <div class="upload-zone" onclick="document.getElementById('file-input').click()" ondragover="event.preventDefault()" ondrop="handleDrop(event)">
                <div class="upload-icon" id="upload-icon">📤</div>
                <p class="upload-text" id="upload-text">Seret & letakkan file di sini</p>
                <p class="upload-sub" id="upload-sub">atau klik untuk memilih file</p>
                <button class="btn-pilih" type="button">Pilih File</button>
                <p class="upload-format" id="upload-format" style="margin-top:10px;">Format: JPG, PNG, WEBP, HEIC • Maks. 5MB</p>
            </div>
            <div class="upload-info">Bukti pembayaran akan diverifikasi oleh tim kami dalam 5-10 menit.</div>
        </div>
    </div>

    {{-- RINGKASAN --}}
    <div>
        <div class="summary-card">
            <p class="summary-title">Ringkasan Pesanan</p>

            @foreach($order->orderItems as $item)
            <div class="summary-item">
                <img src="{{ $item->product && $item->product->image ? asset('storage/' . $item->product->image) : 'https://images.unsplash.com/photo-1563729784474-d77dbb933a9e?w=200&q=80' }}" alt="">
                <div class="summary-item-info">
                    <p>{{ $item->product->name ?? 'Produk' }}</p>
                    <small>{{ $item->quantity }}x</small>
                </div>
                <span class="summary-item-price">Rp {{ number_format($item->price * $item->quantity, 0, ',', '.') }}</span>
            </div>
            @endforeach

            <div class="summary-row"><span>Subtotal</span><span>Rp {{ number_format($order->total_price, 0, ',', '.') }}</span></div>
            <div class="summary-row"><span>Ongkir</span><span style="color:#22C55E;font-weight:700;">Gratis</span></div>
            @if(in_array($paymentMethod, ['bca', 'bni']))
            <div class="summary-row"><span>Kode unik</span><span>+ Rp {{ $uniqueCode }}</span></div>
            <div class="summary-total"><span>Total Transfer</span><span>Rp {{ number_format($totalTransfer, 0, ',', '.') }}</span></div>
            @else
            <div class="summary-total"><span>Total Bayar</span><span>Rp {{ number_format($totalAmount, 0, ',', '.') }}</span></div>
            @endif

            <div style="margin-bottom:20px;">
                <p style="font-size:12px;font-weight:700;color:var(--gray);margin-bottom:6px;">Detail Pengiriman</p>
                <p style="font-size:13px;">{{ auth()->user()->name }} - {{ $order->shipping_address }}</p>
            </div>

            @if($order->notes)
            <div style="margin-bottom:20px;">
                <p style="font-size:12px;font-weight:700;color:var(--gray);margin-bottom:6px;">Catatan Pesanan</p>
                <p style="font-size:13px;">{{ $order->notes }}</p>
            </div>
            @endif

            <button type="submit" class="btn-upload-bukti" id="btn-submit" disabled>Menunggu Upload Bukti Bayar</button>
            <p class="bantuan">ⓘ Butuh Bantuan? <a href="https://wa.me/6282283203385" target="_blank">Chat Whatsapp</a></p>

            <div class="aman-box">
                <p class="aman-title">Pesanan Aman Bersama Kami</p>
                <div class="aman-item"><span class="aman-check">✅</span> Verifikasi pembayaran otomatis</div>
                <div class="aman-item"><span class="aman-check">✅</span> Uang kembali jika pesanan gagal</div>
                <div class="aman-item"><span class="aman-check">✅</span> Data transaksi terenkripsi & aman</div>
            </div>
        </div>
    </div>
</div>
</form>

<footer class="footer"><div class="footer-inner"><div><p class="footer-logo">Jagoan Kue</p><p class="footer-desc">Menyediakan kue dengan cinta sejak 2023</p></div><div><p class="footer-heading">Layanan</p><ul class="footer-links"><li><a href="#">Katalog Kue</a></li><li><a href="#">Kue Custom</a></li></ul></div><div><p class="footer-heading">Selengkapnya</p><ul class="footer-links"><li><a href="#">Tentang Kami</a></li><li><a href="#">Blog</a></li></ul></div><div><p class="footer-heading">Kontak</p><ul class="footer-contact"><li>0822-8320-3385</li><li>muhammadzidane253@gmail.com</li><li>Payakumbuh, Sumatera Barat</li></ul></div></div></footer>

<script>
    let totalSeconds = {{ (int)$remainingSeconds }};
    function updateTimer() {
        if (totalSeconds <= 0) { document.getElementById('timer-jam').textContent = '00'; document.getElementById('timer-menit').textContent = '00'; document.getElementById('timer-detik').textContent = '00'; return; }
        totalSeconds--;
        document.getElementById('timer-jam').textContent = String(Math.floor(totalSeconds / 3600)).padStart(2, '0');
        document.getElementById('timer-menit').textContent = String(Math.floor((totalSeconds % 3600) / 60)).padStart(2, '0');
        document.getElementById('timer-detik').textContent = String(totalSeconds % 60).padStart(2, '0');
    }
    updateTimer();
    setInterval(updateTimer, 1000);

    function salin(text) { navigator.clipboard.writeText(text).then(() => alert('Disalin: ' + text)); }

    function handleFile(input) {
        if (input.files && input.files[0]) {
            const file = input.files[0];
            const icon = document.getElementById('upload-icon');
            const text = document.getElementById('upload-text');
            const sub = document.getElementById('upload-sub');
            const format = document.getElementById('upload-format');

            if (icon) icon.textContent = '✅';
            if (text) text.textContent = file.name;
            if (sub) sub.textContent = 'File siap diupload';
            if (format) format.textContent = '';

            const btn = document.getElementById('btn-submit');
            btn.style.background = 'var(--pink)';
            btn.textContent = '✓ Kirim Bukti Pembayaran';
            btn.disabled = false;
        }
    }
    function handleDrop(e) { e.preventDefault(); document.getElementById('file-input').files = e.dataTransfer.files; handleFile(document.getElementById('file-input')); }

    document.getElementById('upload-form').addEventListener('submit', function() {
        const btn = document.getElementById('btn-submit');
        btn.textContent = '⏳ Mengupload...';
        btn.disabled = true;
    });
</script>
</body>
</html>
