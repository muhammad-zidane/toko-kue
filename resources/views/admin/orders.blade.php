@extends('admin.layout')
@section('title', 'Kelola Pesanan')
@section('page-title', 'Kelola Pesanan')
@section('page-subtitle', 'Lihat dan kelola semua pesanan pelanggan')

@push('styles')
<style>
    .card { background: white; border-radius: 16px; border: 1px solid #EDE0D4; overflow: hidden; }
    table { width: 100%; border-collapse: collapse; }
    th { text-align: left; font-size: 12px; font-weight: 700; color: var(--gray); text-transform: uppercase; padding: 12px 16px; border-bottom: 2px solid #EDE0D4; }
    td { padding: 14px 16px; font-size: 14px; border-bottom: 1px solid #F0E8E0; vertical-align: middle; }
    .badge { display: inline-block; padding: 4px 12px; border-radius: 20px; font-size: 12px; font-weight: 600; }
    .badge-pending { background: #FEF3C7; color: #D97706; }
    .badge-processing { background: #DBEAFE; color: #2563EB; }
    .badge-completed { background: #DCFCE7; color: #16A34A; }
    .badge-cancelled { background: #FEE2E2; color: #DC2626; }
    .badge-unpaid { background: #FEF3C7; color: #D97706; }
    .badge-paid { background: #DCFCE7; color: #16A34A; }
    .status-actions { display: flex; gap: 6px; flex-wrap: wrap; }
    .btn-status { padding: 4px 10px; border-radius: 6px; font-size: 11px; font-weight: 600; border: none; cursor: pointer; font-family: 'Plus Jakarta Sans', sans-serif; transition: opacity 0.2s; }
    .btn-status:hover { opacity: 0.8; }
    .btn-processing { background: #DBEAFE; color: #2563EB; }
    .btn-completed { background: #DCFCE7; color: #16A34A; }
    .btn-cancelled { background: #FEE2E2; color: #DC2626; }
    .btn-detail { padding: 4px 10px; border-radius: 6px; font-size: 11px; font-weight: 600; background: #F3F4F6; color: var(--text-dark); }
    .btn-detail:hover { opacity: 0.8; }
    .pagination { display: flex; justify-content: center; gap: 8px; margin-top: 24px; }
    .pagination a, .pagination span { padding: 8px 14px; border-radius: 8px; font-size: 13px; font-weight: 600; }
    .pagination a { background: var(--cream-dark); color: var(--text-dark); }
    .pagination a:hover { background: var(--pink); color: white; }
    .pagination .current { background: var(--pink); color: white; }
</style>
@endpush

@section('content')
<div class="card">
    <div style="overflow-x:auto;">
        <table>
            <thead>
                <tr>
                    <th>Kode</th>
                    <th>Pelanggan</th>
                    <th>Total</th>
                    <th>Status Order</th>
                    <th>Status Bayar</th>
                    <th>Tanggal</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($orders as $order)
                <tr>
                    <td style="font-weight:600;">{{ $order->order_code }}</td>
                    <td>{{ $order->user->name ?? '-' }}</td>
                    <td>Rp {{ number_format($order->total_price, 0, ',', '.') }}</td>
                    <td>
                        <span class="badge badge-{{ $order->status }}">{{ ucfirst($order->status) }}</span>
                    </td>
                    <td>
                        <span class="badge badge-{{ $order->payment->status ?? 'unpaid' }}">{{ ucfirst($order->payment->status ?? 'unpaid') }}</span>
                    </td>
                    <td>{{ $order->created_at->format('d M Y, H:i') }}</td>
                    <td>
                        <div class="status-actions">
                            <a href="{{ route('admin.orders.show', $order) }}" class="btn-detail">Detail</a>
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
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" style="text-align:center;color:var(--gray);padding:32px;">Belum ada pesanan.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="pagination">
        {{ $orders->links('pagination::simple-bootstrap-5') }}
    </div>
</div>
@endsection

