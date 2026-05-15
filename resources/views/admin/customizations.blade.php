@extends('admin.layout')
@section('title', 'Kelola Kustomisasi')
@section('page-title', 'Kelola Kustomisasi')
@section('page-subtitle', 'Tambah dan kelola opsi kustomisasi produk (rasa, ukuran, topping, dll.)')

@push('styles')
<style>
    .custom-grid { display: grid; grid-template-columns: 1fr 340px; gap: 24px; }
    .card { background: white; border-radius: 16px; border: 1px solid #EDE0D4; overflow: hidden; }
    .card-header { padding: 20px; border-bottom: 1px solid #EDE0D4; display: flex; justify-content: space-between; align-items: center; background: rgba(255,248,238,0.3); }
    .card-header h2 { font-size: 15px; font-weight: 700; color: var(--text-dark); }
    .count-badge { background: var(--pink); color: white; padding: 4px 10px; border-radius: 20px; font-size: 11px; font-weight: 700; }
    table { width: 100%; border-collapse: collapse; }
    th { padding: 12px 16px; font-size: 11px; font-weight: 700; color: var(--gray); text-transform: uppercase; letter-spacing: 0.5px; background: #FAFAF8; border-bottom: 1px solid #EDE0D4; text-align: left; }
    td { padding: 12px 16px; font-size: 13px; border-bottom: 1px solid rgba(237,224,212,0.5); vertical-align: middle; }
    tr:hover { background: #FFFBF5; }
    .type-badge { display: inline-block; padding: 3px 10px; border-radius: 20px; font-size: 11px; font-weight: 700; }
    .type-rasa    { background: #FCE7F3; color: #9D174D; }
    .type-ukuran  { background: #DBEAFE; color: #1D4ED8; }
    .type-topping { background: #D1FAE5; color: #065F46; }
    .type-lainnya { background: #FEF3C7; color: #92400E; }
    .status-badge { display: inline-block; padding: 3px 10px; border-radius: 20px; font-size: 11px; font-weight: 700; }
    .status-active   { background: #DCFCE7; color: #16A34A; }
    .status-inactive { background: #F3F4F6; color: #6B7280; }
    .action-group { display: flex; gap: 6px; align-items: center; justify-content: flex-end; }
    .btn-toggle { padding: 6px 12px; border-radius: 6px; font-size: 11px; font-weight: 600; border: none; cursor: pointer; font-family: 'Plus Jakarta Sans', sans-serif; }
    .btn-toggle-active   { background: #FEF3C7; color: #D97706; }
    .btn-toggle-inactive { background: #DCFCE7; color: #16A34A; }
    .btn-delete { padding: 6px 12px; border-radius: 6px; font-size: 11px; font-weight: 600; background: #FEE2E2; color: #DC2626; border: none; cursor: pointer; font-family: 'Plus Jakarta Sans', sans-serif; display: inline-flex; align-items: center; gap: 4px; }
    .btn-delete:hover, .btn-toggle:hover { opacity: 0.8; }
    .form-card { background: white; border-radius: 16px; border: 1px solid #EDE0D4; padding: 20px; height: fit-content; position: sticky; top: 96px; }
    .form-card h2 { font-size: 15px; font-weight: 700; color: var(--text-dark); margin-bottom: 16px; }
    .form-group { margin-bottom: 16px; }
    .form-label { display: block; font-size: 12px; font-weight: 700; color: var(--gray); margin-bottom: 6px; }
    .form-input { width: 100%; border: 1.5px solid #EDE0D4; border-radius: 8px; padding: 10px 14px; font-size: 13px; outline: none; background: #FAFAF8; font-family: 'Plus Jakarta Sans', sans-serif; transition: border-color 0.2s; box-sizing: border-box; }
    .form-input:focus { border-color: var(--pink); background: white; }
    .form-row { display: grid; grid-template-columns: 1fr 1fr; gap: 12px; }
    .btn-submit { width: 100%; background: var(--pink); color: white; font-weight: 700; font-size: 13px; padding: 10px; border-radius: 8px; border: none; cursor: pointer; font-family: 'Plus Jakarta Sans', sans-serif; box-shadow: 0 4px 12px rgba(240,80,122,0.2); transition: background 0.2s; }
    .btn-submit:hover { opacity: 0.88; }
    .empty-state { text-align: center; padding: 40px 20px; color: var(--gray); }
    .empty-icon { font-size: 40px; margin-bottom: 12px; }
    .pagination-wrap { padding: 16px 20px; border-top: 1px solid #EDE0D4; }
    @media (max-width: 768px) { .custom-grid { grid-template-columns: 1fr; } .form-row { grid-template-columns: 1fr; } }
</style>
@endpush

@section('content')
<div class="custom-grid">

    {{-- TABEL OPSI KUSTOMISASI --}}
    <div class="card">
        <div class="card-header">
            <h2>Daftar Opsi Kustomisasi</h2>
            <span class="count-badge">{{ $options->total() }} Opsi</span>
        </div>
        <div style="overflow-x:auto;">
            <table>
                <thead>
                    <tr>
                        <th>Tipe</th>
                        <th>Nama Opsi</th>
                        <th>Harga Tambahan</th>
                        <th>Urutan</th>
                        <th>Status</th>
                        <th style="text-align:right;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($options as $option)
                    <tr>
                        <td>
                            @php
                                $typeClass = match($option->type) {
                                    'rasa'    => 'type-rasa',
                                    'ukuran'  => 'type-ukuran',
                                    'topping' => 'type-topping',
                                    default   => 'type-lainnya',
                                };
                                $typeLabel = match($option->type) {
                                    'rasa'    => 'Rasa',
                                    'ukuran'  => 'Ukuran',
                                    'topping' => 'Topping',
                                    default   => 'Lainnya',
                                };
                            @endphp
                            <span class="type-badge {{ $typeClass }}">{{ $typeLabel }}</span>
                        </td>
                        <td style="font-weight:600;color:var(--text-dark);">{{ $option->name }}</td>
                        <td>
                            @if($option->extra_price > 0)
                                <span style="color:var(--brown-dark);font-weight:600;">
                                    + Rp {{ number_format($option->extra_price, 0, ',', '.') }}
                                </span>
                            @else
                                <span style="color:var(--gray);">—</span>
                            @endif
                        </td>
                        <td style="color:var(--gray);">{{ $option->sort_order }}</td>
                        <td>
                            @if($option->is_active)
                                <span class="status-badge status-active">Aktif</span>
                            @else
                                <span class="status-badge status-inactive">Nonaktif</span>
                            @endif
                        </td>
                        <td>
                            <div class="action-group">
                                {{-- Toggle aktif/nonaktif --}}
                                <form method="POST" action="{{ route('admin.customizations.toggle', $option) }}">
                                    @csrf
                                    <button type="submit" class="btn-toggle {{ $option->is_active ? 'btn-toggle-active' : 'btn-toggle-inactive' }}">
                                        {{ $option->is_active ? 'Nonaktifkan' : 'Aktifkan' }}
                                    </button>
                                </form>
                                {{-- Hapus --}}
                                <form method="POST" action="{{ route('admin.customizations.destroy', $option) }}"
                                      onsubmit="return confirm('Hapus opsi kustomisasi &quot;{{ addslashes($option->name) }}&quot;?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn-delete">
                                        <i class="fas fa-trash"></i> Hapus
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6">
                            <div class="empty-state">
                                <div class="empty-icon"><i class="fas fa-sliders-h" style="color:var(--pink)"></i></div>
                                <h3 style="font-size:14px;font-weight:700;color:var(--text-dark);margin-bottom:4px;">Belum Ada Opsi Kustomisasi</h3>
                                <p style="font-size:12px;">Tambahkan opsi kustomisasi produk di panel sebelah kanan.</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($options->hasPages())
        <div class="pagination-wrap">
            {{ $options->links() }}
        </div>
        @endif
    </div>

    {{-- FORM TAMBAH OPSI --}}
    <div class="form-card">
        <h2><i class="fas fa-plus-circle" style="color:var(--pink);margin-right:6px;"></i>Tambah Opsi Baru</h2>
        <form method="POST" action="{{ route('admin.customizations.store') }}">
            @csrf

            <div class="form-group">
                <label class="form-label">Tipe <span style="color:#EF4444;">*</span></label>
                <select name="type" required class="form-input">
                    <option value="">— Pilih Tipe —</option>
                    <option value="rasa"    {{ old('type') === 'rasa'    ? 'selected' : '' }}>Rasa</option>
                    <option value="ukuran"  {{ old('type') === 'ukuran'  ? 'selected' : '' }}>Ukuran</option>
                    <option value="topping" {{ old('type') === 'topping' ? 'selected' : '' }}>Topping</option>
                    <option value="lainnya" {{ old('type') === 'lainnya' ? 'selected' : '' }}>Lainnya</option>
                </select>
                @error('type')<p style="font-size:11px;color:#DC2626;margin-top:4px;">{{ $message }}</p>@enderror
            </div>

            <div class="form-group">
                <label class="form-label">Nama Opsi <span style="color:#EF4444;">*</span></label>
                <input type="text" name="name" required
                       placeholder="Contoh: Coklat, 20cm, Oreo..."
                       class="form-input" value="{{ old('name') }}">
                @error('name')<p style="font-size:11px;color:#DC2626;margin-top:4px;">{{ $message }}</p>@enderror
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">Harga Tambahan (Rp)</label>
                    <input type="number" name="extra_price" min="0" step="500"
                           placeholder="0" class="form-input"
                           value="{{ old('extra_price', 0) }}">
                    @error('extra_price')<p style="font-size:11px;color:#DC2626;margin-top:4px;">{{ $message }}</p>@enderror
                </div>
                <div class="form-group">
                    <label class="form-label">Urutan Tampil</label>
                    <input type="number" name="sort_order" min="0"
                           placeholder="0" class="form-input"
                           value="{{ old('sort_order', 0) }}">
                    @error('sort_order')<p style="font-size:11px;color:#DC2626;margin-top:4px;">{{ $message }}</p>@enderror
                </div>
            </div>

            <button type="submit" class="btn-submit">
                <i class="fas fa-plus" style="color:white;margin-right:4px;"></i> Tambah Opsi
            </button>
        </form>
    </div>

</div>
@endsection

