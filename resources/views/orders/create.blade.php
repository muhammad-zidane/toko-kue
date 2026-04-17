<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Jagoan Kue - Form Pemesanan</title>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700;800&family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }

        :root {
            --pink:       #F0507A;
            --brown-dark: #2C1810;
            --brown-mid:  #5C3D2E;
            --cream:      #FFF8EE;
            --cream-dark: #F0E6D3;
            --white:      #FFFFFF;
            --gray:       #6B7280;
            --text-dark:  #1A1A1A;
        }

        body { font-family: 'Plus Jakarta Sans', sans-serif; color: var(--text-dark); background: var(--cream); }
        a { text-decoration: none; }

        /* NAVBAR */
        .navbar { background-color: var(--brown-dark); padding: 16px 24px; position: sticky; top: 0; z-index: 100; }
        .navbar-inner { max-width: 1100px; margin: 0 auto; display: flex; align-items: center; justify-content: space-between; }
        .navbar-logo { font-family: 'Playfair Display', serif; font-size: 24px; font-weight: 800; color: var(--pink); }
        .navbar-links { display: flex; gap: 32px; list-style: none; }
        .navbar-links a { color: white; font-size: 14px; font-weight: 500; opacity: 0.9; }
        .navbar-links a:hover { opacity: 1; }
        .navbar-actions { display: flex; align-items: center; gap: 12px; }
        .btn-cart { background-color: var(--pink); color: white; padding: 8px 18px; border-radius: 8px; font-size: 14px; font-weight: 600; }
        .btn-login { border: 1.5px solid white; color: white; padding: 8px 18px; border-radius: 8px; font-size: 14px; font-weight: 600; transition: all 0.2s; }
        .btn-login:hover { background: white; color: var(--brown-dark); }

        /* BREADCRUMB */
        .breadcrumb { max-width: 1100px; margin: 0 auto; padding: 20px 24px 0; font-size: 14px; color: var(--gray); }

        /* STEPPER */
        .stepper-wrap { max-width: 1100px; margin: 0 auto; padding: 28px 24px; }
        .stepper { display: flex; align-items: center; justify-content: center; gap: 0; }

        .step { display: flex; flex-direction: column; align-items: center; gap: 8px; }

        .step-circle {
            width: 52px;
            height: 52px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 18px;
            font-weight: 700;
            border: 2px solid var(--pink);
            color: var(--pink);
            background: var(--white);
        }

        .step-circle.active { background: var(--pink); color: white; }
        .step-circle.done { background: var(--pink); color: white; font-size: 20px; }

        .step-label { font-size: 13px; font-weight: 500; color: var(--text-dark); }

        .step-line {
            flex: 1;
            height: 2px;
            background: #E5C5CF;
            margin-bottom: 24px;
            max-width: 120px;
        }

        .step-line.done { background: var(--pink); }

        /* MAIN LAYOUT */
        .checkout-layout {
            max-width: 1100px;
            margin: 0 auto;
            padding: 0 24px 60px;
            display: grid;
            grid-template-columns: 1fr 380px;
            gap: 24px;
            align-items: start;
        }

        /* CARDS */
        .card {
            background: var(--white);
            border-radius: 16px;
            padding: 24px;
            margin-bottom: 16px;
            border: 1px solid #EDE0D4;
        }

        .card-title {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 20px;
        }

        .card-num {
            width: 28px;
            height: 28px;
            border-radius: 50%;
            background: var(--pink);
            color: white;
            font-size: 13px;
            font-weight: 700;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        .card-title span {
            font-size: 15px;
            font-weight: 700;
            color: var(--brown-dark);
        }

        /* PRODUK CARD */
        .produk-item {
            display: flex;
            align-items: center;
            gap: 14px;
            background: var(--cream);
            border-radius: 10px;
            padding: 12px;
            margin-bottom: 20px;
        }

        .produk-item img {
            width: 64px;
            height: 64px;
            object-fit: cover;
            border-radius: 8px;
        }

        .produk-item h4 { font-size: 15px; font-weight: 700; color: var(--text-dark); }
        .produk-item p { font-size: 14px; font-weight: 600; color: var(--brown-dark); margin-top: 4px; }

        /* UKURAN KUE */
        .section-label { font-size: 14px; font-weight: 600; color: var(--text-dark); margin-bottom: 12px; }

        .size-options { display: flex; gap: 12px; margin-bottom: 20px; }

        .size-btn {
            border: 1.5px solid #D1C0B8;
            border-radius: 10px;
            padding: 10px 16px;
            text-align: center;
            cursor: pointer;
            transition: all 0.2s;
            background: var(--white);
            min-width: 90px;
        }

        .size-btn:hover, .size-btn.active {
            border-color: var(--brown-dark);
            background: var(--cream-dark);
        }

        .size-btn p { font-size: 14px; font-weight: 600; color: var(--text-dark); }
        .size-btn small { font-size: 11px; color: var(--gray); }

        /* QUANTITY */
        .qty-row { display: flex; align-items: center; gap: 12px; margin-bottom: 20px; }

        .qty-control {
            display: flex;
            align-items: center;
            border: 1.5px solid #D1C0B8;
            border-radius: 8px;
            overflow: hidden;
        }

        .qty-btn {
            background: none;
            border: none;
            padding: 8px 14px;
            font-size: 16px;
            cursor: pointer;
            color: var(--text-dark);
            font-weight: 700;
            transition: background 0.2s;
        }

        .qty-btn:hover { background: var(--cream); }

        .qty-input {
            width: 44px;
            text-align: center;
            border: none;
            border-left: 1.5px solid #D1C0B8;
            border-right: 1.5px solid #D1C0B8;
            padding: 8px 0;
            font-size: 14px;
            font-weight: 600;
            font-family: 'Plus Jakarta Sans', sans-serif;
            outline: none;
            background: var(--white);
        }

        /* INPUT FIELDS */
        .field-label { font-size: 13px; font-weight: 500; color: var(--text-dark); margin-bottom: 6px; display: block; }

        .field-input {
            width: 100%;
            background: var(--brown-dark);
            color: white;
            border: none;
            border-radius: 8px;
            padding: 10px 14px;
            font-size: 13px;
            font-family: 'Plus Jakarta Sans', sans-serif;
            outline: none;
            margin-bottom: 14px;
        }

        .field-input::placeholder { color: rgba(255,255,255,0.5); }

        .field-textarea {
            width: 100%;
            background: var(--brown-dark);
            color: white;
            border: none;
            border-radius: 8px;
            padding: 10px 14px;
            font-size: 13px;
            font-family: 'Plus Jakarta Sans', sans-serif;
            outline: none;
            resize: none;
            margin-bottom: 14px;
        }

        .field-textarea::placeholder { color: rgba(255,255,255,0.5); }

        /* DATA PEMESAN GRID */
        .two-col { display: grid; grid-template-columns: 1fr 1fr; gap: 12px; }

        /* TANGGAL */
        .date-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 12px; margin-bottom: 12px; }

        .info-box {
            background: var(--cream-dark);
            border-radius: 8px;
            padding: 10px 14px;
            font-size: 12px;
            color: var(--brown-mid);
            line-height: 1.6;
        }

        /* METODE PEMBAYARAN */
        .payment-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 12px; }

        .payment-option {
            display: flex;
            align-items: center;
            gap: 10px;
            border: 1.5px solid #D1C0B8;
            border-radius: 10px;
            padding: 12px;
            cursor: pointer;
            transition: all 0.2s;
        }

        .payment-option:has(input:checked) {
            border-color: var(--brown-dark);
            background: var(--cream-dark);
        }

        .payment-option input { accent-color: var(--brown-dark); }

        .payment-logo {
            width: 36px;
            height: 36px;
            border-radius: 6px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 10px;
            font-weight: 800;
            color: white;
            flex-shrink: 0;
        }

        .payment-info p { font-size: 13px; font-weight: 600; color: var(--text-dark); }
        .payment-info small { font-size: 11px; color: var(--gray); }

        /* RINGKASAN */
        .summary-card {
            background: var(--white);
            border-radius: 16px;
            padding: 24px;
            border: 1px solid #EDE0D4;
            position: sticky;
            top: 90px;
        }

        .summary-title { font-size: 16px; font-weight: 700; color: var(--text-dark); margin-bottom: 16px; }

        .summary-item {
            display: flex;
            align-items: center;
            gap: 12px;
            padding-bottom: 16px;
            border-bottom: 1px solid #F0E8E0;
            margin-bottom: 16px;
        }

        .summary-item img {
            width: 52px;
            height: 52px;
            object-fit: cover;
            border-radius: 8px;
        }

        .summary-item-info { flex: 1; }
        .summary-item-info p { font-size: 13px; font-weight: 600; color: var(--text-dark); }
        .summary-item-info small { font-size: 12px; color: var(--gray); }
        .summary-item-price { font-size: 13px; font-weight: 600; color: var(--text-dark); }

        .summary-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-size: 13px;
            margin-bottom: 10px;
        }

        .summary-row span:first-child { color: var(--gray); }
        .summary-row span:last-child { font-weight: 600; color: var(--text-dark); }
        .summary-row .diskon { color: var(--pink); }

        .promo-row {
            display: flex;
            gap: 8px;
            margin: 12px 0 16px;
        }

        .promo-input {
            flex: 1;
            border: 1.5px solid #D1C0B8;
            border-radius: 8px;
            padding: 8px 12px;
            font-size: 13px;
            font-family: 'Plus Jakarta Sans', sans-serif;
            outline: none;
            background: var(--cream);
        }

        .promo-input::placeholder { color: var(--gray); }

        .btn-promo {
            background: none;
            border: 1.5px solid #D1C0B8;
            border-radius: 8px;
            padding: 8px 16px;
            font-size: 13px;
            font-weight: 600;
            cursor: pointer;
            font-family: 'Plus Jakarta Sans', sans-serif;
            transition: all 0.2s;
        }

        .btn-promo:hover { background: var(--cream-dark); }

        .summary-total {
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-size: 14px;
            font-weight: 700;
            color: var(--text-dark);
            padding-top: 12px;
            border-top: 1.5px solid #EDE0D4;
            margin-bottom: 16px;
        }

        .summary-total span:last-child { color: var(--pink); font-size: 16px; }

        .btn-lanjut {
            width: 100%;
            background: var(--brown-dark);
            color: white;
            border: none;
            border-radius: 10px;
            padding: 14px;
            font-size: 14px;
            font-weight: 700;
            cursor: pointer;
            font-family: 'Plus Jakarta Sans', sans-serif;
            transition: opacity 0.2s;
        }

        .btn-lanjut:hover { opacity: 0.85; }

        /* RESPONSIVE */
        @media (max-width: 768px) {
            .navbar-links { display: none; }
            .checkout-layout { grid-template-columns: 1fr; }
            .stepper { gap: 0; }
            .step-line { max-width: 60px; }
        }
    </style>
