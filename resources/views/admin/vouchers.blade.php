@extends('admin.layout')
@section('title', 'Kelola Voucher')
@section('page-title', 'Kelola Voucher')
@section('page-subtitle', 'Buat dan kelola kode diskon untuk pelanggan')

@section('content')
<div class="grid grid-cols-1 lg:grid-cols-[2fr_1.2fr] gap-6">
    {{-- TABEL VOUCHER --}}
    <div class="bg-white rounded-2xl border border-cream-border overflow-hidden shadow-sm">
        <div class="px-5 py-4 border-b border-cream-border flex justify-between items-center bg-cream-warm/10">
            <h2 class="font-heading text-lg font-bold text-brown-dark">Daftar Voucher</h2>
            <span class="bg-primary text-white px-2.5 py-1 rounded-full text-xs font-bold">{{ $vouchers->count() }} Voucher</span>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr>
                        <th class="admin-th">Kode</th>
                        <th class="admin-th">Tipe & Nilai</th>
                        <th class="admin-th">Penggunaan</th>
                        <th class="admin-th">Min. Beli</th>
                        <th class="admin-th">Kadaluarsa</th>
                        <th class="admin-th">Status</th>
                        <th class="admin-th text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($vouchers as $voucher)
                    @php
                        $isExpired = $voucher->expires_at && $voucher->expires_at->isPast();
                        $usagePercent = $voucher->usage_limit > 0 ? min(100, ($voucher->used_count / $voucher->usage_limit) * 100) : 0;
                    @endphp
                    <tr class="hover:bg-cream-warm/20 transition-colors">
                        <td class="px-5 py-4 text-sm border-b border-cream-border/50 align-middle">
                            <span class="font-mono bg-cream-warm px-2 py-1 rounded border border-cream-border text-brown-dark font-bold text-xs">{{ $voucher->code }}</span>
                        </td>
                        <td class="px-5 py-4 text-sm border-b border-cream-border/50 align-middle">
                            <span class="inline-block px-2 py-0.5 rounded text-[10px] font-bold {{ $voucher->type === 'percent' ? 'bg-blue-50 text-blue-700 border border-blue-200' : 'bg-green-50 text-green-700 border border-green-200' }}">
                                {{ $voucher->type === 'percent' ? 'Persen' : 'Nominal' }}
                            </span>
                            <div class="text-xs font-bold text-brown-dark mt-1">
                                @if($voucher->type === 'percent')
                                    {{ $voucher->value }}%
                                @else
                                    Rp {{ number_format($voucher->value, 0, ',', '.') }}
                                @endif
                            </div>
                        </td>
                        <td class="px-5 py-4 text-sm border-b border-cream-border/50 align-middle">
                            <div class="w-24">
                                <div class="text-[11px] text-text-secondary font-semibold mb-1">{{ $voucher->used_count ?? 0 }} / {{ $voucher->usage_limit ?? '∞' }}</div>
                                @if($voucher->usage_limit)
                                <div class="w-full h-1 bg-cream-border rounded-full overflow-hidden">
                                    <div class="h-full bg-primary rounded-full" style="width:{{ $usagePercent }}%"></div>
                                </div>
                                @endif
                            </div>
                        </td>
                        <td class="px-5 py-4 text-sm border-b border-cream-border/50 align-middle">
                            @if($voucher->min_purchase)
                                <span class="text-xs font-semibold text-brown-dark">Rp {{ number_format($voucher->min_purchase, 0, ',', '.') }}</span>
                            @else
                                <span class="text-xs text-text-muted">—</span>
                            @endif
                        </td>
                        <td class="px-5 py-4 text-sm border-b border-cream-border/50 align-middle">
                            @if($voucher->expires_at)
                                <span class="text-xs font-semibold" style="color:{{ $isExpired ? '#DC2626' : 'var(--text-dark)' }};">
                                    {{ $voucher->expires_at->format('d M Y') }}
                                </span>
                            @else
                                <span class="text-xs text-text-muted">Tidak ada</span>
                            @endif
                        </td>
                        <td class="px-5 py-4 text-sm border-b border-cream-border/50 align-middle">
                            @if($isExpired)
                                <span class="inline-flex items-center justify-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-red-50 text-red-700">Kadaluarsa</span>
                            @elseif($voucher->is_active)
                                <span class="inline-flex items-center justify-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-green-50 text-green-700">Aktif</span>
                            @else
                                <span class="inline-flex items-center justify-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-gray-100 text-gray-500">Nonaktif</span>
                            @endif
                        </td>
                        <td class="px-5 py-4 text-sm border-b border-cream-border/50 align-middle text-right">
                            <div class="flex gap-2 justify-end items-center">
                                <form method="POST" action="{{ route('admin.vouchers.update', $voucher) }}" class="m-0">
                                    @csrf @method('PUT')
                                    <input type="hidden" name="code" value="{{ $voucher->code }}">
                                    <input type="hidden" name="type" value="{{ $voucher->type }}">
                                    <input type="hidden" name="value" value="{{ $voucher->value }}">
                                    <input type="hidden" name="usage_limit" value="{{ $voucher->usage_limit }}">
                                    <input type="hidden" name="min_purchase" value="{{ $voucher->min_purchase }}">
                                    <input type="hidden" name="expires_at" value="{{ $voucher->expires_at?->format('Y-m-d') }}">
                                    <input type="hidden" name="is_active" value="{{ $voucher->is_active ? 0 : 1 }}">
                                    <button type="submit" class="text-xs px-2.5 py-1.5 rounded-lg border font-semibold transition-all cursor-pointer @if($voucher->is_active) border-red-300 text-red-600 hover:bg-red-600 hover:text-white @else border-primary text-primary hover:bg-primary hover:text-white @endif">
                                        {{ $voucher->is_active ? 'Nonaktifkan' : 'Aktifkan' }}
                                    </button>
                                </form>
                                <form method="POST" action="{{ route('admin.vouchers.destroy', $voucher) }}" onsubmit="return confirm('Hapus voucher {{ $voucher->code }}?')" class="m-0">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="text-xs px-3 py-1.5 rounded-lg border border-red-300 text-red-600 hover:bg-red-600 hover:text-white transition-all cursor-pointer"><i class="fas fa-trash"></i></button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-5 py-8 border-b border-cream-border/50 text-center">
                            <div class="py-6 text-center">
                                <div class="w-12 h-12 rounded-full bg-primary-light text-primary flex items-center justify-center mx-auto mb-3 text-lg"><i class="fas fa-ticket-alt"></i></div>
                                <h3 class="font-bold text-brown-dark text-sm mb-1">Belum Ada Voucher</h3>
                                <p class="text-xs text-text-secondary">Silakan buat voucher diskon baru di panel sebelah kanan.</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- FORM TAMBAH --}}
    <div class="bg-white rounded-2xl border border-cream-border p-5 shadow-sm h-fit lg:sticky lg:top-24">
        <h2 class="font-heading text-lg font-bold text-brown-dark mb-4">Buat Voucher Baru</h2>
        <form method="POST" action="{{ route('admin.vouchers.store') }}" class="m-0">
            @csrf
            <div class="mb-4">
                <label class="block text-xs font-bold text-text-secondary uppercase tracking-wider mb-1.5">Kode Voucher <span class="text-red-500">*</span></label>
                <input type="text" name="code" required placeholder="Contoh: LEBARAN25" class="w-full border border-cream-border rounded-xl px-4 py-2.5 text-sm text-text-primary bg-white outline-none transition-all focus:border-primary focus:ring-2 focus:ring-primary/20" value="{{ old('code') }}" style="text-transform:uppercase;">
            </div>
            <div class="grid grid-cols-2 gap-3 mb-4">
                <div>
                    <label class="block text-xs font-bold text-text-secondary uppercase tracking-wider mb-1.5">Tipe Diskon <span class="text-red-500">*</span></label>
                    <select name="type" required class="w-full border border-cream-border rounded-xl px-4 py-2.5 text-sm text-text-primary bg-white outline-none transition-all focus:border-primary focus:ring-2 focus:ring-primary/20 cursor-pointer">
                        <option value="percent" {{ old('type') === 'percent' ? 'selected' : '' }}>Persentase (%)</option>
                        <option value="fixed" {{ old('type') === 'fixed' ? 'selected' : '' }}>Nominal (Rp)</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-bold text-text-secondary uppercase tracking-wider mb-1.5">Nilai <span class="text-red-500">*</span></label>
                    <input type="number" name="value" required min="1" placeholder="Contoh: 10" class="w-full border border-cream-border rounded-xl px-4 py-2.5 text-sm text-text-primary bg-white outline-none transition-all focus:border-primary focus:ring-2 focus:ring-primary/20" value="{{ old('value') }}">
                </div>
            </div>
            <div class="mb-4">
                <label class="block text-xs font-bold text-text-secondary uppercase tracking-wider mb-1.5">Batas Penggunaan</label>
                <input type="number" name="usage_limit" min="1" placeholder="Kosongkan = tidak terbatas" class="w-full border border-cream-border rounded-xl px-4 py-2.5 text-sm text-text-primary bg-white outline-none transition-all focus:border-primary focus:ring-2 focus:ring-primary/20" value="{{ old('usage_limit') }}">
            </div>
            <div class="mb-4">
                <label class="block text-xs font-bold text-text-secondary uppercase tracking-wider mb-1.5">Minimum Pembelian (Rp)</label>
                <input type="number" name="min_purchase" min="0" placeholder="Kosongkan = tidak ada minimum" class="w-full border border-cream-border rounded-xl px-4 py-2.5 text-sm text-text-primary bg-white outline-none transition-all focus:border-primary focus:ring-2 focus:ring-primary/20" value="{{ old('min_purchase') }}">
            </div>
            <div class="mb-4">
                <label class="block text-xs font-bold text-text-secondary uppercase tracking-wider mb-1.5">Tanggal Kadaluarsa</label>
                <input type="date" name="expires_at" class="w-full border border-cream-border rounded-xl px-4 py-2.5 text-sm text-text-primary bg-white outline-none transition-all focus:border-primary focus:ring-2 focus:ring-primary/20" value="{{ old('expires_at') }}">
            </div>
            <button type="submit" class="w-full bg-primary text-white font-bold text-xs py-3 px-4 rounded-full shadow-gold hover:bg-primary-hover hover:-translate-y-0.5 transition-all duration-200 cursor-pointer flex items-center justify-center gap-1.5 border-0"><i class="fas fa-plus"></i> Buat Voucher</button>
        </form>
    </div>
</div>
@endsection
