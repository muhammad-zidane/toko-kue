<!DOCTYPE html>
<html lang="id">
<head>
    <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}" />
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Jagoan Kue — @yield('title', 'Admin')</title>

    @include('partials.head-assets')

    <!-- Vite (Tailwind CSS) -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    @stack('styles')
</head>
<body class="bg-cream text-text-primary font-sans antialiased">

<!-- SIDEBAR OVERLAY (mobile) -->
<div class="fixed inset-0 bg-black/40 z-[45] hidden [&.active]:block md:hidden" id="sidebarOverlay" onclick="closeSidebar()"></div>

<!-- SIDEBAR -->
<aside class="fixed left-0 top-0 h-screen w-[220px] bg-brown-dark flex flex-col z-50 transition-transform duration-300 -translate-x-full md:translate-x-0 [&.open]:translate-x-0" id="sidebar">
    <div class="px-6 py-6 border-b border-white/10 flex items-center justify-between">
        <div>
            <div class="font-heading text-xl font-bold text-primary tracking-wide">Jagoan Kue</div>
            <div class="text-[11px] text-white/40 mt-1 uppercase tracking-widest font-semibold">admin panel</div>
        </div>
        <button class="md:hidden bg-transparent border-0 text-white/60 hover:text-white text-lg cursor-pointer" onclick="closeSidebar()"><i class="fas fa-times"></i></button>
    </div>

    <nav class="flex-1 px-3 py-4 overflow-y-auto scrollbar-none">
        <div class="text-[10px] font-bold text-white/30 uppercase tracking-widest px-3 mb-2 mt-5 first:mt-0">Utama</div>

        <x-admin.nav-link route="admin.dashboard" routePattern="admin.dashboard" icon="fa-th-large" label="Dashboard" />
        <x-admin.nav-link route="admin.orders.index" routePattern="admin.orders.*" icon="fa-clipboard-list" label="Pesanan" />
        <x-admin.nav-link route="admin.products.index" routePattern="admin.products.*" icon="fa-birthday-cake" label="Produk" />
        <x-admin.nav-link route="admin.categories.index" routePattern="admin.categories.index" icon="fa-tag" label="Kategori" />
        <x-admin.nav-link route="admin.customers.index" routePattern="admin.customers.index" icon="fa-user" label="Pelanggan" />

        <div class="text-[10px] font-bold text-white/30 uppercase tracking-widest px-3 mb-2 mt-5">Laporan</div>
        <x-admin.nav-link route="admin.analytics.index" routePattern="admin.analytics.index" icon="fa-chart-line" label="Analisis" />
        <x-admin.nav-link route="admin.finance.index" routePattern="admin.finance.index" icon="fa-money-bill-wave" label="Keuangan" />

        <div class="text-[10px] font-bold text-white/30 uppercase tracking-widest px-3 mb-2 mt-5">Manajemen</div>
        <x-admin.nav-link route="admin.banners.index" routePattern="admin.banners.*" icon="fa-image" label="Banner" />
        <x-admin.nav-link route="admin.vouchers.index" routePattern="admin.vouchers.*" icon="fa-ticket-alt" label="Voucher" />
        <x-admin.nav-link route="admin.shipping-zones.index" routePattern="admin.shipping-zones.*" icon="fa-map-marker-alt" label="Zona Kirim" />
        <x-admin.nav-link route="admin.reviews.index" routePattern="admin.reviews.*" icon="fa-star" label="Ulasan" />
        <x-admin.nav-link route="admin.customizations.index" routePattern="admin.customizations.*" icon="fa-sliders-h" label="Kustomisasi" />
        <x-admin.nav-link route="admin.production-calendar.index" routePattern="admin.production-calendar.*" icon="fa-calendar-alt" label="Kalender" />

        <div class="text-[10px] font-bold text-white/30 uppercase tracking-widest px-3 mb-2 mt-5">Sistem</div>
        <x-admin.nav-link route="admin.settings.index" routePattern="admin.settings.index" icon="fa-cog" label="Pengaturan" />
    </nav>

    <div class="px-4 py-4 border-t border-white/10 flex items-center gap-3 bg-brown-dark shrink-0">
        <div class="w-9 h-9 rounded-full bg-primary text-white text-sm font-bold flex items-center justify-center shrink-0">
            {{ strtoupper(substr(auth()->user()->name ?? 'AD', 0, 2)) }}
        </div>
        <div class="flex-1 min-w-0">
            <div class="text-sm font-bold text-white truncate leading-tight">{{ auth()->user()->name ?? 'Admin' }}</div>
            <div class="text-[11px] text-white/45 truncate">Super admin</div>
        </div>
        <form method="POST" action="{{ route('logout') }}" class="m-0 shrink-0">
            @csrf
            <button type="submit" class="w-8 h-8 rounded-lg bg-white/10 hover:bg-primary text-white text-sm flex items-center justify-center transition-all cursor-pointer border-0" title="Logout">
                <i class="fas fa-sign-out-alt"></i>
            </button>
        </form>
    </div>
