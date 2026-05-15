@extends('admin.layout')
@section('title', 'Kelola Banner')
@section('page-title', 'Kelola Banner')
@section('page-subtitle', 'Atur banner yang tampil di halaman utama')

@push('styles')
<style>
    .banner-grid { display: grid; grid-template-columns: 1fr 380px; gap: 24px; }
    .card { background: white; border-radius: 16px; border: 1px solid #EDE0D4; overflow: hidden; }
    .card-header { padding: 20px; border-bottom: 1px solid #EDE0D4; display: flex; justify-content: space-between; align-items: center; background: rgba(255,248,238,0.3); }
    .card-header h2 { font-size: 15px; font-weight: 700; color: var(--text-dark); }
    .count-badge { background: var(--pink); color: white; padding: 4px 10px; border-radius: 20px; font-size: 11px; font-weight: 700; }
    table { width: 100%; border-collapse: collapse; }
    th { padding: 12px 16px; font-size: 11px; font-weight: 700; color: var(--gray); text-transform: uppercase; letter-spacing: 0.5px; background: #FAFAF8; border-bottom: 1px solid #EDE0D4; text-align: left; }
    td { padding: 12px 16px; font-size: 13px; border-bottom: 1px solid rgba(237,224,212,0.5); vertical-align: middle; }
    tr:hover { background: #FFFBF5; }
    .banner-thumb { width: 80px; height: 48px; object-fit: cover; border-radius: 8px; border: 1px solid #EDE0D4; background: #f3f4f6; display: block; }
    .banner-thumb-placeholder { width: 80px; height: 48px; border-radius: 8px; background: #f3f4f6; border: 1px solid #EDE0D4; display: flex; align-items: center; justify-content: center; color: var(--gray); font-size: 18px; }
    .banner-title { font-weight: 700; color: var(--text-dark); }
    .banner-subtitle { font-size: 11px; color: var(--gray); margin-top: 2px; }
    .toggle-form { display: inline-flex; align-items: center; gap: 6px; }
    .toggle { position: relative; display: inline-block; width: 36px; height: 20px; }
    .toggle input { opacity: 0; width: 0; height: 0; }
    .toggle-slider { position: absolute; cursor: pointer; top: 0; left: 0; right: 0; bottom: 0; background: #D1D5DB; border-radius: 20px; transition: 0.3s; }
    .toggle-slider:before { position: absolute; content: ""; height: 14px; width: 14px; left: 3px; bottom: 3px; background: white; border-radius: 50%; transition: 0.3s; }
    input:checked + .toggle-slider { background: var(--pink); }
    input:checked + .toggle-slider:before { transform: translateX(16px); }
    .order-badge { display: inline-block; background: var(--cream); color: var(--brown-dark); padding: 3px 10px; border-radius: 6px; font-size: 11px; font-weight: 700; }
    .action-group { display: flex; gap: 6px; align-items: center; justify-content: flex-end; }
    .btn-edit { padding: 6px 12px; border-radius: 6px; font-size: 11px; font-weight: 600; background: #EFF6FF; color: #2563EB; border: none; cursor: pointer; font-family: 'Plus Jakarta Sans', sans-serif; text-decoration: none; display: inline-flex; align-items: center; gap: 4px; }
    .btn-edit:hover { opacity: 0.8; }
    .btn-delete { padding: 6px 12px; border-radius: 6px; font-size: 11px; font-weight: 600; background: #FEE2E2; color: #DC2626; border: none; cursor: pointer; font-family: 'Plus Jakarta Sans', sans-serif; display: inline-flex; align-items: center; gap: 4px; }
    .btn-delete:hover { opacity: 0.8; }
    .form-card { background: white; border-radius: 16px; border: 1px solid #EDE0D4; padding: 20px; height: fit-content; position: sticky; top: 96px; }
    .form-card h2 { font-size: 15px; font-weight: 700; color: var(--text-dark); margin-bottom: 16px; }
    .form-group { margin-bottom: 16px; }
    .form-label { display: block; font-size: 12px; font-weight: 700; color: var(--gray); margin-bottom: 6px; }
    .form-input { width: 100%; border: 1.5px solid var(--cream-dark); border-radius: 8px; padding: 10px 14px; font-size: 13px; outline: none; background: #FAFAF8; font-family: 'Plus Jakarta Sans', sans-serif; transition: border-color 0.2s; box-sizing: border-box; }
    .form-input:focus { border-color: var(--pink); background: white; }
    .btn-submit { width: 100%; background: var(--pink); color: white; font-weight: 700; font-size: 13px; padding: 10px; border-radius: 8px; border: none; cursor: pointer; font-family: 'Plus Jakarta Sans', sans-serif; box-shadow: 0 4px 12px rgba(240,80,122,0.2); transition: background 0.2s; }
    .btn-submit:hover { background: var(--pink-hover); }
    .empty-state { text-align: center; padding: 40px 20px; color: var(--gray); }
    .empty-icon { font-size: 40px; margin-bottom: 12px; }

    /* Edit Modal */
    .modal-overlay { display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.4); z-index: 1000; align-items: center; justify-content: center; }
    .modal-overlay.open { display: flex; }
    .modal-box { background: white; border-radius: 16px; padding: 24px; width: 420px; max-width: 90vw; max-height: 90vh; overflow-y: auto; }
    .modal-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; }
    .modal-header h3 { font-size: 15px; font-weight: 700; color: var(--text-dark); }
    .modal-close { background: none; border: none; cursor: pointer; font-size: 18px; color: var(--gray); }

    @media (max-width: 768px) { .banner-grid { grid-template-columns: 1fr; } }
</style>
@endpush

@section('content')
<div class="banner-grid">
    {{-- TABEL BANNER --}}
    <div class="card">
        <div class="card-header">
            <h2>Daftar Banner</h2>
            <span class="count-badge">{{ $banners->count() }} Banner</span>
        </div>
        <div style="overflow-x:auto;">
            <table>
                <thead>
                    <tr>
                        <th>Gambar</th>
                        <th>Judul</th>
                        <th style="text-align:center;">Urutan</th>
                        <th style="text-align:center;">Aktif</th>
                        <th style="text-align:right;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($banners as $banner)
                    <tr>
                        <td>
                            @if($banner->image)
                                <img src="{{ asset('storage/' . $banner->image) }}" alt="{{ $banner->title }}" class="banner-thumb">
                            @else
                                <div class="banner-thumb-placeholder"><i class="fas fa-image"></i></div>
                            @endif
                        </td>
                        <td>
                            <div class="banner-title">{{ $banner->title }}</div>
                            @if($banner->subtitle)
                                <div class="banner-subtitle">{{ Str::limit($banner->subtitle, 50) }}</div>
                            @endif
                            @if($banner->link)
                                <div class="banner-subtitle"><i class="fas fa-link"></i> {{ $banner->link }}</div>
                            @endif
                        </td>
                        <td style="text-align:center;"><span class="order-badge">{{ $banner->order }}</span></td>
                        <td style="text-align:center;">
                            <form method="POST" action="{{ route('admin.banners.update', $banner) }}" class="toggle-form">
                                @csrf @method('PUT')
                                <input type="hidden" name="title" value="{{ $banner->title }}">
                                <input type="hidden" name="subtitle" value="{{ $banner->subtitle }}">
                                <input type="hidden" name="link" value="{{ $banner->link }}">
                                <input type="hidden" name="order" value="{{ $banner->order }}">
                                <input type="hidden" name="is_active" value="0">
                                <label class="toggle" title="{{ $banner->is_active ? 'Nonaktifkan' : 'Aktifkan' }}">
                                    <input type="checkbox" name="is_active" value="1" {{ $banner->is_active ? 'checked' : '' }} onchange="this.closest('form').submit()">
                                    <span class="toggle-slider"></span>
                                </label>
                            </form>
                        </td>
                        <td>
                            <div class="action-group">
                                <button class="btn-edit" onclick="openEditModal({{ $banner->id }}, '{{ addslashes($banner->title) }}', '{{ addslashes($banner->subtitle) }}', '{{ addslashes($banner->link) }}', {{ $banner->order }})">
                                    <i class="fas fa-pen"></i> Edit
                                </button>
                                <form method="POST" action="{{ route('admin.banners.destroy', $banner) }}" onsubmit="return confirm('Hapus banner ini?')">
                                    @csrf @method('DELETE')
                                    <button class="btn-delete"><i class="fas fa-trash"></i> Hapus</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5">
                            <div class="empty-state">
                                <div class="empty-icon"><i class="fas fa-images" style="color:var(--pink)"></i></div>
                                <h3 style="font-size:14px;font-weight:700;color:var(--text-dark);margin-bottom:4px;">Belum Ada Banner</h3>
                                <p style="font-size:12px;">Silakan tambah banner baru di panel sebelah kanan.</p>
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
        <h2>Tambah Banner Baru</h2>
        <form method="POST" action="{{ route('admin.banners.store') }}" enctype="multipart/form-data">
            @csrf
            <div class="form-group">
                <label class="form-label">Judul Banner <span style="color:#EF4444;">*</span></label>
                <input type="text" name="title" required placeholder="Contoh: Promo Lebaran" class="form-input" value="{{ old('title') }}">
            </div>
            <div class="form-group">
                <label class="form-label">Subjudul</label>
                <input type="text" name="subtitle" placeholder="Deskripsi singkat banner" class="form-input" value="{{ old('subtitle') }}">
            </div>
            <div class="form-group">
                <label class="form-label">Gambar Banner</label>
                <input type="file" name="image" accept="image/*" class="form-input" style="padding:8px;">
            </div>
            <div class="form-group">
                <label class="form-label">Link (opsional)</label>
                <input type="text" name="link" placeholder="Contoh: /products" class="form-input" value="{{ old('link') }}">
            </div>
            <div class="form-group">
                <label class="form-label">Nomor Urutan</label>
                <input type="number" name="order" placeholder="1" min="1" class="form-input" value="{{ old('order', 1) }}">
            </div>
            <div class="form-group" style="display:flex;align-items:center;gap:10px;">
                <label class="toggle">
                    <input type="checkbox" name="is_active" value="1" checked>
                    <span class="toggle-slider"></span>
                </label>
                <span style="font-size:13px;font-weight:600;color:var(--text-dark);">Aktifkan banner</span>
            </div>
            <button type="submit" class="btn-submit"><i class="fas fa-plus" style="color:white"></i> Simpan Banner</button>
        </form>
    </div>
</div>

{{-- EDIT MODAL --}}
<div class="modal-overlay" id="editModal">
    <div class="modal-box">
        <div class="modal-header">
            <h3>Edit Banner</h3>
            <button class="modal-close" onclick="closeEditModal()"><i class="fas fa-times"></i></button>
        </div>
        <form method="POST" id="editForm" enctype="multipart/form-data">
            @csrf @method('PUT')
            <div class="form-group">
                <label class="form-label">Judul Banner <span style="color:#EF4444;">*</span></label>
                <input type="text" name="title" id="editTitle" required class="form-input">
            </div>
            <div class="form-group">
                <label class="form-label">Subjudul</label>
                <input type="text" name="subtitle" id="editSubtitle" class="form-input">
            </div>
            <div class="form-group">
                <label class="form-label">Ganti Gambar (opsional)</label>
                <input type="file" name="image" accept="image/*" class="form-input" style="padding:8px;">
            </div>
            <div class="form-group">
                <label class="form-label">Link</label>
                <input type="text" name="link" id="editLink" class="form-input">
            </div>
            <div class="form-group">
                <label class="form-label">Nomor Urutan</label>
                <input type="number" name="order" id="editOrder" min="1" class="form-input">
            </div>
            <button type="submit" class="btn-submit"><i class="fas fa-save" style="color:white"></i> Simpan Perubahan</button>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
function openEditModal(id, title, subtitle, link, order) {
    document.getElementById('editTitle').value = title;
    document.getElementById('editSubtitle').value = subtitle;
    document.getElementById('editLink').value = link;
    document.getElementById('editOrder').value = order;
    document.getElementById('editForm').action = '/admin/banners/' + id;
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

