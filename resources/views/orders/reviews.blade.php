<!DOCTYPE html>
<html lang="id">
<head>
    <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}" />
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Jagoan Kue - Ulasan Pesanan {{ $order->order_code }}</title>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700;800&family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        :root { --pink: #F0507A; --brown-dark: #2C1810; --cream: #FFF8EE; --white: #FFFFFF; --gray: #6B7280; --text-dark: #1A1A1A; }
        body { font-family: 'Plus Jakarta Sans', sans-serif; color: var(--text-dark); background: var(--cream); }
        a { text-decoration: none; }
        .navbar { background-color: var(--brown-dark); padding: 16px 24px; position: sticky; top: 0; z-index: 100; }
        .navbar-inner { max-width: 1100px; margin: 0 auto; display: flex; align-items: center; justify-content: space-between; }
        .navbar-logo { font-family: 'Playfair Display', serif; font-size: 24px; font-weight: 800; color: var(--pink); }
        .navbar-links { display: flex; gap: 32px; list-style: none; }
        .navbar-links a { color: white; font-size: 14px; font-weight: 500; opacity: 0.9; }
        .navbar-actions { display: flex; align-items: center; gap: 12px; }
        .btn-cart { background-color: var(--pink); color: white; padding: 8px 18px; border-radius: 8px; font-size: 14px; font-weight: 600; }
        .btn-login { border: 1.5px solid white; color: white; padding: 8px 18px; border-radius: 8px; font-size: 14px; font-weight: 600; }

        .page { max-width: 1000px; margin: 0 auto; padding: 32px 24px 60px; }
        .page-title { font-family: 'Playfair Display', serif; font-size: 28px; font-weight: 800; margin-bottom: 8px; }
        .page-subtitle { font-size: 14px; color: var(--gray); margin-bottom: 20px; }
        .alert-success { background: #ECFDF5; border: 1px solid #A7F3D0; color: #065F46; padding: 12px 14px; border-radius: 12px; font-size: 13px; margin-bottom: 14px; }
        .alert-error { background: #FFF1F2; border: 1px solid #FECDD3; color: #9F1239; padding: 12px 14px; border-radius: 12px; font-size: 13px; margin-bottom: 14px; }

        .review-card { background: var(--white); border-radius: 16px; border: 1px solid #EDE0D4; padding: 20px; margin-bottom: 16px; }
        .item-head { display: flex; align-items: center; gap: 12px; margin-bottom: 14px; }
        .item-head img { width: 56px; height: 56px; border-radius: 10px; object-fit: cover; }
        .item-name { font-size: 16px; font-weight: 700; }
        .item-meta { font-size: 13px; color: var(--gray); margin-top: 4px; }

        .stars { display: flex; flex-direction: row-reverse; justify-content: flex-end; gap: 4px; margin-bottom: 10px; }
        .stars input { display: none; }
        .stars label { font-size: 24px; color: #D1D5DB; cursor: pointer; }
        .stars input:checked ~ label,
        .stars label:hover,
        .stars label:hover ~ label { color: #F59E0B; }

        textarea { width: 100%; border: 1px solid #D1C0B8; border-radius: 10px; padding: 10px 12px; min-height: 90px; resize: vertical; margin-bottom: 10px; font-family: 'Plus Jakarta Sans', sans-serif; }
        .file { margin-bottom: 10px; }
        .preview { display: flex; flex-wrap: wrap; gap: 8px; margin: 10px 0; }
        .preview img { width: 80px; height: 80px; border-radius: 8px; object-fit: cover; border: 1px solid #EDE0D4; }

        .btn-primary { background: var(--pink); color: white; padding: 10px 16px; border: none; border-radius: 10px; font-size: 13px; font-weight: 700; cursor: pointer; }
        .btn-secondary { background: #F3F4F6; color: #111827; padding: 10px 16px; border: none; border-radius: 10px; font-size: 13px; font-weight: 700; cursor: pointer; }
        .btn-danger { background: #DC2626; color: white; padding: 10px 16px; border: none; border-radius: 10px; font-size: 13px; font-weight: 700; cursor: pointer; }
        .btn-back { display: inline-block; margin-top: 10px; background: var(--brown-dark); color: white; padding: 11px 18px; border-radius: 10px; font-size: 13px; font-weight: 700; }
        .reviewed-box { border: 1px solid #D1FAE5; background: #F0FDF4; border-radius: 10px; padding: 12px; font-size: 13px; }
        .reviewed-stars { color: #F59E0B; font-size: 18px; margin-bottom: 6px; }
        .reviewed-comment { line-height: 1.6; margin-bottom: 8px; }
        .action-row { display: flex; gap: 8px; flex-wrap: wrap; margin-top: 10px; }
        .inline-form { display: inline; }
        .edit-block { margin-top: 12px; border: 1px dashed #D1C0B8; border-radius: 10px; padding: 12px; background: #FFFDF9; }
        .thumb-card { display: inline-flex; flex-direction: column; gap: 6px; align-items: center; }
        .thumb-card img { width: 80px; height: 80px; border-radius: 8px; object-fit: cover; border: 1px solid #EDE0D4; }
        .btn-mini { background: #B91C1C; color: white; border: none; border-radius: 8px; font-size: 11px; padding: 5px 8px; cursor: pointer; }

        @media (max-width: 768px) { .navbar-links { display: none; } .footer-inner { grid-template-columns: 1fr 1fr; } }
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
    </style>
</head>
<body>
@include('partials.navbar')

<div class="page">
    <h1 class="page-title">Ulasan Pesanan</h1>
    <p class="page-subtitle">Kode: {{ $order->order_code }} · Beri rating dan ulasan untuk setiap produk.</p>

    @if (session('success'))
        <div class="alert-success">{{ session('success') }}</div>
    @endif

    @if ($errors->any())
        <div class="alert-error">{{ $errors->first() }}</div>
    @endif

    @php
        $canReview = $order->status === 'completed' && ($order->payment?->status === 'paid');
        $reviewsByProduct = $order->productReviews->keyBy('product_id');
    @endphp

    @foreach ($order->orderItems as $item)
        @php
            $product = $item->product;
            $review = $product ? ($reviewsByProduct[$product->id] ?? null) : null;
        @endphp
        <div class="review-card">
            <div class="item-head">
                <img src="{{ $product && $product->image ? asset('storage/' . $product->image) : 'https://images.unsplash.com/photo-1563729784474-d77dbb933a9e?w=200&q=80' }}" alt="{{ $product->name ?? 'Produk' }}">
                <div>
                    <p class="item-name">{{ $product->name ?? 'Produk dihapus' }}</p>
                    <p class="item-meta">{{ $item->quantity }}x · Rp {{ number_format($item->price, 0, ',', '.') }}</p>
                </div>
            </div>

            @if (!$canReview)
                <div class="reviewed-box">Ulasan hanya bisa dikirim jika pesanan sudah selesai dan pembayaran sudah paid.</div>
            @elseif (!$product)
                <div class="reviewed-box">Produk tidak tersedia untuk diulas.</div>
            @elseif ($review)
                <div class="reviewed-box">
                    <div class="reviewed-stars">{{ str_repeat('★', (int) $review->rating) }}{{ str_repeat('☆', 5 - (int) $review->rating) }}</div>
                    <p class="reviewed-comment">{{ $review->comment }}</p>
                    @if ($review->images->isNotEmpty())
                        <div class="preview">
                            @foreach ($review->images as $image)
                                <img src="{{ asset('storage/' . $image->path) }}" alt="Gambar ulasan">
                            @endforeach
                        </div>
                    @endif

                    <div class="action-row">
                        <button class="btn-secondary" type="button" onclick="toggleEdit('edit-{{ $review->id }}')">Edit</button>
                        <form class="inline-form" method="POST" action="{{ route('orders.reviews.destroy', ['order' => $order, 'review' => $review]) }}" onsubmit="return confirm('Hapus ulasan ini?')">
                            @csrf
                            @method('DELETE')
                            <button class="btn-danger" type="submit">Hapus Ulasan</button>
                        </form>
                    </div>
                </div>

                <div class="edit-block" id="edit-{{ $review->id }}" style="display:none;">
                    @if ($review->images->isNotEmpty())
                        <p style="font-size:12px;font-weight:700;margin-bottom:6px;">Gambar Saat Ini</p>
                        <div class="preview">
                            @foreach ($review->images as $image)
                                <div class="thumb-card">
                                    <img src="{{ asset('storage/' . $image->path) }}" alt="Gambar ulasan">
                                    <form method="POST" action="{{ route('orders.reviews.images.destroy', ['order' => $order, 'review' => $review, 'image' => $image]) }}" onsubmit="return confirm('Hapus gambar ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button class="btn-mini" type="submit">Hapus Gambar</button>
                                    </form>
                                </div>
                            @endforeach
                        </div>
                    @endif

                    <form method="POST" action="{{ route('orders.reviews.update', ['order' => $order, 'review' => $review]) }}" enctype="multipart/form-data">
                        @csrf
                        @method('PATCH')

                        <div class="stars">
                            <input type="radio" id="edit-star-{{ $review->id }}-5" name="rating" value="5" {{ (int) $review->rating === 5 ? 'checked' : '' }} required>
                            <label for="edit-star-{{ $review->id }}-5">★</label>
                            <input type="radio" id="edit-star-{{ $review->id }}-4" name="rating" value="4" {{ (int) $review->rating === 4 ? 'checked' : '' }}>
                            <label for="edit-star-{{ $review->id }}-4">★</label>
                            <input type="radio" id="edit-star-{{ $review->id }}-3" name="rating" value="3" {{ (int) $review->rating === 3 ? 'checked' : '' }}>
                            <label for="edit-star-{{ $review->id }}-3">★</label>
                            <input type="radio" id="edit-star-{{ $review->id }}-2" name="rating" value="2" {{ (int) $review->rating === 2 ? 'checked' : '' }}>
                            <label for="edit-star-{{ $review->id }}-2">★</label>
                            <input type="radio" id="edit-star-{{ $review->id }}-1" name="rating" value="1" {{ (int) $review->rating === 1 ? 'checked' : '' }}>
                            <label for="edit-star-{{ $review->id }}-1">★</label>
                        </div>

                        <textarea name="comment" required>{{ $review->comment }}</textarea>

                        <p style="font-size:12px;font-weight:700;margin-bottom:6px;">Tambah Gambar Baru (opsional)</p>
                        <input class="file" type="file" name="images[]" multiple accept=".jpg,.jpeg,.png,.webp" onchange="previewFiles(this)">
                        <div class="preview"></div>

                        <div class="action-row">
                            <button class="btn-primary" type="submit">Simpan Perubahan</button>
                            <button class="btn-secondary" type="button" onclick="toggleEdit('edit-{{ $review->id }}')">Batal</button>
                        </div>
                    </form>
                </div>
            @else
                <form method="POST" action="{{ route('orders.reviews.store', ['order' => $order, 'product' => $product]) }}" enctype="multipart/form-data">
                    @csrf
                    <div class="stars">
                        <input type="radio" id="star-{{ $item->id }}-5" name="rating" value="5" required>
                        <label for="star-{{ $item->id }}-5">★</label>
                        <input type="radio" id="star-{{ $item->id }}-4" name="rating" value="4">
                        <label for="star-{{ $item->id }}-4">★</label>
                        <input type="radio" id="star-{{ $item->id }}-3" name="rating" value="3">
                        <label for="star-{{ $item->id }}-3">★</label>
                        <input type="radio" id="star-{{ $item->id }}-2" name="rating" value="2">
                        <label for="star-{{ $item->id }}-2">★</label>
                        <input type="radio" id="star-{{ $item->id }}-1" name="rating" value="1">
                        <label for="star-{{ $item->id }}-1">★</label>
                    </div>
                    <textarea name="comment" placeholder="Tulis ulasanmu untuk produk ini..." required></textarea>
                    <input class="file" type="file" name="images[]" multiple accept=".jpg,.jpeg,.png,.webp" onchange="previewFiles(this)">
                    <div class="preview"></div>
                    <button class="btn-primary" type="submit">Kirim Ulasan</button>
                </form>
            @endif
        </div>
    @endforeach

    <a href="{{ route('orders.show', $order) }}" class="btn-back">← Kembali ke Detail Pesanan</a>
</div>

@include('partials.footer')
<script>
    function toggleEdit(id) {
        const el = document.getElementById(id);
        if (!el) return;
        el.style.display = el.style.display === 'none' ? 'block' : 'none';
    }

    function previewFiles(input) {
        const box = input.parentElement.querySelector('.preview');
        box.innerHTML = '';

        if (!input.files) return;
        Array.from(input.files).forEach((file) => {
            const reader = new FileReader();
            reader.onload = function (e) {
                const img = document.createElement('img');
                img.src = e.target.result;
                box.appendChild(img);
            };
            reader.readAsDataURL(file);
        });
    }
</script>
</body>
</html>

