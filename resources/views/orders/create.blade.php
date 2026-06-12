<!DOCTYPE html>
<html lang="id">
<head>
    <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}" />
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Jagoan Kue - Form Pemesanan</title>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700;800&family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    <style>
        body { background-color: var(--cream); }
        .breadcrumb { max-width: 1100px; margin: 0 auto; padding: 20px 24px 0; font-size: 14px; color: var(--gray); }
        .stepper-wrap { max-width: 1100px; margin: 0 auto; padding: 28px 24px; }
        .stepper { display: flex; align-items: center; justify-content: center; }
        .step { display: flex; flex-direction: column; align-items: center; gap: 8px; }
        .step-circle { width: 52px; height: 52px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 18px; font-weight: 700; border: 2px solid var(--pink); color: var(--pink); background: var(--white); }
        .step-circle.active { background: var(--pink); color: white; }
        .step-circle.done { background: var(--pink); color: white; }
        .step-label { font-size: 13px; font-weight: 500; }
        .step-line { flex: 1; height: 2px; background: #E5C5CF; margin-bottom: 24px; max-width: 120px; }
        .step-line.done { background: var(--pink); }
        .checkout-layout { max-width: 1100px; margin: 0 auto; padding: 0 24px 60px; display: grid; grid-template-columns: 1fr 380px; gap: 24px; align-items: start; }
        .card { background: var(--white); border-radius: 16px; padding: 24px; margin-bottom: 16px; border: 1px solid #EDE0D4; }
        .card-title { display: flex; align-items: center; gap: 10px; margin-bottom: 20px; }
        .card-num { width: 28px; height: 28px; border-radius: 50%; background: var(--pink); color: white; font-size: 13px; font-weight: 700; display: flex; align-items: center; justify-content: center; flex-shrink: 0; }
        .card-title span { font-size: 15px; font-weight: 700; color: var(--brown-dark); }
        .produk-item { display: flex; align-items: center; gap: 14px; background: var(--cream); border-radius: 10px; padding: 12px; margin-bottom: 12px; }
        .produk-item img { width: 64px; height: 64px; object-fit: cover; border-radius: 8px; }
        .produk-item h4 { font-size: 15px; font-weight: 700; }
        .produk-item p { font-size: 14px; font-weight: 600; color: var(--brown-dark); margin-top: 4px; }
        .produk-item small { font-size: 12px; color: var(--gray); }
        .field-label { font-size: 13px; font-weight: 500; margin-bottom: 6px; display: block; }
        .field-input { width: 100%; background: var(--brown-dark); color: white; border: none; border-radius: 8px; padding: 10px 14px; font-size: 13px; font-family: 'Plus Jakarta Sans', sans-serif; outline: none; margin-bottom: 14px; }
        .field-input::placeholder { color: rgba(255,255,255,0.5); }
        .field-input option { background: var(--brown-dark); }
        .field-textarea { width: 100%; background: var(--brown-dark); color: white; border: none; border-radius: 8px; padding: 10px 14px; font-size: 13px; font-family: 'Plus Jakarta Sans', sans-serif; outline: none; resize: none; margin-bottom: 14px; }
        .field-textarea::placeholder { color: rgba(255,255,255,0.5); }
        .delivery-toggle { display: flex; gap: 12px; margin-bottom: 16px; }
        .delivery-option { flex: 1; border: 2px solid #D1C0B8; border-radius: 12px; padding: 14px; cursor: pointer; text-align: center; transition: all 0.2s; }
        .delivery-option input { display: none; }
        .delivery-option.selected { border-color: var(--pink); background: #FFF5F7; }
        .delivery-option i { font-size: 22px; color: var(--pink); margin-bottom: 6px; display: block; }
        .delivery-option p { font-size: 13px; font-weight: 700; }
        .delivery-option small { font-size: 11px; color: var(--gray); }
        .slot-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 10px; margin-bottom: 14px; }
        .slot-option { border: 1.5px solid #D1C0B8; border-radius: 10px; padding: 10px; cursor: pointer; text-align: center; transition: all 0.2s; }
        .slot-option input { display: none; }
        .slot-option.selected { border-color: var(--pink); background: #FFF5F7; }
        .slot-option p { font-size: 12px; font-weight: 600; }
        .slot-option small { font-size: 11px; color: var(--gray); }
        .payment-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 12px; }
        .payment-option { display: flex; align-items: center; gap: 10px; border: 1.5px solid #D1C0B8; border-radius: 10px; padding: 12px; cursor: pointer; transition: all 0.2s; }
        .payment-option:has(input:checked) { border-color: var(--brown-dark); background: var(--cream-dark); }
        .payment-option input { accent-color: var(--brown-dark); }
        .payment-logo { width: 36px; height: 36px; border-radius: 6px; display: flex; align-items: center; justify-content: center; font-size: 10px; font-weight: 800; color: white; flex-shrink: 0; }
        .payment-info p { font-size: 13px; font-weight: 600; }
        .payment-info small { font-size: 11px; color: var(--gray); }
        .summary-card { background: var(--white); border-radius: 16px; padding: 24px; border: 1px solid #EDE0D4; position: sticky; top: 90px; }
        .summary-title { font-size: 16px; font-weight: 700; margin-bottom: 16px; }
        .summary-item { display: flex; align-items: center; gap: 12px; padding-bottom: 12px; border-bottom: 1px solid #F0E8E0; margin-bottom: 12px; }
        .summary-item img { width: 52px; height: 52px; object-fit: cover; border-radius: 8px; }
        .summary-item-info { flex: 1; }
        .summary-item-info p { font-size: 13px; font-weight: 600; }
        .summary-item-info small { font-size: 12px; color: var(--gray); }
        .summary-item-price { font-size: 13px; font-weight: 600; }
        .summary-row { display: flex; justify-content: space-between; font-size: 13px; margin-bottom: 10px; }
        .summary-row span:first-child { color: var(--gray); }
        .summary-row span:last-child { font-weight: 600; }
        .summary-total { display: flex; justify-content: space-between; font-size: 14px; font-weight: 700; padding-top: 12px; border-top: 1.5px solid #EDE0D4; margin-bottom: 16px; }
        .summary-total span:last-child { color: var(--pink); font-size: 16px; }
        .btn-lanjut { width: 100%; background: var(--brown-dark); color: white; border: none; border-radius: 10px; padding: 14px; font-size: 14px; font-weight: 700; cursor: pointer; font-family: 'Plus Jakarta Sans', sans-serif; }
        .btn-lanjut:hover { opacity: 0.85; }
        .alert-error { background: #FEE2E2; border: 1px solid #FECACA; border-radius: 10px; padding: 12px 16px; font-size: 13px; color: #B91C1C; margin-bottom: 16px; }
        .voucher-wrap { display: flex; gap: 8px; margin-bottom: 8px; }
        .voucher-input { flex: 1; border: 1.5px solid #D1C0B8; border-radius: 8px; padding: 9px 12px; font-size: 13px; font-family: 'Plus Jakarta Sans', sans-serif; outline: none; }
        .voucher-input:focus { border-color: var(--pink); }
        .btn-voucher { background: var(--pink); color: white; border: none; border-radius: 8px; padding: 9px 16px; font-size: 12px; font-weight: 700; cursor: pointer; font-family: 'Plus Jakarta Sans', sans-serif; white-space: nowrap; }
        .voucher-msg { font-size: 12px; margin-bottom: 10px; }
        .voucher-msg.ok { color: #059669; }
        .voucher-msg.err { color: #DC2626; }
        @media (max-width: 768px) { .checkout-layout { grid-template-columns: 1fr; } .slot-grid { grid-template-columns: 1fr 1fr; } }
    </style>
</head>
<body>
@php
    $items = [];
    if (isset($cartItems) && is_array($cartItems)) {
        $items = $cartItems;
    } elseif (isset($product)) {
        $items = [['product' => $product, 'quantity' => 1, 'note' => '']];
    }
    $subtotal = collect($items)->sum(fn($i) => $i['product']->price * $i['quantity']);
    $leadDays = config('app.lead_time_days', 2);
    $minDate  = now()->addDays($leadDays)->format('Y-m-d');
    $shippingZones = \App\Models\ShippingZone::where('is_available', true)->orderBy('area_name')->get();
@endphp

@include('partials.navbar')

<div class="breadcrumb">
    <a href="/" style="color:var(--gray);">Beranda</a> /
    <a href="/products" style="color:var(--gray);">Katalog</a> /
    <span style="color:var(--text-dark);font-weight:600;">Form Pemesanan</span>
</div>

<div class="stepper-wrap"><div class="stepper">
    <div class="step"><div class="step-circle done">✓</div><span class="step-label">Pilih Kue</span></div>
    <div class="step-line done"></div>
    <div class="step"><div class="step-circle active">2</div><span class="step-label">Detail Pesanan</span></div>
    <div class="step-line"></div>
    <div class="step"><div class="step-circle">3</div><span class="step-label">Pembayaran</span></div>
    <div class="step-line"></div>
    <div class="step"><div class="step-circle">4</div><span class="step-label">Konfirmasi</span></div>
</div></div>

<form action="/orders" method="POST" id="checkoutForm">
@csrf

@if($errors->any())
<div style="max-width:1100px;margin:0 auto;padding:0 24px;">
    <div class="alert-error">@foreach($errors->all() as $error)<p>{{ $error }}</p>@endforeach</div>
</div>
@endif
<div id="js-errors" style="display:none;max-width:1100px;margin:0 auto;padding:0 24px 0;">
    <div class="alert-error" id="js-errors-inner"></div>
</div>

<div class="checkout-layout">
    <div>
        {{-- 1. Produk --}}
        <div class="card">
            <div class="card-title"><div class="card-num">1</div><span>Produk yang Dipesan</span></div>
            @foreach($items as $idx => $item)
            <div class="produk-item">
                <img src="{{ $item['product']->image ? asset('storage/' . $item['product']->image) : 'https://images.unsplash.com/photo-1563729784474-d77dbb933a9e?w=200&q=80' }}"
                     alt="{{ $item['product']->name }}" loading="lazy">
                <div>
                    <h4>{{ $item['product']->name }}</h4>
                    <p>Rp {{ number_format($item['product']->price, 0, ',', '.') }}</p>
                    <small>Jumlah: {{ $item['quantity'] }}</small>
                    @if(!empty($item['note']))<small style="display:block;color:var(--pink);">Catatan: {{ $item['note'] }}</small>@endif
                </div>
            </div>
            <input type="hidden" name="items[{{ $idx }}][product_id]" value="{{ $item['product']->id }}">
            <input type="hidden" name="items[{{ $idx }}][quantity]"   value="{{ $item['quantity'] }}">
            <input type="hidden" name="items[{{ $idx }}][note]"       value="{{ $item['note'] ?? '' }}">
            @endforeach
        </div>

        {{-- 2. Metode Pengiriman --}}
        <div class="card">
            <div class="card-title"><div class="card-num">2</div><span>Metode Pengiriman</span></div>
            <div class="delivery-toggle">
                <label class="delivery-option selected" id="opt-delivery" onclick="setDelivery('delivery')">
                    <input type="radio" name="delivery_method" value="delivery" checked>
                    <i class="fas fa-truck"></i>
                    <p>Kirim ke Alamat</p>
                    <small>Ongkir sesuai zona</small>
                </label>
                <label class="delivery-option" id="opt-pickup" onclick="setDelivery('pickup')">
                    <input type="radio" name="delivery_method" value="pickup">
                    <i class="fas fa-store"></i>
                    <p>Ambil di Toko</p>
                    <small>Gratis, ambil sendiri</small>
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
                <div style="text-align:right;margin-top:-10px;margin-bottom:14px;">
                    <a href="{{ route('account.addresses.index') }}" target="_blank"
                       style="font-size:11px;color:var(--pink);">+ Tambah alamat baru</a>
                </div>
                @endif

                <label class="field-label">Nama Penerima</label>
                <input type="text" name="recipient_name" id="fieldName" class="field-input"
                       value="{{ old('recipient_name', auth()->user()->name) }}" placeholder="Nama penerima">

                <label class="field-label">No. Telepon Penerima</label>
                <input type="text" name="phone" id="fieldPhone" class="field-input"
                       value="{{ old('phone') }}" placeholder="08123456789">

                <label class="field-label">Alamat Lengkap</label>
                <textarea name="shipping_address" id="fieldAddress" class="field-textarea" rows="3"
                          placeholder="Jl. Imam Bonjol No. 10, RT 01/RW 02...">{{ old('shipping_address') }}</textarea>

                <label class="field-label">Kota / Kabupaten Tujuan <span style="color:#F9A8D4;">*</span></label>
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
                <p style="font-size:11px;color:var(--gray);margin-top:-10px;margin-bottom:14px;">Ongkir dihitung berdasarkan kota/kabupaten tujuan pengiriman</p>
            </div>

            <div id="pickup-section" style="display:none;">
                <div style="background:var(--cream);border-radius:10px;padding:14px;font-size:13px;">
                    <i class="fas fa-map-marker-alt" style="color:var(--pink);"></i>
                    <strong style="margin-left:6px;">Alamat Toko:</strong>
                    <p style="margin-top:6px;">Jl. Contoh No. 1, Jakarta Selatan</p>
                    <p style="color:var(--gray);margin-top:4px;">Buka: Senin–Sabtu, 08.00–18.00</p>
                </div>
            </div>
        </div>

        {{-- 3. Tanggal & Slot Waktu --}}
        <div class="card">
            <div class="card-title"><div class="card-num">3</div><span>Jadwal Pengiriman / Pengambilan</span></div>

            <label class="field-label">
                Tanggal
                <span style="color:var(--gray);font-weight:400;">(minimal {{ $leadDays }} hari ke depan)</span>
            </label>
            <input type="date" name="delivery_date" id="delivery_date" class="field-input"
                   min="{{ $minDate }}" value="{{ old('delivery_date', $minDate) }}" required
                   oninput="validateDeliveryDate(this)">
            <p id="date-error" style="display:none;color:#DC2626;font-size:12px;margin-top:-10px;margin-bottom:10px;">
                Tanggal pengiriman minimal {{ $leadDays }} hari setelah tanggal pemesanan.
            </p>

            <label class="field-label">Slot Waktu</label>
            <div class="slot-grid">
                @php $slots = ['08:00-11:00' => 'Pagi', '11:00-14:00' => 'Siang', '14:00-18:00' => 'Sore']; @endphp
                @foreach($slots as $value => $label)
                <label class="slot-option {{ old('delivery_slot') === $value ? 'selected' : '' }}"
                       onclick="selectSlot(this)">
                    <input type="radio" name="delivery_slot" value="{{ $value }}"
                           {{ old('delivery_slot') === $value ? 'checked' : '' }}>
                    <p>{{ $label }}</p>
                    <small>{{ $value }}</small>
                </label>
                @endforeach
            </div>
        </div>

        {{-- 4. Catatan --}}
        <div class="card">
            <div class="card-title"><div class="card-num">4</div><span>Catatan Pesanan</span></div>
            <label class="field-label">Catatan untuk toko (opsional, maks. 300 karakter)</label>
            <textarea name="notes" class="field-textarea" rows="3"
                      placeholder="Contoh: tolong tambahkan lilin, warna biru..." maxlength="300">{{ old('notes') }}</textarea>
        </div>

        {{-- 5. Pembayaran --}}
        <div class="card">
            <div class="card-title"><div class="card-num">5</div><span>Metode Pembayaran</span></div>
            <div class="payment-grid">
                <label class="payment-option">
                    <input type="radio" name="payment_method" value="transfer_bank" checked>
                    <div class="payment-logo" style="background:#006CB0;"><i class="fas fa-university"></i></div>
                    <div class="payment-info"><p>Transfer Bank</p><small>BCA / BNI / dll</small></div>
                </label>
                <label class="payment-option">
                    <input type="radio" name="payment_method" value="ewallet">
                    <div class="payment-logo" style="background:#00B14F;"><i class="fas fa-wallet"></i></div>
                    <div class="payment-info"><p>E-wallet</p><small>GoPay / OVO / dll</small></div>
                </label>
                <label class="payment-option">
                    <input type="radio" name="payment_method" value="qris">
                    <div class="payment-logo" style="background:#7C3AED;"><i class="fas fa-qrcode"></i></div>
                    <div class="payment-info"><p>QRIS</p><small>Scan & bayar</small></div>
                </label>
                <label class="payment-option">
                    <input type="radio" name="payment_method" value="cod">
                    <div class="payment-logo" style="background:#6B7280;"><i class="fas fa-motorcycle"></i></div>
                    <div class="payment-info"><p>COD</p><small>Bayar di tempat</small></div>
                </label>
            </div>
        </div>
    </div>

    {{-- RINGKASAN --}}
    <div>
        <div class="summary-card">
            <p class="summary-title">Ringkasan Pesanan</p>

            @foreach($items as $item)
            <div class="summary-item">
                <img src="{{ $item['product']->image ? asset('storage/' . $item['product']->image) : 'https://images.unsplash.com/photo-1563729784474-d77dbb933a9e?w=200&q=80' }}"
                     alt="" loading="lazy">
                <div class="summary-item-info">
                    <p>{{ $item['product']->name }}</p>
                    <small>{{ $item['quantity'] }}x</small>
                </div>
                <span class="summary-item-price">Rp {{ number_format($item['product']->price * $item['quantity'], 0, ',', '.') }}</span>
            </div>
            @endforeach

            <div class="summary-row"><span>Subtotal</span><span>Rp {{ number_format($subtotal, 0, ',', '.') }}</span></div>
            <div class="summary-row" id="row-shipping"><span>Ongkir</span><span id="val-shipping" style="color:var(--gray);">Pilih kota tujuan</span></div>
            <div class="summary-row" id="row-discount" style="display:none;">
                <span>Diskon Voucher</span>
                <span id="val-discount" style="color:#059669;">-Rp 0</span>
            </div>

            {{-- Voucher --}}
            <div style="margin: 8px 0 12px;">
                <label class="field-label" style="color:var(--text-dark);">Kode Voucher (opsional)</label>
                <div class="voucher-wrap">
                    <input type="text" id="voucherInput" placeholder="Masukkan kode"
                           class="voucher-input" style="text-transform:uppercase;">
                    <button type="button" class="btn-voucher" onclick="applyVoucher()">Pakai</button>
                </div>
                <input type="hidden" name="voucher_code" id="voucherCode">
                <div id="voucherMsg" class="voucher-msg"></div>
            </div>

            <div class="summary-total">
                <span>Total</span>
                <span id="val-total">Rp {{ number_format($subtotal, 0, ',', '.') }}</span>
            </div>

            {{-- Opsi DP --}}
            @php $dpMin = $dpMinAmount ?? config('app.dp_min_amount', 200000); $dpPct = $dpPercentage ?? config('app.dp_percentage', 50); @endphp
            <div id="dp-section" style="display:none;background:var(--cream);border-radius:10px;padding:14px;margin-bottom:14px;">
                <label style="display:flex;align-items:flex-start;gap:10px;cursor:pointer;">
                    <input type="checkbox" name="use_dp" id="useDpCheck" value="1" style="margin-top:2px;accent-color:var(--pink);">
                    <div>
                        <p style="font-size:13px;font-weight:700;color:var(--text-dark);margin-bottom:2px;">
                            Bayar DP {{ $dpPct }}% Sekarang
                        </p>
                        <p style="font-size:12px;color:var(--gray);">
                            Bayar Rp <span id="dp-amount-display">0</span> sekarang, sisanya sebelum pengiriman.
                        </p>
                    </div>
                </label>
            </div>

            <button type="submit" id="submitBtn" class="btn-lanjut" onclick="return validateCheckout()">
                Lanjutkan Ke Pembayaran →
            </button>
        </div>
    </div>
</div>
</form>

@include('partials.footer')

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
</body>
</html>
