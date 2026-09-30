@extends('layouts.main')

@section('title', 'Jagoan Kue - Form Pemesanan')

@php
    $items = [];
    if (isset($cartItems) && is_array($cartItems)) {
        $items = $cartItems;
    } elseif (isset($product)) {
        $items = [['product' => $product, 'quantity' => 1, 'note' => '']];
    }
    $subtotal = collect($items)->sum(function($i) {
        $extraPrice = isset($i['customizationOptions'])
            ? $i['customizationOptions']->sum(fn($o) => $o->extra_price ?? 0)
            : 0;
        return ($i['product']->price + $extraPrice) * $i['quantity'];
    });
    $leadDays = config('app.lead_time_days', 2);
    $minDate  = now()->addDays($leadDays)->format('Y-m-d');
    $shippingZones = \App\Models\ShippingZone::where('is_available', true)->orderBy('area_name')->get();
@endphp

@section('content')
<div class="bg-cream min-h-screen py-8 px-6">
    <div class="max-w-[1140px] mx-auto">
        <div class="text-xs text-text-secondary mb-4">
            <a href="/" class="hover:text-primary transition-colors">Beranda</a> /
            <a href="/products" class="hover:text-primary transition-colors">Katalog</a> /
            <span class="text-brown-dark font-semibold">Form Pemesanan</span>
        </div>

        <div class="flex items-center justify-center gap-0 mb-8 max-w-xl mx-auto mt-6">
            <!-- Step 1 -->
            <div class="flex flex-col items-center">
                <div class="w-9 h-9 rounded-full flex items-center justify-center text-sm font-bold bg-green-500 text-white">✓</div>
                <span class="text-xs font-semibold mt-1 text-center text-brown-dark">Pilih Kue</span>
            </div>
            
            <!-- Connector 1 -->
            <div class="flex-1 w-16 sm:w-24 h-0.5 bg-primary mb-4"></div>

            <!-- Step 2 -->
            <div class="flex flex-col items-center">
                <div class="w-9 h-9 rounded-full flex items-center justify-center text-sm font-bold bg-primary text-white shadow-gold">2</div>
                <span class="text-xs font-semibold mt-1 text-center text-brown-dark">Detail Pesanan</span>
            </div>

            <!-- Connector 2 -->
            <div class="flex-1 w-16 sm:w-24 h-0.5 bg-cream-border mb-4"></div>

            <!-- Step 3 -->
            <div class="flex flex-col items-center">
                <div class="w-9 h-9 rounded-full flex items-center justify-center text-sm font-bold bg-cream-dark text-brown-light">3</div>
                <span class="text-xs font-semibold mt-1 text-center text-text-muted">Pembayaran</span>
            </div>

            <!-- Connector 3 -->
            <div class="flex-1 w-16 sm:w-24 h-0.5 bg-cream-border mb-4"></div>

            <!-- Step 4 -->
            <div class="flex flex-col items-center">
                <div class="w-9 h-9 rounded-full flex items-center justify-center text-sm font-bold bg-cream-dark text-brown-light">4</div>
                <span class="text-xs font-semibold mt-1 text-center text-text-muted">Konfirmasi</span>
            </div>
        </div>

        <form action="/orders" method="POST" id="checkoutForm">
        @csrf

        @if($errors->any())
        <div class="bg-red-50 text-red-700 border border-red-200 rounded-xl p-4 text-sm mb-6">
            @foreach($errors->all() as $error)
                <p>{{ $error }}</p>
            @endforeach
        </div>
        @endif
        
        <div id="js-errors" style="display:none;" class="bg-red-50 text-red-700 border border-red-200 rounded-xl p-4 text-sm mb-6">
            <div id="js-errors-inner"></div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-[1fr_360px] gap-8 items-start">
            <div class="space-y-6">
                {{-- 1. Produk --}}
                <div class="bg-white rounded-2xl border border-cream-border p-6 shadow-sm">
                    <div class="flex items-center gap-3 mb-6">
                        <div class="w-7 h-7 rounded-full bg-primary text-white flex items-center justify-center font-bold text-sm">1</div>
                        <span class="font-heading text-xl font-bold text-brown-dark">Produk yang Dipesan</span>
                    </div>
                    @foreach($items as $idx => $item)
                    <div class="flex items-start gap-4 py-4 border-b border-cream-border last:border-0">
                        <img src="{{ $item['product']->image ? asset('storage/' . $item['product']->image) : 'https://images.unsplash.com/photo-1563729784474-d77dbb933a9e?w=200&q=80' }}"
                             alt="{{ $item['product']->name }}" class="w-16 h-16 rounded-xl object-cover shrink-0">
                        <div class="flex-1 min-w-0">
                            <h4 class="font-semibold text-sm text-brown-dark truncate">{{ $item['product']->name }}</h4>
                            @php
                                $itemExtraPrice = isset($item['customizationOptions']) ? $item['customizationOptions']->sum(fn($o) => $o->extra_price ?? 0) : 0;
                                $itemUnitPrice  = $item['product']->price + $itemExtraPrice;
                            @endphp
                            <div class="flex flex-col gap-0.5 mt-1">
                                <small class="text-xs text-text-secondary">Jumlah: {{ $item['quantity'] }}</small>
                                @if(!empty($item['customizationOptions']) && $item['customizationOptions']->isNotEmpty())
                                <small class="text-xs text-brown-light">Kustomisasi:
                                    {{ $item['customizationOptions']->map(fn($o) => $o->name)->join(', ') }}
                                </small>
                                @endif
                                @if(!empty($item['note']))
                                <small class="text-xs text-text-secondary font-medium">Catatan: {{ $item['note'] }}</small>
                                @endif
                            </div>
                        </div>
                        <div class="text-right shrink-0">
                            <span class="font-bold text-sm text-primary">Rp {{ number_format($itemUnitPrice, 0, ',', '.') }}</span>
                            @if($itemExtraPrice > 0)
                            <div class="text-[10px] text-text-muted mt-0.5">(+Rp {{ number_format($itemExtraPrice, 0, ',', '.') }} kustomisasi)</div>
                            @endif
                        </div>
                    </div>
                    <input type="hidden" name="items[{{ $idx }}][product_id]" value="{{ $item['product']->id }}">
                    <input type="hidden" name="items[{{ $idx }}][quantity]"   value="{{ $item['quantity'] }}">
                    <input type="hidden" name="items[{{ $idx }}][note]"       value="{{ $item['note'] ?? '' }}">
                    <input type="hidden" name="items[{{ $idx }}][customizations]" value="{{ json_encode($item['customizations'] ?? []) }}">
                    @endforeach
                </div>

                {{-- 2. Metode Pengiriman --}}
                <div class="bg-white rounded-2xl border border-cream-border p-6 shadow-sm">
                    <div class="flex items-center gap-3 mb-6">
                        <div class="w-7 h-7 rounded-full bg-primary text-white flex items-center justify-center font-bold text-sm">2</div>
                        <span class="font-heading text-xl font-bold text-brown-dark">Metode Pengiriman</span>
                    </div>
                    
                    <div class="grid grid-cols-2 gap-4 mb-6">
                        <label class="delivery-option selected" id="opt-delivery" onclick="setDelivery('delivery')">
                            <input type="radio" name="delivery_method" value="delivery" class="hidden" checked>
                            <i class="fas fa-truck text-lg"></i>
                            <p class="font-bold text-sm text-brown-dark">Kirim ke Alamat</p>
                            <small class="text-xs text-text-secondary">Ongkir sesuai zona</small>
                        </label>
                        <label class="delivery-option" id="opt-pickup" onclick="setDelivery('pickup')">
                            <input type="radio" name="delivery_method" value="pickup" class="hidden">
                            <i class="fas fa-store text-lg"></i>
                            <p class="font-bold text-sm text-brown-dark">Ambil di Toko</p>
                            <small class="text-xs text-text-secondary">Gratis, ambil sendiri</small>
                        </label>
                    </div>

                    <div id="address-section">
                        {{-- Alamat Tersimpan --}}
                        @if(isset($savedAddresses) && $savedAddresses->isNotEmpty())
                        <label class="field-label">Pilih Alamat Tersimpan</label>
                        <select class="field-input" id="savedAddressSelect" onchange="fillSavedAddress(this)">
                            <option value="">-- Isi manual / alamat baru --</option>
                            @foreach($savedAddresses as $addr)
                            <option value="{{ $addr->id }}"
                                    data-name="{{ $addr->recipient_name }}"
                                    data-phone="{{ $addr->phone }}"
                                    data-address="{{ $addr->full_address }}"
                                    data-city="{{ $addr->city }}"
                                    {{ $addr->is_default ? 'selected' : '' }}>
                                {{ $addr->label }} — {{ $addr->recipient_name }} ({{ $addr->city }})
                            </option>
                            @endforeach
                        </select>
                        <div class="text-right mt-1.5 mb-4">
                            <a href="{{ route('account.addresses.index') }}" target="_blank"
                               class="text-xs font-semibold text-primary hover:text-primary-hover transition-colors">+ Tambah alamat baru</a>
                        </div>
                        @endif

                        <label class="field-label">Nama Penerima</label>
                        <input type="text" name="recipient_name" id="fieldName" class="field-input"
                               value="{{ old('recipient_name', auth()->user()->name) }}" placeholder="Nama penerima">

                        <label class="field-label">No. Telepon Penerima</label>
                        <input type="text" name="phone" id="fieldPhone" class="field-input"
                               value="{{ old('phone') }}" placeholder="08123456789">

                        <label class="field-label">Alamat Lengkap</label>
                        <textarea name="shipping_address" id="fieldAddress" class="field-textarea resize-none" rows="3"
                                  placeholder="Jl. Imam Bonjol No. 10, RT 01/RW 02...">{{ old('shipping_address') }}</textarea>

                        <label class="field-label">Kota / Kabupaten Tujuan <span class="text-red-500">*</span></label>
                        @php
                            $kotaZones = $shippingZones->filter(fn($z) => str_starts_with($z->area_name, 'Kota'));
                            $kabZones  = $shippingZones->filter(fn($z) => str_starts_with($z->area_name, 'Kabupaten'));
                        @endphp
                        <select name="shipping_zone_id" class="field-input" id="zoneSelect" onchange="updateShipping()" required>
                            <option value="">-- Pilih kota/kabupaten tujuan --</option>
                            @if($kotaZones->isNotEmpty())
                            <optgroup label="── Kota ──">
                                @foreach($kotaZones as $zone)
                                <option value="{{ $zone->id }}" data-cost="{{ $zone->cost }}"
                                        @selected(old('shipping_zone_id') == $zone->id)>
                                    {{ $zone->area_name }} — Rp {{ number_format($zone->cost, 0, ',', '.') }}
                                </option>
                                @endforeach
                            </optgroup>
                            @endif
                            @if($kabZones->isNotEmpty())
                            <optgroup label="── Kabupaten ──">
                                @foreach($kabZones as $zone)
                                <option value="{{ $zone->id }}" data-cost="{{ $zone->cost }}"
                                        @selected(old('shipping_zone_id') == $zone->id)>
                                    {{ $zone->area_name }} — Rp {{ number_format($zone->cost, 0, ',', '.') }}
                                </option>
                                @endforeach
                            </optgroup>
                            @endif
                        </select>
                        <p class="text-[11px] text-text-muted mt-1.5">Ongkir dihitung berdasarkan kota/kabupaten tujuan pengiriman</p>
                    </div>

                    <div id="pickup-section" style="display:none;">
                        <div class="bg-cream-warm border border-cream-border rounded-xl p-4 text-sm text-brown-mid">
                            <div class="flex items-center gap-2 mb-2 font-bold text-brown-dark">
                                <i class="fas fa-map-marker-alt text-primary"></i>
                                <span>Alamat Toko:</span>
                            </div>
                            <p class="font-semibold">Jl. Contoh No. 1, Jakarta Selatan</p>
                            <p class="text-xs text-text-muted mt-1">Buka: Senin–Sabtu, 08.00–18.00</p>
                        </div>
                    </div>
                </div>

                {{-- 3. Tanggal & Slot Waktu --}}
                <div class="bg-white rounded-2xl border border-cream-border p-6 shadow-sm">
                    <div class="flex items-center gap-3 mb-6">
                        <div class="w-7 h-7 rounded-full bg-primary text-white flex items-center justify-center font-bold text-sm">3</div>
                        <span class="font-heading text-xl font-bold text-brown-dark">Jadwal Pengiriman / Pengambilan</span>
                    </div>

                    <label class="field-label">
                        Tanggal
                        <span class="text-xs text-text-muted font-normal">(minimal {{ $leadDays }} hari ke depan)</span>
                    </label>
                    <input type="date" name="delivery_date" id="delivery_date" class="field-input"
                           min="{{ $minDate }}" value="{{ old('delivery_date', $minDate) }}" required
                           oninput="validateDeliveryDate(this)">
                    <p id="date-error" style="display:none;" class="text-red-600 text-xs font-semibold mt-1">
                        Tanggal pengiriman minimal {{ $leadDays }} hari setelah tanggal pemesanan.
                    </p>

                    <label class="field-label">Slot Waktu</label>
                    <div class="grid grid-cols-3 gap-3">
                        @php $slots = ['08:00-11:00' => 'Pagi', '11:00-14:00' => 'Siang', '14:00-18:00' => 'Sore']; @endphp
                        @foreach($slots as $value => $label)
                        <label class="slot-option {{ old('delivery_slot') === $value ? 'selected' : '' }}"
                               onclick="selectSlot(this)">
                            <input type="radio" name="delivery_slot" value="{{ $value }}" class="hidden"
                                   {{ old('delivery_slot') === $value ? 'checked' : '' }}>
                            <p class="font-bold text-sm text-brown-dark">{{ $label }}</p>
                            <small class="text-xs text-text-secondary">{{ $value }}</small>
                        </label>
                        @endforeach
                    </div>
                </div>

                {{-- 4. Catatan --}}
                <div class="bg-white rounded-2xl border border-cream-border p-6 shadow-sm">
                    <div class="flex items-center gap-3 mb-6">
                        <div class="w-7 h-7 rounded-full bg-primary text-white flex items-center justify-center font-bold text-sm">4</div>
                        <span class="font-heading text-xl font-bold text-brown-dark">Catatan Pesanan</span>
                    </div>
                    <label class="field-label">Catatan untuk toko (opsional, maks. 300 karakter)</label>
                    <textarea name="notes" class="field-textarea resize-none" rows="3"
                              placeholder="Contoh: tolong tambahkan lilin, warna biru..." maxlength="300">{{ old('notes') }}</textarea>
                </div>

                {{-- 5. Pembayaran --}}
                <div class="bg-white rounded-2xl border border-cream-border p-6 shadow-sm">
                    <div class="flex items-center gap-3 mb-6">
                        <div class="w-7 h-7 rounded-full bg-primary text-white flex items-center justify-center font-bold text-sm">5</div>
                        <span class="font-heading text-xl font-bold text-brown-dark">Metode Pembayaran</span>
                    </div>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <label class="payment-option">
                            <input type="radio" name="payment_method" value="transfer_bank" checked class="accent-primary w-4 h-4">
                            <div class="w-8 h-8 rounded-lg bg-[#006CB0] text-white flex items-center justify-center text-xs shrink-0"><i class="fas fa-university"></i></div>
                            <div class="text-left"><p class="font-semibold text-xs text-brown-dark">Transfer Bank</p><small class="text-[10px] text-text-secondary">BCA / BNI / dll</small></div>
                        </label>
                        <label class="payment-option">
                            <input type="radio" name="payment_method" value="ewallet" class="accent-primary w-4 h-4">
                            <div class="w-8 h-8 rounded-lg bg-[#00B14F] text-white flex items-center justify-center text-xs shrink-0"><i class="fas fa-wallet"></i></div>
                            <div class="text-left"><p class="font-semibold text-xs text-brown-dark">E-wallet</p><small class="text-[10px] text-text-secondary">GoPay / OVO / dll</small></div>
                        </label>
                        <label class="payment-option">
                            <input type="radio" name="payment_method" value="qris" class="accent-primary w-4 h-4">
                            <div class="w-8 h-8 rounded-lg bg-[#7C3AED] text-white flex items-center justify-center text-xs shrink-0"><i class="fas fa-qrcode"></i></div>
                            <div class="text-left"><p class="font-semibold text-xs text-brown-dark">QRIS</p><small class="text-[10px] text-text-secondary">Scan & bayar</small></div>
                        </label>
                        <label class="payment-option">
                            <input type="radio" name="payment_method" value="cod" class="accent-primary w-4 h-4">
                            <div class="w-8 h-8 rounded-lg bg-gray-500 text-white flex items-center justify-center text-xs shrink-0"><i class="fas fa-motorcycle"></i></div>
                            <div class="text-left"><p class="font-semibold text-xs text-brown-dark">COD</p><small class="text-[10px] text-text-secondary">Bayar di tempat</small></div>
                        </label>
                    </div>
                </div>
            </div>

            {{-- RINGKASAN --}}
            <div class="bg-cream-warm rounded-2xl border border-cream-border p-5 shrink-0 shadow-sm">
                <p class="font-heading text-xl font-bold text-brown-dark mb-4">Ringkasan Pesanan</p>

                <div class="divide-y divide-cream-border max-h-[280px] overflow-y-auto pr-1 mb-4">
                @foreach($items as $item)
                <div class="flex items-start gap-3 py-3 first:pt-0">
                    <img src="{{ $item['product']->image ? asset('storage/' . $item['product']->image) : 'https://images.unsplash.com/photo-1563729784474-d77dbb933a9e?w=200&q=80' }}"
                         alt="" class="w-12 h-12 rounded-lg object-cover shrink-0">
                    <div class="flex-1 min-w-0">
                        <p class="font-semibold text-xs text-brown-dark truncate">{{ $item['product']->name }}</p>
                        <small class="text-[10px] text-text-secondary">{{ $item['quantity'] }}x</small>
                    </div>
                    <span class="font-bold text-xs text-primary shrink-0">Rp {{ number_format((($item['product']->price + (isset($item['customizationOptions']) ? $item['customizationOptions']->sum(fn($o) => $o->extra_price ?? 0) : 0)) * $item['quantity']), 0, ',', '.') }}</span>
                </div>
                @endforeach
                </div>

                <div class="space-y-2 mb-4 border-t border-cream-border pt-4">
                    <div class="flex justify-between items-center text-xs text-text-secondary">
                        <span>Subtotal</span>
                        <span class="font-semibold text-brown-dark">Rp {{ number_format($subtotal, 0, ',', '.') }}</span>
                    </div>
                    <div class="flex justify-between items-center text-xs text-text-secondary" id="row-shipping">
                        <span>Ongkir</span>
                        <span id="val-shipping" class="font-semibold text-brown-dark">Pilih kota tujuan</span>
                    </div>
                    <div class="flex justify-between items-center text-xs text-green-700" id="row-discount" style="display:none;">
                        <span>Diskon Voucher</span>
                        <span id="val-discount" class="font-bold">-Rp 0</span>
                    </div>
                </div>

                {{-- Voucher --}}
                <div class="border-t border-cream-border pt-4 mb-4">
                    <label class="block text-xs font-semibold text-brown-mid mb-1.5">Kode Voucher (opsional)</label>
                    <div class="flex gap-2">
                        <input type="text" id="voucherInput" placeholder="Masukkan kode"
                               class="flex-1 input-field uppercase py-2 px-3 text-xs">
                        <button type="button" class="btn-secondary py-2 px-4 text-xs" onclick="applyVoucher()">Pakai</button>
                    </div>
                    <input type="hidden" name="voucher_code" id="voucherCode">
                    <div id="voucherMsg" class="voucher-msg"></div>
                </div>

                <div class="flex justify-between items-center pt-3 border-t border-cream-border font-bold text-brown-dark mb-4">
                    <span>Total</span>
                    <span id="val-total" class="text-xl text-primary font-extrabold">Rp {{ number_format($subtotal, 0, ',', '.') }}</span>
                </div>

                {{-- Opsi DP --}}
                @php $dpMin = $dpMinAmount ?? config('app.dp_min_amount', 200000); $dpPct = $dpPercentage ?? config('app.dp_percentage', 50); @endphp
                <div id="dp-section" style="display:none;" class="bg-white rounded-xl border border-cream-border p-4 mb-4">
                    <label class="flex items-start gap-2.5 cursor-pointer">
                        <input type="checkbox" name="use_dp" id="useDpCheck" value="1" class="mt-1 rounded border-cream-border accent-primary">
                        <div>
                            <p class="text-xs font-bold text-brown-dark mb-0.5">
                                Bayar DP {{ $dpPct }}% Sekarang
                            </p>
                            <p class="text-[10px] text-text-secondary leading-relaxed">
                                Bayar Rp <span id="dp-amount-display">0</span> sekarang, sisanya sebelum pengiriman.
                            </p>
                        </div>
                    </label>
                </div>

                <button type="submit" id="submitBtn" class="btn-primary w-full py-3.5 text-sm font-bold justify-center" onclick="return validateCheckout()">
                    Lanjutkan Ke Pembayaran →
                </button>
            </div>
        </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
