<nav x-data="{ mobileOpen: false }" class="sticky top-0 z-50 bg-white border-b border-cream-border shadow-sm">
    <div class="max-w-[1140px] mx-auto px-6">
        <div class="flex items-center justify-between h-20">
            {{-- Logo --}}
            <a href="{{ route('home') }}" class="font-heading text-2xl font-bold text-primary flex items-center gap-2 hover:text-primary-hover transition-colors">
                🍰 Jagoan Kue
            </a>

            {{-- Links --}}
            <div class="hidden md:flex items-center gap-8">
                <a href="{{ route('home') }}" class="text-sm font-semibold hover:text-primary transition-colors {{ request()->is('/') ? 'text-primary border-b-2 border-primary pb-0.5' : 'text-brown-mid border-b-2 border-transparent pb-0.5' }}">
                    Beranda
                </a>
                <a href="{{ route('products.index') }}" class="text-sm font-semibold hover:text-primary transition-colors {{ request()->is('products*') ? 'text-primary border-b-2 border-primary pb-0.5' : 'text-brown-mid border-b-2 border-transparent pb-0.5' }}">
                    Katalog
                </a>
                <a href="/about" class="text-sm font-semibold hover:text-primary transition-colors {{ request()->is('about') ? 'text-primary border-b-2 border-primary pb-0.5' : 'text-brown-mid border-b-2 border-transparent pb-0.5' }}">
                    Tentang Kami
                </a>
                @auth
                <a href="{{ route('orders.index') }}" class="text-sm font-semibold hover:text-primary transition-colors {{ request()->is('orders*') ? 'text-primary border-b-2 border-primary pb-0.5' : 'text-brown-mid border-b-2 border-transparent pb-0.5' }}">
                    Pesanan Saya
                </a>
                @endauth
            </div>

            {{-- Actions --}}
            <div class="flex items-center gap-3">
                @php $cartCount = collect(session()->get('cart', []))->sum('quantity'); @endphp
                <a href="{{ route('cart.index') }}" class="relative inline-flex items-center gap-2 bg-primary text-white px-5 py-2.5 rounded-full text-sm font-bold shadow-gold hover:bg-primary-hover hover:-translate-y-0.5 transition-all">
                    <i class="fas fa-shopping-cart"></i>
                    <span>Keranjang</span>
                    <span id="cart-badge" class="absolute -top-2 -right-2 bg-white text-primary text-[10px] font-black w-5 h-5 rounded-full flex items-center justify-center border-2 border-primary {{ $cartCount > 0 ? 'flex' : 'hidden' }}">
                        {{ $cartCount }}
                    </span>
                </a>

                @auth
                    <div class="relative" x-data="{ open: false }" @click.away="open = false">
                        <button @click="open = !open" id="profileToggle" aria-label="Menu profil pengguna" aria-expanded="false" class="bg-cream-warm text-brown-dark border border-cream-border px-4 py-2.5 rounded-full text-sm font-bold flex items-center gap-2 cursor-pointer hover:bg-cream-dark transition-colors">
                            <span>{{ auth()->user()->name }}</span>
                            <i class="fas fa-chevron-down transition-transform duration-200" :class="open ? 'rotate-180' : ''"></i>
                        </button>

                        <div x-show="open"
                             x-transition:enter="transition ease-out duration-100"
                             x-transition:enter-start="transform opacity-0 scale-95"
                             x-transition:enter-end="transform opacity-100 scale-100"
                             x-transition:leave="transition ease-in duration-75"
                             x-transition:leave-start="transform opacity-100 scale-100"
                             x-transition:leave-end="transform opacity-0 scale-95"
                             id="profileMenu"
                             class="absolute right-0 top-full mt-2 w-52 bg-white rounded-2xl shadow-lg border border-cream-border py-2 z-50"
                             style="display: none;">
                            <a href="{{ route('profile.index') }}" class="flex items-center gap-3 px-4 py-3 text-sm text-brown-mid hover:bg-cream hover:text-primary transition-colors">
                                <i class="fas fa-user text-primary w-4 text-center"></i> Profil
                            </a>
                            <a href="{{ route('account.addresses.index') }}" class="flex items-center gap-3 px-4 py-3 text-sm text-brown-mid hover:bg-cream hover:text-primary transition-colors">
                                <i class="fas fa-map-marker-alt text-primary w-4 text-center"></i> Alamat
                            </a>
                            <a href="{{ route('account.change-password') }}" class="flex items-center gap-3 px-4 py-3 text-sm text-brown-mid hover:bg-cream hover:text-primary transition-colors">
                                <i class="fas fa-lock text-primary w-4 text-center"></i> Ganti Password
                            </a>
                            @if(auth()->user()->isAdmin())
                            <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-3 px-4 py-3 text-sm text-brown-mid hover:bg-cream hover:text-primary transition-colors">
                                <i class="fas fa-cog text-primary w-4 text-center"></i> Admin Panel
                            </a>
                            @endif
                            <div class="border-t border-cream-border my-1"></div>
                            <form method="POST" action="{{ route('logout') }}" class="block w-full">@csrf
                                <button type="submit" class="flex items-center gap-3 w-full px-4 py-3 text-sm text-red-600 font-bold hover:bg-red-50 transition-colors bg-transparent border-none text-left cursor-pointer">
                                    <i class="fas fa-sign-out-alt text-red-600 w-4 text-center"></i> Keluar
                                </button>
                            </form>
                        </div>
                    </div>
                @else
                    <a href="{{ route('login') }}" class="bg-cream-warm border-2 border-primary text-primary px-5 py-2.5 rounded-full text-sm font-bold hover:bg-primary hover:text-white transition-all">
                        Login
                    </a>
                @endauth

                {{-- Hamburger --}}
                <button @click="mobileOpen = !mobileOpen" aria-label="Toggle navigasi mobile" class="md:hidden flex items-center justify-center w-10 h-10 rounded-xl border border-cream-border text-brown-dark hover:bg-cream-warm">
                    <i class="fas fa-bars"></i>
                </button>
            </div>
        </div>
    </div>

    {{-- Mobile Menu --}}
    <div md:hidden x-show="mobileOpen" x-transition class="bg-white border-t border-cream-border px-6 py-4 flex flex-col gap-2" style="display: none;">
        <a href="/" class="text-sm font-semibold py-2.5 transition-colors hover:text-primary {{ request()->is('/') ? 'text-primary' : 'text-brown-mid' }}">
            Beranda
        </a>
        <a href="/products" class="text-sm font-semibold py-2.5 transition-colors hover:text-primary {{ request()->is('products*') ? 'text-primary' : 'text-brown-mid' }}">
            Katalog
        </a>
        <a href="/about" class="text-sm font-semibold py-2.5 transition-colors hover:text-primary {{ request()->is('about') ? 'text-primary' : 'text-brown-mid' }}">
            Tentang Kami
        </a>
        @auth
        <a href="/orders" class="text-sm font-semibold py-2.5 transition-colors hover:text-primary {{ request()->is('orders*') ? 'text-primary' : 'text-brown-mid' }}">
            Pesanan Saya
        </a>
        @endauth
    </div>
</nav>