</head>
<body>

{{-- NAVBAR --}}
<nav class="navbar">
    <div class="navbar-inner">
        <a href="/" class="navbar-logo">Jagoan Kue</a>
        <ul class="navbar-links">
            <li><a href="/">Beranda</a></li>
            <li><a href="/products">Katalog</a></li>
            <li><a href="/orders">Pemesanan</a></li>
        </ul>
        <div class="navbar-actions">
            <a href="/cart" class="btn-cart">🛒 Keranjang</a>
            @auth
                <a href="/profile" class="btn-login">{{ auth()->user()->name }}</a>
            @else
                <a href="/login" class="btn-login">Login</a>
            @endauth
        </div>
    </div>
</nav>

{{-- BREADCRUMB --}}
<div class="breadcrumb">
    <a href="/" style="color: var(--gray);">Beranda</a> /
    <a href="/products" style="color: var(--gray);">Katalog</a> /
    <span style="color: var(--text-dark); font-weight: 600;">Form Pemesanan</span>
</div>

{{-- STEPPER --}}
<div class="stepper-wrap">
    <div class="stepper">
        <div class="step">
            <div class="step-circle done">✓</div>
            <span class="step-label">Pilih Kue</span>
        </div>
        <div class="step-line done"></div>
        <div class="step">
            <div class="step-circle active">2</div>
            <span class="step-label">Detail Pesanan</span>
        </div>
        <div class="step-line"></div>
        <div class="step">
            <div class="step-circle">3</div>
            <span class="step-label">Pembayaran</span>
        </div>
        <div class="step-line"></div>
        <div class="step">
            <div class="step-circle">4</div>
            <span class="step-label">Konfirmasi</span>
        </div>
    </div>
