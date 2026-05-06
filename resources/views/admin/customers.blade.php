@extends('admin.layout')
@section('title', 'Data Pelanggan')
@section('page-title', 'Data Pelanggan')
@section('page-subtitle', 'Lihat semua pelanggan terdaftar')

@section('styles')
<style>
    .stats-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 16px; margin-bottom: 24px; }
    .stat-card { background: white; border-radius: 16px; padding: 20px; border: 1px solid #EDE0D4; transition: transform 0.2s; }
    .stat-card:hover { transform: translateY(-2px); }
    .stat-icon { width: 44px; height: 44px; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 20px; margin-bottom: 12px; }
    .stat-value { font-size: 30px; font-weight: 800; color: var(--text-dark); }
    .stat-label { font-size: 12px; color: var(--gray); margin-top: 4px; }
    .card { background: white; border-radius: 16px; border: 1px solid #EDE0D4; overflow: hidden; }
    table { width: 100%; border-collapse: collapse; }
    th { padding: 12px 16px; font-size: 11px; font-weight: 700; color: var(--gray); text-transform: uppercase; letter-spacing: 0.5px; background: var(--cream); border-bottom: 1px solid #EDE0D4; text-align: left; }
    td { padding: 14px 16px; font-size: 14px; border-bottom: 1px solid rgba(237,224,212,0.5); vertical-align: middle; }
    tr:hover { background: rgba(255,248,238,0.3); }
    .avatar { width: 40px; height: 40px; border-radius: 50%; color: white; font-size: 14px; font-weight: 700; display: flex; align-items: center; justify-content: center; flex-shrink: 0; }
    .customer-name { font-size: 14px; font-weight: 600; }
    .customer-email { font-size: 11px; color: var(--gray); }
    .order-badge { background: var(--cream); padding: 4px 10px; border-radius: 20px; font-size: 12px; font-weight: 600; color: var(--brown-dark); }
    @media (max-width: 768px) { .stats-grid { grid-template-columns: 1fr; } }
</style>
@endsection

@section('content')
@php
    $colors = ['#E8587A', '#3B82F6', '#22C55E', '#F59E0B', '#8B5CF6', '#EC4899', '#14B8A6'];
@endphp

{{-- STATS --}}
<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-icon" style="background:rgba(240,80,122,0.1);">👤</div>
        <div class="stat-value">{{ $totalCustomers }}</div>
        <div class="stat-label">Total Pelanggan</div>
    </div>
    <div class="stat-card">
        <div class="stat-icon" style="background:rgba(34,197,94,0.1);">🆕</div>
        <div class="stat-value">{{ $newCustomers }}</div>
        <div class="stat-label">Pelanggan Baru (Bulan Ini)</div>
    </div>
    <div class="stat-card">
        <div class="stat-icon" style="background:rgba(59,130,246,0.1);">📦</div>
        <div class="stat-value">{{ $totalOrders }}</div>
        <div class="stat-label">Total Pesanan</div>
    </div>
</div>

{{-- TABLE --}}
<div class="card">
    <div style="overflow-x:auto;">
        <table>
            <thead>
                <tr>
                    <th>#</th>
                    <th>Pelanggan</th>
                    <th>Pesanan</th>
                    <th>Total Belanja</th>
                    <th>Bergabung</th>
                </tr>
            </thead>
            <tbody>
                @forelse($customers as $i => $customer)
                @php $totalSpent = $customer->orders->sum('total_price'); @endphp
                <tr>
                    <td style="font-weight:600;color:var(--gray);">{{ $i + 1 }}</td>
                    <td>
                        <div style="display:flex;align-items:center;gap:12px;">
                            <div class="avatar" style="background:{{ $colors[$i % count($colors)] }};">
                                {{ strtoupper(substr($customer->name, 0, 2)) }}
                            </div>
                            <div>
                                <div class="customer-name">{{ $customer->name }}</div>
                                <div class="customer-email">{{ $customer->email }}</div>
                            </div>
                        </div>
                    </td>
                    <td><span class="order-badge">{{ $customer->orders->count() }} pesanan</span></td>
                    <td style="font-weight:700;color:var(--brown-dark);">Rp {{ number_format($totalSpent, 0, ',', '.') }}</td>
                    <td style="font-size:12px;color:var(--gray);">{{ $customer->created_at->format('d M Y') }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" style="text-align:center;padding:64px;">
                        <div style="font-size:48px;margin-bottom:12px;">👤</div>
                        <h3 style="font-weight:700;color:var(--brown-dark);">Belum Ada Pelanggan</h3>
                        <p style="font-size:14px;color:var(--gray);">Pelanggan akan muncul di sini.</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
