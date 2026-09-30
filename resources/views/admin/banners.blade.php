@extends('admin.layout')
@section('title', 'Kelola Banner')
@section('page-title', 'Kelola Banner')
@section('page-subtitle', 'Atur banner yang tampil di halaman utama')

@section('content')
<div class="grid grid-cols-1 lg:grid-cols-[2fr_1.2fr] gap-6">
    {{-- TABEL BANNER --}}
    <div class="bg-white rounded-2xl border border-cream-border overflow-hidden shadow-sm">
        <div class="px-5 py-4 border-b border-cream-border flex justify-between items-center bg-cream-warm/10">
            <h2 class="font-heading text-lg font-bold text-brown-dark">Daftar Banner</h2>
            <span class="bg-primary text-white px-2.5 py-1 rounded-full text-xs font-bold">{{ $banners->count() }} Banner</span>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr>
                        <th class="admin-th">Gambar</th>
                        <th class="admin-th">Judul</th>
                        <th class="admin-th text-center">Urutan</th>
                        <th class="admin-th text-center">Aktif</th>
                        <th class="admin-th text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($banners as $banner)
                    <tr class="hover:bg-cream-warm/20 transition-colors">
                        <td class="px-5 py-4 text-sm border-b border-cream-border/50 align-middle">
                            @if($banner->image)
                                <img src="{{ asset('storage/' . $banner->image) }}" alt="{{ $banner->title }}" class="w-[100px] h-[60px] rounded-lg object-cover">
                            @else
                                <div class="w-[100px] h-[60px] rounded-lg bg-cream flex items-center justify-center text-primary border border-cream-border"><i class="fas fa-image"></i></div>
                            @endif
                        </td>
                        <td class="px-5 py-4 text-sm border-b border-cream-border/50 align-middle">
                            <div class="font-bold text-brown-dark text-sm">{{ $banner->title }}</div>
                            @if($banner->subtitle)
                                <div class="text-xs text-text-secondary mt-0.5">{{ Str::limit($banner->subtitle, 50) }}</div>
                            @endif
                            @if($banner->link)
                                <div class="text-xs text-primary font-semibold mt-1"><i class="fas fa-link text-[10px] mr-1"></i> {{ $banner->link }}</div>
                            @endif
                        </td>
                        <td class="px-5 py-4 text-sm border-b border-cream-border/50 align-middle text-center">
                            <span class="inline-block px-2.5 py-1 rounded-lg bg-cream-warm text-brown-dark text-xs font-bold border border-cream-border">{{ $banner->order }}</span>
                        </td>
                        <td class="px-5 py-4 text-sm border-b border-cream-border/50 align-middle text-center">
                            <form method="POST" action="{{ route('admin.banners.update', $banner) }}" class="m-0">
                                @csrf @method('PUT')
                                <input type="hidden" name="title" value="{{ $banner->title }}">
                                <input type="hidden" name="subtitle" value="{{ $banner->subtitle }}">
                                <input type="hidden" name="link" value="{{ $banner->link }}">
                                <input type="hidden" name="order" value="{{ $banner->order }}">
                                <input type="hidden" name="is_active" value="0">
                                <label class="relative inline-flex items-center cursor-pointer" title="{{ $banner->is_active ? 'Nonaktifkan' : 'Aktifkan' }}">
                                    <input type="checkbox" name="is_active" value="1" {{ $banner->is_active ? 'checked' : '' }} onchange="this.closest('form').submit()" class="sr-only peer">
                                    <div class="w-11 h-6 bg-gray-200 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-primary"></div>
                                </label>
                            </form>
                        </td>
                        <td class="px-5 py-4 text-sm border-b border-cream-border/50 align-middle text-right">
                            <div class="flex gap-2 justify-end items-center">
                                <button type="button" class="text-xs px-3 py-1.5 rounded-lg border border-primary text-primary hover:bg-primary hover:text-white transition-all cursor-pointer"
                                    data-url="{{ route('admin.banners.update', $banner) }}"
                                    data-title="{{ $banner->title }}"
                                    data-subtitle="{{ $banner->subtitle }}"
                                    data-link="{{ $banner->link }}"
                                    data-order="{{ $banner->order }}"
                                    onclick="openEditModal(this.dataset)">
                                    <i class="fas fa-pen"></i> Edit
                                </button>
                                <form method="POST" action="{{ route('admin.banners.destroy', $banner) }}" onsubmit="return confirm('Hapus banner ini?')" class="m-0">
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
                                <div class="w-12 h-12 rounded-full bg-primary-light text-primary flex items-center justify-center mx-auto mb-3 text-lg"><i class="fas fa-images"></i></div>
                                <h3 class="font-bold text-brown-dark text-sm mb-1">Belum Ada Banner</h3>
                                <p class="text-xs text-text-secondary">Silakan tambah banner baru di panel sebelah kanan.</p>
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
        <h2 class="font-heading text-lg font-bold text-brown-dark mb-4">Tambah Banner Baru</h2>
        <form method="POST" action="{{ route('admin.banners.store') }}" enctype="multipart/form-data" class="m-0">
            @csrf
            <div class="mb-4">
                <label class="block text-xs font-bold text-text-secondary uppercase tracking-wider mb-1.5">Judul Banner <span class="text-red-500">*</span></label>
                <input type="text" name="title" required placeholder="Contoh: Promo Lebaran" class="w-full border border-cream-border rounded-xl px-4 py-2.5 text-sm text-text-primary bg-white outline-none transition-all focus:border-primary focus:ring-2 focus:ring-primary/20" value="{{ old('title') }}">
            </div>
            <div class="mb-4">
                <label class="block text-xs font-bold text-text-secondary uppercase tracking-wider mb-1.5">Subjudul</label>
                <input type="text" name="subtitle" placeholder="Deskripsi singkat banner" class="w-full border border-cream-border rounded-xl px-4 py-2.5 text-sm text-text-primary bg-white outline-none transition-all focus:border-primary focus:ring-2 focus:ring-primary/20" value="{{ old('subtitle') }}">
            </div>
            <div class="mb-4">
                <label class="block text-xs font-bold text-text-secondary uppercase tracking-wider mb-1.5">Gambar Banner</label>
                <input type="file" name="image" accept="image/*" class="w-full border border-cream-border rounded-xl px-4 py-2 text-sm text-text-primary bg-white outline-none transition-all focus:border-primary focus:ring-2 focus:ring-primary/20" onchange="previewImage(this, 'addPreview')">
                <img id="addPreview" src="" alt="Preview" class="hidden mt-2 w-full max-h-[120px] object-cover rounded-lg border border-cream-border">
            </div>
            <div class="mb-4">
                <label class="block text-xs font-bold text-text-secondary uppercase tracking-wider mb-1.5">Link (opsional)</label>
                <input type="text" name="link" placeholder="Contoh: /products" class="w-full border border-cream-border rounded-xl px-4 py-2.5 text-sm text-text-primary bg-white outline-none transition-all focus:border-primary focus:ring-2 focus:ring-primary/20" value="{{ old('link') }}">
            </div>
            <div class="mb-4">
                <label class="block text-xs font-bold text-text-secondary uppercase tracking-wider mb-1.5">Nomor Urutan</label>
                <input type="number" name="order" placeholder="1" min="1" class="w-full border border-cream-border rounded-xl px-4 py-2.5 text-sm text-text-primary bg-white outline-none transition-all focus:border-primary focus:ring-2 focus:ring-primary/20" value="{{ old('order', 1) }}">
            </div>
            <div class="mb-4 flex items-center gap-3">
                <label class="relative inline-flex items-center cursor-pointer">
                    <input type="checkbox" name="is_active" value="1" checked class="sr-only peer">
                    <div class="w-11 h-6 bg-gray-200 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-primary"></div>
                </label>
                <span class="text-xs font-bold text-brown-dark">Aktifkan banner</span>
            </div>
            <button type="submit" class="w-full bg-primary text-white font-bold text-xs py-3 px-4 rounded-full shadow-gold hover:bg-primary-hover hover:-translate-y-0.5 transition-all duration-200 cursor-pointer flex items-center justify-center gap-1.5 border-0"><i class="fas fa-plus"></i> Simpan Banner</button>
        </form>
    </div>
