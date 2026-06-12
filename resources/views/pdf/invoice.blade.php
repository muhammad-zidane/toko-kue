<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice {{ $order->order_code ?? '#' . $order->id }} — Jagoan Kue</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            font-size: 12px;
            color: #2C1810;
            line-height: 1.5;
            background: #FFFFFF;
        }
        .page {
            padding: 32px 36px;
            max-width: 700px;
            margin: 0 auto;
        }

        /* HEADER */
        .invoice-header {
            display: table;
            width: 100%;
            margin-bottom: 28px;
            border-bottom: 3px solid #E8424A;
            padding-bottom: 20px;
        }
        .header-left { display: table-cell; vertical-align: top; width: 55%; }
        .header-right { display: table-cell; vertical-align: top; text-align: right; }
        .logo-text {
            font-size: 26px;
            font-weight: 900;
            color: #E8424A;
            letter-spacing: -0.5px;
        }
        .logo-sub {
            font-size: 10px;
            color: #7C6057;
            margin-top: 2px;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        .header-left address {
            font-style: normal;
            font-size: 11px;
            color: #7C6057;
            margin-top: 10px;
            line-height: 1.7;
        }
        .invoice-label {
            font-size: 22px;
            font-weight: 700;
            color: #2C1810;
            text-transform: uppercase;
            letter-spacing: 2px;
        }
        .invoice-num {
            font-size: 14px;
            font-weight: 700;
            color: #E8424A;
            margin-top: 4px;
        }
        .invoice-date {
            font-size: 11px;
            color: #7C6057;
            margin-top: 4px;
        }

        /* INFO GRID */
        .info-section {
            display: table;
            width: 100%;
            margin-bottom: 24px;
        }
        .info-col { display: table-cell; width: 50%; vertical-align: top; }
        .info-col:last-child { padding-left: 20px; }
        .info-box {
            background: #FFF8EE;
            border: 1px solid #EDE0D4;
            border-radius: 6px;
            padding: 14px 16px;
        }
        .info-title {
            font-size: 9px;
            font-weight: 700;
            color: #7C6057;
            text-transform: uppercase;
            letter-spacing: 1.5px;
            margin-bottom: 10px;
            border-bottom: 1px solid #EDE0D4;
            padding-bottom: 6px;
        }
        .info-row {
            display: table;
            width: 100%;
            margin-bottom: 4px;
        }
        .info-key {
            display: table-cell;
            font-size: 10px;
            color: #7C6057;
            width: 95px;
        }
        .info-val {
            display: table-cell;
            font-size: 11px;
            font-weight: 600;
            color: #2C1810;
        }

        /* STATUS BADGE */
        .status-badge {
            display: inline-block;
            padding: 2px 10px;
            border-radius: 10px;
            font-size: 10px;
            font-weight: 700;
        }
        .status-pending { background: #FEF3C7; color: #D97706; }
        .status-processing { background: #DBEAFE; color: #1D4ED8; }
        .status-completed { background: #DCFCE7; color: #16A34A; }
        .status-cancelled { background: #FEE2E2; color: #DC2626; }

        /* ITEMS TABLE */
        .items-title {
            font-size: 10px;
            font-weight: 700;
            color: #7C6057;
            text-transform: uppercase;
            letter-spacing: 1.5px;
            margin-bottom: 10px;
        }
        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 24px;
        }
        .items-table thead tr {
            background: #2C1810;
            color: white;
        }
        .items-table th {
            padding: 10px 12px;
            font-size: 10px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            text-align: left;
        }
        .items-table th.right { text-align: right; }
        .items-table td {
            padding: 10px 12px;
            font-size: 11px;
            border-bottom: 1px solid #EDE0D4;
            vertical-align: middle;
        }
        .items-table td.right { text-align: right; }
        .items-table tbody tr:nth-child(even) { background: #FFFBF5; }
        .item-name { font-weight: 600; color: #2C1810; }
        .item-note { font-size: 10px; color: #7C6057; margin-top: 2px; }

        /* SUMMARY */
        .summary-wrap {
            display: table;
            width: 100%;
            margin-bottom: 28px;
        }
        .summary-spacer { display: table-cell; width: 55%; }
        .summary-box { display: table-cell; width: 45%; }
        .summary-row {
            display: table;
            width: 100%;
            padding: 5px 0;
            border-bottom: 1px solid #EDE0D4;
        }
        .summary-row:last-child { border-bottom: none; }
        .summary-label { display: table-cell; font-size: 11px; color: #7C6057; }
        .summary-val { display: table-cell; text-align: right; font-size: 11px; font-weight: 600; color: #2C1810; }
        .summary-total {
            background: #2C1810;
            border-radius: 6px;
            padding: 10px 14px;
            margin-top: 8px;
        }
        .summary-total-row {
            display: table;
            width: 100%;
        }
        .summary-total-label { display: table-cell; font-size: 12px; font-weight: 700; color: white; }
        .summary-total-val { display: table-cell; text-align: right; font-size: 14px; font-weight: 900; color: #E8424A; }

        /* PAYMENT INFO */
        .payment-section {
            background: #FFF8EE;
            border: 1px solid #EDE0D4;
            border-radius: 6px;
            padding: 14px 16px;
            margin-bottom: 24px;
        }
        .payment-grid {
            display: table;
            width: 100%;
        }
        .payment-col { display: table-cell; width: 33%; vertical-align: top; }
        .payment-label { font-size: 9px; color: #7C6057; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 3px; }
        .payment-val { font-size: 11px; font-weight: 700; color: #2C1810; }

        /* NOTES */
        .notes-section {
            border: 1px dashed #EDE0D4;
            border-radius: 6px;
            padding: 12px 16px;
            margin-bottom: 24px;
        }
        .notes-title { font-size: 10px; font-weight: 700; color: #7C6057; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 6px; }
        .notes-text { font-size: 11px; color: #2C1810; line-height: 1.6; }

        /* FOOTER */
        .invoice-footer {
            border-top: 2px solid #EDE0D4;
            padding-top: 16px;
            text-align: center;
        }
        .footer-thanks {
            font-size: 14px;
            font-weight: 700;
            color: #E8424A;
            margin-bottom: 6px;
        }
        .footer-sub {
            font-size: 10px;
            color: #7C6057;
            line-height: 1.6;
        }
        .footer-contact {
            margin-top: 10px;
            font-size: 10px;
            color: #7C6057;
        }
        .footer-contact span {
            margin: 0 8px;
        }
        .watermark-paid {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%) rotate(-30deg);
            font-size: 80px;
            font-weight: 900;
            color: rgba(16,163,74,0.06);
            text-transform: uppercase;
            letter-spacing: 8px;
            pointer-events: none;
            z-index: 0;
        }
    </style>
</head>
<body>
<div class="page">

    {{-- HEADER --}}
    <div class="invoice-header">
        <div class="header-left">
            <div class="logo-text">Jagoan Kue</div>
            <div class="logo-sub">Toko Kue Premium</div>
            <address>
                Jl. Mawar No. 12, Sukamaju<br>
                Kota Bandung, Jawa Barat<br>
                halo@jagoan-kue.id &bull; +62 812-3456-7890
            </address>
        </div>
        <div class="header-right">
            <div class="invoice-label">Invoice</div>
            <div class="invoice-num">{{ $order->order_code ?? '#' . str_pad($order->id, 5, '0', STR_PAD_LEFT) }}</div>
            <div class="invoice-date">Tanggal: {{ $order->created_at->format('d F Y') }}</div>
            @if($order->payment)
            <div class="invoice-date" style="margin-top:4px;">
                Lunas: {{ $order->payment->paid_at ? \Carbon\Carbon::parse($order->payment->paid_at)->format('d F Y') : '-' }}
            </div>
            @endif
        </div>
    </div>

    {{-- INFO PELANGGAN & PESANAN --}}
    <div class="info-section">
        <div class="info-col">
            <div class="info-box">
                <div class="info-title">Informasi Pelanggan</div>
                <div class="info-row">
                    <span class="info-key">Nama</span>
                    <span class="info-val">{{ $order->user->name ?? '-' }}</span>
                </div>
                <div class="info-row">
                    <span class="info-key">Email</span>
                    <span class="info-val">{{ $order->user->email ?? '-' }}</span>
                </div>
                @if($order->user->phone ?? null)
                <div class="info-row">
                    <span class="info-key">Telepon</span>
                    <span class="info-val">{{ $order->user->phone }}</span>
                </div>
                @endif
                @if($order->shipping_address ?? null)
                <div class="info-row">
                    <span class="info-key">Alamat</span>
                    <span class="info-val">{{ $order->shipping_address }}</span>
                </div>
                @endif
            </div>
        </div>
        <div class="info-col">
            <div class="info-box">
                <div class="info-title">Detail Pesanan</div>
                <div class="info-row">
                    <span class="info-key">No. Pesanan</span>
                    <span class="info-val">{{ $order->order_code ?? '#' . $order->id }}</span>
                </div>
                <div class="info-row">
                    <span class="info-key">Tanggal Pesan</span>
                    <span class="info-val">{{ $order->created_at->format('d M Y H:i') }}</span>
                </div>
                <div class="info-row">
                    <span class="info-key">Status</span>
                    <span class="info-val">
                        @php
                            $statusMap = ['pending'=>'Menunggu','processing'=>'Diproses','completed'=>'Selesai','cancelled'=>'Dibatalkan'];
                            $statusCls = ['pending'=>'status-pending','processing'=>'status-processing','completed'=>'status-completed','cancelled'=>'status-cancelled'];
                        @endphp
                        <span class="status-badge {{ $statusCls[$order->status] ?? 'status-pending' }}">
                            {{ $statusMap[$order->status] ?? ucfirst($order->status) }}
                        </span>
                    </span>
                </div>
                @if($order->shipping_zone ?? null)
                <div class="info-row">
                    <span class="info-key">Zona Kirim</span>
                    <span class="info-val">{{ $order->shipping_zone }}</span>
                </div>
                @endif
            </div>
        </div>
    </div>

    {{-- TABEL ITEM --}}
    <div class="items-title">Rincian Produk</div>
    <table class="items-table">
        <thead>
            <tr>
                <th style="width:30px;">#</th>
                <th>Nama Produk</th>
                <th class="right" style="width:60px;">Qty</th>
                <th class="right" style="width:110px;">Harga Satuan</th>
                <th class="right" style="width:120px;">Subtotal</th>
            </tr>
        </thead>
        <tbody>
            @foreach($order->orderItems as $i => $item)
            <tr>
                <td style="color:#7C6057;">{{ $i + 1 }}</td>
                <td>
                    <div class="item-name">{{ $item->product->name ?? $item->product_name ?? 'Produk' }}</div>
                    @if($item->notes ?? null)
                    <div class="item-note">Catatan: {{ $item->notes }}</div>
                    @endif
                </td>
                <td class="right">{{ $item->quantity }}</td>
                <td class="right">Rp {{ number_format($item->price, 0, ',', '.') }}</td>
                <td class="right" style="font-weight:700;">Rp {{ number_format($item->quantity * $item->price, 0, ',', '.') }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    {{-- SUMMARY --}}
    <div class="summary-wrap">
        <div class="summary-spacer"></div>
        <div class="summary-box">
            <div class="summary-row">
                <span class="summary-label">Subtotal Produk</span>
                <span class="summary-val">Rp {{ number_format($order->subtotal ?? $order->orderItems->sum(fn($i) => $i->price * $i->quantity), 0, ',', '.') }}</span>
            </div>
            @if(($order->shipping_cost ?? 0) > 0)
            <div class="summary-row">
                <span class="summary-label">Ongkos Kirim</span>
                <span class="summary-val">Rp {{ number_format($order->shipping_cost, 0, ',', '.') }}</span>
            </div>
            @endif
            @if(($order->discount_amount ?? 0) > 0)
            <div class="summary-row">
                <span class="summary-label">Diskon Voucher</span>
                <span class="summary-val" style="color:#16A34A;">- Rp {{ number_format($order->discount_amount, 0, ',', '.') }}</span>
            </div>
            @endif
            <div class="summary-total">
                <div class="summary-total-row">
                    <span class="summary-total-label">Total Pembayaran</span>
                    <span class="summary-total-val">Rp {{ number_format($order->total_amount, 0, ',', '.') }}</span>
                </div>
            </div>
        </div>
    </div>

    {{-- INFO PEMBAYARAN --}}
    @if($order->payment)
    <div class="items-title">Informasi Pembayaran</div>
    <div class="payment-section">
        <div class="payment-grid">
            <div class="payment-col">
                <div class="payment-label">Metode</div>
                <div class="payment-val">{{ ucfirst(str_replace('_', ' ', $order->payment->method ?? 'Transfer Bank')) }}</div>
            </div>
            <div class="payment-col">
                <div class="payment-label">Status</div>
                <div class="payment-val">{{ ucfirst($order->payment->status ?? '-') }}</div>
            </div>
            <div class="payment-col">
                <div class="payment-label">Tanggal Bayar</div>
                <div class="payment-val">
                    {{ $order->payment->paid_at ? \Carbon\Carbon::parse($order->payment->paid_at)->format('d M Y') : '-' }}
                </div>
            </div>
        </div>
    </div>
    @endif

    {{-- CATATAN --}}
    @if($order->notes ?? null)
    <div class="notes-section">
        <div class="notes-title">Catatan Pesanan</div>
        <div class="notes-text">{{ $order->notes }}</div>
    </div>
    @endif

    {{-- FOOTER --}}
    <div class="invoice-footer">
        <div class="footer-thanks">Terima kasih telah berbelanja di Jagoan Kue!</div>
        <div class="footer-sub">
            Invoice ini diterbitkan secara otomatis dan sah tanpa tanda tangan.<br>
            Simpan sebagai bukti transaksi Anda.
        </div>
        <div class="footer-contact">
            <span><i>halo@jagoan-kue.id</i></span> &bull;
            <span>+62 812-3456-7890</span> &bull;
            <span>jagoan-kue.id</span>
        </div>
    </div>

</div>
</body>
</html>
