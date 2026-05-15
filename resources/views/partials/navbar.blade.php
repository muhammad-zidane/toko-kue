<nav class="navbar">
    <div class="navbar-inner">
        <a href="/" class="navbar-logo">Jagoan Kue</a>
        <ul class="navbar-links">
            <li><a href="/" {{ request()->is('/') ? 'class=active' : '' }}>Beranda</a></li>
            <li><a href="/products" {{ request()->is('products*') ? 'class=active' : '' }}>Katalog</a></li>
            <li><a href="/about" {{ request()->is('about') ? 'class=active' : '' }}>Tentang Kami</a></li>
            @auth
            <li><a href="/orders" {{ request()->is('orders*') ? 'class=active' : '' }}>Pesanan Saya</a></li>
            @endauth
        </ul>
        <div class="navbar-actions">
            <a href="/cart" class="btn-cart">
                <i class="fas fa-shopping-cart" style="color:white"></i> Keranjang
            </a>
            @auth
                <div style="position:relative;display:inline-block;" x-data="{ open: false }">
                    <button @click="open=!open" style="background:var(--pink);color:white;border:none;border-radius:8px;padding:8px 16px;font-size:13px;font-weight:600;cursor:pointer;font-family:'Plus Jakarta Sans',sans-serif;">
                        {{ auth()->user()->name }} <i class="fas fa-chevron-down" style="font-size:10px;margin-left:4px;"></i>
                    </button>
                    <div x-show="open" @click.outside="open=false" x-cloak
                         style="position:absolute;right:0;top:44px;background:white;border-radius:10px;box-shadow:0 8px 24px rgba(0,0,0,0.12);border:1px solid #EDE0D4;min-width:180px;z-index:100;overflow:hidden;">
                        <a href="/profile" style="display:block;padding:10px 16px;font-size:13px;color:var(--text-dark);text-decoration:none;border-bottom:1px solid #F0E8E0;">
                            <i class="fas fa-user" style="color:var(--pink);margin-right:8px;"></i> Profil
                        </a>
                        <a href="/account/addresses" style="display:block;padding:10px 16px;font-size:13px;color:var(--text-dark);text-decoration:none;border-bottom:1px solid #F0E8E0;">
                            <i class="fas fa-map-marker-alt" style="color:var(--pink);margin-right:8px;"></i> Alamat Tersimpan
                        </a>
                        <a href="/akun/ganti-password" style="display:block;padding:10px 16px;font-size:13px;color:var(--text-dark);text-decoration:none;border-bottom:1px solid #F0E8E0;">
                            <i class="fas fa-lock" style="color:var(--pink);margin-right:8px;"></i> Ganti Password
                        </a>
                        @if(auth()->user()->role === 'admin')
                        <a href="/admin/dashboard" style="display:block;padding:10px 16px;font-size:13px;color:var(--text-dark);text-decoration:none;border-bottom:1px solid #F0E8E0;">
                            <i class="fas fa-cog" style="color:var(--pink);margin-right:8px;"></i> Admin Panel
                        </a>
                        @endif
                        <form method="POST" action="/logout">@csrf
                            <button type="submit" style="display:block;width:100%;padding:10px 16px;font-size:13px;color:#DC2626;text-align:left;background:none;border:none;cursor:pointer;font-family:'Plus Jakarta Sans',sans-serif;">
                                <i class="fas fa-sign-out-alt" style="margin-right:8px;"></i> Keluar
                            </button>
                        </form>
                    </div>
                </div>
            @else
                <a href="/login" class="btn-login">Login</a>
            @endauth
        </div>
    </div>
</nav>
