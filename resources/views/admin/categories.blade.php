@extends('admin.layout')
@section('title', 'Kelola Kategori')
@section('page-title', 'Kelola Kategori')
@section('page-subtitle', 'Tambah, lihat, dan hapus kategori produk')

@section('styles')
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
    .cat-name { font-weight: 700; color: var(--text-dark); }
    .cat-desc { font-size: 11px; color: var(--gray); margin-top: 2px; }
    .cat-slug { font-size: 12px; color: var(--gray); font-family: monospace; background: rgba(243,244,246,0.5); padding: 2px 4px; border-radius: 4px; }
    .cat-count { display: inline-block; background: var(--cream); padding: 4px 10px; border-radius: 6px; font-size: 11px; font-weight: 700; color: var(--brown-dark); }
    .btn-delete { padding: 6px 12px; border-radius: 6px; font-size: 11px; font-weight: 600; background: #FEE2E2; color: #DC2626; border: none; cursor: pointer; font-family: 'Plus Jakarta Sans', sans-serif; }
    .btn-delete:hover { opacity: 0.8; }
    .form-card { background: white; border-radius: 16px; border: 1px solid #EDE0D4; padding: 20px; height: fit-content; position: sticky; top: 96px; }
    .form-card h2 { font-size: 15px; font-weight: 700; color: var(--text-dark); margin-bottom: 16px; }
    .form-group { margin-bottom: 16px; }
    .form-label { display: block; font-size: 12px; font-weight: 700; color: var(--gray); margin-bottom: 6px; }
    .form-input { width: 100%; border: 1.5px solid var(--cream-dark); border-radius: 8px; padding: 10px 14px; font-size: 13px; outline: none; background: #FAFAF8; font-family: 'Plus Jakarta Sans', sans-serif; transition: border-color 0.2s, background 0.2s; }
    .form-input:focus { border-color: var(--pink); background: white; }
    .form-textarea { resize: none; }
    .btn-submit { width: 100%; background: var(--pink); color: white; font-weight: 700; font-size: 13px; padding: 10px; border-radius: 8px; border: none; cursor: pointer; font-family: 'Plus Jakarta Sans', sans-serif; box-shadow: 0 4px 12px rgba(240,80,122,0.2); transition: background 0.2s; }
    .btn-submit:hover { background: var(--pink-hover); }
    .empty-state { text-align: center; padding: 40px 20px; color: var(--gray); }
    .empty-icon { font-size: 40px; margin-bottom: 12px; }
    @media (max-width: 768px) { .cat-grid { grid-template-columns: 1fr; } }
</style>
@endsection

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
                            <div class="cat-name">{{ $cat->name }}</div>
                            @if($cat->description)
                            <div class="cat-desc">{{ Str::limit($cat->description, 60) }}</div>
                            @endif
                        </td>
                        <td><span class="cat-slug">/{{ $cat->slug }}</span></td>
                        <td style="text-align:center;"><span class="cat-count">{{ $cat->products_count }}</span></td>
                        <td style="text-align:right;">
                            <form method="POST" action="{{ route('admin.categories.destroy', $cat) }}" onsubmit="return confirm('Hapus kategori {{ $cat->name }}? Semua produk di kategori ini juga akan terhapus.')">
                                @csrf @method('DELETE')
                                <button class="btn-delete">🗑️ Hapus</button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4">
                            <div class="empty-state">
                                <div class="empty-icon">🏷️</div>
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
        <form method="POST" action="{{ route('admin.categories.store') }}">
            @csrf
            <div class="form-group">
                <label class="form-label">Nama Kategori <span style="color:#EF4444;">*</span></label>
                <input type="text" name="name" required placeholder="Contoh: Kue Kering" class="form-input" value="{{ old('name') }}">
            </div>
            <div class="form-group">
                <label class="form-label">Deskripsi Singkat</label>
                <textarea name="description" rows="3" placeholder="Deskripsi kategori..." class="form-input form-textarea">{{ old('description') }}</textarea>
            </div>
            <button type="submit" class="btn-submit">➕ Simpan Kategori</button>
        </form>
    </div>
</div>
@endsection
