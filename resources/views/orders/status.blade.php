<!DOCTYPE html>
<html lang="id">
<head>
    <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}" />
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Jagoan Kue - Status Pesanan {{ $order->order_code }}</title>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700;800&family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    <style>

        .page { max-width: 900px; margin: 0 auto; padding: 32px 24px 60px; }
        .page-title { font-family: 'Playfair Display', serif; font-size: 26px; font-weight: 800; margin-bottom: 4px; }
        .page-subtitle { font-size: 13px; color: var(--gray); margin-bottom: 28px; }

        /* Status Timeline */
        .status-card { background: var(--white); border-radius: 16px; border: 1px solid #EDE0D4; padding: 28px 24px; margin-bottom: 20px; }
        .status-header { display: flex; align-items: center; justify-content: space-between; margin-bottom: 24px; flex-wrap: wrap; gap: 10px; }
        .status-header-left h2 { font-size: 15px; font-weight: 700; color: var(--pink); letter-spacing: 0.5px; text-transform: uppercase; }
        .status-header-left p { font-size: 13px; color: var(--gray); margin-top: 2px; }

        .badge { display: inline-block; padding: 5px 14px; border-radius: 20px; font-size: 13px; font-weight: 700; }
        .badge-pending    { background: #FEF3C7; color: #D97706; }
        .badge-processing { background: #DBEAFE; color: #2563EB; }
        .badge-shipped    { background: #EDE9FE; color: #7C3AED; }
        .badge-completed  { background: #DCFCE7; color: #16A34A; }
        .badge-cancelled  { background: #FEE2E2; color: #DC2626; }
        .badge-unpaid { background: #FEF3C7; color: #D97706; }
        .badge-paid   { background: #DCFCE7; color: #16A34A; }

        /* Timeline */
        .timeline { display: flex; align-items: flex-start; gap: 0; margin-top: 8px; }
        .timeline-step { flex: 1; display: flex; flex-direction: column; align-items: center; position: relative; }
        .timeline-step:not(:last-child)::after {
            content: '';
            position: absolute;
            top: 18px;
            left: 50%;
            width: 100%;
            height: 3px;
            background: #EDE0D4;
            z-index: 0;
        }
        .timeline-step.done:not(:last-child)::after  { background: var(--pink); }
        .timeline-icon {
            width: 36px; height: 36px; border-radius: 50%;
            background: #EDE0D4; color: #B0A09A;
            display: flex; align-items: center; justify-content: center;
            font-size: 14px; z-index: 1; position: relative;
            border: 3px solid var(--cream);
            transition: background 0.3s;
        }
        .timeline-step.done  .timeline-icon { background: var(--pink);  color: white; }
        .timeline-step.active .timeline-icon { background: var(--brown-dark); color: white; box-shadow: 0 0 0 4px #F0507A33; }
        .timeline-step.cancelled .timeline-icon { background: #FEE2E2; color: #DC2626; }
        .timeline-label { font-size: 11px; font-weight: 700; text-align: center; margin-top: 8px; color: var(--gray); letter-spacing: 0.3px; line-height: 1.4; }
        .timeline-step.done   .timeline-label { color: var(--pink); }
        .timeline-step.active .timeline-label { color: var(--brown-dark); }
        .timeline-step.cancelled .timeline-label { color: #DC2626; }

        .cancelled-notice { display: flex; align-items: center; gap: 10px; background: #FEF2F2; border: 1px solid #FECACA; border-radius: 10px; padding: 12px 16px; margin-top: 20px; }
        .cancelled-notice i { color: #DC2626; font-size: 16px; }
        .cancelled-notice p { font-size: 13px; color: #7F1D1D; font-weight: 600; }

        /* Detail Grid */
        .detail-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 20px; }
        .card { background: var(--white); border-radius: 16px; border: 1px solid #EDE0D4; padding: 24px; }
        .card-label { font-size: 13px; font-weight: 700; color: var(--pink); margin-bottom: 16px; letter-spacing: 0.5px; text-transform: uppercase; }

        .order-item { display: flex; align-items: center; gap: 12px; padding: 12px; background: var(--cream); border-radius: 10px; margin-bottom: 10px; }
        .order-item:last-child { margin-bottom: 0; }
        .order-item img { width: 52px; height: 52px; object-fit: cover; border-radius: 8px; flex-shrink: 0; }
        .order-item-info { flex: 1; }
        .order-item-info p { font-size: 14px; font-weight: 700; }
        .order-item-info small { font-size: 12px; color: var(--gray); }
        .item-note { margin-top: 5px; font-size: 12px; color: var(--brown-dark); background: #FFF4E6; border-radius: 6px; padding: 5px 8px; line-height: 1.5; }
        .order-item-price { font-size: 14px; font-weight: 700; white-space: nowrap; }

        .price-total { display: flex; justify-content: space-between; align-items: center; padding: 12px 16px; background: var(--cream); border-radius: 10px; margin-top: 14px; }
        .price-total span:first-child { font-size: 14px; font-weight: 600; color: var(--gray); }
        .price-total span:last-child { font-size: 18px; font-weight: 800; color: var(--brown-dark); }

        .info-row { display: flex; justify-content: space-between; align-items: center; padding: 10px 0; border-bottom: 1px solid #F0E8E0; font-size: 13px; }
        .info-row:last-child { border-bottom: none; }
        .info-row .info-label { color: var(--gray); flex-shrink: 0; margin-right: 12px; }
        .info-row .info-value { font-weight: 600; text-align: right; }

        .btn-back { display: inline-flex; align-items: center; gap: 8px; background: var(--brown-dark); color: white; padding: 12px 24px; border-radius: 10px; font-size: 14px; font-weight: 700; }
        .btn-detail { display: inline-flex; align-items: center; gap: 8px; background: var(--pink); color: white; padding: 12px 24px; border-radius: 10px; font-size: 14px; font-weight: 700; margin-left: 12px; }
        .btn-pay { display: block; width: 100%; text-align: center; background: var(--pink); color: white; padding: 12px 20px; border-radius: 10px; font-size: 14px; font-weight: 700; margin-bottom: 12px; }

        @media (max-width: 768px) {
            .detail-grid { grid-template-columns: 1fr; }
            .timeline-label { font-size: 10px; }
        }
    </style></head>
<body>
@include('partials.navbar')

<div class="page">
    <h1 class="page-title">Status Pesanan</h1>
    <p class="page-subtitle">{{ $order->order_code }} &middot; {{ $order->created_at->translatedFormat('d F Y, H:i') }}</p>

    {{-- Status Timeline --}}
    <div class="status-card">
        <div class="status-header">
            <div class="status-header-left">
                <h2>Lacak Pesanan</h2>
                <p>Terakhir diperbarui: {{ $order->updated_at->translatedFormat('d F Y, H:i') }}</p>
            </div>
            @php
                $statusLabels = [
                    'pending'    => 'Menunggu Konfirmasi',
                    'processing' => 'Diproses',
                    'shipped'    => 'Dikirim',
                    'completed'  => 'Selesai',
                    'cancelled'  => 'Dibatalkan',
                ];
            @endphp
            <span class="badge badge-{{ $order->status }}">
                {{ $statusLabels[$order->status] ?? ucfirst($order->status) }}
            </span>
        </div>

        @if($order->status === 'cancelled')
            {{-- Cancelled state: simple notice --}}
            <div class="timeline">
                @php $steps = [
                    ['pending',    'fa-clock',        'Pesanan\nMasuk'],
                    ['cancelled',  'fa-times-circle', 'Dibatalkan'],
                ]; @endphp
                @foreach($steps as [$key, $icon, $label])
                    <div class="timeline-step cancelled">
                        <div class="timeline-icon"><i class="fa-solid fa-{{ $icon === 'fa-clock' ? 'clock' : 'xmark' }}"></i></div>
                        <div class="timeline-label">{!! nl2br(e(str_replace('\n', "\n", $label))) !!}</div>
                    </div>
                @endforeach
            </div>
            <div class="cancelled-notice">
                <i class="fa-solid fa-circle-exclamation"></i>
                <p>Pesanan ini telah dibatalkan. Hubungi kami jika ada pertanyaan.</p>
            </div>
        @else
            @php
                $steps = [
                    ['pending',    'fa-clock',          'Menunggu\nKonfirmasi'],
                    ['processing', 'fa-gear',           'Sedang\nDiproses'],
                    ['shipped',    'fa-truck',          'Dalam\nPengiriman'],
                    ['completed',  'fa-circle-check',  'Selesai'],
                ];
                $statusOrder = ['pending' => 0, 'processing' => 1, 'shipped' => 2, 'completed' => 3];
                $currentIdx  = $statusOrder[$order->status] ?? 0;
            @endphp
            <div class="timeline">
                @foreach($steps as $i => [$key, $icon, $label])
                    @php
                        $stepIdx = $statusOrder[$key];
                        $stepClass = $stepIdx < $currentIdx ? 'done' : ($stepIdx === $currentIdx ? 'active' : '');
                    @endphp
                    <div class="timeline-step {{ $stepClass }}">
                        <div class="timeline-icon">
                            <i class="fa-solid fa-{{ $icon }}"></i>
                        </div>
                        <div class="timeline-label">{!! nl2br(e(str_replace('\n', "\n", $label))) !!}</div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>

    {{-- Detail Grid --}}
    <div class="detail-grid">
        {{-- Produk --}}
        <div class="card">
            <p class="card-label">Produk Dipesan</p>
            @foreach($order->orderItems as $item)
                <div class="order-item">
                    <img src="{{ $item->product && $item->product->image ? asset('storage/' . $item->product->image) : 'https://images.unsplash.com/photo-1563729784474-d77dbb933a9e?w=200&q=80' }}"
                         alt="{{ $item->product->name ?? 'Produk' }}">
                    <div class="order-item-info">
                        <p>{{ $item->product->name ?? 'Produk dihapus' }}</p>
                        <small>{{ $item->quantity }}x &middot; Rp {{ number_format($item->price, 0, ',', '.') }}</small>
                        @if(!empty($item->note))
                            <p class="item-note"><i class="fa-solid fa-note-sticky" style="font-size:10px;margin-right:4px;"></i>{{ $item->note }}</p>
                        @endif
                    </div>
                    <span class="order-item-price">Rp {{ number_format($item->price * $item->quantity, 0, ',', '.') }}</span>
                </div>
            @endforeach

            <div class="price-total">
                <span>Total Pembayaran</span>
                <span>Rp {{ number_format($order->total_price, 0, ',', '.') }}</span>
            </div>
        </div>

        {{-- Info Pesanan --}}
        <div>
            <div class="card" style="margin-bottom: 14px;">
                <p class="card-label">Info Pesanan</p>
                <div class="info-row">
                    <span class="info-label">Nomor Pesanan</span>
                    <span class="info-value">{{ $order->order_code }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Tanggal Pesan</span>
                    <span class="info-value">{{ $order->created_at->translatedFormat('d M Y, H:i') }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Status</span>
                    <span class="info-value">
                        <span class="badge badge-{{ $order->status }}">
                            {{ $statusLabels[$order->status] ?? ucfirst($order->status) }}
                        </span>
                    </span>
                </div>
                <div class="info-row">
                    <span class="info-label">Pembayaran</span>
                    <span class="info-value">
                        <span class="badge badge-{{ $order->payment->status ?? 'unpaid' }}">
                            {{ $order->payment && $order->payment->status === 'paid' ? 'Lunas' : 'Belum Dibayar' }}
                        </span>
                    </span>
                </div>
                <div class="info-row">
                    <span class="info-label">Metode</span>
                    <span class="info-value">
                        {{ ['transfer_bank' => 'Transfer Bank', 'ewallet' => 'E-Wallet', 'qris' => 'QRIS', 'cod' => 'COD'][$order->payment->payment_method ?? ''] ?? ucfirst($order->payment->payment_method ?? '-') }}
                    </span>
                </div>
                <div class="info-row">
                    <span class="info-label">Alamat Kirim</span>
                    <span class="info-value">{{ $order->shipping_address }}</span>
                </div>
                @if($order->notes)
                    <div class="info-row">
                        <span class="info-label">Catatan</span>
                        <span class="info-value">{{ $order->notes }}</span>
                    </div>
                @endif
            </div>

            @if($order->status === 'pending' && $order->payment && $order->payment->status === 'unpaid')
                <a href="{{ route('orders.payment', $order) }}" class="btn-pay">
                    <i class="fa-solid fa-credit-card"></i> Bayar Sekarang
                </a>
            @endif
        </div>
    </div>

    <div>
        <a href="{{ route('orders.index') }}" class="btn-back">
            <i class="fa-solid fa-arrow-left"></i> Daftar Pesanan
        </a>
        <a href="{{ route('orders.show', $order) }}" class="btn-detail">
            <i class="fa-solid fa-receipt"></i> Detail Lengkap
        </a>
    </div>
</div>

@include('partials.footer')
<script src="{{ asset('js/app.js') }}" defer></script>
</body>
</html>
