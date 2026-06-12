@extends('admin.layout')
@section('title', 'Kelola Kategori')
@section('page-title', 'Kelola Kategori')
@section('page-subtitle', 'Tambah, lihat, dan hapus kategori produk')

@push('styles')
<style>
    .cat-grid { display: grid; grid-template-columns: 1fr 350px; gap: 24px; }
    .card { background: white; border-radius: 16px; border: 1px solid #EDE0D4; overflow: hidden; }
    .card-header { padding: 20px; border-bottom: 1px solid #EDE0D4; display: flex; justify-content: space-between; align-items: center; background: rgba(255,248,238,0.3); }
    .card-header h2 { font-size: 15px; font-weight: 700; color: var(--text-dark); }
    .count-badge { background: var(--pink); color: white; padding: 4px 10px; border-radius: 20px; font-size: 11px; font-weight: 700; }
    table { width: 100%; border-collapse: collapse; }
    th { padding: 12px 16px; font-size: 11px; font-weight: 700; color: var(--gray); text-transform: uppercase; letter-spacing: 0.5px; background: #FAFAF8; border-bottom: 1px solid #EDE0D4; text-align: left; }
    td { padding: 14px 16px; font-size: 13px; border-bottom: 1px solid rgba(237,224,212,0.5); vertical-align: middle; }
    tr:hover { background: #FFFBF5; }
    .cat-img { width: 48px; height: 48px; border-radius: 10px; object-fit: cover; border: 1px solid #EDE0D4; display: block; }
    .cat-img-placeholder { width: 48px; height: 48px; border-radius: 10px; background: #F3F4F6; display: flex; align-items: center; justify-content: center; color: #D1D5DB; font-size: 18px; border: 1px solid #EDE0D4; }
    .cat-name { font-weight: 700; color: var(--text-dark); }
    .cat-desc { font-size: 11px; color: var(--gray); margin-top: 2px; }
    .cat-slug { font-size: 12px; color: var(--gray); font-family: monospace; background: rgba(243,244,246,0.5); padding: 2px 4px; border-radius: 4px; }
    .cat-count { display: inline-block; background: var(--cream); padding: 4px 10px; border-radius: 6px; font-size: 11px; font-weight: 700; color: var(--brown-dark); }
    .action-group { display: flex; justify-content: flex-end; gap: 4px; }
    .btn-delete { padding: 6px 12px; border-radius: 6px; font-size: 11px; font-weight: 600; background: #FEE2E2; color: #DC2626; border: none; cursor: pointer; font-family: 'Plus Jakarta Sans', sans-serif; display: inline-flex; align-items: center; gap: 4px; }
    .btn-delete:hover { opacity: 0.8; }
    .btn-edit { padding: 6px 12px; border-radius: 6px; font-size: 11px; font-weight: 600; background: #EDE9FE; color: #7C3AED; border: none; cursor: pointer; font-family: 'Plus Jakarta Sans', sans-serif; display: inline-flex; align-items: center; gap: 4px; }
    .btn-edit:hover { opacity: 0.8; }
    .form-card { background: white; border-radius: 16px; border: 1px solid #EDE0D4; padding: 20px; height: fit-content; position: sticky; top: 96px; }
    .form-card h2 { font-size: 15px; font-weight: 700; color: var(--text-dark); margin-bottom: 16px; }
    .form-group { margin-bottom: 16px; }
    .form-label { display: block; font-size: 12px; font-weight: 700; color: var(--gray); margin-bottom: 6px; }
    .form-input { width: 100%; border: 1.5px solid var(--cream-dark); border-radius: 8px; padding: 10px 14px; font-size: 13px; outline: none; background: #FAFAF8; font-family: 'Plus Jakarta Sans', sans-serif; transition: border-color 0.2s, background 0.2s; box-sizing: border-box; }
    .form-input:focus { border-color: var(--pink); background: white; }
    .form-textarea { resize: none; }
    .img-preview { display: none; margin-top: 8px; width: 100%; max-height: 120px; object-fit: cover; border-radius: 8px; border: 1px solid #EDE0D4; }
    .file-input-wrap { position: relative; }
    .file-input-wrap input[type="file"] { position: absolute; inset: 0; opacity: 0; width: 100%; cursor: pointer; z-index: 1; }
    .file-input-face { display: flex; align-items: center; gap: 10px; border: 1.5px solid var(--cream-dark); border-radius: 8px; padding: 10px 14px; font-size: 13px; background: #FAFAF8; font-family: 'Plus Jakarta Sans', sans-serif; transition: border-color 0.2s, background 0.2s; cursor: pointer; }
    .file-input-face:hover { border-color: var(--pink); background: white; }
    .file-input-face .file-btn { background: var(--pink); color: white; font-size: 11px; font-weight: 700; padding: 4px 10px; border-radius: 6px; white-space: nowrap; flex-shrink: 0; }
    .file-input-face .file-name { color: var(--gray); font-size: 12px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
    .btn-submit { width: 100%; background: var(--pink); color: white; font-weight: 700; font-size: 13px; padding: 10px; border-radius: 8px; border: none; cursor: pointer; font-family: 'Plus Jakarta Sans', sans-serif; box-shadow: 0 4px 12px rgba(240,80,122,0.2); transition: background 0.2s; }
    .btn-submit:hover { background: var(--pink-hover); }
    .empty-state { text-align: center; padding: 40px 20px; color: var(--gray); }
    .empty-icon { font-size: 40px; margin-bottom: 12px; }
    /* Modal */
    .modal-overlay { display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.4); z-index: 1000; align-items: center; justify-content: center; }
    .modal-overlay.open { display: flex; }
    .modal-box { background: white; border-radius: 16px; padding: 24px; width: 420px; max-width: 90vw; max-height: 90vh; overflow-y: auto; }
    .modal-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; }
    .modal-header h3 { font-size: 15px; font-weight: 700; color: var(--text-dark); }
    .modal-close { background: none; border: none; cursor: pointer; font-size: 18px; color: var(--gray); }
    .btn-cancel { flex: 1; padding: 10px; border-radius: 8px; font-size: 13px; font-weight: 600; background: #F3F4F6; color: var(--text-dark); border: none; cursor: pointer; font-family: 'Plus Jakarta Sans', sans-serif; }
    .modal-actions { display: flex; gap: 8px; margin-top: 4px; }
    @media (max-width: 768px) { .cat-grid { grid-template-columns: 1fr; } }
</style>
@endpush

@section('content')
<div class="cat-grid">
    {{-- TABEL KATEGORI --}}
    <div class="card">
        <div class="card-header">
            <h2>Daftar Kategori</h2>
            <span class="count-badge">{{ $categories->count() }} Kategori</span>
        </div>
        <div style="overflow-x:auto;">
            <table>
                <thead>
                    <tr>
                        <th style="width:64px;">Gambar</th>
                        <th>Nama Kategori</th>
                        <th>Slug</th>
                        <th style="text-align:center;">Jumlah Produk</th>
                        <th style="text-align:right;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($categories as $cat)
                    <tr>
                        <td>
                            @if($cat->image)
                                <img src="{{ Storage::url($cat->image) }}" alt="{{ $cat->name }}" class="cat-img">
                            @else
                                <div class="cat-img-placeholder"><i class="fas fa-tag"></i></div>
                            @endif
                        </td>
                        <td>
                            <div class="cat-name">{{ $cat->name }}</div>
                            @if($cat->description)
                            <div class="cat-desc">{{ Str::limit($cat->description, 60) }}</div>
                            @endif
                        </td>
                        <td><span class="cat-slug">/{{ $cat->slug }}</span></td>
                        <td style="text-align:center;"><span class="cat-count">{{ $cat->products_count }}</span></td>
                        <td style="text-align:right;">
                            <div class="action-group">
                                <button class="btn-edit" onclick="openEditModal({{ $cat->id }}, '{{ addslashes($cat->name) }}', '{{ addslashes($cat->description ?? '') }}', '{{ $cat->image ? Storage::url($cat->image) : '' }}')">
                                    <i class="fas fa-pencil-alt"></i> Edit
                                </button>
                                <form method="POST" action="{{ route('admin.categories.destroy', $cat) }}" onsubmit="return confirm('Hapus kategori {{ $cat->name }}? Semua produk di kategori ini juga akan terhapus.')">
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
                                <div class="empty-icon"><i class="fas fa-tag" style="color:var(--pink)"></i></div>
                                <h3 style="font-size:14px;font-weight:700;color:var(--text-dark);margin-bottom:4px;">Belum Ada Kategori</h3>
                                <p style="font-size:12px;">Silakan tambah kategori baru di panel sebelah kanan.</p>
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
        <h2>Tambah Kategori Baru</h2>
        <form method="POST" action="{{ route('admin.categories.store') }}" enctype="multipart/form-data">
            @csrf
            <div class="form-group">
                <label class="form-label">Nama Kategori <span style="color:#EF4444;">*</span></label>
                <input type="text" name="name" required placeholder="Contoh: Kue Kering" class="form-input" value="{{ old('name') }}">
            </div>
            <div class="form-group">
                <label class="form-label">Deskripsi Singkat</label>
                <textarea name="description" rows="3" placeholder="Deskripsi kategori..." class="form-input form-textarea">{{ old('description') }}</textarea>
            </div>
            <div class="form-group">
                <label class="form-label">Gambar Kategori</label>
                <div class="file-input-wrap">
                    <input type="file" name="image" id="addImageInput" accept="image/jpg,image/jpeg,image/png,image/webp" onchange="previewImage(this, 'addPreview', 'addFileName')">
                    <div class="file-input-face">
                        <span class="file-btn"><i class="fas fa-upload"></i> Pilih File</span>
                        <span class="file-name" id="addFileName">Belum ada file dipilih</span>
                    </div>
                </div>
                <img id="addPreview" src="" alt="Preview" class="img-preview">
                @error('image')<p style="color:#EF4444;font-size:11px;margin-top:4px;">{{ $message }}</p>@enderror
            </div>
            <button type="submit" class="btn-submit"><i class="fas fa-plus" style="color:white"></i> Simpan Kategori</button>
        </form>
    </div>
</div>

{{-- MODAL EDIT --}}
<div class="modal-overlay" id="editModal">
    <div class="modal-box">
        <div class="modal-header">
            <h3>Edit Kategori</h3>
            <button class="modal-close" onclick="closeEditModal()"><i class="fas fa-times"></i></button>
        </div>
        <form method="POST" id="editForm" enctype="multipart/form-data">
            @csrf @method('PUT')
            <div class="form-group">
                <label class="form-label">Nama Kategori <span style="color:#EF4444;">*</span></label>
                <input type="text" name="name" id="editName" required class="form-input">
            </div>
            <div class="form-group">
                <label class="form-label">Deskripsi Singkat</label>
                <textarea name="description" id="editDescription" rows="3" class="form-input form-textarea"></textarea>
            </div>
            <div class="form-group">
                <label class="form-label">Ganti Gambar <span style="font-weight:400;color:#9CA3AF;">(opsional)</span></label>
                <div class="file-input-wrap">
                    <input type="file" name="image" id="editImageInput" accept="image/jpg,image/jpeg,image/png,image/webp" onchange="previewImage(this, 'editPreview', 'editFileName')">
                    <div class="file-input-face">
                        <span class="file-btn"><i class="fas fa-upload"></i> Pilih File</span>
                        <span class="file-name" id="editFileName">Belum ada file dipilih</span>
                    </div>
                </div>
                <img id="editPreview" src="" alt="Preview" class="img-preview">
            </div>
            <div class="modal-actions">
                <button type="button" class="btn-cancel" onclick="closeEditModal()">Batal</button>
                <button type="submit" class="btn-submit" style="flex:1;box-shadow:none;"><i class="fas fa-save" style="color:white"></i> Simpan</button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
function previewImage(input, previewId, fileNameId) {
    const preview = document.getElementById(previewId);
    const fileNameEl = document.getElementById(fileNameId);
    if (input.files && input.files[0]) {
        if (fileNameEl) fileNameEl.textContent = input.files[0].name;
        const reader = new FileReader();
        reader.onload = e => { preview.src = e.target.result; preview.style.display = 'block'; };
        reader.readAsDataURL(input.files[0]);
    } else {
        if (fileNameEl) fileNameEl.textContent = 'Belum ada file dipilih';
        preview.style.display = 'none';
    }
}

function openEditModal(id, name, description, imageUrl) {
    document.getElementById('editName').value = name;
    document.getElementById('editDescription').value = description;
    document.getElementById('editForm').action = '/admin/categories/' + id;

    const preview = document.getElementById('editPreview');
    if (imageUrl) {
        preview.src = imageUrl;
        preview.style.display = 'block';
    } else {
        preview.src = '';
        preview.style.display = 'none';
    }

    const editFileInput = document.getElementById('editImageInput');
    editFileInput.value = '';
    document.getElementById('editFileName').textContent = 'Belum ada file dipilih';
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
