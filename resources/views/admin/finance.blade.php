@extends('admin.layout')
@section('title', 'Keuangan')
@section('page-title', 'Keuangan')
@section('page-subtitle', 'Pantau arus kas dan riwayat pembayaran')

@section('styles')
<style>
    .stats-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 16px; margin-bottom: 24px; }
    .stat-card { background: white; border-radius: 16px; padding: 20px; border: 1px solid #EDE0D4; }
    .stat-icon { width: 44px; height: 44px; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 20px; margin-bottom: 12px; }
    .stat-value { font-size: 24px; font-weight: 800; }
    .stat-label { font-size: 12px; color: var(--gray); margin-top: 4px; }
    .card { background: white; border-radius: 16px; border: 1px solid #EDE0D4; overflow: hidden; }
    table { width: 100%; border-collapse: collapse; }
    th { padding: 12px 16px; font-size: 11px; font-weight: 700; color: var(--gray); text-transform: uppercase; letter-spacing: 0.5px; background: var(--cream); border-bottom: 1px solid #EDE0D4; text-align: left; }
    td { padding: 14px 16px; font-size: 14px; border-bottom: 1px solid rgba(237,224,212,0.5); vertical-align: middle; }
    tr:hover { background: rgba(255,248,238,0.3); }
    .badge { display: inline-block; padding: 4px 10px; border-radius: 20px; font-size: 11px; font-weight: 600; }
    .badge-paid { background: #DCFCE7; color: #16A34A; }
    .badge-unpaid { background: #FEF3C7; color: #D97706; }
    .badge-failed { background: #FEE2E2; color: #DC2626; }
    .method-badge { background: var(--cream); padding: 4px 10px; border-radius: 8px; font-size: 11px; font-weight: 600; color: var(--brown-dark); text-transform: uppercase; }
    @media (max-width: 1024px) { .stats-grid { grid-template-columns: repeat(2, 1fr); } }
    @media (max-width: 640px) { .stats-grid { grid-template-columns: 1fr; } }
</style>
@endsection

@section('content')
{{-- STATS --}}
<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-icon" style="background:rgba(34,197,94,0.1);"><i class="fas fa-money-bill-wave" style="color:var(--pink)"></i></div>
        <div class="stat-value">Rp {{ number_format($totalRevenue, 0, ',', '.') }}</div>
        <div class="stat-label">Total Pendapatan</div>
    </div>
    <div class="stat-card">
        <div class="stat-icon" style="background:rgba(245,158,11,0.1);"><i class="fas fa-hourglass-half" style="color:var(--pink)"></i></div>
        <div class="stat-value">Rp {{ number_format($pendingPayments, 0, ',', '.') }}</div>
        <div class="stat-label">Menunggu Pembayaran</div>
    </div>
    <div class="stat-card">
        <div class="stat-icon" style="background:rgba(240,80,122,0.1);"><i class="fas fa-check-circle" style="color:var(--pink)"></i></div>
        <div class="stat-value">{{ $paidCount }}</div>
        <div class="stat-label">Pembayaran Lunas</div>
    </div>
    <div class="stat-card">
        <div class="stat-icon" style="background:rgba(59,130,246,0.1);"><i class="fas fa-clock" style="color:var(--pink)"></i></div>
        <div class="stat-value">{{ $pendingCount }}</div>
        <div class="stat-label">Belum Dibayar</div>
    </div>
</div>

{{-- TABLE --}}
<div class="card">
    <div style="overflow-x:auto;">
        <table>
            <thead>
                <tr>
                    <th>#</th>
                    <th>Kode Pesanan</th>
                    <th>Pelanggan</th>
                    <th>Metode</th>
                    <th>Jumlah</th>
                    <th>Status</th>
                    <th>Tanggal</th>
                </tr>
            </thead>
            <tbody>
                @forelse($payments as $i => $payment)
                <tr>
                    <td style="font-weight:600;color:var(--gray);">{{ $i + 1 }}</td>
                    <td style="font-weight:700;">#{{ $payment->order->order_code ?? '-' }}</td>
                    <td>{{ $payment->order->user->name ?? '-' }}</td>
                    <td><span class="method-badge">{{ ['transfer_bank'=>'Transfer Bank','ewallet'=>'E-Wallet','qris'=>'QRIS','cod'=>'COD'][$payment->payment_method ?? ''] ?? ($payment->payment_method ?? '-') }}</span></td>
                    <td style="font-weight:700;color:var(--brown-dark);">Rp {{ number_format($payment->amount, 0, ',', '.') }}</td>
                    <td>
                        @if($payment->status === 'paid')
                            <span class="badge badge-paid">Lunas</span>
                        @elseif($payment->status === 'unpaid')
                            <span class="badge badge-unpaid">Menunggu</span>
                        @else
                            <span class="badge badge-failed">Gagal</span>
                        @endif
                    </td>
                    <td style="font-size:12px;color:var(--gray);">{{ $payment->created_at->format('d M Y, H:i') }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" style="text-align:center;padding:64px;">
                        <div style="font-size:48px;margin-bottom:12px;"><i class="fas fa-credit-card" style="color:var(--pink)"></i></div>
                        <h3 style="font-weight:700;color:var(--brown-dark);">Belum Ada Transaksi</h3>
                        <p style="font-size:14px;color:var(--gray);">Transaksi pembayaran akan muncul di sini.</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
