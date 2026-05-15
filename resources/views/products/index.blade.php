<!DOCTYPE html>
<html lang="id">
<head>
    <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}" />
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Jagoan Kue - Katalog Produk</title>
    <meta name="description" content="Temukan berbagai kue lezat pilihan di Jagoan Kue. Kue ulang tahun, pernikahan, dan custom cake berkualitas.">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700;800&family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    <style>
        .page-header { background-color: var(--cream); padding: 48px 24px 32px; text-align: center; }
        .page-header h1 { font-family: 'Playfair Display', serif; font-size: 36px; font-weight: 800; color: var(--text-dark); }

        /* FILTER BAR */
        .filter-bar { background: var(--cream); border-bottom: 1px solid #EDE0D4; padding: 16px 24px; position: sticky; top: 70px; z-index: 10; }
        .filter-inner { max-width: 1100px; margin: 0 auto; display: flex; flex-wrap: wrap; gap: 12px; align-items: center; }
        .search-wrap { position: relative; flex: 1; min-width: 200px; }
        .search-wrap input { width: 100%; border: 1.5px solid #D1C0B8; border-radius: 8px; padding: 9px 36px 9px 14px; font-size: 13px; font-family: 'Plus Jakarta Sans', sans-serif; outline: none; }
        .search-wrap input:focus { border-color: var(--pink); }
        .search-wrap i { position: absolute; right: 12px; top: 50%; transform: translateY(-50%); color: var(--gray); font-size: 13px; }
        .filter-select { border: 1.5px solid #D1C0B8; border-radius: 8px; padding: 9px 14px; font-size: 13px; font-family: 'Plus Jakarta Sans', sans-serif; outline: none; background: var(--white); cursor: pointer; }
        .filter-select:focus { border-color: var(--pink); }
        .price-wrap { display: flex; align-items: center; gap: 6px; }
        .price-wrap input { border: 1.5px solid #D1C0B8; border-radius: 8px; padding: 9px 10px; font-size: 13px; width: 110px; font-family: 'Plus Jakarta Sans', sans-serif; outline: none; }
        .price-wrap input:focus { border-color: var(--pink); }
        .price-wrap span { font-size: 12px; color: var(--gray); }
        .btn-filter { background: var(--brown-dark); color: white; border: none; border-radius: 8px; padding: 9px 18px; font-size: 13px; font-weight: 600; cursor: pointer; font-family: 'Plus Jakarta Sans', sans-serif; }
        .btn-reset { background: transparent; color: var(--pink); border: 1.5px solid var(--pink); border-radius: 8px; padding: 9px 14px; font-size: 13px; font-weight: 600; cursor: pointer; font-family: 'Plus Jakarta Sans', sans-serif; text-decoration: none; display: inline-flex; align-items: center; }
        .active-badge { background: var(--pink); color: white; font-size: 11px; font-weight: 700; border-radius: 100px; padding: 2px 8px; margin-left: 6px; }

        /* RESULT SECTION */
        .result-section { padding: 32px 24px 60px; background: var(--cream); min-height: 300px; }
        .result-inner { max-width: 1100px; margin: 0 auto; }
        .result-header { display: flex; align-items: center; justify-content: space-between; margin-bottom: 24px; }
        .result-title { font-family: 'Playfair Display', serif; font-size: 22px; font-weight: 700; color: var(--text-dark); }
        .result-count { font-size: 13px; color: var(--gray); }

        /* PRODUCT GRID */
        .product-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 24px; }
        .product-card { background: #FFFFFF; border-radius: 16px; overflow: hidden; box-shadow: 0 2px 12px rgba(0,0,0,0.07); transition: transform 0.2s, box-shadow 0.2s; display: block; color: inherit; position: relative; }
        .product-card:hover { transform: translateY(-4px); box-shadow: 0 8px 24px rgba(0,0,0,0.12); }
        .product-card img { width: 100%; height: 200px; object-fit: cover; display: block; transition: transform 0.3s; }
        .product-card:hover img { transform: scale(1.05); }
        .product-info { padding: 20px; }
        .product-info h3 { font-size: 16px; font-weight: 700; color: var(--text-dark); margin-bottom: 8px; }
        .product-info p { font-size: 13px; color: var(--gray); line-height: 1.6; margin-bottom: 16px; display: -webkit-box; -webkit-line-clamp: 3; -webkit-box-orient: vertical; overflow: hidden; }
        .product-price { font-size: 15px; font-weight: 700; color: var(--pink); margin-bottom: 10px; display: block; }
        .product-order { font-size: 13px; font-weight: 600; color: var(--text-dark); display: inline-flex; align-items: center; gap: 4px; transition: color 0.2s; }
        .product-order:hover { color: var(--pink); }

        /* BADGE */
        .product-badge { position: absolute; top: 12px; left: 12px; font-size: 11px; font-weight: 700; padding: 4px 10px; border-radius: 100px; z-index: 2; }
        .badge-best-seller { background: #F59E0B; color: white; }
        .badge-new { background: #10B981; color: white; }
        .badge-sale { background: var(--pink); color: white; }
        .badge-habis { background: #6B7280; color: white; }

        /* UNAVAILABLE */
        .product-card.unavailable img { filter: grayscale(60%); }
        .product-card.unavailable .btn-order { opacity: 0.5; pointer-events: none; }

        /* EMPTY */
        .empty-state { text-align: center; padding: 60px 24px; color: var(--gray); }
        .empty-state i { font-size: 40px; margin-bottom: 12px; display: block; }

        /* PAGINATION */
        .pagination-wrap { display: flex; justify-content: center; margin-top: 32px; gap: 6px; }
        .pagination-wrap a, .pagination-wrap span { border: 1.5px solid #D1C0B8; border-radius: 8px; padding: 8px 14px; font-size: 13px; color: var(--text-dark); text-decoration: none; transition: all 0.2s; }
        .pagination-wrap a:hover { border-color: var(--pink); color: var(--pink); }
        .pagination-wrap span.active-page { background: var(--pink); color: white; border-color: var(--pink); }

        @media (max-width: 900px) { .product-grid { grid-template-columns: repeat(2, 1fr); } }
        @media (max-width: 540px) {
            .product-grid { grid-template-columns: 1fr; }
            .filter-inner { flex-direction: column; align-items: stretch; }
            .price-wrap { flex-wrap: wrap; }
        }
    </style>
</head>
<body>
@include('partials.navbar')

<div class="page-header">
    <h1>Produk Kami</h1>
</div>

{{-- FILTER BAR --}}
<div class="filter-bar">
    <form method="GET" action="{{ route('products.index') }}" class="filter-inner" id="filterForm">
        <div class="search-wrap">
            <input type="text" name="search" placeholder="Cari produk kue..." value="{{ request('search') }}">
            <i class="fas fa-search"></i>
        </div>

        <select name="category" class="filter-select" onchange="document.getElementById('filterForm').submit()">
            <option value="">Semua Kategori</option>
            @foreach($categories as $cat)
            <option value="{{ $cat->slug }}" @selected(request('category') === $cat->slug)>{{ $cat->name }}</option>
            @endforeach
        </select>

        <select name="sort" class="filter-select" onchange="document.getElementById('filterForm').submit()">
            <option value="">Urutkan</option>
            <option value="newest"     @selected(request('sort') === 'newest')>Terbaru</option>
            <option value="price_asc"  @selected(request('sort') === 'price_asc')>Harga: Terendah</option>
            <option value="price_desc" @selected(request('sort') === 'price_desc')>Harga: Tertinggi</option>
        </select>

        <div class="price-wrap">
            <span>Rp</span>
            <input type="number" name="min_price" placeholder="Min" value="{{ request('min_price') }}" min="0" step="1000">
            <span>–</span>
            <input type="number" name="max_price" placeholder="Max" value="{{ request('max_price') }}" min="0" step="1000">
        </div>

        <button type="submit" class="btn-filter"><i class="fas fa-filter"></i> Terapkan</button>

        @if($isFiltered)
        <a href="{{ route('products.index') }}" class="btn-reset"><i class="fas fa-times"></i> Reset</a>
        @endif
    </form>
</div>

{{-- RESULT --}}
<section class="result-section">
    <div class="result-inner">
        <div class="result-header">
            <h2 class="result-title">
                @if(request('search'))
                    Hasil pencarian: "{{ request('search') }}"
                @elseif(request('category'))
                    {{ $categories->firstWhere('slug', request('category'))?->name ?? 'Kategori' }}
                @else
                    Semua Produk
                @endif
            </h2>
            <span class="result-count">{{ $products->total() }} produk ditemukan</span>
        </div>

        @if($products->isEmpty())
        <div class="empty-state">
            <i class="fas fa-search"></i>
            <p style="font-size:15px;font-weight:600;color:var(--text-dark);margin-bottom:8px;">Produk tidak ditemukan</p>
            <p>Coba kata kunci lain atau <a href="{{ route('products.index') }}" style="color:var(--pink);">lihat semua produk</a></p>
        </div>
        @else
        <div class="product-grid">
            @foreach($products as $product)
            <a href="{{ route('products.show', $product) }}" class="product-card {{ !$product->is_available ? 'unavailable' : '' }}">
                @if(!$product->is_available)
                    <span class="product-badge badge-habis">Habis</span>
                @elseif($product->badge === 'best_seller')
                    <span class="product-badge badge-best-seller">Best Seller</span>
                @elseif($product->badge === 'new')
                    <span class="product-badge badge-new">Baru</span>
                @elseif($product->badge === 'sale')
                    <span class="product-badge badge-sale">Diskon</span>
                @endif

                <img src="{{ $product->image ? asset('storage/' . $product->image) : 'https://images.unsplash.com/photo-1563729784474-d77dbb933a9e?w=600&q=80' }}"
                     alt="{{ $product->name }}" loading="lazy">
                <div class="product-info">
                    <h3>{{ $product->name }}</h3>
                    <p>{{ $product->description }}</p>
                    <span class="product-price">Rp {{ number_format($product->price, 0, ',', '.') }}</span>
                    <span class="product-order">Lihat Detail →</span>
                </div>
            </a>
            @endforeach
        </div>

        {{-- PAGINATION --}}
        @if($products->hasPages())
        <div class="pagination-wrap">
            @if($products->onFirstPage())
                <span>‹</span>
            @else
                <a href="{{ $products->previousPageUrl() }}">‹</a>
            @endif

            @foreach($products->getUrlRange(1, $products->lastPage()) as $page => $url)
                @if($page == $products->currentPage())
                    <span class="active-page">{{ $page }}</span>
                @else
                    <a href="{{ $url }}">{{ $page }}</a>
                @endif
            @endforeach

            @if($products->hasMorePages())
                <a href="{{ $products->nextPageUrl() }}">›</a>
            @else
                <span>›</span>
            @endif
        </div>
        @endif
        @endif
    </div>
</section>

@include('partials.footer')
</body>
</html>
