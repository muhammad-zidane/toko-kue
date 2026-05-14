@extends('admin.layout')
@section('title', 'Detail Pesanan')
@section('page-title', 'Detail Pesanan #' . $order->order_code)
@section('page-subtitle', 'Dibuat pada ' . $order->created_at->format('d M Y, H:i'))

@section('styles')
<style>
    .detail-grid { display: grid; grid-template-columns: 1fr 360px; gap: 20px; }
    .card { background: white; border-radius: 16px; border: 1px solid #EDE0D4; padding: 24px; margin-bottom: 20px; }
    .card-title { font-size: 14px; font-weight: 700; color: var(--text-dark); margin-bottom: 16px; padding-bottom: 12px; border-bottom: 1px solid #F0E8E0; }
    .info-row { display: flex; justify-content: space-between; align-items: center; padding: 8px 0; font-size: 13px; }
    .info-label { color: var(--gray); flex-shrink: 0; }
    .info-value { font-weight: 600; color: var(--text-dark); text-align: right; }
    .badge { display: inline-block; padding: 4px 12px; border-radius: 20px; font-size: 12px; font-weight: 600; white-space: nowrap; }
    .badge-pending { background: #FEF3C7; color: #D97706; }
    .badge-processing { background: #DBEAFE; color: #2563EB; }
    .badge-completed { background: #DCFCE7; color: #16A34A; }
    .badge-cancelled { background: #FEE2E2; color: #DC2626; }
    .badge-unpaid { background: #FEF3C7; color: #D97706; }
    .badge-paid { background: #DCFCE7; color: #16A34A; }
    .item-row { display: flex; align-items: center; gap: 12px; padding: 12px 0; border-bottom: 1px solid #F9F4EE; }
    .item-row:last-child { border-bottom: none; }
    .item-img { width: 56px; height: 56px; border-radius: 10px; background: var(--cream); display: flex; align-items: center; justify-content: center; overflow: hidden; flex-shrink: 0; }
    .item-img img { width: 100%; height: 100%; object-fit: cover; }
    .item-name { font-size: 14px; font-weight: 600; color: var(--text-dark); }
    .item-qty { font-size: 13px; color: var(--gray); }
    .item-price { font-size: 14px; font-weight: 700; color: var(--text-dark); margin-left: auto; }
    .status-actions { display: flex; gap: 8px; flex-wrap: wrap; margin-top: 16px; }
    .btn-status { padding: 8px 16px; border-radius: 8px; font-size: 13px; font-weight: 600; border: none; cursor: pointer; font-family: 'Plus Jakarta Sans', sans-serif; transition: opacity 0.2s; }
    .btn-status:hover { opacity: 0.8; }
    .btn-processing { background: #DBEAFE; color: #2563EB; }
    .btn-completed { background: #DCFCE7; color: #16A34A; }
    .btn-cancelled { background: #FEE2E2; color: #DC2626; }
    .proof-img { max-width: 100%; border-radius: 12px; margin-top: 12px; border: 1px solid #EDE0D4; display: block; }
    .btn-download { display: inline-flex; align-items: center; gap: 8px; margin-top: 10px; background: var(--brown-dark); color: white; padding: 8px 16px; border-radius: 8px; font-size: 12px; font-weight: 600; transition: opacity 0.2s; }
    .btn-download:hover { opacity: 0.85; }
    .proof-img.expanded { max-width: 100%; width: 100%; cursor: zoom-out; }
    .proof-img:not(.expanded) { max-height: 200px; object-fit: cover; cursor: zoom-in; }
    @media (max-width: 768px) { .detail-grid { grid-template-columns: 1fr; } }
</style>
@endsection

@section('content')
<div style="margin-bottom:16px;">
    <a href="{{ route('admin.orders') }}" style="font-size:13px;font-weight:600;color:var(--brown-dark);">← Kembali ke Pesanan</a>
</div>

<div class="detail-grid">
    {{-- KIRI --}}
    <div>
        {{-- DAFTAR ITEM --}}
        <div class="card">
            <div class="card-title">Produk Dipesan ({{ $order->orderItems->count() }} item)</div>
            @foreach($order->orderItems as $item)
            <div class="item-row">
                <div class="item-img">
                    @if($item->product->image)
                        <img src="{{ asset('storage/' . $item->product->image) }}" alt="{{ $item->product->name }}">
                    @else
                        <span style="font-size:24px;">🧁</span>
                    @endif
                </div>
                <div>
                    <div class="item-name">{{ $item->product->name }}</div>
                    <div class="item-qty">{{ $item->quantity }} x Rp {{ number_format($item->price, 0, ',', '.') }}</div>
                </div>
                <div class="item-price">Rp {{ number_format($item->price * $item->quantity, 0, ',', '.') }}</div>
            </div>
            @endforeach
        </div>

        {{-- CATATAN --}}
        @if($order->notes)
        <div class="card">
            <div class="card-title">Catatan</div>
            <p style="font-size:14px;color:var(--text-dark);line-height:1.6;">{{ $order->notes }}</p>
        </div>
        @endif
    </div>

    {{-- KANAN --}}
    <div>
        {{-- INFO PESANAN --}}
        <div class="card">
            <div class="card-title">Informasi Pesanan</div>
            <div class="info-row">
                <span class="info-label">Kode Pesanan</span>
                <span class="info-value">{{ $order->order_code }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Status</span>
                <span class="badge badge-{{ $order->status }}">{{ ucfirst($order->status) }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Total</span>
                <span class="info-value" style="color:var(--pink);">Rp {{ number_format($order->total_price, 0, ',', '.') }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Alamat Pengiriman</span>
                <span class="info-value" style="max-width:200px;text-align:right;">{{ $order->shipping_address }}</span>
            </div>

            {{-- UBAH STATUS --}}
            <div class="card-title" style="margin-top:16px;">Ubah Status</div>
            <div class="status-actions">
                @if($order->status !== 'processing')
                <form method="POST" action="{{ route('admin.orders.status', [$order, 'processing']) }}">
                    @csrf @method('PATCH')
                    <button class="btn-status btn-processing">Proses</button>
                </form>
                @endif
                @if($order->status !== 'completed')
                <form method="POST" action="{{ route('admin.orders.status', [$order, 'completed']) }}">
                    @csrf @method('PATCH')
                    <button class="btn-status btn-completed">Selesai</button>
                </form>
                @endif
                @if($order->status !== 'cancelled')
                <form method="POST" action="{{ route('admin.orders.status', [$order, 'cancelled']) }}">
                    @csrf @method('PATCH')
                    <button class="btn-status btn-cancelled">Batal</button>
                </form>
                @endif
            </div>
        </div>

        {{-- INFO PELANGGAN --}}
        <div class="card">
            <div class="card-title">Pelanggan</div>
            <div style="display:flex;align-items:center;gap:12px;">
                <div style="width:44px;height:44px;border-radius:50%;background:var(--blue);color:white;font-size:16px;font-weight:700;display:flex;align-items:center;justify-content:center;">
                    {{ strtoupper(substr($order->user->name ?? '-', 0, 2)) }}
                </div>
                <div>
                    <div style="font-size:14px;font-weight:700;">{{ $order->user->name ?? '-' }}</div>
                    <div style="font-size:12px;color:var(--gray);">{{ $order->user->email ?? '-' }}</div>
                </div>
            </div>
        </div>

        {{-- INFO PEMBAYARAN --}}
        <div class="card">
            <div class="card-title">Pembayaran</div>
            @if($order->payment)
            <div class="info-row">
                <span class="info-label">Metode</span>
                <span class="info-value">{{ ['transfer_bank'=>'Transfer Bank','ewallet'=>'E-Wallet','qris'=>'QRIS','cod'=>'COD'][$order->payment->payment_method ?? ''] ?? strtoupper($order->payment->payment_method ?? '-') }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Status</span>
                <span class="badge badge-{{ $order->payment->status }}">{{ ucfirst($order->payment->status) }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Jumlah</span>
                <span class="info-value">Rp {{ number_format($order->payment->amount, 0, ',', '.') }}</span>
            </div>
            @if($order->payment->paid_at)
            <div class="info-row">
                <span class="info-label">Dibayar Pada</span>
                <span class="info-value">{{ $order->payment->paid_at->format('d M Y, H:i') ?? '-' }}</span>
            </div>
            @endif
            @if($order->payment->proof_image)
            <div style="margin-top:12px;">
                <p style="font-size:12px;font-weight:600;color:var(--gray);margin-bottom:8px;">Bukti Pembayaran:</p>
                <img src="{{ asset('storage/' . $order->payment->proof_image) }}"
                     alt="Bukti Pembayaran"
                     class="proof-img"
                     onclick="this.classList.toggle('expanded')"
                     style="cursor:zoom-in;">
                <a href="{{ route('admin.orders.downloadProof', $order) }}" class="btn-download">
                    <i class="fas fa-download" style="color:white;"></i> Unduh Bukti
                </a>
            </div>
            @endif
            @else
            <p style="font-size:13px;color:var(--gray);">Belum ada data pembayaran.</p>
            @endif
        </div>
    </div>
</div>
@endsection
