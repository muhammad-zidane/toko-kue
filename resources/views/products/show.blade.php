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
        .product-info { background: white; border-radius: 16px; border: 1px solid #EDE0D4; padding: 24px; box-shadow: 0 2px 12px rgba(0,0,0,0.05); }
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
        /* Kustomisasi */
        .custom-section { margin-bottom: 16px; padding-bottom: 16px; border-bottom: 1px solid #F0E8E0; }
        .custom-section-title { font-size: 14px; font-weight: 700; color: var(--text-dark); margin-bottom: 12px; }
        .custom-type-block { margin-bottom: 14px; }
        .custom-type-label { font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px; color: var(--gray); margin-bottom: 8px; }
        .custom-options { display: flex; flex-wrap: wrap; gap: 8px; }
        .custom-option-item { display: none; }
        .custom-option-label { display: inline-flex; align-items: center; gap: 6px; padding: 6px 12px; border: 1.5px solid #D1C4C0; border-radius: 8px; font-size: 12px; font-weight: 600; color: var(--text-dark); cursor: pointer; transition: border-color 0.2s, background 0.2s; user-select: none; background: white; }
        .custom-option-item:checked + .custom-option-label { border-color: var(--pink); background: #FFF0F4; color: var(--pink); }
        .custom-option-label:hover { border-color: var(--pink); }
        .custom-option-extra { font-size: 10px; font-weight: 500; color: var(--gray); }
        .price-base { font-size: 13px; color: var(--gray); }
        .price-extra { font-size: 13px; color: var(--brown-dark); font-weight: 600; }
        .price-total-label { font-size: 15px; font-weight: 700; color: var(--text-dark); }
        .btn-add-cart { width: 100%; background-color: var(--pink); color: white; border: none; padding: 12px; border-radius: 8px; font-size: 14px; font-weight: 700; cursor: pointer; font-family: 'Plus Jakarta Sans', sans-serif; transition: opacity 0.2s; }
        .btn-add-cart:hover { opacity: 0.85; }
        .reviews-section { max-width: 1100px; margin: 24px auto 0; padding: 0 24px 60px; }
        .reviews-card { background: white; border-radius: 16px; border: 1px solid #EDE0D4; padding: 28px; box-shadow: 0 2px 12px rgba(0,0,0,0.05); }
        .reviews-title { font-family: 'Playfair Display', serif; font-size: 24px; font-weight: 700; color: var(--text-dark); margin-bottom: 4px; }
        .reviews-count { font-size: 13px; color: var(--gray); margin-bottom: 20px; }
        .reviews-divider { height: 1px; background: #EDE0D4; margin-bottom: 20px; }
        .review-card { background: var(--cream); border: 1px solid #EDE0D4; border-radius: 12px; padding: 16px; margin-bottom: 12px; }
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

            {{-- KUSTOMISASI --}}
            @if(isset($customizationOptions) && $customizationOptions->isNotEmpty())
            <div class="custom-section">
                <p class="custom-section-title"><i class="fas fa-paint-brush" style="color:var(--pink);margin-right:6px;font-size:12px;"></i>Pilih Kustomisasi</p>
                @foreach($customizationOptions as $type => $options)
                <div class="custom-type-block">
                    <p class="custom-type-label">
                        {{ match($type) { 'rasa' => 'Rasa', 'ukuran' => 'Ukuran', 'topping' => 'Topping', default => ucfirst($type) } }}
                    </p>
                    <div class="custom-options">
                        @foreach($options as $option)
                        @if($type === 'topping')
                        {{-- Topping: checkbox (bisa pilih banyak) --}}
                        <div>
                            <input type="checkbox"
                                   class="custom-option-item custom-opt-input"
                                   id="opt-{{ $option->id }}"
                                   name="customizations[]"
                                   value="{{ $option->id }}"
                                   data-price="{{ $option->extra_price }}"
                                   data-type="checkbox">
                            <label class="custom-option-label" for="opt-{{ $option->id }}">
                                {{ $option->name }}
                                @if($option->extra_price > 0)
                                    <span class="custom-option-extra">+Rp{{ number_format($option->extra_price, 0, ',', '.') }}</span>
                                @endif
                            </label>
                        </div>
                        @else
                        {{-- Rasa / Ukuran / Lainnya: radio (pilih satu) --}}
                        <div>
                            <input type="radio"
                                   class="custom-option-item custom-opt-input"
                                   id="opt-{{ $option->id }}"
                                   name="customization_{{ $type }}"
                                   value="{{ $option->id }}"
                                   data-price="{{ $option->extra_price }}"
                                   data-type="radio"
                                   data-group="{{ $type }}">
                            <label class="custom-option-label" for="opt-{{ $option->id }}">
                                {{ $option->name }}
                                @if($option->extra_price > 0)
                                    <span class="custom-option-extra">+Rp{{ number_format($option->extra_price, 0, ',', '.') }}</span>
                                @endif
                            </label>
                        </div>
                        @endif
                        @endforeach
                    </div>
                </div>
                @endforeach
                {{-- Hidden JSON untuk dikirim ke cart --}}
                <input type="hidden" id="customizations-json" name="customizations_json" value="[]">
            </div>
            @endif

            {{-- Catatan / Tulisan di kue --}}
            <label class="catatan-label">Tulisan di kue / instruksi khusus</label>
            <textarea id="note-input" class="catatan-input" rows="3"
                      placeholder="Contoh: Selamat ulang tahun Budi, warna biru..."
                      maxlength="300"></textarea>
            <div style="text-align:right;font-size:11px;color:var(--gray);margin-top:-12px;margin-bottom:12px;">
                <span id="note-char-count">0</span>/300
            </div>

            <div class="subtotal-row">
                <span class="subtotal-label">Subtotal</span>
                <div id="price-display">
                    <span class="price-total-label" id="subtotal">Rp{{ number_format($product->price, 0, ',', '.') }}</span>
                </div>
            </div>

        @auth
            <form id="add-to-cart-form" action="/cart/add" method="POST">
                @csrf
                    <input type="hidden" name="product_id" value="{{ $product->id }}">
                    <input type="hidden" name="quantity" id="qty-hidden" value="1">
                    <input type="hidden" name="note" id="note-hidden" value="">
                    <input type="hidden" name="customizations_json" id="form-customizations-json" value="[]">
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
        <div class="reviews-card">
            <h2 class="reviews-title">Ulasan Produk</h2>
            <p class="reviews-count">{{ $product->reviews->count() }} ulasan</p>
            <div class="reviews-divider"></div>

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
                    <img src="{{ asset('storage/' . $image->path) }}" alt="Gambar ulasan" loading="lazy">
                    @endforeach
                </div>
                @endif
            </div>
            @empty
            <div style="text-align:center;padding:32px 0;color:var(--gray);">
                <i class="fas fa-star" style="font-size:32px;color:#E5D5C5;margin-bottom:10px;display:block;"></i>
                <p style="font-size:14px;">Belum ada ulasan untuk produk ini.</p>
            </div>
            @endforelse
        </div>
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
        updatePriceDisplay();
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
    const noteCharCount = document.getElementById('note-char-count');
    if (noteInput && noteHidden) {
        noteInput.addEventListener('input', function() {
            noteHidden.value = this.value;
            if (noteCharCount) noteCharCount.textContent = this.value.length;
        });
    }

    // ---- KUSTOMISASI ----
    const basePrice = {{ $product->price }};

    function getExtraPrice() {
        let extra = 0;
        document.querySelectorAll('.custom-opt-input').forEach(function(el) {
            if (el.checked) {
                extra += parseInt(el.dataset.price || 0);
            }
        });
        return extra;
    }

    function buildCustomizationsJson() {
        const selected = [];
        document.querySelectorAll('.custom-opt-input:checked').forEach(function(el) {
            selected.push({ id: el.value, price: parseInt(el.dataset.price || 0) });
        });
        return JSON.stringify(selected);
    }

    function updatePriceDisplay() {
        const qty = parseInt(document.getElementById('qty')?.value || 1);
        const extra = getExtraPrice();
        const total = (basePrice + extra) * qty;

        const priceDisplay = document.getElementById('price-display');

        if (priceDisplay) {
            if (extra > 0) {
                priceDisplay.innerHTML =
                    '<span class="price-base">Rp' + basePrice.toLocaleString('id-ID') +
                    ' <span class="price-extra">+ Rp' + extra.toLocaleString('id-ID') + '</span></span>' +
                    ' <span class="price-total-label" id="subtotal">= Rp' + total.toLocaleString('id-ID') + '</span>';
            } else {
                priceDisplay.innerHTML =
                    '<span class="price-total-label" id="subtotal">Rp' + total.toLocaleString('id-ID') + '</span>';
            }
        }

        const jsonVal = buildCustomizationsJson();
        const jsonInput = document.getElementById('customizations-json');
        if (jsonInput) jsonInput.value = jsonVal;
        const formJsonInput = document.getElementById('form-customizations-json');
        if (formJsonInput) formJsonInput.value = jsonVal;
    }

    document.querySelectorAll('.custom-opt-input').forEach(function(el) {
        el.addEventListener('change', updatePriceDisplay);
    });


</script>

<script src="{{ asset('js/app.js') }}" defer></script>
</body>
</html>