<!DOCTYPE html>
<html lang="id">
<head>
    <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}" />
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Jagoan Kue - Katalog Produk</title>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700;800&family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    <style>
        /* PAGE HEADER */
        .page-header { background-color: var(--cream); padding: 48px 24px 32px; text-align: center; }
        .page-header h1 { font-family: 'Playfair Display', serif; font-size: 36px; font-weight: 800; color: var(--text-dark); }

        /* CATEGORY SECTION */
        .category-section { padding: 48px 24px; }
        .category-section:nth-child(even) { background-color: var(--cream); }
        .category-section:nth-child(odd) { background-color: var(--white); }
        .category-inner { max-width: 1100px; margin: 0 auto; }
        .category-title { font-family: 'Playfair Display', serif; font-size: 28px; font-weight: 700; text-align: center; color: var(--text-dark); margin-bottom: 32px; }

        /* PRODUCT GRID */
        .product-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 24px; }
        .product-card { background: var(--white); border-radius: 16px; overflow: hidden; box-shadow: 0 2px 12px rgba(0,0,0,0.07); transition: transform 0.2s, box-shadow 0.2s; display: block; color: inherit; }
        .product-card:hover { transform: translateY(-4px); box-shadow: 0 8px 24px rgba(0,0,0,0.12); }
        .product-card img { width: 100%; height: 200px; object-fit: cover; display: block; transition: transform 0.3s; }
        .product-card:hover img { transform: scale(1.05); }
        .product-info { padding: 20px; }
        .product-info h3 { font-size: 16px; font-weight: 700; color: var(--text-dark); margin-bottom: 8px; }
        .product-info p { font-size: 13px; color: var(--gray); line-height: 1.6; margin-bottom: 16px; display: -webkit-box; -webkit-line-clamp: 3; -webkit-box-orient: vertical; overflow: hidden; }
        .product-price { font-size: 15px; font-weight: 700; color: var(--pink); margin-bottom: 10px; display: block; }
        .product-order { font-size: 13px; font-weight: 600; color: var(--text-dark); display: inline-flex; align-items: center; gap: 4px; transition: color 0.2s; }
        .product-order:hover { color: var(--pink); }
        .empty-state { grid-column: span 3; text-align: center; color: var(--gray); padding: 40px; font-size: 14px; }

        @media (max-width: 768px) { .product-grid { grid-template-columns: 1fr; } }
    </style></head>
<body>
@include('partials.navbar')

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

@include('partials.footer')

<script src="{{ asset('js/app.js') }}" defer></script>
</body>
</html>
