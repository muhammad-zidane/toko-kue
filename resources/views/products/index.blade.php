<!DOCTYPE html>
<html lang="id">
<head>
    <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}" />
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Jagoan Kue - Katalog Produk</title>
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
        .navbar-links a.active { opacity: 1; font-weight: 700; }
        .navbar-actions { display: flex; align-items: center; gap: 12px; }
        .btn-cart { background-color: var(--pink); color: white; padding: 8px 18px; border-radius: 8px; font-size: 14px; font-weight: 600; }
        .btn-cart:hover { opacity: 0.85; }
        .btn-login { border: 1.5px solid white; color: white; padding: 8px 18px; border-radius: 8px; font-size: 14px; font-weight: 600; transition: all 0.2s; }
        .btn-login:hover { background: white; color: var(--brown-dark); }

        /* PAGE HEADER */
        .page-header { background-color: var(--cream); padding: 48px 24px 32px; text-align: center; }
        .page-header h1 { font-family: 'Playfair Display', serif; font-size: 36px; font-weight: 800; color: var(--text-dark); }

        /* CATEGORY SECTION */
        .category-section { padding: 48px 24px; }
        .category-section:nth-child(even) { background-color: var(--cream); }
        .category-section:nth-child(odd) { background-color: var(--white); }

        .category-inner { max-width: 1100px; margin: 0 auto; }

        .category-title {
            font-family: 'Playfair Display', serif;
            font-size: 28px;
            font-weight: 700;
            text-align: center;
            color: var(--text-dark);
            margin-bottom: 32px;
        }

        /* PRODUCT GRID */
        .product-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 24px; }

        .product-card {
            background: var(--white);
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 2px 12px rgba(0,0,0,0.07);
            transition: transform 0.2s, box-shadow 0.2s;
            display: block;
            color: inherit;
        }

        .product-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 8px 24px rgba(0,0,0,0.12);
        }

        .product-card img {
            width: 100%;
            height: 200px;
            object-fit: cover;
            display: block;
            transition: transform 0.3s;
        }

        .product-card:hover img { transform: scale(1.05); }

        .product-info { padding: 20px; }

        .product-info h3 {
            font-size: 16px;
            font-weight: 700;
            color: var(--text-dark);
            margin-bottom: 8px;
        }

        .product-info p {
            font-size: 13px;
            color: var(--gray);
            line-height: 1.6;
            margin-bottom: 16px;
            display: -webkit-box;
            -webkit-line-clamp: 3;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }

        .product-order {
            font-size: 13px;
            font-weight: 600;
            color: var(--text-dark);
            display: inline-flex;
            align-items: center;
            gap: 4px;
            transition: color 0.2s;
        }

        .product-order:hover { color: var(--pink); }
        .product-price { font-size: 15px; font-weight: 700; color: var(--pink); margin-bottom: 10px; display: block; }

        /* EMPTY STATE */
        .empty-state {
            grid-column: span 3;
            text-align: center;
            color: var(--gray);
            padding: 40px;
            font-size: 14px;
        }

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
            .product-grid { grid-template-columns: 1fr; }
            .footer-inner { grid-template-columns: 1fr 1fr; }
        }
    </style>
    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @endif
</head>
<body>
{{-- NAVBAR --}}
<nav class="navbar">
    <div class="navbar-inner">
        <a href="/" class="navbar-logo">Jagoan Kue</a>
        <ul class="navbar-links">
            <li><a href="/">Beranda</a></li>
            <li><a href="/products" class="active">Katalog</a></li>
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

{{-- PAGE HEADER --}}
<div class="page-header">
    <h1>Produk Kami</h1>
</div>

{{-- PRODUK PER KATEGORI --}}
@forelse($categories as $category)
<section class="category-section">
    <div class="category-inner">
        <h2 class="category-title">{{ $category->name }}</h2>

        <div class="product-grid">
            @forelse($category->products as $product)
            <a href="{{ route('products.show', $product) }}" class="product-card">
                <img src="{{ $product->image ? asset('storage/' . $product->image) : 'https://images.unsplash.com/photo-1563729784474-d77dbb933a9e?w=600&q=80' }}"
                     alt="{{ $product->name }}">
                <div class="product-info">
                    <h3>{{ $product->name }}</h3>
                    <p>{{ $product->description }}</p>
                    <span class="product-price">Rp {{ number_format($product->price, 0, ',', '.') }}</span>
                    <span class="product-order">Lihat Detail →</span>
                </div>
            </a>
            @empty
            <p class="empty-state">Belum ada produk di kategori ini.</p>
            @endforelse
        </div>
    </div>
</section>
@empty
<section style="padding: 80px 24px; text-align: center; color: var(--gray);">
    <p>Belum ada kategori produk.</p>
</section>
@endforelse

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
                <li>muhammadzidane253@gmail.com</li>
                <li>Payakumbuh, Sumatera Barat</li>
            </ul>
        </div>
    </div>
</footer>

</body>
</html>
