<!DOCTYPE html>
<html lang="id">
<head>
    <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}" />
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Jagoan Kue - Kue Lezat Dikirim ke Pintumu</title>
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

        /* HERO */
        .hero { background-color: var(--cream); padding: 80px 24px; }
        .hero-inner { max-width: 1100px; margin: 0 auto; display: flex; align-items: center; justify-content: space-between; gap: 40px; }
        .hero-text { flex: 1; max-width: 520px; }
        .hero-title { font-family: 'Playfair Display', serif; font-size: 52px; font-weight: 800; line-height: 1.2; color: var(--text-dark); margin-bottom: 16px; }
        .hero-title span { color: var(--pink); }
        .hero-subtitle { font-size: 15px; color: var(--gray); line-height: 1.7; margin-bottom: 36px; }
        .hero-buttons { display: flex; gap: 16px; }
        .btn-primary { background-color: var(--brown-dark); color: white; padding: 12px 24px; border-radius: 8px; font-size: 14px; font-weight: 600; transition: opacity 0.2s; }
        .btn-primary:hover { opacity: 0.85; }
        .btn-secondary { border: 2px solid var(--brown-dark); color: var(--brown-dark); padding: 12px 24px; border-radius: 8px; font-size: 14px; font-weight: 600; transition: background 0.2s; }
        .btn-secondary:hover { background: var(--cream-dark); }
        .hero-image { flex: 1; display: flex; justify-content: center; }
        .hero-image img { width: 380px; height: 380px; object-fit: cover; border-radius: 20px; box-shadow: 0 20px 60px rgba(0,0,0,0.15); }

        /* SECTIONS */
        .section { padding: 72px 24px; }
        .section-white { background: var(--white); }
        .section-cream { background-color: var(--cream); }
        .section-inner { max-width: 1100px; margin: 0 auto; }
        .section-title { font-family: 'Playfair Display', serif; font-size: 32px; font-weight: 700; text-align: center; color: var(--text-dark); margin-bottom: 8px; }
        .section-subtitle { text-align: center; color: var(--gray); font-size: 14px; margin-bottom: 48px; }

        /* KATEGORI */
        .category-grid { display: grid; grid-template-columns: repeat(2, 1fr); gap: 24px; }
        .category-card { border-radius: 16px; overflow: hidden; box-shadow: 0 2px 12px rgba(0,0,0,0.07); transition: transform 0.2s, box-shadow 0.2s; display: block; }
        .category-card:hover { transform: translateY(-4px); box-shadow: 0 8px 24px rgba(0,0,0,0.12); }
        .category-card img { width: 100%; height: 200px; object-fit: cover; display: block; transition: transform 0.3s; }
        .category-card:hover img { transform: scale(1.05); }
        .category-info { background-color: var(--cream-dark); padding: 16px; }
        .category-info h3 { font-size: 15px; font-weight: 600; color: var(--text-dark); margin-bottom: 2px; }
        .category-info p { font-size: 13px; color: var(--gray); }

        /* PRODUK */
        .product-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 24px; }
        .product-card { background: var(--white); border-radius: 16px; overflow: hidden; box-shadow: 0 2px 12px rgba(0,0,0,0.07); transition: transform 0.2s, box-shadow 0.2s; }
        .product-card:hover { transform: translateY(-4px); box-shadow: 0 8px 24px rgba(0,0,0,0.12); }
        .product-card img { width: 100%; height: 200px; object-fit: cover; display: block; transition: transform 0.3s; }
        .product-card:hover img { transform: scale(1.05); }
        .product-info { padding: 20px; }
        .product-info h3 { font-size: 16px; font-weight: 700; color: var(--text-dark); margin-bottom: 6px; }
        .product-info p { font-size: 13px; color: var(--gray); line-height: 1.6; margin-bottom: 16px; overflow: hidden; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; }
        .product-footer { display: flex; align-items: center; justify-content: space-between; }
        .product-price { font-size: 15px; font-weight: 700; color: var(--pink); }
        .product-order { font-size: 13px; font-weight: 600; color: var(--brown-dark); }
        .product-order:hover { opacity: 0.7; }

        /* TESTIMONI */
        .testimoni-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 24px; }
        .testimoni-card { background-color: var(--cream); border-radius: 16px; padding: 24px; border: 1px solid #EDE5D0; }
        .testimoni-text { font-size: 14px; color: #444; line-height: 1.7; margin-bottom: 20px; }
        .testimoni-author { display: flex; align-items: center; gap: 12px; }
        .testimoni-avatar { width: 38px; height: 38px; border-radius: 50%; background-color: var(--pink); color: white; font-size: 14px; font-weight: 700; display: flex; align-items: center; justify-content: center; flex-shrink: 0; }
        .testimoni-name { font-size: 13px; font-weight: 700; color: var(--text-dark); }
        .testimoni-role { font-size: 12px; color: var(--gray); margin-top: 2px; }

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
            .hero-inner { flex-direction: column; text-align: center; }
            .hero-title { font-size: 36px; }
            .hero-buttons { justify-content: center; }
            .hero-image img { width: 280px; height: 280px; }
            .category-grid { grid-template-columns: 1fr; }
            .product-grid { grid-template-columns: 1fr; }
            .testimoni-grid { grid-template-columns: 1fr; }
            .footer-inner { grid-template-columns: 1fr 1fr; }
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

{{-- HERO --}}
<section class="hero">
    <div class="hero-inner">
        <div class="hero-text">
            <h1 class="hero-title">
                Kue Lezat, Dikirim<br>
                Hangat ke <span>Pintumu</span>
            </h1>
            <p class="hero-subtitle">
                Menyediakan Bermacam-macam kue yang<br>dibuat oleh cinta
            </p>
            <div class="hero-buttons">
                <a href="/orders" class="btn-primary">Pemesanan</a>
                <a href="/products" class="btn-secondary">Katalog</a>
            </div>
        </div>
        <div class="hero-image">
            <img src="https://images.unsplash.com/photo-1578985545062-69928b1d9587?w=600&q=80" alt="Kue Lezat">
        </div>
    </div>
</section>

{{-- KATEGORI --}}
<section class="section section-white">
    <div class="section-inner">
        <h2 class="section-title">Jelajahi Kategori</h2>
        <div class="category-grid" style="margin-top: 40px;">
            @forelse($categories as $category)
            <a href="/products?category={{ $category->slug }}" class="category-card">
                <img src="https://images.unsplash.com/photo-1563729784474-d77dbb933a9e?w=600&q=80" alt="{{ $category->name }}">
                <div class="category-info">
                    <h3>{{ $category->name }}</h3>
                    <p>{{ $category->products_count ?? 0 }} Produk</p>
                </div>
            </a>
            @empty
            <p style="grid-column: span 2; text-align: center; color: var(--gray);">Belum ada kategori.</p>
            @endforelse
        </div>
    </div>
</section>

{{-- PRODUK UNGGULAN --}}
<section class="section section-cream">
    <div class="section-inner">
        <h2 class="section-title">Produk Unggulan</h2>
        <div class="product-grid" style="margin-top: 40px;">
            @forelse($featuredProducts as $product)
            <div class="product-card">
                <img src="{{ $product->image ? asset('storage/' . $product->image) : 'https://images.unsplash.com/photo-1563729784474-d77dbb933a9e?w=600&q=80' }}"
                     alt="{{ $product->name }}">
                <div class="product-info">
                    <h3>{{ $product->name }}</h3>
                    <p>{{ $product->description }}</p>
                    <div class="product-footer">
                        <span class="product-price">Rp {{ number_format($product->price, 0, ',', '.') }}</span>
                        <a href="/products/{{ $product->slug }}" class="product-order">Pesan Sekarang →</a>
                    </div>
                </div>
            </div>
            @empty
            <p style="grid-column: span 3; text-align: center; color: var(--gray);">Belum ada produk.</p>
            @endforelse
        </div>
    </div>
</section>

{{-- TESTIMONI --}}
<section class="section section-white">
    <div class="section-inner">
        <h2 class="section-title">Testimoni</h2>
        <p class="section-subtitle">Yang orang-orang rasakan.</p>
        <div class="testimoni-grid">
                @forelse($testimonials as $t)
            <div class="testimoni-card">
                <p class="testimoni-text">"{{ $t->text }}"</p>
                <div class="testimoni-author">
                    <div class="testimoni-avatar">{{ strtoupper(substr($t->name, 0, 1)) }}</div>
                    <div>
                        <p class="testimoni-name">{{ $t->name }}</p>
                        <p class="testimoni-role">{{ $t->role }}</p>
                    </div>
                </div>
            </div>
            @empty
            <p style="grid-column: span 3; text-align: center; color: var(--gray);">Belum ada testimoni.</p>
            @endforelse
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
                <li>muhammadzidane253@gmail.com</li>
                <li>Payakumbuh, Sumatera Barat</li>
            </ul>
        </div>
    </div>
</footer>

</body>
</html>