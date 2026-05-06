@extends('admin.layout')
@section('title', 'Dashboard')
@section('page-title', 'Dashboard Admin')
@section('page-subtitle', 'Selamat datang, ' . auth()->user()->name . '!')

@section('styles')
<style>
    .stats-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 16px; margin-bottom: 24px; }
    .stat-card { background: white; border-radius: 16px; padding: 20px; border: 1px solid #EDE0D4; }
    .stat-icon { width: 44px; height: 44px; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 20px; margin-bottom: 12px; }
    .stat-value { font-size: 26px; font-weight: 800; color: var(--text-dark); margin-bottom: 2px; }
    .stat-label { font-size: 12px; color: var(--gray); margin-bottom: 6px; }
    .stat-growth { font-size: 12px; font-weight: 600; }
    .growth-up { color: #22C55E; }
    .growth-down { color: #EF4444; }

    .middle-grid { display: grid; grid-template-columns: 1fr 320px; gap: 20px; margin-bottom: 24px; }
    .card { background: white; border-radius: 16px; border: 1px solid #EDE0D4; overflow: hidden; }
    .card-header { display: flex; align-items: center; justify-content: space-between; padding: 18px 20px; border-bottom: 1px solid #F0E8E0; }
    .card-header h3 { font-size: 14px; font-weight: 700; color: var(--text-dark); }
    .card-header a { font-size: 13px; color: var(--pink); font-weight: 600; }

    table { width: 100%; border-collapse: collapse; }
    th { padding: 10px 16px; font-size: 11px; font-weight: 700; color: var(--gray); text-transform: uppercase; letter-spacing: 0.5px; background: #FAFAF8; border-bottom: 1px solid #F0E8E0; text-align: left; }
    td { padding: 12px 16px; font-size: 13px; border-bottom: 1px solid #F9F4EE; }
    tr:last-child td { border-bottom: none; }
    tr:hover { background: #FAFAF8; }

    .badge { display: inline-block; padding: 2px 10px; border-radius: 20px; font-size: 11px; font-weight: 600; }
    .badge-pending { background: #DBEAFE; color: #2563EB; }
    .badge-processing { background: #FEF3C7; color: #D97706; }
    .badge-completed { background: #DCFCE7; color: #16A34A; }
    .badge-cancelled { background: #FEE2E2; color: #DC2626; }

    .avatar-sm { width: 26px; height: 26px; border-radius: 50%; background: var(--blue); color: white; font-size: 10px; font-weight: 700; display: inline-flex; align-items: center; justify-content: center; flex-shrink: 0; }
    .btn-detail { padding: 4px 10px; border-radius: 6px; font-size: 11px; font-weight: 600; background: #F3F4F6; color: var(--text-dark); }
    .btn-detail:hover { opacity: 0.8; }

    .activity-item { display: flex; align-items: flex-start; gap: 10px; padding: 8px 0; border-bottom: 1px solid #F9F4EE; }
    .activity-item:last-child { border-bottom: none; }
    .activity-dot { width: 10px; height: 10px; border-radius: 50%; flex-shrink: 0; margin-top: 4px; }
    .activity-dot.bg-pink { background: var(--pink); }
    .activity-dot.bg-blue { background: var(--blue); }
    .activity-dot.bg-green { background: var(--green); }
    .activity-dot.bg-red { background: var(--red); }
    .activity-text { font-size: 12px; color: var(--text-dark); line-height: 1.5; }
    .activity-time { font-size: 11px; color: var(--gray); margin-top: 2px; }

    .quick-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 10px; }
    .quick-link { background: var(--cream); border-radius: 12px; padding: 12px; display: flex; align-items: flex-start; gap: 10px; transition: background 0.2s; }
    .quick-link:hover { background: var(--cream-dark); }
    .quick-link-icon { font-size: 18px; flex-shrink: 0; transition: transform 0.2s; }
    .quick-link:hover .quick-link-icon { transform: scale(1.1); }
    .quick-link-title { font-size: 12px; font-weight: 700; color: var(--text-dark); }
    .quick-link-desc { font-size: 11px; color: var(--gray); margin-top: 2px; }

    .bottom-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; }
    .chart-bars { display: flex; align-items: flex-end; gap: 8px; height: 80px; margin-bottom: 10px; }
    .chart-bar-col { flex: 1; display: flex; flex-direction: column; align-items: center; gap: 4px; height: 100%; justify-content: flex-end; }
    .chart-bar { width: 100%; border-radius: 3px 3px 0 0; min-height: 4px; transition: all 0.3s; }
    .chart-bar.high { background: var(--pink); }
    .chart-bar.mid { background: #F5EDD8; }
    .chart-bar.low { background: #EDE0D4; }
    .chart-label { font-size: 10px; color: var(--gray); }
    .chart-legend { display: flex; gap: 16px; margin-top: 8px; }
    .legend-item { display: flex; align-items: center; gap: 6px; font-size: 11px; color: var(--gray); }
    .legend-dot { width: 8px; height: 8px; border-radius: 50%; }

    .top-product-item { display: flex; align-items: center; gap: 12px; padding: 10px 0; border-bottom: 1px solid #F9F4EE; }
    .top-product-item:last-child { border-bottom: none; }
    .top-product-rank { font-size: 13px; font-weight: 700; color: var(--gray); width: 16px; flex-shrink: 0; }
    .top-product-info { flex: 1; }
    .top-product-name { font-size: 13px; font-weight: 600; color: var(--text-dark); }
    .top-product-cat { font-size: 11px; color: var(--gray); }
    .top-product-sold { font-size: 12px; font-weight: 700; color: var(--text-dark); }
    .top-product-bar { width: 60px; height: 3px; background: #EDE0D4; border-radius: 2px; margin-top: 4px; }
    .top-product-bar-fill { height: 3px; background: var(--pink); border-radius: 2px; }

    @media (max-width: 1024px) {
        .stats-grid { grid-template-columns: repeat(2, 1fr); }
        .middle-grid { grid-template-columns: 1fr; }
        .bottom-grid { grid-template-columns: 1fr; }
    }
    @media (max-width: 640px) {
        .stats-grid { grid-template-columns: 1fr; }
    }
</style>
@endsection

@section('content')
{{-- STATS --}}
<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-icon" style="background:rgba(240,80,122,0.1);">📋</div>
        <div class="stat-value">{{ $ordersThisMonth }}</div>
        <div class="stat-label">Total Pesanan Bulan Ini</div>
        <div class="stat-growth {{ $orderGrowth >= 0 ? 'growth-up' : 'growth-down' }}">
            {{ $orderGrowth >= 0 ? '+' : '' }}{{ $orderGrowth }}% dari bulan lalu
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon" style="background:rgba(20,184,166,0.1);">💰</div>
        <div class="stat-value" style="color:#0D9488;">Rp {{ number_format($revenueThisMonth/1000, 0, ',', '.') }}k</div>
        <div class="stat-label">Pendapatan Bulan Ini</div>
        <div class="stat-growth {{ $revenueGrowth >= 0 ? 'growth-up' : 'growth-down' }}">
            {{ $revenueGrowth >= 0 ? '+' : '' }}{{ $revenueGrowth }}% dari bulan lalu
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon" style="background:rgba(59,130,246,0.1);">👤</div>
        <div class="stat-value">{{ $customersThisMonth }}</div>
        <div class="stat-label">Pelanggan Baru</div>
        <div class="stat-growth {{ $customerGrowth >= 0 ? 'growth-up' : 'growth-down' }}">
            {{ $customerGrowth >= 0 ? '+' : '' }}{{ $customerGrowth }}% dari bulan lalu
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon" style="background:var(--cream);">🏠</div>
        <div class="stat-value">{{ $pendingOrdersCount }}</div>
        <div class="stat-label">Pesanan Perlu Diproses</div>
        @if($pendingOrdersCount > 0)
            <div class="stat-growth" style="color:var(--pink);">Segera proses!</div>
        @else
            <div class="stat-growth" style="color:var(--gray);">Semua pesanan tertangani</div>
        @endif
    </div>
</div>

{{-- MIDDLE --}}
<div class="middle-grid">
    {{-- TABEL PESANAN --}}
    <div class="card">
        <div class="card-header">
            <h3>Pesanan Terbaru</h3>
            <a href="{{ route('admin.orders') }}">Lihat Semua →</a>
        </div>
        <div style="overflow-x:auto;">
            <table style="min-width:700px;">
                <thead>
                    <tr>
                        <th>No. Pesanan</th>
                        <th>Pelanggan</th>
                        <th>Produk</th>
                        <th>Total</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($latestOrders as $order)
                    <tr>
                        <td style="font-weight:700;">{{ $order->order_code }}</td>
                        <td>
                            <div style="display:flex;align-items:center;gap:8px;">
                                <span class="avatar-sm">{{ strtoupper(substr($order->user->name ?? '-', 0, 2)) }}</span>
                                {{ $order->user->name ?? '-' }}
                            </div>
                        </td>
                        <td>
                            {{ $order->orderItems->first()->product->name ?? '-' }}
                            @if($order->orderItems->count() > 1)
                                (+{{ $order->orderItems->count() - 1 }})
                            @endif
                        </td>
                        <td style="font-weight:600;">Rp {{ number_format($order->total_price, 0, ',', '.') }}</td>
                        <td>
                            <span class="badge badge-{{ $order->status }}">
                                @switch($order->status)
                                    @case('pending') Menunggu @break
                                    @case('processing') Diproses @break
                                    @case('completed') Selesai @break
                                    @case('cancelled') Dibatalkan @break
                                    @default {{ ucfirst($order->status) }}
                                @endswitch
                            </span>
                        </td>
                        <td>
                            <a href="{{ route('admin.orders.detail', $order) }}" class="btn-detail">Detail</a>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="6" style="text-align:center;color:var(--gray);padding:24px;">Belum ada pesanan terbaru</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- KANAN --}}
    <div style="display:flex;flex-direction:column;gap:20px;">
        {{-- AKTIVITAS --}}
        <div class="card" style="padding:18px 20px;">
            <h3 style="font-size:14px;font-weight:700;color:var(--text-dark);margin-bottom:14px;">Aktivitas Terkini</h3>
            @forelse($recentActivities as $act)
            <div class="activity-item">
                <div class="activity-dot {{ $act['colorClass'] }}"></div>
                <div>
                    <p class="activity-text">{!! $act['message'] !!}</p>
                    <span class="activity-time">{{ $act['timeLabel'] }}</span>
                </div>
            </div>
            @empty
            <p style="font-size:12px;color:var(--gray);padding:8px 0;">Belum ada aktivitas</p>
            @endforelse
        </div>

        {{-- AKSI CEPAT --}}
        <div class="card" style="padding:18px 20px;">
            <h3 style="font-size:14px;font-weight:700;color:var(--text-dark);margin-bottom:14px;">Aksi Cepat</h3>
            <div class="quick-grid">
                <a href="{{ route('admin.products.create') }}" class="quick-link">
                    <span class="quick-link-icon">➕</span>
                    <div>
                        <div class="quick-link-title">Tambah Produk</div>
                        <div class="quick-link-desc">Daftarkan kue baru</div>
                    </div>
                </a>
                <a href="{{ route('admin.analytics') }}" class="quick-link">
                    <span class="quick-link-icon">📊</span>
                    <div>
                        <div class="quick-link-title">Lihat Laporan</div>
                        <div class="quick-link-desc">Analisis penjualan</div>
                    </div>
                </a>
                <a href="{{ route('admin.orders') }}" class="quick-link">
                    <span class="quick-link-icon">📋</span>
                    <div>
                        <div class="quick-link-title">Kelola Pesanan</div>
                        <div class="quick-link-desc">Lihat semua pesanan</div>
                    </div>
                </a>
                <a href="{{ route('admin.customers') }}" class="quick-link">
                    <span class="quick-link-icon">👥</span>
                    <div>
                        <div class="quick-link-title">Data Pelanggan</div>
                        <div class="quick-link-desc">Lihat Semua User</div>
                    </div>
                </a>
            </div>
        </div>
    </div>
</div>

{{-- BOTTOM --}}
<div class="bottom-grid">
    {{-- GRAFIK --}}
    <div class="card" style="padding:20px;">
        <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:20px;">
            <h3 style="font-size:13px;font-weight:700;color:var(--text-dark);">Pendapatan 7 Hari Terakhir</h3>
            <span style="font-size:12px;color:var(--pink);font-weight:600;">Rp {{ number_format($revenueThisWeek/1000, 0, ',', '.') }}k minggu ini</span>
        </div>
        <div class="chart-bars">
            @foreach($dailyRevenue as $d)
                @php
                    $pct = $maxDaily > 0 ? ($d['amount'] / $maxDaily) * 100 : 0;
                    $barClass = $pct > 70 ? 'high' : ($pct > 30 ? 'mid' : 'low');
                @endphp
                <div class="chart-bar-col">
                    <div class="chart-bar {{ $barClass }}" style="height:{{ max($pct, 5) }}%"></div>
                    <span class="chart-label">{{ $d['day'] }}</span>
                </div>
            @endforeach
        </div>
        <div class="chart-legend">
            <div class="legend-item"><div class="legend-dot" style="background:var(--pink);"></div> Tertinggi</div>
            <div class="legend-item"><div class="legend-dot" style="background:#F5EDD8;border:1px solid #D1C0B8;"></div> Normal</div>
            <div class="legend-item"><div class="legend-dot" style="background:#EDE0D4;"></div> Rendah</div>
        </div>
    </div>

    {{-- TOP PRODUK --}}
    <div class="card" style="padding:20px;">
        <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:20px;">
            <h3 style="font-size:13px;font-weight:700;color:var(--text-dark);">Produk Terlaris</h3>
            <span style="font-size:12px;color:var(--gray);">Semua Waktu</span>
        </div>
        @forelse($topProducts as $i => $p)
        <div class="top-product-item">
            <span class="top-product-rank">{{ $i + 1 }}</span>
            <div class="top-product-info">
                <div class="top-product-name">{{ $p->name }}</div>
                <div class="top-product-cat">{{ $p->category->name ?? '-' }}</div>
            </div>
            <div style="text-align:right;">
                <div class="top-product-sold">{{ $p->order_items_count }} terjual</div>
                <div class="top-product-bar">
                    <div class="top-product-bar-fill" style="width:{{ ($p->order_items_count / $maxSold) * 100 }}%"></div>
                </div>
            </div>
        </div>
        @empty
        <p style="text-align:center;font-size:12px;color:var(--gray);padding:16px;">Belum ada data penjualan</p>
        @endforelse
    </div>
</div>
@endsection
