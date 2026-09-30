@extends('admin.layout')
@section('title', 'Kelola Pesanan')
@section('page-title', 'Kelola Pesanan')
@section('page-subtitle', 'Lihat dan kelola semua pesanan pelanggan')

@section('content')
<div class="bg-white rounded-2xl border border-cream-border overflow-hidden shadow-sm">
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr>
                    <th class="px-5 py-3 text-xs font-bold text-brown-light uppercase tracking-wider bg-cream/50 border-b border-cream-border">Kode</th>
                    <th class="px-5 py-3 text-xs font-bold text-brown-light uppercase tracking-wider bg-cream/50 border-b border-cream-border">Pelanggan</th>
                    <th class="px-5 py-3 text-xs font-bold text-brown-light uppercase tracking-wider bg-cream/50 border-b border-cream-border">Total</th>
                    <th class="px-5 py-3 text-xs font-bold text-brown-light uppercase tracking-wider bg-cream/50 border-b border-cream-border">Status Order</th>
                    <th class="px-5 py-3 text-xs font-bold text-brown-light uppercase tracking-wider bg-cream/50 border-b border-cream-border">Status Bayar</th>
                    <th class="px-5 py-3 text-xs font-bold text-brown-light uppercase tracking-wider bg-cream/50 border-b border-cream-border">Tanggal</th>
                    <th class="px-5 py-3 text-xs font-bold text-brown-light uppercase tracking-wider bg-cream/50 border-b border-cream-border">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($orders as $order)
                <tr class="hover:bg-cream-warm/20 transition-colors">
                    <td class="px-5 py-4 text-sm border-b border-cream-border/50 align-middle font-bold text-brown-dark">{{ $order->order_code }}</td>
                    <td class="px-5 py-4 text-sm border-b border-cream-border/50 align-middle text-brown-dark">{{ $order->user->name ?? '-' }}</td>
                    <td class="px-5 py-4 text-sm border-b border-cream-border/50 align-middle font-semibold text-brown-dark">Rp {{ number_format($order->total_price, 0, ',', '.') }}</td>
                    <td class="px-5 py-4 text-sm border-b border-cream-border/50 align-middle">
                        <span class="inline-flex items-center justify-center px-3 py-1 rounded-full text-xs font-bold @if($order->status == 'pending') bg-amber-50 text-amber-700 @elseif($order->status == 'processing') bg-blue-50 text-blue-700 @elseif($order->status == 'completed') bg-green-50 text-green-700 @else bg-red-50 text-red-700 @endif">{{ ucfirst($order->status) }}</span>
                    </td>
                    <td class="px-5 py-4 text-sm border-b border-cream-border/50 align-middle">
                        @php
                            $pStatus = $order->payment->status ?? 'unpaid';
                            $pLabel = $order->payment?->status_label ?? 'Belum Bayar';
                        @endphp
                        <span class="inline-flex items-center justify-center px-3 py-1 rounded-full text-xs font-bold @if($pStatus == 'paid') bg-green-50 text-green-700 @elseif($pStatus == 'processing') bg-blue-50 text-blue-700 @else bg-amber-50 text-amber-700 @endif">{{ $pLabel }}</span>
                    </td>
                    <td class="px-5 py-4 text-sm border-b border-cream-border/50 align-middle text-text-secondary">{{ $order->created_at->format('d M Y, H:i') }}</td>
                    <td class="px-5 py-4 text-sm border-b border-cream-border/50 align-middle">
                        <div class="flex flex-wrap gap-1.5 items-center">
                            <a href="{{ route('admin.orders.show', $order) }}" class="text-xs px-3 py-1.5 rounded-lg border border-cream-border text-brown-mid hover:bg-cream-dark transition-all">Detail</a>
                            @if($order->status !== 'processing' && $order->status !== 'completed' && $order->status !== 'cancelled')
                            <form method="POST" action="{{ route('admin.orders.status', [$order, 'processing']) }}" class="m-0">
                                @csrf @method('PATCH')
                                <button type="submit" class="text-xs px-2.5 py-1.5 rounded-lg font-semibold bg-blue-50 text-blue-700 hover:bg-blue-100 transition-colors border-0 cursor-pointer">Proses</button>
                            </form>
                            @endif
                            @if($order->status !== 'completed' && $order->status !== 'cancelled')
                            <form method="POST" action="{{ route('admin.orders.status', [$order, 'completed']) }}" class="m-0">
                                @csrf @method('PATCH')
                                <button type="submit" class="text-xs px-2.5 py-1.5 rounded-lg font-semibold bg-green-50 text-green-700 hover:bg-green-100 transition-colors border-0 cursor-pointer">Selesai</button>
                            </form>
                            @endif
                            @if($order->status !== 'cancelled' && $order->status !== 'completed')
                            <form method="POST" action="{{ route('admin.orders.status', [$order, 'cancelled']) }}" class="m-0">
                                @csrf @method('PATCH')
                                <button type="submit" class="text-xs px-2.5 py-1.5 rounded-lg font-semibold bg-red-50 text-red-700 hover:bg-red-100 transition-colors border-0 cursor-pointer">Batal</button>
                            </form>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="px-5 py-8 text-center text-text-muted text-sm border-b border-cream-border/50">Belum ada pesanan.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="p-4 flex justify-center bg-white border-t border-cream-border">
        {{ $orders->links('pagination::simple-bootstrap-5') }}
    </div>
</div>
@endsection

