<x-mail::message>
# Pesanan Berhasil Dibuat! 🎂

Halo **{{ $order->user->name }}**,

Terima kasih telah memesan di **Jagoan Kue**! Pesananmu sudah kami terima dan sedang menunggu konfirmasi pembayaran.

**Detail Pesanan:**

| Info | Detail |
|------|--------|
| Nomor Pesanan | {{ $order->order_code }} |
| Tanggal Pesan | {{ $order->created_at->format('d M Y, H:i') }} |
| Tanggal Kirim | {{ $order->delivery_date?->format('d M Y') ?? '-' }} |
| Metode | {{ $order->delivery_method === 'pickup' ? 'Ambil di Toko' : 'Kirim ke Alamat' }} |
| Total | Rp {{ number_format($order->total_price, 0, ',', '.') }} |
| Status | {{ ucfirst($order->status) }} |

**Produk yang Dipesan:**
@foreach($order->orderItems as $item)
- {{ $item->product->name }} × {{ $item->quantity }} — Rp {{ number_format($item->price * $item->quantity, 0, ',', '.') }}
@endforeach

@if($order->payment && $order->payment->status === 'unpaid')
Silakan lakukan pembayaran dan upload bukti transfer melalui tombol di bawah.

<x-mail::button :url="route('orders.payment', $order)">
Upload Bukti Pembayaran
</x-mail::button>
@endif

Terima kasih sudah mempercayai **Jagoan Kue**! 🍰

Salam manis,<br>
**Tim Jagoan Kue**
</x-mail::message>
