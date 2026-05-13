<!DOCTYPE html>
<html lang="id">
<head>
    <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}" />
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Jagoan Kue — @yield('title', 'Admin')</title>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700;800&family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        :root {
            --pink: #F0507A;
            --pink-hover: #D64A6C;
            --brown-dark: #2C1810;
            --brown-mid: #5C3D2E;
            --cream: #FFF8EE;
            --cream-dark: #F5EDD8;
            --white: #FFFFFF;
            --gray: #6B7280;
            --text-dark: #1A1A1A;
            --green: #22C55E;
            --blue: #3B82F6;
            --orange: #F59E0B;
            --red: #EF4444;
            --sidebar-w: 220px;
        }
        body { font-family: 'Plus Jakarta Sans', sans-serif; color: var(--text-dark); background: var(--cream); margin: 0; }
        a { text-decoration: none; color: inherit; }

        /* SIDEBAR */
        .sidebar {
            width: var(--sidebar-w);
            background: var(--brown-dark);
            min-height: 100vh;
            position: fixed;
            top: 0; left: 0;
            display: flex;
            flex-direction: column;
            z-index: 50;
            transition: transform 0.3s ease;
        }
        .sidebar-header {
            padding: 24px 24px 20px;
            border-bottom: 1px solid rgba(255,255,255,0.1);
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        .sidebar-logo {
            font-family: 'Playfair Display', serif;
            font-size: 20px;
            font-weight: 800;
            color: var(--pink);
        }
        .sidebar-subtitle {
            font-size: 11px;
            color: rgba(255,255,255,0.4);
            margin-top: 4px;
        }
        .sidebar-close {
            display: none;
            background: none;
            border: none;
            color: rgba(255,255,255,0.6);
            font-size: 18px;
            cursor: pointer;
        }
        .sidebar-close:hover { color: white; }

        .sidebar-nav {
            flex: 1;
            padding: 16px 12px;
            overflow-y: auto;
        }
        .sidebar-section {
            font-size: 10px;
            font-weight: 700;
            color: rgba(255,255,255,0.3);
            text-transform: uppercase;
            letter-spacing: 1.2px;
            padding: 0 10px;
            margin-bottom: 8px;
            margin-top: 20px;
        }
        .sidebar-section:first-child { margin-top: 0; }
        .sidebar-link {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 10px 14px;
            border-radius: 8px;
            font-size: 13px;
            font-weight: 500;
            color: rgba(255,255,255,0.6);
            margin-bottom: 2px;
            transition: all 0.2s;
        }
        .sidebar-link:hover {
            background: rgba(255,255,255,0.1);
            color: white;
        }
        .sidebar-link.active {
            background: var(--pink);
            color: white;
            font-weight: 600;
            box-shadow: 0 4px 12px rgba(240,80,122,0.3);
        }
        .sidebar-link-icon {
            width: 20px;
            text-align: center;
            font-size: 16px;
        }

        .sidebar-footer {
            padding: 16px;
            border-top: 1px solid rgba(255,255,255,0.1);
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .sidebar-avatar {
            width: 36px; height: 36px;
            border-radius: 50%;
            background: var(--pink);
            color: white;
            font-size: 13px;
            font-weight: 700;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }
        .sidebar-user { flex: 1; overflow: hidden; }
        .sidebar-user-name { font-size: 13px; font-weight: 700; color: white; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
        .sidebar-user-role { font-size: 11px; color: rgba(255,255,255,0.45); }
        .sidebar-logout {
            width: 32px; height: 32px;
            border-radius: 8px;
            background: rgba(255,255,255,0.1);
            border: none;
            color: white;
            font-size: 14px;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: background 0.2s;
        }
        .sidebar-logout:hover { background: var(--pink); }

        /* MAIN */
        .main-content {
            margin-left: var(--sidebar-w);
            min-height: 100vh;
        }
        .topbar {
            background: white;
            border-bottom: 1px solid #EDE0D4;
            padding: 16px 28px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            position: sticky;
            top: 0;
            z-index: 40;
        }
        .topbar-title {
            font-family: 'Playfair Display', serif;
            font-size: 22px;
            font-weight: 700;
            color: var(--text-dark);
        }
        .topbar-subtitle {
            font-size: 13px;
            color: var(--gray);
            margin-top: 2px;
        }
        .topbar-hamburger {
            display: none;
            background: none;
            border: none;
            font-size: 24px;
            cursor: pointer;
            color: var(--text-dark);
            margin-right: 12px;
        }
        .topbar-left { display: flex; align-items: center; }
        .topbar-actions { display: flex; align-items: center; gap: 10px; }
        .btn-topbar {
            background: var(--brown-dark);
            color: white;
            padding: 8px 16px;
            border-radius: 8px;
            font-size: 13px;
            font-weight: 600;
            transition: opacity 0.2s;
        }
        .btn-topbar:hover { opacity: 0.85; }

        .page-content {
            padding: 28px;
        }

        /* OVERLAY */
        .sidebar-overlay {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(0,0,0,0.4);
            z-index: 45;
        }

        /* RESPONSIVE */
        @media (max-width: 768px) {
            .sidebar { transform: translateX(-100%); }
            .sidebar.open { transform: translateX(0); }
            .sidebar-close { display: block; }
            .sidebar-overlay.active { display: block; }
            .main-content { margin-left: 0; }
            .topbar-hamburger { display: block; }
        }
    </style>
    @yield('styles')
    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @endif
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
        <button class="sidebar-close" onclick="closeSidebar()">✕</button>
    </div>

    <nav class="sidebar-nav">
        <div class="sidebar-section">Utama</div>
        <a href="{{ route('admin.dashboard') }}" class="sidebar-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
            <span class="sidebar-link-icon">⊞</span> Dashboard
        </a>
        <a href="{{ route('admin.orders') }}" class="sidebar-link {{ request()->routeIs('admin.orders') ? 'active' : '' }}">
            <span class="sidebar-link-icon">📋</span> Pesanan
        </a>
        <a href="{{ route('admin.products.index') }}" class="sidebar-link {{ request()->routeIs('admin.products.*') ? 'active' : '' }}">
            <span class="sidebar-link-icon">🎂</span> Produk
        </a>
        <a href="{{ route('admin.categories') }}" class="sidebar-link {{ request()->routeIs('admin.categories') ? 'active' : '' }}">
            <span class="sidebar-link-icon">🏷️</span> Kategori
        </a>
        <a href="{{ route('admin.customers') }}" class="sidebar-link {{ request()->routeIs('admin.customers') ? 'active' : '' }}">
            <span class="sidebar-link-icon">👤</span> Pelanggan
        </a>

        <div class="sidebar-section">Laporan</div>
        <a href="{{ route('admin.analytics') }}" class="sidebar-link {{ request()->routeIs('admin.analytics') ? 'active' : '' }}">
            <span class="sidebar-link-icon">📈</span> Analisis
        </a>
        <a href="{{ route('admin.finance') }}" class="sidebar-link {{ request()->routeIs('admin.finance') ? 'active' : '' }}">
            <span class="sidebar-link-icon">💰</span> Keuangan
        </a>

        <div class="sidebar-section">Sistem</div>
        <a href="{{ route('admin.settings') }}" class="sidebar-link {{ request()->routeIs('admin.settings') ? 'active' : '' }}">
            <span class="sidebar-link-icon">⚙</span> Pengaturan
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
            <button type="submit" class="sidebar-logout" title="Logout">🚪</button>
        </form>
    </div>
</aside>

<!-- MAIN CONTENT -->
<div class="main-content">
    <div class="topbar">
        <div class="topbar-left">
            <button class="topbar-hamburger" onclick="openSidebar()">☰</button>
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
        <div style="background:#DCFCE7;border:1px solid #A7F3D0;border-radius:10px;padding:12px 16px;font-size:13px;color:#065F46;margin-bottom:16px;">
            {{ session('success') }}
        </div>
        @endif
        @if($errors->any())
        <div style="background:#FEE2E2;border:1px solid #FECACA;border-radius:10px;padding:12px 16px;font-size:13px;color:#991B1B;margin-bottom:16px;">
            @foreach($errors->all() as $error)
                <p>{{ $error }}</p>
            @endforeach
        </div>
        @endif
        @yield('content')
    </div>
</div>

<script>
function openSidebar() {
    document.getElementById('sidebar').classList.add('open');
    document.getElementById('sidebarOverlay').classList.add('active');
}
function closeSidebar() {
    document.getElementById('sidebar').classList.remove('open');
    document.getElementById('sidebarOverlay').classList.remove('active');
}
</script>
@yield('scripts')
</body>
</html>