</div>

{{-- CHECKOUT LAYOUT --}}
<form action="/orders" method="POST">
@csrf

<div class="checkout-layout">

    {{-- KIRI: FORM --}}
    <div>

        {{-- 1. Produk --}}
        <div class="card">
            <div class="card-title">
                <div class="card-num">1.</div>
                <span>Produk yang Dipesan</span>
            </div>

            <div class="produk-item">
                <img src="{{ isset($product) && $product->image ? asset('storage/' . $product->image) : 'https://images.unsplash.com/photo-1563729784474-d77dbb933a9e?w=200&q=80' }}"
                     alt="Produk">
                <div>
                    <h4>{{ isset($product) ? $product->name : 'Produk' }}</h4>
                    <p>Rp. {{ isset($product) ? number_format($product->price, 0, ',', '.') : '0' }}</p>
                </div>
            </div>

            <input type="hidden" name="items[0][product_id]" value="{{ isset($product) ? $product->id : '' }}">

            {{-- Ukuran --}}
            <p class="section-label">Ukuran Kue</p>
            <div class="size-options">
                <label class="size-btn active" onclick="selectSize(this, 'small')">
                    <input type="radio" name="size" value="small" style="display:none;" checked>
                    <p>Small</p>
                    <small>6-8 orang</small>
                </label>
                <label class="size-btn" onclick="selectSize(this, 'medium')">
                    <input type="radio" name="size" value="medium" style="display:none;">
                    <p>Medium</p>
                    <small>10-12 orang</small>
                </label>
                <label class="size-btn" onclick="selectSize(this, 'large')">
                    <input type="radio" name="size" value="large" style="display:none;">
                    <p>Large</p>
                    <small>16-20 orang</small>
                </label>
            </div>

            {{-- Jumlah --}}
            <p class="section-label">Jumlah</p>
            <div class="qty-row" style="margin-bottom: 20px;">
                <div class="qty-control">
                    <button type="button" class="qty-btn" onclick="changeQty(-1)">−</button>
                    <input type="number" id="qty" name="items[0][quantity]" class="qty-input" value="1" min="1" max="{{ isset($product) ? $product->stock : 99 }}">
                    <button type="button" class="qty-btn" onclick="changeQty(1)">+</button>
                </div>
            </div>

            {{-- Tulisan di Kue --}}
            <label class="field-label">Tulisan di Kue</label>
            <input type="text" name="cake_text" class="field-input" placeholder="cth: Selamat Ulang Tahun">

            {{-- Catatan Khusus --}}
            <label class="field-label">Catatan Khusus</label>
            <textarea name="notes" class="field-textarea" rows="4" placeholder="Cth: Kurangi gula, tambahkan banyak buah dll."></textarea>
        </div>

        {{-- 2. Data Pemesan --}}
        <div class="card">
            <div class="card-title">
                <div class="card-num">2.</div>
                <span>Data Pemesan</span>
            </div>

            <div class="two-col">
                <div>
                    <label class="field-label">Nama Lengkap</label>
                    <input type="text" name="name" class="field-input" placeholder="Cth: Muhammad Zidane" value="{{ auth()->user()->name ?? '' }}">
                </div>
                <div>
                    <label class="field-label">No. Telepon</label>
                    <input type="text" name="phone" class="field-input" placeholder="Cth: 08123456789">
                </div>
            </div>

            <label class="field-label">Alamat Lengkap</label>
            <textarea name="shipping_address" class="field-textarea" rows="3" placeholder="Jl.imam bonjol ....."></textarea>
        </div>

        {{-- 3. Tanggal & Waktu --}}
        <div class="card">
            <div class="card-title">
                <div class="card-num">3.</div>
                <span>Tanggal & Waktu Pengiriman</span>
            </div>

            <div class="date-grid">
                <div>
                    <label class="field-label">Tanggal Pengiriman</label>
                    <input type="date" name="delivery_date" class="field-input" style="color-scheme: dark;">
                </div>
                <div>
                    <label class="field-label">Waktu Pengiriman</label>
                    <input type="time" name="delivery_time" class="field-input" style="color-scheme: dark;">
                </div>
            </div>

            <div class="info-box">
                Pemesanan minimal H-2 sebelum tanggal pengiriman. Untuk pesanan mendadak, hubungi kami via WhatsApp terlebih dahulu.
            </div>
        </div>

        {{-- 4. Metode Pembayaran --}}
        <div class="card">
            <div class="card-title">
                <div class="card-num">4.</div>
                <span>Metode Pembayaran</span>
            </div>

            <div class="payment-grid">
                <label class="payment-option">
                    <input type="radio" name="payment_method" value="bca" checked>
                    <div class="payment-logo" style="background:#006CB0;">BCA</div>
                    <div class="payment-info">
                        <p>Transfer BCA</p>
                        <small>Konfirmasi otomatis dalam 5 menit</small>
                    </div>
                </label>

                <label class="payment-option">
                    <input type="radio" name="payment_method" value="gopay">
                    <div class="payment-logo" style="background:#00B14F;">go-pay</div>
                    <div class="payment-info">
                        <p>go-pay</p>
                        <small>Bayar langsung via e-wallet</small>
                    </div>
                </label>

                <label class="payment-option">
                    <input type="radio" name="payment_method" value="bni">
                    <div class="payment-logo" style="background:#F26522;">BNI</div>
                    <div class="payment-info">
                        <p>Transfer BNI</p>
                        <small>Konfirmasi otomatis dalam 5 menit</small>
                    </div>
                </label>

                <label class="payment-option">
                    <input type="radio" name="payment_method" value="cod">
                    <div class="payment-logo" style="background:#6B7280;">COD</div>
                    <div class="payment-info">
                        <p>COD</p>
                        <small>Bayar di tempat (khusus wilayah payakumbuh dan sekitarnya)</small>
                    </div>
                </label>
            </div>
        </div>

    </div>

    {{-- KANAN: RINGKASAN --}}
    <div>
        <div class="summary-card">
            <p class="summary-title">Ringkasan Pesanan</p>

            <div class="summary-item">
                <img src="{{ isset($product) && $product->image ? asset('storage/' . $product->image) : 'https://images.unsplash.com/photo-1563729784474-d77dbb933a9e?w=200&q=80' }}"
                     alt="Produk">
                <div class="summary-item-info">
                    <p>{{ isset($product) ? $product->name : 'Produk' }}</p>
                    <small>Medium . 1x</small>
                </div>
                <span class="summary-item-price" id="summary-price">
                    Rp. {{ isset($product) ? number_format($product->price, 0, ',', '.') : '0' }}
                </span>
            </div>

            <div class="summary-row">
                <span>Subtotal</span>
                <span id="subtotal-text">Rp. {{ isset($product) ? number_format($product->price, 0, ',', '.') : '0' }}</span>
            </div>
            <div class="summary-row">
                <span>Ongkir</span>
                <span>Rp. 15.000</span>
            </div>
            <div class="summary-row">
                <span>Diskon</span>
                <span class="diskon">-Rp. 0</span>
            </div>

            <div class="promo-row">
                <input type="text" name="promo_code" class="promo-input" placeholder="Kode Promo...">
                <button type="button" class="btn-promo">Pakai</button>
            </div>

            <div class="summary-total">
                <span>Total Pembayaran</span>
                <span id="total-text">Rp. {{ isset($product) ? number_format($product->price + 15000, 0, ',', '.') : '15.000' }}</span>
            </div>

            <button type="submit" class="btn-lanjut">Lanjutkan Ke Pembayaran --></button>
        </div>
    </div>

</div>
</form>

<script>
    const basePrice = {{ isset($product) ? $product->price : 0 }};
    const ongkir = 15000;

    function changeQty(delta) {
        const input = document.getElementById('qty');
        let val = parseInt(input.value) + delta;
        if (val < 1) val = 1;
        if (val > parseInt(input.max)) val = parseInt(input.max);
        input.value = val;
        updateSummary(val);
    }

    function updateSummary(qty) {
        const subtotal = basePrice * qty;
        const total = subtotal + ongkir;
        document.getElementById('summary-price').textContent = 'Rp. ' + subtotal.toLocaleString('id-ID');
        document.getElementById('subtotal-text').textContent = 'Rp. ' + subtotal.toLocaleString('id-ID');
        document.getElementById('total-text').textContent = 'Rp. ' + total.toLocaleString('id-ID');
    }

    function selectSize(el, size) {
        document.querySelectorAll('.size-btn').forEach(b => b.classList.remove('active'));
        el.classList.add('active');
    }

    document.getElementById('qty').addEventListener('input', function() {
        let val = parseInt(this.value);
        if (isNaN(val) || val < 1) val = 1;
        this.value = val;
        updateSummary(val);
    });
</script>

</body>
</html>
