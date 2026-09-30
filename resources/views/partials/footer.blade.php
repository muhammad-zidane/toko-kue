<footer class="bg-brown-dark text-white py-14 px-6">
    <div class="max-w-[1100px] mx-auto grid grid-cols-1 sm:grid-cols-2 md:grid-cols-[2fr_1fr_1fr_1.5fr] gap-10">
        {{-- Brand info --}}
        <div>
            <span class="font-heading text-[22px] font-extrabold text-primary mb-2 block">
                🍰 Jagoan Kue
            </span>
            <p class="text-[13px] opacity-60 mb-5 leading-[1.6]">
                Menyediakan kue dengan cinta sejak 2023. Hadirkan kehangatan dan kebersamaan di setiap gigitan artisan bakery kami.
            </p>
            <div class="flex gap-4 text-[18px]">
                <a href="#" class="text-white opacity-60 transition-opacity duration-200 hover:opacity-100">
                    <i class="fab fa-instagram"></i>
                </a>
                <a href="#" class="text-white opacity-60 transition-opacity duration-200 hover:opacity-100">
                    <i class="fab fa-tiktok"></i>
                </a>
                <a href="#" class="text-white opacity-60 transition-opacity duration-200 hover:opacity-100">
                    <i class="fab fa-whatsapp"></i>
                </a>
                <a href="#" class="text-white opacity-60 transition-opacity duration-200 hover:opacity-100">
                    <i class="fab fa-facebook-f"></i>
                </a>
            </div>
        </div>

        {{-- Layanan --}}
        <div>
            <p class="text-sm font-bold mb-4">Layanan</p>
            <ul class="list-none flex flex-col gap-[10px]">
                <li><a href="{{ route('products.index') }}" class="text-white text-[13px] opacity-60 transition-opacity duration-200 hover:opacity-100">Katalog Kue</a></li>
                <li><a href="{{ route('products.index') }}" class="text-white text-[13px] opacity-60 transition-opacity duration-200 hover:opacity-100">Kue Custom</a></li>
            </ul>
        </div>

        {{-- Selengkapnya --}}
        <div>
            <p class="text-sm font-bold mb-4">Selengkapnya</p>
            <ul class="list-none flex flex-col gap-[10px]">
                <li><a href="{{ route('about') }}" class="text-white text-[13px] opacity-60 transition-opacity duration-200 hover:opacity-100">Tentang Kami</a></li>
            </ul>
        </div>

        {{-- Kontak --}}
        <div>
            <p class="text-sm font-bold mb-4">Kontak Kami</p>
            <ul class="list-none flex flex-col gap-[10px]">
                <li><a href="tel:081234567890" class="text-white text-[13px] opacity-60 transition-opacity duration-200 hover:opacity-100"><i class="fas fa-phone mr-2"></i>081234567890</a></li>
                <li><a href="mailto:muhammadzidane@student.unp.ac.id" class="text-white text-[13px] opacity-60 transition-opacity duration-200 hover:opacity-100"><i class="fas fa-envelope mr-2"></i>muhammadzidane@student.unp.ac.id</a></li>
                <li class="text-[13px] opacity-60 leading-relaxed text-white">
                    <i class="fas fa-map-marker-alt mr-2"></i>
                    <span>Payakumbuh, Sumatera Barat</span>
                </li>
            </ul>
        </div>
    </div>

    <div class="max-w-[1100px] mx-auto mt-10 pt-6 border-t border-white/15 text-center text-xs opacity-40">
        <p>© {{ date('Y') }} Jagoan Kue. All rights reserved.</p>
    </div>
</footer>
