@extends('layouts.main')

@section('title', 'Jagoan Kue - Katalog Produk')

@section('content')
<div class="bg-cream-warm py-12 px-6">
    <div class="max-w-[1140px] mx-auto">
        <h1 class="font-heading text-4xl font-bold text-brown-dark">Produk Kami</h1>
    </div>
</div>

{{-- FILTER BAR --}}
<div class="bg-white border-b border-cream-border sticky top-20 z-10 py-4 px-6">
    <form method="GET" action="{{ route('products.index') }}" class="max-w-[1140px] mx-auto flex flex-wrap gap-3 items-center" id="filterForm">
        <div class="relative flex-1 min-w-[200px]">
            <input type="text" name="search" class="w-full input-field pl-10" placeholder="Cari produk kue..." value="{{ request('search') }}">
            <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-text-muted"></i>
        </div>

        <select name="category" class="input-field cursor-pointer w-full md:w-auto" onchange="document.getElementById('filterForm').submit()">
            <option value="">Semua Kategori</option>
            @foreach($categories as $cat)
            <option value="{{ $cat->slug }}" @selected(request('category') === $cat->slug)>{{ $cat->name }}</option>
            @endforeach
        </select>

        <select name="sort" class="input-field cursor-pointer w-full md:w-auto" onchange="document.getElementById('filterForm').submit()">
            <option value="">Urutkan</option>
            <option value="newest"     @selected(request('sort') === 'newest')>Terbaru</option>
            <option value="price_asc"  @selected(request('sort') === 'price_asc')>Harga: Terendah</option>
            <option value="price_desc" @selected(request('sort') === 'price_desc')>Harga: Tertinggi</option>
        </select>

        <div class="flex items-center gap-2 w-full md:w-auto">
            <span class="text-xs font-semibold text-text-secondary">Rp</span>
            <input type="number" name="min_price" class="input-field py-3 px-3 w-28 text-sm" placeholder="Min" value="{{ request('min_price') }}" min="0" step="1000">
            <span class="text-xs font-semibold text-text-secondary">–</span>
            <input type="number" name="max_price" class="input-field py-3 px-3 w-28 text-sm" placeholder="Max" value="{{ request('max_price') }}" min="0" step="1000">
        </div>

        <button type="submit" class="btn-primary text-xs px-4 py-2"><i class="fas fa-filter mr-1"></i> Terapkan</button>

        @if($isFiltered)
        <a href="{{ route('products.index') }}" class="btn-secondary text-xs px-4 py-2"><i class="fas fa-times mr-1"></i> Reset</a>
        @endif
    </form>
</div>

{{-- RESULT --}}
<section class="py-8 bg-cream min-h-[300px]">
    <div class="max-w-[1140px] mx-auto px-6">
        <div class="flex items-center justify-between mb-6">
            <h2 class="font-heading text-2xl font-bold text-brown-dark">
                @if(request('search'))
                    Hasil pencarian: "{{ request('search') }}"
                @elseif(request('category'))
                    {{ $categories->firstWhere('slug', request('category'))?->name ?? 'Kategori' }}
                @else
                    Semua Produk
                @endif
            </h2>
            <span class="text-sm text-text-secondary">{{ $products->total() }} produk ditemukan</span>
        </div>

        @if($products->isEmpty())
        <div class="text-center py-16 text-text-secondary">
            <i class="fas fa-search text-5xl mb-4 text-cream-dark"></i>
            <p class="text-lg font-bold text-brown-dark mb-2">Produk tidak ditemukan</p>
            <p>Coba kata kunci lain atau <a href="{{ route('products.index') }}" class="text-primary hover:underline font-semibold">lihat semua produk</a></p>
        </div>
        @else
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($products as $product)
            <a href="{{ route('products.show', $product) }}" class="card card-hover relative overflow-hidden group block {{ !$product->is_available ? 'unavailable' : '' }}">
                @if(!$product->is_available)
                    <span class="absolute top-3 left-3 text-[11px] font-bold px-2.5 py-1 rounded-full bg-gray-100 text-gray-500 z-10">Habis</span>
                @elseif($product->badge === 'best_seller')
                    <span class="absolute top-3 left-3 text-[11px] font-bold px-2.5 py-1 rounded-full bg-amber-100 text-amber-700 z-10">Best Seller</span>
                @elseif($product->badge === 'new')
                    <span class="absolute top-3 left-3 text-[11px] font-bold px-2.5 py-1 rounded-full bg-green-100 text-green-700 z-10">Baru</span>
                @elseif($product->badge === 'sale')
                    <span class="absolute top-3 left-3 text-[11px] font-bold px-2.5 py-1 rounded-full bg-primary-light text-primary z-10">Diskon</span>
                @endif

                <div class="overflow-hidden">
                    <img src="{{ $product->image ? asset('storage/' . $product->image) : 'https://images.unsplash.com/photo-1563729784474-d77dbb933a9e?w=600&q=80' }}"
                         class="w-full h-52 object-cover group-hover:scale-105 transition-transform duration-300 {{ !$product->is_available ? 'grayscale' : '' }}"
                         alt="{{ $product->name }}" loading="lazy">
                </div>

                <div class="p-5 flex flex-col h-[calc(100%-13rem)] justify-between">
                    <div>
                        <h3 class="font-bold text-base text-brown-dark mb-2">{{ $product->name }}</h3>
                        <p class="text-sm text-text-secondary line-clamp-3 mb-4 leading-relaxed">{{ $product->description }}</p>
                    </div>
                    <div>
                        <span class="text-primary font-extrabold text-lg mb-3 block">Rp {{ number_format($product->price, 0, ',', '.') }}</span>
                        <span class="btn-primary text-xs px-4 py-2 w-full justify-center {{ !$product->is_available ? 'opacity-50 pointer-events-none' : '' }}">Lihat Detail →</span>
                    </div>
                </div>
            </a>
            @endforeach
        </div>

        {{-- PAGINATION --}}
        @if($products->hasPages())
        <div class="flex justify-center gap-2 mt-8">
            @if($products->onFirstPage())
                <span class="border border-cream-border rounded-xl px-4 py-2 text-sm text-gray-400 bg-white/50 cursor-default">‹</span>
            @else
                <a href="{{ $products->previousPageUrl() }}" class="border border-cream-border rounded-xl px-4 py-2 text-sm text-brown-dark bg-white hover:border-primary hover:text-primary transition-colors">‹</a>
            @endif

            @foreach($products->getUrlRange(1, $products->lastPage()) as $page => $url)
                @if($page == $products->currentPage())
                    <span class="border border-primary bg-primary text-white rounded-xl px-4 py-2 text-sm font-bold">{{ $page }}</span>
                @else
                    <a href="{{ $url }}" class="border border-cream-border rounded-xl px-4 py-2 text-sm text-brown-dark bg-white hover:border-primary hover:text-primary transition-colors">{{ $page }}</a>
                @endif
            @endforeach

            @if($products->hasMorePages())
                <a href="{{ $products->nextPageUrl() }}" class="border border-cream-border rounded-xl px-4 py-2 text-sm text-brown-dark bg-white hover:border-primary hover:text-primary transition-colors">›</a>
            @else
                <span class="border border-cream-border rounded-xl px-4 py-2 text-sm text-gray-400 bg-white/50 cursor-default">›</span>
            @endif
        </div>
        @endif
        @endif
    </div>
</section>
@endsection
