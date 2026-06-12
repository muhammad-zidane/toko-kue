<!DOCTYPE html>
<html lang="id">
<head>
    <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}" />
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Jagoan Kue - Edit Produk</title>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700;800&family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    <style>
        .navbar-inner { max-width: 800px; }
        .btn-back { border: 1.5px solid white; color: white; padding: 8px 18px; border-radius: 8px; font-size: 14px; font-weight: 600; }
        .page { max-width: 800px; margin: 0 auto; padding: 32px 24px 60px; }
        .page-title { font-family: 'Playfair Display', serif; font-size: 28px; font-weight: 700; margin-bottom: 24px; }
        .card { background: var(--white); border-radius: 16px; border: 1px solid #EDE0D4; padding: 32px; }
        .alert-error { background: #FEE2E2; border: 1px solid #FECACA; border-radius: 10px; padding: 12px 16px; font-size: 13px; color: #B91C1C; margin-bottom: 16px; }
        .form-group { margin-bottom: 20px; }
        .form-group label { display: block; font-size: 14px; font-weight: 600; margin-bottom: 6px; }
        .form-group input, .form-group select, .form-group textarea { width: 100%; border: 1.5px solid #D1C0B8; border-radius: 10px; padding: 12px 16px; font-size: 14px; font-family: 'Plus Jakarta Sans', sans-serif; outline: none; }
        .form-group input:focus, .form-group select:focus, .form-group textarea:focus { border-color: var(--pink); }
        .form-group textarea { resize: vertical; min-height: 100px; }
        .form-group small { font-size: 12px; color: var(--gray); margin-top: 4px; display: block; }
        .two-col { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; }
        .current-image { width: 120px; height: 120px; object-fit: cover; border-radius: 10px; margin-bottom: 8px; border: 1px solid #EDE0D4; }
        .btn-submit { background: var(--brown-dark); color: white; border: none; border-radius: 10px; padding: 14px 32px; font-size: 15px; font-weight: 700; cursor: pointer; font-family: 'Plus Jakarta Sans', sans-serif; }
        .btn-cancel { background: #F5EDD8; color: var(--text-dark); border: 1.5px solid #D1C0B8; border-radius: 10px; padding: 14px 24px; font-size: 15px; font-weight: 600; }
        .btn-delete { background: #FEE2E2; color: #DC2626; border: 1.5px solid #FECACA; border-radius: 10px; padding: 14px 24px; font-size: 15px; font-weight: 600; cursor: pointer; font-family: 'Plus Jakarta Sans', sans-serif; }
        .form-actions { display: flex; gap: 12px; justify-content: space-between; margin-top: 24px; }
        .form-actions-right { display: flex; gap: 12px; }
    </style>
</head>
<body>
<nav class="navbar"><div class="navbar-inner"><a href="{{ route('admin.dashboard') }}" class="navbar-logo">Jagoan Kue — Admin</a><a href="{{ route('admin.dashboard') }}" class="btn-back">← Dashboard</a></div></nav>
<div class="page">
    <h1 class="page-title">Edit Produk: {{ $product->name }}</h1>
    @if($errors->any())<div class="alert-error">@foreach($errors->all() as $error)<p>{{ $error }}</p>@endforeach</div>@endif
    <div class="card">
        <form id="product-edit-form" method="POST" action="{{ route('admin.products.update', $product) }}" enctype="multipart/form-data">
            @csrf @method('PUT')
            <div class="form-group"><label>Nama Produk</label><input type="text" name="name" value="{{ old('name', $product->name) }}" required></div>
            <div class="form-group"><label>Kategori</label><select name="category_id" required><option value="">— Pilih —</option>@foreach($categories as $cat)<option value="{{ $cat->id }}" {{ old('category_id', $product->category_id)==$cat->id?'selected':'' }}>{{ $cat->name }}</option>@endforeach</select></div>
            <div class="form-group"><label>Deskripsi</label><textarea name="description">{{ old('description', $product->description) }}</textarea></div>
            <div class="two-col">
                <div class="form-group"><label>Harga (Rp)</label><input type="number" name="price" value="{{ old('price', $product->price) }}" min="0" required></div>
                <div class="form-group"><label>Stok</label><input type="number" name="stock" value="{{ old('stock', $product->stock) }}" min="0" required></div>
            </div>
            <div class="form-group">
                <label>Badge Produk (opsional)</label>
                <select name="badge" style="width:100%;border:1px solid #ddd;border-radius:8px;padding:10px;font-size:14px;">
                    <option value="">-- Tidak ada badge --</option>
                    <option value="best_seller" {{ old('badge', $product->badge) === 'best_seller' ? 'selected' : '' }}>Best Seller</option>
                    <option value="new"         {{ old('badge', $product->badge) === 'new'         ? 'selected' : '' }}>Baru</option>
                    <option value="sale"        {{ old('badge', $product->badge) === 'sale'        ? 'selected' : '' }}>Diskon</option>
                </select>
            </div>
            <div class="form-group">
                <label>Gambar Produk</label>
                @if($product->image)<img src="{{ asset('storage/' . $product->image) }}" class="current-image" alt="Current"><small>Gambar saat ini. Upload baru untuk mengganti.</small>@endif
                <input type="file" name="image" accept="image/*" style="margin-top:8px;">
            </div>
        </form>
        <div class="form-actions">
            <form method="POST" action="{{ route('admin.products.destroy', $product) }}" onsubmit="return confirm('Yakin hapus produk ini?')">
                @csrf @method('DELETE')
                <button type="submit" class="btn-delete">Hapus Produk</button>
            </form>
            <div class="form-actions-right">
                <a href="{{ route('admin.dashboard') }}" class="btn-cancel">Batal</a>
                <button type="submit" form="product-edit-form" class="btn-submit">Simpan Perubahan</button>
            </div>
        </div>
    </div>
</div>
</body>
</html>
