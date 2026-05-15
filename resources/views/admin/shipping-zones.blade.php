@extends('admin.layout')
@section('title', 'Zona Pengiriman')
@section('page-title', 'Zona Pengiriman')
@section('page-subtitle', 'Kelola area dan biaya pengiriman')

@push('styles')
<style>
    .zone-grid { display: grid; grid-template-columns: 1fr 340px; gap: 24px; }
    .card { background: white; border-radius: 16px; border: 1px solid #EDE0D4; overflow: hidden; }
    .card-header { padding: 20px; border-bottom: 1px solid #EDE0D4; display: flex; justify-content: space-between; align-items: center; background: rgba(255,248,238,0.3); }
    .card-header h2 { font-size: 15px; font-weight: 700; color: var(--text-dark); }
    .count-badge { background: var(--pink); color: white; padding: 4px 10px; border-radius: 20px; font-size: 11px; font-weight: 700; }
    table { width: 100%; border-collapse: collapse; }
    th { padding: 12px 16px; font-size: 11px; font-weight: 700; color: var(--gray); text-transform: uppercase; letter-spacing: 0.5px; background: #FAFAF8; border-bottom: 1px solid #EDE0D4; text-align: left; }
    td { padding: 14px 16px; font-size: 13px; border-bottom: 1px solid rgba(237,224,212,0.5); vertical-align: middle; }
    tr:hover { background: #FFFBF5; }
    .area-name { font-weight: 700; color: var(--text-dark); }
    .cost-text { font-weight: 700; color: var(--pink); }
    .avail-badge { display: inline-block; padding: 4px 12px; border-radius: 20px; font-size: 11px; font-weight: 700; }
    .avail-yes { background: #DCFCE7; color: #16A34A; }
    .avail-no { background: #FEE2E2; color: #DC2626; }
    .action-group { display: flex; gap: 6px; justify-content: flex-end; align-items: center; }
    .btn-edit { padding: 6px 12px; border-radius: 6px; font-size: 11px; font-weight: 600; background: #EFF6FF; color: #2563EB; border: none; cursor: pointer; font-family: 'Plus Jakarta Sans', sans-serif; }
    .btn-edit:hover { opacity: 0.8; }
    .btn-delete { padding: 6px 12px; border-radius: 6px; font-size: 11px; font-weight: 600; background: #FEE2E2; color: #DC2626; border: none; cursor: pointer; font-family: 'Plus Jakarta Sans', sans-serif; display: inline-flex; align-items: center; gap: 4px; }
    .btn-delete:hover { opacity: 0.8; }
    .form-card { background: white; border-radius: 16px; border: 1px solid #EDE0D4; padding: 20px; height: fit-content; position: sticky; top: 96px; }
    .form-card h2 { font-size: 15px; font-weight: 700; color: var(--text-dark); margin-bottom: 16px; }
    .form-group { margin-bottom: 16px; }
    .form-label { display: block; font-size: 12px; font-weight: 700; color: var(--gray); margin-bottom: 6px; }
    .form-input { width: 100%; border: 1.5px solid var(--cream-dark); border-radius: 8px; padding: 10px 14px; font-size: 13px; outline: none; background: #FAFAF8; font-family: 'Plus Jakarta Sans', sans-serif; transition: border-color 0.2s; box-sizing: border-box; }
    .form-input:focus { border-color: var(--pink); background: white; }
    .toggle { position: relative; display: inline-block; width: 36px; height: 20px; }
    .toggle input { opacity: 0; width: 0; height: 0; }
    .toggle-slider { position: absolute; cursor: pointer; top: 0; left: 0; right: 0; bottom: 0; background: #D1D5DB; border-radius: 20px; transition: 0.3s; }
    .toggle-slider:before { position: absolute; content: ""; height: 14px; width: 14px; left: 3px; bottom: 3px; background: white; border-radius: 50%; transition: 0.3s; }
    input:checked + .toggle-slider { background: var(--pink); }
    input:checked + .toggle-slider:before { transform: translateX(16px); }
    .btn-submit { width: 100%; background: var(--pink); color: white; font-weight: 700; font-size: 13px; padding: 10px; border-radius: 8px; border: none; cursor: pointer; font-family: 'Plus Jakarta Sans', sans-serif; box-shadow: 0 4px 12px rgba(240,80,122,0.2); transition: background 0.2s; }
    .btn-submit:hover { background: var(--pink-hover); }
    .empty-state { text-align: center; padding: 40px 20px; color: var(--gray); }
    .empty-icon { font-size: 40px; margin-bottom: 12px; }

    /* Edit Modal */
    .modal-overlay { display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.4); z-index: 1000; align-items: center; justify-content: center; }
    .modal-overlay.open { display: flex; }
    .modal-box { background: white; border-radius: 16px; padding: 24px; width: 380px; max-width: 90vw; }
    .modal-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; }
    .modal-header h3 { font-size: 15px; font-weight: 700; color: var(--text-dark); }
    .modal-close { background: none; border: none; cursor: pointer; font-size: 18px; color: var(--gray); }

    @media (max-width: 768px) { .zone-grid { grid-template-columns: 1fr; } }
</style>
@endpush

@section('content')
<div class="zone-grid">
    {{-- TABEL ZONA --}}
    <div class="card">
        <div class="card-header">
            <h2>Daftar Zona Pengiriman</h2>
            <span class="count-badge">{{ $zones->count() }} Zona</span>
        </div>
        <div style="overflow-x:auto;">
            <table>
                <thead>
                    <tr>
                        <th>Nama Area</th>
                        <th>Biaya Pengiriman</th>
                        <th style="text-align:center;">Tersedia</th>
                        <th style="text-align:right;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($zones as $zone)
                    <tr>
                        <td><div class="area-name">{{ $zone->area_name }}</div></td>
                        <td><span class="cost-text">Rp {{ number_format($zone->cost, 0, ',', '.') }}</span></td>
                        <td style="text-align:center;">
                            <span class="avail-badge {{ $zone->is_available ? 'avail-yes' : 'avail-no' }}">
                                {{ $zone->is_available ? 'Tersedia' : 'Tidak Tersedia' }}
                            </span>
                        </td>
                        <td>
                            <div class="action-group">
                                <button class="btn-edit" onclick="openEditModal({{ $zone->id }}, '{{ addslashes($zone->area_name) }}', {{ $zone->cost }}, {{ $zone->is_available ? 'true' : 'false' }})">
                                    <i class="fas fa-pen"></i> Edit
                                </button>
                                <form method="POST" action="{{ route('admin.shipping-zones.destroy', $zone) }}" onsubmit="return confirm('Hapus zona {{ $zone->area_name }}?')">
                                    @csrf @method('DELETE')
                                    <button class="btn-delete"><i class="fas fa-trash"></i> Hapus</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4">
                            <div class="empty-state">
                                <div class="empty-icon"><i class="fas fa-map-marker-alt" style="color:var(--pink)"></i></div>
                                <h3 style="font-size:14px;font-weight:700;color:var(--text-dark);margin-bottom:4px;">Belum Ada Zona Pengiriman</h3>
                                <p style="font-size:12px;">Tambahkan zona pengiriman di panel sebelah kanan.</p>
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
        <h2>Tambah Zona Baru</h2>
        <form method="POST" action="{{ route('admin.shipping-zones.store') }}">
            @csrf
            <div class="form-group">
                <label class="form-label">Nama Area <span style="color:#EF4444;">*</span></label>
                <input type="text" name="area_name" required placeholder="Contoh: Dalam Kota" class="form-input" value="{{ old('area_name') }}">
            </div>
            <div class="form-group">
                <label class="form-label">Biaya Pengiriman (Rp) <span style="color:#EF4444;">*</span></label>
                <input type="number" name="cost" required min="0" placeholder="Contoh: 15000" class="form-input" value="{{ old('cost', 0) }}">
            </div>
            <div class="form-group" style="display:flex;align-items:center;gap:10px;">
                <label class="toggle">
                    <input type="checkbox" name="is_available" value="1" checked>
                    <span class="toggle-slider"></span>
                </label>
                <span style="font-size:13px;font-weight:600;color:var(--text-dark);">Zona tersedia</span>
            </div>
            <button type="submit" class="btn-submit"><i class="fas fa-plus" style="color:white"></i> Tambah Zona</button>
        </form>
    </div>
</div>

{{-- EDIT MODAL --}}
<div class="modal-overlay" id="editModal">
    <div class="modal-box">
        <div class="modal-header">
            <h3>Edit Zona Pengiriman</h3>
            <button class="modal-close" onclick="closeEditModal()"><i class="fas fa-times"></i></button>
        </div>
        <form method="POST" id="editForm">
            @csrf @method('PUT')
            <div class="form-group">
                <label class="form-label">Nama Area <span style="color:#EF4444;">*</span></label>
                <input type="text" name="area_name" id="editAreaName" required class="form-input">
            </div>
            <div class="form-group">
                <label class="form-label">Biaya Pengiriman (Rp) <span style="color:#EF4444;">*</span></label>
                <input type="number" name="cost" id="editCost" required min="0" class="form-input">
            </div>
            <div class="form-group" style="display:flex;align-items:center;gap:10px;">
                <label class="toggle">
                    <input type="checkbox" id="editAvailable" name="is_available" value="1">
                    <span class="toggle-slider"></span>
                </label>
                <span style="font-size:13px;font-weight:600;color:var(--text-dark);">Zona tersedia</span>
            </div>
            <button type="submit" class="btn-submit"><i class="fas fa-save" style="color:white"></i> Simpan Perubahan</button>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
function openEditModal(id, areaName, cost, isAvailable) {
    document.getElementById('editAreaName').value = areaName;
    document.getElementById('editCost').value = cost;
    document.getElementById('editAvailable').checked = isAvailable;
    document.getElementById('editForm').action = '/admin/shipping-zones/' + id;
    document.getElementById('editModal').classList.add('open');
}
function closeEditModal() {
    document.getElementById('editModal').classList.remove('open');
}
document.getElementById('editModal').addEventListener('click', function(e) {
    if (e.target === this) closeEditModal();
});
</script>
@endpush

