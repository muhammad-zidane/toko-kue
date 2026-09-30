@extends('admin.layout')
@section('title', 'Data Pelanggan')
@section('page-title', 'Data Pelanggan')
@section('page-subtitle', 'Lihat semua pelanggan terdaftar')

@section('content')
@php
    $colors = ['#E8587A', '#3B82F6', '#22C55E', '#F59E0B', '#8B5CF6', '#EC4899', '#14B8A6'];
@endphp

{{-- STATS --}}
<div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
    <div class="bg-white rounded-2xl border border-cream-border p-5 shadow-sm hover:-translate-y-1 hover:shadow-md transition-all duration-200">
        <div class="w-12 h-12 rounded-xl flex items-center justify-center text-xl mb-4 bg-primary/10 text-primary"><i class="fas fa-user"></i></div>
        <div class="font-heading text-3xl font-bold text-brown-dark mt-1">{{ $totalCustomers }}</div>
        <div class="text-sm text-text-secondary">Total Pelanggan</div>
    </div>
    <div class="bg-white rounded-2xl border border-cream-border p-5 shadow-sm hover:-translate-y-1 hover:shadow-md transition-all duration-200">
        <div class="w-12 h-12 rounded-xl flex items-center justify-center text-xl mb-4 bg-green-50 text-green-600"><i class="fas fa-user-plus"></i></div>
        <div class="font-heading text-3xl font-bold text-brown-dark mt-1">{{ $newCustomers }}</div>
        <div class="text-sm text-text-secondary">Pelanggan Baru (Bulan Ini)</div>
    </div>
    <div class="bg-white rounded-2xl border border-cream-border p-5 shadow-sm hover:-translate-y-1 hover:shadow-md transition-all duration-200">
        <div class="w-12 h-12 rounded-xl flex items-center justify-center text-xl mb-4 bg-blue-50 text-blue-600"><i class="fas fa-box"></i></div>
        <div class="font-heading text-3xl font-bold text-brown-dark mt-1">{{ $totalOrders }}</div>
        <div class="text-sm text-text-secondary">Total Pesanan</div>
    </div>
</div>

{{-- TABLE --}}
<div class="bg-white rounded-2xl border border-cream-border overflow-hidden shadow-sm">
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr>
                    <th class="px-5 py-3 text-xs font-bold text-brown-light uppercase tracking-wider bg-cream/50 border-b border-cream-border">#</th>
                    <th class="px-5 py-3 text-xs font-bold text-brown-light uppercase tracking-wider bg-cream/50 border-b border-cream-border">Pelanggan</th>
                    <th class="px-5 py-3 text-xs font-bold text-brown-light uppercase tracking-wider bg-cream/50 border-b border-cream-border">Pesanan</th>
                    <th class="px-5 py-3 text-xs font-bold text-brown-light uppercase tracking-wider bg-cream/50 border-b border-cream-border">Total Belanja</th>
                    <th class="px-5 py-3 text-xs font-bold text-brown-light uppercase tracking-wider bg-cream/50 border-b border-cream-border">Bergabung</th>
                </tr>
            </thead>
            <tbody>
                @forelse($customers as $i => $customer)
                @php $totalSpent = $customer->orders->sum('total_price'); @endphp
                <tr class="hover:bg-cream-warm/20 transition-colors">
                    <td class="px-5 py-4 text-sm border-b border-cream-border/50 align-middle font-semibold text-text-muted">{{ $loop->iteration + ($customers->currentPage() - 1) * $customers->perPage() }}</td>
                    <td class="px-5 py-4 text-sm border-b border-cream-border/50 align-middle">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-full flex items-center justify-center text-white font-bold text-xs shadow-sm" style="background:{{ $colors[$i % count($colors)] }};">
                                {{ strtoupper(substr($customer->name, 0, 2)) }}
                            </div>
                            <div>
                                <div class="font-bold text-brown-dark text-sm">{{ $customer->name }}</div>
                                <div class="text-xs text-text-secondary mt-0.5">{{ $customer->email }}</div>
                            </div>
                        </div>
                    </td>
                    <td class="px-5 py-4 text-sm border-b border-cream-border/50 align-middle">
                        <span class="inline-block px-2.5 py-0.5 rounded-full text-xs font-semibold bg-cream-warm text-brown-mid border border-cream-border">{{ $customer->orders->count() }} pesanan</span>
                    </td>
                    <td class="px-5 py-4 text-sm border-b border-cream-border/50 align-middle font-bold text-brown-dark">Rp {{ number_format($totalSpent, 0, ',', '.') }}</td>
                    <td class="px-5 py-4 text-sm border-b border-cream-border/50 align-middle text-text-secondary text-xs">{{ $customer->created_at->format('d M Y') }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="px-5 py-8 border-b border-cream-border/50 text-center">
                        <div class="py-6 text-center">
                            <div class="w-12 h-12 rounded-full bg-primary-light text-primary flex items-center justify-center mx-auto mb-3 text-lg"><i class="fas fa-user"></i></div>
                            <h3 class="font-bold text-brown-dark text-sm mb-1">Belum Ada Pelanggan</h3>
                            <p class="text-xs text-text-secondary">Pelanggan akan muncul di sini.</p>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($customers->hasPages())
    <div class="p-4 flex justify-center bg-white border-t border-cream-border">
        {{ $customers->links() }}
    </div>
    @endif
</div>
@endsection
