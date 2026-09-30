<!DOCTYPE html>
<html lang="id">
<head>
    <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}" />
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Jagoan Kue - Tambah Produk</title>
    <!-- Google Fonts: Plus Jakarta Sans + Cormorant Garamond -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,400;0,600;0,700;1,400;1,600&family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <!-- FontAwesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <!-- Vite (Tailwind CSS) -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-cream text-text-primary font-sans antialiased min-h-screen flex flex-col">
<nav class="bg-brown-dark text-white py-4 px-6 shadow-md">
    <div class="max-w-3xl mx-auto flex items-center justify-between">
        <a href="{{ route('admin.dashboard') }}" class="font-heading text-xl font-bold text-primary hover:text-primary-hover transition-colors">Jagoan Kue — Admin</a>
        <a href="{{ route('admin.dashboard') }}" class="inline-flex items-center justify-center px-4 py-2 border border-white/20 text-white font-semibold text-xs rounded-full transition-all hover:bg-white/10">← Dashboard</a>
    </div>
</nav>
<div class="max-w-3xl mx-auto w-full py-10 px-6 flex-1">
    <h1 class="font-heading text-3xl font-bold text-brown-dark mb-6">Tambah Produk Baru</h1>
    @if($errors->any())
    <div class="bg-red-50 text-red-700 border border-red-200 rounded-xl p-4 text-sm mb-6">
        @foreach($errors->all() as $error)
        <p>{{ $error }}</p>
        @endforeach
    </div>
    @endif
    <div class="card p-8">
        <form method="POST" action="{{ route('admin.products.store') }}" enctype="multipart/form-data">
            @csrf
            <div class="mb-5">
                <label class="input-label">Nama Produk</label>
                <input type="text" name="name" class="input-field" value="{{ old('name') }}" required>
            </div>
            <div class="mb-5">
                <label class="input-label">Kategori</label>
                <select name="category_id" class="input-field cursor-pointer" required>
                    <option value="">— Pilih —</option>
                    @foreach($categories as $cat)
                    <option value="{{ $cat->id }}" {{ old('category_id')==$cat->id?'selected':'' }}>{{ $cat->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="mb-5">
                <label class="input-label">Deskripsi</label>
                <textarea name="description" class="input-field min-h-[100px] resize-y">{{ old('description') }}</textarea>
            </div>
            <div class="grid grid-cols-2 gap-4 mb-5">
                <div>
                    <label class="input-label">Harga (Rp)</label>
                    <input type="number" name="price" class="input-field" value="{{ old('price') }}" min="0" required>
                </div>
                <div>
                    <label class="input-label">Stok</label>
                    <input type="number" name="stock" class="input-field" value="{{ old('stock', 0) }}" min="0" required>
                </div>
            </div>
            <div class="mb-5">
                <label class="input-label">Badge Produk (opsional)</label>
                <select name="badge" class="input-field cursor-pointer">
                    <option value="">-- Tidak ada badge --</option>
                    <option value="best_seller" {{ old('badge') === 'best_seller' ? 'selected' : '' }}>Best Seller</option>
                    <option value="new"         {{ old('badge') === 'new'         ? 'selected' : '' }}>Baru</option>
                    <option value="sale"        {{ old('badge') === 'sale'        ? 'selected' : '' }}>Diskon</option>
                </select>
            </div>
            <div class="mb-5">
                <label class="input-label">Gambar Produk</label>
                <input type="file" name="image" class="w-full text-sm text-text-secondary file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-xs file:font-semibold file:bg-primary-light file:text-primary hover:file:bg-primary/20 cursor-pointer" accept="image/*">
                <small class="block text-xs text-text-muted mt-1.5">Max 2MB. Format: JPG, PNG.</small>
            </div>
            <div class="flex gap-3 justify-end mt-8">
                <a href="{{ route('admin.dashboard') }}" class="btn-ghost">Batal</a>
                <button type="submit" class="btn-primary">Simpan Produk</button>
            </div>
        </form>
    </div>
</div>
</body>
</html>
