@extends('admin.layout')
@section('title', 'Analisis')
@section('page-title', 'Analisis & Laporan')
@section('page-subtitle', 'Pantau performa bisnis secara keseluruhan')

@push('styles')
<style>
    .stats-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 16px; margin-bottom: 24px; }
    .stat-card { background: white; border-radius: 16px; padding: 20px; border: 1px solid #EDE0D4; transition: transform 0.2s; }
    .stat-card:hover { transform: translateY(-2px); }
    .stat-icon { width: 44px; height: 44px; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 20px; margin-bottom: 12px; }
    .stat-value { font-size: 24px; font-weight: 800; }
    .stat-label { font-size: 12px; color: var(--gray); margin-top: 4px; }
    .stat-growth { font-size: 12px; font-weight: 600; margin-top: 4px; }
    .two-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 20px; }
    .card { background: white; border-radius: 16px; border: 1px solid #EDE0D4; padding: 24px; margin-bottom: 0; }
    .card-title { font-size: 14px; font-weight: 700; color: var(--text-dark); margin-bottom: 20px; display: flex; align-items: center; gap: 8px; }
    .filter-bar { background: white; border-radius: 16px; border: 1px solid #EDE0D4; padding: 16px 24px; margin-bottom: 20px; display: flex; align-items: center; gap: 12px; flex-wrap: wrap; }
    .filter-bar label { font-size: 13px; font-weight: 600; color: var(--text-dark); }
    .filter-bar input[type=date] { border: 1px solid #D1C4C0; border-radius: 8px; padding: 7px 12px; font-size: 13px; font-family: 'Plus Jakarta Sans', sans-serif; }
    .btn-filter { background: var(--pink); color: white; border: none; padding: 8px 20px; border-radius: 8px; font-size: 13px; font-weight: 600; cursor: pointer; font-family: 'Plus Jakarta Sans', sans-serif; }
    .btn-export { background: #22C55E; color: white; border: none; padding: 8px 20px; border-radius: 8px; font-size: 13px; font-weight: 600; cursor: pointer; font-family: 'Plus Jakarta Sans', sans-serif; text-decoration: none; display: inline-flex; align-items: center; gap: 6px; }
    .top-item { display: flex; align-items: center; gap: 12px; padding: 10px 0; border-bottom: 1px solid rgba(237,224,212,0.5); }
    .top-item:last-child { border-bottom: none; }
    .top-rank { font-size: 13px; font-weight: 800; width: 24px; text-align: center; }
    .top-info { flex: 1; }
    .top-name { font-size: 13px; font-weight: 600; }
    .top-cat { font-size: 11px; color: var(--gray); }
    .top-sold { font-size: 12px; font-weight: 700; color: var(--pink); white-space: nowrap; }
    .top-bar { width: 70px; height: 4px; background: var(--cream); border-radius: 2px; margin-top: 4px; }
    .top-bar-fill { height: 4px; background: var(--pink); border-radius: 2px; }
    .donut-container { display: flex; align-items: center; gap: 32px; padding: 10px 0; }
    .donut { width: 130px; height: 130px; border-radius: 50%; position: relative; flex-shrink: 0; }
    .donut-center { position: absolute; inset: 28px; background: white; border-radius: 50%; display: flex; flex-direction: column; align-items: center; justify-content: center; }
    .donut-count { font-size: 18px; font-weight: 800; }
    .donut-label { font-size: 10px; color: var(--gray); }
    .legend { display: flex; flex-direction: column; gap: 8px; }
    .legend-item { display: flex; align-items: center; gap: 8px; font-size: 12px; }
    .legend-dot { width: 10px; height: 10px; border-radius: 50%; flex-shrink: 0; }
    .legend-val { font-weight: 700; margin-left: auto; padding-left: 8px; }
    .cat-item { display: flex; align-items: center; justify-content: space-between; padding: 10px 0; border-bottom: 1px solid rgba(237,224,212,0.5); }
    .cat-item:last-child { border-bottom: none; }
    .cat-bar { flex: 1; max-width: 100px; height: 6px; background: var(--cream); border-radius: 3px; margin: 0 12px; }
    .cat-bar-fill { height: 6px; background: var(--pink); border-radius: 3px; }
    .filter-summary { background: #FFF7ED; border: 1px solid #FED7AA; border-radius: 10px; padding: 12px 16px; margin-bottom: 20px; font-size: 13px; color: #92400E; }
    @media (max-width: 1024px) { .stats-grid { grid-template-columns: repeat(2, 1fr); } .two-grid { grid-template-columns: 1fr; } }
    @media (max-width: 640px) { .stats-grid { grid-template-columns: 1fr; } .donut-container { flex-direction: column; } .filter-bar { flex-direction: column; align-items: flex-start; } }
</style>
@endpush

@section('content')

{{-- FILTER BAR --}}
<form method="GET" action="{{ route('admin.analytics.index') }}" class="filter-bar">
    <label>Dari:</label>
    <input type="date" name="dari" value="{{ $dari }}" max="{{ date('Y-m-d') }}">
    <label>Sampai:</label>
    <input type="date" name="sampai" value="{{ $sampai }}" max="{{ date('Y-m-d') }}">
    <button type="submit" class="btn-filter"><i class="fas fa-search" style="margin-right:6px;"></i>Filter</button>
    <a href="{{ route('admin.analytics.export', ['dari' => $dari, 'sampai' => $sampai]) }}" class="btn-export">
        <i class="fas fa-file-excel"></i> Export Excel
    </a>
</form>

{{-- FILTER SUMMARY STATS --}}
<div class="filter-summary">
    <strong>Periode {{ \Carbon\Carbon::parse($dari)->format('d M Y') }} – {{ \Carbon\Carbon::parse($sampai)->format('d M Y') }}:</strong>
    &nbsp;
    <strong>{{ $totalFilterOrders }}</strong> pesanan &nbsp;|&nbsp;
    <strong>Rp {{ number_format($totalFilterRevenue, 0, ',', '.') }}</strong> pendapatan &nbsp;|&nbsp;
    <strong>{{ $totalItemsTerjual }}</strong> item terjual &nbsp;|&nbsp;
    rata-rata <strong>Rp {{ number_format($avgFilterOrder, 0, ',', '.') }}</strong>/pesanan
</div>

{{-- STATS BULAN INI --}}
<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-icon" style="background:rgba(240,80,122,0.1);"><i class="fas fa-money-bill-wave" style="color:var(--pink)"></i></div>
        <div class="stat-value">Rp {{ number_format($revenueThisMonth/1000, 0, ',', '.') }}rb</div>
        <div class="stat-label">Pendapatan Bulan Ini</div>
        <div class="stat-growth" style="color:{{ $growthPercent >= 0 ? '#22C55E' : '#EF4444' }};">{{ $growthPercent >= 0 ? '+' : '' }}{{ $growthPercent }}% dari bulan lalu</div>
    </div>
    <div class="stat-card">
        <div class="stat-icon" style="background:rgba(59,130,246,0.1);"><i class="fas fa-box" style="color:var(--pink)"></i></div>
        <div class="stat-value">{{ $ordersThisMonth }}</div>
        <div class="stat-label">Pesanan Bulan Ini</div>
    </div>
    <div class="stat-card">
        <div class="stat-icon" style="background:rgba(245,158,11,0.1);"><i class="fas fa-chart-bar" style="color:var(--pink)"></i></div>
        <div class="stat-value">Rp {{ number_format($avgOrderValue/1000, 0, ',', '.') }}rb</div>
        <div class="stat-label">Rata-rata Per Pesanan</div>
    </div>
    <div class="stat-card">
        <div class="stat-icon" style="background:rgba(34,197,94,0.1);"><i class="fas fa-chart-line" style="color:var(--pink)"></i></div>
        <div class="stat-value">Rp {{ number_format($revenueLastMonth/1000, 0, ',', '.') }}rb</div>
        <div class="stat-label">Pendapatan Bulan Lalu</div>
    </div>
</div>

{{-- GRAFIK CHART.JS --}}
<div class="card" style="margin-bottom:20px;">
    <div class="card-title">
        <i class="fas fa-chart-line" style="color:var(--pink)"></i>
        Grafik Penjualan Per Hari
        <span style="font-size:11px;font-weight:400;color:var(--gray);margin-left:8px;">{{ \Carbon\Carbon::parse($dari)->format('d M') }} – {{ \Carbon\Carbon::parse($sampai)->format('d M Y') }}</span>
    </div>
    <canvas id="salesChart" height="80"></canvas>
</div>

{{-- TOP 10 & STATUS --}}
<div class="two-grid">
    <div class="card">
        <div class="card-title"><i class="fas fa-trophy" style="color:var(--pink)"></i> Top 10 Produk Terlaris</div>
        @forelse($topProducts as $i => $p)
        <div class="top-item">
            <span class="top-rank" style="color:{{ $i === 0 ? '#F59E0B' : ($i === 1 ? '#9CA3AF' : ($i === 2 ? '#B45309' : '#D1D5DB')) }};">{{ $i + 1 }}</span>
            <div class="top-info">
                <div class="top-name">{{ $p->name }}</div>
                <div class="top-cat">{{ $p->category->name ?? '-' }}</div>
            </div>
            <div style="text-align:right;">
                <div class="top-sold">{{ $p->order_items_count }} terjual</div>
                <div class="top-bar"><div class="top-bar-fill" style="width:{{ ($p->order_items_count / $maxSold) * 100 }}%"></div></div>
            </div>
        </div>
        @empty
        <p style="text-align:center;color:var(--gray);padding:20px;font-size:14px;">Belum ada data penjualan</p>
        @endforelse
    </div>

    <div class="card">
        <div class="card-title"><i class="fas fa-clipboard-list" style="color:var(--pink)"></i> Distribusi Status Pesanan</div>
        @php
            $pending    = $statusCounts['pending'] ?? 0;
            $processing = $statusCounts['processing'] ?? 0;
            $completed  = $statusCounts['completed'] ?? 0;
            $cancelled  = $statusCounts['cancelled'] ?? 0;
            $d1 = $pending / $totalOrdersAll * 360;
            $d2 = $d1 + ($processing / $totalOrdersAll * 360);
            $d3 = $d2 + ($completed / $totalOrdersAll * 360);
        @endphp
        <div class="donut-container">
            <div class="donut" style="background:conic-gradient(#3B82F6 0deg {{ $d1 }}deg, #F59E0B {{ $d1 }}deg {{ $d2 }}deg, #22C55E {{ $d2 }}deg {{ $d3 }}deg, #EF4444 {{ $d3 }}deg 360deg);">
                <div class="donut-center">
                    <span class="donut-count">{{ $totalOrdersAll }}</span>
                    <span class="donut-label">pesanan</span>
                </div>
            </div>
            <div class="legend">
                <div class="legend-item"><div class="legend-dot" style="background:#3B82F6;"></div> Menunggu <span class="legend-val">{{ $pending }}</span></div>
                <div class="legend-item"><div class="legend-dot" style="background:#F59E0B;"></div> Diproses <span class="legend-val">{{ $processing }}</span></div>
                <div class="legend-item"><div class="legend-dot" style="background:#22C55E;"></div> Selesai <span class="legend-val">{{ $completed }}</span></div>
                <div class="legend-item"><div class="legend-dot" style="background:#EF4444;"></div> Dibatalkan <span class="legend-val">{{ $cancelled }}</span></div>
            </div>
        </div>

        <div class="card-title" style="margin-top:24px;margin-bottom:12px;"><i class="fas fa-folder-open" style="color:var(--pink)"></i> Performa Kategori</div>
        @forelse($categories as $cat)
        <div class="cat-item">
            <span style="font-size:13px;font-weight:600;">{{ $cat->name }}</span>
            <div class="cat-bar"><div class="cat-bar-fill" style="width:{{ ($cat->products_count / $maxProd) * 100 }}%"></div></div>
            <span style="font-size:12px;color:var(--gray);">{{ $cat->products_count }} produk</span>
        </div>
        @empty
        <p style="text-align:center;color:var(--gray);padding:20px;font-size:14px;">Belum ada kategori</p>
        @endforelse
    </div>
</div>

@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
const labels  = @json($penjualanPerHari->pluck('tanggal')->map(fn($t) => \Carbon\Carbon::parse($t)->format('d M')));
const totals  = @json($penjualanPerHari->pluck('total')->map(fn($v) => (float)$v));
const counts  = @json($penjualanPerHari->pluck('jumlah_pesanan')->map(fn($v) => (int)$v));

const ctx = document.getElementById('salesChart').getContext('2d');
new Chart(ctx, {
    type: 'bar',
    data: {
        labels,
        datasets: [
            {
                label: 'Pendapatan (Rp)',
                data: totals,
                backgroundColor: 'rgba(240,80,122,0.7)',
                borderColor: 'rgba(240,80,122,1)',
                borderWidth: 1,
                yAxisID: 'y',
            },
            {
                label: 'Jumlah Pesanan',
                data: counts,
                type: 'line',
                borderColor: '#7B4B2A',
                backgroundColor: 'rgba(123,75,42,0.1)',
                borderWidth: 2,
                pointRadius: 4,
                yAxisID: 'y1',
            },
        ],
    },
    options: {
        responsive: true,
        interaction: { mode: 'index', intersect: false },
        plugins: {
            legend: { position: 'top' },
            tooltip: {
                callbacks: {
                    label: (ctx) => {
                        if (ctx.datasetIndex === 0) return ' Rp ' + ctx.parsed.y.toLocaleString('id-ID');
                        return ' ' + ctx.parsed.y + ' pesanan';
                    }
                }
            }
        },
        scales: {
            y:  { position: 'left',  title: { display: true, text: 'Pendapatan (Rp)' } },
            y1: { position: 'right', title: { display: true, text: 'Jumlah Pesanan' }, grid: { drawOnChartArea: false } },
        },
    },
});
</script>
@endpush
