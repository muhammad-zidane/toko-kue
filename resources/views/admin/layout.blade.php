<!DOCTYPE html>
<html lang="id">
<head>
    <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}" />
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
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
@stack('scripts')
</body>
</html>
