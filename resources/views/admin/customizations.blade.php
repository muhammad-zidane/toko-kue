@extends('admin.layout')
@section('title', 'Kelola Kustomisasi')
@section('page-title', 'Kelola Kustomisasi')
@section('page-subtitle', 'Tambah dan kelola opsi kustomisasi produk per kategori (rasa, ukuran, topping, dll.)')

@section('content')

@if(session('success'))
<div class="bg-green-50 border border-green-200 text-green-700 rounded-2xl p-4 text-xs font-semibold mb-6 flex items-center gap-2">
    <i class="fas fa-check-circle text-sm text-green-600"></i>
    <span>{{ session('success') }}</span>
</div>
@endif

<div class="grid grid-cols-1 lg:grid-cols-[2fr_1.2fr] gap-6">

    {{-- TABEL OPSI KUSTOMISASI --}}
    <div class="bg-white rounded-2xl border border-cream-border overflow-hidden shadow-sm">
        <div class="px-5 py-4 border-b border-cream-border flex justify-between items-center bg-cream-warm/10">
            <h2 class="font-heading text-lg font-bold text-brown-dark">Daftar Opsi Kustomisasi</h2>
            <span class="bg-primary text-white px-2.5 py-1 rounded-full text-xs font-bold">{{ $options->total() }} Opsi</span>
        </div>

        {{-- Filter Tabs per Kategori --}}
        <div class="flex items-center gap-2 p-4 border-b border-cream-border bg-cream/10 overflow-x-auto whitespace-nowrap">
            <a href="{{ route('admin.customizations.index') }}"
               class="inline-block px-3.5 py-1.5 rounded-full text-xs font-bold transition-all {{ !$selectedCategory ? 'bg-primary text-white shadow-sm' : 'bg-cream border border-cream-border text-brown-mid hover:bg-cream-warm/40' }}">
                Semua Kategori
            </a>
            @foreach($categories as $cat)
            <a href="{{ route('admin.customizations.index', ['category_id' => $cat->id]) }}"
               class="inline-block px-3.5 py-1.5 rounded-full text-xs font-bold transition-all {{ $selectedCategory == $cat->id ? 'bg-primary text-white shadow-sm' : 'bg-cream border border-cream-border text-brown-mid hover:bg-cream-warm/40' }}">
                {{ $cat->name }}
            </a>
            @endforeach
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr>
                        <th class="admin-th">Kategori</th>
                        <th class="admin-th">Tipe</th>
                        <th class="admin-th">Nama Opsi</th>
                        <th class="admin-th">Harga Tambahan</th>
                        <th class="admin-th">Urutan</th>
                        <th class="admin-th">Status</th>
                        <th class="admin-th text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($options as $option)
                    <tr class="hover:bg-cream-warm/20 transition-colors">
                        <td class="px-5 py-4 text-sm border-b border-cream-border/50 align-middle">
                            <span class="inline-block px-2.5 py-0.5 rounded-full text-xs font-semibold bg-cream-warm text-brown-mid border border-cream-border">
                                {{ $option->category->name ?? '—' }}
                            </span>
                        </td>
                        <td class="admin-td">
                            <span class="inline-block px-2 py-0.5 rounded text-[10px] font-bold {{ $option->type_color_class }}">{{ $option->type_label }}</span>
                        </td>
                        <td class="px-5 py-4 text-sm border-b border-cream-border/50 align-middle font-bold text-brown-dark">{{ $option->name }}</td>
                        <td class="px-5 py-4 text-sm border-b border-cream-border/50 align-middle">
                            @if($option->extra_price > 0)
                                <span class="font-semibold text-primary">
                                    + Rp {{ number_format($option->extra_price, 0, ',', '.') }}
                                </span>
                            @else
                                <span class="text-xs text-text-muted">—</span>
                            @endif
                        </td>
                        <td class="px-5 py-4 text-sm border-b border-cream-border/50 align-middle text-text-secondary text-xs">{{ $option->sort_order }}</td>
                        <td class="px-5 py-4 text-sm border-b border-cream-border/50 align-middle">
                            @if($option->is_active)
                                <span class="inline-flex items-center justify-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-green-50 text-green-700">Aktif</span>
                            @else
                                <span class="inline-flex items-center justify-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-gray-100 text-gray-500">Nonaktif</span>
                            @endif
                        </td>
                        <td class="px-5 py-4 text-sm border-b border-cream-border/50 align-middle">
                            <div class="flex gap-2 justify-end items-center">
                                <button type="button" class="text-xs px-2.5 py-1.5 rounded-lg border border-primary text-primary hover:bg-primary hover:text-white transition-all cursor-pointer font-semibold"
                                    onclick="openEditModal({
                                        id: {{ $option->id }},
                                        category_id: {{ $option->category_id ?? 'null' }},
                                        type: '{{ $option->type }}',
                                        name: {{ Js::from($option->name) }},
                                        extra_price: {{ $option->extra_price }},
                                        sort_order: {{ $option->sort_order }},
                                        url: '{{ route('admin.customizations.update', $option) }}'
                                    })">
                                    <i class="fas fa-pencil-alt mr-1"></i> Edit
                                </button>
                                <form method="POST" action="{{ route('admin.customizations.toggle', $option) }}" class="m-0">
                                    @csrf
                                    <button type="submit" class="text-xs px-2.5 py-1.5 rounded-lg border font-semibold transition-all cursor-pointer @if($option->is_active) border-red-300 text-red-600 hover:bg-red-600 hover:text-white @else border-primary text-primary hover:bg-primary hover:text-white @endif">
                                        {{ $option->is_active ? 'Nonaktifkan' : 'Aktifkan' }}
                                    </button>
                                </form>
                                <form method="POST" action="{{ route('admin.customizations.destroy', $option) }}"
                                      onsubmit="return confirm('Hapus opsi kustomisasi &quot;{{ addslashes($option->name) }}&quot;?')" class="m-0">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="text-xs px-3 py-1.5 rounded-lg border border-red-300 text-red-600 hover:bg-red-600 hover:text-white transition-all cursor-pointer">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-5 py-8 border-b border-cream-border/50 text-center">
                            <div class="py-6 text-center">
                                <div class="w-12 h-12 rounded-full bg-primary-light text-primary flex items-center justify-center mx-auto mb-3 text-lg"><i class="fas fa-sliders-h"></i></div>
                                <h3 class="font-bold text-brown-dark text-sm mb-1">Belum Ada Opsi Kustomisasi</h3>
                                <p class="text-xs text-text-secondary">Tambahkan opsi kustomisasi produk di panel sebelah kanan.</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($options->hasPages())
        <div class="p-4 flex justify-end bg-white border-t border-cream-border">
            {{ $options->links() }}
        </div>
        @endif
    </div>

    {{-- FORM TAMBAH OPSI --}}
    <div class="bg-white rounded-2xl border border-cream-border p-5 shadow-sm h-fit lg:sticky lg:top-24">
        <h2 class="font-heading text-lg font-bold text-brown-dark mb-4 flex items-center gap-1.5"><i class="fas fa-plus-circle text-primary"></i> Tambah Opsi Baru</h2>
        <form method="POST" action="{{ route('admin.customizations.store') }}" class="m-0">
            @csrf

            <div class="mb-4">
                <label class="block text-xs font-bold text-text-secondary uppercase tracking-wider mb-1.5">Kategori Produk <span class="text-red-500">*</span></label>
                <select name="category_id" required class="w-full border border-cream-border rounded-xl px-4 py-2.5 text-sm text-text-primary bg-white outline-none transition-all focus:border-primary focus:ring-2 focus:ring-primary/20 cursor-pointer">
                    <option value="">— Pilih Kategori —</option>
                    @foreach($categories as $cat)
                    <option value="{{ $cat->id }}"
                        {{ old('category_id', $selectedCategory) == $cat->id ? 'selected' : '' }}>
                        {{ $cat->name }}
                    </option>
                    @endforeach
                </select>
                @error('category_id')<p class="text-[10px] text-red-600 mt-1 font-semibold">{{ $message }}</p>@enderror
            </div>

            <div class="mb-4">
                <label class="block text-xs font-bold text-text-secondary uppercase tracking-wider mb-1.5">Tipe <span class="text-red-500">*</span></label>
                <select name="type" required class="w-full border border-cream-border rounded-xl px-4 py-2.5 text-sm text-text-primary bg-white outline-none transition-all focus:border-primary focus:ring-2 focus:ring-primary/20 cursor-pointer">
                    <option value="">— Pilih Tipe —</option>
                    <option value="rasa"    {{ old('type') === 'rasa'    ? 'selected' : '' }}>Rasa</option>
                    <option value="ukuran"  {{ old('type') === 'ukuran'  ? 'selected' : '' }}>Ukuran</option>
                    <option value="topping" {{ old('type') === 'topping' ? 'selected' : '' }}>Topping</option>
                    <option value="lainnya" {{ old('type') === 'lainnya' ? 'selected' : '' }}>Lainnya</option>
                </select>
                @error('type')<p class="text-[10px] text-red-600 mt-1 font-semibold">{{ $message }}</p>@enderror
            </div>

            <div class="mb-4">
                <label class="block text-xs font-bold text-text-secondary uppercase tracking-wider mb-1.5">Nama Opsi <span class="text-red-500">*</span></label>
                <input type="text" name="name" required
                       placeholder="Contoh: Coklat, 20cm, Oreo..."
                       class="w-full border border-cream-border rounded-xl px-4 py-2.5 text-sm text-text-primary bg-white outline-none transition-all focus:border-primary focus:ring-2 focus:ring-primary/20" value="{{ old('name') }}">
                @error('name')<p class="text-[10px] text-red-600 mt-1 font-semibold">{{ $message }}</p>@enderror
            </div>

            <div class="grid grid-cols-2 gap-3 mb-4">
                <div>
                    <label class="block text-xs font-bold text-text-secondary uppercase tracking-wider mb-1.5">Harga Tambahan</label>
                    <input type="number" name="extra_price" min="0" step="500"
                           placeholder="0" class="w-full border border-cream-border rounded-xl px-4 py-2.5 text-sm text-text-primary bg-white outline-none transition-all focus:border-primary focus:ring-2 focus:ring-primary/20"
                           value="{{ old('extra_price', 0) }}">
                    @error('extra_price')<p class="text-[10px] text-red-600 mt-1 font-semibold">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-xs font-bold text-text-secondary uppercase tracking-wider mb-1.5">Urutan Tampil</label>
                    <input type="number" name="sort_order" min="0"
                           placeholder="0" class="w-full border border-cream-border rounded-xl px-4 py-2.5 text-sm text-text-primary bg-white outline-none transition-all focus:border-primary focus:ring-2 focus:ring-primary/20"
                           value="{{ old('sort_order', 0) }}">
                    @error('sort_order')<p class="text-[10px] text-red-600 mt-1 font-semibold">{{ $message }}</p>@enderror
                </div>
            </div>

            <button type="submit" class="w-full bg-primary text-white font-bold text-xs py-3 px-4 rounded-full shadow-gold hover:bg-primary-hover hover:-translate-y-0.5 transition-all duration-200 cursor-pointer flex items-center justify-center gap-1.5 border-0">
                <i class="fas fa-plus"></i> Tambah Opsi
            </button>
        </form>
    </div>

