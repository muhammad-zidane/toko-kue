<!DOCTYPE html>
<html lang="id">
<head>
    <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}" />
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Jagoan Kue - {{ $product->name }}</title>
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
        }

        body { font-family: 'Plus Jakarta Sans', sans-serif; color: var(--text-dark); background: var(--white); }
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
        .btn-cart:hover { opacity: 0.85; }
        .btn-login { border: 1.5px solid white; color: white; padding: 8px 18px; border-radius: 8px; font-size: 14px; font-weight: 600; transition: all 0.2s; }
        .btn-login:hover { background: white; color: var(--brown-dark); }

        /* PRODUCT DETAIL */
        .product-page { background-color: var(--cream); padding: 60px 24px; min-height: 70vh; }
        .product-inner {
            max-width: 1100px;
            margin: 0 auto;
            display: grid;
            grid-template-columns: 1fr 1.2fr 0.8fr;
            gap: 40px;
            align-items: start;
        }

        /* LEFT - Image */
        .product-image img {
            width: 100%;
            aspect-ratio: 1;
            object-fit: cover;
            border-radius: 16px;
            box-shadow: 0 8px 32px rgba(0,0,0,0.1);
        }

        /* MIDDLE - Info */
        .product-info h1 {
            font-size: 22px;
            font-weight: 700;
            color: var(--text-dark);
            margin-bottom: 6px;
        }

        .product-sold {
            font-size: 12px;
            color: var(--gray);
            margin-bottom: 16px;
        }

        .product-price {
            font-size: 22px;
            font-weight: 700;
            color: var(--text-dark);
            margin-bottom: 16px;
            padding-bottom: 16px;
            border-bottom: 1.5px solid #E5B8C2;
        }

        .product-detail-label {
            font-size: 14px;
            font-weight: 600;
            color: var(--text-dark);
            margin-bottom: 10px;
        }

        .product-description {
            font-size: 14px;
            color: var(--gray);
            line-height: 1.8;
        }

        /* RIGHT - Widget */
        .product-widget {
            background: var(--white);
            border-radius: 12px;
            border: 1px solid #E5D5C5;
            padding: 20px;
        }

        .widget-title {
            font-size: 14px;
            font-weight: 700;
            color: var(--text-dark);
            margin-bottom: 16px;
        }

        .quantity-row {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 16px;
        }

        .quantity-control {
            display: flex;
            align-items: center;
            border: 1px solid #D1C4C0;
            border-radius: 6px;
            overflow: hidden;
        }

        .qty-btn {
            background: none;
            border: none;
            padding: 6px 12px;
            font-size: 16px;
            cursor: pointer;
            color: var(--text-dark);
            font-weight: 600;
            transition: background 0.2s;
        }

        .qty-btn:hover { background: var(--cream); }

        .qty-input {
            width: 40px;
            text-align: center;
            border: none;
            border-left: 1px solid #D1C4C0;
            border-right: 1px solid #D1C4C0;
            padding: 6px 0;
            font-size: 14px;
            font-weight: 600;
            font-family: 'Plus Jakarta Sans', sans-serif;
            outline: none;
        }

        .stock-info {
            font-size: 13px;
            color: var(--gray);
        }

        .stock-info span {
            font-weight: 700;
            color: var(--text-dark);
        }

        .subtotal-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 12px 0;
            border-top: 1px solid #F0E8E0;
            margin-bottom: 12px;
        }

        .subtotal-label { font-size: 13px; color: var(--gray); }

        .subtotal-value {
            font-size: 15px;
            font-weight: 700;
            color: var(--text-dark);
        }

        /* Catatan */
        .catatan-label {
            font-size: 13px;
            font-weight: 600;
            color: var(--text-dark);
            margin-bottom: 6px;
            display: block;
        }

        .catatan-input {
            width: 100%;
            border: 1px solid #D1C4C0;
            border-radius: 8px;
            padding: 8px 12px;
            font-size: 13px;
            font-family: 'Plus Jakarta Sans', sans-serif;
            resize: none;
            outline: none;
            margin-bottom: 16px;
            background: var(--cream);
        }

        .catatan-input:focus { border-color: var(--pink); }

        .btn-add-cart {
            width: 100%;
            background-color: var(--pink);
            color: white;
            border: none;
            padding: 12px;
            border-radius: 8px;
            font-size: 14px;
            font-weight: 700;
            cursor: pointer;
            font-family: 'Plus Jakarta Sans', sans-serif;
            transition: opacity 0.2s;
        }

        .btn-add-cart:hover { opacity: 0.85; }

        /* FOOTER */
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

        /* RESPONSIVE */
        @media (max-width: 768px) {
            .navbar-links { display: none; }
            .product-inner { grid-template-columns: 1fr; }
            .footer-inner { grid-template-columns: 1fr 1fr; }
        }
    </style>
    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @endif
