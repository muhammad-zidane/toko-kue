@extends('admin.layout')
@section('title', 'Kelola Kategori')
@section('page-title', 'Kelola Kategori')
@section('page-subtitle', 'Tambah, lihat, dan hapus kategori produk')

@section('content')
<div class="grid grid-cols-1 lg:grid-cols-[2fr_1.2fr] gap-6">
    {{-- TABEL KATEGORI --}}
    <div class="bg-white rounded-2xl border border-cream-border overflow-hidden shadow-sm">
        <div class="px-5 py-4 border-b border-cream-border flex justify-between items-center bg-cream-warm/10">
            <h2 class="font-heading text-lg font-bold text-brown-dark">Daftar Kategori</h2>
            <span class="bg-primary text-white px-2.5 py-1 rounded-full text-xs font-bold">{{ $categories->count() }} Kategori</span>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr>
                        <th class="px-5 py-3 text-xs font-bold text-brown-light uppercase tracking-wider bg-cream/50 border-b border-cream-border" style="width:64px;">Gambar</th>
                        <th class="px-5 py-3 text-xs font-bold text-brown-light uppercase tracking-wider bg-cream/50 border-b border-cream-border">Nama Kategori</th>
                        <th class="px-5 py-3 text-xs font-bold text-brown-light uppercase tracking-wider bg-cream/50 border-b border-cream-border">Slug</th>
                        <th class="px-5 py-3 text-xs font-bold text-brown-light uppercase tracking-wider bg-cream/50 border-b border-cream-border text-center">Jumlah Produk</th>
                        <th class="px-5 py-3 text-xs font-bold text-brown-light uppercase tracking-wider bg-cream/50 border-b border-cream-border text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($categories as $cat)
                    <tr class="hover:bg-cream-warm/20 transition-colors">
                        <td class="px-5 py-4 text-sm border-b border-cream-border/50 align-middle">
                            @if($cat->image)
                                <img src="{{ Storage::url($cat->image) }}" alt="{{ $cat->name }}" class="w-11 h-11 rounded-lg object-cover">
                            @else
                                <div class="w-11 h-11 rounded-lg bg-cream flex items-center justify-center text-primary border border-cream-border"><i class="fas fa-tag"></i></div>
                            @endif
                        </td>
                        <td class="px-5 py-4 text-sm border-b border-cream-border/50 align-middle">
                            <div class="font-bold text-brown-dark text-sm">{{ $cat->name }}</div>
                            @if($cat->description)
                            <div class="text-xs text-text-secondary mt-0.5">{{ Str::limit($cat->description, 60) }}</div>
                            @endif
                        </td>
                        <td class="px-5 py-4 text-sm border-b border-cream-border/50 align-middle text-xs font-mono text-brown-light">/{{ $cat->slug }}</td>
                        <td class="px-5 py-4 text-sm border-b border-cream-border/50 align-middle text-center"><span class="inline-block px-2 py-0.5 rounded-full text-xs font-semibold bg-primary-light text-primary">{{ $cat->products_count }}</span></td>
                        <td class="px-5 py-4 text-sm border-b border-cream-border/50 align-middle text-right">
                            <div class="flex gap-2 justify-end items-center">
                                <button class="text-xs px-3 py-1.5 rounded-lg border border-primary text-primary hover:bg-primary hover:text-white transition-all cursor-pointer" onclick="openEditModal({{ $cat->id }}, '{{ addslashes($cat->name) }}', '{{ addslashes($cat->description ?? '') }}', '{{ $cat->image ? Storage::url($cat->image) : '' }}')">
                                    <i class="fas fa-pencil-alt"></i> Edit
                                </button>
                                <form method="POST" action="{{ route('admin.categories.destroy', $cat) }}" onsubmit="return confirm('Hapus kategori {{ $cat->name }}? Semua produk di kategori ini juga akan terhapus.')" class="m-0">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="text-xs px-3 py-1.5 rounded-lg border border-red-300 text-red-600 hover:bg-red-600 hover:text-white transition-all cursor-pointer"><i class="fas fa-trash"></i> Hapus</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-5 py-8 border-b border-cream-border/50 text-center">
                            <div class="py-6 text-center">
                                <div class="w-12 h-12 rounded-full bg-primary-light text-primary flex items-center justify-center mx-auto mb-3 text-lg"><i class="fas fa-tag"></i></div>
                                <h3 class="font-bold text-brown-dark text-sm mb-1">Belum Ada Kategori</h3>
                                <p class="text-xs text-text-secondary">Silakan tambah kategori baru di panel sebelah kanan.</p>
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
        <h2 class="font-heading text-lg font-bold text-brown-dark mb-4">Tambah Kategori Baru</h2>
        <form method="POST" action="{{ route('admin.categories.store') }}" enctype="multipart/form-data" class="m-0">
            @csrf
            <div class="mb-4">
                <label class="block text-xs font-bold text-text-secondary uppercase tracking-wider mb-1.5">Nama Kategori <span class="text-red-500">*</span></label>
                <input type="text" name="name" required placeholder="Contoh: Kue Kering" class="w-full border border-cream-border rounded-xl px-4 py-2.5 text-sm text-text-primary bg-white outline-none transition-all focus:border-primary focus:ring-2 focus:ring-primary/20" value="{{ old('name') }}">
            </div>
            <div class="mb-4">
                <label class="block text-xs font-bold text-text-secondary uppercase tracking-wider mb-1.5">Deskripsi Singkat</label>
                <textarea name="description" rows="3" placeholder="Deskripsi kategori..." class="w-full border border-cream-border rounded-xl px-4 py-2.5 text-sm text-text-primary bg-white outline-none transition-all focus:border-primary focus:ring-2 focus:ring-primary/20 resize-none">{{ old('description') }}</textarea>
            </div>
            <div class="mb-4">
                <label class="block text-xs font-bold text-text-secondary uppercase tracking-wider mb-1.5">Gambar Kategori</label>
                <div class="relative overflow-hidden inline-block w-full mb-2">
                    <input type="file" name="image" id="addImageInput" accept="image/jpg,image/jpeg,image/png,image/webp" onchange="previewImage(this, 'addPreview', 'addFileName')" class="absolute left-0 top-0 opacity-0 w-full h-full cursor-pointer">
                    <div class="flex items-center border border-cream-border rounded-xl bg-cream/30 overflow-hidden">
                        <span class="bg-cream-dark text-brown-mid px-4 py-2.5 text-xs font-bold shrink-0"><i class="fas fa-upload"></i> Pilih File</span>
                        <span class="px-3 text-xs text-text-muted truncate flex-1" id="addFileName">Belum ada file dipilih</span>
                    </div>
                </div>
                <img id="addPreview" src="" alt="Preview" class="max-w-full max-h-[120px] rounded-lg mt-2 hidden object-contain border border-cream-border bg-cream/10">
                @error('image')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
            </div>
            <button type="submit" class="w-full bg-primary text-white font-bold text-xs py-3 px-4 rounded-full shadow-gold hover:bg-primary-hover hover:-translate-y-0.5 transition-all duration-200 cursor-pointer flex items-center justify-center gap-1.5 border-0"><i class="fas fa-plus"></i> Simpan Kategori</button>
        </form>
    </div>