const subtotal = {{ $subtotal }};
const DEFAULT_SHIPPING = 0;
let shippingCost = DEFAULT_SHIPPING;
let discountAmt  = 0;

function setDelivery(method) {
    document.querySelectorAll('.delivery-option').forEach(el => el.classList.remove('selected'));
    document.getElementById('opt-' + method).classList.add('selected');
    document.querySelector('[name=delivery_method][value=' + method + ']').checked = true;

    const zoneSelect = document.getElementById('zoneSelect');

    if (method === 'pickup') {
        document.getElementById('address-section').style.display = 'none';
        document.getElementById('pickup-section').style.display  = 'block';
        shippingCost = 0;
        document.getElementById('val-shipping').textContent = 'Gratis';
        // Nonaktifkan required agar tidak divalidasi browser
        if (zoneSelect) {
            zoneSelect.required = false;
            zoneSelect.disabled = true;
        }
        document.querySelectorAll('#address-section input, #address-section textarea').forEach(el => {
            el.required = false;
        });
    } else {
        document.getElementById('address-section').style.display = 'block';
        document.getElementById('pickup-section').style.display  = 'none';
        // Aktifkan kembali required
        if (zoneSelect) {
            zoneSelect.required = true;
            zoneSelect.disabled = false;
        }
        updateShipping();
    }
    recalc();
}