</head>
<body>
    {{-- 1. Loader --}}
    <div id="page-loader">
        <div class="loader-spinner"></div>
    </div>

    <div class="fade-in-content">

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

{{-- PRODUCT DETAIL --}}
<section class="product-page">
    <div class="product-inner">

        {{-- Gambar --}}
        <div class="product-image">
            <img src="{{ $product->image ? asset('storage/' . $product->image) : 'https://images.unsplash.com/photo-1563729784474-d77dbb933a9e?w=600&q=80' }}"
                 alt="{{ $product->name }}">
        </div>

        {{-- Info Produk --}}
        <div class="product-info">
            <h1>{{ $product->name }}</h1>
            <p class="product-sold">30+ Barang Telah Terjual</p>
            <p class="product-price">Rp{{ number_format($product->price, 0, ',', '.') }}</p>
            <p class="product-detail-label">Detail Produk:</p>
            <p class="product-description">{{ $product->description ?? 'Tidak ada deskripsi untuk produk ini.' }}</p>
        </div>

        {{-- Widget Keranjang --}}
        <div class="product-widget">
            <p class="widget-title">Atur Jumlah dan Catatan</p>

            <div class="quantity-row">
                <div class="quantity-control">
                    <button class="qty-btn" onclick="changeQty(-1)">−</button>
                    <input type="number" id="qty" class="qty-input" value="1" min="1" max="{{ $product->stock }}">
                    <button class="qty-btn" onclick="changeQty(1)">+</button>
                </div>
                <p class="stock-info">Stok Total: <span>{{ $product->stock }}</span></p>
            </div>

            <label class="catatan-label">Catatan (opsional)</label>
            <textarea class="catatan-input" rows="3" placeholder="Contoh: Tulisan di kue, warna, ukuran..."></textarea>

            <div class="subtotal-row">
                <span class="subtotal-label">Subtotal</span>
                <span class="subtotal-value" id="subtotal">Rp{{ number_format($product->price, 0, ',', '.') }}</span>
            </div>

        @auth
            <form action="/cart/add" method="POST">
                @csrf
                    <input type="hidden" name="product_id" value="{{ $product->id }}">
                    <input type="hidden" name="quantity" id="qty-hidden" value="1">
                    <button type="submit" class="btn-add-cart">+ Keranjang</button>
                </form>
        @else
            <a href="/login">
                <button class="btn-add-cart">+ Keranjang</button>
            </a>
        @endauth
        </div>

    </div>
</section>

{{-- FOOTER --}}
<footer class="footer">
    <div class="footer-inner">
        <div>
            <p class="footer-logo">Jagoan Kue</p>
            <p class="footer-desc">Menyediakan kue dengan cinta sejak 2023</p>
            <div class="footer-socials">
                <a href="#">📸</a>
                <a href="#">🎵</a>
                <a href="#">💬</a>
                <a href="#">👤</a>
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
                <li><a href="/cdn-cgi/l/email-protection" class="__cf_email__" data-cfemail="f29f879a939f9f9396889b96939c97c0c7c1b2959f939b9edc919d9f">[email&#160;protected]</a></li>
                <li>Payakumbuh, Sumatera Barat</li>
            </ul>
        </div>
    </div>
</footer>

<script data-cfasync="false" src="/cdn-cgi/scripts/5c5dd728/cloudflare-static/email-decode.min.js"></script><script>
    const price = {{ $product->price }};

    function changeQty(delta) {
        const input = document.getElementById('qty');
        const hidden = document.getElementById('qty-hidden');
        let val = parseInt(input.value) + delta;
        const max = parseInt(input.max);
        if (val < 1) val = 1;
        if (val > max) val = max;
        input.value = val;
        hidden.value = val;
        updateSubtotal(val);
    }

    function updateSubtotal(qty) {
        const total = price * qty;
        document.getElementById('subtotal').textContent =
            'Rp' + total.toLocaleString('id-ID');
    }

    document.getElementById('qty').addEventListener('input', function() {
        let val = parseInt(this.value);
        if (isNaN(val) || val < 1) val = 1;
        if (val > parseInt(this.max)) val = parseInt(this.max);
        this.value = val;
        document.getElementById('qty-hidden').value = val;
        updateSubtotal(val);
    });


</script>

    </div>
</body>
</html>