</div>

{{-- MODAL EDIT --}}
<div class="fixed inset-0 bg-brown-dark/60 backdrop-blur-sm z-[200] hidden [&.open]:flex items-center justify-center" id="editModal">
    <div class="bg-white rounded-3xl max-w-md w-full mx-4 shadow-lg p-6">
        <div class="flex justify-between items-center pb-4 border-b border-cream-border mb-4">
            <h3 class="font-heading text-lg font-bold text-brown-dark">Edit Kategori</h3>
            <button class="w-8 h-8 rounded-full hover:bg-cream-warm flex items-center justify-center text-brown-light hover:text-primary transition-all border-0 cursor-pointer" onclick="closeEditModal()"><i class="fas fa-times"></i></button>
        </div>
        <form method="POST" id="editForm" enctype="multipart/form-data" class="m-0">
            @csrf @method('PUT')
            <div class="mb-4">
                <label class="block text-xs font-bold text-text-secondary uppercase tracking-wider mb-1.5">Nama Kategori <span class="text-red-500">*</span></label>
                <input type="text" name="name" id="editName" required class="w-full border border-cream-border rounded-xl px-4 py-2.5 text-sm text-text-primary bg-white outline-none transition-all focus:border-primary focus:ring-2 focus:ring-primary/20">
            </div>
            <div class="mb-4">
                <label class="block text-xs font-bold text-text-secondary uppercase tracking-wider mb-1.5">Deskripsi Singkat</label>
                <textarea name="description" id="editDescription" rows="3" class="w-full border border-cream-border rounded-xl px-4 py-2.5 text-sm text-text-primary bg-white outline-none transition-all focus:border-primary focus:ring-2 focus:ring-primary/20 resize-none"></textarea>
            </div>
            <div class="mb-4">
                <label class="block text-xs font-bold text-text-secondary uppercase tracking-wider mb-1.5">Ganti Gambar <span class="font-normal text-text-muted">(opsional)</span></label>
                <div class="relative overflow-hidden inline-block w-full mb-2">
                    <input type="file" name="image" id="editImageInput" accept="image/jpg,image/jpeg,image/png,image/webp" onchange="previewImage(this, 'editPreview', 'editFileName')" class="absolute left-0 top-0 opacity-0 w-full h-full cursor-pointer">
                    <div class="flex items-center border border-cream-border rounded-xl bg-cream/30 overflow-hidden">
                        <span class="bg-cream-dark text-brown-mid px-4 py-2.5 text-xs font-bold shrink-0"><i class="fas fa-upload"></i> Pilih File</span>
                        <span class="px-3 text-xs text-text-muted truncate flex-1" id="editFileName">Belum ada file dipilih</span>
                    </div>
                </div>
                <img id="editPreview" src="" alt="Preview" class="max-w-full max-h-[120px] rounded-lg mt-2 hidden object-contain border border-cream-border bg-cream/10">
            </div>
            <div class="flex gap-2 mt-4">
                <button type="button" class="inline-flex items-center justify-center px-6 py-2.5 bg-cream-warm text-brown-mid border border-cream-border font-semibold text-xs rounded-full transition-all duration-200 hover:bg-cream-dark cursor-pointer" onclick="closeEditModal()">Batal</button>
                <button type="submit" class="flex-1 inline-flex items-center justify-center px-6 py-2.5 bg-primary text-white font-bold text-xs rounded-full shadow-gold transition-all duration-200 hover:bg-primary-hover border-0 cursor-pointer gap-1.5"><i class="fas fa-save"></i> Simpan</button>
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
        reader.onload = e => { preview.src = e.target.result; preview.style.display = 'block'; preview.classList.remove('hidden'); };
        reader.readAsDataURL(input.files[0]);
    } else {
        if (fileNameEl) fileNameEl.textContent = 'Belum ada file dipilih';
        preview.style.display = 'none';
        preview.classList.add('hidden');
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
        preview.classList.remove('hidden');
    } else {
        preview.src = '';
        preview.style.display = 'none';
        preview.classList.add('hidden');
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
