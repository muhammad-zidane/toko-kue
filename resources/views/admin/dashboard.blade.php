@extends('admin.layout')
@section('title', 'Dashboard')
@section('page-title', 'Dashboard Admin')
@section('page-subtitle', 'Selamat datang, ' . auth()->user()->name . '!')

@section('content')
{{-- STATS --}}
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
    <div class="bg-white rounded-2xl border border-cream-border p-5 shadow-sm hover:-translate-y-1 hover:shadow-md transition-all duration-200">
        <div class="w-12 h-12 rounded-xl flex items-center justify-center text-xl mb-4 bg-primary/10 text-primary"><i class="fas fa-clipboard-list"></i></div>
        <div class="font-heading text-3xl font-bold text-brown-dark mt-1">{{ $ordersThisMonth }}</div>
        <div class="text-sm text-text-secondary">Total Pesanan Bulan Ini</div>
        <div class="text-xs font-bold mt-1 {{ $orderGrowth >= 0 ? 'text-green-600' : 'text-red-500' }}">
            {{ $orderGrowth >= 0 ? '+' : '' }}{{ $orderGrowth }}% dari bulan lalu
        </div>
    </div>
    <div class="bg-white rounded-2xl border border-cream-border p-5 shadow-sm hover:-translate-y-1 hover:shadow-md transition-all duration-200">
        <div class="w-12 h-12 rounded-xl flex items-center justify-center text-xl mb-4 bg-teal-50 text-teal-600"><i class="fas fa-money-bill-wave"></i></div>
        <div class="font-heading text-3xl font-bold text-teal-600 mt-1">Rp {{ number_format($revenueThisMonth/1000, 0, ',', '.') }}k</div>
        <div class="text-sm text-text-secondary">Pendapatan Bulan Ini</div>
        <div class="text-xs font-bold mt-1 {{ $revenueGrowth >= 0 ? 'text-green-600' : 'text-red-500' }}">
            {{ $revenueGrowth >= 0 ? '+' : '' }}{{ $revenueGrowth }}% dari bulan lalu
        </div>
    </div>
    <div class="bg-white rounded-2xl border border-cream-border p-5 shadow-sm hover:-translate-y-1 hover:shadow-md transition-all duration-200">
        <div class="w-12 h-12 rounded-xl flex items-center justify-center text-xl mb-4 bg-blue-50 text-blue-600"><i class="fas fa-user"></i></div>
        <div class="font-heading text-3xl font-bold text-brown-dark mt-1">{{ $customersThisMonth }}</div>
        <div class="text-sm text-text-secondary">Pelanggan Baru</div>
        <div class="text-xs font-bold mt-1 {{ $customerGrowth >= 0 ? 'text-green-600' : 'text-red-500' }}">
            {{ $customerGrowth >= 0 ? '+' : '' }}{{ $customerGrowth }}% dari bulan lalu
        </div>
    </div>
    <div class="bg-white rounded-2xl border border-cream-border p-5 shadow-sm hover:-translate-y-1 hover:shadow-md transition-all duration-200">
        <div class="w-12 h-12 rounded-xl flex items-center justify-center text-xl mb-4 bg-cream-warm/50 text-primary"><i class="fas fa-home"></i></div>
        <div class="font-heading text-3xl font-bold text-brown-dark mt-1">{{ $pendingOrdersCount }}</div>
        <div class="text-sm text-text-secondary">Pesanan Perlu Diproses</div>
        @if($pendingOrdersCount > 0)
            <div class="text-xs font-bold mt-1 text-primary">Segera proses!</div>
        @else
            <div class="text-xs font-bold mt-1 text-text-muted">Semua pesanan tertangani</div>
        @endif
    </div>
</div>

