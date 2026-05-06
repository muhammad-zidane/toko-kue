<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Jagoan Kue - Keranjang</title>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700;800&family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        :root { --pink: #F0507A; --brown-dark: #2C1810; --cream: #FFF8EE; --cream-dark: #F5EDD8; --white: #FFFFFF; --gray: #6B7280; --text-dark: #1A1A1A; }
        body { font-family: 'Plus Jakarta Sans', sans-serif; color: var(--text-dark); background: var(--cream); }
        a { text-decoration: none; }
        .navbar { background-color: var(--brown-dark); padding: 16px 24px; position: sticky; top: 0; z-index: 100; }
        .navbar-inner { max-width: 1100px; margin: 0 auto; display: flex; align-items: center; justify-content: space-between; }
        .navbar-logo { font-family: 'Playfair Display', serif; font-size: 24px; font-weight: 800; color: var(--pink); }
        .navbar-links { display: flex; gap: 32px; list-style: none; }
        .navbar-links a { color: white; font-size: 14px; font-weight: 500; opacity: 0.9; }
        .navbar-actions { display: flex; align-items: center; gap: 12px; }
        .btn-cart { background-color: var(--pink); color: white; padding: 8px 18px; border-radius: 8px; font-size: 14px; font-weight: 600; }
        .btn-login { border: 1.5px solid white; color: white; padding: 8px 18px; border-radius: 8px; font-size: 14px; font-weight: 600; transition: all 0.2s; }
        .btn-login:hover { background: white; color: var(--brown-dark); }
        .page { max-width: 1100px; margin: 0 auto; padding: 32px 24px 60px; }
        .page-title { font-size: 18px; font-weight: 700; margin-bottom: 20px; }
        .cart-layout { display: grid; grid-template-columns: 1fr 340px; gap: 24px; align-items: start; }
        .cart-box { background: var(--white); border-radius: 16px; border: 1px solid #EDE0D4; overflow: hidden; }
        .select-all-row { display: flex; align-items: center; justify-content: space-between; padding: 16px 20px; border-bottom: 1px solid #F0E8E0; }
        .select-all-left { display: flex; align-items: center; gap: 12px; }
        .custom-check { width: 20px; height: 20px; accent-color: var(--brown-dark); cursor: pointer; }
        .select-all-label { font-size: 15px; font-weight: 700; }
        .select-count { font-size: 14px; color: var(--gray); font-weight: 400; }
        .btn-hapus { font-size: 14px; font-weight: 600; color: var(--pink); background: none; border: none; cursor: pointer; font-family: 'Plus Jakarta Sans', sans-serif; }
        .cart-item { display: flex; align-items: center; gap: 14px; padding: 16px 20px; border-bottom: 1px solid #F0E8E0; }
        .cart-item:last-child { border-bottom: none; }
        .cart-item img { width: 80px; height: 80px; object-fit: cover; border-radius: 10px; flex-shrink: 0; }
        .item-info { flex: 1; }
        .item-name { font-size: 15px; font-weight: 600; margin-bottom: 4px; }
        .item-price { font-size: 15px; font-weight: 700; }
        .item-actions { display: flex; align-items: center; gap: 10px; margin-top: 10px; }
        .action-btn { background: none; border: none; cursor: pointer; font-size: 16px; padding: 4px; color: var(--gray); transition: color 0.2s; }
        .action-btn:hover { color: var(--pink); }
        .qty-control { display: flex; align-items: center; border: 1.5px solid #D1C0B8; border-radius: 6px; overflow: hidden; }
        .qty-btn { background: none; border: none; padding: 4px 10px; font-size: 15px; cursor: pointer; color: var(--text-dark); font-weight: 700; font-family: 'Plus Jakarta Sans', sans-serif; }
        .qty-btn:hover { background: var(--cream); }
        .qty-num { width: 36px; text-align: center; border: none; border-left: 1.5px solid #D1C0B8; border-right: 1.5px solid #D1C0B8; padding: 4px 0; font-size: 13px; font-weight: 600; font-family: 'Plus Jakarta Sans', sans-serif; outline: none; background: var(--white); }
        .empty-cart { padding: 60px 20px; text-align: center; color: var(--gray); }
        .empty-cart p { font-size: 15px; margin-bottom: 16px; }
        .btn-shop { display: inline-block; background: var(--pink); color: white; padding: 10px 24px; border-radius: 8px; font-size: 14px; font-weight: 600; }
        .summary-box { background: var(--white); border-radius: 16px; border: 1px solid #EDE0D4; padding: 24px; position: sticky; top: 90px; }
        .summary-title { font-size: 16px; font-weight: 700; margin-bottom: 20px; }
        .summary-row { display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px; padding-bottom: 16px; border-bottom: 1px solid #F0E8E0; }
        .summary-row span:first-child { font-size: 15px; font-weight: 600; }
        .summary-row span:last-child { font-size: 16px; font-weight: 700; }
        .btn-beli { width: 100%; background: var(--pink); color: white; border: none; border-radius: 10px; padding: 14px; font-size: 15px; font-weight: 700; cursor: pointer; font-family: 'Plus Jakarta Sans', sans-serif; transition: opacity 0.2s; }
        .btn-beli:hover { opacity: 0.85; }
        .footer { background-color: var(--brown-dark); color: white; padding: 56px 24px; }
        .footer-inner { max-width: 1100px; margin: 0 auto; display: grid; grid-template-columns: 2fr 1fr 1fr 1.5fr; gap: 40px; }
        .footer-logo { font-family: 'Playfair Display', serif; font-size: 22px; font-weight: 800; color: var(--pink); margin-bottom: 8px; }
        .footer-desc { font-size: 13px; opacity: 0.6; margin-bottom: 20px; line-height: 1.6; }
        .footer-heading { font-size: 14px; font-weight: 700; margin-bottom: 16px; }
        .footer-links { list-style: none; display: flex; flex-direction: column; gap: 10px; }
        .footer-links a { color: white; font-size: 13px; opacity: 0.6; }
        .footer-contact { list-style: none; display: flex; flex-direction: column; gap: 10px; }
        .footer-contact li { font-size: 13px; opacity: 0.6; line-height: 1.5; }
        @media (max-width: 768px) { .navbar-links { display: none; } .cart-layout { grid-template-columns: 1fr; } .footer-inner { grid-template-columns: 1fr 1fr; } }
    </style>
</head>
<body>

<nav class="navbar"><div class="navbar-inner"><a href="/" class="navbar-logo">Jagoan Kue</a><ul class="navbar-links"><li><a href="/">Beranda</a></li><li><a href="/products">Katalog</a></li><li><a href="/orders">Pemesanan</a></li></ul><div class="navbar-actions"><a href="/cart" class="btn-cart">🛒 Keranjang</a>@auth<a href="/profile" class="btn-login">{{ auth()->user()->name }}</a>@else<a href="/login" class="btn-login">Login</a>@endauth</div></div></nav>

<div class="page">
    <p class="page-title">Keranjang</p>
    <div class="cart-layout">
        <div class="cart-box">
            @if(isset($cartItems) && count($cartItems) > 0)
            <div class="select-all-row">
                <div class="select-all-left">
                    <input type="checkbox" class="custom-check" id="check-all" onchange="toggleAll(this)" checked>
                    <span class="select-all-label">Pilih Semua</span>
                    <span class="select-count">({{ count($cartItems) }})</span>
                </div>
                <button class="btn-hapus" onclick="hapusSelected()">Hapus</button>
            </div>

            @foreach($cartItems as $item)
            <div class="cart-item" id="item-{{ $item['product']->id }}">
                <input type="checkbox" class="custom-check item-check" data-price="{{ $item['product']->price }}" data-id="{{ $item['product']->id }}" onchange="updateTotal()" checked>
                <img src="{{ $item['product']->image ? asset('storage/' . $item['product']->image) : 'https://images.unsplash.com/photo-1563729784474-d77dbb933a9e?w=200&q=80' }}" alt="{{ $item['product']->name }}">
                <div class="item-info">
                    <p class="item-name">{{ $item['product']->name }}</p>
                    <p class="item-price" id="price-{{ $item['product']->id }}">Rp{{ number_format($item['product']->price * $item['quantity'], 0, ',', '.') }}</p>
                    <div class="item-actions">
                        <button class="action-btn" title="Hapus" onclick="hapusItem({{ $item['product']->id }})">🗑</button>
                        <div class="qty-control">
                            <button class="qty-btn" onclick="changeItemQty({{ $item['product']->id }}, -1)">−</button>
                            <input type="number" class="qty-num" id="qty-{{ $item['product']->id }}" value="{{ $item['quantity'] }}" min="1" onchange="updateItemPrice({{ $item['product']->id }}, {{ $item['product']->price }})">
                            <button class="qty-btn" onclick="changeItemQty({{ $item['product']->id }}, 1)">+</button>
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
            @else
            <div class="empty-cart">
                <p>Keranjang kamu masih kosong 🛒</p>
                <a href="/products" class="btn-shop">Belanja Sekarang</a>
            </div>
            @endif
        </div>

        <div class="summary-box">
            <p class="summary-title">Ringkasan Belanja</p>
            <div class="summary-row">
                <span>Total</span>
                <span id="total-price">Rp0</span>
            </div>
            <button class="btn-beli" onclick="beliSekarang()">Beli (<span id="beli-count">{{ isset($cartItems) ? count($cartItems) : 0 }}</span>)</button>
        </div>
    </div>
</div>

<footer class="footer"><div class="footer-inner"><div><p class="footer-logo">Jagoan Kue</p><p class="footer-desc">Menyediakan kue dengan cinta sejak 2023</p></div><div><p class="footer-heading">Layanan</p><ul class="footer-links"><li><a href="#">Katalog Kue</a></li><li><a href="#">Kue Custom</a></li></ul></div><div><p class="footer-heading">Selengkapnya</p><ul class="footer-links"><li><a href="#">Tentang Kami</a></li><li><a href="#">Blog</a></li></ul></div><div><p class="footer-heading">Kontak</p><ul class="footer-contact"><li>0822-8320-3385</li><li>muhammadzidane253@gmail.com</li><li>Payakumbuh, Sumatera Barat</li></ul></div></div></footer>

<script>
    const products = @json(isset($cartItems) ? collect($cartItems)->mapWithKeys(fn($i) => [$i['product']->id => $i['product']->price]) : []);
    const csrfToken = document.querySelector('meta[name="csrf-token"]').content;

    function updateTotal() {
        let total = 0, count = 0;
        document.querySelectorAll('.item-check').forEach(check => {
            if (check.checked) {
                const id = check.dataset.id;
                const qty = parseInt(document.getElementById('qty-' + id)?.value || 1);
                total += (products[id] || 0) * qty;
                count++;
            }
        });
        document.getElementById('total-price').textContent = 'Rp' + total.toLocaleString('id-ID');
        document.getElementById('beli-count').textContent = count;
    }

    function toggleAll(el) {
        document.querySelectorAll('.item-check').forEach(c => { c.checked = el.checked; });
        updateTotal();
    }

    function changeItemQty(id, delta) {
        const input = document.getElementById('qty-' + id);
        let val = parseInt(input.value) + delta;
        if (val < 1) val = 1;
        input.value = val;
        updateItemPrice(id, products[id]);
        updateTotal();
    }

    function updateItemPrice(id, price) {
        const qty = parseInt(document.getElementById('qty-' + id).value);
        document.getElementById('price-' + id).textContent = 'Rp' + (price * qty).toLocaleString('id-ID');
        updateTotal();
    }

    function hapusItem(id) {
        fetch('/cart/remove', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken },
            body: JSON.stringify({ ids: [id] })
        }).then(() => {
            const el = document.getElementById('item-' + id);
            if (el) { el.remove(); updateTotal(); }
        });
    }

    function hapusSelected() {
        const ids = [];
        document.querySelectorAll('.item-check:checked').forEach(check => ids.push(check.dataset.id));
        if (ids.length === 0) return;
        fetch('/cart/remove', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken },
            body: JSON.stringify({ ids: ids })
        }).then(() => {
            ids.forEach(id => {
                const el = document.getElementById('item-' + id);
                if (el) el.remove();
            });
            updateTotal();
        });
    }

    function beliSekarang() {
        const checked = document.querySelectorAll('.item-check:checked');
        if (checked.length === 0) { alert('Pilih produk terlebih dahulu!'); return; }
        // Checkout semua item di keranjang
        window.location.href = '/cart/checkout';
    }

    updateTotal();
</script>
</body>
</html>
