@extends('admin.layout')
@section('title', 'Kelola Kustomisasi')
@section('page-title', 'Kelola Kustomisasi')
@section('page-subtitle', 'Tambah dan kelola opsi kustomisasi produk per kategori (rasa, ukuran, topping, dll.)')

@push('styles')
<style>
    .custom-grid { display: grid; grid-template-columns: 1fr 340px; gap: 24px; }
    .card { background: white; border-radius: 16px; border: 1px solid #EDE0D4; overflow: hidden; }
    .card-header { padding: 20px; border-bottom: 1px solid #EDE0D4; display: flex; justify-content: space-between; align-items: center; background: rgba(255,248,238,0.3); flex-wrap: wrap; gap: 12px; }
    .card-header h2 { font-size: 15px; font-weight: 700; color: var(--text-dark); }
    .count-badge { background: var(--pink); color: white; padding: 4px 10px; border-radius: 20px; font-size: 11px; font-weight: 700; }
    .filter-tabs { display: flex; gap: 6px; flex-wrap: wrap; padding: 12px 20px; border-bottom: 1px solid #EDE0D4; background: #FAFAF8; }
    .filter-tab { padding: 5px 12px; border-radius: 20px; font-size: 12px; font-weight: 600; border: 1.5px solid #EDE0D4; color: var(--gray); background: white; cursor: pointer; text-decoration: none; transition: all 0.2s; }
    .filter-tab:hover { border-color: var(--pink); color: var(--pink); }
    .filter-tab.active { background: var(--pink); color: white; border-color: var(--pink); }
    table { width: 100%; border-collapse: collapse; }
    th { padding: 12px 16px; font-size: 11px; font-weight: 700; color: var(--gray); text-transform: uppercase; letter-spacing: 0.5px; background: #FAFAF8; border-bottom: 1px solid #EDE0D4; text-align: left; }
    td { padding: 12px 16px; font-size: 13px; border-bottom: 1px solid rgba(237,224,212,0.5); vertical-align: middle; }
    tr:hover { background: #FFFBF5; }
    .category-badge { display: inline-block; padding: 3px 10px; border-radius: 20px; font-size: 11px; font-weight: 700; background: #EDE0D4; color: var(--brown-dark); }
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
    .btn-edit { padding: 6px 12px; border-radius: 6px; font-size: 11px; font-weight: 600; background: #EDE0D4; color: var(--brown-dark); border: none; cursor: pointer; font-family: 'Plus Jakarta Sans', sans-serif; display: inline-flex; align-items: center; gap: 4px; }
    .btn-edit:hover { opacity: 0.8; }
    .btn-delete { padding: 6px 12px; border-radius: 6px; font-size: 11px; font-weight: 600; background: #FEE2E2; color: #DC2626; border: none; cursor: pointer; font-family: 'Plus Jakarta Sans', sans-serif; display: inline-flex; align-items: center; gap: 4px; }
    .btn-delete:hover, .btn-toggle:hover { opacity: 0.8; }
    .modal-overlay { display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.4); z-index: 200; align-items: center; justify-content: center; padding: 20px; }
    .modal-overlay.open { display: flex; }
    .modal-box { background: white; border-radius: 20px; width: 100%; max-width: 480px; padding: 28px; box-shadow: 0 20px 60px rgba(0,0,0,0.2); }
    .modal-title { font-size: 16px; font-weight: 700; color: var(--text-dark); margin-bottom: 20px; display: flex; justify-content: space-between; align-items: center; }
    .modal-close { background: none; border: none; font-size: 20px; cursor: pointer; color: var(--gray); line-height: 1; }
    .modal-close:hover { color: var(--text-dark); }
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

@if(session('success'))
<div style="background:#DCFCE7;border:1px solid #A7F3D0;border-radius:10px;padding:12px 16px;font-size:13px;color:#065F46;margin-bottom:16px;">
    ✓ {{ session('success') }}
</div>
@endif

<div class="custom-grid">

    {{-- TABEL OPSI KUSTOMISASI --}}
    <div class="card">
        <div class="card-header">
            <h2>Daftar Opsi Kustomisasi</h2>
            <span class="count-badge">{{ $options->total() }} Opsi</span>
        </div>

        {{-- Filter Tabs per Kategori --}}
        <div class="filter-tabs">
            <a href="{{ route('admin.customizations.index') }}"
               class="filter-tab {{ !$selectedCategory ? 'active' : '' }}">
                Semua Kategori
            </a>
            @foreach($categories as $cat)
            <a href="{{ route('admin.customizations.index', ['category_id' => $cat->id]) }}"
               class="filter-tab {{ $selectedCategory == $cat->id ? 'active' : '' }}">
                {{ $cat->name }}
            </a>
            @endforeach
        </div>

        <div style="overflow-x:auto;">
            <table>
                <thead>
                    <tr>
                        <th>Kategori</th>
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
                            <span class="category-badge">
                                {{ $option->category->name ?? '—' }}
                            </span>
                        </td>
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
                                <button type="button" class="btn-edit"
                                    onclick="openEditModal({
                                        id: {{ $option->id }},
                                        category_id: {{ $option->category_id ?? 'null' }},
                                        type: '{{ $option->type }}',
                                        name: {{ Js::from($option->name) }},
                                        extra_price: {{ $option->extra_price }},
                                        sort_order: {{ $option->sort_order }},
                                        url: '{{ route('admin.customizations.update', $option) }}'
                                    })">
                                    <i class="fas fa-pencil-alt"></i> Edit
                                </button>
                                <form method="POST" action="{{ route('admin.customizations.toggle', $option) }}">
                                    @csrf
                                    <button type="submit" class="btn-toggle {{ $option->is_active ? 'btn-toggle-active' : 'btn-toggle-inactive' }}">
                                        {{ $option->is_active ? 'Nonaktifkan' : 'Aktifkan' }}
                                    </button>
                                </form>
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
                        <td colspan="7">
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
                <label class="form-label">Kategori Produk <span style="color:#EF4444;">*</span></label>
                <select name="category_id" required class="form-input">
                    <option value="">— Pilih Kategori —</option>
                    @foreach($categories as $cat)
                    <option value="{{ $cat->id }}"
                        {{ old('category_id', $selectedCategory) == $cat->id ? 'selected' : '' }}>
                        {{ $cat->name }}
                    </option>
                    @endforeach
                </select>
                @error('category_id')<p style="font-size:11px;color:#DC2626;margin-top:4px;">{{ $message }}</p>@enderror
            </div>

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
{{-- MODAL EDIT --}}
<div class="modal-overlay" id="modal-edit" onclick="if(event.target===this) closeEditModal()">
    <div class="modal-box">
        <div class="modal-title">
            <span><i class="fas fa-pencil-alt" style="color:var(--pink);margin-right:8px;"></i>Edit Opsi Kustomisasi</span>
            <button class="modal-close" onclick="closeEditModal()">&times;</button>
        </div>
        <form method="POST" id="edit-form" action="">
            @csrf @method('PUT')

            <div class="form-group">
                <label class="form-label">Kategori Produk <span style="color:#EF4444;">*</span></label>
                <select name="category_id" id="edit-category" required class="form-input">
                    <option value="">— Pilih Kategori —</option>
                    @foreach($categories as $cat)
                    <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="form-group">
                <label class="form-label">Tipe <span style="color:#EF4444;">*</span></label>
                <select name="type" id="edit-type" required class="form-input">
                    <option value="rasa">Rasa</option>
                    <option value="ukuran">Ukuran</option>
                    <option value="topping">Topping</option>
                    <option value="lainnya">Lainnya</option>
                </select>
            </div>

            <div class="form-group">
                <label class="form-label">Nama Opsi <span style="color:#EF4444;">*</span></label>
                <input type="text" name="name" id="edit-name" required class="form-input"
                       placeholder="Contoh: Coklat, 20cm, Oreo...">
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">Harga Tambahan (Rp)</label>
                    <input type="number" name="extra_price" id="edit-extra-price" min="0" step="500" class="form-input">
                </div>
                <div class="form-group">
                    <label class="form-label">Urutan Tampil</label>
                    <input type="number" name="sort_order" id="edit-sort-order" min="0" class="form-input">
                </div>
            </div>

            <div style="display:flex;gap:10px;margin-top:4px;">
                <button type="submit" class="btn-submit" style="flex:1;">
                    <i class="fas fa-save" style="color:white;margin-right:4px;"></i> Simpan Perubahan
                </button>
                <button type="button" onclick="closeEditModal()"
                    style="padding:10px 20px;border-radius:8px;border:none;background:#F3F4F6;font-size:13px;font-weight:600;cursor:pointer;font-family:'Plus Jakarta Sans',sans-serif;">
                    Batal
                </button>
            </div>
        </form>
    </div>
</div>

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
</script>
@endpush

@endsection
