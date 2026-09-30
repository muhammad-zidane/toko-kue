@extends('admin.layout')
@section('title', 'Produk')
@section('page-title', 'Kelola Produk')
@section('page-subtitle', 'Lihat, tambah, edit, dan hapus produk')

@section('content')
<div class="bg-white rounded-2xl border border-cream-border overflow-hidden shadow-sm">
    <div class="px-5 py-4 border-b border-cream-border flex justify-between items-center bg-cream-warm/10">
        <h2 class="font-heading text-lg font-bold text-brown-dark">Daftar Produk ({{ $products->total() }})</h2>
        <a href="{{ route('admin.products.create') }}" class="inline-flex items-center gap-1.5 px-4 py-2 bg-primary text-white font-bold text-xs rounded-full shadow-gold transition-all duration-200 hover:bg-primary-hover hover:-translate-y-0.5"><i class="fas fa-plus"></i> Tambah Produk</a>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr>
                    <th class="px-5 py-3 text-xs font-bold text-brown-light uppercase tracking-wider bg-cream/50 border-b border-cream-border">Produk</th>
                    <th class="px-5 py-3 text-xs font-bold text-brown-light uppercase tracking-wider bg-cream/50 border-b border-cream-border">Kategori</th>
                    <th class="px-5 py-3 text-xs font-bold text-brown-light uppercase tracking-wider bg-cream/50 border-b border-cream-border">Harga</th>
                    <th class="px-5 py-3 text-xs font-bold text-brown-light uppercase tracking-wider bg-cream/50 border-b border-cream-border">Stok</th>
                    <th class="px-5 py-3 text-xs font-bold text-brown-light uppercase tracking-wider bg-cream/50 border-b border-cream-border">Status</th>
                    <th class="px-5 py-3 text-xs font-bold text-brown-light uppercase tracking-wider bg-cream/50 border-b border-cream-border">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($products as $product)
                <tr class="hover:bg-cream-warm/20 transition-colors">
                    <td class="px-5 py-4 text-sm border-b border-cream-border/50 align-middle">
                        <div class="flex items-center gap-3">
                            <div class="w-12 h-12 rounded-lg bg-cream/40 border border-cream-border flex items-center justify-center overflow-hidden shrink-0">
                                @if($product->image)
                                    <img src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->name }}" class="w-full h-full object-cover">
                                @else
                                    <span class="text-xl text-primary"><i class="fas fa-birthday-cake"></i></span>
                                @endif
                            </div>
                            <div>
                                <div class="font-bold text-brown-dark text-sm">{{ $product->name }}</div>
                                <div class="text-xs text-text-secondary line-clamp-1 max-w-[240px]">{{ $product->description }}</div>
                            </div>
                        </div>
                    </td>
                    <td class="px-5 py-4 text-sm border-b border-cream-border/50 align-middle">
                        <span class="inline-block px-2.5 py-0.5 rounded-full text-xs font-semibold bg-cream-warm text-brown-mid border border-cream-border">{{ $product->category->name ?? '-' }}</span>
                    </td>
                    <td class="px-5 py-4 text-sm border-b border-cream-border/50 align-middle font-bold text-brown-dark">Rp {{ number_format($product->price, 0, ',', '.') }}</td>
                    <td class="px-5 py-4 text-sm border-b border-cream-border/50 align-middle">
                        @if($product->stock > 10)
                            <span class="inline-block px-2.5 py-0.5 rounded-full text-xs font-bold bg-green-50 text-green-700">{{ $product->stock }}</span>
                        @elseif($product->stock > 0)
                            <span class="inline-block px-2.5 py-0.5 rounded-full text-xs font-bold bg-amber-50 text-amber-700">{{ $product->stock }}</span>
                        @else
                            <span class="inline-block px-2.5 py-0.5 rounded-full text-xs font-bold bg-red-50 text-red-700">Habis</span>
                        @endif
                    </td>
                    <td class="px-5 py-4 text-sm border-b border-cream-border/50 align-middle">
                        @if($product->is_available)
                            <span class="inline-flex items-center gap-1.5 text-xs font-bold text-green-600">● Aktif</span>
                        @else
                            <span class="inline-flex items-center gap-1.5 text-xs font-bold text-red-600">● Nonaktif</span>
                        @endif
                    </td>
                    <td class="px-5 py-4 text-sm border-b border-cream-border/50 align-middle">
                        <div class="flex gap-2 items-center">
                            <a href="{{ route('admin.products.edit', $product) }}" class="text-xs px-3 py-1.5 rounded-lg border border-primary text-primary hover:bg-primary hover:text-white transition-all"><i class="fas fa-pen text-[10px] mr-1"></i> Edit</a>
                            <form method="POST" action="{{ route('admin.products.destroy', $product) }}" onsubmit="return confirm('Hapus produk {{ $product->name }}?')" class="m-0">
                                @csrf @method('DELETE')
                                <button type="submit" class="text-xs px-3 py-1.5 rounded-lg border border-red-300 text-red-600 hover:bg-red-600 hover:text-white transition-all cursor-pointer"><i class="fas fa-trash"></i></button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-5 py-12 text-center border-b border-cream-border/50">
                        <div class="w-20 h-20 rounded-full bg-primary-light text-primary text-3xl flex items-center justify-center mx-auto mb-4"><i class="fas fa-birthday-cake"></i></div>
                        <h3 class="font-semibold text-base text-brown-dark mb-1">Belum Ada Produk</h3>
                        <p class="text-sm text-text-secondary mb-4">Mulai tambahkan produk kue pertama Anda.</p>
                        <a href="{{ route('admin.products.create') }}" class="inline-flex items-center gap-1.5 px-4 py-2 bg-primary text-white font-bold text-xs rounded-full shadow-gold transition-all duration-200 hover:bg-primary-hover hover:-translate-y-0.5"><i class="fas fa-plus"></i> Tambah Produk</a>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="p-4 flex justify-center bg-white border-t border-cream-border">
        {{ $products->links('pagination::simple-bootstrap-5') }}
    </div>
</div>
@endsection
