@extends('admin.layout')
@section('title', 'Analisis')
@section('page-title', 'Analisis & Laporan')
@section('page-subtitle', 'Pantau performa bisnis secara keseluruhan')

@section('styles')
<style>
    .stats-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 16px; margin-bottom: 24px; }
    .stat-card { background: white; border-radius: 16px; padding: 20px; border: 1px solid #EDE0D4; transition: transform 0.2s; }
    .stat-card:hover { transform: translateY(-2px); }
    .stat-icon { width: 44px; height: 44px; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 20px; margin-bottom: 12px; }
    .stat-value { font-size: 24px; font-weight: 800; }
    .stat-label { font-size: 12px; color: var(--gray); margin-top: 4px; }
    .stat-growth { font-size: 12px; font-weight: 600; margin-top: 4px; }
    .two-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 20px; }
    .card { background: white; border-radius: 16px; border: 1px solid #EDE0D4; padding: 24px; }
    .card-title { font-size: 14px; font-weight: 700; color: var(--text-dark); margin-bottom: 20px; }
    .chart-bars { display: flex; align-items: flex-end; gap: 10px; height: 160px; }
    .chart-col { flex: 1; display: flex; flex-direction: column; align-items: center; gap: 6px; height: 100%; justify-content: flex-end; }
    .chart-val { font-size: 9px; font-weight: 700; color: var(--gray); }
    .chart-bar { width: 100%; border-radius: 4px 4px 0 0; min-height: 4px; }
    .chart-bar.high { background: linear-gradient(to top, var(--pink), #D64A6C); }
    .chart-bar.mid { background: var(--cream-dark); }
    .chart-bar.low { background: #F3F4F6; }
    .chart-label { font-size: 10px; color: var(--gray); }
    .top-item { display: flex; align-items: center; gap: 12px; padding: 12px 0; border-bottom: 1px solid rgba(237,224,212,0.5); }
    .top-item:last-child { border-bottom: none; }
    .top-rank { font-size: 14px; font-weight: 800; width: 24px; text-align: center; }
    .top-info { flex: 1; }
    .top-name { font-size: 14px; font-weight: 600; }
    .top-cat { font-size: 11px; color: var(--gray); }
    .top-sold { font-size: 12px; font-weight: 700; color: var(--pink); }
    .top-bar { width: 80px; height: 4px; background: var(--cream); border-radius: 2px; margin-top: 4px; }
    .top-bar-fill { height: 4px; background: var(--pink); border-radius: 2px; }
    .donut-container { display: flex; flex-direction: column; align-items: center; gap: 32px; padding: 20px 0; }
    .donut { width: 140px; height: 140px; border-radius: 50%; position: relative; }
    .donut-center { position: absolute; inset: 30px; background: white; border-radius: 50%; display: flex; flex-direction: column; align-items: center; justify-content: center; }
    .donut-count { font-size: 20px; font-weight: 800; }
    .donut-label { font-size: 10px; color: var(--gray); }
    .legend { display: flex; flex-direction: column; gap: 10px; }
    .legend-item { display: flex; align-items: center; gap: 8px; font-size: 12px; }
    .legend-dot { width: 10px; height: 10px; border-radius: 50%; }
    .legend-val { font-weight: 700; margin-left: auto; }
    .cat-item { display: flex; align-items: center; justify-content: space-between; padding: 12px 0; border-bottom: 1px solid rgba(237,224,212,0.5); }
    .cat-item:last-child { border-bottom: none; }
    .cat-bar { flex: 1; max-width: 120px; height: 6px; background: var(--cream); border-radius: 3px; margin: 0 16px; }
    .cat-bar-fill { height: 6px; background: var(--pink); border-radius: 3px; }
    @media (max-width: 1024px) { .stats-grid { grid-template-columns: repeat(2, 1fr); } .two-grid { grid-template-columns: 1fr; } }
    @media (max-width: 640px) { .stats-grid { grid-template-columns: 1fr; } .donut-container { flex-direction: column; } }
</style>
@endsection

@section('content')
{{-- STATS --}}
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

{{-- CHART & TOP PRODUCTS --}}
<div class="two-grid">
    <div class="card">
        <div class="card-title"><i class="fas fa-chart-bar" style="color:var(--pink)"></i> Pendapatan 7 Hari Terakhir</div>
        <div class="chart-bars">
            @foreach($dailyRevenue as $d)
            @php
                $pct = $maxDaily > 0 ? ($d['amount'] / $maxDaily) * 100 : 0;
                $barClass = $pct > 70 ? 'high' : ($pct > 30 ? 'mid' : 'low');
            @endphp
            <div class="chart-col">
                <span class="chart-val">{{ $d['amount'] > 0 ? 'Rp' . number_format($d['amount']/1000, 0, ',', '.') . 'rb' : '-' }}</span>
                <div class="chart-bar {{ $barClass }}" style="height:{{ max($pct, 5) }}%"></div>
                <span class="chart-label">{{ $d['day'] }}</span>
            </div>
            @endforeach
        </div>
    </div>
    <div class="card">
        <div class="card-title"><i class="fas fa-trophy" style="color:var(--pink)"></i> Produk Terlaris</div>
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
</div>

{{-- STATUS & CATEGORIES --}}
<div class="two-grid">
    <div class="card">
        <div class="card-title"><i class="fas fa-clipboard-list" style="color:var(--pink)"></i> Distribusi Status Pesanan</div>
        @php
            $pending = $statusCounts['pending'] ?? 0;
            $processing = $statusCounts['processing'] ?? 0;
            $completed = $statusCounts['completed'] ?? 0;
            $cancelled = $statusCounts['cancelled'] ?? 0;
            $d1 = $pending / $totalOrdersAll * 360;
            $d2 = $d1 + ($processing / $totalOrdersAll * 360);
            $d3 = $d2 + ($completed / $totalOrdersAll * 360);
        @endphp
        <div class="donut-container" style="flex-direction:row;">
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
    </div>
    <div class="card">
        <div class="card-title"><i class="fas fa-folder-open" style="color:var(--pink)"></i> Performa Kategori</div>
        @forelse($categories as $cat)
        <div class="cat-item">
            <span style="font-size:14px;font-weight:600;">{{ $cat->name }}</span>
            <div class="cat-bar"><div class="cat-bar-fill" style="width:{{ ($cat->products_count / $maxProd) * 100 }}%"></div></div>
            <span style="font-size:12px;color:var(--gray);">{{ $cat->products_count }} produk</span>
        </div>
        @empty
        <p style="text-align:center;color:var(--gray);padding:20px;font-size:14px;">Belum ada kategori</p>
        @endforelse
    </div>
</div>
@endsection
