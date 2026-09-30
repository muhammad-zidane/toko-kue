@extends('layouts.main')

@section('title', $product->name . ' — Jagoan Kue')

@section('content')
{{-- PRODUCT DETAIL --}}
<section class="bg-cream min-h-screen py-12 px-6">
    <div class="max-w-[1140px] mx-auto grid grid-cols-1 md:grid-cols-[1fr_1.2fr_0.9fr] gap-10">

        {{-- Gambar --}}
        <div>
            <img src="{{ $product->image ? asset('storage/' . $product->image) : 'https://images.unsplash.com/photo-1563729784474-d77dbb933a9e?w=600&q=80' }}"
                 class="rounded-3xl shadow-lg border-4 border-white w-full object-cover aspect-square"
                 alt="{{ $product->name }}">
        </div>

        {{-- Info Produk --}}
        <div class="bg-white rounded-3xl border border-cream-border p-6 shadow-sm self-start">
            <h1 class="font-heading text-3xl font-bold text-brown-dark mb-2">{{ $product->name }}</h1>
            <p class="text-xs text-text-secondary mb-4">30+ Barang Telah Terjual</p>
            <p class="text-primary text-2xl font-extrabold mb-4 pb-4 border-b border-cream-border">Rp{{ number_format($product->price, 0, ',', '.') }}</p>
            <div>
                <p class="text-xs font-bold text-brown-light uppercase tracking-wide mb-2">Detail Produk</p>
                <p class="text-sm text-text-secondary leading-relaxed">{{ $product->description ?? 'Tidak ada deskripsi untuk produk ini.' }}</p>
            </div>
        </div>

        {{-- Widget Keranjang --}}
        <div class="bg-cream-warm rounded-3xl border border-cream-border p-6 self-start shadow-sm">
            <p class="text-sm font-bold text-brown-dark mb-4">Atur Jumlah dan Catatan</p>

            <div class="flex items-center gap-3 mb-4 justify-between">
                <div class="flex items-center gap-3">
                    <button class="w-9 h-9 rounded-full border-2 border-primary text-primary hover:bg-primary hover:text-white transition-all flex items-center justify-center text-xs" onclick="changeQty(-1)"><i class="fas fa-minus"></i></button>
                    <input type="number" id="qty" class="w-12 text-center font-bold text-brown-dark bg-transparent border-0 outline-none [appearance:textfield] [&::-webkit-outer-spin-button]:appearance-none [&::-webkit-inner-spin-button]:appearance-none" value="1" min="1" max="{{ $product->stock }}">
                    <button class="w-9 h-9 rounded-full border-2 border-primary text-primary hover:bg-primary hover:text-white transition-all flex items-center justify-center text-xs" onclick="changeQty(1)"><i class="fas fa-plus"></i></button>
                </div>
                <p class="text-xs text-text-secondary">Stok: <span class="font-bold text-brown-dark">{{ $product->stock }}</span></p>
            </div>

            {{-- KUSTOMISASI --}}
            @if(isset($customizationOptions) && $customizationOptions->isNotEmpty())
            <div class="border-t border-cream-border pt-4 mb-4">
                <p class="text-xs font-bold text-brown-light uppercase tracking-wide mb-3 flex items-center gap-1.5">
                    <i class="fas fa-paint-brush text-primary"></i> Pilih Kustomisasi
                </p>
                @foreach($customizationOptions as $type => $options)
                <div class="mb-4">
                    <p class="text-[10px] font-bold text-text-secondary uppercase tracking-wider mb-2">
                        {{ match($type) { 'rasa' => 'Rasa', 'ukuran' => 'Ukuran', 'topping' => 'Topping', default => ucfirst($type) } }}
                    </p>
                    <div class="flex flex-wrap gap-2">
                        @foreach($options as $option)
                        @if($type === 'topping')
                        {{-- Topping: checkbox (bisa pilih banyak) --}}
                        <div class="relative">
                            <input type="checkbox"
                                   class="sr-only custom-opt-input peer"
                                   id="opt-{{ $option->id }}"
                                   name="customizations[]"
                                   value="{{ $option->id }}"
                                   data-price="{{ $option->extra_price }}"
                                   data-type="checkbox">
                            <label class="inline-flex items-center gap-1 px-3 py-1.5 border border-cream-border rounded-xl text-xs font-semibold text-brown-dark cursor-pointer transition-all select-none bg-white hover:border-primary peer-checked:border-primary peer-checked:bg-primary-light peer-checked:text-primary" for="opt-{{ $option->id }}">
                                {{ $option->name }}
                                @if($option->extra_price > 0)
                                    <span class="text-[9px] font-bold text-text-secondary">+Rp{{ number_format($option->extra_price, 0, ',', '.') }}</span>
                                @endif
                            </label>
                        </div>
                        @else
                        {{-- Rasa / Ukuran / Lainnya: radio (pilih satu) --}}
                        <div class="relative">
                            <input type="radio"
                                   class="sr-only custom-opt-input peer"
                                   id="opt-{{ $option->id }}"
                                   name="customization_{{ $type }}"
                                   value="{{ $option->id }}"
                                   data-price="{{ $option->extra_price }}"
                                   data-type="radio"
                                   data-group="{{ $type }}">
                            <label class="inline-flex items-center gap-1 px-3 py-1.5 border border-cream-border rounded-xl text-xs font-semibold text-brown-dark cursor-pointer transition-all select-none bg-white hover:border-primary peer-checked:border-primary peer-checked:bg-primary-light peer-checked:text-primary" for="opt-{{ $option->id }}">
                                {{ $option->name }}
                                @if($option->extra_price > 0)
                                    <span class="text-[9px] font-bold text-text-secondary">+Rp{{ number_format($option->extra_price, 0, ',', '.') }}</span>
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
            <label class="block text-xs font-semibold text-brown-mid mb-1.5 mt-4">Tulisan di kue / instruksi khusus</label>
            <textarea id="note-input" class="w-full border border-cream-border rounded-xl px-3 py-2 text-sm text-text-primary bg-white outline-none transition-all duration-200 focus:border-primary focus:ring-2 focus:ring-primary/20 resize-none" rows="3"
                      placeholder="Contoh: Selamat ulang tahun Budi, warna biru..."
                      maxlength="300"></textarea>
            <div class="text-right text-[10px] text-text-muted mt-1 mb-3">
                <span id="note-char-count">0</span>/300
            </div>

            <div class="flex justify-between items-center py-3 border-t border-cream-border mt-4 mb-5">
                <span class="text-sm font-semibold text-text-secondary">Subtotal</span>
                <div id="price-display">
                    <span class="font-heading text-2xl font-bold text-primary" id="subtotal">Rp{{ number_format($product->price, 0, ',', '.') }}</span>
                </div>
            </div>

        @auth
            <form id="add-to-cart-form" action="/cart/add" method="POST" class="mb-2">
                @csrf
                    <input type="hidden" name="product_id" value="{{ $product->id }}">
                    <input type="hidden" name="quantity" id="qty-hidden" value="1">
                    <input type="hidden" name="note" id="note-hidden" value="">
                    <input type="hidden" name="customizations_json" id="form-customizations-json" value="[]">
                    <button type="submit" id="btn-add-cart" class="btn-primary w-full justify-center shadow-gold hover:shadow-gold-lg transition-all duration-200">+ Keranjang</button>
            </form>
            <a href="{{ route('orders.create', $product) }}" class="w-full justify-center py-3 bg-brown-dark text-white font-bold rounded-full hover:bg-brown-mid transition-colors inline-flex items-center text-sm shadow-md">Beli Sekarang</a>
            <div id="cart-toast" class="mt-3 bg-green-50 border border-green-200 rounded-xl p-3 text-xs text-green-700 font-bold hidden items-center gap-2">
                ✓ Produk berhasil ditambahkan ke keranjang!
            </div>
        @else
            <a href="/login" class="block mb-2">
                <button class="btn-primary w-full justify-center shadow-gold hover:shadow-gold-lg transition-all duration-200">+ Keranjang</button>
            </a>
        @endauth
        </div>

    </div>

    {{-- REVIEWS --}}
    <div class="max-w-[1140px] mx-auto px-6 pb-12 mt-10">
        <div class="card p-7">
            <h2 class="font-heading text-2xl font-bold text-brown-dark mb-1">Ulasan Produk</h2>
            <p class="text-sm text-text-secondary mb-5">{{ $product->reviews->count() }} ulasan</p>
            <div class="border-t border-cream-border my-5"></div>

            @forelse($product->reviews as $review)
            <div class="bg-cream-warm rounded-xl border border-cream-border p-4 mb-3">
                <div class="flex justify-between items-start mb-3">
                    <div>
                        <p class="font-bold text-sm text-brown-dark">{{ $review->user->name ?? 'Pelanggan' }}</p>
                        <p class="text-[11px] text-text-muted mt-0.5">{{ $review->created_at?->format('d M Y, H:i') }}</p>
                    </div>
                    <div class="text-amber-400 text-sm">
                        {{ str_repeat('★', (int) $review->rating) }}{{ str_repeat('☆', 5 - (int) $review->rating) }}
                    </div>
                </div>
                <p class="text-sm text-text-secondary leading-relaxed mb-3">{{ $review->comment }}</p>
                @if($review->images->isNotEmpty())
                <div class="flex flex-wrap gap-2">
                    @foreach($review->images as $image)
                    <img src="{{ asset('storage/' . $image->path) }}" class="w-20 h-20 object-cover rounded-lg border border-cream-border" alt="Gambar ulasan" loading="lazy">
                    @endforeach
                </div>
                @endif
            </div>
            @empty
            <div class="text-center py-12 text-text-secondary">
                <i class="fas fa-star text-4xl text-cream-dark mb-3 block"></i>
                <p class="text-sm">Belum ada ulasan untuk produk ini.</p>
            </div>
            @endforelse
        </div>
    </div>
