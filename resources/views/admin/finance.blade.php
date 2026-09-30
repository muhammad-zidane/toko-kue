@extends('admin.layout')
@section('title', 'Keuangan')
@section('page-title', 'Keuangan')
@section('page-subtitle', 'Pantau arus kas dan riwayat pembayaran')

@section('content')
{{-- STATS --}}
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
    <div class="bg-white rounded-2xl border border-cream-border p-5 shadow-sm hover:-translate-y-1 hover:shadow-md transition-all duration-200">
        <div class="w-12 h-12 rounded-xl flex items-center justify-center text-xl mb-4 bg-green-50 text-green-600"><i class="fas fa-money-bill-wave"></i></div>
        <div class="font-heading text-3xl font-bold text-brown-dark mt-1">Rp {{ number_format($totalRevenue, 0, ',', '.') }}</div>
        <div class="text-sm text-text-secondary">Total Pendapatan</div>
    </div>
    <div class="bg-white rounded-2xl border border-cream-border p-5 shadow-sm hover:-translate-y-1 hover:shadow-md transition-all duration-200">
        <div class="w-12 h-12 rounded-xl flex items-center justify-center text-xl mb-4 bg-amber-50 text-amber-600"><i class="fas fa-hourglass-half"></i></div>
        <div class="font-heading text-3xl font-bold text-brown-dark mt-1">Rp {{ number_format($pendingPayments, 0, ',', '.') }}</div>
        <div class="text-sm text-text-secondary">Menunggu Pembayaran</div>
    </div>
    <div class="bg-white rounded-2xl border border-cream-border p-5 shadow-sm hover:-translate-y-1 hover:shadow-md transition-all duration-200">
        <div class="w-12 h-12 rounded-xl flex items-center justify-center text-xl mb-4 bg-primary/10 text-primary"><i class="fas fa-check-circle"></i></div>
        <div class="font-heading text-3xl font-bold text-brown-dark mt-1">{{ $paidCount }}</div>
        <div class="text-sm text-text-secondary">Pembayaran Lunas</div>
    </div>
    <div class="bg-white rounded-2xl border border-cream-border p-5 shadow-sm hover:-translate-y-1 hover:shadow-md transition-all duration-200">
        <div class="w-12 h-12 rounded-xl flex items-center justify-center text-xl mb-4 bg-blue-50 text-blue-600"><i class="fas fa-clock"></i></div>
        <div class="font-heading text-3xl font-bold text-brown-dark mt-1">{{ $pendingCount }}</div>
        <div class="text-sm text-text-secondary">Belum Dibayar</div>
    </div>
</div>

{{-- TABLE --}}
<div class="bg-white rounded-2xl border border-cream-border overflow-hidden shadow-sm">
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr>
                    <th class="px-5 py-3 text-xs font-bold text-brown-light uppercase tracking-wider bg-cream/50 border-b border-cream-border">#</th>
                    <th class="px-5 py-3 text-xs font-bold text-brown-light uppercase tracking-wider bg-cream/50 border-b border-cream-border">Kode Pesanan</th>
                    <th class="px-5 py-3 text-xs font-bold text-brown-light uppercase tracking-wider bg-cream/50 border-b border-cream-border">Pelanggan</th>
                    <th class="px-5 py-3 text-xs font-bold text-brown-light uppercase tracking-wider bg-cream/50 border-b border-cream-border">Metode</th>
                    <th class="px-5 py-3 text-xs font-bold text-brown-light uppercase tracking-wider bg-cream/50 border-b border-cream-border">Jumlah</th>
                    <th class="px-5 py-3 text-xs font-bold text-brown-light uppercase tracking-wider bg-cream/50 border-b border-cream-border">Status</th>
                    <th class="px-5 py-3 text-xs font-bold text-brown-light uppercase tracking-wider bg-cream/50 border-b border-cream-border">Tanggal</th>
                </tr>
            </thead>
            <tbody>
                @forelse($payments as $payment)
                <tr class="hover:bg-cream-warm/20 transition-colors">
                    <td class="px-5 py-4 text-sm border-b border-cream-border/50 align-middle font-semibold text-text-muted">{{ $loop->iteration + ($payments->currentPage() - 1) * $payments->perPage() }}</td>
                    <td class="px-5 py-4 text-sm border-b border-cream-border/50 align-middle font-bold text-brown-dark">#{{ $payment->order->order_code ?? '-' }}</td>
                    <td class="px-5 py-4 text-sm border-b border-cream-border/50 align-middle text-brown-dark">{{ $payment->order->user->name ?? '-' }}</td>
                    <td class="px-5 py-4 text-sm border-b border-cream-border/50 align-middle">
                        <span class="inline-block px-2.5 py-0.5 rounded-full text-xs font-semibold bg-cream-warm text-brown-mid border border-cream-border">{{ ['transfer_bank'=>'Transfer Bank','ewallet'=>'E-Wallet','qris'=>'QRIS','cod'=>'COD'][$payment->payment_method ?? ''] ?? ($payment->payment_method ?? '-') }}</span>
                    </td>
                    <td class="px-5 py-4 text-sm border-b border-cream-border/50 align-middle font-bold text-brown-dark">Rp {{ number_format($payment->amount, 0, ',', '.') }}</td>
                    <td class="px-5 py-4 text-sm border-b border-cream-border/50 align-middle">
                        @php
                            $badgeClass = match($payment->status) {
                                'paid'   => 'bg-green-50 text-green-700',
                                'failed' => 'bg-red-50 text-red-700',
                                default  => 'bg-amber-50 text-amber-700',
                            };
                        @endphp
                        <span class="inline-flex items-center justify-center px-3 py-1 rounded-full text-xs font-bold {{ $badgeClass }}">{{ $payment->status_label }}</span>
                    </td>
                    <td class="px-5 py-4 text-sm border-b border-cream-border/50 align-middle text-text-secondary text-xs">{{ $payment->created_at->format('d M Y, H:i') }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="px-5 py-12 text-center border-b border-cream-border/50">
                        <div class="w-20 h-20 rounded-full bg-primary-light text-primary text-3xl flex items-center justify-center mx-auto mb-4"><i class="fas fa-credit-card"></i></div>
                        <h3 class="font-semibold text-base text-brown-dark mb-1">Belum Ada Transaksi</h3>
                        <p class="text-sm text-text-secondary">Transaksi pembayaran akan muncul di sini.</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($payments->hasPages())
    <div class="p-4 flex justify-center bg-white border-t border-cream-border">
        {{ $payments->links('pagination::simple-bootstrap-5') }}
    </div>
    @endif
</div>
@endsection

