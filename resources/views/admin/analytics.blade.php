@extends('admin.layout')
@section('title', 'Analisis')
@section('page-title', 'Analisis & Laporan')
@section('page-subtitle', 'Pantau performa bisnis secara keseluruhan')

@section('content')

{{-- FILTER BAR --}}
<form method="GET" action="{{ route('admin.analytics.index') }}" class="flex items-center gap-3 mb-6 bg-white p-4 rounded-xl border border-cream-border flex-wrap">
    <label class="text-xs font-bold text-brown-mid">Dari:</label>
    <input type="date" name="dari" value="{{ $dari }}" max="{{ date('Y-m-d') }}" class="border border-cream-border rounded-xl px-3 py-2 text-sm text-text-primary bg-white outline-none transition-all focus:border-primary focus:ring-2 focus:ring-primary/20">
    <label class="text-xs font-bold text-brown-mid">Sampai:</label>
    <input type="date" name="sampai" value="{{ $sampai }}" max="{{ date('Y-m-d') }}" class="border border-cream-border rounded-xl px-3 py-2 text-sm text-text-primary bg-white outline-none transition-all focus:border-primary focus:ring-2 focus:ring-primary/20">
    <button type="submit" class="inline-flex items-center justify-center px-4 py-2 bg-primary text-white font-bold text-xs rounded-full shadow-gold transition-all duration-200 hover:bg-primary-hover hover:-translate-y-0.5 border-0 cursor-pointer"><i class="fas fa-search mr-1.5"></i>Filter</button>
    <a href="{{ route('admin.analytics.export', ['dari' => $dari, 'sampai' => $sampai]) }}" class="inline-flex items-center justify-center px-4 py-2 bg-green-50 text-green-700 border border-green-200 font-bold text-xs rounded-full transition-all duration-200 hover:bg-green-700 hover:text-white cursor-pointer ml-auto">
        <i class="fas fa-file-excel mr-1.5"></i> Export Excel
    </a>
</form>

{{-- FILTER SUMMARY STATS --}}
<div class="bg-cream-warm border border-cream-border rounded-xl px-4 py-2.5 text-xs md:text-sm text-brown-mid mb-6">
    <strong>Periode {{ \Carbon\Carbon::parse($dari)->format('d M Y') }} – {{ \Carbon\Carbon::parse($sampai)->format('d M Y') }}:</strong>
    &nbsp;
    <strong>{{ $totalFilterOrders }}</strong> pesanan &nbsp;|&nbsp;
    <strong>Rp {{ number_format($totalFilterRevenue, 0, ',', '.') }}</strong> pendapatan &nbsp;|&nbsp;
    <strong>{{ $totalItemsTerjual }}</strong> item terjual &nbsp;|&nbsp;
    rata-rata <strong>Rp {{ number_format($avgFilterOrder, 0, ',', '.') }}</strong>/pesanan
</div>