</div>

{{-- EDIT MODAL --}}
<div class="fixed inset-0 bg-brown-dark/60 backdrop-blur-sm z-[200] hidden [&.open]:flex items-center justify-center" id="editModal">
    <div class="bg-white rounded-3xl max-w-md w-full mx-4 shadow-lg p-6">
        <div class="flex justify-between items-center pb-4 border-b border-cream-border mb-4">
            <h3 class="font-heading text-lg font-bold text-brown-dark">Edit Banner</h3>
            <button class="w-8 h-8 rounded-full hover:bg-cream-warm flex items-center justify-center text-brown-light hover:text-primary transition-all border-0 cursor-pointer" onclick="closeEditModal()"><i class="fas fa-times"></i></button>
        </div>
        <form method="POST" id="editForm" enctype="multipart/form-data" class="m-0">
            @csrf @method('PUT')
            <div class="mb-4">
                <label class="block text-xs font-bold text-text-secondary uppercase tracking-wider mb-1.5">Judul Banner <span class="text-red-500">*</span></label>
                <input type="text" name="title" id="editTitle" required class="w-full border border-cream-border rounded-xl px-4 py-2.5 text-sm text-text-primary bg-white outline-none transition-all focus:border-primary focus:ring-2 focus:ring-primary/20">
            </div>
            <div class="mb-4">
                <label class="block text-xs font-bold text-text-secondary uppercase tracking-wider mb-1.5">Subjudul</label>
                <input type="text" name="subtitle" id="editSubtitle" class="w-full border border-cream-border rounded-xl px-4 py-2.5 text-sm text-text-primary bg-white outline-none transition-all focus:border-primary focus:ring-2 focus:ring-primary/20">
            </div>
            <div class="mb-4">
                <label class="block text-xs font-bold text-text-secondary uppercase tracking-wider mb-1.5">Ganti Gambar (opsional)</label>
                <input type="file" name="image" accept="image/*" class="w-full border border-cream-border rounded-xl px-4 py-2 text-sm text-text-primary bg-white outline-none transition-all focus:border-primary focus:ring-2 focus:ring-primary/20" onchange="previewImage(this, 'editPreview')">
                <img id="editPreview" src="" alt="Preview" class="hidden mt-2 w-full max-h-[120px] object-cover rounded-lg border border-cream-border">
            </div>
            <div class="mb-4">
                <label class="block text-xs font-bold text-text-secondary uppercase tracking-wider mb-1.5">Link</label>
                <input type="text" name="link" id="editLink" class="w-full border border-cream-border rounded-xl px-4 py-2.5 text-sm text-text-primary bg-white outline-none transition-all focus:border-primary focus:ring-2 focus:ring-primary/20">
            </div>
            <div class="mb-4">
                <label class="block text-xs font-bold text-text-secondary uppercase tracking-wider mb-1.5">Nomor Urutan</label>
                <input type="number" name="order" id="editOrder" min="1" class="w-full border border-cream-border rounded-xl px-4 py-2.5 text-sm text-text-primary bg-white outline-none transition-all focus:border-primary focus:ring-2 focus:ring-primary/20">
            </div>
            <button type="submit" class="w-full bg-primary text-white font-bold text-xs py-3 px-4 rounded-full shadow-gold hover:bg-primary-hover hover:-translate-y-0.5 transition-all duration-200 cursor-pointer flex items-center justify-center gap-1.5 border-0"><i class="fas fa-save"></i> Simpan Perubahan</button>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
function previewImage(input, previewId) {
    const preview = document.getElementById(previewId);
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = e => { preview.src = e.target.result; preview.style.display = 'block'; preview.classList.remove('hidden'); };
        reader.readAsDataURL(input.files[0]);
    } else {
        preview.style.display = 'none';
        preview.classList.add('hidden');
    }
}

function openEditModal(data) {
    document.getElementById('editTitle').value = data.title || '';
    document.getElementById('editSubtitle').value = data.subtitle || '';
    document.getElementById('editLink').value = data.link || '';
    document.getElementById('editOrder').value = data.order || '';
    document.getElementById('editForm').action = data.url;
    document.getElementById('editModal').classList.add('open');
}
function closeEditModal() {
    document.getElementById('editModal').classList.remove('open');
    document.getElementById('editPreview').style.display = 'none';
    document.getElementById('editPreview').classList.add('hidden');
}
document.getElementById('editModal').addEventListener('click', function(e) {
    if (e.target === this) closeEditModal();
});
</script>
@endpush
