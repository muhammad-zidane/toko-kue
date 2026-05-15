<nav class="navbar">
    <div class="navbar-inner">
        <a href="/" class="navbar-logo">Jagoan Kue</a>
        <ul class="navbar-links">
            <li><a href="/" {{ request()->is('/') ? 'class=active' : '' }}>Beranda</a></li>
            <li><a href="/products" {{ request()->is('products*') ? 'class=active' : '' }}>Katalog</a></li>
            <li><a href="/orders" {{ request()->is('orders*') ? 'class=active' : '' }}>Pesanan Saya</a></li>
        </ul>
        <div class="navbar-actions">
            <a href="/cart" class="btn-cart">
                <i class="fas fa-shopping-cart" style="color:white"></i> Keranjang
            </a>
            @auth
                <a href="/profile" class="btn-login">{{ auth()->user()->name }}</a>
            @else
                <a href="/login" class="btn-login">Login</a>
            @endauth
        </div>
    </div>
</nav>