function updateShipping() {
    const sel = document.getElementById('zoneSelect');
    if (!sel) return;
    const opt = sel.options[sel.selectedIndex];
    if (opt && opt.value && opt.dataset.cost) {
        shippingCost = parseFloat(opt.dataset.cost);
        sel.style.borderColor = '';
    } else {
        shippingCost = DEFAULT_SHIPPING;
    }
    document.getElementById('val-shipping').textContent = shippingCost > 0 ? 'Rp ' + fmt(shippingCost) : 'Gratis';
    recalc();
}

function selectSlot(label) {
    document.querySelectorAll('.slot-option').forEach(el => el.classList.remove('selected'));
    label.classList.add('selected');
    label.querySelector('input').checked = true;
}

const dpMinAmount  = {{ $dpMin }};
const dpPercentage = {{ $dpPct }};

function recalc() {
    const total = Math.max(0, subtotal + shippingCost - discountAmt);
    document.getElementById('val-total').textContent = 'Rp ' + fmt(total);

    // Show DP section if total qualifies
    const dpSection = document.getElementById('dp-section');
    if (dpSection) {
        dpSection.style.display = total >= dpMinAmount ? 'block' : 'none';
        const dpAmt = Math.round(total * dpPercentage / 100);
        const el = document.getElementById('dp-amount-display');
        if (el) el.textContent = fmt(dpAmt);
    }
}