{{-- STATS BULAN INI --}}
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
    <div class="bg-white rounded-2xl border border-cream-border p-5 shadow-sm hover:-translate-y-1 hover:shadow-md transition-all duration-200">
        <div class="w-12 h-12 rounded-xl flex items-center justify-center text-xl mb-4 bg-primary/10 text-primary"><i class="fas fa-money-bill-wave"></i></div>
        <div class="font-heading text-3xl font-bold text-brown-dark mt-1">Rp {{ number_format($revenueThisMonth/1000, 0, ',', '.') }}rb</div>
        <div class="text-sm text-text-secondary">Pendapatan Bulan Ini</div>
        <div class="text-xs font-bold mt-1" style="color:{{ $growthPercent >= 0 ? '#15803D' : '#B91C1C' }};">{{ $growthPercent >= 0 ? '+' : '' }}{{ $growthPercent }}% dari bulan lalu</div>
    </div>
    <div class="bg-white rounded-2xl border border-cream-border p-5 shadow-sm hover:-translate-y-1 hover:shadow-md transition-all duration-200">
        <div class="w-12 h-12 rounded-xl flex items-center justify-center text-xl mb-4 bg-blue-50 text-blue-600"><i class="fas fa-box"></i></div>
        <div class="font-heading text-3xl font-bold text-brown-dark mt-1">{{ $ordersThisMonth }}</div>
        <div class="text-sm text-text-secondary">Pesanan Bulan Ini</div>
    </div>
    <div class="bg-white rounded-2xl border border-cream-border p-5 shadow-sm hover:-translate-y-1 hover:shadow-md transition-all duration-200">
        <div class="w-12 h-12 rounded-xl flex items-center justify-center text-xl mb-4 bg-amber-50 text-amber-600"><i class="fas fa-chart-bar"></i></div>
        <div class="font-heading text-3xl font-bold text-brown-dark mt-1">Rp {{ number_format($avgOrderValue/1000, 0, ',', '.') }}rb</div>
        <div class="text-sm text-text-secondary">Rata-rata Per Pesanan</div>
    </div>
    <div class="bg-white rounded-2xl border border-cream-border p-5 shadow-sm hover:-translate-y-1 hover:shadow-md transition-all duration-200">
        <div class="w-12 h-12 rounded-xl flex items-center justify-center text-xl mb-4 bg-green-50 text-green-600"><i class="fas fa-chart-line"></i></div>
        <div class="font-heading text-3xl font-bold text-brown-dark mt-1">Rp {{ number_format($revenueLastMonth/1000, 0, ',', '.') }}rb</div>
        <div class="text-sm text-text-secondary">Pendapatan Bulan Lalu</div>
    </div>
</div>

{{-- GRAFIK CHART.JS --}}
<div class="bg-white rounded-2xl border border-cream-border p-5 mb-6 shadow-sm">
    <div class="font-heading text-lg font-bold text-brown-dark mb-4 flex items-center gap-2">
        <i class="fas fa-chart-line text-primary"></i>
        Grafik Penjualan Per Hari
        <span class="text-xs font-normal text-text-muted ml-2">{{ \Carbon\Carbon::parse($dari)->format('d M') }} – {{ \Carbon\Carbon::parse($sampai)->format('d M Y') }}</span>
    </div>
    <canvas id="salesChart" height="80"></canvas>
</div>

