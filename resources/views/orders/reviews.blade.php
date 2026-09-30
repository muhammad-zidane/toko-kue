@extends('layouts.main')

@section('title', 'Jagoan Kue - Ulasan Pesanan')

@section('content')
<div class="bg-cream min-h-screen py-8 px-6">
    <div class="max-w-[1140px] mx-auto">
        <h1 class="font-heading text-3xl font-bold text-brown-dark mb-1">Ulasan Pesanan</h1>
        <p class="text-sm text-text-secondary mb-6">Kode: {{ $order->order_code }} · Beri rating dan ulasan untuk setiap produk.</p>

        @if (session('success'))
            <div class="bg-green-50 border border-green-200 text-green-700 rounded-xl p-4 text-xs font-semibold mb-6">
                {{ session('success') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="bg-red-50 border border-red-200 text-red-700 rounded-xl p-4 text-xs font-semibold mb-6">
                {{ $errors->first() }}
            </div>
        @endif

        @php
            $canReview = $order->status === 'completed' && ($order->payment?->status === 'paid');
            $reviewsByProduct = $order->productReviews->keyBy('product_id');
        @endphp

        @foreach ($order->orderItems as $item)
            @php
                $product = $item->product;
                $review = $product ? ($reviewsByProduct[$product->id] ?? null) : null;
            @endphp
            <div class="bg-white rounded-2xl border border-cream-border p-6 mb-6 shadow-sm">
                <div class="flex items-center gap-4 border-b border-cream-border pb-4 mb-6">
                    <img src="{{ $product && $product->image ? asset('storage/' . $product->image) : 'https://images.unsplash.com/photo-1563729784474-d77dbb933a9e?w=200&q=80' }}" alt="{{ $product->name ?? 'Produk' }}" class="w-16 h-16 rounded-xl object-cover shrink-0">
                    <div>
                        <p class="font-semibold text-sm text-brown-dark">{{ $product->name ?? 'Produk dihapus' }}</p>
                        <p class="text-xs text-text-secondary mt-0.5">{{ $item->quantity }}x · Rp {{ number_format($item->price, 0, ',', '.') }}</p>
                    </div>
                </div>

                @if (!$canReview)
                    <div class="bg-amber-50 border border-amber-200 text-amber-800 text-xs font-semibold rounded-xl p-4 text-center">Ulasan hanya bisa dikirim jika pesanan sudah selesai dan pembayaran sudah paid.</div>
                @elseif (!$product)
                    <div class="bg-red-50 border border-red-200 text-red-700 text-xs font-semibold rounded-xl p-4 text-center">Produk tidak tersedia untuk diulas.</div>
                @elseif ($review)
                    <div class="space-y-4">
                        <div class="bg-cream-warm/50 border border-cream-border rounded-xl p-4">
                            <div class="text-amber-400 text-sm mb-2">{{ str_repeat('★', (int) $review->rating) }}{{ str_repeat('☆', 5 - (int) $review->rating) }}</div>
                            <p class="text-sm text-brown-dark leading-relaxed">{{ $review->comment }}</p>
                            @if ($review->images->isNotEmpty())
                                <div class="flex gap-2 flex-wrap mt-3">
                                    @foreach ($review->images as $image)
                                        <img src="{{ asset('storage/' . $image->path) }}" alt="Gambar ulasan" class="w-16 h-16 rounded-xl object-cover border border-cream-border">
                                    @endforeach
                                </div>
                            @endif
                        </div>

                        <div class="flex items-center gap-3">
                            <button class="btn-secondary py-2 px-6 text-xs" type="button" onclick="toggleEdit('edit-{{ $review->id }}')">Edit</button>
                            <form class="inline-block" method="POST" action="{{ route('orders.reviews.destroy', ['order' => $order, 'review' => $review]) }}" onsubmit="return confirm('Hapus ulasan ini?')">
                                @csrf
                                @method('DELETE')
                                <button class="btn-danger py-2 px-6 text-xs" type="submit">Hapus Ulasan</button>
                            </form>
                        </div>
                    </div>

                    <div class="mt-6 border-t border-cream-border pt-6" id="edit-{{ $review->id }}" style="display:none;">
                        @if ($review->images->isNotEmpty())
                            <p class="text-xs font-bold text-brown-dark mb-3">Gambar Saat Ini</p>
                            <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 mb-6">
                                @foreach ($review->images as $image)
                                    <div class="bg-cream rounded-xl border border-cream-border p-2 flex flex-col items-center gap-2">
                                        <img src="{{ asset('storage/' . $image->path) }}" alt="Gambar ulasan" class="w-full h-20 rounded-lg object-cover">
                                        <form method="POST" action="{{ route('orders.reviews.images.destroy', ['order' => $order, 'review' => $review, 'image' => $image]) }}" onsubmit="return confirm('Hapus gambar ini?')">
                                            @csrf
                                            @method('DELETE')
                                            <button class="text-[10px] text-red-600 font-bold hover:underline" type="submit">Hapus Gambar</button>
                                        </form>
                                    </div>
                                @endforeach
                            </div>
                        @endif

                        <form method="POST" action="{{ route('orders.reviews.update', ['order' => $order, 'review' => $review]) }}" enctype="multipart/form-data" class="space-y-4">
                            @csrf
                            @method('PATCH')

                            <div class="stars mb-2">
                                <input type="radio" id="edit-star-{{ $review->id }}-5" name="rating" value="5" {{ (int) $review->rating === 5 ? 'checked' : '' }} required>
                                <label for="edit-star-{{ $review->id }}-5">★</label>
                                <input type="radio" id="edit-star-{{ $review->id }}-4" name="rating" value="4" {{ (int) $review->rating === 4 ? 'checked' : '' }}>
                                <label for="edit-star-{{ $review->id }}-4">★</label>
                                <input type="radio" id="edit-star-{{ $review->id }}-3" name="rating" value="3" {{ (int) $review->rating === 3 ? 'checked' : '' }}>
                                <label for="edit-star-{{ $review->id }}-3">★</label>
                                <input type="radio" id="edit-star-{{ $review->id }}-2" name="rating" value="2" {{ (int) $review->rating === 2 ? 'checked' : '' }}>
                                <label for="edit-star-{{ $review->id }}-2">★</label>
                                <input type="radio" id="edit-star-{{ $review->id }}-1" name="rating" value="1" {{ (int) $review->rating === 1 ? 'checked' : '' }}>
                                <label for="edit-star-{{ $review->id }}-1">★</label>
                            </div>

                            <div>
                                <label class="input-label">Ulasan Kamu</label>
                                <textarea name="comment" class="input-field h-24 resize-none" placeholder="Tulis ulasanmu di sini..." required>{{ $review->comment }}</textarea>
                            </div>

                            <div>
                                <p class="text-xs font-bold text-brown-dark mb-2">Tambah Gambar Baru (opsional)</p>
                                <input class="block w-full text-xs text-text-secondary file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-xs file:font-semibold file:bg-cream-warm file:text-brown-dark hover:file:bg-cream-dark cursor-pointer" type="file" name="images[]" multiple accept=".jpg,.jpeg,.png,.webp" onchange="previewFiles(this)">
                                <div class="preview flex gap-2 flex-wrap mt-3"></div>
                            </div>

                            <div class="flex items-center gap-3 pt-2">
                                <button class="btn-primary py-2 px-6 text-xs" type="submit">Simpan Perubahan</button>
                                <button class="btn-ghost py-2 px-6 text-xs" type="button" onclick="toggleEdit('edit-{{ $review->id }}')">Batal</button>
                            </div>
                        </form>
                    </div>
                @else
                    <form method="POST" action="{{ route('orders.reviews.store', ['order' => $order, 'product' => $product]) }}" enctype="multipart/form-data" class="space-y-4">
                        @csrf
                        <div class="stars mb-2">
                            <input type="radio" id="star-{{ $item->id }}-5" name="rating" value="5" required>
                            <label for="star-{{ $item->id }}-5">★</label>
                            <input type="radio" id="star-{{ $item->id }}-4" name="rating" value="4">
                            <label for="star-{{ $item->id }}-4">★</label>
                            <input type="radio" id="star-{{ $item->id }}-3" name="rating" value="3">
                            <label for="star-{{ $item->id }}-3">★</label>
                            <input type="radio" id="star-{{ $item->id }}-2" name="rating" value="2">
                            <label for="star-{{ $item->id }}-2">★</label>
                            <input type="radio" id="star-{{ $item->id }}-1" name="rating" value="1">
                            <label for="star-{{ $item->id }}-1">★</label>
                        </div>
                        <div>
                            <label class="input-label">Ulasan Kamu</label>
                            <textarea name="comment" class="input-field h-24 resize-none" placeholder="Tulis ulasanmu untuk produk ini..." required></textarea>
                        </div>
                        <div>
                            <p class="text-xs font-bold text-brown-dark mb-2">Tambah Gambar (opsional)</p>
                            <input class="block w-full text-xs text-text-secondary file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-xs file:font-semibold file:bg-cream-warm file:text-brown-dark hover:file:bg-cream-dark cursor-pointer" type="file" name="images[]" multiple accept=".jpg,.jpeg,.png,.webp" onchange="previewFiles(this)">
                            <div class="preview flex gap-2 flex-wrap mt-3"></div>
                        </div>
                        <button class="btn-primary py-2 px-6 text-xs mt-2" type="submit">Kirim Ulasan</button>
                    </form>
                @endif
            </div>
        @endforeach

        <div class="mt-6">
            <a href="{{ route('orders.show', $order) }}" class="btn-ghost py-2.5 px-6 text-xs">
                <i class="fa-solid fa-arrow-left mr-1.5"></i> Kembali ke Detail Pesanan
            </a>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    function toggleEdit(id) {
        const el = document.getElementById(id);
        if (!el) return;
        el.style.display = el.style.display === 'none' ? 'block' : 'none';
    }

    function previewFiles(input) {
        const box = input.parentElement.querySelector('.preview');
        box.innerHTML = '';

        if (!input.files) return;
        Array.from(input.files).forEach((file) => {
            const reader = new FileReader();
            reader.onload = function (e) {
                const img = document.createElement('img');
                img.src = e.target.result;
                img.className = 'w-16 h-16 rounded-xl object-cover border border-cream-border';
                box.appendChild(img);
            };
            reader.readAsDataURL(file);
        });
    }
</script>
<script src="{{ asset('js/app.js') }}" defer></script>
@endpush