function fmt(n) { return Math.round(n).toLocaleString('id-ID'); }

function validateDeliveryDate(input) {
    const errEl = document.getElementById('date-error');
    if (input.value && input.value < input.min) {
        errEl.style.display = 'block';
        input.value = input.min;
        setTimeout(() => { errEl.style.display = 'none'; }, 3000);
    } else {
        errEl.style.display = 'none';
    }
}

function fillSavedAddress(sel) {
    const opt = sel.options[sel.selectedIndex];
    if (!opt || !opt.value) return;
    const name = document.getElementById('fieldName');
    const phone = document.getElementById('fieldPhone');
    const addr = document.getElementById('fieldAddress');
    if (name)  name.value  = opt.dataset.name  || '';
    if (phone) phone.value = opt.dataset.phone || '';
    if (addr)  addr.value  = opt.dataset.address || '';

    // Sinkronkan kota/kabupaten ke zoneSelect
    const city = (opt.dataset.city || '').trim().toLowerCase();
    if (city) {
        const zoneSelect = document.getElementById('zoneSelect');
        if (zoneSelect) {
            let matched = false;
            for (const option of zoneSelect.options) {
                const areaName = option.textContent.split('—')[0].trim().toLowerCase();
                if (areaName === city || areaName.includes(city) || city.includes(areaName)) {
                    zoneSelect.value = option.value;
                    matched = true;
                    break;
                }
            }
            if (matched) updateShipping();
        }
    }
}

