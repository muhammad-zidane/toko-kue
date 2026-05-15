<!DOCTYPE html>
<html lang="id">
<head>
    <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}" />
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $product->name }} — Jagoan Kue</title>
    <meta name="description" content="{{ Str::limit(strip_tags($product->description ?? $product->name . ' tersedia di Jagoan Kue.'), 155) }}">
    <meta property="og:title" content="{{ $product->name }} — Jagoan Kue">
    <meta property="og:description" content="{{ Str::limit(strip_tags($product->description ?? ''), 155) }}">
    @if($product->image)<meta property="og:image" content="{{ asset('storage/' . $product->image) }}">@endif
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700;800&family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    <style>
        .product-page { background-color: var(--cream); padding: 60px 24px; min-height: 70vh; }
        .product-inner { max-width: 1100px; margin: 0 auto; display: grid; grid-template-columns: 1fr 1.2fr 0.8fr; gap: 40px; align-items: start; }
        .product-image img { width: 100%; aspect-ratio: 1; object-fit: cover; border-radius: 16px; box-shadow: 0 8px 32px rgba(0,0,0,0.1); }
        .product-info h1 { font-size: 22px; font-weight: 700; color: var(--text-dark); margin-bottom: 6px; }
        .product-sold { font-size: 12px; color: var(--gray); margin-bottom: 16px; }
        .product-price { font-size: 22px; font-weight: 700; color: var(--text-dark); margin-bottom: 16px; padding-bottom: 16px; border-bottom: 1.5px solid #E5B8C2; }
        .product-detail-label { font-size: 14px; font-weight: 600; color: var(--text-dark); margin-bottom: 10px; }
        .product-description { font-size: 14px; color: var(--gray); line-height: 1.8; }
        .product-widget { background: var(--white); border-radius: 12px; border: 1px solid #E5D5C5; padding: 20px; }
        .widget-title { font-size: 14px; font-weight: 700; color: var(--text-dark); margin-bottom: 16px; }
        .quantity-row { display: flex; align-items: center; gap: 12px; margin-bottom: 16px; }
        .quantity-control { display: flex; align-items: center; border: 1px solid #D1C4C0; border-radius: 6px; overflow: hidden; }
        .qty-btn { background: none; border: none; padding: 6px 12px; font-size: 16px; cursor: pointer; color: var(--text-dark); font-weight: 600; transition: background 0.2s; }
        .qty-btn:hover { background: var(--cream); }
        .qty-input { width: 40px; text-align: center; border: none; border-left: 1px solid #D1C4C0; border-right: 1px solid #D1C4C0; padding: 6px 0; font-size: 14px; font-weight: 600; font-family: 'Plus Jakarta Sans', sans-serif; outline: none; }
        .stock-info { font-size: 13px; color: var(--gray); }
        .stock-info span { font-weight: 700; color: var(--text-dark); }
        .subtotal-row { display: flex; justify-content: space-between; align-items: center; padding: 12px 0; border-top: 1px solid #F0E8E0; margin-bottom: 12px; }
        .subtotal-label { font-size: 13px; color: var(--gray); }
        .subtotal-value { font-size: 15px; font-weight: 700; color: var(--text-dark); }
        .catatan-label { font-size: 13px; font-weight: 600; color: var(--text-dark); margin-bottom: 6px; display: block; }
        .catatan-input { width: 100%; border: 1px solid #D1C4C0; border-radius: 8px; padding: 8px 12px; font-size: 13px; font-family: 'Plus Jakarta Sans', sans-serif; resize: none; outline: none; margin-bottom: 16px; background: var(--cream); }
        .catatan-input:focus { border-color: var(--pink); }
        .btn-add-cart { width: 100%; background-color: var(--pink); color: white; border: none; padding: 12px; border-radius: 8px; font-size: 14px; font-weight: 700; cursor: pointer; font-family: 'Plus Jakarta Sans', sans-serif; transition: opacity 0.2s; }
        .btn-add-cart:hover { opacity: 0.85; }
        .reviews-section { max-width: 1100px; margin: 26px auto 0; padding: 0 24px 60px; }
        .reviews-title { font-family: 'Playfair Display', serif; font-size: 28px; margin-bottom: 14px; }
        .review-card { background: var(--white); border: 1px solid #EDE0D4; border-radius: 14px; padding: 14px; margin-bottom: 12px; }
        .review-top { display: flex; justify-content: space-between; align-items: center; margin-bottom: 8px; gap: 12px; }
        .review-user { font-size: 14px; font-weight: 700; }
        .review-date { font-size: 12px; color: var(--gray); }
        .review-stars { color: #F59E0B; font-size: 18px; }
        .review-comment { font-size: 14px; line-height: 1.7; margin-bottom: 10px; }
        .review-images { display: flex; flex-wrap: wrap; gap: 8px; }
        .review-images img { width: 82px; height: 82px; object-fit: cover; border-radius: 8px; border: 1px solid #EDE0D4; }
        @media (max-width: 768px) { .product-inner { grid-template-columns: 1fr; } }
    </style></head>
<body>
@include('partials.navbar')

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
            <textarea id="note-input" class="catatan-input" rows="3" placeholder="Contoh: Tulisan di kue, warna, ukuran..."></textarea>

            <div class="subtotal-row">
                <span class="subtotal-label">Subtotal</span>
                <span class="subtotal-value" id="subtotal">Rp{{ number_format($product->price, 0, ',', '.') }}</span>
            </div>

        @auth
            <form id="add-to-cart-form" action="/cart/add" method="POST">
                @csrf
                    <input type="hidden" name="product_id" value="{{ $product->id }}">
                    <input type="hidden" name="quantity" id="qty-hidden" value="1">
                    <input type="hidden" name="note" id="note-hidden" value="">
                    <button type="submit" class="btn-add-cart">+ Keranjang</button>
                </form>
        @else
            <a href="/login">
                <button class="btn-add-cart">+ Keranjang</button>
            </a>
        @endauth
        </div>

    </div>

    <div class="reviews-section">
        <h2 class="reviews-title">Ulasan</h2>
        @forelse($product->reviews as $review)
            <div class="review-card">
                <div class="review-top">
                    <div>
                        <p class="review-user">{{ $review->user->name ?? 'Pelanggan' }}</p>
                        <p class="review-date">{{ $review->created_at?->format('d M Y, H:i') }}</p>
                    </div>
                    <div class="review-stars">{{ str_repeat('★', (int) $review->rating) }}{{ str_repeat('☆', 5 - (int) $review->rating) }}</div>
                </div>
                <p class="review-comment">{{ $review->comment }}</p>
                @if($review->images->isNotEmpty())
                    <div class="review-images">
                        @foreach($review->images as $image)
                            <img src="{{ asset('storage/' . $image->path) }}" alt="Gambar ulasan produk">
                        @endforeach
                    </div>
                @endif
            </div>
        @empty
            <p style="font-size:14px; color:var(--gray);">Belum ada ulasan untuk produk ini.</p>
        @endforelse
    </div>
</section>

@include('partials.footer')

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

    const noteInput = document.getElementById('note-input');
    const noteHidden = document.getElementById('note-hidden');
    if (noteInput && noteHidden) {
        noteInput.addEventListener('input', function() {
            noteHidden.value = this.value;
        });
    }

</script>

<script src="{{ asset('js/app.js') }}" defer></script>
</body>
</html>