</aside>

<!-- MAIN CONTENT -->
<div class="ml-0 md:ml-[220px] min-h-screen pt-[69px] bg-cream flex flex-col">
    <!-- TOPBAR -->
    <header class="fixed top-0 left-0 md:left-[220px] right-0 z-40 bg-white border-b border-cream-border h-[69px] flex items-center justify-between px-4 md:px-7 shadow-sm">
        <div class="flex items-center">
            <button class="md:hidden bg-transparent border-0 text-2xl cursor-pointer text-brown-dark mr-3 flex items-center justify-center" onclick="openSidebar()"><i class="fas fa-bars"></i></button>
            <div>
                <h1 class="font-heading text-lg md:text-2xl font-bold text-brown-dark leading-tight">@yield('page-title', 'Dashboard')</h1>
                <div class="text-xs md:text-sm text-text-secondary mt-0.5">@yield('page-subtitle', '')</div>
            </div>
        </div>

        <div class="flex items-center gap-3">
            {{-- Notification Bell --}}
            @php $unreadCount = auth()->user()->unreadNotifications->count(); @endphp
            <div class="relative inline-block">
                <button onclick="toggleNotifPanel()" class="bg-transparent border-none cursor-pointer p-2 relative flex items-center justify-center">
                    <i class="fas fa-bell text-lg text-brown-dark"></i>
                    @if($unreadCount > 0)
                    <span id="notifBadge" class="absolute top-1 right-1 bg-red-500 text-white text-[10px] font-bold rounded-full w-4 h-4 flex items-center justify-center">{{ $unreadCount > 9 ? '9+' : $unreadCount }}</span>
                    @endif
                </button>
                <div id="notifPanel" class="hidden absolute right-0 top-11 w-80 bg-white rounded-2xl shadow-lg z-50 border border-cream-border overflow-hidden">
                    <div class="px-4 py-3.5 border-b border-cream-border flex justify-between items-center">
                        <strong class="text-sm text-brown-dark">Notifikasi</strong>
                        @if($unreadCount > 0)
                        <form method="POST" action="{{ route('admin.notifications.readAll') }}" class="m-0">@csrf
                            <button type="submit" class="bg-transparent border-none cursor-pointer text-xs text-primary hover:text-primary-hover font-semibold">Tandai semua dibaca</button>
                        </form>
                        @endif
                    </div>
                    <div class="max-h-80 overflow-y-auto">
                        @forelse(auth()->user()->notifications->take(10) as $notif)
                        <a href="{{ $notif->data['url'] ?? '#' }}" onclick="markRead('{{ $notif->id }}')"
                           class="block px-4 py-3 border-b border-cream-warm text-left hover:bg-cream-warm transition-colors {{ $notif->read_at ? 'bg-white' : 'bg-amber-50/50' }}">
                            <p class="text-xs text-brown-dark mb-1 leading-snug {{ $notif->read_at ? 'font-normal' : 'font-semibold' }}">
                                {{ $notif->data['message'] ?? 'Notifikasi baru' }}
                            </p>
                            <small class="text-[10px] text-text-muted">{{ $notif->created_at->diffForHumans() }}</small>
                        </a>
                        @empty
                        <div class="py-6 text-center text-text-muted text-xs">Tidak ada notifikasi</div>
                        @endforelse
                    </div>
                </div>
            </div>
            <a href="{{ route('home') }}" class="bg-brown-dark text-white px-4 py-2 rounded-xl text-xs font-semibold hover:bg-brown-mid transition-all">← Ke Toko</a>
        </div>
    </header>

    <div class="p-4 md:p-7 flex-1">
        @if(session('success'))
        <div class="bg-green-50 text-green-700 border border-green-200 rounded-xl p-4 text-sm mb-4">{{ session('success') }}</div>
        @endif
        @if($errors->any())
        <div class="bg-red-50 text-red-700 border border-red-200 rounded-xl p-4 text-sm mb-4">
            @foreach($errors->all() as $error)
                <p>{{ $error }}</p>
            @endforeach
        </div>
        @endif
        @yield('content')
    </div>
</div>

<script src="{{ asset('js/admin.js') }}" defer></script>
<script src="{{ asset('js/app.js') }}" defer></script>
@stack('scripts')
</body>
</html>
