@extends('layouts.main')

@section('title', 'Jagoan Kue — Kue Lezat Dikirim ke Pintumu')

@section('content')
        {{-- BANNER SLIDESHOW (dari database) --}}
        @if($banners->isNotEmpty())
        <div id="bannerSlider" class="relative overflow-hidden bg-cream">
            @foreach($banners as $i => $banner)
            <div class="banner-slide" style="display:{{ $i === 0 ? 'block' : 'none' }}; position: relative;">
                <a href="{{ $banner->link ?? '#' }}">
                    @if($banner->image)
                        <img src="{{ asset('storage/' . $banner->image) }}"
                             alt="{{ $banner->title }}"
                             class="w-full max-h-[480px] object-cover block" loading="lazy">
                    @else
                        <div class="w-full h-[480px] bg-gradient-to-br from-cream to-cream-warm flex items-center justify-center">
                        </div>
                    @endif
                </a>
                @if($banner->title)
                <div class="absolute bottom-0 inset-x-0 bg-gradient-to-t from-black/60 to-transparent px-8 py-6">
                    <h2 class="text-white font-heading text-2xl md:text-3xl font-extrabold mb-1">{{ $banner->title }}</h2>
                    @if($banner->subtitle)<p class="text-white/90 text-sm">{{ $banner->subtitle }}</p>@endif
                </div>
                @endif
            </div>
            @endforeach

            @if($banners->count() > 1)
            <button onclick="slideBanner(-1)" aria-label="Slide sebelumnya" class="absolute left-4 top-1/2 -translate-y-1/2 bg-white/90 rounded-full w-10 h-10 flex items-center justify-center shadow-md hover:bg-white transition-all text-brown-dark font-bold text-lg">‹</button>
            <button onclick="slideBanner(1)"  aria-label="Slide berikutnya" class="absolute right-4 top-1/2 -translate-y-1/2 bg-white/90 rounded-full w-10 h-10 flex items-center justify-center shadow-md hover:bg-white transition-all text-brown-dark font-bold text-lg">›</button>
            <div class="absolute bottom-4 left-1/2 -translate-x-1/2 flex gap-1.5" id="bannerDots">
                @foreach($banners as $i => $banner)
                <div class="banner-dot {{ $i === 0 ? 'w-3 bg-white' : 'w-2 bg-white/50' }} h-2 rounded-full cursor-pointer transition-all duration-200" onclick="goToBanner({{ $i }})"></div>
                @endforeach
            </div>
            @endif
        </div>
        @endif

        {{-- HERO --}}
        <section class="bg-gradient-to-br from-cream to-cream-warm py-20 px-6">
            <div class="max-w-[1140px] mx-auto flex flex-col md:flex-row items-center justify-between gap-10">
                <div class="flex-1 max-w-[520px]">
                    <h1 class="font-heading text-5xl font-bold leading-tight text-brown-dark mb-4">
                        Kue Lezat, Dikirim<br>
                        Hangat ke <span class="text-primary relative after:content-[''] after:absolute after:-bottom-1 after:left-0 after:w-full after:h-1.5 after:bg-primary/20 after:rounded-full">Pintumu</span>
                    </h1>
                    <p class="text-base text-text-secondary leading-relaxed mb-9 max-w-md">
                        Menyediakan Bermacam-macam kue yang<br>dibuat dengan cinta
                    </p>
                    <div class="flex gap-4">
                        <a href="/products" class="btn-primary">Katalog</a>
                        <a href="/orders" class="btn-secondary">Pesanan Saya</a>
                    </div>
                </div>
                <div class="flex-1 flex justify-center">
                    <img src="https://images.unsplash.com/photo-1578985545062-69928b1d9587?w=600&q=80" alt="Kue Lezat" class="w-[380px] h-[380px] object-cover rounded-3xl shadow-lg border-4 border-white" loading="lazy">
                </div>
            </div>
        </section>

        {{-- KATEGORI --}}
        <section class="py-20 px-6 bg-white">
            <div class="max-w-[1140px] mx-auto">
                <h2 class="section-title">Jelajahi Kategori</h2>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 mt-10">
                    @forelse($categories as $category)
                    <a href="/products?category={{ $category->slug }}" class="block rounded-2xl overflow-hidden border border-cream-border shadow-sm hover:-translate-y-1 hover:shadow-md transition-all duration-200">
                        <img src="{{ $category->image ? Storage::url($category->image) : 'https://images.unsplash.com/photo-1563729784474-d77dbb933a9e?w=600&q=80' }}" alt="{{ $category->name }}" class="w-full h-52 object-cover">
                        <div class="bg-cream-warm px-5 py-4">
                            <h3 class="font-semibold text-base text-brown-dark">{{ $category->name }}</h3>
                            <span class="inline-block mt-1 bg-primary-light text-primary text-xs font-bold px-2 py-0.5 rounded-full">
                                {{ $category->products_count ?? 0 }} Produk
                            </span>
                        </div>
                    </a>
                    @empty
                    <p class="sm:col-span-2 text-center text-text-muted">Belum ada kategori.</p>
                    @endforelse
                </div>
            </div>
        </section>

        {{-- PRODUK UNGGULAN --}}
        <section class="py-20 px-6 bg-cream">
            <div class="max-w-[1140px] mx-auto">
                <h2 class="section-title">Produk Unggulan</h2>
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 mt-10">
                    @forelse($featuredProducts as $product)
                    <div class="card card-hover relative overflow-hidden group">
                        <div class="absolute top-3 left-3 bg-amber-100 text-amber-700 text-[11px] font-bold px-2.5 py-1 rounded-full z-10">Unggulan</div>
                        <div class="overflow-hidden">
                            <img src="{{ $product->image ? asset('storage/' . $product->image) : 'https://images.unsplash.com/photo-1563729784474-d77dbb933a9e?w=600&q=80' }}"
                                 alt="{{ $product->name }}"
                                 class="w-full h-52 object-cover group-hover:scale-105 transition-transform duration-300">
                        </div>
                        <div class="p-5">
                            <h3 class="font-bold text-base text-brown-dark mb-1.5">{{ $product->name }}</h3>
                            <p class="text-sm text-text-secondary line-clamp-2 mb-4 leading-relaxed">{{ $product->description }}</p>
                            <div class="flex items-center justify-between">
                                <span class="text-base font-extrabold text-primary">Rp {{ number_format($product->price, 0, ',', '.') }}</span>
                                <a href="/products/{{ $product->slug }}" class="text-sm font-semibold text-brown-mid hover:text-primary transition-colors">Pesan Sekarang →</a>
                            </div>
                        </div>
                    </div>
                    @empty
                    <p class="lg:col-span-3 sm:col-span-2 text-center text-text-muted">Belum ada produk.</p>
                    @endforelse
                </div>
            </div>
        </section>

        {{-- TESTIMONI --}}
        <section class="py-20 px-6 bg-white">
            <div class="max-w-[1140px] mx-auto">
                <h2 class="section-title">Testimoni</h2>
                <p class="section-subtitle">Yang orang-orang rasakan.</p>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mt-10">
                    @forelse($testimonials as $t)
                    <div class="bg-cream rounded-2xl p-6 border border-cream-border relative overflow-hidden">
                        <span class="absolute top-3 right-4 text-7xl font-heading text-cream-dark opacity-60 leading-none select-none">"</span>
                        <div class="flex gap-0.5 text-amber-400 text-sm mb-3">
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                        </div>
                        <p class="text-sm text-text-secondary leading-relaxed mb-5 relative z-10">"{{ $t->comment ?? '-' }}"</p>
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-full bg-gradient-to-br from-primary to-primary-hover text-white text-sm font-bold flex items-center justify-center">
                                {{ strtoupper(substr($t->user->name ?? 'U', 0, 1)) }}
                            </div>
                            <div>
                                <p class="text-sm font-bold text-brown-dark">{{ $t->user->name ?? 'Pelanggan' }}</p>
                                <p class="text-xs text-text-muted mt-0.5">
                                    {{ $t->product ? 'Ulasan untuk ' . $t->product->name : 'Ulasan Produk' }}
                                </p>
                            </div>
                        </div>
                    </div>
                    @empty
                    <p class="md:col-span-3 text-center text-text-muted">Belum ada testimoni.</p>
                    @endforelse
                </div>
            </div>
        </section>
@endsection

@push('scripts')
    @if($banners->isNotEmpty())
        <script>
        let bannerIdx = 0;
        const slides = document.querySelectorAll('.banner-slide');
        const dots   = document.querySelectorAll('.banner-dot');
        function goToBanner(n) {
            slides[bannerIdx].style.display = 'none';
            if (dots[bannerIdx]) {
                dots[bannerIdx].classList.remove('bg-white', 'w-3');
                dots[bannerIdx].classList.add('bg-white/50', 'w-2');
            }
            bannerIdx = (n + slides.length) % slides.length;
            slides[bannerIdx].style.display = 'block';
            if (dots[bannerIdx]) {
                dots[bannerIdx].classList.remove('bg-white/50', 'w-2');
                dots[bannerIdx].classList.add('bg-white', 'w-3');
            }
        }
        function slideBanner(dir) { goToBanner(bannerIdx + dir); }
        if (slides.length > 1) setInterval(() => slideBanner(1), 5000);
        </script>
    @endif
@endpush