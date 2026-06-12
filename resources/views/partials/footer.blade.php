<footer class="footer">
    <div class="footer-inner">
        <div>
            <p class="footer-logo">Jagoan Kue</p>
            <p class="footer-desc">Menyediakan kue dengan cinta sejak 2023</p>
            <div class="footer-socials">
                <a href="#"><i class="fab fa-instagram" style="color:white"></i></a>
                <a href="#"><i class="fab fa-tiktok" style="color:white"></i></a>
                <a href="#"><i class="fab fa-whatsapp" style="color:white"></i></a>
                <a href="#"><i class="fab fa-facebook-f" style="color:white"></i></a>
            </div>
        </div>
        <div>
            <p class="footer-heading">Layanan</p>
            <ul class="footer-links">
                <li><a href="{{ route('products.index') }}">Katalog Kue</a></li>
                <li><a href="{{ route('products.index') }}">Kue Custom</a></li>
            </ul>
        </div>
        <div>
            <p class="footer-heading">Selengkapnya</p>
            <ul class="footer-links">
                <li><a href="{{ route('about') }}">Tentang Kami</a></li>
            </ul>
        </div>
        <div>
            <p class="footer-heading">Kontak Kami</p>
            <ul class="footer-contact">
                <li><a href="tel:081234567890">081234567890</a></li>
                <li><a href="mailto:muhammadzidane@student.unp.ac.id">muhammadzidane@student.unp.ac.id</a></li>
                <li>Payakumbuh, Sumatera Barat</li>
            </ul>
        </div>
    </div>
    <div class="footer-bottom">
        <p>© {{ date('Y') }} Jagoan Kue. All rights reserved.</p>
    </div>
</footer>
