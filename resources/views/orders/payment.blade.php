@extends('layouts.main')

@section('title', 'Jagoan Kue - Konfirmasi Pembayaran')

@php
    $paymentMethod = $order->payment->payment_method ?? 'transfer';

    // Hitung jumlah yang harus dibayar sekarang (DP atau pelunasan atau penuh)
    $isFirstDP  = $order->payment_status === 'dp' && $order->paid_amount < $order->dp_amount;
    $isRemaining = $order->payment_status === 'dp' && $order->paid_amount >= $order->dp_amount;
    if ($isFirstDP) {
        $amountDue  = $order->dp_amount;
        $payLabel   = 'Bayar DP 50%';
    } elseif ($isRemaining) {
        $amountDue  = $order->total_price - $order->paid_amount;
        $payLabel   = 'Pelunasan Sisa';
    } else {
        $amountDue  = $order->total_price;
        $payLabel   = 'Total Bayar';
    }

    $totalAmount   = $order->total_price;
    $uniqueCode    = 1000;
    $totalTransfer = $amountDue + $uniqueCode;
    $deadline      = \Carbon\Carbon::parse($order->created_at)->addHours(2);
    $remainingSeconds = max(0, now()->diffInSeconds($deadline, false));
@endphp

@section('content')
<div class="bg-cream min-h-screen py-8 px-6">
    <div class="max-w-[1140px] mx-auto">
        <div class="text-xs text-text-secondary mb-4">
            <a href="/" class="hover:text-primary transition-colors">Beranda</a> /
            <a href="/products" class="hover:text-primary transition-colors">Katalog</a> /
            <span class="text-brown-dark font-semibold">Konfirmasi Pembayaran</span>
        </div>

        <div class="flex items-center justify-center gap-0 mb-8 max-w-xl mx-auto mt-6">
            <!-- Step 1 -->
            <div class="flex flex-col items-center">
                <div class="w-9 h-9 rounded-full flex items-center justify-center text-sm font-bold bg-green-500 text-white">✓</div>
                <span class="text-xs font-semibold mt-1 text-center text-brown-dark">Pilih Kue</span>
            </div>
            
            <!-- Connector 1 -->
            <div class="flex-1 w-16 sm:w-24 h-0.5 bg-primary mb-4"></div>

            <!-- Step 2 -->
            <div class="flex flex-col items-center">
                <div class="w-9 h-9 rounded-full flex items-center justify-center text-sm font-bold bg-green-500 text-white">✓</div>
                <span class="text-xs font-semibold mt-1 text-center text-brown-dark">Detail Pesanan</span>
            </div>

            <!-- Connector 2 -->
            <div class="flex-1 w-16 sm:w-24 h-0.5 bg-primary mb-4"></div>

            <!-- Step 3 -->
            <div class="flex flex-col items-center">
                <div class="w-9 h-9 rounded-full flex items-center justify-center text-sm font-bold bg-primary text-white shadow-gold">3</div>
                <span class="text-xs font-semibold mt-1 text-center text-brown-dark">Pembayaran</span>
            </div>

            <!-- Connector 3 -->
            <div class="flex-1 w-16 sm:w-24 h-0.5 bg-cream-border mb-4"></div>

            <!-- Step 4 -->
            <div class="flex flex-col items-center">
                <div class="w-9 h-9 rounded-full flex items-center justify-center text-sm font-bold bg-cream-dark text-brown-light">4</div>
                <span class="text-xs font-semibold mt-1 text-center text-text-muted">Konfirmasi</span>
            </div>
        </div>

        <form id="upload-form" action="{{ route('orders.uploadProof', $order) }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="grid grid-cols-1 lg:grid-cols-[1fr_360px] gap-8 items-start">
            <div class="space-y-6">
                {{-- TIMER --}}
                <div class="bg-white rounded-2xl border border-cream-border p-6 shadow-sm">
                    <p class="font-semibold text-xs text-text-secondary uppercase tracking-wider mb-3">Batas Waktu Pembayaran</p>
                    <div class="border-2 border-amber-200 bg-amber-50 rounded-2xl p-6 text-center mb-4">
                        <p class="text-xs text-brown-mid mb-4">Selesaikan pembayaran sebelum:</p>
                        <div class="flex justify-center items-center gap-4">
                            <div class="flex flex-col items-center">
                                <div class="font-heading text-4xl font-bold text-brown-dark" id="timer-jam">00</div>
                                <div class="text-[10px] text-text-muted mt-1 uppercase font-semibold">Jam</div>
                            </div>
                            <div class="font-heading text-2xl font-bold text-brown-light self-start mt-3">:</div>
                            <div class="flex flex-col items-center">
                                <div class="font-heading text-4xl font-bold text-brown-dark" id="timer-menit">00</div>
                                <div class="text-[10px] text-text-muted mt-1 uppercase font-semibold">Menit</div>
                            </div>
                            <div class="font-heading text-2xl font-bold text-brown-light self-start mt-3">:</div>
                            <div class="flex flex-col items-center">
                                <div class="font-heading text-4xl font-bold text-brown-dark" id="timer-detik">00</div>
                                <div class="text-[10px] text-text-muted mt-1 uppercase font-semibold">Detik</div>
                            </div>
                        </div>
                    </div>
                    <p class="text-xs text-text-secondary text-center leading-relaxed">Pesanan akan otomatis dibatalkan jika tidak dibayar sebelum <strong class="text-brown-dark">{{ $deadline->format('d M Y, H.i') }} WIB</strong></p>
                    <div class="bg-amber-100/50 border border-amber-200 rounded-xl p-3.5 text-xs text-amber-800 font-semibold mt-4 text-center">Segera lakukan pembayaran agar pesanan kue kamu tidak hangus dan stok tetap terjamin.</div>
                </div>

                {{-- METODE PEMBAYARAN --}}
                <div class="bg-white rounded-2xl border border-cream-border p-6 shadow-sm">
                    <div class="flex items-center justify-between gap-2 mb-6 border-b border-cream-border pb-4">
                        <p class="font-heading text-xl font-bold text-brown-dark">Metode Pembayaran</p>
                        <div class="inline-flex items-center gap-1.5 px-3 py-1 bg-amber-50 border border-amber-200 text-amber-700 text-xs font-bold rounded-full">
                            <span class="w-1.5 h-1.5 rounded-full bg-amber-500 animate-pulse"></span>
                            <span>Menunggu Pembayaran</span>
                        </div>
                    </div>

                    @if($paymentMethod === 'transfer_bank')
                    {{-- TRANSFER BANK UI --}}
                    <div class="flex items-center gap-3 mb-6 bg-cream-warm/30 border border-cream-border rounded-xl p-4">
                        <div class="w-10 h-10 rounded-xl bg-[#006CB0] text-white flex items-center justify-center text-sm shrink-0"><i class="fas fa-university"></i></div>
                        <div>
                            <p class="font-bold text-sm text-brown-dark">Transfer Bank</p>
                            <p class="text-[11px] text-text-secondary">Transfer Manual</p>
                        </div>
                    </div>

                    <div class="space-y-3 mb-6">
                        <div class="flex justify-between items-center py-2 text-sm border-b border-cream-border/50">
                            <span class="text-text-secondary">Nama Rekening</span>
                            <span class="font-semibold text-brown-dark">Jagoan Kue Official</span>
                        </div>
                        <div class="flex justify-between items-center py-2 text-sm border-b border-cream-border/50">
                            <span class="text-text-secondary">Nomor Rekening</span>
                            <span class="font-semibold text-brown-dark flex items-center gap-2">
                                1234 5678 9012
                                <button type="button" class="btn-secondary py-1 px-3 text-xs" onclick="salin('123456789012')">Salin</button>
                            </span>
                        </div>
                    </div>

                    <div class="bg-primary text-white rounded-2xl p-5 flex justify-between items-center shadow-gold mb-4">
                        <div>
                            <p class="text-xs text-white/80">{{ $payLabel }} (Transfer Tepat)</p>
                            <small class="text-[10px] text-white/70 block mt-0.5">Transfer sesuai nominal untuk verifikasi</small>
                        </div>
                        <div class="text-right flex items-center gap-3">
                            <span class="font-heading text-2xl font-bold">Rp {{ number_format($totalTransfer, 0, ',', '.') }}</span>
                            <button type="button" class="bg-white/20 hover:bg-white/30 text-white font-bold py-1 px-3 rounded-full text-xs transition-colors shrink-0" onclick="salin('{{ $totalTransfer }}')">Salin</button>
                        </div>
                    </div>
                    @if($isFirstDP)
                    <p class="text-xs text-text-secondary leading-relaxed bg-cream p-3 rounded-xl border border-cream-border">Ini adalah pembayaran <strong>DP 50%</strong> dari total Rp {{ number_format($totalAmount, 0, ',', '.') }}. Nominal transfer berbeda Rp {{ number_format($uniqueCode, 0, ',', '.') }} sebagai kode unik verifikasi.</p>
                    @elseif($isRemaining)
                    <p class="text-xs text-text-secondary leading-relaxed bg-cream p-3 rounded-xl border border-cream-border">Ini adalah <strong>pelunasan sisa</strong> dari total Rp {{ number_format($totalAmount, 0, ',', '.') }}. Nominal transfer berbeda Rp {{ number_format($uniqueCode, 0, ',', '.') }} sebagai kode unik verifikasi.</p>
                    @else
                    <p class="text-xs text-text-secondary leading-relaxed bg-cream p-3 rounded-xl border border-cream-border">Nominal transfer berbeda Rp {{ number_format($uniqueCode, 0, ',', '.') }} dari total pesanan — ini adalah kode unik untuk verifikasi otomatis.</p>
                    @endif

                    @elseif($paymentMethod === 'ewallet')
                    {{-- E-WALLET UI --}}
                    <div class="flex items-center gap-3 mb-6 bg-cream-warm/30 border border-cream-border rounded-xl p-4">
                        <div class="w-10 h-10 rounded-xl bg-[#00B14F] text-white flex items-center justify-center text-sm shrink-0"><i class="fas fa-wallet"></i></div>
                        <div>
                            <p class="font-bold text-sm text-brown-dark">E-Wallet</p>
                            <p class="text-[11px] text-text-secondary">GoPay / OVO / dll</p>
                        </div>
                    </div>

                    <div class="bg-primary text-white rounded-2xl p-5 flex justify-between items-center shadow-gold mb-4">
                        <div>
                            <p class="text-xs text-white/80">{{ $payLabel }}</p>
                            <small class="text-[10px] text-white/70 block mt-0.5">Bayar via E-Wallet</small>
                        </div>
                        <div class="text-right">
                            <span class="font-heading text-2xl font-bold">Rp {{ number_format($amountDue, 0, ',', '.') }}</span>
                        </div>
                    </div>

                    @elseif($paymentMethod === 'qris')
                    {{-- QRIS UI --}}
                    <div class="flex items-center gap-3 mb-6 bg-cream-warm/30 border border-cream-border rounded-xl p-4">
                        <div class="w-10 h-10 rounded-xl bg-[#7C3AED] text-white flex items-center justify-center text-sm shrink-0"><i class="fas fa-qrcode"></i></div>
                        <div>
                            <p class="font-bold text-sm text-brown-dark">QRIS</p>
                            <p class="text-[11px] text-text-secondary">Scan & Bayar</p>
                        </div>
                    </div>

                    <div class="bg-primary text-white rounded-2xl p-5 flex justify-between items-center shadow-gold mb-6">
                        <div>
                            <p class="text-xs text-white/80">{{ $payLabel }}</p>
                            <small class="text-[10px] text-white/70 block mt-0.5">Scan QR Code di bawah</small>
                        </div>
                        <div class="text-right">
                            <span class="font-heading text-2xl font-bold">Rp {{ number_format($amountDue, 0, ',', '.') }}</span>
                        </div>
                    </div>

                    <div class="flex flex-col items-center justify-center mt-4">
                        @if(file_exists(public_path('images/qris.png')))
                        <img src="{{ asset('images/qris.png') }}" alt="QR Code QRIS" class="w-[220px] h-[220px] object-contain border border-cream-border rounded-2xl p-2 bg-white shadow-sm">
                        @else
                        <div class="w-[220px] h-[220px] border-2 border-dashed border-[#7C3AED]/40 rounded-2xl flex flex-col items-center justify-center gap-2 text-[#7C3AED] bg-[#7C3AED]/5 p-4 text-center">
                            <i class="fas fa-qrcode text-5xl opacity-50"></i>
                            <span class="text-[11px] font-semibold opacity-70">QR Code belum tersedia</span>
                        </div>
                        @endif
                        <p class="mt-3 text-xs text-text-secondary max-w-sm text-center">Scan QR Code ini menggunakan aplikasi apapun yang mendukung QRIS</p>
                    </div>
                    @endif
                </div>

                {{-- CARA TRANSFER / PEMBAYARAN --}}
                <div class="bg-white rounded-2xl border border-cream-border p-6 shadow-sm">
                    <p class="font-heading text-lg font-bold text-brown-dark mb-4">
                        @if($paymentMethod === 'transfer_bank')
                            CARA MELAKUKAN TRANSFER
                        @elseif($paymentMethod === 'ewallet')
                            CARA PEMBAYARAN E-WALLET
                        @elseif($paymentMethod === 'qris')
                            CARA SCAN QRIS
                        @endif
                    </p>
                    <ul class="space-y-4">
                        @if($paymentMethod === 'transfer_bank')
                        <li class="flex gap-3.5 items-start">
                            <div class="w-6 h-6 rounded-full bg-primary-light text-primary flex items-center justify-center text-xs font-bold shrink-0 mt-0.5">1</div>
                            <div class="text-sm text-text-secondary leading-relaxed">Buka aplikasi mobile banking atau m-banking kamu</div>
                        </li>
                        <li class="flex gap-3.5 items-start">
                            <div class="w-6 h-6 rounded-full bg-primary-light text-primary flex items-center justify-center text-xs font-bold shrink-0 mt-0.5">2</div>
                            <div class="text-sm text-text-secondary leading-relaxed">Pilih menu Transfer</div>
                        </li>
                        <li class="flex gap-3.5 items-start">
                            <div class="w-6 h-6 rounded-full bg-primary-light text-primary flex items-center justify-center text-xs font-bold shrink-0 mt-0.5">3</div>
                            <div class="text-sm text-text-secondary leading-relaxed">Masukkan nomor rekening <strong class="text-brown-dark">1234 5678 9012</strong> a.n. Jagoan Kue Official</div>
                        </li>
                        <li class="flex gap-3.5 items-start">
                            <div class="w-6 h-6 rounded-full bg-primary-light text-primary flex items-center justify-center text-xs font-bold shrink-0 mt-0.5">4</div>
                            <div class="text-sm text-text-secondary leading-relaxed">Masukkan nominal transfer <strong class="text-brown-dark">Rp {{ number_format($totalTransfer, 0, ',', '.') }}</strong> ({{ $payLabel }} + kode unik Rp {{ number_format($uniqueCode, 0, ',', '.') }})</div>
                        </li>
                        <li class="flex gap-3.5 items-start">
                            <div class="w-6 h-6 rounded-full bg-primary-light text-primary flex items-center justify-center text-xs font-bold shrink-0 mt-0.5">5</div>
                            <div class="text-sm text-text-secondary leading-relaxed">Selesaikan transfer, lalu upload bukti pembayaran di bawah</div>
                        </li>
                        @elseif($paymentMethod === 'ewallet')
                        <li class="flex gap-3.5 items-start">
                            <div class="w-6 h-6 rounded-full bg-primary-light text-primary flex items-center justify-center text-xs font-bold shrink-0 mt-0.5">1</div>
                            <div class="text-sm text-text-secondary leading-relaxed">Buka aplikasi E-Wallet kamu (GoPay, OVO, Dana, dll)</div>
                        </li>
                        <li class="flex gap-3.5 items-start">
                            <div class="w-6 h-6 rounded-full bg-primary-light text-primary flex items-center justify-center text-xs font-bold shrink-0 mt-0.5">2</div>
                            <div class="text-sm text-text-secondary leading-relaxed">Transfer ke nomor: <strong class="text-brown-dark">0822-8320-3385</strong> a.n. Jagoan Kue</div>
                        </li>
                        <li class="flex gap-3.5 items-start">
                            <div class="w-6 h-6 rounded-full bg-primary-light text-primary flex items-center justify-center text-xs font-bold shrink-0 mt-0.5">3</div>
                            <div class="text-sm text-text-secondary leading-relaxed">Masukkan nominal Rp {{ number_format($totalAmount, 0, ',', '.') }}</div>
                        </li>
                        <li class="flex gap-3.5 items-start">
                            <div class="w-6 h-6 rounded-full bg-primary-light text-primary flex items-center justify-center text-xs font-bold shrink-0 mt-0.5">4</div>
                            <div class="text-sm text-text-secondary leading-relaxed">Selesaikan pembayaran, lalu screenshot dan upload bukti di bawah</div>
                        </li>
                        @elseif($paymentMethod === 'qris')
                        <li class="flex gap-3.5 items-start">
                            <div class="w-6 h-6 rounded-full bg-primary-light text-primary flex items-center justify-center text-xs font-bold shrink-0 mt-0.5">1</div>
                            <div class="text-sm text-text-secondary leading-relaxed">Screenshot atau simpan gambar QR Code di atas</div>
                        </li>
                        <li class="flex gap-3.5 items-start">
                            <div class="w-6 h-6 rounded-full bg-primary-light text-primary flex items-center justify-center text-xs font-bold shrink-0 mt-0.5">2</div>
                            <div class="text-sm text-text-secondary leading-relaxed">Buka aplikasi mobile banking atau e-wallet yang mendukung QRIS</div>
                        </li>
                        <li class="flex gap-3.5 items-start">
                            <div class="w-6 h-6 rounded-full bg-primary-light text-primary flex items-center justify-center text-xs font-bold shrink-0 mt-0.5">3</div>
                            <div class="text-sm text-text-secondary leading-relaxed">Pilih opsi Bayar / Scan, lalu pilih gambar QR Code dari galeri handphone</div>
                        </li>
                        <li class="flex gap-3.5 items-start">
                            <div class="w-6 h-6 rounded-full bg-primary-light text-primary flex items-center justify-center text-xs font-bold shrink-0 mt-0.5">4</div>
                            <div class="text-sm text-text-secondary leading-relaxed">Konfirmasi pembayaran dengan jumlah tepat Rp {{ number_format($totalAmount, 0, ',', '.') }}</div>
                        </li>
                        <li class="flex gap-3.5 items-start">
                            <div class="w-6 h-6 rounded-full bg-primary-light text-primary flex items-center justify-center text-xs font-bold shrink-0 mt-0.5">5</div>
                            <div class="text-sm text-text-secondary leading-relaxed">Selesaikan transaksi, screenshot bukti sukses, dan unggah di bawah</div>
                        </li>
                        @endif
                    </ul>
                </div>

                {{-- BUKTI PEMBAYARAN --}}
                <div class="bg-white rounded-2xl border border-cream-border p-6 shadow-sm">
                    <p class="font-heading text-lg font-bold text-brown-dark mb-4">UPLOAD BUKTI PEMBAYARAN</p>
                    @if($order->payment && $order->payment->proof_image)
                        <div class="flex items-start gap-2.5 bg-green-50 border border-green-200 text-green-700 rounded-xl p-4 text-xs font-semibold mb-4 leading-relaxed">
                            <i class="fas fa-check-circle text-base mt-0.5 shrink-0"></i>
                            <div>
                                Bukti pembayaran telah diunggah. Kami akan segera memverifikasi pesanan kamu.
                                <br>
                                <a href="{{ asset('storage/' . $order->payment->proof_image) }}" target="_blank" class="text-green-800 font-bold underline mt-1.5 inline-block">Lihat bukti yang diunggah</a>
                            </div>
                        </div>
                        <div class="text-center text-xs text-text-muted mb-4">Ingin mengganti bukti? Pilih file baru di bawah.</div>
                    @endif

                    <input type="file" name="proof_image" id="file-proof" style="display:none;" accept="image/*" onchange="fileSelected(this)" required>
                    <div class="border-2 border-dashed border-cream-border rounded-2xl p-8 text-center hover:border-primary hover:bg-primary-light/30 transition-all cursor-pointer bg-white" onclick="document.getElementById('file-proof').click()" id="uploadZone">
                        <div class="text-4xl mb-3">📸</div>
                        <p class="text-sm font-semibold text-brown-dark mb-1" id="uploadText">Klik di sini untuk memilih foto bukti transfer</p>
                        <p class="text-[11px] text-text-muted mb-4">Mendukung format JPG, PNG, GIF (Maks 2MB)</p>
                        <button type="button" class="btn-ghost py-2 px-6 text-xs">Pilih File</button>
                    </div>
                    <p class="text-[10px] text-text-muted mt-3 text-center leading-relaxed">Pastikan bukti transfer menampilkan: Tanggal, Waktu, Nominal Sukses, Rekening Tujuan.</p>
                </div>
            </div>

            {{-- RINGKASAN --}}
            <div class="bg-cream-warm rounded-2xl border border-cream-border p-5 shrink-0 shadow-sm">
                <p class="font-heading text-xl font-bold text-brown-dark mb-4">Detail Pesanan</p>
                
                <div class="divide-y divide-cream-border max-h-[240px] overflow-y-auto pr-1 mb-4">
                @foreach($order->orderItems as $item)
                <div class="flex items-start gap-3 py-3 first:pt-0">
                    <img src="{{ $item->product?->image ? asset('storage/' . $item->product->image) : 'https://images.unsplash.com/photo-1563729784474-d77dbb933a9e?w=200&q=80' }}"
                         alt="" class="w-12 h-12 rounded-lg object-cover shrink-0">
                    <div class="flex-1 min-w-0">
                        <p class="font-semibold text-xs text-brown-dark truncate">{{ $item->product?->name }}</p>
                        <small class="text-[10px] text-text-secondary">{{ $item->quantity }}x</small>
                    </div>
                    <span class="font-bold text-xs text-primary shrink-0">Rp {{ number_format($item->price * $item->quantity, 0, ',', '.') }}</span>
                </div>
                @endforeach
                </div>

                <div class="space-y-2 mb-4 border-t border-cream-border pt-4 text-xs text-text-secondary">
                    <div class="flex justify-between items-center">
                        <span>Total Harga</span>
                        <span class="font-semibold text-brown-dark">Rp {{ number_format($order->total_price - $order->shipping_cost, 0, ',', '.') }}</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span>Ongkir</span>
                        <span class="font-semibold text-brown-dark">Rp {{ number_format($order->shipping_cost, 0, ',', '.') }}</span>
                    </div>
                </div>

                <div class="flex justify-between items-center pt-3 border-t border-cream-border font-bold text-brown-dark mb-4">
                    <span>Total</span>
                    <span class="text-xl text-primary font-extrabold">Rp {{ number_format($order->total_price, 0, ',', '.') }}</span>
                </div>

                @if($order->payment_status === 'dp')
                <div class="bg-white rounded-xl border border-cream-border p-3 text-xs mb-4 space-y-1.5">
                    <div class="flex justify-between">
                        <span class="text-text-secondary">Telah Dibayar (DP):</span>
                        <span class="font-bold text-brown-dark">Rp {{ number_format($order->paid_amount, 0, ',', '.') }}</span>
                    </div>
                    <div class="flex justify-between font-bold text-primary border-t border-cream-border/50 pt-1.5">
                        <span>Sisa Pelunasan:</span>
                        <span>Rp {{ number_format($order->total_price - $order->paid_amount, 0, ',', '.') }}</span>
                    </div>
                </div>
                @endif

                <button type="submit" class="btn-primary w-full py-3.5 text-sm font-bold justify-center mb-4">Kirim Bukti Pembayaran</button>

                <div class="text-center text-xs text-text-secondary mb-6">
                    Butuh Bantuan?
                    <a href="https://wa.me/081234567890" target="_blank" class="inline-flex items-center gap-1 text-primary hover:underline font-bold ml-1">
                        <i class="fab fa-whatsapp"></i> Hubungi WA Kami
                    </a>
                </div>

                <div class="border-t border-cream-border pt-4 space-y-3">
                    <p class="text-xs font-bold text-brown-dark">Mengapa Belanja di Jagoan Kue Aman?</p>
                    <div class="flex items-start gap-2 text-[11px] text-text-secondary">
                        <i class="fas fa-check-circle text-primary mt-0.5 shrink-0"></i>
                        <span>Bahan baku premium & fresh</span>
                    </div>
                    <div class="flex items-start gap-2 text-[11px] text-text-secondary">
                        <i class="fas fa-check-circle text-primary mt-0.5 shrink-0"></i>
                        <span>Pengiriman aman & tepat waktu</span>
                    </div>
                    <div class="flex items-start gap-2 text-[11px] text-text-secondary">
                        <i class="fas fa-check-circle text-primary mt-0.5 shrink-0"></i>
                        <span>CS kami siap membantu 24/7</span>
                    </div>
                </div>
            </div>
        </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
    let seconds = {{ $remainingSeconds }};
    const jamEl = document.getElementById('timer-jam');
    const minEl = document.getElementById('timer-menit');
    const detEl = document.getElementById('timer-detik');

    function updateTimer() {
        if (seconds <= 0) {
            jamEl.textContent = '00';
            minEl.textContent = '00';
            detEl.textContent = '00';
            return;
        }
        let h = Math.floor(seconds / 3600);
        let m = Math.floor((seconds % 3600) / 60);
        let s = seconds % 60;

        jamEl.textContent = String(h).padStart(2, '0');
        minEl.textContent = String(m).padStart(2, '0');
        detEl.textContent = String(s).padStart(2, '0');
        seconds--;
    }

    if (seconds > 0) {
        updateTimer();
        setInterval(updateTimer, 1000);
    }

    function salin(text) {
        navigator.clipboard.writeText(text).then(function() {
            alert('Teks berhasil disalin ke clipboard!');
        });
    }

    function fileSelected(input) {
        const text = document.getElementById('uploadText');
        const zone = document.getElementById('uploadZone');
        if (input.files && input.files[0]) {
            text.innerHTML = '<strong>File Terpilih:</strong> ' + input.files[0].name;
            zone.style.borderColor = '#10B981';
            zone.style.background = '#ECFDF5';
        }
    }
</script>
<script src="{{ asset('js/app.js') }}" defer></script>
@endpush
