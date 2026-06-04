<!DOCTYPE html>
<html lang="id">
<head>
    <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}" />
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Jagoan Kue — @yield('title', 'Admin')</title>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700;800&family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('css/admin.css') }}">
    @stack('styles')
</head>
<body>

<!-- SIDEBAR OVERLAY (mobile) -->
<div class="sidebar-overlay" id="sidebarOverlay" onclick="closeSidebar()"></div>

<!-- SIDEBAR -->
<aside class="sidebar" id="sidebar">
    <div class="sidebar-header">
        <div>
            <div class="sidebar-logo">Jagoan Kue</div>
            <div class="sidebar-subtitle">admin panel</div>
        </div>
        <button class="sidebar-close" onclick="closeSidebar()"><i class="fas fa-times"></i></button>
    </div>

    <nav class="sidebar-nav">
        <div class="sidebar-section">Utama</div>
        <a href="{{ route('admin.dashboard') }}" class="sidebar-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
            <span class="sidebar-link-icon"><i class="fas fa-th-large"></i></span> Dashboard
        </a>
        <a href="{{ route('admin.orders.index') }}" class="sidebar-link {{ request()->routeIs('admin.orders.index') ? 'active' : '' }}">
            <span class="sidebar-link-icon"><i class="fas fa-clipboard-list"></i></span> Pesanan
        </a>
        <a href="{{ route('admin.products.index') }}" class="sidebar-link {{ request()->routeIs('admin.products.*') ? 'active' : '' }}">
            <span class="sidebar-link-icon"><i class="fas fa-birthday-cake"></i></span> Produk
        </a>
        <a href="{{ route('admin.categories.index') }}" class="sidebar-link {{ request()->routeIs('admin.categories.index') ? 'active' : '' }}">
            <span class="sidebar-link-icon"><i class="fas fa-tag"></i></span> Kategori
        </a>
        <a href="{{ route('admin.customers.index') }}" class="sidebar-link {{ request()->routeIs('admin.customers.index') ? 'active' : '' }}">
            <span class="sidebar-link-icon"><i class="fas fa-user"></i></span> Pelanggan
        </a>

        <div class="sidebar-section">Laporan</div>
        <a href="{{ route('admin.analytics.index') }}" class="sidebar-link {{ request()->routeIs('admin.analytics.index') ? 'active' : '' }}">
            <span class="sidebar-link-icon"><i class="fas fa-chart-line"></i></span> Analisis
        </a>
        <a href="{{ route('admin.finance.index') }}" class="sidebar-link {{ request()->routeIs('admin.finance.index') ? 'active' : '' }}">
            <span class="sidebar-link-icon"><i class="fas fa-money-bill-wave"></i></span> Keuangan
        </a>

        <div class="sidebar-section">Manajemen</div>
        <a href="{{ route('admin.banners.index') }}" class="sidebar-link {{ request()->routeIs('admin.banners.*') ? 'active' : '' }}">
            <span class="sidebar-link-icon"><i class="fas fa-image"></i></span> Banner
        </a>
        <a href="{{ route('admin.vouchers.index') }}" class="sidebar-link {{ request()->routeIs('admin.vouchers.*') ? 'active' : '' }}">
            <span class="sidebar-link-icon"><i class="fas fa-ticket-alt"></i></span> Voucher
        </a>
        <a href="{{ route('admin.shipping-zones.index') }}" class="sidebar-link {{ request()->routeIs('admin.shipping-zones.*') ? 'active' : '' }}">
            <span class="sidebar-link-icon"><i class="fas fa-map-marker-alt"></i></span> Zona Kirim
        </a>
        <a href="{{ route('admin.reviews.index') }}" class="sidebar-link {{ request()->routeIs('admin.reviews.*') ? 'active' : '' }}">
            <span class="sidebar-link-icon"><i class="fas fa-star"></i></span> Ulasan
        </a>
        <a href="{{ route('admin.customizations.index') }}" class="sidebar-link {{ request()->routeIs('admin.customizations.*') ? 'active' : '' }}">
            <span class="sidebar-link-icon"><i class="fas fa-sliders-h"></i></span> Kustomisasi
        </a>
        <a href="{{ route('admin.production-calendar.index') }}" class="sidebar-link {{ request()->routeIs('admin.production-calendar.*') ? 'active' : '' }}">
            <span class="sidebar-link-icon"><i class="fas fa-calendar-alt"></i></span> Kalender
        </a>

        <div class="sidebar-section">Sistem</div>
        <a href="{{ route('admin.settings.index') }}" class="sidebar-link {{ request()->routeIs('admin.settings.index') ? 'active' : '' }}">
            <span class="sidebar-link-icon"><i class="fas fa-cog"></i></span> Pengaturan
        </a>
    </nav>

    <div class="sidebar-footer">
        <div class="sidebar-avatar">{{ strtoupper(substr(auth()->user()->name ?? 'AD', 0, 2)) }}</div>
        <div class="sidebar-user">
            <div class="sidebar-user-name">{{ auth()->user()->name ?? 'Admin' }}</div>
            <div class="sidebar-user-role">Super admin</div>
        </div>
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="sidebar-logout" title="Logout"><i class="fas fa-sign-out-alt" style="color:white"></i></button>
        </form>
    </div>
