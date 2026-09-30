@extends('layouts.main')

@section('title', 'Jagoan Kue - Keranjang')

@section('content')
<div class="bg-cream min-h-screen py-8 px-6">
    <div class="max-w-[1140px] mx-auto">
        <h1 class="font-heading text-3xl font-bold text-brown-dark mb-1">Keranjang</h1>
        <p class="text-sm text-text-secondary mb-6">Kelola item belanjaan Anda sebelum checkout.</p>

        <div class="flex flex-col md:flex-row gap-6 items-start">
            <div class="flex-1 w-full">
                @if(isset($cartItems) && count($cartItems) > 0)
                <div class="bg-white rounded-2xl border border-cream-border p-6 mb-4 flex items-center justify-between shadow-sm">
                    <div class="flex items-center gap-3">
                        <input type="checkbox" class="w-5 h-5 rounded border-cream-border accent-primary cursor-pointer" id="check-all" onchange="toggleAll(this)" checked>
                        <span class="text-sm font-semibold text-brown-dark">Pilih Semua</span>
                        <span class="text-sm text-text-secondary font-medium">({{ count($cartItems) }})</span>
                    </div>
                    <button class="text-sm font-semibold text-red-600 hover:text-red-700 transition-colors" onclick="hapusSelected()">Hapus</button>
                </div>

                @foreach($cartItems as $item)
                <div class="bg-white rounded-2xl border border-cream-border p-6 mb-4 flex flex-col sm:flex-row gap-4 items-start shadow-sm" id="item-{{ $item['product']->id }}">
                    <div class="flex items-center gap-3 mt-1 shrink-0">
                        <input type="checkbox" class="w-5 h-5 rounded border-cream-border accent-primary cursor-pointer item-check" data-price="{{ $item['product']->price }}" data-id="{{ $item['product']->id }}" onchange="updateTotal()" checked>
                    </div>
                    <img src="{{ $item['product']->image ? asset('storage/' . $item['product']->image) : 'https://images.unsplash.com/photo-1563729784474-d77dbb933a9e?w=200&q=80' }}" alt="{{ $item['product']->name }}" class="w-20 h-20 sm:w-24 sm:h-24 rounded-xl object-cover shrink-0">
                    <div class="flex-1 w-full">
                        <div class="flex flex-col sm:flex-row sm:items-start justify-between gap-1 mb-2">
                            <p class="font-semibold text-base text-brown-dark">{{ $item['product']->name }}</p>
                            <p class="font-bold text-base text-primary" id="price-{{ $item['product']->id }}">Rp{{ number_format($item['product']->price * $item['quantity'], 0, ',', '.') }}</p>
                        </div>
                        @if(!empty($item['customizationOptions']) && $item['customizationOptions']->isNotEmpty())
                        <p class="text-xs text-brown-dark mb-3 flex items-center gap-1.5">
                            <i class="fas fa-paint-brush text-primary"></i>
                            {{ $item['customizationOptions']->map(fn($o) => $o->name)->join(', ') }}
                        </p>
                        @endif
                        <label class="block text-xs font-semibold text-brown-mid mb-1.5">Catatan Produk</label>
                        <textarea class="w-full border border-cream-border rounded-xl px-4 py-2.5 text-xs text-text-primary bg-white outline-none focus:border-primary focus:ring-2 focus:ring-primary/20 mb-3 resize-none" id="note-{{ $item['product']->id }}" rows="2" placeholder="Contoh: tulisan ucapan, warna, request khusus..." oninput="queueNoteSave({{ $item['product']->id }})">{{ $item['note'] ?? '' }}</textarea>
                        <div class="flex items-center justify-between mt-2">
                            <button class="p-2 -ml-2 text-primary hover:text-primary-hover transition-colors text-sm" title="Hapus" onclick="hapusItem({{ $item['product']->id }})"><i class="fas fa-trash"></i></button>
                            <div class="flex items-center gap-3">
                                <button class="w-8 h-8 rounded-full border-2 border-primary text-primary hover:bg-primary hover:text-white transition-all flex items-center justify-center text-xs" onclick="changeItemQty({{ $item['product']->id }}, -1)"><i class="fas fa-minus"></i></button>
                                <input type="number" class="w-12 text-center font-bold text-brown-dark bg-transparent border-0 focus:ring-0 p-0 [appearance:textfield] [&::-webkit-outer-spin-button]:appearance-none [&::-webkit-inner-spin-button]:appearance-none" id="qty-{{ $item['product']->id }}" value="{{ $item['quantity'] }}" min="1" onchange="updateItemPrice({{ $item['product']->id }}, {{ $item['product']->price }})">
                                <button class="w-8 h-8 rounded-full border-2 border-primary text-primary hover:bg-primary hover:text-white transition-all flex items-center justify-center text-xs" onclick="changeItemQty({{ $item['product']->id }}, 1)"><i class="fas fa-plus"></i></button>
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach
                @else
                <div class="text-center py-20 bg-white rounded-2xl border border-cream-border p-6 flex flex-col items-center justify-center shadow-sm">
                    <i class="fas fa-shopping-cart text-6xl text-cream-dark mb-4"></i>
                    <p class="text-lg font-semibold text-brown-dark mb-6">Keranjang kamu masih kosong</p>
                    <a href="/products" class="btn-primary">Belanja Sekarang</a>
                </div>
                @endif
            </div>

            <div class="w-full md:w-[320px] bg-cream-warm rounded-2xl border border-cream-border p-5 shrink-0 shadow-sm">
                <p class="font-heading text-xl font-bold text-brown-dark mb-4">Ringkasan Belanja</p>
                <div class="flex justify-between items-center pt-3 mt-2 border-t border-cream-border font-bold text-brown-dark">
                    <span>Total Harga</span>
                    <span id="total-price" class="text-xl text-primary font-extrabold">Rp0</span>
                </div>
                <p id="cart-error" class="hidden text-red-600 text-xs font-semibold mt-3 text-center"></p>
                @guest
                {{-- Guest: arahkan ke /cart/checkout agar auth middleware simpan intended URL --}}
                <a href="{{ route('cart.checkout') }}" class="block w-full mt-4">
                    <button class="btn-primary w-full justify-center py-3" type="button">
                        <i class="fas fa-lock mr-2 text-xs"></i>Login untuk Checkout
                    </button>
                </a>
                <p class="text-center text-[11px] text-text-muted mt-3">Silakan login untuk melanjutkan pemesanan</p>
                @else
                <button class="btn-primary w-full justify-center mt-4 py-3" onclick="beliSekarang()">Beli (<span id="beli-count">{{ isset($cartItems) ? count($cartItems) : 0 }}</span>)</button>
                @endguest
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
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
        const beliCountEl = document.getElementById('beli-count');
        if (beliCountEl) beliCountEl.textContent = count;
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
            if (errEl) { errEl.textContent = 'Pilih produk terlebih dahulu!'; errEl.classList.remove('hidden'); }
            return;
        }
        if (errEl) errEl.classList.add('hidden');

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
@endpush