{{-- MIDDLE --}}
<div class="grid grid-cols-1 lg:grid-cols-[2fr_1.2fr] gap-6 mb-8">
    {{-- TABEL PESANAN --}}
    <div class="bg-white rounded-2xl border border-cream-border overflow-hidden shadow-sm">
        <div class="px-5 py-4 border-b border-cream-border flex justify-between items-center bg-cream-warm/10">
            <h3 class="font-heading text-lg font-bold text-brown-dark">Pesanan Terbaru</h3>
            <a href="{{ route('admin.orders.index') }}" class="text-sm font-semibold text-primary hover:text-primary-hover transition-colors">Lihat Semua →</a>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse" style="min-width:700px;">
                <thead>
                    <tr>
                        <th class="admin-th">No. Pesanan</th>
                        <th class="admin-th">Pelanggan</th>
                        <th class="admin-th">Produk</th>
                        <th class="admin-th">Total</th>
                        <th class="admin-th">Status</th>
                        <th class="admin-th">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($latestOrders as $order)
                    <tr class="hover:bg-cream-warm/20 transition-colors">
                        <td class="px-5 py-4 text-sm border-b border-cream-border/50 align-middle font-bold text-brown-dark">{{ $order->order_code }}</td>
                        <td class="px-5 py-4 text-sm border-b border-cream-border/50 align-middle">
                            <div class="flex items-center gap-2">
                                <span class="w-7 h-7 rounded-full bg-cream-warm text-brown-dark text-xs font-bold flex items-center justify-center shrink-0">{{ strtoupper(substr($order->user->name ?? '-', 0, 2)) }}</span>
                                <span class="font-medium text-brown-dark">{{ $order->user->name ?? '-' }}</span>
                            </div>
                        </td>
                        <td class="px-5 py-4 text-sm border-b border-cream-border/50 align-middle text-text-secondary">
                            {{ $order->orderItems->first()->product->name ?? '-' }}
                            @if($order->orderItems->count() > 1)
                                <span class="text-xs font-semibold text-primary">(+{{ $order->orderItems->count() - 1 }})</span>
                            @endif
                        </td>
                        <td class="px-5 py-4 text-sm border-b border-cream-border/50 align-middle font-bold text-brown-dark">Rp {{ number_format($order->total_price, 0, ',', '.') }}</td>
                        <td class="px-5 py-4 text-sm border-b border-cream-border/50 align-middle">
                            <span class="inline-flex items-center justify-center px-3 py-1 rounded-full text-xs font-bold @if($order->status == 'pending') bg-amber-50 text-amber-700 @elseif($order->status == 'processing') bg-blue-50 text-blue-700 @elseif($order->status == 'completed') bg-green-50 text-green-700 @else bg-red-50 text-red-700 @endif">
                                @switch($order->status)
                                    @case('pending') Menunggu @break
                                    @case('processing') Diproses @break
                                    @case('completed') Selesai @break
                                    @case('cancelled') Dibatalkan @break
                                    @default {{ ucfirst($order->status) }}
                                @endswitch
                            </span>
                        </td>
                        <td class="px-5 py-4 text-sm border-b border-cream-border/50 align-middle">
                            <a href="{{ route('admin.orders.show', $order) }}" class="text-xs px-3 py-1.5 rounded-lg border border-cream-border text-brown-mid hover:bg-cream-dark transition-all">Detail</a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-5 py-8 text-center text-text-muted text-sm border-b border-cream-border/50">Belum ada pesanan terbaru</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- KANAN --}}
    <div class="flex flex-col gap-6">
        {{-- AKTIVITAS --}}
        <div class="bg-white rounded-2xl border border-cream-border p-5 shadow-sm">
            <h3 class="text-sm font-bold text-brown-dark mb-4">Aktivitas Terkini</h3>
            <div class="flex flex-col">
                @forelse($recentActivities as $act)
                <div class="flex gap-3 mb-4 last:mb-0 relative">
                    <div class="w-3 h-3 rounded-full border-2 border-white ring-1 ring-cream-border mt-1 shrink-0 {{ $act['color'] === 'green' ? 'bg-green-500' : ($act['color'] === 'blue' ? 'bg-blue-500' : ($act['color'] === 'amber' ? 'bg-amber-500' : 'bg-red-500')) }}"></div>
                    <div class="flex-1">
                        <p class="text-xs text-text-primary leading-snug">
                            @switch($act['status'])
                                @case('pending')
                                    Pesanan baru <strong>{{ $act['order_code'] }}</strong> masuk dari {{ $act['user_name'] }}.
                                    @break
                                @case('processing')
                                    Pesanan <strong>{{ $act['order_code'] }}</strong> sedang diproses.
                                    @break
                                @case('completed')
                                    Pesanan <strong>{{ $act['order_code'] }}</strong> telah selesai.
                                    @break
                                @default
                                    Pesanan <strong>{{ $act['order_code'] }}</strong> dibatalkan.
                            @endswitch
                        </p>
                        <span class="text-[10px] text-text-muted mt-1 block">{{ $act['time_label'] }}</span>
                    </div>
                </div>
                @empty
                <p class="text-xs text-text-muted py-2">Belum ada aktivitas</p>
                @endforelse
            </div>
        </div>

        {{-- AKSI CEPAT --}}
        <div class="bg-white rounded-2xl border border-cream-border p-5 shadow-sm">
            <h3 class="text-sm font-bold text-brown-dark mb-4">Aksi Cepat</h3>
            <div class="grid grid-cols-2 gap-3">
                <a href="{{ route('admin.products.create') }}" class="flex items-center gap-3 p-3 bg-cream/40 border border-cream-border rounded-xl hover:bg-cream-warm hover:border-primary hover:-translate-y-0.5 transition-all">
                    <span class="w-8 h-8 bg-white border border-cream-border rounded-lg flex items-center justify-center text-sm shrink-0"><i class="fas fa-plus text-primary"></i></span>
                    <div class="min-w-0">
                        <div class="text-xs font-bold text-brown-dark truncate">Tambah Produk</div>
                        <div class="text-[10px] text-text-muted mt-0.5 truncate">Daftarkan kue baru</div>
                    </div>
                </a>
                <a href="{{ route('admin.analytics.index') }}" class="flex items-center gap-3 p-3 bg-cream/40 border border-cream-border rounded-xl hover:bg-cream-warm hover:border-primary hover:-translate-y-0.5 transition-all">
                    <span class="w-8 h-8 bg-white border border-cream-border rounded-lg flex items-center justify-center text-sm shrink-0"><i class="fas fa-chart-bar text-primary"></i></span>
                    <div class="min-w-0">
                        <div class="text-xs font-bold text-brown-dark truncate">Lihat Laporan</div>
                        <div class="text-[10px] text-text-muted mt-0.5 truncate">Analisis penjualan</div>
                    </div>
                </a>
                <a href="{{ route('admin.orders.index') }}" class="flex items-center gap-3 p-3 bg-cream/40 border border-cream-border rounded-xl hover:bg-cream-warm hover:border-primary hover:-translate-y-0.5 transition-all">
                    <span class="w-8 h-8 bg-white border border-cream-border rounded-lg flex items-center justify-center text-sm shrink-0"><i class="fas fa-clipboard-list text-primary"></i></span>
                    <div class="min-w-0">
                        <div class="text-xs font-bold text-brown-dark truncate">Kelola Pesanan</div>
                        <div class="text-[10px] text-text-muted mt-0.5 truncate">Lihat semua</div>
                    </div>
                </a>
                <a href="{{ route('admin.customers.index') }}" class="flex items-center gap-3 p-3 bg-cream/40 border border-cream-border rounded-xl hover:bg-cream-warm hover:border-primary hover:-translate-y-0.5 transition-all">
                    <span class="w-8 h-8 bg-white border border-cream-border rounded-lg flex items-center justify-center text-sm shrink-0"><i class="fas fa-users text-primary"></i></span>
                    <div class="min-w-0">
                        <div class="text-xs font-bold text-brown-dark truncate">Data Pelanggan</div>
                        <div class="text-[10px] text-text-muted mt-0.5 truncate">Lihat Semua User</div>
                    </div>
                </a>
            </div>
        </div>
    </div>
