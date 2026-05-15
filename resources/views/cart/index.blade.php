<!DOCTYPE html>
<html lang="id">
<head>
    <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}" />
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Jagoan Kue - Keranjang</title>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700;800&family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <style>
        body { background: var(--cream); }
        .page { max-width: 1100px; margin: 0 auto; padding: 32px 24px 60px; }
        .page-title { font-size: 18px; font-weight: 700; margin-bottom: 20px; }
        .cart-layout { display: grid; grid-template-columns: 1fr 340px; gap: 24px; align-items: start; }
        .cart-box { background: var(--white); border-radius: 16px; border: 1px solid #EDE0D4; overflow: hidden; min-height: 290px;}
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
        .item-note-label { font-size: 12px; color: var(--gray); margin-top: 8px; margin-bottom: 4px; display: block; }
        .item-note-input { width: 100%; border: 1px solid #D1C0B8; border-radius: 8px; padding: 8px 10px; font-size: 12px; font-family: 'Plus Jakarta Sans', sans-serif; background: #FFFDF9; }
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
        @media (max-width: 768px) {
 .cart-layout { grid-template-columns: 1fr; }
 }
    </style></head>
<body>
@include('partials.navbar')

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
                    <label class="item-note-label">Catatan Produk</label>
                    <textarea class="item-note-input" id="note-{{ $item['product']->id }}" rows="2" placeholder="Contoh: tulisan ucapan, warna, request khusus..." oninput="queueNoteSave({{ $item['product']->id }})">{{ $item['note'] ?? '' }}</textarea>
                    <div class="item-actions">
                        <button class="action-btn" title="Hapus" onclick="hapusItem({{ $item['product']->id }})"><i class="fas fa-trash" style="color:var(--pink)"></i></button>
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
                <p>Keranjang kamu masih kosong <i class="fas fa-shopping-cart" style="color:var(--pink)"></i></p>
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
            <p id="cart-error" style="display:none;color:#DC2626;font-size:13px;font-weight:600;margin-bottom:8px;text-align:center;"></p>
            <button class="btn-beli" onclick="beliSekarang()">Beli (<span id="beli-count">{{ isset($cartItems) ? count($cartItems) : 0 }}</span>)</button>
        </div>
    </div>
</div>

@include('partials.footer')

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
        syncCartItem(id);
        updateTotal();
    }

    let noteSaveTimers = {};

    function queueNoteSave(id) {
        if (noteSaveTimers[id]) {
            clearTimeout(noteSaveTimers[id]);
        }
        noteSaveTimers[id] = setTimeout(() => {
            syncCartItem(id);
            delete noteSaveTimers[id];
        }, 400);
    }

    function syncCartItem(id) {
        const qtyEl = document.getElementById('qty-' + id);
        const noteEl = document.getElementById('note-' + id);
        const qty = qtyEl ? parseInt(qtyEl.value || 1) : 1;
        const note = noteEl ? noteEl.value : '';

        return fetch('/cart/update-item', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken },
            body: JSON.stringify({ product_id: id, quantity: qty, note: note })
        });
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
        const errEl = document.getElementById('cart-error');
        if (checked.length === 0) {
            if (errEl) { errEl.textContent = 'Pilih produk terlebih dahulu!'; errEl.style.display = 'block'; }
            return;
        }
        if (errEl) errEl.style.display = 'none';

        const ids = [];
        checked.forEach(check => ids.push(check.dataset.id));

        Promise.all(ids.map(id => syncCartItem(id)))
            .catch(() => null)
            .finally(() => {
                window.location.href = '/cart/checkout';
            });
    }

    updateTotal();
</script>
<script src="{{ asset('js/app.js') }}" defer></script>
</body>
</html>