</section>
@endsection

@push('scripts')
<script>
    function changeQty(delta) {
        const input = document.getElementById('qty');
        const hidden = document.getElementById('qty-hidden');
        let val = parseInt(input.value) + delta;
        const max = parseInt(input.max);
        if (val < 1) val = 1;
        if (val > max) val = max;
        input.value = val;
        hidden.value = val;
        updatePriceDisplay();
    }

    document.getElementById('qty').addEventListener('input', function() {
        let val = parseInt(this.value);
        if (isNaN(val) || val < 1) val = 1;
        if (val > parseInt(this.max)) val = parseInt(this.max);
        this.value = val;
        document.getElementById('qty-hidden').value = val;
        updatePriceDisplay();
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
                    '<span class="text-xs text-text-secondary">Rp' + basePrice.toLocaleString('id-ID') +
                    ' <span class="text-xs font-bold text-brown-dark">+ Rp' + extra.toLocaleString('id-ID') + '</span></span>' +
                    ' <span class="font-heading text-2xl font-bold text-primary" id="subtotal">= Rp' + total.toLocaleString('id-ID') + '</span>';
            } else {
                priceDisplay.innerHTML =
                    '<span class="font-heading text-2xl font-bold text-primary" id="subtotal">Rp' + total.toLocaleString('id-ID') + '</span>';
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

@auth
<script>
document.getElementById('add-to-cart-form')?.addEventListener('submit', async function(e) {
    e.preventDefault();
    const btn = document.getElementById('btn-add-cart');
    const toast = document.getElementById('cart-toast');
    const form = this;

    btn.disabled = true;
    btn.textContent = 'Menambahkan...';

    try {
        const resp = await fetch(form.action, {
            method: 'POST',
            headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': form.querySelector('[name=_token]').value },
            body: new FormData(form),
        });
        const data = await resp.json();

        if (data.success) {
            toast.style.display = 'flex';
            btn.textContent = '✓ Ditambahkan';
            btn.style.background = '#22C55E';

            const badge = document.getElementById('cart-badge');
            if (badge) {
                badge.textContent = data.cart_count;
                badge.style.display = 'flex';
            }

            setTimeout(() => {
                toast.style.display = 'none';
                btn.disabled = false;
                btn.textContent = '+ Keranjang';
                btn.style.background = '';
            }, 2500);
        }
    } catch {
        btn.disabled = false;
        btn.textContent = '+ Keranjang';
        form.submit();
    }
});
</script>
@endauth
@endpush