function validateCheckout() {
    const method   = document.querySelector('[name=delivery_method]:checked')?.value;
    const date     = document.querySelector('[name=delivery_date]')?.value;
    const slot     = document.querySelector('[name=delivery_slot]:checked');
    const errors   = [];

    if (!date) errors.push('Tanggal pengiriman wajib dipilih.');
    if (!slot) errors.push('Slot waktu wajib dipilih.');

    if (method === 'delivery') {
        const addr = document.querySelector('[name=shipping_address]')?.value?.trim();
        if (!addr) errors.push('Alamat pengiriman wajib diisi.');

        const zone = document.getElementById('zoneSelect');
        if (zone && !zone.value) {
            errors.push('Zona pengiriman wajib dipilih.');
            zone.style.borderColor = '#DC2626';
            zone.focus();
        }
    }

    if (errors.length > 0) {
        const box = document.getElementById('js-errors');
        const inner = document.getElementById('js-errors-inner');
        inner.innerHTML = errors.map(e => '<p>' + e + '</p>').join('');
        box.style.display = 'block';
        box.scrollIntoView({ behavior: 'smooth', block: 'center' });
        return false;
    }
    document.getElementById('js-errors').style.display = 'none';
    return true;
}

// Auto-fill default address on load
window.addEventListener('DOMContentLoaded', () => {
    const sel = document.getElementById('savedAddressSelect');
    if (sel && sel.value) fillSavedAddress(sel);

    // Sinkronkan state delivery method saat halaman dimuat
    const currentMethod = document.querySelector('[name=delivery_method]:checked')?.value ?? 'delivery';
    setDelivery(currentMethod);
});

async function applyVoucher() {
    const code = document.getElementById('voucherInput').value.trim().toUpperCase();
    const msg  = document.getElementById('voucherMsg');
    if (!code) { msg.textContent = ''; return; }

    try {
        const resp = await fetch('/voucher/apply', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ code, amount: subtotal }),
        });
        const data = await resp.json();

        if (data.valid) {
            discountAmt = data.discount;
            document.getElementById('voucherCode').value = code;
            document.getElementById('val-discount').textContent = '-Rp ' + fmt(discountAmt);
            document.getElementById('row-discount').style.display = 'flex';
            msg.className = 'voucher-msg ok';
            msg.textContent = '✓ Voucher berhasil! Hemat Rp ' + fmt(discountAmt);
        } else {
            discountAmt = 0;
            document.getElementById('voucherCode').value = '';
            document.getElementById('row-discount').style.display = 'none';
            msg.className = 'voucher-msg err';
            msg.textContent = data.message || 'Kode voucher tidak valid.';
        }
    } catch {
        msg.className = 'voucher-msg err';
        msg.textContent = 'Gagal menghubungi server.';
    }
    recalc();
}
</script>
<script src="{{ asset('js/app.js') }}" defer></script>
@endpush
