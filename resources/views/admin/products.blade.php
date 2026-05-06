@extends('admin.layout')
@section('title', 'Produk')
@section('page-title', 'Kelola Produk')
@section('page-subtitle', 'Lihat, tambah, edit, dan hapus produk')

@section('styles')
<style>
    .card { background: white; border-radius: 16px; border: 1px solid #EDE0D4; overflow: hidden; }
    .card-header { display: flex; align-items: center; justify-content: space-between; padding: 20px; border-bottom: 1px solid #EDE0D4; }
    .card-header h2 { font-size: 15px; font-weight: 700; color: var(--text-dark); }
    .btn-add { background: var(--pink); color: white; padding: 8px 16px; border-radius: 8px; font-size: 13px; font-weight: 600; transition: opacity 0.2s; }
    .btn-add:hover { opacity: 0.85; }
    table { width: 100%; border-collapse: collapse; }
    th { padding: 12px 16px; font-size: 11px; font-weight: 700; color: var(--gray); text-transform: uppercase; letter-spacing: 0.5px; background: #FAFAF8; border-bottom: 1px solid #EDE0D4; text-align: left; }
    td { padding: 12px 16px; font-size: 13px; border-bottom: 1px solid #F9F4EE; vertical-align: middle; }
    tr:hover { background: #FAFAF8; }
    .product-img { width: 48px; height: 48px; border-radius: 10px; object-fit: cover; background: var(--cream); display: flex; align-items: center; justify-content: center; overflow: hidden; }
    .product-img img { width: 100%; height: 100%; object-fit: cover; }
    .product-name { font-weight: 700; color: var(--text-dark); }
    .product-desc { font-size: 11px; color: var(--gray); margin-top: 2px; overflow: hidden; display: -webkit-box; -webkit-line-clamp: 1; -webkit-box-orient: vertical; }
    .cat-badge { background: var(--cream); padding: 3px 8px; border-radius: 6px; font-size: 11px; font-weight: 600; color: var(--brown-dark); }
    .stock-badge { padding: 3px 8px; border-radius: 6px; font-size: 11px; font-weight: 600; }
    .stock-ok { background: #DCFCE7; color: #16A34A; }
    .stock-low { background: #FEF3C7; color: #D97706; }
    .stock-out { background: #FEE2E2; color: #DC2626; }
    .actions { display: flex; gap: 6px; }
    .btn-edit { padding: 4px 10px; border-radius: 6px; font-size: 11px; font-weight: 600; background: #DBEAFE; color: #2563EB; }
    .btn-edit:hover { opacity: 0.8; }
    .btn-delete { padding: 4px 10px; border-radius: 6px; font-size: 11px; font-weight: 600; background: #FEE2E2; color: #DC2626; border: none; cursor: pointer; font-family: 'Plus Jakarta Sans', sans-serif; }
    .btn-delete:hover { opacity: 0.8; }
    .pagination { display: flex; justify-content: center; gap: 8px; padding: 16px; }
</style>
@endsection

@section('content')
<div class="card">
    <div class="card-header">
        <h2>Daftar Produk ({{ $products->total() }})</h2>
        <a href="{{ route('admin.products.create') }}" class="btn-add">➕ Tambah Produk</a>
    </div>
    <div style="overflow-x:auto;">
        <table>
            <thead>
                <tr>
                    <th>Produk</th>
                    <th>Kategori</th>
                    <th>Harga</th>
                    <th>Stok</th>
                    <th>Status</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($products as $product)
                <tr>
                    <td>
                        <div style="display:flex;align-items:center;gap:12px;">
                            <div class="product-img">
                                @if($product->image)
                                    <img src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->name }}">
                                @else
                                    <span style="font-size:20px;">🎂</span>
                                @endif
                            </div>
                            <div>
                                <div class="product-name">{{ $product->name }}</div>
                                <div class="product-desc">{{ $product->description }}</div>
                            </div>
                        </div>
                    </td>
                    <td><span class="cat-badge">{{ $product->category->name ?? '-' }}</span></td>
                    <td style="font-weight:700;">Rp {{ number_format($product->price, 0, ',', '.') }}</td>
                    <td>
                        @if($product->stock > 10)
                            <span class="stock-badge stock-ok">{{ $product->stock }}</span>
                        @elseif($product->stock > 0)
                            <span class="stock-badge stock-low">{{ $product->stock }}</span>
                        @else
                            <span class="stock-badge stock-out">Habis</span>
                        @endif
                    </td>
                    <td>
                        @if($product->is_available)
                            <span style="color:#16A34A;font-weight:600;font-size:12px;">● Aktif</span>
                        @else
                            <span style="color:#DC2626;font-weight:600;font-size:12px;">● Nonaktif</span>
                        @endif
                    </td>
                    <td>
                        <div class="actions">
                            <a href="{{ route('admin.products.edit', $product) }}" class="btn-edit">✏️ Edit</a>
                            <form method="POST" action="{{ route('admin.products.destroy', $product) }}" onsubmit="return confirm('Hapus produk {{ $product->name }}?')">
                                @csrf @method('DELETE')
                                <button class="btn-delete">🗑️</button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" style="text-align:center;padding:48px;">
                        <div style="font-size:48px;margin-bottom:12px;">🎂</div>
                        <h3 style="font-weight:700;color:var(--brown-dark);">Belum Ada Produk</h3>
                        <p style="font-size:14px;color:var(--gray);margin-bottom:16px;">Mulai tambahkan produk kue pertama Anda.</p>
                        <a href="{{ route('admin.products.create') }}" class="btn-add">➕ Tambah Produk</a>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="pagination">
        {{ $products->links('pagination::simple-bootstrap-5') }}
    </div>
</div>
@endsection
