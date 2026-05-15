@extends('admin.layout')
@section('title', 'Kelola Voucher')
@section('page-title', 'Kelola Voucher')
@section('page-subtitle', 'Buat dan kelola kode diskon untuk pelanggan')

@push('styles')
<style>
    .voucher-grid { display: grid; grid-template-columns: 1fr 360px; gap: 24px; }
    .card { background: white; border-radius: 16px; border: 1px solid #EDE0D4; overflow: hidden; }
    .card-header { padding: 20px; border-bottom: 1px solid #EDE0D4; display: flex; justify-content: space-between; align-items: center; background: rgba(255,248,238,0.3); }
    .card-header h2 { font-size: 15px; font-weight: 700; color: var(--text-dark); }
    .count-badge { background: var(--pink); color: white; padding: 4px 10px; border-radius: 20px; font-size: 11px; font-weight: 700; }
    table { width: 100%; border-collapse: collapse; }
    th { padding: 12px 16px; font-size: 11px; font-weight: 700; color: var(--gray); text-transform: uppercase; letter-spacing: 0.5px; background: #FAFAF8; border-bottom: 1px solid #EDE0D4; text-align: left; }
    td { padding: 12px 16px; font-size: 13px; border-bottom: 1px solid rgba(237,224,212,0.5); vertical-align: middle; }
    tr:hover { background: #FFFBF5; }
    .voucher-code { font-family: monospace; font-weight: 700; background: var(--cream); color: var(--brown-dark); padding: 4px 10px; border-radius: 6px; font-size: 13px; letter-spacing: 1px; }
    .type-badge { display: inline-block; padding: 3px 10px; border-radius: 20px; font-size: 11px; font-weight: 700; }
    .type-percent { background: #DCFCE7; color: #16A34A; }
    .type-fixed { background: #DBEAFE; color: #1D4ED8; }
    .usage-bar-wrap { min-width: 80px; }
    .usage-text { font-size: 12px; color: var(--gray); margin-bottom: 4px; }
    .usage-bar { height: 6px; background: #EDE0D4; border-radius: 3px; overflow: hidden; }
    .usage-fill { height: 100%; background: var(--pink); border-radius: 3px; }
    .status-badge { display: inline-block; padding: 3px 10px; border-radius: 20px; font-size: 11px; font-weight: 700; }
    .status-active { background: #DCFCE7; color: #16A34A; }
    .status-inactive { background: #F3F4F6; color: #6B7280; }
    .status-expired { background: #FEE2E2; color: #DC2626; }
    .action-group { display: flex; gap: 6px; align-items: center; justify-content: flex-end; }
    .btn-toggle { padding: 6px 12px; border-radius: 6px; font-size: 11px; font-weight: 600; border: none; cursor: pointer; font-family: 'Plus Jakarta Sans', sans-serif; }
    .btn-toggle-active { background: #FEF3C7; color: #D97706; }
    .btn-toggle-inactive { background: #DCFCE7; color: #16A34A; }
    .btn-delete { padding: 6px 12px; border-radius: 6px; font-size: 11px; font-weight: 600; background: #FEE2E2; color: #DC2626; border: none; cursor: pointer; font-family: 'Plus Jakarta Sans', sans-serif; display: inline-flex; align-items: center; gap: 4px; }
    .btn-delete:hover, .btn-toggle:hover { opacity: 0.8; }
    .form-card { background: white; border-radius: 16px; border: 1px solid #EDE0D4; padding: 20px; height: fit-content; position: sticky; top: 96px; }
    .form-card h2 { font-size: 15px; font-weight: 700; color: var(--text-dark); margin-bottom: 16px; }
    .form-group { margin-bottom: 16px; }
    .form-label { display: block; font-size: 12px; font-weight: 700; color: var(--gray); margin-bottom: 6px; }
    .form-input { width: 100%; border: 1.5px solid var(--cream-dark); border-radius: 8px; padding: 10px 14px; font-size: 13px; outline: none; background: #FAFAF8; font-family: 'Plus Jakarta Sans', sans-serif; transition: border-color 0.2s; box-sizing: border-box; }
    .form-input:focus { border-color: var(--pink); background: white; }
    .form-row { display: grid; grid-template-columns: 1fr 1fr; gap: 12px; }
    .btn-submit { width: 100%; background: var(--pink); color: white; font-weight: 700; font-size: 13px; padding: 10px; border-radius: 8px; border: none; cursor: pointer; font-family: 'Plus Jakarta Sans', sans-serif; box-shadow: 0 4px 12px rgba(240,80,122,0.2); transition: background 0.2s; }
    .btn-submit:hover { background: var(--pink-hover); }
    .empty-state { text-align: center; padding: 40px 20px; color: var(--gray); }
    .empty-icon { font-size: 40px; margin-bottom: 12px; }
    @media (max-width: 768px) { .voucher-grid { grid-template-columns: 1fr; } .form-row { grid-template-columns: 1fr; } }
</style>
@endpush

@section('content')
<div class="voucher-grid">
    {{-- TABEL VOUCHER --}}
    <div class="card">
        <div class="card-header">
            <h2>Daftar Voucher</h2>
            <span class="count-badge">{{ $vouchers->count() }} Voucher</span>
        </div>
        <div style="overflow-x:auto;">
            <table>
                <thead>
                    <tr>
                        <th>Kode</th>
                        <th>Tipe & Nilai</th>
                        <th>Penggunaan</th>
                        <th>Min. Beli</th>
                        <th>Kadaluarsa</th>
                        <th>Status</th>
                        <th style="text-align:right;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($vouchers as $voucher)
                    @php
                        $isExpired = $voucher->expires_at && $voucher->expires_at->isPast();
                        $usagePercent = $voucher->usage_limit > 0 ? min(100, ($voucher->used_count / $voucher->usage_limit) * 100) : 0;
                    @endphp
                    <tr>
                        <td><span class="voucher-code">{{ $voucher->code }}</span></td>
                        <td>
                            <span class="type-badge {{ $voucher->type === 'percent' ? 'type-percent' : 'type-fixed' }}">
                                {{ $voucher->type === 'percent' ? 'Persen' : 'Nominal' }}
                            </span>
                            <div style="font-size:12px;font-weight:700;color:var(--text-dark);margin-top:4px;">
                                @if($voucher->type === 'percent')
                                    {{ $voucher->value }}%
                                @else
                                    Rp {{ number_format($voucher->value, 0, ',', '.') }}
                                @endif
                            </div>
                        </td>
                        <td>
                            <div class="usage-bar-wrap">
                                <div class="usage-text">{{ $voucher->used_count ?? 0 }} / {{ $voucher->usage_limit ?? '∞' }}</div>
                                @if($voucher->usage_limit)
                                <div class="usage-bar">
                                    <div class="usage-fill" style="width:{{ $usagePercent }}%"></div>
                                </div>
                                @endif
                            </div>
                        </td>
                        <td>
                            @if($voucher->min_purchase)
                                <span style="font-size:12px;">Rp {{ number_format($voucher->min_purchase, 0, ',', '.') }}</span>
                            @else
                                <span style="font-size:12px;color:var(--gray);">—</span>
                            @endif
                        </td>
                        <td>
                            @if($voucher->expires_at)
                                <span style="font-size:12px; color:{{ $isExpired ? '#DC2626' : 'var(--text-dark)' }};">
                                    {{ $voucher->expires_at->format('d M Y') }}
                                </span>
                            @else
                                <span style="font-size:12px;color:var(--gray);">Tidak ada</span>
                            @endif
                        </td>
                        <td>
                            @if($isExpired)
                                <span class="status-badge status-expired">Kadaluarsa</span>
                            @elseif($voucher->is_active)
                                <span class="status-badge status-active">Aktif</span>
                            @else
                                <span class="status-badge status-inactive">Nonaktif</span>
                            @endif
                        </td>
                        <td>
                            <div class="action-group">
                                <form method="POST" action="{{ route('admin.vouchers.update', $voucher) }}">
                                    @csrf @method('PUT')
                                    <input type="hidden" name="code" value="{{ $voucher->code }}">
                                    <input type="hidden" name="type" value="{{ $voucher->type }}">
                                    <input type="hidden" name="value" value="{{ $voucher->value }}">
                                    <input type="hidden" name="usage_limit" value="{{ $voucher->usage_limit }}">
                                    <input type="hidden" name="min_purchase" value="{{ $voucher->min_purchase }}">
                                    <input type="hidden" name="expires_at" value="{{ $voucher->expires_at?->format('Y-m-d') }}">
                                    <input type="hidden" name="is_active" value="{{ $voucher->is_active ? 0 : 1 }}">
                                    <button type="submit" class="btn-toggle {{ $voucher->is_active ? 'btn-toggle-active' : 'btn-toggle-inactive' }}">
                                        {{ $voucher->is_active ? 'Nonaktifkan' : 'Aktifkan' }}
                                    </button>
                                </form>
                                <form method="POST" action="{{ route('admin.vouchers.destroy', $voucher) }}" onsubmit="return confirm('Hapus voucher {{ $voucher->code }}?')">
                                    @csrf @method('DELETE')
                                    <button class="btn-delete"><i class="fas fa-trash"></i> Hapus</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7">
                            <div class="empty-state">
                                <div class="empty-icon"><i class="fas fa-ticket-alt" style="color:var(--pink)"></i></div>
                                <h3 style="font-size:14px;font-weight:700;color:var(--text-dark);margin-bottom:4px;">Belum Ada Voucher</h3>
                                <p style="font-size:12px;">Silakan buat voucher diskon baru di panel sebelah kanan.</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- FORM TAMBAH --}}
    <div class="form-card">
        <h2>Buat Voucher Baru</h2>
        <form method="POST" action="{{ route('admin.vouchers.store') }}">
            @csrf
            <div class="form-group">
                <label class="form-label">Kode Voucher <span style="color:#EF4444;">*</span></label>
                <input type="text" name="code" required placeholder="Contoh: LEBARAN25" class="form-input" value="{{ old('code') }}" style="text-transform:uppercase;">
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">Tipe Diskon <span style="color:#EF4444;">*</span></label>
                    <select name="type" required class="form-input">
                        <option value="percent" {{ old('type') === 'percent' ? 'selected' : '' }}>Persentase (%)</option>
                        <option value="fixed" {{ old('type') === 'fixed' ? 'selected' : '' }}>Nominal (Rp)</option>
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">Nilai <span style="color:#EF4444;">*</span></label>
                    <input type="number" name="value" required min="1" placeholder="Contoh: 10" class="form-input" value="{{ old('value') }}">
                </div>
            </div>
            <div class="form-group">
                <label class="form-label">Batas Penggunaan</label>
                <input type="number" name="usage_limit" min="1" placeholder="Kosongkan = tidak terbatas" class="form-input" value="{{ old('usage_limit') }}">
            </div>
            <div class="form-group">
                <label class="form-label">Minimum Pembelian (Rp)</label>
                <input type="number" name="min_purchase" min="0" placeholder="Kosongkan = tidak ada minimum" class="form-input" value="{{ old('min_purchase') }}">
            </div>
            <div class="form-group">
                <label class="form-label">Tanggal Kadaluarsa</label>
                <input type="date" name="expires_at" class="form-input" value="{{ old('expires_at') }}">
            </div>
            <button type="submit" class="btn-submit"><i class="fas fa-plus" style="color:white"></i> Buat Voucher</button>
        </form>
    </div>
</div>
@endsection
