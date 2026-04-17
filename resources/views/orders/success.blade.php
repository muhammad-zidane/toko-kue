<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Jagoan Kue - Pesanan Berhasil</title>
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
            --teal:       #0D9488;
        }
        body { font-family: 'Plus Jakarta Sans', sans-serif; color: var(--text-dark); background: var(--cream); }
        a { text-decoration: none; }

        /* NAVBAR */
        .navbar { background-color: var(--brown-dark); padding: 16px 24px; }
        .navbar-inner { max-width: 1100px; margin: 0 auto; display: flex; align-items: center; justify-content: space-between; }
        .navbar-logo { font-family: 'Playfair Display', serif; font-size: 24px; font-weight: 800; color: var(--pink); }
        .navbar-links { display: flex; gap: 32px; list-style: none; }
        .navbar-links a { color: white; font-size: 14px; font-weight: 500; opacity: 0.9; }
        .navbar-links a:hover { opacity: 1; }
        .navbar-actions { display: flex; align-items: center; gap: 12px; }
        .btn-cart { background-color: var(--pink); color: white; padding: 8px 18px; border-radius: 8px; font-size: 14px; font-weight: 600; }
        .btn-login { border: 1.5px solid white; color: white; padding: 8px 18px; border-radius: 8px; font-size: 14px; font-weight: 600; transition: all 0.2s; }
        .btn-login:hover { background: white; color: var(--brown-dark); }

        /* PAGE */
        .page { max-width: 960px; margin: 0 auto; padding: 40px 24px 60px; }

        /* SUCCESS HEADER */
        .success-header {
            background: var(--white);
            border-radius: 20px;
            padding: 48px 32px;
            text-align: center;
            margin-bottom: 24px;
            border: 1px solid #EDE0D4;
        }

        .success-icon {
            width: 72px;
            height: 72px;
            border-radius: 50%;
            border: 3px solid var(--teal);
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px;
            font-size: 28px;
            color: var(--teal);
            animation: popIn 0.5s ease;
        }

        @keyframes popIn {
            0% { transform: scale(0); opacity: 0; }
            70% { transform: scale(1.1); }
            100% { transform: scale(1); opacity: 1; }
        }

        .success-title {
            font-family: 'Playfair Display', serif;
            font-size: 28px;
            font-weight: 700;
            color: var(--text-dark);
            margin-bottom: 10px;
        }

        .success-desc {
            font-size: 14px;
            color: var(--gray);
            line-height: 1.7;
            max-width: 420px;
            margin: 0 auto 24px;
        }

        .order-code-box {
            display: inline-block;
            border: 1.5px solid #E5D5C5;
            border-radius: 10px;
            padding: 14px 32px;
            font-size: 14px;
            color: var(--text-dark);
        }

        .order-code-box span { color: var(--pink); font-weight: 700; }

        /* GRID */
        .detail-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 20px; }

        /* CARD */
        .card { background: var(--white); border-radius: 16px; border: 1px solid #EDE0D4; padding: 24px; }

        .card-label { font-size: 13px; font-weight: 700; color: var(--pink); margin-bottom: 16px; letter-spacing: 0.5px; }

        /* DETAIL PESANAN */
        .order-item { display: flex; align-items: center; gap: 12px; padding: 12px; background: var(--cream); border-radius: 10px; margin-bottom: 16px; }
        .order-item img { width: 52px; height: 52px; object-fit: cover; border-radius: 8px; flex-shrink: 0; }
        .order-item-info { flex: 1; }
        .order-item-info p { font-size: 14px; font-weight: 700; color: var(--text-dark); }
        .order-item-info small { font-size: 12px; color: var(--gray); }
        .order-item-info .tulisan { font-size: 12px; color: var(--pink); margin-top: 2px; }
        .order-item-price { font-size: 14px; font-weight: 600; color: var(--text-dark); }

        .price-row { display: flex; justify-content: space-between; font-size: 13px; margin-bottom: 8px; }
        .price-row span:first-child { color: var(--gray); }
        .price-row span:last-child { font-weight: 500; color: var(--text-dark); }

        .price-total { display: flex; justify-content: space-between; align-items: center; padding: 12px 16px; background: var(--cream); border-radius: 10px; margin-top: 12px; }
        .price-total span:first-child { font-size: 14px; font-weight: 600; color: var(--text-dark); }
        .price-total span:last-child { font-size: 18px; font-weight: 800; color: var(--text-dark); }

        /* INFO PENGIRIMAN */
        .info-row { display: flex; justify-content: space-between; align-items: flex-start; padding: 10px 0; border-bottom: 1px solid #F0E8E0; font-size: 13px; }
        .info-row:last-child { border-bottom: none; }
        .info-row span:first-child { color: var(--gray); flex-shrink: 0; }
        .info-row span:last-child { font-weight: 600; color: var(--text-dark); text-align: right; max-width: 60%; }
        .info-row .highlight { color: var(--pink); }
        .info-row .lunas { color: var(--green); }

        /* STATUS PESANAN */
        .status-list { display: flex; flex-direction: column; gap: 0; }

        .status-item { display: flex; align-items: flex-start; gap: 12px; position: relative; padding-bottom: 20px; }
        .status-item:last-child { padding-bottom: 0; }

        .status-item::before {
            content: '';
            position: absolute;
            left: 10px;
            top: 24px;
            bottom: 0;
            width: 2px;
            background: #E5D5C5;
        }

        .status-item:last-child::before { display: none; }
        .status-item.done::before { background: var(--teal); }
        .status-item.active::before { background: linear-gradient(to bottom, var(--pink), #E5D5C5); }

        .status-dot {
            width: 22px;
            height: 22px;
            border-radius: 50%;
            border: 2px solid #E5D5C5;
            background: var(--white);
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            font-size: 11px;
            z-index: 1;
        }

        .status-dot.done { background: var(--teal); border-color: var(--teal); color: white; }
        .status-dot.active { background: var(--pink); border-color: var(--pink); color: white; font-size: 8px; }

        .status-content p { font-size: 13px; font-weight: 600; color: var(--text-dark); }
        .status-content small { font-size: 12px; color: var(--gray); }
        .status-content .est { font-size: 11px; color: var(--gray); display: block; margin-top: 2px; }

        /* WHATSAPP BANNER */
        .wa-banner {
            background: var(--white);
            border-radius: 16px;
            border: 1px solid #EDE0D4;
            padding: 20px 24px;
            display: flex;
            align-items: center;
            gap: 16px;
            margin-bottom: 24px;
        }

        .wa-icon { width: 44px; height: 44px; border-radius: 50%; background: #22C55E; display: flex; align-items: center; justify-content: center; font-size: 22px; flex-shrink: 0; }
        .wa-text { flex: 1; font-size: 13px; color: var(--gray); line-height: 1.6; }
        .btn-wa { background: var(--brown-dark); color: white; padding: 10px 20px; border-radius: 8px; font-size: 13px; font-weight: 700; white-space: nowrap; transition: opacity 0.2s; }
        .btn-wa:hover { opacity: 0.85; }

        /* ACTION BUTTONS */
        .action-buttons { display: grid; grid-template-columns: repeat(3, 1fr); gap: 12px; margin-bottom: 40px; }

        .btn-action {
            padding: 14px;
            border-radius: 10px;
            font-size: 14px;
            font-weight: 600;
            text-align: center;
            cursor: pointer;
            transition: all 0.2s;
            font-family: 'Plus Jakarta Sans', sans-serif;
            border: none;
        }

        .btn-action.pink { background: var(--pink); color: white; }
        .btn-action.pink:hover { opacity: 0.85; }
        .btn-action.outline { background: var(--white); color: var(--text-dark); border: 1.5px solid #D1C0B8; }
        .btn-action.outline:hover { background: var(--cream-dark); }

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
            .detail-grid { grid-template-columns: 1fr; }
            .action-buttons { grid-template-columns: 1fr; }
            .footer-inner { grid-template-columns: 1fr 1fr; }
            .wa-banner { flex-direction: column; text-align: center; }
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

{{-- PAGE --}}
<div class="page">

    {{-- SUCCESS HEADER --}}
    <div class="success-header">
        <div class="success-icon">✓</div>
        <h1 class="success-title">Pesanan Berhasil Ditempatkan!</h1>
        <p class="success-desc">
            Terima kasih! Pesananmu sedang kami proses. Kamu akan mendapatkan notifikasi WhatsApp ketika pesanan mulai dipersiapkan.
        </p>
        <div class="order-code-box">
            No. Pesanan: <span>{{ isset($order) ? '#SWO-' . date('Ymd', strtotime($order->created_at)) . '-' . str_pad($order->id, 4, '0', STR_PAD_LEFT) : '#SWO-20250812-0047' }}</span>
        </div>
    </div>

    {{-- DETAIL GRID --}}
    <div class="detail-grid">

        {{-- DETAIL PESANAN --}}
        <div class="card">
            <p class="card-label">Detail Pesanan</p>

            <div class="order-item">
                <img src="{{ isset($order) && $order->orderItems->first()?->product?->image ? asset('storage/' . $order->orderItems->first()->product->image) : 'https://images.unsplash.com/photo-1563729784474-d77dbb933a9e?w=200&q=80' }}"
                     alt="Produk">
                <div class="order-item-info">
                    <p>{{ isset($order) ? $order->orderItems->first()?->product?->name : 'Kue Coklat Premium' }}</p>
                    <small>Medium . {{ isset($order) ? $order->orderItems->first()?->quantity : 1 }}x</small>
                    <p class="tulisan">Tulisan: "Selamat Ulang Tahun Budi!"</p>
                </div>
                <span class="order-item-price">Rp. {{ isset($order) ? number_format($order->orderItems->first()?->price, 0, ',', '.') : '120.000' }}</span>
            </div>

            <div class="price-row"><span>Subtotal</span><span>Rp. {{ isset($order) ? number_format($order->total_price, 0, ',', '.') : '120.000' }}</span></div>
            <div class="price-row"><span>Ongkos Kirim</span><span>Rp 15.000</span></div>
            <div class="price-row"><span>Kode unik</span><span>+ Rp 47</span></div>
            <div class="price-row"><span>Diskon</span><span>− Rp 0</span></div>

            <div class="price-total">
                <span>Total Transfer</span>
                <span>Rp 300.047</span>
            </div>
        </div>

        {{-- KANAN: INFO + STATUS --}}
        <div>

            {{-- INFO PENGIRIMAN --}}
            <div class="card" style="margin-bottom: 20px;">
                <p class="card-label">INFO PENGIRIMAN</p>
                <div class="info-row">
                    <span>Penerima</span>
                    <span>{{ isset($order) ? auth()->user()->name : 'Rina Amelia' }}</span>
                </div>
                <div class="info-row">
                    <span>Telepon</span>
                    <span>0812-3456-7890</span>
                </div>
                <div class="info-row">
                    <span>Alamat</span>
                    <span>{{ isset($order) ? $order->shipping_address : 'Jl. Imam Bonjol No. 12, Padang' }}</span>
                </div>
                <div class="info-row">
                    <span>Tanggal kirim</span>
                    <span class="highlight">Rabu, 20 Agustus 2025</span>
                </div>
                <div class="info-row">
                    <span>Slot waktu</span>
                    <span>10.00 – 12.00 WIB</span>
                </div>
                <div class="info-row">
                    <span>Pembayaran</span>
                    <span>Transfer BCA</span>
                </div>
                <div class="info-row">
                    <span>Status bayar</span>
                    <span class="lunas">Lunas</span>
                </div>
            </div>

            {{-- STATUS PESANAN --}}
            <div class="card">
                <p class="card-label">STATUS PESANAN</p>
                <div class="status-list">
                    <div class="status-item done">
                        <div class="status-dot done">✓</div>
                        <div class="status-content">
                            <p>Pesanan Diterima Toko</p>
                            <small>{{ isset($order) ? \Carbon\Carbon::parse($order->created_at)->format('d M Y, H:i') : '12 Agt 2025, 14:32' }}</small>
                        </div>
                    </div>
                    <div class="status-item active">
                        <div class="status-dot active">●</div>
                        <div class="status-content">
                            <p>Sedang dipersiapkan</p>
                            <small>Kue sedang dibuat oleh tim kami</small>
                            <span class="est">Estimasi selesai: 19 Agt 2025</span>
                        </div>
                    </div>
                    <div class="status-item">
                        <div class="status-dot"></div>
                        <div class="status-content">
                            <p style="color: var(--gray);">Dalam pengiriman</p>
                        </div>
                    </div>
                    <div class="status-item">
                        <div class="status-dot"></div>
                        <div class="status-content">
                            <p style="color: var(--gray);">Pesanan diterima</p>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>

    {{-- WHATSAPP BANNER --}}
    <div class="wa-banner">
        <div class="wa-icon">💬</div>
        <p class="wa-text">Ada pertanyaan tentang pesananmu? Tim kami siap membantu via WhatsApp setiap hari pukul 08.00–20.00 WIB.</p>
        <a href="https://wa.me/6282283203385" target="_blank" class="btn-wa">Chat WhatsApp</a>
    </div>

    {{-- ACTION BUTTONS --}}
    <div class="action-buttons">
        <a href="/orders">
            <button class="btn-action pink" style="width:100%;">Lihat Riwayat Pesanan</button>
        </a>
        <button class="btn-action outline" onclick="window.print()">Unduh Bukti Pesanan</button>
        <a href="/">
            <button class="btn-action outline" style="width:100%;">Kembali ke Beranda</button>
        </a>
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

</body>
</html>