</div>

{{-- BOTTOM --}}
<div class="grid grid-cols-1 lg:grid-cols-[2fr_1.2fr] gap-6">
    {{-- GRAFIK --}}
    <div class="bg-white rounded-2xl border border-cream-border p-5 shadow-sm">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-xs font-bold text-brown-dark">Pendapatan 7 Hari Terakhir</h3>
            <span class="text-xs text-primary font-semibold">Rp {{ number_format($revenueThisWeek/1000, 0, ',', '.') }}k minggu ini</span>
        </div>
        <div class="flex justify-between items-end h-[180px] pt-2 mb-4 border-b border-cream-border">
            @foreach($dailyRevenue as $d)
                @php
                    $pct = $maxDaily > 0 ? ($d['amount'] / $maxDaily) * 100 : 0;
                    $barClass = $pct > 70 ? 'high' : ($pct > 30 ? 'mid' : 'low');
                @endphp
                <div class="flex flex-col items-center w-[calc(100%/7)] h-full justify-end">
                    <div class="w-3/5 rounded-t-md transition-all duration-300 {{ $barClass === 'high' ? 'bg-primary' : ($barClass === 'mid' ? 'bg-brown-light' : 'bg-cream-border') }}" style="height:{{ max($pct, 5) }}%"></div>
                    <span class="text-[10px] text-text-muted mt-2 uppercase">{{ $d['day'] }}</span>
                </div>
            @endforeach
        </div>
        <div class="flex justify-center gap-4 mt-2 text-xs">
            <div class="flex items-center gap-1.5 text-[11px] text-text-secondary"><div class="w-2.5 h-2.5 rounded-full bg-primary"></div> Tertinggi</div>
            <div class="flex items-center gap-1.5 text-[11px] text-text-secondary"><div class="w-2.5 h-2.5 rounded-full bg-brown-light"></div> Normal</div>
            <div class="flex items-center gap-1.5 text-[11px] text-text-secondary"><div class="w-2.5 h-2.5 rounded-full bg-cream-border"></div> Rendah</div>
        </div>
    </div>

    {{-- TOP PRODUK --}}
    <div class="bg-white rounded-2xl border border-cream-border p-5 shadow-sm">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-xs font-bold text-brown-dark">Produk Terlaris</h3>
            <span class="text-xs text-text-muted">Semua Waktu</span>
        </div>
        @forelse($topProducts as $i => $p)
        <div class="flex items-center gap-3 py-3 border-b border-cream-border last:border-0 last:pb-0">
            <span class="w-6 h-6 bg-cream/40 border border-cream-border rounded-full flex items-center justify-center text-xs font-bold text-brown-mid shrink-0">{{ $i + 1 }}</span>
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
</div>
@endsection