</aside>

<!-- MAIN CONTENT -->
<div class="main-content">
    <div class="topbar">
        <div class="topbar-left">
            <button class="topbar-hamburger" onclick="openSidebar()"><i class="fas fa-bars" style="color:var(--brown-dark)"></i></button>
            <div>
                <div class="topbar-title">@yield('page-title', 'Dashboard')</div>
                <div class="topbar-subtitle">@yield('page-subtitle', '')</div>
            </div>
        </div>
        <div class="topbar-actions">
            {{-- Notification Bell --}}
            @php $unreadCount = auth()->user()->unreadNotifications->count(); @endphp
            <div style="position:relative;display:inline-block;">
                <button onclick="toggleNotifPanel()" style="background:none;border:none;cursor:pointer;padding:8px;position:relative;">
                    <i class="fas fa-bell" style="font-size:18px;color:var(--brown-dark);"></i>
                    @if($unreadCount > 0)
                    <span id="notifBadge" style="position:absolute;top:4px;right:4px;background:#EF4444;color:white;font-size:10px;font-weight:700;border-radius:50%;width:16px;height:16px;display:flex;align-items:center;justify-content:center;">{{ $unreadCount > 9 ? '9+' : $unreadCount }}</span>
                    @endif
                </button>
                <div id="notifPanel" style="display:none;position:absolute;right:0;top:44px;width:320px;background:white;border-radius:12px;box-shadow:0 8px 32px rgba(0,0,0,0.15);z-index:100;border:1px solid #EDE0D4;overflow:hidden;">
                    <div style="padding:14px 16px;border-bottom:1px solid #EDE0D4;display:flex;justify-content:space-between;align-items:center;">
                        <strong style="font-size:14px;">Notifikasi</strong>
                        @if($unreadCount > 0)
                        <form method="POST" action="{{ route('admin.notifications.readAll') }}">@csrf
                            <button type="submit" style="background:none;border:none;cursor:pointer;font-size:11px;color:var(--pink);">Tandai semua dibaca</button>
                        </form>
                        @endif
                    </div>
                    <div style="max-height:320px;overflow-y:auto;">
                        @forelse(auth()->user()->notifications->take(10) as $notif)
                        <a href="{{ $notif->data['url'] ?? '#' }}" onclick="markRead('{{ $notif->id }}')"
                           style="display:block;padding:12px 16px;border-bottom:1px solid #F5F0EB;text-decoration:none;background:{{ $notif->read_at ? 'white' : '#FFF8F8' }};">
                            <p style="font-size:13px;font-weight:{{ $notif->read_at ? '400' : '600' }};color:#1F2937;margin-bottom:4px;">
                                {{ $notif->data['message'] ?? 'Notifikasi baru' }}
                            </p>
                            <small style="color:#9CA3AF;">{{ $notif->created_at->diffForHumans() }}</small>
                        </a>
                        @empty
                        <div style="padding:24px;text-align:center;color:#9CA3AF;font-size:13px;">Tidak ada notifikasi</div>
                        @endforelse
                    </div>
                </div>
            </div>
            <a href="{{ route('home') }}" class="btn-topbar">← Ke Toko</a>
        </div>
    </div>

    <div class="page-content">
        @if(session('success'))
        <div class="flash-success">{{ session('success') }}</div>
        @endif
        @if($errors->any())
        <div class="flash-error">
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
<script>
function toggleNotifPanel() {
    const p = document.getElementById('notifPanel');
    if (p) p.style.display = p.style.display === 'none' ? 'block' : 'none';
}
function markRead(id) {
    fetch('/admin/notifications/' + id + '/read', {
        method: 'POST',
        headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]')?.content || '' }
    });
}
document.addEventListener('click', function(e) {
    const panel = document.getElementById('notifPanel');
    if (panel && !panel.contains(e.target) && !e.target.closest('[onclick="toggleNotifPanel()"]')) {
        panel.style.display = 'none';
    }
});
</script>
@stack('scripts')
</body>
</html>