{{-- TOP 10 & STATUS --}}
<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
    <div class="bg-white rounded-2xl border border-cream-border p-5 shadow-sm">
        <div class="font-heading text-lg font-bold text-brown-dark mb-4 flex items-center gap-2"><i class="fas fa-trophy text-primary"></i> Top 10 Produk Terlaris</div>
        @forelse($topProducts as $i => $p)
        <div class="flex items-center gap-3 py-3 border-b border-cream-border last:border-0 last:pb-0">
            <span class="w-6 h-6 rounded-full flex items-center justify-center text-xs font-bold shrink-0 {{ $i === 0 ? 'bg-amber-100 text-amber-700' : ($i === 1 ? 'bg-gray-100 text-gray-700' : ($i === 2 ? 'bg-amber-50 text-amber-800' : 'bg-cream/40 text-text-muted border border-cream-border')) }}">{{ $i + 1 }}</span>
            <div class="flex-1 min-w-0">
                <div class="text-xs font-bold text-brown-dark truncate">{{ $p->name }}</div>
                <div class="text-[10px] text-text-muted mt-0.5">{{ $p->category->name ?? '-' }}</div>
            </div>
            <div class="text-right">
                <div class="text-xs font-bold text-brown-dark">{{ $p->order_items_count }} terjual</div>
                <div class="w-20 h-1 bg-cream-border rounded-full mt-1 overflow-hidden ml-auto">
                    <div class="h-full bg-primary rounded-full" style="width:{{ ($p->order_items_count / $maxSold) * 100 }}%"></div>
                </div>
            </div>
        </div>
        @empty
        <p class="text-center text-xs text-text-muted py-4">Belum ada data penjualan</p>
        @endforelse
    </div>

    <div class="bg-white rounded-2xl border border-cream-border p-5 shadow-sm">
        <div class="font-heading text-lg font-bold text-brown-dark mb-4 flex items-center gap-2"><i class="fas fa-clipboard-list text-primary"></i> Distribusi Status Pesanan</div>
        @php
            $pending    = $statusCounts['pending'] ?? 0;
            $processing = $statusCounts['processing'] ?? 0;
            $completed  = $statusCounts['completed'] ?? 0;
            $cancelled  = $statusCounts['cancelled'] ?? 0;
            $d1 = $pending / $totalOrdersAll * 360;
            $d2 = $d1 + ($processing / $totalOrdersAll * 360);
            $d3 = $d2 + ($completed / $totalOrdersAll * 360);
        @endphp
        <div class="flex flex-col sm:flex-row items-center gap-6 mt-3">
            <div class="w-[140px] h-[140px] rounded-full flex items-center justify-center relative shrink-0" style="background:conic-gradient(#3B82F6 0deg {{ $d1 }}deg, #F59E0B {{ $d1 }}deg {{ $d2 }}deg, #22C55E {{ $d2 }}deg {{ $d3 }}deg, #EF4444 {{ $d3 }}deg 360deg);">
                <div class="w-24 h-24 bg-white rounded-full flex flex-col items-center justify-center shadow-inner">
                    <span class="text-2xl font-bold text-brown-dark leading-tight">{{ $totalOrdersAll }}</span>
                    <span class="text-[10px] text-text-muted uppercase tracking-wider">pesanan</span>
                </div>
            </div>
            <div class="flex-1 flex flex-col gap-2 w-full">
                <div class="flex items-center gap-2 text-xs font-medium text-text-secondary"><div class="w-2.5 h-2.5 rounded-full bg-[#3B82F6]"></div> Menunggu <span class="font-bold text-brown-dark ml-auto">{{ $pending }}</span></div>
                <div class="flex items-center gap-2 text-xs font-medium text-text-secondary"><div class="w-2.5 h-2.5 rounded-full bg-[#F59E0B]"></div> Diproses <span class="font-bold text-brown-dark ml-auto">{{ $processing }}</span></div>
                <div class="flex items-center gap-2 text-xs font-medium text-text-secondary"><div class="w-2.5 h-2.5 rounded-full bg-[#22C55E]"></div> Selesai <span class="font-bold text-brown-dark ml-auto">{{ $completed }}</span></div>
                <div class="flex items-center gap-2 text-xs font-medium text-text-secondary"><div class="w-2.5 h-2.5 rounded-full bg-[#EF4444]"></div> Dibatalkan <span class="font-bold text-brown-dark ml-auto">{{ $cancelled }}</span></div>
            </div>
        </div>

        <div class="font-heading text-lg font-bold text-brown-dark mt-6 mb-4 flex items-center gap-2"><i class="fas fa-folder-open text-primary"></i> Performa Kategori</div>
        @forelse($categories as $cat)
        <div class="mb-3 last:mb-0">
            <span class="text-xs font-semibold text-brown-dark">{{ $cat->name }}</span>
            <div class="w-full h-1.5 bg-cream-border rounded-full mt-1.5 overflow-hidden">
                <div class="h-full bg-primary rounded-full" style="width:{{ ($cat->products_count / $maxProd) * 100 }}%"></div>
            </div>
            <span class="text-[10px] text-text-muted mt-1 block">{{ $cat->products_count }} produk</span>
        </div>
        @empty
        <p class="text-center text-xs text-text-muted py-4">Belum ada kategori</p>
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
                backgroundColor: 'rgba(200,134,10,0.7)',
                borderColor: 'rgba(200,134,10,1)',
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
