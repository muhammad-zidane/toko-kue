<!DOCTYPE html>
<html lang="id">
<head>
    <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}" />
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Jagoan Kue — Kue Lezat Dikirim ke Pintumu</title>
    <meta name="description" content="Jagoan Kue menyediakan berbagai kue lezat: kue ulang tahun, pernikahan, kering, dan custom cake. Pesan sekarang, kirim ke pintumu!">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700;800&family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    <style>
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
        .category-card { border-radius: 16px; overflow: hidden; box-shadow: 0 2px 12px rgba(0,0,0,0.07); display: block; }
        .category-card img { width: 100%; height: 200px; object-fit: cover; display: block; }
        .category-info { background-color: var(--cream-dark); padding: 16px; }
        .category-info h3 { font-size: 15px; font-weight: 600; color: var(--text-dark); margin-bottom: 2px; }
        .category-info p { font-size: 13px; color: var(--gray); }

        /* PRODUK */
        .product-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 24px; }
        .product-card { background: var(--white); border-radius: 16px; overflow: hidden; box-shadow: 0 2px 12px rgba(0,0,0,0.07); }
        .product-card img { width: 100%; height: 200px; object-fit: cover; display: block; }
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

        /* RESPONSIVE */
        @media (max-width: 768px) {
            .hero-inner { flex-direction: column; text-align: center; }
            .hero-title { font-size: 36px; }
            .hero-buttons { justify-content: center; }
            .hero-image img { width: 280px; height: 280px; }
            .category-grid { grid-template-columns: 1fr; }
            .product-grid { grid-template-columns: 1fr; }
            .testimoni-grid { grid-template-columns: 1fr; }
        }
    </style>
</head>
<body class="bg-white">
        {{-- NAVBAR --}}
        @include('partials.navbar')

        {{-- BANNER SLIDESHOW (dari database) --}}
        @if($banners->isNotEmpty())
        <div id="bannerSlider" style="position:relative;overflow:hidden;background:var(--cream);">
            @foreach($banners as $i => $banner)
            <div class="banner-slide" style="display:{{ $i === 0 ? 'block' : 'none' }};position:relative;">
                <a href="{{ $banner->link ?? '#' }}">
                    <img src="{{ asset('storage/' . $banner->image) }}"
                         alt="{{ $banner->title }}"
                         style="width:100%;max-height:480px;object-fit:cover;display:block;" loading="lazy">
                </a>
                @if($banner->title)
                <div style="position:absolute;bottom:0;left:0;right:0;background:linear-gradient(transparent,rgba(0,0,0,0.55));padding:24px 32px;">
                    <h2 style="color:white;font-family:'Playfair Display',serif;font-size:28px;font-weight:800;margin-bottom:4px;">{{ $banner->title }}</h2>
                    @if($banner->subtitle)<p style="color:rgba(255,255,255,0.85);font-size:14px;">{{ $banner->subtitle }}</p>@endif
                </div>
                @endif
            </div>
            @endforeach

            @if($banners->count() > 1)
            <button onclick="slideBanner(-1)" style="position:absolute;left:12px;top:50%;transform:translateY(-50%);background:rgba(255,255,255,0.8);border:none;border-radius:50%;width:36px;height:36px;font-size:16px;cursor:pointer;">‹</button>
            <button onclick="slideBanner(1)"  style="position:absolute;right:12px;top:50%;transform:translateY(-50%);background:rgba(255,255,255,0.8);border:none;border-radius:50%;width:36px;height:36px;font-size:16px;cursor:pointer;">›</button>
            <div style="position:absolute;bottom:10px;left:50%;transform:translateX(-50%);display:flex;gap:6px;" id="bannerDots">
                @foreach($banners as $i => $banner)
                <div class="banner-dot" onclick="goToBanner({{ $i }})"
                     style="width:8px;height:8px;border-radius:50%;background:{{ $i === 0 ? 'white' : 'rgba(255,255,255,0.5)' }};cursor:pointer;transition:background 0.2s;"></div>
                @endforeach
            </div>
            @endif
        </div>
        <script>
        let bannerIdx = 0;
        const slides = document.querySelectorAll('.banner-slide');
        const dots   = document.querySelectorAll('.banner-dot');
        function goToBanner(n) {
            slides[bannerIdx].style.display = 'none';
            dots[bannerIdx].style.background = 'rgba(255,255,255,0.5)';
            bannerIdx = (n + slides.length) % slides.length;
            slides[bannerIdx].style.display = 'block';
            dots[bannerIdx].style.background = 'white';
        }
        function slideBanner(dir) { goToBanner(bannerIdx + dir); }
        setInterval(() => slideBanner(1), 5000);
        </script>
        @endif

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
                        <a href="/products" class="btn-primary">Katalog</a>
                        <a href="/orders" class="btn-secondary">Pesanan Saya</a>
                    </div>
                </div>
                <div class="hero-image">
                    <img src="https://images.unsplash.com/photo-1578985545062-69928b1d9587?w=600&q=80" alt="Kue Lezat" loading="lazy">
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
                        <p class="testimoni-text">"{{ $t->comment ?? '-' }}"</p>
                        <div class="testimoni-author">
                            <div class="testimoni-avatar">{{ strtoupper(substr($t->user->name ?? 'U', 0, 1)) }}</div>
                            <div>
                                <p class="testimoni-name">{{ $t->user->name ?? 'Pelanggan' }}</p>
                                <p class="testimoni-role">
                                    {{ $t->product ? 'Ulasan untuk ' . $t->product->name : 'Ulasan Produk' }}
                                </p>
                            </div>
                        </div>
                    </div>
                    @empty
                    <p style="grid-column: span 3; text-align: center; color: var(--gray);">Belum ada testimoni.</p>
                    @endforelse
                </div>
            </div>
        </section>


@include('partials.footer')
<script src="{{ asset('js/app.js') }}" defer></script>
</body>
</html>