@extends('admin.layout')
@section('title', 'Kalender Produksi')
@section('page-title', 'Kalender Produksi')
@section('page-subtitle', 'Lihat jadwal pesanan per tanggal')

@push('styles')
<style>
    .cal-nav { display: flex; align-items: center; justify-content: space-between; margin-bottom: 20px; }
    .cal-month-label { font-family: 'Playfair Display', serif; font-size: 22px; font-weight: 700; color: var(--text-dark); }
    .cal-nav-btns { display: flex; gap: 8px; }
    .btn-nav { padding: 8px 16px; border-radius: 8px; border: 1.5px solid #EDE0D4; background: white; color: var(--brown-dark); font-size: 13px; font-weight: 600; cursor: pointer; font-family: 'Plus Jakarta Sans', sans-serif; text-decoration: none; display: inline-flex; align-items: center; gap: 6px; transition: background 0.2s; }
    .btn-nav:hover { background: var(--cream); }
    .calendar-card { background: white; border-radius: 16px; border: 1px solid #EDE0D4; overflow: hidden; margin-bottom: 24px; }
    .cal-grid { display: grid; grid-template-columns: repeat(7, 1fr); }
    .cal-day-header { padding: 12px 8px; text-align: center; font-size: 11px; font-weight: 700; color: var(--gray); text-transform: uppercase; letter-spacing: 0.5px; background: #FAFAF8; border-bottom: 1px solid #EDE0D4; }
    .cal-day-header:first-child { color: #DC2626; }
    .cal-day-header:last-child { color: #2563EB; }
    .cal-cell { min-height: 90px; padding: 8px; border-right: 1px solid rgba(237,224,212,0.5); border-bottom: 1px solid rgba(237,224,212,0.5); position: relative; cursor: pointer; transition: background 0.15s; }
    .cal-cell:hover { background: #FFFBF5; }
    .cal-cell.empty { background: #FAFAF8; cursor: default; }
    .cal-cell.today { background: rgba(240,80,122,0.04); }
    .cal-cell.has-orders { background: rgba(255,248,238,0.6); }
    .cal-cell.selected { background: rgba(240,80,122,0.08); outline: 2px solid var(--pink); outline-offset: -2px; }
    .cal-date { font-size: 13px; font-weight: 700; color: var(--text-dark); width: 26px; height: 26px; display: flex; align-items: center; justify-content: center; border-radius: 50%; margin-bottom: 4px; }
    .cal-date.today-dot { background: var(--pink); color: white; }
    .order-badge { display: inline-flex; align-items: center; justify-content: center; background: var(--pink); color: white; border-radius: 20px; font-size: 10px; font-weight: 700; padding: 2px 8px; gap: 3px; }
    .order-badge i { font-size: 9px; }

    /* Detail Panel */
    .detail-card { background: white; border-radius: 16px; border: 1px solid #EDE0D4; overflow: hidden; display: none; }
    .detail-card.visible { display: block; }
    .detail-header { padding: 16px 20px; border-bottom: 1px solid #EDE0D4; background: rgba(255,248,238,0.3); display: flex; justify-content: space-between; align-items: center; }
    .detail-header h3 { font-size: 14px; font-weight: 700; color: var(--text-dark); }
    .detail-close { background: none; border: none; cursor: pointer; color: var(--gray); font-size: 16px; }
    .order-item { padding: 14px 20px; border-bottom: 1px solid rgba(237,224,212,0.5); display: flex; align-items: center; gap: 12px; }
    .order-item:last-child { border-bottom: none; }
    .order-id { font-size: 11px; font-family: monospace; background: var(--cream); color: var(--brown-dark); padding: 3px 8px; border-radius: 4px; font-weight: 700; flex-shrink: 0; }
    .order-customer { font-size: 13px; font-weight: 600; color: var(--text-dark); }
    .order-total { font-size: 12px; color: var(--pink); font-weight: 700; }
    .order-status { font-size: 11px; font-weight: 700; padding: 2px 8px; border-radius: 10px; flex-shrink: 0; }
    .status-pending { background: #FEF3C7; color: #D97706; }
    .status-processing { background: #DBEAFE; color: #1D4ED8; }
    .status-completed { background: #DCFCE7; color: #16A34A; }
    .status-cancelled { background: #FEE2E2; color: #DC2626; }
    .btn-view { padding: 5px 10px; border-radius: 6px; font-size: 11px; font-weight: 600; background: #EFF6FF; color: #2563EB; text-decoration: none; display: inline-flex; align-items: center; gap: 4px; flex-shrink: 0; margin-left: auto; }

    @media (max-width: 640px) {
        .cal-cell { min-height: 60px; padding: 4px; }
        .cal-date { font-size: 11px; width: 22px; height: 22px; }
        .order-badge { font-size: 9px; padding: 1px 5px; }
    }
</style>
@endpush

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
<div class="cal-nav">
    <div class="cal-month-label">{{ $monthNames[$month] }} {{ $year }}</div>
    <div class="cal-nav-btns">
        <a href="{{ route('admin.production-calendar.index', ['month' => $prevMonth->month, 'year' => $prevMonth->year]) }}" class="btn-nav">
            <i class="fas fa-chevron-left"></i> Sebelumnya
        </a>
        <a href="{{ route('admin.production-calendar.index', ['month' => now()->month, 'year' => now()->year]) }}" class="btn-nav">
            Hari ini
        </a>
        <a href="{{ route('admin.production-calendar.index', ['month' => $nextMonth->month, 'year' => $nextMonth->year]) }}" class="btn-nav">
            Berikutnya <i class="fas fa-chevron-right"></i>
        </a>
    </div>
</div>

{{-- KALENDER --}}
<div class="calendar-card">
    <div class="cal-grid">
        @foreach($dayNames as $i => $day)
        <div class="cal-day-header">{{ $day }}</div>
        @endforeach

        {{-- Sel kosong di awal --}}
        @for($i = 0; $i < $startDow; $i++)
        <div class="cal-cell empty"></div>
        @endfor

        @for($d = 1; $d <= $daysInMonth; $d++)
        @php
            $dateKey = \Carbon\Carbon::createFromDate($year, $month, $d)->format('Y-m-d');
            $dayOrders = $orders->get($dateKey, collect());
            $isToday = ($today->year == $year && $today->month == $month && $today->day == $d);
        @endphp
        <div class="cal-cell {{ $isToday ? 'today' : '' }} {{ $dayOrders->isNotEmpty() ? 'has-orders' : '' }}"
             id="cell-{{ $dateKey }}"
             onclick="{{ $dayOrders->isNotEmpty() ? "showDetail('$dateKey', '{$monthNames[$month]} $d, $year')" : '' }}">
            <div class="cal-date {{ $isToday ? 'today-dot' : '' }}">{{ $d }}</div>
            @if($dayOrders->isNotEmpty())
            <div class="order-badge">
                <i class="fas fa-box"></i> {{ $dayOrders->count() }}
            </div>
            @endif
        </div>
        @endfor
    </div>
</div>

{{-- DETAIL PANEL --}}
<div class="detail-card" id="detailPanel">
    <div class="detail-header">
        <h3 id="detailTitle">Pesanan</h3>
        <button class="detail-close" onclick="hideDetail()"><i class="fas fa-times"></i></button>
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
    pending: { label: 'Menunggu', cls: 'status-pending' },
    processing: { label: 'Diproses', cls: 'status-processing' },
    completed: { label: 'Selesai', cls: 'status-completed' },
    cancelled: { label: 'Dibatalkan', cls: 'status-cancelled' },
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
        body.innerHTML = '<div style="padding:24px;text-align:center;color:var(--gray);font-size:13px;">Tidak ada pesanan</div>';
    } else {
        body.innerHTML = items.map(o => {
            const st = statusLabels[o.status] || { label: o.status, cls: 'status-pending' };
            return `<div class="order-item">
                <span class="order-id">${o.code}</span>
                <div style="flex:1;min-width:0;">
                    <div class="order-customer">${o.customer}</div>
                    <div class="order-total">${o.total}</div>
                </div>
                <span class="order-status ${st.cls}">${st.label}</span>
                <a href="${o.url}" class="btn-view"><i class="fas fa-eye"></i> Lihat</a>
            </div>`;
        }).join('');
    }

    panel.classList.add('visible');
    panel.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
}

function hideDetail() {
    document.getElementById('detailPanel').classList.remove('visible');
    if (selectedCell) { selectedCell.classList.remove('selected'); selectedCell = null; }
}
</script>
@endsection

