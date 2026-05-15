<!DOCTYPE html>
<html lang="id">
<head>
    <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}" />
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tentang Kami — Jagoan Kue</title>
    <meta name="description" content="Jagoan Kue — toko kue rumahan dengan bahan segar pilihan. Melayani custom cake, kue ulang tahun, kue pernikahan, dan pengiriman ke seluruh kota.">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700;800&family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    <style>
        /* HERO */
        .about-hero {
            background: linear-gradient(135deg, var(--cream) 0%, #FDE8EF 100%);
            padding: 80px 24px 60px;
            text-align: center;
        }
        .about-hero-tag {
            display: inline-block;
            background: var(--pink);
            color: white;
            font-size: 12px;
            font-weight: 700;
            padding: 6px 16px;
            border-radius: 100px;
            letter-spacing: 0.5px;
            margin-bottom: 20px;
        }
        .about-hero h1 {
            font-family: 'Playfair Display', serif;
            font-size: clamp(32px, 5vw, 52px);
            font-weight: 800;
            color: var(--text-dark);
            margin-bottom: 20px;
            line-height: 1.2;
        }
        .about-hero h1 span { color: var(--pink); }
        .about-hero p {
            font-size: 16px;
            color: var(--gray);
            max-width: 600px;
            margin: 0 auto;
            line-height: 1.8;
        }
        .hero-deco {
            margin-top: 40px;
            display: flex;
            justify-content: center;
            gap: 32px;
            flex-wrap: wrap;
        }
        .hero-stat {
            text-align: center;
        }
        .hero-stat-num {
            font-family: 'Playfair Display', serif;
            font-size: 32px;
            font-weight: 800;
            color: var(--pink);
        }
        .hero-stat-label {
            font-size: 13px;
            color: var(--gray);
            font-weight: 500;
        }

        /* STORY */
        .about-story {
            padding: 72px 24px;
            background: white;
        }
        .story-inner {
            max-width: 1000px;
            margin: 0 auto;
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 60px;
            align-items: center;
        }
        .story-img-wrap {
            position: relative;
        }
        .story-img {
            width: 100%;
            border-radius: 20px;
            object-fit: cover;
            height: 380px;
            background: var(--cream);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 80px;
        }
        .story-img-placeholder {
            width: 100%;
            height: 380px;
            border-radius: 20px;
            background: linear-gradient(135deg, var(--cream) 0%, #FDE8EF 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 80px;
            border: 2px dashed #EDE0D4;
        }
        .story-badge {
            position: absolute;
            bottom: -16px;
            right: -16px;
            background: var(--pink);
            color: white;
            border-radius: 16px;
            padding: 16px 20px;
            text-align: center;
            box-shadow: 0 8px 24px rgba(240,80,122,0.3);
        }
        .story-badge-num { font-size: 28px; font-weight: 800; font-family: 'Playfair Display', serif; }
        .story-badge-text { font-size: 11px; font-weight: 600; opacity: 0.9; }
        .story-tag {
            display: inline-block;
            background: var(--cream);
            color: var(--brown-dark);
            font-size: 12px;
            font-weight: 700;
            padding: 5px 14px;
            border-radius: 100px;
            margin-bottom: 16px;
        }
        .story-content h2 {
            font-family: 'Playfair Display', serif;
            font-size: 32px;
            font-weight: 800;
            color: var(--text-dark);
            margin-bottom: 16px;
            line-height: 1.3;
        }
        .story-content h2 span { color: var(--pink); }
        .story-content p {
            font-size: 14px;
            color: var(--gray);
            line-height: 1.8;
            margin-bottom: 14px;
        }

        /* KEUNGGULAN */
        .about-features {
            padding: 72px 24px;
            background: var(--cream);
        }
        .features-inner { max-width: 1000px; margin: 0 auto; }
        .section-header { text-align: center; margin-bottom: 48px; }
        .section-tag {
            display: inline-block;
            background: var(--pink);
            color: white;
            font-size: 12px;
            font-weight: 700;
            padding: 5px 14px;
            border-radius: 100px;
            margin-bottom: 14px;
        }
        .section-header h2 {
            font-family: 'Playfair Display', serif;
            font-size: 32px;
            font-weight: 800;
            color: var(--text-dark);
        }
        .section-header h2 span { color: var(--pink); }
        .section-header p { font-size: 14px; color: var(--gray); margin-top: 10px; }
        .features-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 24px;
        }
        .feature-card {
            background: white;
            border-radius: 16px;
            padding: 28px;
            border: 1px solid #EDE0D4;
            display: flex;
            gap: 18px;
            align-items: flex-start;
            transition: box-shadow 0.2s, transform 0.2s;
        }
        .feature-card:hover {
            box-shadow: 0 8px 24px rgba(0,0,0,0.08);
            transform: translateY(-2px);
        }
        .feature-icon {
            width: 52px;
            height: 52px;
            border-radius: 14px;
            background: linear-gradient(135deg, var(--pink) 0%, #FF8FAB 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 22px;
            color: white;
            flex-shrink: 0;
        }
        .feature-icon.brown {
            background: linear-gradient(135deg, var(--brown-dark) 0%, #8B6347 100%);
        }
        .feature-content h3 {
            font-size: 15px;
            font-weight: 700;
            color: var(--text-dark);
            margin-bottom: 8px;
        }
        .feature-content p {
            font-size: 13px;
            color: var(--gray);
            line-height: 1.7;
        }

        /* KONTAK */
        .about-contact {
            padding: 72px 24px;
            background: white;
        }
        .contact-inner { max-width: 1000px; margin: 0 auto; }
        .contact-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 40px;
            margin-top: 40px;
        }
        .contact-info-wrap {}
        .contact-item {
            display: flex;
            gap: 14px;
            align-items: flex-start;
            margin-bottom: 24px;
        }
        .contact-icon {
            width: 44px;
            height: 44px;
            border-radius: 12px;
            background: var(--cream);
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--pink);
            font-size: 16px;
            flex-shrink: 0;
        }
        .contact-label {
            font-size: 11px;
            font-weight: 700;
            color: var(--gray);
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 4px;
        }
        .contact-value {
            font-size: 14px;
            font-weight: 600;
            color: var(--text-dark);
        }
        .contact-value a {
            color: var(--pink);
            text-decoration: none;
        }
        .contact-value a:hover { text-decoration: underline; }
        .hours-card {
            background: linear-gradient(135deg, var(--brown-dark) 0%, #5C3D2E 100%);
            border-radius: 16px;
            padding: 28px;
            color: white;
            height: fit-content;
        }
        .hours-card h3 {
            font-family: 'Playfair Display', serif;
            font-size: 20px;
            font-weight: 700;
            margin-bottom: 20px;
        }
        .hours-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 10px 0;
            border-bottom: 1px solid rgba(255,255,255,0.1);
            font-size: 14px;
        }
        .hours-row:last-child { border-bottom: none; }
        .hours-day { opacity: 0.8; }
        .hours-time { font-weight: 700; }
        .hours-closed { color: #FCA5A5; font-weight: 700; }

        /* CTA */
        .about-cta {
            padding: 72px 24px;
            background: linear-gradient(135deg, var(--pink) 0%, #FF6B95 100%);
            text-align: center;
        }
        .about-cta h2 {
            font-family: 'Playfair Display', serif;
            font-size: 32px;
            font-weight: 800;
            color: white;
            margin-bottom: 14px;
        }
        .about-cta p { font-size: 15px; color: rgba(255,255,255,0.85); margin-bottom: 28px; }
        .btn-cta {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: white;
            color: var(--pink);
            font-weight: 700;
            font-size: 14px;
            padding: 14px 28px;
            border-radius: 12px;
            text-decoration: none;
            box-shadow: 0 4px 16px rgba(0,0,0,0.15);
            transition: transform 0.2s, box-shadow 0.2s;
        }
        .btn-cta:hover { transform: translateY(-2px); box-shadow: 0 8px 24px rgba(0,0,0,0.2); }
        .btn-cta-outline {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: transparent;
            color: white;
            font-weight: 700;
            font-size: 14px;
            padding: 14px 28px;
            border-radius: 12px;
            text-decoration: none;
            border: 2px solid rgba(255,255,255,0.6);
            margin-left: 12px;
            transition: background 0.2s;
        }
        .btn-cta-outline:hover { background: rgba(255,255,255,0.1); }

        @media (max-width: 768px) {
            .story-inner { grid-template-columns: 1fr; gap: 40px; }
            .story-badge { bottom: 12px; right: 12px; }
            .features-grid { grid-template-columns: 1fr; }
            .contact-grid { grid-template-columns: 1fr; }
        }
    </style>
</head>
<body>

@include('partials.navbar')

{{-- HERO --}}
<section class="about-hero">
    <div class="about-hero-tag"><i class="fas fa-birthday-cake"></i> Tentang Jagoan Kue</div>
    <h1>Dibuat dengan <span>Cinta</span>,<br>Disajikan dengan Kebanggaan</h1>
    <p>Kami adalah toko kue rumahan yang hadir dengan misi sederhana: membawa kebahagiaan melalui setiap gigitan kue yang lezat dan berkualitas tinggi.</p>
    <div class="hero-deco">
        <div class="hero-stat">
            <div class="hero-stat-num">500+</div>
            <div class="hero-stat-label">Pelanggan Puas</div>
        </div>
        <div class="hero-stat">
            <div class="hero-stat-num">50+</div>
            <div class="hero-stat-label">Varian Kue</div>
        </div>
        <div class="hero-stat">
            <div class="hero-stat-num">3+</div>
            <div class="hero-stat-label">Tahun Pengalaman</div>
        </div>
    </div>
</section>

{{-- CERITA KAMI --}}
<section class="about-story">
    <div class="story-inner">
        <div class="story-img-wrap">
            <img src="{{ asset('images/cake.png') }}"
                 alt="Dapur Jagoan Kue"
                 style="width:100%;height:380px;object-fit:cover;border-radius:20px;display:block;box-shadow:0 12px 40px rgba(0,0,0,0.12);"
                 loading="lazy">
            <div class="story-badge">
                <div class="story-badge-num">3+</div>
                <div class="story-badge-text">Tahun Melayani</div>
            </div>
        </div>
        <div class="story-content">
            <span class="story-tag">Cerita Kami</span>
            <h2>Berawal dari <span>Dapur Rumah</span>, Menjadi Pilihan Utama</h2>
            <p>Jagoan Kue lahir dari kecintaan mendalam terhadap seni membuat kue. Berawal dari dapur rumah kecil, kami mulai melayani pesanan kue untuk acara keluarga dan kerabat terdekat.</p>
            <p>Dengan komitmen terhadap kualitas bahan baku segar dan teknik pembuatan yang teliti, kami perlahan-lahan mendapat kepercayaan dari semakin banyak pelanggan. Kini, Jagoan Kue melayani ratusan pesanan setiap bulannya.</p>
            <p>Setiap kue yang kami buat mengandung ketulusan dan perhatian pada detail, karena kami percaya bahwa kue yang baik bukan hanya soal rasa, tapi juga soal momen yang diciptakannya.</p>
        </div>
    </div>
</section>

{{-- KEUNGGULAN --}}
<section class="about-features">
    <div class="features-inner">
        <div class="section-header">
            <span class="section-tag">Mengapa Pilih Kami</span>
            <h2>Keunggulan <span>Jagoan Kue</span></h2>
            <p>Kami berkomitmen memberikan yang terbaik untuk setiap pelanggan</p>
        </div>
        <div class="features-grid">
            <div class="feature-card">
                <div class="feature-icon">
                    <i class="fas fa-leaf"></i>
                </div>
                <div class="feature-content">
                    <h3>Bahan Baku Segar & Pilihan</h3>
                    <p>Kami hanya menggunakan bahan-bahan segar berkualitas tinggi, tanpa bahan pengawet. Tepung premium, mentega asli, telur segar, dan cokelat pilihan menjadi standar kami.</p>
                </div>
            </div>
            <div class="feature-card">
                <div class="feature-icon brown">
                    <i class="fas fa-palette"></i>
                </div>
                <div class="feature-content">
                    <h3>Custom Order Sesuai Keinginan</h3>
                    <p>Kue spesial untuk momen spesial. Kami menerima pesanan kustom dengan desain, rasa, dan ukuran sesuai permintaan Anda — dari kue ulang tahun hingga kue pernikahan.</p>
                </div>
            </div>
            <div class="feature-card">
                <div class="feature-icon">
                    <i class="fas fa-truck"></i>
                </div>
                <div class="feature-content">
                    <h3>Pengiriman Tepat Waktu</h3>
                    <p>Kami memahami bahwa ketepatan waktu sangat penting untuk sebuah perayaan. Tim pengiriman kami memastikan kue sampai dalam kondisi sempurna dan tepat waktu.</p>
                </div>
            </div>
            <div class="feature-card">
                <div class="feature-icon brown">
                    <i class="fas fa-tags"></i>
                </div>
                <div class="feature-content">
                    <h3>Harga Terjangkau & Bersaing</h3>
                    <p>Kualitas premium tidak harus mahal. Kami menawarkan kue berkualitas tinggi dengan harga yang kompetitif, agar momen istimewa bisa dinikmati semua kalangan.</p>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- KONTAK --}}
<section class="about-contact">
    <div class="contact-inner">
        <div class="section-header" style="text-align:left;">
            <span class="section-tag">Hubungi Kami</span>
            <h2>Siap <span>Melayani</span> Anda</h2>
            <p>Punya pertanyaan atau ingin memesan? Jangan ragu untuk menghubungi kami.</p>
        </div>
        <div class="contact-grid">
            <div class="contact-info-wrap">
                <div class="contact-item">
                    <div class="contact-icon"><i class="fas fa-phone"></i></div>
                    <div>
                        <div class="contact-label">Telepon / WhatsApp</div>
                        <div class="contact-value">
                            <a href="https://wa.me/6281234567890">081234567890</a>
                        </div>
                    </div>
                </div>
                <div class="contact-item">
                    <div class="contact-icon"><i class="fas fa-envelope"></i></div>
                    <div>
                        <div class="contact-label">Email</div>
                        <div class="contact-value">
                            <a href="mailto:muhammadzidane@student.unp.ac.id">muhammadzidane@student.unp.ac.id</a>
                        </div>
                    </div>
                </div>
                <div class="contact-item">
                    <div class="contact-icon"><i class="fas fa-map-marker-alt"></i></div>
                    <div>
                        <div class="contact-label">Alamat</div>
                        <div class="contact-value">Jl. Imam Bonjol No. 139, Kelurahan Padangdata Tanahmati,<br>Kota Payakumbuh, Sumatera Barat</div>
                    </div>
                </div>
                <div class="contact-item">
                    <div class="contact-icon"><i class="fab fa-instagram"></i></div>
                    <div>
                        <div class="contact-label">Instagram</div>
                        <div class="contact-value">
                            <a href="#">@jagoan.kue</a>
                        </div>
                    </div>
                </div>
            </div>
            <div>
                <div class="hours-card">
                    <h3><i class="fas fa-clock" style="margin-right:10px;opacity:0.8;"></i>Jam Operasional</h3>
                    <div class="hours-row">
                        <span class="hours-day">Senin — Jumat</span>
                        <span class="hours-time">08.00 – 17.00</span>
                    </div>
                    <div class="hours-row">
                        <span class="hours-day">Sabtu</span>
                        <span class="hours-time">08.00 – 15.00</span>
                    </div>
                    <div class="hours-row">
                        <span class="hours-day">Minggu</span>
                        <span class="hours-closed">Libur</span>
                    </div>
                    <div style="margin-top:20px;padding-top:16px;border-top:1px solid rgba(255,255,255,0.15);font-size:12px;opacity:0.75;line-height:1.6;">
                        <i class="fas fa-info-circle" style="margin-right:6px;"></i>
                        Pesanan di luar jam operasional akan diproses pada hari kerja berikutnya.
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- CTA --}}
<section class="about-cta">
    <h2>Siap Pesan Kue Spesialmu?</h2>
    <p>Jelajahi koleksi kue kami atau hubungi kami langsung untuk custom order</p>
    <div>
        <a href="{{ route('products.index') }}" class="btn-cta">
            <i class="fas fa-birthday-cake"></i> Lihat Katalog
        </a>
        <a href="https://wa.me/6281234567890" class="btn-cta-outline" target="_blank">
            <i class="fab fa-whatsapp"></i> WhatsApp Kami
        </a>
    </div>
</section>

@include('partials.footer')

<script src="{{ asset('js/app.js') }}" defer></script>
</body>
</html>