</div>

{{-- MODAL EDIT --}}
<div class="fixed inset-0 bg-brown-dark/60 backdrop-blur-sm z-[200] hidden [&.open]:flex items-center justify-center" id="modal-edit">
    <div class="bg-white rounded-3xl max-w-md w-full mx-4 shadow-lg p-6">
        <div class="flex justify-between items-center pb-4 border-b border-cream-border mb-4">
            <span class="font-heading text-lg font-bold text-brown-dark flex items-center gap-1.5"><i class="fas fa-pencil-alt text-primary"></i> Edit Opsi Kustomisasi</span>
            <button class="w-8 h-8 rounded-full hover:bg-cream-warm flex items-center justify-center text-brown-light hover:text-primary transition-all border-0 cursor-pointer text-xl" onclick="closeEditModal()">&times;</button>
        </div>
        <form method="POST" id="edit-form" action="" class="m-0">
            @csrf @method('PUT')

            <div class="mb-4">
                <label class="block text-xs font-bold text-text-secondary uppercase tracking-wider mb-1.5">Kategori Produk <span class="text-red-500">*</span></label>
                <select name="category_id" id="edit-category" required class="w-full border border-cream-border rounded-xl px-4 py-2.5 text-sm text-text-primary bg-white outline-none transition-all focus:border-primary focus:ring-2 focus:ring-primary/20 cursor-pointer">
                    <option value="">— Pilih Kategori —</option>
                    @foreach($categories as $cat)
                    <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="mb-4">
                <label class="block text-xs font-bold text-text-secondary uppercase tracking-wider mb-1.5">Tipe <span class="text-red-500">*</span></label>
                <select name="type" id="edit-type" required class="w-full border border-cream-border rounded-xl px-4 py-2.5 text-sm text-text-primary bg-white outline-none transition-all focus:border-primary focus:ring-2 focus:ring-primary/20 cursor-pointer">
                    <option value="rasa">Rasa</option>
                    <option value="ukuran">Ukuran</option>
                    <option value="topping">Topping</option>
                    <option value="lainnya">Lainnya</option>
                </select>
            </div>

            <div class="mb-4">
                <label class="block text-xs font-bold text-text-secondary uppercase tracking-wider mb-1.5">Nama Opsi <span class="text-red-500">*</span></label>
                <input type="text" name="name" id="edit-name" required class="w-full border border-cream-border rounded-xl px-4 py-2.5 text-sm text-text-primary bg-white outline-none transition-all focus:border-primary focus:ring-2 focus:ring-primary/20"
                       placeholder="Contoh: Coklat, 20cm, Oreo...">
            </div>

            <div class="grid grid-cols-2 gap-3 mb-4">
                <div>
                    <label class="block text-xs font-bold text-text-secondary uppercase tracking-wider mb-1.5">Harga Tambahan (Rp)</label>
                    <input type="number" name="extra_price" id="edit-extra-price" min="0" step="500" class="w-full border border-cream-border rounded-xl px-4 py-2.5 text-sm text-text-primary bg-white outline-none transition-all focus:border-primary focus:ring-2 focus:ring-primary/20">
                </div>
                <div>
                    <label class="block text-xs font-bold text-text-secondary uppercase tracking-wider mb-1.5">Urutan Tampil</label>
                    <input type="number" name="sort_order" id="edit-sort-order" min="0" class="w-full border border-cream-border rounded-xl px-4 py-2.5 text-sm text-text-primary bg-white outline-none transition-all focus:border-primary focus:ring-2 focus:ring-primary/20">
                </div>
            </div>

            <div class="flex gap-3 mt-4">
                <button type="submit" class="flex-1 bg-primary text-white font-bold text-xs py-3 px-4 rounded-full shadow-gold hover:bg-primary-hover hover:-translate-y-0.5 transition-all duration-200 cursor-pointer flex items-center justify-center gap-1.5 border-0">
                    <i class="fas fa-save"></i> Simpan Perubahan
                </button>
                <button type="button" onclick="closeEditModal()" class="px-5 py-2.5 rounded-full border border-cream-border text-brown-dark bg-cream/50 text-xs font-bold hover:bg-cream-warm transition-all cursor-pointer">
                    Batal
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
function openEditModal(data) {
    document.getElementById('edit-form').action = data.url;
    document.getElementById('edit-category').value = data.category_id ?? '';
    document.getElementById('edit-type').value = data.type;
    document.getElementById('edit-name').value = data.name;
    document.getElementById('edit-extra-price').value = data.extra_price;
    document.getElementById('edit-sort-order').value = data.sort_order;
    document.getElementById('modal-edit').classList.add('open');
    document.body.style.overflow = 'hidden';
}
function closeEditModal() {
    document.getElementById('modal-edit').classList.remove('open');
    document.body.style.overflow = '';
}
document.getElementById('modal-edit').addEventListener('click', function(e) {
    if (e.target === this) closeEditModal();
});
</script>
@endpush
