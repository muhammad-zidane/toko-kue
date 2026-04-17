<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Jagoan Kue - Konfirmasi Pembayaran</title>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700;800&family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        :root {
            --pink:       #F0507A;
            --brown-dark: #2C1810;
            --cream:      #FFF8EE;
            --cream-dark: #F5EDD8;
            --white:      #FFFFFF;
            --gray:       #6B7280;
            --text-dark:  #1A1A1A;
            --green:      #22C55E;
        }
        body { font-family: 'Plus Jakarta Sans', sans-serif; color: var(--text-dark); background: var(--cream); }
        a { text-decoration: none; }

        /* NAVBAR */
        .navbar { background-color: var(--brown-dark); padding: 16px 24px; position: sticky; top: 0; z-index: 100; }
        .navbar-inner { max-width: 1100px; margin: 0 auto; display: flex; align-items: center; justify-content: space-between; }
        .navbar-logo { font-family: 'Playfair Display', serif; font-size: 24px; font-weight: 800; color: var(--pink); }
        .navbar-links { display: flex; gap: 32px; list-style: none; }
        .navbar-links a { color: white; font-size: 14px; font-weight: 500; opacity: 0.9; }
        .navbar-links a:hover { opacity: 1; }
        .navbar-actions { display: flex; align-items: center; gap: 12px; }
        .btn-cart { background-color: var(--pink); color: white; padding: 8px 18px; border-radius: 8px; font-size: 14px; font-weight: 600; }
        .btn-login { border: 1.5px solid white; color: white; padding: 8px 18px; border-radius: 8px; font-size: 14px; font-weight: 600; transition: all 0.2s; }
        .btn-login:hover { background: white; color: var(--brown-dark); }

        /* BREADCRUMB */
        .breadcrumb { max-width: 1100px; margin: 0 auto; padding: 20px 24px 0; font-size: 13px; color: var(--gray); }
        .breadcrumb a { color: var(--gray); }
        .breadcrumb span { color: var(--text-dark); font-weight: 600; }

        /* STEPPER */
        .stepper-wrap { max-width: 1100px; margin: 0 auto; padding: 28px 24px; }
        .stepper { display: flex; align-items: center; justify-content: center; }
        .step { display: flex; flex-direction: column; align-items: center; gap: 8px; }
        .step-circle { width: 52px; height: 52px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 18px; font-weight: 700; border: 2px solid var(--pink); color: var(--pink); background: var(--white); }
        .step-circle.done { background: var(--pink); color: white; }
        .step-circle.active { background: var(--pink); color: white; }
        .step-label { font-size: 13px; font-weight: 500; color: var(--text-dark); }
        .step-line { flex: 1; height: 2px; background: #E5C5CF; margin-bottom: 24px; max-width: 120px; }
        .step-line.done { background: var(--pink); }

        /* MAIN LAYOUT */
        .main { max-width: 1100px; margin: 0 auto; padding: 0 24px 60px; display: grid; grid-template-columns: 1fr 380px; gap: 24px; align-items: start; }

        /* CARD */
        .card { background: var(--white); border-radius: 16px; border: 1px solid #EDE0D4; padding: 24px; margin-bottom: 16px; }

        /* TIMER */
        .timer-label { font-size: 14px; font-weight: 700; color: var(--pink); margin-bottom: 16px; }

        .timer-box { background: var(--cream-dark); border-radius: 12px; padding: 16px 20px; display: flex; align-items: center; justify-content: space-between; margin-bottom: 12px; }
        .timer-text { font-size: 13px; color: var(--text-dark); }
        .timer-digits { display: flex; align-items: center; gap: 4px; }
        .timer-unit { text-align: center; }
        .timer-num { font-size: 28px; font-weight: 800; color: var(--pink); font-variant-numeric: tabular-nums; }
        .timer-sep { font-size: 24px; font-weight: 800; color: var(--pink); margin-bottom: 12px; }
        .timer-sub { font-size: 10px; color: var(--gray); }

        .timer-warning { font-size: 12px; color: var(--pink); margin-bottom: 12px; }

        .alert-box { background: #FFF0F3; border: 1px solid #FECDD3; border-radius: 10px; padding: 12px 16px; font-size: 13px; color: #BE123C; line-height: 1.6; }

        /* METODE PEMBAYARAN */
        .metode-label { font-size: 15px; font-weight: 700; color: var(--text-dark); margin-bottom: 12px; }
        .status-badge { display: inline-flex; align-items: center; gap: 6px; background: var(--cream-dark); border-radius: 20px; padding: 4px 12px; font-size: 12px; font-weight: 600; color: var(--brown-dark); margin-bottom: 16px; }
        .status-dot { width: 8px; height: 8px; border-radius: 50%; background: #F59E0B; }

        .metode-tabs { display: grid; grid-template-columns: repeat(3, 1fr); gap: 10px; margin-bottom: 20px; }
        .metode-tab { border: 1.5px solid #D1C0B8; border-radius: 10px; padding: 12px; text-align: center; cursor: pointer; transition: all 0.2s; background: var(--white); }
        .metode-tab.active { border-color: var(--pink); background: #FFF0F3; }
        .metode-tab p { font-size: 13px; font-weight: 700; color: var(--text-dark); }
        .metode-tab small { font-size: 11px; color: var(--gray); }

        /* BANK INFO */
        .bank-header { display: flex; align-items: center; gap: 12px; padding: 14px; background: var(--cream-dark); border-radius: 10px; margin-bottom: 16px; }
        .bank-logo { width: 42px; height: 42px; border-radius: 8px; background: #006CB0; display: flex; align-items: center; justify-content: center; color: white; font-size: 12px; font-weight: 800; }
        .bank-name { font-size: 14px; font-weight: 700; color: var(--text-dark); }
        .bank-desc { font-size: 12px; color: var(--gray); }

        .bank-row { display: flex; justify-content: space-between; align-items: center; padding: 12px 0; border-bottom: 1px solid #F0E8E0; }
        .bank-row:last-child { border-bottom: none; }
        .bank-row-label { font-size: 13px; color: var(--gray); }
        .bank-row-value { font-size: 13px; font-weight: 600; color: var(--text-dark); display: flex; align-items: center; gap: 8px; }

        .btn-salin { background: none; border: 1.5px solid #D1C0B8; border-radius: 6px; padding: 3px 10px; font-size: 12px; font-weight: 600; cursor: pointer; font-family: 'Plus Jakarta Sans', sans-serif; transition: all 0.2s; }
        .btn-salin:hover { background: var(--cream); }

        .jumlah-box { background: var(--pink); border-radius: 10px; padding: 14px 16px; display: flex; align-items: center; justify-content: space-between; margin: 12px 0; }
        .jumlah-left p { font-size: 13px; font-weight: 700; color: white; }
        .jumlah-left small { font-size: 11px; color: rgba(255,255,255,0.75); }
        .jumlah-right { display: flex; align-items: center; gap: 8px; }
        .jumlah-amount { font-size: 18px; font-weight: 800; color: white; }
        .btn-salin-white { background: white; border: none; border-radius: 6px; padding: 4px 10px; font-size: 12px; font-weight: 700; cursor: pointer; color: var(--pink); font-family: 'Plus Jakarta Sans', sans-serif; }

        .kode-unik-note { font-size: 12px; color: var(--gray); line-height: 1.6; margin-top: 4px; }

        /* CARA TRANSFER */
        .cara-label { font-size: 13px; font-weight: 700; color: var(--pink); margin-bottom: 12px; }
        .cara-list { list-style: none; display: flex; flex-direction: column; gap: 10px; }
        .cara-item { display: flex; align-items: flex-start; gap: 10px; font-size: 13px; color: var(--text-dark); line-height: 1.6; }
        .cara-num { width: 22px; height: 22px; border-radius: 50%; border: 1.5px solid var(--pink); color: var(--pink); font-size: 11px; font-weight: 700; display: flex; align-items: center; justify-content: center; flex-shrink: 0; margin-top: 1px; }
        .cara-item small { font-size: 11px; color: var(--gray); display: block; margin-top: 2px; }

        /* UPLOAD */
        .upload-label { font-size: 13px; font-weight: 700; color: var(--pink); margin-bottom: 12px; }
        .upload-zone { border: 2px dashed #D1C0B8; border-radius: 12px; padding: 32px; text-align: center; cursor: pointer; transition: all 0.2s; margin-bottom: 12px; }
        .upload-zone:hover { border-color: var(--pink); background: #FFF0F3; }
        .upload-icon { font-size: 32px; margin-bottom: 8px; }
        .upload-text { font-size: 13px; color: var(--text-dark); margin-bottom: 4px; }
        .upload-sub { font-size: 12px; color: var(--gray); margin-bottom: 12px; }
        .btn-pilih { background: var(--pink); color: white; border: none; border-radius: 8px; padding: 8px 20px; font-size: 13px; font-weight: 600; cursor: pointer; font-family: 'Plus Jakarta Sans', sans-serif; }
        .upload-format { font-size: 11px; color: var(--gray); }
        .upload-info { background: #ECFDF5; border: 1px solid #A7F3D0; border-radius: 10px; padding: 12px 16px; font-size: 12px; color: #065F46; line-height: 1.6; }

        /* RINGKASAN */
        .summary-card { background: var(--white); border-radius: 16px; border: 1px solid #EDE0D4; padding: 24px; position: sticky; top: 90px; }
        .summary-title { font-size: 18px; font-weight: 700; color: var(--text-dark); margin-bottom: 20px; }
        .summary-item { display: flex; align-items: center; gap: 12px; padding-bottom: 16px; border-bottom: 1px solid #F0E8E0; margin-bottom: 16px; }
        .summary-item img { width: 52px; height: 52px; object-fit: cover; border-radius: 8px; }
        .summary-item-info { flex: 1; }
        .summary-item-info p { font-size: 13px; font-weight: 600; color: var(--text-dark); }
        .summary-item-info small { font-size: 12px; color: var(--gray); }
        .summary-item-price { font-size: 13px; font-weight: 600; color: var(--text-dark); }
        .summary-row { display: flex; justify-content: space-between; font-size: 13px; margin-bottom: 8px; }
        .summary-row span:first-child { color: var(--gray); }
        .summary-row span:last-child { font-weight: 600; color: var(--text-dark); }
        .summary-row .plus { color: var(--text-dark); }
        .summary-total { display: flex; justify-content: space-between; font-size: 15px; font-weight: 700; padding-top: 12px; border-top: 1.5px solid #EDE0D4; margin: 12px 0 20px; }
        .summary-total span:last-child { color: var(--text-dark); }

        .detail-pengiriman { margin-bottom: 20px; }
        .detail-pengiriman p { font-size: 12px; font-weight: 700; color: var(--gray); margin-bottom: 6px; }
        .detail-pengiriman .alamat { font-size: 13px; color: var(--text-dark); }
        .detail-pengiriman .jadwal { font-size: 13px; color: var(--pink); margin-top: 2px; }

        .btn-upload-bukti { width: 100%; background: var(--brown-dark); color: white; border: none; border-radius: 10px; padding: 14px; font-size: 14px; font-weight: 700; cursor: pointer; font-family: 'Plus Jakarta Sans', sans-serif; margin-bottom: 10px; transition: opacity 0.2s; }
        .btn-upload-bukti:hover { opacity: 0.85; }

        .bantuan { text-align: center; font-size: 12px; color: var(--gray); margin-bottom: 16px; }
        .bantuan a { color: var(--green); font-weight: 600; }

        .aman-box { background: var(--cream); border-radius: 10px; padding: 14px; }
        .aman-title { font-size: 12px; font-weight: 700; color: var(--text-dark); margin-bottom: 8px; }
        .aman-item { display: flex; align-items: center; gap: 8px; font-size: 12px; color: var(--text-dark); margin-bottom: 6px; }
        .aman-check { color: var(--green); font-size: 14px; }

        /* FOOTER */
        .footer { background-color: var(--brown-dark); color: white; padding: 56px 24px; }
        .footer-inner { max-width: 1100px; margin: 0 auto; display: grid; grid-template-columns: 2fr 1fr 1fr 1.5fr; gap: 40px; }
        .footer-logo { font-family: 'Playfair Display', serif; font-size: 22px; font-weight: 800; color: var(--pink); margin-bottom: 8px; }
        .footer-desc { font-size: 13px; opacity: 0.6; margin-bottom: 20px; line-height: 1.6; }
        .footer-socials { display: flex; gap: 16px; font-size: 18px; }
        .footer-socials a { opacity: 0.6; }
        .footer-heading { font-size: 14px; font-weight: 700; margin-bottom: 16px; }
        .footer-links { list-style: none; display: flex; flex-direction: column; gap: 10px; }
        .footer-links a { color: white; font-size: 13px; opacity: 0.6; }
        .footer-contact { list-style: none; display: flex; flex-direction: column; gap: 10px; }
        .footer-contact li { font-size: 13px; opacity: 0.6; line-height: 1.5; }

        @media (max-width: 768px) {
            .navbar-links { display: none; }
            .main { grid-template-columns: 1fr; }
            .footer-inner { grid-template-columns: 1fr 1fr; }
            .timer-box { flex-direction: column; gap: 12px; }
        }
    </style>
</head>
<body>

{{-- NAVBAR --}}
<nav class="navbar">
    <div class="navbar-inner">
        <a href="/" class="navbar-logo">Jagoan Kue</a>
        <ul class="navbar-links">
            <li><a href="/">Beranda</a></li>
            <li><a href="/products">Katalog</a></li>
            <li><a href="/orders">Pemesanan</a></li>
        </ul>
        <div class="navbar-actions">
            <a href="/cart" class="btn-cart">🛒 Keranjang</a>
            @auth
                <a href="/profile" class="btn-login">{{ auth()->user()->name }}</a>
            @else
                <a href="/login" class="btn-login">Login</a>
            @endauth
        </div>
    </div>
</nav>

{{-- BREADCRUMB --}}
<div class="breadcrumb">
    <a href="/">Beranda</a> / <a href="/products">Katalog</a> / <a href="#">Form Pemesanan</a> / <span>Konfirmasi Pembayaran</span>
</div>

{{-- STEPPER --}}
<div class="stepper-wrap">
    <div class="stepper">
        <div class="step">
            <div class="step-circle done">✓</div>
            <span class="step-label">Pilih Kue</span>
        </div>
        <div class="step-line done"></div>
        <div class="step">
            <div class="step-circle done">✓</div>
            <span class="step-label">Detail Pesanan</span>
        </div>
        <div class="step-line done"></div>
        <div class="step">
            <div class="step-circle active">3</div>
            <span class="step-label">Pembayaran</span>
        </div>
        <div class="step-line"></div>
        <div class="step">
            <div class="step-circle">4</div>
            <span class="step-label">Konfirmasi</span>
        </div>
    </div>
</div>

{{-- MAIN --}}
<div class="main">

    {{-- KIRI --}}
    <div>

        {{-- TIMER --}}
        <div class="card">
            <p class="timer-label">Batas Waktu Pembayaran</p>
            <div class="timer-box">
                <p class="timer-text">Selesaikan pembayaran sebelum:</p>
                <div class="timer-digits">
                    <div class="timer-unit">
                        <div class="timer-num" id="timer-jam">01</div>
                        <div class="timer-sub">Jam</div>
                    </div>
                    <div class="timer-sep">:</div>
                    <div class="timer-unit">
                        <div class="timer-num" id="timer-menit">47</div>
                        <div class="timer-sub">Menit</div>
                    </div>
                    <div class="timer-sep">:</div>
                    <div class="timer-unit">
                        <div class="timer-num" id="timer-detik">33</div>
                        <div class="timer-sub">Detik</div>
                    </div>
                </div>
            </div>
            <p class="timer-warning">
                Pesanan akan otomatis dibatalkan jika tidak dibayar sebelum
                <strong>{{ isset($order) ? \Carbon\Carbon::parse($order->created_at)->addHours(2)->format('d M Y, H.i') . ' WIB' : '12 Agt 2025, 16.30 WIB' }}</strong>
            </p>
            <div class="alert-box">
                Segera lakukan pembayaran agar pesanan kue kamu tidak hangus dan stok tetap terjamin.
            </div>
        </div>

        {{-- METODE PEMBAYARAN --}}
        <div class="card">
            <p class="metode-label">Metode Pembayaran</p>
            <div class="status-badge">
                <div class="status-dot"></div>
                Menunggu Pembayaran
            </div>

            <div class="metode-tabs">
                <div class="metode-tab active" onclick="setMetode(this, 'bank')">
                    <p>Transfer Bank</p>
                    <small>BCA / BNI / Mandiri</small>
                </div>
                <div class="metode-tab" onclick="setMetode(this, 'ewallet')">
                    <p>E-Wallet</p>
                    <small>OVO / GoPay / Dana</small>
                </div>
                <div class="metode-tab" onclick="setMetode(this, 'cod')">
                    <p>COD</p>
                    <small>Bayar di tempat</small>
                </div>
            </div>

            {{-- BANK DETAIL --}}
            <div id="bank-detail">
                <div class="bank-header">
                    <div class="bank-logo">BCA</div>
                    <div>
                        <p class="bank-name">Bank Central Asia</p>
                        <p class="bank-desc">Transfer Manual • Konfirmasi otomatis dalam 5 menit</p>
                    </div>
                </div>

                <div class="bank-row">
                    <span class="bank-row-label">Nama Rekening</span>
                    <span class="bank-row-value">Jagoan Kue Offical</span>
                </div>
                <div class="bank-row">
                    <span class="bank-row-label">Nomor Rekening</span>
                    <span class="bank-row-value">
                        1234 5678 9012
                        <button class="btn-salin" onclick="salin('1234567890 12')">Salin</button>
                    </span>
                </div>
                <div class="bank-row">
                    <span class="bank-row-label">Bank Tujuan</span>
                    <span class="bank-row-value">BCA (Bank Central Asia)</span>
                </div>

                <div class="jumlah-box">
                    <div class="jumlah-left">
                        <p>Jumlah Transfer Tepat</p>
                        <small>Transfer sesuai nominal untuk verifikasi otomatis</small>
                    </div>
                    <div class="jumlah-right">
                        <span class="jumlah-amount">Rp 300.047</span>
                        <button class="btn-salin-white" onclick="salin('300047')">Salin</button>
                    </div>
                </div>

                <p class="kode-unik-note">
                    Nominal transfer berbeda 47 rupiah dari total pesanan — ini adalah kode unik untuk verifikasi otomatis. Pastikan transfer tepat sesuai nominal di atas.
                </p>
            </div>
        </div>

        {{-- CARA TRANSFER --}}
        <div class="card">
            <p class="cara-label">CARA MELAKUKAN TRANSFER</p>
            <ul class="cara-list">
                <li class="cara-item">
                    <div class="cara-num">1</div>
                    <div>Buka aplikasi BCA mobile atau m-BCA di smartphone kamu</div>
                </li>
                <li class="cara-item">
                    <div class="cara-num">2</div>
                    <div>Pilih menu Transfer → Transfer ke Rekening BCA</div>
                </li>
                <li class="cara-item">
                    <div class="cara-num">3</div>
                    <div>Masukkan nomor rekening 1234 5678 9012 atas nama Jagoan Kue Official</div>
                </li>
                <li class="cara-item">
                    <div class="cara-num">4</div>
                    <div>Masukkan nominal transfer Rp 300.047 (persis, termasuk kode unik)</div>
                </li>
                <li class="cara-item">
                    <div class="cara-num">5</div>
                    <div>
                        Selesaikan transfer, lalu upload bukti pembayaran di bawah ini
                        <small>Pembayaran terverifikasi otomatis dalam 5-10 menit setelah upload</small>
                    </div>
                </li>
            </ul>
        </div>

        {{-- UPLOAD BUKTI --}}
        <div class="card">
            <p class="upload-label">UPLOAD BUKTI PEMBAYARAN</p>
            <div class="upload-zone" onclick="document.getElementById('file-input').click()"
                 ondragover="event.preventDefault()" ondrop="handleDrop(event)">
                <div class="upload-icon">📤</div>
                <p class="upload-text">Seret & letakkan file di sini</p>
                <p class="upload-sub">atau klik untuk memilih file dari perangkat kamu</p>
                <button class="btn-pilih" type="button">Pilih File</button>
                <input type="file" id="file-input" style="display:none" accept=".jpg,.jpeg,.png,.pdf" onchange="handleFile(this)">
                <p class="upload-format" style="margin-top:10px;">Format: JPG, PNG, PDF • Maks. ukuran 5MB</p>
            </div>
            <div class="upload-info">
                Bukti pembayaran akan diverifikasi oleh tim kami. Proses biasanya selesai dalam 5-10 menit pada jam operasional.
            </div>
        </div>

    </div>

    {{-- KANAN: RINGKASAN --}}
    <div>
        <div class="summary-card">
            <p class="summary-title">Ringkasan Pesanan</p>

            <div class="summary-item">
                <img src="{{ isset($order) && $order->orderItems->first()?->product?->image ? asset('storage/' . $order->orderItems->first()->product->image) : 'https://images.unsplash.com/photo-1563729784474-d77dbb933a9e?w=200&q=80' }}"
                     alt="Produk">
                <div class="summary-item-info">
                    <p>{{ isset($order) ? $order->orderItems->first()?->product?->name : 'Kue Coklat Premium' }}</p>
                    <small>Medium . 1x</small>
                </div>
                <span class="summary-item-price">Rp. {{ isset($order) ? number_format($order->orderItems->first()?->price, 0, ',', '.') : '120.000' }}</span>
            </div>

            <div class="summary-row">
                <span>Subtotal</span>
                <span>Rp. {{ isset($order) ? number_format($order->total_price, 0, ',', '.') : '120.000' }}</span>
            </div>
            <div class="summary-row">
                <span>Ongkos Kirim</span>
                <span>Rp. 15.000</span>
            </div>
            <div class="summary-row">
                <span>Kode unik</span>
                <span class="plus">+ Rp 47</span>
            </div>
            <div class="summary-row">
                <span>Diskon</span>
                <span>- Rp 0</span>
            </div>

            <div class="summary-total">
                <span>Total Transfer</span>
                <span>Rp 300.047</span>
            </div>

            <div class="detail-pengiriman">
                <p>Detail Pengiriman</p>
                <p class="alamat">{{ isset($order) ? auth()->user()->name . ' - ' . $order->shipping_address : 'Rina Amelia - 0812-3456-7890 Jl. Imam Bonjol No. 12, Padang' }}</p>
                <p class="jadwal">Rabu, 20 Agt 2025 · 10.00-12.00</p>
            </div>

            <button class="btn-upload-bukti">Menunggu Upload Bukti Bayar</button>

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

{{-- FOOTER --}}
<footer class="footer">
    <div class="footer-inner">
        <div>
            <p class="footer-logo">Jagoan Kue</p>
            <p class="footer-desc">Menyediakan kue dengan cinta sejak 2023</p>
            <div class="footer-socials">
                <a href="#">📸</a><a href="#">🎵</a><a href="#">💬</a><a href="#">👤</a>
            </div>
        </div>
        <div>
            <p class="footer-heading">Layanan</p>
            <ul class="footer-links">
                <li><a href="#">Katalog Kue</a></li>
                <li><a href="#">Kue Custom</a></li>
                <li><a href="#">Hampers</a></li>
                <li><a href="#">Catering</a></li>
            </ul>
        </div>
        <div>
            <p class="footer-heading">Selengkapnya</p>
            <ul class="footer-links">
                <li><a href="#">Tentang Kami</a></li>
                <li><a href="#">Blog</a></li>
                <li><a href="#">Karir</a></li>
            </ul>
        </div>
        <div>
            <p class="footer-heading">Kontak Kami</p>
            <ul class="footer-contact">
                <li>0822-8320-3385</li>
                <li>muhammadzidane253@gmail.com</li>
                <li>Payakumbuh, Sumatera Barat</li>
            </ul>
        </div>
    </div>
</footer>

<script>
    // TIMER
    let totalSeconds = 1 * 3600 + 47 * 60 + 33;
    function updateTimer() {
        if (totalSeconds <= 0) {
            document.getElementById('timer-jam').textContent = '00';
            document.getElementById('timer-menit').textContent = '00';
            document.getElementById('timer-detik').textContent = '00';
            return;
        }
        totalSeconds--;
        const jam = Math.floor(totalSeconds / 3600);
        const menit = Math.floor((totalSeconds % 3600) / 60);
        const detik = totalSeconds % 60;
        document.getElementById('timer-jam').textContent = String(jam).padStart(2, '0');
        document.getElementById('timer-menit').textContent = String(menit).padStart(2, '0');
        document.getElementById('timer-detik').textContent = String(detik).padStart(2, '0');
    }
    setInterval(updateTimer, 1000);

    // METODE TAB
    function setMetode(el, type) {
        document.querySelectorAll('.metode-tab').forEach(t => t.classList.remove('active'));
        el.classList.add('active');
    }

    // SALIN
    function salin(text) {
        navigator.clipboard.writeText(text).then(() => alert('Disalin: ' + text));
    }

    // FILE UPLOAD
    function handleFile(input) {
    if (input.files && input.files[0]) {
        const file = input.files[0];

        // Update zona upload
        const zone = document.querySelector('.upload-zone');
        zone.innerHTML = `<div style="font-size:32px;">✅</div><p style="font-size:13px;font-weight:600;color:var(--text-dark);margin-top:8px;">${file.name}</p><p style="font-size:12px;color:var(--gray);">File siap diupload</p>`;

        // Update tombol di kanan
        const btn = document.querySelector('.btn-upload-bukti');
        btn.style.background = 'var(--pink)';
        btn.textContent = '✓ Kirim Bukti Pembayaran';
        btn.onclick = function() {
            btn.textContent = '⏳ Mengupload...';
            btn.disabled = true;
            setTimeout(() => {
                window.location.href = '/orders/{{ $order->id ?? 1 }}/success';
            }, 2000);
        };
    }
}

    function handleDrop(e) {
        e.preventDefault();
        const input = document.getElementById('file-input');
        input.files = e.dataTransfer.files;
        handleFile(input);
    }
</script>

</body>
</html>
