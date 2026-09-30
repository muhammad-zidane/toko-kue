@extends('admin.layout')
@section('title', 'Kalender Produksi')
@section('page-title', 'Kalender Produksi')
@section('page-subtitle', 'Lihat jadwal pesanan per tanggal')

@section('content')
@php
    $monthNames = ['', 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
    $dayNames = ['Min', 'Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab'];
    $firstDay = \Carbon\Carbon::createFromDate($year, $month, 1);
    $daysInMonth = $firstDay->daysInMonth;
    $startDow = $firstDay->dayOfWeek; // 0=Sun
    $today = \Carbon\Carbon::today();
    $prevMonth = $firstDay->copy()->subMonth();
    $nextMonth = $firstDay->copy()->addMonth();
@endphp

{{-- NAVIGASI --}}
<div class="flex justify-between items-center mb-6">
    <div class="font-heading text-2xl font-bold text-brown-dark">{{ $monthNames[$month] }} {{ $year }}</div>
    <div class="flex gap-2.5">
        <a href="{{ route('admin.production-calendar.index', ['month' => $prevMonth->month, 'year' => $prevMonth->year]) }}" class="px-4 py-2 border border-cream-border text-brown-dark rounded-xl bg-white hover:bg-cream hover:border-primary hover:text-primary transition-all duration-200 text-xs font-semibold flex items-center gap-1.5 no-underline">
            <i class="fas fa-chevron-left"></i> Sebelumnya
        </a>
        <a href="{{ route('admin.production-calendar.index', ['month' => now()->month, 'year' => now()->year]) }}" class="px-4 py-2 border border-cream-border text-brown-dark rounded-xl bg-white hover:bg-cream hover:border-primary hover:text-primary transition-all duration-200 text-xs font-semibold flex items-center gap-1.5 no-underline">
            Hari ini
        </a>
        <a href="{{ route('admin.production-calendar.index', ['month' => $nextMonth->month, 'year' => $nextMonth->year]) }}" class="px-4 py-2 border border-cream-border text-brown-dark rounded-xl bg-white hover:bg-cream hover:border-primary hover:text-primary transition-all duration-200 text-xs font-semibold flex items-center gap-1.5 no-underline">
            Berikutnya <i class="fas fa-chevron-right"></i>
        </a>
    </div>
</div>

{{-- KALENDER --}}
<div class="bg-white rounded-2xl border border-cream-border p-5 mb-6 shadow-sm">
    <div class="grid grid-cols-7 gap-2.5">
        @foreach($dayNames as $i => $day)
        <div class="text-center font-bold text-xs text-brown-light py-2.5 uppercase tracking-wider">{{ $day }}</div>
        @endforeach

        {{-- Sel kosong di awal --}}
        @for($i = 0; $i < $startDow; $i++)
        <div class="bg-transparent border-0 cursor-default"></div>
        @endfor

        @for($d = 1; $d <= $daysInMonth; $d++)
        @php
            $dateKey = \Carbon\Carbon::createFromDate($year, $month, $d)->format('Y-m-d');
            $dayOrders = $orders->get($dateKey, collect());
            $isToday = ($today->year == $year && $today->month == $month && $today->day == $d);
        @endphp
        <div class="aspect-[1.2] bg-cream border border-cream-border rounded-xl p-2.5 flex flex-col justify-between cursor-pointer relative transition-all hover:bg-cream-warm/40 hover:-translate-y-0.5 hover:shadow-sm [&.today]:border-2 [&.today]:border-primary [&.has-orders]:bg-amber-50/50 [&.has-orders]:border-amber-200 [&.selected]:bg-cream-warm/80 [&.selected]:border-primary {{ $isToday ? 'today' : '' }} {{ $dayOrders->isNotEmpty() ? 'has-orders' : '' }}"
             id="cell-{{ $dateKey }}"
             onclick="{{ $dayOrders->isNotEmpty() ? "showDetail('$dateKey', '{$monthNames[$month]} $d, $year')" : '' }}">
            <div class="text-sm font-bold text-brown-dark flex items-center justify-center w-6 h-6 {{ $isToday ? 'bg-primary text-white rounded-full' : '' }}">{{ $d }}</div>
            @if($dayOrders->isNotEmpty())
            <div class="inline-flex items-center gap-1 bg-primary text-white text-[10px] font-bold px-2 py-0.5 rounded-full self-end">
                <i class="fas fa-box text-[9px]"></i> {{ $dayOrders->count() }}
            </div>
            @endif
        </div>
        @endfor
    </div>
</div>

{{-- DETAIL PANEL --}}
<div class="bg-white rounded-2xl border border-cream-border p-5 shadow-sm hidden [&.visible]:block" id="detailPanel">
    <div class="flex justify-between items-center pb-3 border-b border-cream-border mb-4">
        <h3 class="font-heading text-lg font-bold text-brown-dark" id="detailTitle">Pesanan</h3>
        <button class="w-8 h-8 rounded-full hover:bg-cream-warm flex items-center justify-center text-brown-light hover:text-primary transition-all border-0 cursor-pointer" onclick="hideDetail()"><i class="fas fa-times"></i></button>
    </div>
    <div id="detailBody"></div>
</div>

{{-- DATA UNTUK JS --}}
<script>
const ordersData = {
    @foreach($orders as $dateKey => $dayOrders)
    "{{ $dateKey }}": [
        @foreach($dayOrders as $order)
        {
            id: "{{ $order->id }}",
            code: "{{ $order->order_code ?? '#' . $order->id }}",
            customer: "{{ addslashes($order->user->name ?? 'Pelanggan') }}",
            total: "Rp {{ number_format($order->total_amount ?? 0, 0, ',', '.') }}",
            status: "{{ $order->status ?? 'pending' }}",
            url: "{{ route('admin.orders.show', $order) }}"
        },
        @endforeach
    ],
    @endforeach
};

const statusLabels = {
    pending: { label: 'Menunggu', cls: 'bg-amber-50 text-amber-700 border border-amber-200' },
    processing: { label: 'Diproses', cls: 'bg-blue-50 text-blue-700 border border-blue-200' },
    completed: { label: 'Selesai', cls: 'bg-green-50 text-green-700 border border-green-200' },
    cancelled: { label: 'Dibatalkan', cls: 'bg-red-50 text-red-700 border border-red-200' },
};

let selectedCell = null;

function showDetail(dateKey, dateLabel) {
    if (selectedCell) selectedCell.classList.remove('selected');
    selectedCell = document.getElementById('cell-' + dateKey);
    if (selectedCell) selectedCell.classList.add('selected');

    const panel = document.getElementById('detailPanel');
    const body = document.getElementById('detailBody');
    document.getElementById('detailTitle').textContent = 'Pesanan — ' + dateLabel;

    const items = ordersData[dateKey] || [];
    if (items.length === 0) {
        body.innerHTML = '<div class="py-6 text-center text-xs text-text-muted">Tidak ada pesanan</div>';
    } else {
        body.innerHTML = items.map(o => {
            const st = statusLabels[o.status] || { label: o.status, cls: 'bg-gray-100 text-gray-700' };
            return `<div class="flex items-center justify-between gap-4 p-3 border border-cream-border rounded-xl mb-2.5 bg-cream/30 hover:bg-cream-warm/20 transition-all">
                <span class="font-mono font-bold text-brown-dark bg-white px-2 py-1 border border-cream-border rounded-lg text-xs">${o.code}</span>
                <div class="flex-1 min-w-0">
                    <div class="font-bold text-xs text-brown-dark truncate">${o.customer}</div>
                    <div class="text-xs text-primary font-bold mt-0.5">${o.total}</div>
                </div>
                <span class="text-[11px] font-bold px-2.5 py-0.5 rounded-full ${st.cls}">${st.label}</span>
                <a href="${o.url}" class="inline-flex items-center gap-1 text-xs px-2.5 py-1.5 rounded-lg border border-cream-border text-brown-dark bg-white hover:bg-primary hover:text-white hover:border-primary transition-all font-semibold"><i class="fas fa-eye"></i> Lihat</a>
            </div>`;
        }).join('');
    }

    panel.classList.add('visible');
    panel.classList.remove('hidden');
    panel.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
}

function hideDetail() {
    const panel = document.getElementById('detailPanel');
    panel.classList.remove('visible');
    panel.classList.add('hidden');
    if (selectedCell) { selectedCell.classList.remove('selected'); selectedCell = null; }
}
</script>
@endsection

